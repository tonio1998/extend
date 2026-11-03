<?php

namespace App\Http\Controllers\DSS\RIE;

use App\Http\Controllers\Controller;
use App\Models\Dts\Unit;
use App\Models\RIE\ExtensionPlan;
use App\Models\RIE\ExtensionProposal;
use App\Models\SaaS\Campus;
use App\Models\SaaS\UploadedDocument;
use App\Services\DSS\TenantContext;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UnitMonitoringController extends Controller
{
    /**
     * Display the Unit Extension Monitoring and 3-Year Post-Commencement Impact Assessment dashboard.
     */
    public function index(Request $request): View
    {
        $payload = $this->buildMonitoringPayload($request);

        return view('dss.rie.unit_monitoring.index', $payload);
    }

    /**
     * Display dedicated projects, plans, repository documents, and transactions for a specific academic unit.
     */
    public function unitTransactions(Request $request, string $id): View
    {
        $tenant = TenantContext::getTenant();
        $tenantId = $tenant?->id ?? 1;
        $unitId = function_exists('dss_decrypt_id') ? dss_decrypt_id($id) : (int) $id;

        $unit = Unit::with(['campus', 'headEmployee', 'extensionHeadEmployee'])->findOrFail($unitId);
        $today = Carbon::today();

        // 1. Fetch Extension Proposals for this unit
        $proposals = ExtensionProposal::with([
            'unit.campus',
            'academicProgram.department',
            'projectLeaderUser',
            'extensionPlan',
        ])
            ->where('tenant_id', $tenantId)
            ->where(function ($q) use ($unit) {
                $q->where('unit_id', $unit->id)
                    ->orWhereHas('academicProgram', fn ($aq) => $aq->where('department_id', $unit->id));
            })
            ->latest()
            ->get();

        // 2. Compute 3-Year Post-Commencement Impact Details for each proposal
        $proposalsWithImpact = $proposals->map(function ($proposal) use ($today) {
            $commencementDate = $proposal->duration_start ? Carbon::parse($proposal->duration_start) : ($proposal->created_at ? Carbon::parse($proposal->created_at) : null);
            $impactDueDate = $commencementDate ? $commencementDate->copy()->addYears(3) : null;
            $isAssessed = ! empty($proposal->metadata['impact_assessment']['conducted_at']) ||
                (($proposal->metadata['impact_assessment']['status'] ?? null) === 'completed');

            $elapsedDays = $commencementDate ? $commencementDate->diffInDays($today) : 0;
            $elapsedMonths = $commencementDate ? $commencementDate->diffInMonths($today) : 0;
            $elapsedYears = round($elapsedDays / 365.25, 1);

            $monthsUntilDue = ($impactDueDate && $today->lte($impactDueDate))
                ? $today->diffInMonths($impactDueDate)
                : 0;

            if ($isAssessed) {
                $statusKey = 'completed';
                $badge = [
                    'label' => 'Impact Assessed',
                    'class' => 'bg-success-subtle text-success border border-success-subtle',
                    'icon' => 'mdi-check-decagram',
                ];
            } elseif ($commencementDate && $today->gte($impactDueDate)) {
                $statusKey = 'due';
                $badge = [
                    'label' => 'Impact Due (3+ Yrs)',
                    'class' => 'bg-danger-subtle text-danger border border-danger-subtle',
                    'icon' => 'mdi-alert-circle',
                ];
            } elseif ($commencementDate && $monthsUntilDue <= 6) {
                $statusKey = 'upcoming';
                $badge = [
                    'label' => 'Due Soon (< 6 Mos)',
                    'class' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
                    'icon' => 'mdi-clock-alert-outline',
                ];
            } else {
                $statusKey = 'maturing';
                $badge = [
                    'label' => 'Ongoing / Maturing',
                    'class' => 'bg-light text-secondary border',
                    'icon' => 'mdi-timer-sand',
                ];
            }

            return [
                'proposal' => $proposal,
                'commencement_date' => $commencementDate,
                'impact_due_date' => $impactDueDate,
                'elapsed_years' => $elapsedYears,
                'is_assessed' => $isAssessed,
                'impact_status' => $statusKey,
                'impact_badge' => $badge,
                'assessment_meta' => $proposal->metadata['impact_assessment'] ?? null,
            ];
        });

        // 3. Fetch Extension Plans for this unit
        $plans = ExtensionPlan::with(['academicProgram', 'activities', 'extensionProposal'])
            ->where('tenant_id', $tenantId)
            ->where(function ($pq) use ($unit) {
                $pq->whereHas('academicProgram', fn ($aq) => $aq->where('department_id', $unit->id))
                    ->orWhereHas('extensionProposal', fn ($uq) => $uq->where('unit_id', $unit->id));
            })
            ->latest()
            ->get();

        // 4. Fetch Centralized Uploaded Documents & Evidence for this unit
        $propIds = $proposals->pluck('id')->toArray();
        $planIds = $plans->pluck('id')->toArray();

        $documents = UploadedDocument::with(['uploader', 'campus'])
            ->where('tenant_id', $tenantId)
            ->where(function ($dq) use ($propIds, $planIds) {
                if (! empty($propIds)) {
                    $dq->orWhere(function ($sq) use ($propIds) {
                        $sq->where('documentable_type', ExtensionProposal::class)->whereIn('documentable_id', $propIds);
                    });
                }
                if (! empty($planIds)) {
                    $dq->orWhere(function ($sq) use ($planIds) {
                        $sq->where('documentable_type', ExtensionPlan::class)->whereIn('documentable_id', $planIds);
                    });
                    foreach (array_slice($planIds, 0, 15) as $pid) {
                        $dq->orWhereJsonContains('metadata->plan_id', (int) $pid);
                    }
                }
            })
            ->latest()
            ->get();

        // 5. Compute Aggregate Unit Metrics
        $totalBudget = (float) $proposals->sum('approved_budget');
        $activeCount = $proposals->filter(fn ($p) => in_array($p->status, ['submitted', 'reviewed', 'noted', 'concurred', 'recommended', 'approved']))->count();
        $completedCount = $proposals->filter(fn ($p) => in_array($p->status, ['completed', 'terminal']))->count();
        $impactDueCount = $proposalsWithImpact->where('impact_status', 'due')->count();
        $impactAssessedCount = $proposalsWithImpact->where('is_assessed', true)->count();
        $projectLeadersCount = $proposals->pluck('project_leader_name')->filter()->unique()->count();

        $headName = $unit->extensionHeadEmployee?->full_name
            ?: ($unit->head_name ?: ($unit->headEmployee?->full_name ?: 'Designated Unit Head'));

        return view('dss.rie.unit_monitoring.unit_transactions', compact(
            'unit',
            'proposalsWithImpact',
            'plans',
            'documents',
            'totalBudget',
            'activeCount',
            'completedCount',
            'impactDueCount',
            'impactAssessedCount',
            'projectLeadersCount',
            'headName'
        ));
    }

    /**
     * Display dedicated projects, proposals, and transactions authored by a specific project leader.
     */
    public function leaderTransactions(Request $request, string $name): View
    {
        $tenant = TenantContext::getTenant();
        $tenantId = $tenant?->id ?? 1;
        $leaderName = urldecode($name);

        $today = Carbon::today();

        $proposals = ExtensionProposal::with([
            'unit.campus',
            'academicProgram.department',
            'projectLeaderUser',
            'extensionPlan',
        ])
            ->where('tenant_id', $tenantId)
            ->where('project_leader_name', $leaderName)
            ->latest()
            ->get();

        $proposalsWithImpact = $proposals->map(function ($proposal) use ($today) {
            $commencementDate = $proposal->duration_start ? Carbon::parse($proposal->duration_start) : ($proposal->created_at ? Carbon::parse($proposal->created_at) : null);
            $impactDueDate = $commencementDate ? $commencementDate->copy()->addYears(3) : null;
            $isAssessed = ! empty($proposal->metadata['impact_assessment']['conducted_at']) ||
                (($proposal->metadata['impact_assessment']['status'] ?? null) === 'completed');

            $elapsedDays = $commencementDate ? $commencementDate->diffInDays($today) : 0;
            $elapsedYears = round($elapsedDays / 365.25, 1);

            return [
                'proposal' => $proposal,
                'commencement_date' => $commencementDate,
                'impact_due_date' => $impactDueDate,
                'elapsed_years' => $elapsedYears,
                'is_assessed' => $isAssessed,
                'assessment_meta' => $proposal->metadata['impact_assessment'] ?? null,
            ];
        });

        $plans = ExtensionPlan::with(['academicProgram', 'activities', 'extensionProposal'])
            ->where('tenant_id', $tenantId)
            ->where('prepared_by_name', $leaderName)
            ->latest()
            ->get();

        $propIds = $proposals->pluck('id')->toArray();
        $documents = UploadedDocument::with(['uploader', 'campus'])
            ->where('tenant_id', $tenantId)
            ->where(function ($dq) use ($propIds, $leaderName) {
                if (! empty($propIds)) {
                    $dq->orWhere(function ($sq) use ($propIds) {
                        $sq->where('documentable_type', ExtensionProposal::class)->whereIn('documentable_id', $propIds);
                    });
                }
                $dq->orWhere('uploader_name', $leaderName);
            })
            ->latest()
            ->get();

        $totalBudget = (float) $proposals->sum('approved_budget');
        $unitAffiliation = $proposals->first()?->unit?->unit_name ?: 'Surigao del Norte State University';

        return view('dss.rie.unit_monitoring.leader_transactions', compact(
            'leaderName',
            'unitAffiliation',
            'proposalsWithImpact',
            'plans',
            'documents',
            'totalBudget'
        ));
    }

    // /**
    //  * Display dedicated projects, plans, repository documents, and transactions for a specific academic unit.
    //  */
    // public function unitTransactions(Request $request, string $id): View
    // {
    //     $tenant = TenantContext::getTenant();
    //     $tenantId = $tenant?->id ?? 1;
    //     $unitId = function_exists('dss_decrypt_id') ? dss_decrypt_id($id) : (int) $id;

    //     $unit = Unit::with(['campus', 'headEmployee', 'extensionHeadEmployee'])->findOrFail($unitId);
    //     $today = Carbon::today();

    //     // 1. Fetch Extension Proposals for this unit
    //     $proposals = ExtensionProposal::with([
    //         'unit.campus',
    //         'academicProgram.department',
    //         'projectLeaderUser',
    //         'extensionPlan',
    //     ])
    //     ->where('tenant_id', $tenantId)
    //     ->where(function ($q) use ($unit) {
    //         $q->where('unit_id', $unit->id)
    //             ->orWhereHas('academicProgram', fn ($aq) => $aq->where('department_id', $unit->id));
    //     })
    //     ->latest()
    //     ->get();

    //     // 2. Compute 3-Year Post-Commencement Impact Details for each proposal
    //     $proposalsWithImpact = $proposals->map(function ($proposal) use ($today) {
    //         $commencementDate = $proposal->duration_start ? Carbon::parse($proposal->duration_start) : ($proposal->created_at ? Carbon::parse($proposal->created_at) : null);
    //         $impactDueDate = $commencementDate ? $commencementDate->copy()->addYears(3) : null;
    //         $isAssessed = ! empty($proposal->metadata['impact_assessment']['conducted_at']) ||
    //             (($proposal->metadata['impact_assessment']['status'] ?? null) === 'completed');

    //         $elapsedDays = $commencementDate ? $commencementDate->diffInDays($today) : 0;
    //         $elapsedMonths = $commencementDate ? $commencementDate->diffInMonths($today) : 0;
    //         $elapsedYears = round($elapsedDays / 365.25, 1);

    //         $monthsUntilDue = ($impactDueDate && $today->lte($impactDueDate))
    //             ? $today->diffInMonths($impactDueDate)
    //             : 0;

    //         if ($isAssessed) {
    //             $statusKey = 'completed';
    //             $badge = [
    //                 'label' => 'Impact Assessed',
    //                 'class' => 'bg-success-subtle text-success border border-success-subtle',
    //                 'icon' => 'mdi-check-decagram',
    //             ];
    //         } elseif ($commencementDate && $today->gte($impactDueDate)) {
    //             $statusKey = 'due';
    //             $badge = [
    //                 'label' => 'Impact Due (3+ Yrs)',
    //                 'class' => 'bg-danger-subtle text-danger border border-danger-subtle',
    //                 'icon' => 'mdi-alert-circle',
    //             ];
    //         } elseif ($commencementDate && $monthsUntilDue <= 6) {
    //             $statusKey = 'upcoming';
    //             $badge = [
    //                 'label' => 'Due Soon (< 6 Mos)',
    //                 'class' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
    //                 'icon' => 'mdi-clock-alert-outline',
    //             ];
    //         } else {
    //             $statusKey = 'maturing';
    //             $badge = [
    //                 'label' => 'Ongoing / Maturing',
    //                 'class' => 'bg-light text-secondary border',
    //                 'icon' => 'mdi-timer-sand',
    //             ];
    //         }

    //         return [
    //             'proposal' => $proposal,
    //             'commencement_date' => $commencementDate,
    //             'impact_due_date' => $impactDueDate,
    //             'elapsed_years' => $elapsedYears,
    //             'is_assessed' => $isAssessed,
    //             'impact_status' => $statusKey,
    //             'impact_badge' => $badge,
    //             'assessment_meta' => $proposal->metadata['impact_assessment'] ?? null,
    //         ];
    //     });

    //     // 3. Fetch Extension Plans for this unit
    //     $plans = ExtensionPlan::with(['academicProgram', 'activities', 'extensionProposal'])
    //         ->where('tenant_id', $tenantId)
    //         ->where(function ($pq) use ($unit) {
    //             $pq->whereHas('academicProgram', fn ($aq) => $aq->where('department_id', $unit->id))
    //                 ->orWhereHas('extensionProposal', fn ($uq) => $uq->where('unit_id', $unit->id));
    //         })
    //         ->latest()
    //         ->get();

    //     // 4. Fetch Centralized Uploaded Documents & Evidence for this unit
    //     $propIds = $proposals->pluck('id')->toArray();
    //     $planIds = $plans->pluck('id')->toArray();

    //     $documents = UploadedDocument::with(['uploader', 'campus'])
    //         ->where('tenant_id', $tenantId)
    //         ->where(function ($dq) use ($propIds, $planIds) {
    //             if (! empty($propIds)) {
    //                 $dq->orWhere(function ($sq) use ($propIds) {
    //                     $sq->where('documentable_type', ExtensionProposal::class)->whereIn('documentable_id', $propIds);
    //                 });
    //             }
    //             if (! empty($planIds)) {
    //                 $dq->orWhere(function ($sq) use ($planIds) {
    //                     $sq->where('documentable_type', ExtensionPlan::class)->whereIn('documentable_id', $planIds);
    //                 });
    //                 foreach (array_slice($planIds, 0, 15) as $pid) {
    //                     $dq->orWhereJsonContains('metadata->plan_id', (int) $pid);
    //                 }
    //             }
    //         })
    //         ->latest()
    //         ->get();

    //     // 5. Compute Aggregate Unit Metrics
    //     $totalBudget = (float) $proposals->sum('approved_budget');
    //     $activeCount = $proposals->filter(fn ($p) => in_array($p->status, ['submitted', 'reviewed', 'noted', 'concurred', 'recommended', 'approved']))->count();
    //     $completedCount = $proposals->filter(fn ($p) => in_array($p->status, ['completed', 'terminal']))->count();
    //     $impactDueCount = $proposalsWithImpact->where('impact_status', 'due')->count();
    //     $impactAssessedCount = $proposalsWithImpact->where('is_assessed', true)->count();
    //     $projectLeadersCount = $proposals->pluck('project_leader_name')->filter()->unique()->count();

    //     $headName = $unit->extensionHeadEmployee?->full_name
    //         ?: ($unit->head_name ?: ($unit->headEmployee?->full_name ?: 'Designated Unit Head'));

    //     return view('dss.rie.unit_monitoring.unit_transactions', compact(
    //         'unit',
    //         'proposalsWithImpact',
    //         'plans',
    //         'documents',
    //         'totalBudget',
    //         'activeCount',
    //         'completedCount',
    //         'impactDueCount',
    //         'impactAssessedCount',
    //         'projectLeadersCount',
    //         'headName'
    //     ));
    // }

    /**
     * Display dedicated projects, proposals, and transactions authored by a specific project leader.
     */
    // public function leaderTransactions(Request $request, string $name): View
    // {
    //     $tenant = TenantContext::getTenant();
    //     $tenantId = $tenant?->id ?? 1;
    //     $leaderName = urldecode($name);

    //     $today = Carbon::today();

    //     $proposals = ExtensionProposal::with([
    //         'unit.campus',
    //         'academicProgram.department',
    //         'projectLeaderUser',
    //         'extensionPlan',
    //     ])
    //     ->where('tenant_id', $tenantId)
    //     ->where('project_leader_name', $leaderName)
    //     ->latest()
    //     ->get();

    //     $proposalsWithImpact = $proposals->map(function ($proposal) use ($today) {
    //         $commencementDate = $proposal->duration_start ? Carbon::parse($proposal->duration_start) : ($proposal->created_at ? Carbon::parse($proposal->created_at) : null);
    //         $impactDueDate = $commencementDate ? $commencementDate->copy()->addYears(3) : null;
    //         $isAssessed = ! empty($proposal->metadata['impact_assessment']['conducted_at']) ||
    //             (($proposal->metadata['impact_assessment']['status'] ?? null) === 'completed');

    //         $elapsedDays = $commencementDate ? $commencementDate->diffInDays($today) : 0;
    //         $elapsedYears = round($elapsedDays / 365.25, 1);

    //         return [
    //             'proposal' => $proposal,
    //             'commencement_date' => $commencementDate,
    //             'impact_due_date' => $impactDueDate,
    //             'elapsed_years' => $elapsedYears,
    //             'is_assessed' => $isAssessed,
    //             'assessment_meta' => $proposal->metadata['impact_assessment'] ?? null,
    //         ];
    //     });

    //     $plans = ExtensionPlan::with(['academicProgram', 'activities', 'extensionProposal'])
    //         ->where('tenant_id', $tenantId)
    //         ->where('prepared_by_name', $leaderName)
    //         ->latest()
    //         ->get();

    //     $propIds = $proposals->pluck('id')->toArray();
    //     $documents = UploadedDocument::with(['uploader', 'campus'])
    //         ->where('tenant_id', $tenantId)
    //         ->where(function ($dq) use ($propIds, $leaderName) {
    //             if (! empty($propIds)) {
    //                 $dq->orWhere(function ($sq) use ($propIds) {
    //                     $sq->where('documentable_type', ExtensionProposal::class)->whereIn('documentable_id', $propIds);
    //                 });
    //             }
    //             $dq->orWhere('uploader_name', $leaderName);
    //         })
    //         ->latest()
    //         ->get();

    //     $totalBudget = (float) $proposals->sum('approved_budget');
    //     $unitAffiliation = $proposals->first()?->unit?->unit_name ?: 'Surigao del Norte State University';

    //     return view('dss.rie.unit_monitoring.leader_transactions', compact(
    //         'leaderName',
    //         'unitAffiliation',
    //         'proposalsWithImpact',
    //         'plans',
    //         'documents',
    //         'totalBudget'
    //     ));
    // }

    /**
     * Record or update an impact assessment conducted for an extension proposal.
     */
    public function recordImpactAssessment(Request $request, string $id): JsonResponse
    {
        $proposalId = function_exists('dss_decrypt_id') ? dss_decrypt_id($id) : (int) $id;
        $proposal = ExtensionProposal::findOrFail($proposalId);

        $validated = $request->validate([
            'assessment_date' => 'required|date',
            'evaluator_name' => 'required|string|max:191',
            'sustainability_rating' => 'nullable|string|max:50',
            'community_adoption_level' => 'nullable|string|max:50',
            'findings_summary' => 'nullable|string|max:3000',
            'recommendations' => 'nullable|string|max:3000',
            'report_url' => 'nullable|url|max:500',
        ]);

        $meta = $proposal->metadata ?? [];
        $meta['impact_assessment'] = [
            'conducted_at' => Carbon::parse($validated['assessment_date'])->toDateString(),
            'evaluator_name' => $validated['evaluator_name'],
            'sustainability_rating' => $validated['sustainability_rating'] ?? 'Satisfactory',
            'community_adoption_level' => $validated['community_adoption_level'] ?? 'High Adoption',
            'findings_summary' => $validated['findings_summary'] ?? null,
            'recommendations' => $validated['recommendations'] ?? null,
            'report_url' => $validated['report_url'] ?? null,
            'status' => 'completed',
            'recorded_by_user_id' => auth()->id(),
            'recorded_at' => now()->toDateTimeString(),
        ];

        $proposal->metadata = $meta;
        $proposal->save();

        return response()->json([
            'success' => true,
            'message' => 'Impact assessment status recorded successfully for '.$proposal->proposal_code,
            'impact_assessment' => $meta['impact_assessment'],
        ]);
    }

    /**
     * Export monitoring data as CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        $type = $request->query('type', 'units');
        $payload = $this->buildMonitoringPayload($request);
        $filename = $type === 'impact'
            ? 'extension-3yr-impact-assessments-'.now()->format('Y-m-d').'.csv'
            : 'unit-extension-monitoring-summary-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($type, $payload) {
            $handle = fopen('php://output', 'w');

            if ($type === 'impact') {
                fputcsv($handle, [
                    'Proposal Code',
                    'Extension Title',
                    'Unit / Department',
                    'Campus',
                    'Project Leader',
                    'Commencement Date',
                    'Elapsed Since Commencement',
                    'Impact Assessment Due Date (3 Yrs Post-Commencement)',
                    'Impact Assessment Status',
                    'Total Budget',
                    'Current Workflow Status',
                ]);

                foreach ($payload['proposalsList'] as $item) {
                    $prop = $item['proposal'];
                    fputcsv($handle, [
                        $prop->proposal_code,
                        $prop->title,
                        $item['unit_name'],
                        $item['campus_name'],
                        $prop->project_leader_name,
                        $item['commencement_date'] ? $item['commencement_date']->format('Y-m-d') : 'TBD',
                        $item['elapsed_text'],
                        $item['impact_due_date'] ? $item['impact_due_date']->format('Y-m-d') : 'TBD',
                        $item['impact_status_badge']['label'],
                        $prop->budget_total_display ?: '₱0.00',
                        ucfirst($prop->status),
                    ]);
                }
            } else {
                fputcsv($handle, [
                    'Unit Code',
                    'Unit / Department Name',
                    'Campus',
                    'Unit Head',
                    'Total Extensions',
                    'Active / Ongoing',
                    'Completed / Terminal',
                    'Cumulative Budget',
                    'Impact Assessments Due (>= 3 Years Post-Commencement)',
                    'Impact Assessments Completed',
                ]);

                foreach ($payload['unitsSummary'] as $row) {
                    fputcsv($handle, [
                        $row['unit_code'],
                        $row['unit_name'],
                        $row['campus_name'],
                        $row['unit_head'],
                        $row['total_extensions'],
                        $row['active_extensions'],
                        $row['completed_extensions'],
                        '₱'.number_format($row['total_budget'], 2),
                        $row['impact_due_count'],
                        $row['impact_completed_count'],
                    ]);
                }
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * Compute comprehensive monitoring metrics and 3-year impact assessment statuses.
     *
     * @return array<string, mixed>
     */
    private function buildMonitoringPayload(Request $request): array
    {
        $tenant = TenantContext::getTenant();
        $tenantId = $tenant?->id ?? 1;
        $today = Carbon::today();

        $selectedCampus = $request->query('campus', 'all');
        $searchQuery = trim((string) $request->query('search', ''));
        $activeTab = $request->query('tab', 'units');
        $impactStatusFilter = $request->query('impact_status', 'all');
        $selectedUnitId = $request->query('unit_id', 'all');

        $campuses = Campus::orderBy('campus_name')->get();

        // 1. Fetch all Units
        $unitsQuery = Unit::with(['campus', 'headEmployee', 'extensionHeadEmployee']);
        if ($selectedCampus !== 'all' && is_numeric($selectedCampus)) {
            $unitsQuery->where('campus_id', (int) $selectedCampus);
        }
        $units = $unitsQuery->orderBy('unit_name')->get();

        // 2. Fetch all Extension Proposals with relations
        $proposalsQuery = ExtensionProposal::with([
            'unit.campus',
            'academicProgram.campus',
            'academicProgram.department',
            'campus',
            'projectLeaderUser',
            'extensionPlan',
        ]);

        if ($selectedCampus !== 'all' && is_numeric($selectedCampus)) {
            $proposalsQuery->where('campus_id', (int) $selectedCampus);
        }

        $allProposals = $proposalsQuery->latest()->get();

        // 3. Process every proposal to identify 3-Year Post-Commencement Impact Assessment Eligibility
        $proposalsList = [];
        $totalImpactDueCount = 0;
        $totalImpactUpcomingCount = 0;
        $totalImpactCompletedCount = 0;
        $totalImpactMaturingCount = 0;

        foreach ($allProposals as $proposal) {
            $unit = $proposal->unit ?: ($proposal->academicProgram?->department);
            $campus = $proposal->campus ?: ($unit?->campus ?: $proposal->academicProgram?->campus);

            $commencementDate = $proposal->duration_start ? Carbon::parse($proposal->duration_start) : ($proposal->created_at ? Carbon::parse($proposal->created_at) : null);
            $impactDueDate = $commencementDate ? $commencementDate->copy()->addYears(3) : null;

            // Check if impact assessment has been officially conducted/recorded
            $isAssessed = ! empty($proposal->metadata['impact_assessment']['conducted_at']) ||
                (($proposal->metadata['impact_assessment']['status'] ?? null) === 'completed');

            $elapsedDays = $commencementDate ? $commencementDate->diffInDays($today) : 0;
            $elapsedMonths = $commencementDate ? $commencementDate->diffInMonths($today) : 0;
            $elapsedYears = round($elapsedDays / 365.25, 1);

            $monthsUntilDue = ($impactDueDate && $today->lte($impactDueDate))
                ? $today->diffInMonths($impactDueDate)
                : 0;

            // Determine Impact Assessment Status:
            // Rule: Impact assessment conducted 3 years after the commencement of the project
            if ($isAssessed) {
                $statusKey = 'completed';
                $badge = [
                    'label' => 'Impact Assessment Completed',
                    'class' => 'bg-success text-white',
                    'icon' => 'mdi-check-decagram',
                    'urgency' => 'completed',
                    'description' => 'Official impact evaluation completed & documented.',
                ];
                $totalImpactCompletedCount++;
            } elseif ($commencementDate && $today->gte($impactDueDate)) {
                $statusKey = 'due';
                $overdueMonths = $impactDueDate->diffInMonths($today);
                $badge = [
                    'label' => 'Impact Assessment Due (3+ Yrs)',
                    'class' => 'bg-danger text-white',
                    'icon' => 'mdi-alert-circle',
                    'urgency' => 'high',
                    'description' => 'Project commenced '.$elapsedYears.' yrs ago ('.($overdueMonths > 0 ? $overdueMonths.' mos overdue' : 'due now').'). Evaluation required.',
                ];
                $totalImpactDueCount++;
            } elseif ($commencementDate && $monthsUntilDue <= 6) {
                $statusKey = 'upcoming';
                $badge = [
                    'label' => 'Due Soon (< 6 Months)',
                    'class' => 'bg-warning text-dark',
                    'icon' => 'mdi-clock-alert-outline',
                    'urgency' => 'medium',
                    'description' => 'Reaching 3-year post-commencement mark in '.$monthsUntilDue.' month(s). Prepare assessment tools.',
                ];
                $totalImpactUpcomingCount++;
            } else {
                $statusKey = 'maturing';
                $badge = [
                    'label' => 'Ongoing / Maturing',
                    'class' => 'bg-light text-secondary border',
                    'icon' => 'mdi-timer-sand',
                    'urgency' => 'low',
                    'description' => $impactDueDate ? 'Due on '.$impactDueDate->format('M Y').' ('.$monthsUntilDue.' mos left)' : 'Commencement date to be verified.',
                ];
                $totalImpactMaturingCount++;
            }

            // Elapsed text formatter
            $elapsedText = 'Recent';
            if ($commencementDate) {
                $years = (int) floor($elapsedMonths / 12);
                $remMonths = ((int) $elapsedMonths) % 12;
                if ($years > 0) {
                    $elapsedText = $years.' yr'.($years > 1 ? 's' : '').($remMonths > 0 ? ' '.$remMonths.' mo'.($remMonths > 1 ? 's' : '') : '');
                } else {
                    $elapsedText = max(1, (int) $elapsedMonths).' mo'.((int) $elapsedMonths > 1 ? 's' : '');
                }
            }

            $proposalItem = [
                'proposal' => $proposal,
                'unit_id' => $unit?->id,
                'unit_name' => $unit?->unit_name ?: 'General / Unassigned Unit',
                'unit_code' => $unit?->unit_code ?: 'EXT',
                'campus_name' => $campus?->campus_name ?: ($unit?->campus?->campus_name ?: 'SNSU Main Campus'),
                'commencement_date' => $commencementDate,
                'impact_due_date' => $impactDueDate,
                'elapsed_text' => $elapsedText,
                'elapsed_months' => $elapsedMonths,
                'elapsed_years' => $elapsedYears,
                'months_until_due' => $monthsUntilDue,
                'is_assessed' => $isAssessed,
                'impact_status' => $statusKey,
                'impact_status_badge' => $badge,
                'assessment_meta' => $proposal->metadata['impact_assessment'] ?? null,
            ];

            // Apply impact status filter if in impact tab
            $matchesStatus = ($impactStatusFilter === 'all') || ($impactStatusFilter === $statusKey);
            $matchesUnit = ($selectedUnitId === 'all') || ((int) $selectedUnitId === (int) $unit?->id);
            $matchesSearch = empty($searchQuery) ||
                str_contains(strtolower($proposal->title), strtolower($searchQuery)) ||
                str_contains(strtolower($proposal->proposal_code), strtolower($searchQuery)) ||
                str_contains(strtolower($proposal->project_leader_name), strtolower($searchQuery)) ||
                str_contains(strtolower($unit?->unit_name ?? ''), strtolower($searchQuery));

            if ($matchesStatus && $matchesUnit && $matchesSearch) {
                $proposalsList[] = $proposalItem;
            }
        }

        // 4. Build Unit / Department Summary
        // Group all proposals by unit_id to aggregate counts and metrics
        $proposalsByUnit = $allProposals->groupBy(function ($prop) {
            return $prop->unit_id ?: ($prop->academicProgram?->department_id ?: 0);
        });

        $unitsSummary = [];
        $totalBudgetSum = 0;
        $totalExtensionsCount = $allProposals->count();

        foreach ($units as $unit) {
            $unitProposals = $proposalsByUnit->get($unit->id, collect());
            $unitExtCount = $unitProposals->count();

            // Calculate budget sum
            $unitBudget = 0;
            $unitActiveCount = 0;
            $unitCompletedCount = 0;
            $unitImpactDueCount = 0;
            $unitImpactCompletedCount = 0;

            foreach ($unitProposals as $p) {
                $cash = (float) ($p->budget_cash_amount ?: 0);
                if ($cash <= 0 && ! empty($p->budget_total_display)) {
                    $cleaned = preg_replace('/[^0-9.]/', '', (string) $p->budget_total_display);
                    $cash = (float) $cleaned;
                }
                $unitBudget += $cash;
                $totalBudgetSum += $cash;

                $st = strtolower($p->status ?? '');
                if (in_array($st, ['completed', 'terminal'], true)) {
                    $unitCompletedCount++;
                } else {
                    $unitActiveCount++;
                }

                // Check impact due
                $pCommence = $p->duration_start ? Carbon::parse($p->duration_start) : ($p->created_at ? Carbon::parse($p->created_at) : null);
                $pDue = $pCommence ? $pCommence->copy()->addYears(3) : null;
                $pAssessed = ! empty($p->metadata['impact_assessment']['conducted_at']) || (($p->metadata['impact_assessment']['status'] ?? null) === 'completed');

                if ($pAssessed) {
                    $unitImpactCompletedCount++;
                } elseif ($pCommence && $today->gte($pDue)) {
                    $unitImpactDueCount++;
                }
            }

            $unitProposalsFormatted = $unitProposals->map(function ($p) {
                $cash = (float) ($p->budget_cash_amount ?: 0);
                if ($cash <= 0 && ! empty($p->budget_total_display)) {
                    $cleaned = preg_replace('/[^0-9.]/', '', (string) $p->budget_total_display);
                    $cash = (float) $cleaned;
                }

                $commence = $p->duration_start ? Carbon::parse($p->duration_start)->format('M d, Y') : ($p->created_at ? Carbon::parse($p->created_at)->format('M d, Y') : '—');

                return [
                    'id' => $p->id,
                    'code' => $p->proposal_code ?: 'PROP-'.$p->id,
                    'title' => $p->title,
                    'status' => ucfirst(str_replace('_', ' ', $p->status ?? 'active')),
                    'leader' => $p->project_leader_name ?: '—',
                    'commencement' => $commence,
                    'budget' => '₱'.number_format($cash, 2),
                ];
            })->values()->toArray();

            $unitHead = $unit->extensionHeadEmployee?->full_name ?: ($unit->head_name ?: ($unit->headEmployee?->full_name ?: 'Designated Coordinator'));

            $matchesSearch = empty($searchQuery) ||
                str_contains(strtolower($unit->unit_name), strtolower($searchQuery)) ||
                str_contains(strtolower($unit->unit_code ?? ''), strtolower($searchQuery)) ||
                str_contains(strtolower($unitHead), strtolower($searchQuery));

            if ($matchesSearch) {
                $unitsSummary[] = [
                    'unit' => $unit,
                    'unit_id' => $unit->id,
                    'unit_code' => $unit->unit_code ?: 'EXT',
                    'unit_name' => $unit->unit_name,
                    'short_name' => $unit->short_name ?: $unit->unit_code,
                    'unit_type' => $unit->unit_type ?: 'Academic Unit',
                    'campus_name' => $unit->campus?->campus_name ?: 'Main Campus',
                    'unit_head' => $unitHead,
                    'total_extensions' => $unitExtCount,
                    'active_extensions' => $unitActiveCount,
                    'completed_extensions' => $unitCompletedCount,
                    'total_budget' => $unitBudget,
                    'impact_due_count' => $unitImpactDueCount,
                    'impact_completed_count' => $unitImpactCompletedCount,
                    'proposals' => $unitProposals,
                    'proposals_list' => $unitProposalsFormatted,
                ];
            }
        }

        // Sort units: units with extensions first, then by extension count descending
        usort($unitsSummary, function ($a, $b) {
            if ($a['total_extensions'] === $b['total_extensions']) {
                return strcmp($a['unit_name'], $b['unit_name']);
            }

            return $b['total_extensions'] <=> $a['total_extensions'];
        });

        // Compute active units count (units that have at least 1 extension)
        $unitsWithExtensionsCount = count(array_filter($unitsSummary, fn ($u) => $u['total_extensions'] > 0));

        $activeExtSum = array_sum(array_column($unitsSummary, 'active_extensions'));
        $completedExtSum = array_sum(array_column($unitsSummary, 'completed_extensions'));

        $kpis = [
            'total_units' => count($units),
            'units_with_extensions' => $unitsWithExtensionsCount,
            'total_extensions' => $totalExtensionsCount,
            'active_extensions' => $activeExtSum,
            'completed_extensions' => $completedExtSum,
            'total_budget' => $totalBudgetSum,
            'cumulative_budget' => $totalBudgetSum,
            'impact_due_count' => $totalImpactDueCount,
            'impact_upcoming_count' => $totalImpactUpcomingCount,
            'impact_completed_count' => $totalImpactCompletedCount,
            'impact_maturing_count' => $totalImpactMaturingCount,
        ];

        return [
            'kpis' => $kpis,
            'campuses' => $campuses,
            'selectedCampus' => $selectedCampus,
            'searchQuery' => $searchQuery,
            'activeTab' => $activeTab,
            'impactStatusFilter' => $impactStatusFilter,
            'selectedUnitId' => $selectedUnitId,
            'unitsSummary' => $unitsSummary,
            'proposalsList' => $proposalsList,
            'totalUnitsCount' => count($units),
            'unitsWithExtensionsCount' => $unitsWithExtensionsCount,
            'totalExtensionsCount' => $totalExtensionsCount,
            'totalBudgetSum' => $totalBudgetSum,
            'totalImpactDueCount' => $totalImpactDueCount,
            'totalImpactUpcomingCount' => $totalImpactUpcomingCount,
            'totalImpactCompletedCount' => $totalImpactCompletedCount,
            'totalImpactMaturingCount' => $totalImpactMaturingCount,
        ];
    }
}

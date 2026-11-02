<?php

namespace App\Http\Controllers\DSS\RIE;

use App\Http\Controllers\Controller;
use App\Models\RIE\ExtensionPlan;
use App\Models\RIE\ExtensionProposal;
use App\Models\SaaS\Campus;
use App\Services\DSS\TenantContext;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExtensionCalendarController extends Controller
{
    public function index(Request $request): View
    {
        $payload = $this->buildCalendarPayload($request);

        return view('dss.rie.calendar.index', $payload);
    }

    public function events(Request $request): JsonResponse
    {
        $payload = $this->buildCalendarPayload($request);

        return response()->json([
            'events' => $payload['calendarEvents'],
            'roadmap' => $payload['durationRoadmapItems'],
            'stats' => [
                'ongoing' => $payload['ongoingExtensionsCount'],
                'upcoming' => $payload['upcomingCommencementsCount'],
                'completed' => $payload['completedExtensionsCount'],
                'total_events' => $payload['totalScheduledEventsCount'],
            ],
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function buildCalendarPayload(Request $request): array
    {
        $tenant = TenantContext::getTenant();
        $tenantId = $tenant?->id ?? 1;
        $institution = TenantContext::getInstitution();
        $defaultFy = (int) ($institution?->current_fy ?: date('Y'));
        $today = Carbon::today();

        $selectedPeriod = (string) $request->query('period', 'all');
        $selectedCampus = (string) $request->query('campus_id', 'all');
        $selectedStatus = (string) $request->query('status', 'all');
        $selectedCategory = (string) $request->query('category', 'all');

        $campuses = Campus::where('tenant_id', $tenantId)->orderBy('campus_name')->get();

        $proposalsQuery = ExtensionProposal::with(['campus', 'academicProgram', 'extensionPlan', 'sdgs'])
            ->where('tenant_id', $tenantId);

        $plansQuery = ExtensionPlan::with(['campus', 'academicProgram', 'extensionProposal'])
            ->where('tenant_id', $tenantId);

        if ($selectedCampus !== 'all' && is_numeric($selectedCampus)) {
            $proposalsQuery->where('campus_id', (int) $selectedCampus);
            $plansQuery->where('campus_id', (int) $selectedCampus);
        }

        if ($selectedStatus !== 'all') {
            if ($selectedStatus === 'approved') {
                $proposalsQuery->whereIn('status', ['approved', 'Approved']);
                $plansQuery->whereIn('status', ['approved', 'Approved']);
            } elseif ($selectedStatus === 'in_review') {
                $proposalsQuery->whereIn('status', ['submitted', 'reviewed', 'noted', 'concurred', 'recommended']);
                $plansQuery->whereIn('status', ['submitted', 'noted']);
            } elseif ($selectedStatus === 'draft') {
                $proposalsQuery->whereIn('status', ['draft', 'Draft', 'returned']);
                $plansQuery->whereIn('status', ['draft', 'Draft', 'returned']);
            }
        }

        $proposals = $proposalsQuery->latest()->get();
        $plans = $plansQuery->latest()->get();

        $availableYears = collect([$defaultFy, (int) date('Y'), $defaultFy + 1])
            ->merge($proposals->flatMap(fn ($p) => [$p->duration_start?->year, $p->duration_end?->year, $p->created_at?->year]))
            ->merge($plans->flatMap(fn ($p) => [$p->conduct_activity_start?->year, $p->conduct_activity_end?->year, $p->created_at?->year]))
            ->filter(fn ($y) => $y && $y >= 2020 && $y <= 2035)
            ->map(fn ($y) => (int) $y)
            ->unique()
            ->sortDesc()
            ->values()
            ->all();

        $filterYear = ($selectedPeriod !== 'all' && is_numeric($selectedPeriod)) ? (int) $selectedPeriod : null;

        $calendarEvents = [];
        $durationRoadmapItems = [];
        $ongoingExtensionsCount = 0;
        $upcomingCommencementsCount = 0;
        $completedExtensionsCount = 0;

        // 1. Process Extension Proposals (Commencement Date, Completion Date, Duration Span)
        foreach ($proposals as $proposal) {
            $start = $proposal->duration_start ?: $proposal->extensionPlan?->conduct_activity_start;
            $end = $proposal->duration_end ?: ($proposal->extensionPlan?->conduct_activity_end ?: $start);

            if ($filterYear !== null) {
                $matchesYear = ($start && $start->year === $filterYear)
                    || ($end && $end->year === $filterYear)
                    || (! $start && $proposal->created_at?->year === $filterYear);
                if (! $matchesYear) {
                    continue;
                }
            }

            $code = $proposal->proposal_code ?: 'PROP-'.str_pad((string) $proposal->id, 4, '0', STR_PAD_LEFT);
            $campusName = $proposal->campus?->campus_name ?? 'Main Campus';
            $programName = $proposal->academicProgram?->name ?? 'General Extension';
            $leaderName = $proposal->project_leader_name ?: ($proposal->prepared_by_name ?: 'Project Leader');
            $badge = $proposal->status_badge;
            $url = route('dss.rie.proposals.show', $proposal->getRouteKey());
            $pdfUrl = route('dss.rie.proposals.pdf', $proposal->getRouteKey());

            $durationDays = ($start && $end) ? max(1, (int) $start->diffInDays($end) + 1) : 0;
            $durationMonths = $proposal->duration_months ?: (($start && $end) ? max(1, (int) round($start->diffInMonths($end))) : null);
            $durationLabel = $proposal->duration_text
                ?: ($durationMonths ? $durationMonths.' '.($durationMonths === 1 ? 'Month' : 'Months').' ('.$durationDays.' Days)' : ($durationDays > 0 ? $durationDays.' Days' : 'Schedule Pending'));

            $timelineState = 'unscheduled';
            $timelineBadge = ['label' => 'UNSCHEDULED', 'class' => 'bg-secondary-subtle text-secondary', 'days_note' => 'Dates not set'];
            $progressPct = 0;

            if ($start && $end) {
                if ($today->betweenIncluded($start, $end)) {
                    $timelineState = 'ongoing';
                    $ongoingExtensionsCount++;
                    $elapsed = max(1, (int) $start->diffInDays($today) + 1);
                    $progressPct = min(100, max(10, (int) round(($elapsed / max(1, $durationDays)) * 100)));
                    $daysLeft = (int) $today->diffInDays($end);
                    $timelineBadge = [
                        'label' => 'ONGOING NOW',
                        'class' => 'bg-success text-white',
                        'days_note' => $daysLeft === 0 ? 'Ends today' : $daysLeft.' '.($daysLeft === 1 ? 'day' : 'days').' remaining',
                    ];
                } elseif ($start->gt($today)) {
                    $timelineState = 'upcoming';
                    $upcomingCommencementsCount++;
                    $daysUntil = (int) $today->diffInDays($start);
                    $timelineBadge = [
                        'label' => 'UPCOMING',
                        'class' => 'bg-primary text-white',
                        'days_note' => 'Starts in '.$daysUntil.' '.($daysUntil === 1 ? 'day' : 'days'),
                    ];
                } else {
                    $timelineState = 'completed';
                    $completedExtensionsCount++;
                    $progressPct = 100;
                    $daysAgo = (int) $end->diffInDays($today);
                    $timelineBadge = [
                        'label' => 'COMPLETED WINDOW',
                        'class' => 'bg-dark text-white',
                        'days_note' => 'Ended '.$daysAgo.' '.($daysAgo === 1 ? 'day' : 'days').' ago',
                    ];
                }
            }

            $activeMonths = [];
            if ($start && $end) {
                $cursor = $start->copy()->startOfMonth();
                $endMonth = $end->copy()->startOfMonth();
                $guard = 0;
                while ($cursor->lte($endMonth) && $guard < 24) {
                    if ($filterYear === null || $cursor->year === $filterYear) {
                        $activeMonths[] = (int) $cursor->month;
                    }
                    $cursor->addMonth();
                    $guard++;
                }
                $activeMonths = array_values(array_unique($activeMonths));
            }

            $resolvedSdgs = $proposal->resolved_sdg_alignments;
            $sdgNums = array_values(array_unique(array_merge(
                array_map('intval', (array) ($resolvedSdgs['selected'] ?? [])),
                array_map('intval', (array) ($resolvedSdgs['indirect'] ?? []))
            )));
            sort($sdgNums);

            $durationRoadmapItems[] = [
                'type' => 'Proposal',
                'code' => $code,
                'title' => $proposal->title,
                'banner_program' => $proposal->banner_program ?: 'Institutional Extension',
                'campus' => $campusName,
                'program' => $programName,
                'leader' => $leaderName,
                'location' => $proposal->project_location ?: $campusName,
                'start_date' => $start?->toDateString(),
                'start_formatted' => $start ? $start->format('M d, Y') : 'TBD',
                'end_date' => $end?->toDateString(),
                'end_formatted' => $end ? $end->format('M d, Y') : 'TBD',
                'duration_label' => $durationLabel,
                'duration_days' => $durationDays,
                'duration_months' => $durationMonths,
                'timeline_state' => $timelineState,
                'timeline_badge' => $timelineBadge,
                'progress_pct' => $progressPct,
                'active_months' => $activeMonths,
                'status_label' => $badge['label'] ?? strtoupper((string) $proposal->status),
                'status_class' => $badge['class'] ?? 'bg-secondary text-white',
                'budget_formatted' => $proposal->budget_cash_amount > 0 ? '₱'.number_format((float) $proposal->budget_cash_amount, 2) : 'In-Kind / Non-Cash',
                'sdgs' => $sdgNums,
                'url' => $url,
                'pdf_url' => $pdfUrl,
            ];

            if ($start && ($selectedCategory === 'all' || $selectedCategory === 'proposal_duration')) {
                $effectiveEnd = $end ?: $start;
                $rangeLabel = $start->isSameDay($effectiveEnd)
                    ? $start->format('M d, Y')
                    : ($start->year === $effectiveEnd->year
                        ? $start->format('M d').' – '.$effectiveEnd->format('M d, Y')
                        : $start->format('M d, Y').' – '.$effectiveEnd->format('M d, Y'));

                $calendarEvents[] = [
                    'id' => 'prop_dur_'.$proposal->id,
                    'title' => 'Commencement & Duration: '.$proposal->title,
                    'short_title' => $proposal->title,
                    'start' => $start->toDateString(),
                    'end' => $effectiveEnd->toDateString(),
                    'start_formatted' => $start->format('F d, Y'),
                    'end_formatted' => $effectiveEnd->format('F d, Y'),
                    'range_label' => $rangeLabel,
                    'is_range' => ! $start->isSameDay($effectiveEnd),
                    'span_days' => $durationDays,
                    'category' => 'proposal_duration',
                    'category_label' => 'Proposal Commencement & Duration',
                    'color' => '#2563eb',
                    'icon' => 'mdi-rocket-launch-outline',
                    'doc_type' => 'Extension Proposal',
                    'code' => $code,
                    'campus' => $campusName,
                    'leader' => $leaderName,
                    'duration_label' => $durationLabel,
                    'timeline_state' => $timelineState,
                    'timeline_badge' => $timelineBadge,
                    'status_label' => $badge['label'] ?? strtoupper((string) $proposal->status),
                    'status_class' => $badge['class'] ?? 'bg-secondary text-white',
                    'budget_formatted' => $proposal->budget_cash_amount > 0 ? '₱'.number_format((float) $proposal->budget_cash_amount, 2) : 'In-Kind / Non-Cash',
                    'sdgs' => $sdgNums,
                    'url' => $url,
                    'pdf_url' => $pdfUrl,
                ];
            }
        }

        // 2. Process Extension Plans (Conduct of Activity, MOA Signing, Materials Arrival, PR, Monitoring & Evaluation)
        foreach ($plans as $plan) {
            $conductStart = $plan->conduct_activity_start;
            $conductEnd = $plan->conduct_activity_end ?: $conductStart;

            if ($filterYear !== null) {
                $planMatchesYear = ($conductStart && $conductStart->year === $filterYear)
                    || ($conductEnd && $conductEnd->year === $filterYear)
                    || ($plan->moa_signing_date && $plan->moa_signing_date->year === $filterYear)
                    || ($plan->monitoring_feedback_date && $plan->monitoring_feedback_date->year === $filterYear)
                    || (! $conductStart && $plan->created_at?->year === $filterYear);
                if (! $planMatchesYear) {
                    continue;
                }
            }

            $planCode = $plan->plan_code ?: 'PLAN-'.str_pad((string) $plan->id, 4, '0', STR_PAD_LEFT);
            $planTitle = $plan->extension_activity ?: $plan->program_name;
            $campusName = $plan->campus?->campus_name ?? 'Main Campus';
            $leaderName = $plan->prepared_by_name ?: 'Extension Proponent';
            $badge = $plan->status_badge;
            $url = route('dss.rie.plans.show', $plan->getRouteKey());
            $pdfUrl = route('dss.rie.plans.pdf', $plan->getRouteKey());

            // If this plan does NOT have an ExtensionProposal yet, also include it in the Duration Roadmap
            if (! $plan->extensionProposal && $conductStart) {
                $cEnd = $conductEnd ?: $conductStart;
                $cDays = max(1, (int) $conductStart->diffInDays($cEnd) + 1);
                $cState = 'upcoming';
                $cBadge = ['label' => 'PLANNED', 'class' => 'bg-info text-dark', 'days_note' => $conductStart->format('M d, Y')];
                $cPct = 0;

                if ($today->betweenIncluded($conductStart, $cEnd)) {
                    $cState = 'ongoing';
                    $ongoingExtensionsCount++;
                    $cPct = 50;
                    $cBadge = ['label' => 'ONGOING ACTIVITY', 'class' => 'bg-success text-white', 'days_note' => 'In progress'];
                } elseif ($conductStart->gt($today)) {
                    $cState = 'upcoming';
                    $upcomingCommencementsCount++;
                    $daysUntil = (int) $today->diffInDays($conductStart);
                    $cBadge = ['label' => 'UPCOMING PLAN', 'class' => 'bg-primary text-white', 'days_note' => 'Starts in '.$daysUntil.'d'];
                } else {
                    $cState = 'completed';
                    $completedExtensionsCount++;
                    $cPct = 100;
                    $cBadge = ['label' => 'COMPLETED', 'class' => 'bg-dark text-white', 'days_note' => 'Finished'];
                }

                $activeMonths = [];
                $cursor = $conductStart->copy()->startOfMonth();
                $endMonth = $cEnd->copy()->startOfMonth();
                $guard = 0;
                while ($cursor->lte($endMonth) && $guard < 24) {
                    if ($filterYear === null || $cursor->year === $filterYear) {
                        $activeMonths[] = (int) $cursor->month;
                    }
                    $cursor->addMonth();
                    $guard++;
                }

                $durationRoadmapItems[] = [
                    'type' => 'Extension Plan',
                    'code' => $planCode,
                    'title' => $planTitle,
                    'banner_program' => $plan->program_name ?: 'Extension Plan',
                    'campus' => $campusName,
                    'program' => $plan->program_name ?: 'Extension Program',
                    'leader' => $leaderName,
                    'location' => $campusName,
                    'start_date' => $conductStart->toDateString(),
                    'start_formatted' => $conductStart->format('M d, Y'),
                    'end_date' => $cEnd->toDateString(),
                    'end_formatted' => $cEnd->format('M d, Y'),
                    'duration_label' => $cDays.' '.($cDays === 1 ? 'Day' : 'Days').' Activity Window',
                    'duration_days' => $cDays,
                    'duration_months' => 1,
                    'timeline_state' => $cState,
                    'timeline_badge' => $cBadge,
                    'progress_pct' => $cPct,
                    'active_months' => array_values(array_unique($activeMonths)),
                    'status_label' => $badge['label'] ?? strtoupper((string) $plan->status),
                    'status_class' => $badge['class'] ?? 'bg-secondary text-white',
                    'budget_formatted' => '₱'.number_format((float) $plan->proposed_budget, 2),
                    'sdgs' => [],
                    'url' => $url,
                    'pdf_url' => $pdfUrl,
                ];
            }

            // Event A: Conduct of Extension Activity
            if ($conductStart && ($selectedCategory === 'all' || $selectedCategory === 'conduct_activity')) {
                $cEnd = $conductEnd ?: $conductStart;
                $cDays = max(1, (int) $conductStart->diffInDays($cEnd) + 1);
                $cRangeLabel = $conductStart->isSameDay($cEnd)
                    ? $conductStart->format('M d, Y')
                    : $conductStart->format('M d').' – '.$cEnd->format('M d, Y');

                $calendarEvents[] = [
                    'id' => 'plan_conduct_'.$plan->id,
                    'title' => 'Conduct of Activity: '.$planTitle,
                    'short_title' => $planTitle,
                    'start' => $conductStart->toDateString(),
                    'end' => $cEnd->toDateString(),
                    'start_formatted' => $conductStart->format('F d, Y'),
                    'end_formatted' => $cEnd->format('F d, Y'),
                    'range_label' => $cRangeLabel,
                    'is_range' => ! $conductStart->isSameDay($cEnd),
                    'span_days' => $cDays,
                    'category' => 'conduct_activity',
                    'category_label' => 'Conduct of Extension Activity',
                    'color' => '#059669',
                    'icon' => 'mdi-calendar-check-outline',
                    'doc_type' => 'Extension Plan',
                    'code' => $planCode,
                    'campus' => $campusName,
                    'leader' => $leaderName,
                    'duration_label' => $cDays.' '.($cDays === 1 ? 'Day' : 'Days').' Conduct',
                    'timeline_state' => $today->betweenIncluded($conductStart, $cEnd) ? 'ongoing' : ($conductStart->gt($today) ? 'upcoming' : 'completed'),
                    'timeline_badge' => ['label' => 'ACTIVITY CONDUCT', 'class' => 'bg-success text-white', 'days_note' => $conductStart->format('M d, Y')],
                    'status_label' => $badge['label'] ?? strtoupper((string) $plan->status),
                    'status_class' => $badge['class'] ?? 'bg-secondary text-white',
                    'budget_formatted' => '₱'.number_format((float) $plan->proposed_budget, 2),
                    'sdgs' => [],
                    'url' => $url,
                    'pdf_url' => $pdfUrl,
                ];
            }

            // Event B: MOA Signing Date
            if ($plan->moa_signing_date && ($selectedCategory === 'all' || $selectedCategory === 'moa_signing')) {
                $calendarEvents[] = [
                    'id' => 'plan_moa_'.$plan->id,
                    'title' => 'MOA Signing: '.$planTitle,
                    'short_title' => 'MOA: '.$planTitle,
                    'start' => $plan->moa_signing_date->toDateString(),
                    'end' => $plan->moa_signing_date->toDateString(),
                    'start_formatted' => $plan->moa_signing_date->format('F d, Y'),
                    'end_formatted' => $plan->moa_signing_date->format('F d, Y'),
                    'range_label' => $plan->moa_signing_date->format('M d, Y'),
                    'is_range' => false,
                    'span_days' => 1,
                    'category' => 'moa_signing',
                    'category_label' => 'MOA Signing Milestone',
                    'color' => '#7c3aed',
                    'icon' => 'mdi-file-sign',
                    'doc_type' => 'Extension Plan',
                    'code' => $planCode,
                    'campus' => $campusName,
                    'leader' => $leaderName,
                    'duration_label' => '1-Day Milestone',
                    'timeline_state' => $plan->moa_signing_date->isToday() ? 'ongoing' : ($plan->moa_signing_date->gt($today) ? 'upcoming' : 'completed'),
                    'timeline_badge' => ['label' => 'MOA SIGNING', 'class' => 'bg-primary text-white', 'days_note' => $plan->moa_signing_date->format('M d, Y')],
                    'status_label' => $badge['label'] ?? strtoupper((string) $plan->status),
                    'status_class' => $badge['class'] ?? 'bg-secondary text-white',
                    'budget_formatted' => '₱'.number_format((float) $plan->proposed_budget, 2),
                    'sdgs' => [],
                    'url' => $url,
                    'pdf_url' => $pdfUrl,
                ];
            }

            // Event C: Materials Arrival / Procurement
            if ($plan->materials_arrival_start && ($selectedCategory === 'all' || $selectedCategory === 'materials_procurement')) {
                $matEnd = $plan->materials_arrival_end ?: $plan->materials_arrival_start;
                $matDays = max(1, (int) $plan->materials_arrival_start->diffInDays($matEnd) + 1);
                $matRangeLabel = $plan->materials_arrival_start->isSameDay($matEnd)
                    ? $plan->materials_arrival_start->format('M d, Y')
                    : $plan->materials_arrival_start->format('M d').' – '.$matEnd->format('M d, Y');

                $calendarEvents[] = [
                    'id' => 'plan_mat_'.$plan->id,
                    'title' => 'Materials Arrival: '.$planTitle,
                    'short_title' => 'Materials: '.$planTitle,
                    'start' => $plan->materials_arrival_start->toDateString(),
                    'end' => $matEnd->toDateString(),
                    'start_formatted' => $plan->materials_arrival_start->format('F d, Y'),
                    'end_formatted' => $matEnd->format('F d, Y'),
                    'range_label' => $matRangeLabel,
                    'is_range' => ! $plan->materials_arrival_start->isSameDay($matEnd),
                    'span_days' => $matDays,
                    'category' => 'materials_procurement',
                    'category_label' => 'Arrival of Materials & Procurement',
                    'color' => '#d97706',
                    'icon' => 'mdi-truck-delivery-outline',
                    'doc_type' => 'Extension Plan',
                    'code' => $planCode,
                    'campus' => $campusName,
                    'leader' => $leaderName,
                    'duration_label' => $matDays.' '.($matDays === 1 ? 'Day' : 'Days').' Logistics Range',
                    'timeline_state' => $today->betweenIncluded($plan->materials_arrival_start, $matEnd) ? 'ongoing' : ($plan->materials_arrival_start->gt($today) ? 'upcoming' : 'completed'),
                    'timeline_badge' => ['label' => 'LOGISTICS / MATERIALS', 'class' => 'bg-warning text-dark', 'days_note' => $plan->materials_arrival_start->format('M d, Y')],
                    'status_label' => $badge['label'] ?? strtoupper((string) $plan->status),
                    'status_class' => $badge['class'] ?? 'bg-secondary text-white',
                    'budget_formatted' => '₱'.number_format((float) $plan->proposed_budget, 2),
                    'sdgs' => [],
                    'url' => $url,
                    'pdf_url' => $pdfUrl,
                ];
            }

            // Event D: Monitoring, Feedback & Evaluation
            if ($plan->monitoring_feedback_date && ($selectedCategory === 'all' || $selectedCategory === 'monitoring_report')) {
                $monEnd = $plan->monitoring_feedback_end_date ?: $plan->monitoring_feedback_date;
                $monDays = max(1, (int) $plan->monitoring_feedback_date->diffInDays($monEnd) + 1);
                $monRangeLabel = $plan->monitoring_feedback_date->isSameDay($monEnd)
                    ? $plan->monitoring_feedback_date->format('M d, Y')
                    : $plan->monitoring_feedback_date->format('M d').' – '.$monEnd->format('M d, Y');

                $calendarEvents[] = [
                    'id' => 'plan_mon_'.$plan->id,
                    'title' => 'Monitoring & Evaluation: '.$planTitle,
                    'short_title' => 'Monitoring: '.$planTitle,
                    'start' => $plan->monitoring_feedback_date->toDateString(),
                    'end' => $monEnd->toDateString(),
                    'start_formatted' => $plan->monitoring_feedback_date->format('F d, Y'),
                    'end_formatted' => $monEnd->format('F d, Y'),
                    'range_label' => $monRangeLabel,
                    'is_range' => ! $plan->monitoring_feedback_date->isSameDay($monEnd),
                    'span_days' => $monDays,
                    'category' => 'monitoring_report',
                    'category_label' => 'Monitoring, Feedback & Report',
                    'color' => '#db2777',
                    'icon' => 'mdi-clipboard-Check-multiple-outline',
                    'doc_type' => 'Extension Plan',
                    'code' => $planCode,
                    'campus' => $campusName,
                    'leader' => $leaderName,
                    'duration_label' => $monDays > 1 ? $monDays.' Days Monitoring Range' : 'Post-Activity Monitoring',
                    'timeline_state' => $today->betweenIncluded($plan->monitoring_feedback_date, $monEnd) ? 'ongoing' : ($plan->monitoring_feedback_date->gt($today) ? 'upcoming' : 'completed'),
                    'timeline_badge' => ['label' => 'MONITORING & EVAL', 'class' => 'bg-danger text-white', 'days_note' => $plan->monitoring_feedback_date->format('M d, Y')],
                    'status_label' => $badge['label'] ?? strtoupper((string) $plan->status),
                    'status_class' => $badge['class'] ?? 'bg-secondary text-white',
                    'budget_formatted' => '₱'.number_format((float) $plan->proposed_budget, 2),
                    'sdgs' => [],
                    'url' => $url,
                    'pdf_url' => $pdfUrl,
                ];
            }
        }

        usort($calendarEvents, fn ($a, $b) => strcmp((string) $a['start'], (string) $b['start']));
        usort($durationRoadmapItems, fn ($a, $b) => strcmp((string) ($a['start_date'] ?? '9999-12-31'), (string) ($b['start_date'] ?? '9999-12-31')));

        $totalScheduledEventsCount = count($calendarEvents);

        $categoryCounts = [
            'all' => $totalScheduledEventsCount,
            'proposal_duration' => count(array_filter($calendarEvents, fn ($e) => ($e['category'] ?? '') === 'proposal_duration')),
            'conduct_activity' => count(array_filter($calendarEvents, fn ($e) => ($e['category'] ?? '') === 'conduct_activity')),
            'materials_procurement' => count(array_filter($calendarEvents, fn ($e) => ($e['category'] ?? '') === 'materials_procurement')),
            'moa_signing' => count(array_filter($calendarEvents, fn ($e) => ($e['category'] ?? '') === 'moa_signing')),
            'monitoring_report' => count(array_filter($calendarEvents, fn ($e) => ($e['category'] ?? '') === 'monitoring_report')),
        ];

        return [
            'institution' => $institution,
            'theme' => $theme ?? TenantContext::getTheme(),
            'campuses' => $campuses,
            'availableYears' => $availableYears,
            'selectedPeriod' => $selectedPeriod,
            'selectedCampus' => $selectedCampus,
            'selectedStatus' => $selectedStatus,
            'selectedCategory' => $selectedCategory,
            'categoryCounts' => $categoryCounts,
            'calendarEvents' => $calendarEvents,
            'durationRoadmapItems' => $durationRoadmapItems,
            'ongoingExtensionsCount' => $ongoingExtensionsCount,
            'upcomingCommencementsCount' => $upcomingCommencementsCount,
            'completedExtensionsCount' => $completedExtensionsCount,
            'totalScheduledEventsCount' => $totalScheduledEventsCount,
            'defaultYear' => $filterYear ?: $defaultFy,
            'defaultMonth' => (int) date('n'),
        ];
    }
}

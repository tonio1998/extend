<?php

namespace App\Http\Controllers\DSS;

use App\Http\Controllers\Controller;
use App\Models\Dts\Records;
use App\Models\Dts\Unit;
use App\Models\Policy\InstitutionalPolicy;
use App\Models\Policy\UniversityMemorandum;
use App\Models\RIE\ExtensionPlan;
use App\Models\RIE\ExtensionPlanActivity;
use App\Models\RIE\ExtensionProposal;
use App\Models\SaaS\Campus;
use App\Models\SaaS\DocumentAuditTrail;
use App\Models\SaaS\UploadedDocument;
use App\Models\User;
use App\Services\DSS\TenantContext;
use App\Services\PDF\DocumentWatermarkService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CentralizedDocumentUploadController extends Controller
{
    /**
     * Map friendly type aliases to full Model class names.
     */
    public static function resolveModelClass(?string $type): ?string
    {
        if (! $type) {
            return null;
        }

        return match (strtolower(trim($type))) {
            'extension_plan', 'extensionplan', 'rie_plan', 'plan' => ExtensionPlan::class,
            'extension_proposal', 'extensionproposal', 'rie_proposal', 'proposal' => ExtensionProposal::class,
            'extension_plan_activity', 'extensionplanactivity', 'activity', 'milestone', 'extension_activity' => ExtensionPlanActivity::class,
            'policy', 'institutional_policy', 'institutionalpolicy' => InstitutionalPolicy::class,
            'memo', 'memorandum', 'university_memorandum' => UniversityMemorandum::class,
            'dts', 'record', 'records' => Records::class,
            default => class_exists($type) ? $type : null,
        };
    }

    /**
     * Determine module name from model class or alias.
     */
    public static function resolveModule(?string $type): string
    {
        $resolved = self::resolveModelClass($type);

        return match ($resolved) {
            ExtensionPlan::class,
            ExtensionProposal::class,
            ExtensionPlanActivity::class => 'rie',
            InstitutionalPolicy::class => 'policy',
            UniversityMemorandum::class => 'memo',
            Records::class => 'dts',
            default => 'general',
        };
    }

    /**
     * Centralized Institutional Document Repository Masterlist.
     */
    public function index(Request $request): View|JsonResponse
    {
        $tenant = TenantContext::getTenant();
        $tenantId = $tenant?->id ?? 1;

        $moduleFilter = $request->query('module');
        $categoryFilter = $request->query('category');
        $physicallySignedOnly = $request->boolean('physically_signed');
        $formatFilter = $request->query('format');
        $storageFilter = $request->query('storage');
        $search = trim((string) $request->query('search', ''));
        $campusId = $request->query('campus_id');
        $unitId = $request->query('unit_id');
        $projectLeader = $request->query('project_leader');
        $sort = $request->query('sort', 'latest');
        $viewMode = $request->query('view', 'table');
        if (! in_array($viewMode, ['grid', 'table'], true)) {
            $viewMode = 'table';
        }

        $query = UploadedDocument::with([
            'uploader',
            'campus',
            'documentable' => function ($morphTo) {
                $morphTo->morphWith([
                    ExtensionPlanActivity::class => ['extensionPlan.academicProgram.department', 'extensionPlan.extensionProposal'],
                    ExtensionPlan::class => ['academicProgram.department', 'extensionProposal'],
                    ExtensionProposal::class => ['unit', 'academicProgram.department'],
                ]);
            },
        ])->where('tenant_id', $tenantId);

        if ($moduleFilter) {
            $query->where('module', $moduleFilter);
        }

        if ($categoryFilter) {
            $query->where('category', $categoryFilter);
        }

        if ($physicallySignedOnly) {
            $query->where('is_physically_signed', true);
        }

        if ($campusId) {
            $query->where('campus_id', $campusId);
        }

        // Unit / Department Filter
        if ($unitId) {
            $planIdsForUnit = ExtensionPlan::where('tenant_id', $tenantId)
                ->where(function ($pq) use ($unitId) {
                    $pq->whereHas('academicProgram', fn ($aq) => $aq->where('department_id', $unitId))
                        ->orWhereHas('extensionProposal', fn ($uq) => $uq->where('unit_id', $unitId));
                })->pluck('id')->toArray();

            $propIdsForUnit = ExtensionProposal::where('tenant_id', $tenantId)
                ->where('unit_id', $unitId)
                ->pluck('id')->toArray();

            $actIdsForUnit = ! empty($planIdsForUnit)
                ? ExtensionPlanActivity::whereIn('extension_plan_id', $planIdsForUnit)->pluck('id')->toArray()
                : [];

            $query->where(function ($q) use ($propIdsForUnit, $planIdsForUnit, $actIdsForUnit) {
                $q->where(function ($sub) use ($propIdsForUnit) {
                    $sub->where('documentable_type', ExtensionProposal::class)
                        ->whereIn('documentable_id', $propIdsForUnit);
                })->orWhere(function ($sub) use ($planIdsForUnit) {
                    $sub->where('documentable_type', ExtensionPlan::class)
                        ->whereIn('documentable_id', $planIdsForUnit);
                })->orWhere(function ($sub) use ($actIdsForUnit) {
                    $sub->where('documentable_type', ExtensionPlanActivity::class)
                        ->whereIn('documentable_id', $actIdsForUnit);
                })->orWhere(function ($sub) use ($planIdsForUnit) {
                    foreach ($planIdsForUnit as $pid) {
                        $sub->orWhereJsonContains('metadata->plan_id', (int) $pid);
                    }
                });
            });
        }

        // Project Leader Filter
        if ($projectLeader) {
            $planIdsForLeader = ExtensionPlan::where('tenant_id', $tenantId)
                ->where('prepared_by_name', $projectLeader)
                ->pluck('id')->toArray();

            $propIdsForLeader = ExtensionProposal::where('tenant_id', $tenantId)
                ->where('project_leader_name', $projectLeader)
                ->pluck('id')->toArray();

            $actIdsForLeader = ! empty($planIdsForLeader)
                ? ExtensionPlanActivity::whereIn('extension_plan_id', $planIdsForLeader)->pluck('id')->toArray()
                : [];

            $query->where(function ($q) use ($propIdsForLeader, $planIdsForLeader, $actIdsForLeader, $projectLeader) {
                $q->where(function ($sub) use ($propIdsForLeader) {
                    $sub->where('documentable_type', ExtensionProposal::class)
                        ->whereIn('documentable_id', $propIdsForLeader);
                })->orWhere(function ($sub) use ($planIdsForLeader) {
                    $sub->where('documentable_type', ExtensionPlan::class)
                        ->whereIn('documentable_id', $planIdsForLeader);
                })->orWhere(function ($sub) use ($actIdsForLeader) {
                    $sub->where('documentable_type', ExtensionPlanActivity::class)
                        ->whereIn('documentable_id', $actIdsForLeader);
                })->orWhere(function ($sub) use ($planIdsForLeader) {
                    foreach ($planIdsForLeader as $pid) {
                        $sub->orWhereJsonContains('metadata->plan_id', (int) $pid);
                    }
                })->orWhere('uploader_name', $projectLeader);
            });
        }

        if ($storageFilter === 'gdrive' || $storageFilter === 'cloud') {
            $query->where('disk', 'external');
        } elseif ($storageFilter === 'local') {
            $query->where('disk', '!=', 'external');
        }

        if ($formatFilter) {
            match ($formatFilter) {
                'pdf' => $query->where('extension', 'pdf'),
                'image' => $query->whereIn('extension', ['png', 'jpg', 'jpeg', 'webp', 'gif']),
                'sheet' => $query->whereIn('extension', ['xls', 'xlsx', 'csv']),
                'doc' => $query->whereIn('extension', ['doc', 'docx', 'txt', 'rtf']),
                'gdrive' => $query->where('disk', 'external'),
                'zip' => $query->where('extension', 'zip'),
                default => null,
            };
        }

        // Comprehensive Global Search across files, metadata, project leader, department & project code
        if ($search) {
            $matchedUnitIds = Unit::where('tenant_id', $tenantId)
                ->where(function ($uq) use ($search) {
                    $uq->where('unit_name', 'like', "%{$search}%")
                        ->orWhere('unit_code', 'like', "%{$search}%");
                })->pluck('id')->toArray();

            $searchPlanIds = ExtensionPlan::where('tenant_id', $tenantId)
                ->where(function ($pq) use ($search, $matchedUnitIds) {
                    $pq->where('plan_code', 'like', "%{$search}%")
                        ->orWhere('program_name', 'like', "%{$search}%")
                        ->orWhere('prepared_by_name', 'like', "%{$search}%");
                    if (! empty($matchedUnitIds)) {
                        $pq->orWhereHas('academicProgram', fn ($aq) => $aq->whereIn('department_id', $matchedUnitIds));
                    }
                })->pluck('id')->toArray();

            $searchProposalIds = ExtensionProposal::where('tenant_id', $tenantId)
                ->where(function ($prq) use ($search, $matchedUnitIds) {
                    $prq->where('proposal_code', 'like', "%{$search}%")
                        ->orWhere('title', 'like', "%{$search}%")
                        ->orWhere('project_leader_name', 'like', "%{$search}%");
                    if (! empty($matchedUnitIds)) {
                        $prq->orWhereIn('unit_id', $matchedUnitIds);
                    }
                })->pluck('id')->toArray();

            $searchActivityIds = ExtensionPlanActivity::where('tenant_id', $tenantId)
                ->where(function ($aq) use ($search, $searchPlanIds) {
                    $aq->where('title', 'like', "%{$search}%");
                    if (! empty($searchPlanIds)) {
                        $aq->orWhereIn('extension_plan_id', $searchPlanIds);
                    }
                })->pluck('id')->toArray();

            $query->where(function ($q) use ($search, $searchProposalIds, $searchPlanIds, $searchActivityIds) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('original_filename', 'like', "%{$search}%")
                    ->orWhere('sha256_hash', 'like', "%{$search}%")
                    ->orWhere('uploader_name', 'like', "%{$search}%")
                    ->orWhere('signatory_notes', 'like', "%{$search}%")
                    ->orWhere(function ($sub) use ($searchProposalIds, $searchPlanIds, $searchActivityIds) {
                        if (! empty($searchProposalIds)) {
                            $sub->orWhere(function ($s) use ($searchProposalIds) {
                                $s->where('documentable_type', ExtensionProposal::class)
                                    ->whereIn('documentable_id', $searchProposalIds);
                            });
                        }
                        if (! empty($searchPlanIds)) {
                            $sub->orWhere(function ($s) use ($searchPlanIds) {
                                $s->where('documentable_type', ExtensionPlan::class)
                                    ->whereIn('documentable_id', $searchPlanIds);
                            });
                            foreach (array_slice($searchPlanIds, 0, 10) as $pid) {
                                $sub->orWhereJsonContains('metadata->plan_id', (int) $pid);
                            }
                        }
                        if (! empty($searchActivityIds)) {
                            $sub->orWhere(function ($s) use ($searchActivityIds) {
                                $s->where('documentable_type', ExtensionPlanActivity::class)
                                    ->whereIn('documentable_id', $searchActivityIds);
                            });
                        }
                    });
            });
        }

        match ($sort) {
            'oldest' => $query->orderBy('created_at', 'asc')->orderBy('id', 'asc'),
            'name_asc' => $query->orderBy('title', 'asc'),
            'name_desc' => $query->orderBy('title', 'desc'),
            'size_desc' => $query->orderByDesc('file_size'),
            default => $query->orderByDesc('created_at')->orderByDesc('id'), // Default: sorted by latest
        };

        $documents = $query->paginate(24)->onEachSide(1)->withQueryString();

        $campuses = Campus::where('tenant_id', $tenantId)->orderBy('campus_name')->get();
        $units = Unit::where('tenant_id', $tenantId)
            ->whereNotNull('unit_name')
            ->orderBy('unit_name')
            ->get();

        $projectLeaders = ExtensionProposal::where('tenant_id', $tenantId)
            ->whereNotNull('project_leader_name')
            ->where('project_leader_name', '!=', '')
            ->pluck('project_leader_name')
            ->merge(
                ExtensionPlan::where('tenant_id', $tenantId)
                    ->whereNotNull('prepared_by_name')
                    ->where('prepared_by_name', '!=', '')
                    ->pluck('prepared_by_name')
            )
            ->filter()
            ->unique()
            ->sort()
            ->values();

        $totalCount = UploadedDocument::where('tenant_id', $tenantId)->count();
        $signedCount = UploadedDocument::where('tenant_id', $tenantId)->where('is_physically_signed', true)->count();
        $rieCount = UploadedDocument::where('tenant_id', $tenantId)->where('module', 'rie')->count();
        $gdriveCount = UploadedDocument::where('tenant_id', $tenantId)->where('disk', 'external')->count();
        $policyCount = UploadedDocument::where('tenant_id', $tenantId)->whereIn('module', ['policy', 'memo'])->count();
        $dtsCount = UploadedDocument::where('tenant_id', $tenantId)->where('module', 'dts')->count();
        $totalStorageBytes = (int) UploadedDocument::where('tenant_id', $tenantId)->where('disk', '!=', 'external')->sum('file_size');
        $systemStorage = $this->getSystemStorageMetrics($totalStorageBytes);
        $recentAuditTrails = DocumentAuditTrail::where('tenant_id', $tenantId)->with(['user'])->latest()->take(15)->get();

        $institution = TenantContext::getInstitution();
        $theme = TenantContext::getTheme();

        // Resolve Matched Project Leader Profile Card (if filtered or search matches a leader)
        $matchedLeader = null;
        $leaderLookup = $projectLeader ?: ($search ?: null);
        if ($leaderLookup) {
            $matchingLeaderName = ExtensionProposal::where('tenant_id', $tenantId)
                ->whereNotNull('project_leader_name')
                ->where(function ($lq) use ($leaderLookup) {
                    $lq->where('project_leader_name', $leaderLookup)
                        ->orWhere('project_leader_name', 'like', "%{$leaderLookup}%");
                })
                ->value('project_leader_name');

            if (! $matchingLeaderName) {
                $matchingLeaderName = ExtensionPlan::where('tenant_id', $tenantId)
                    ->whereNotNull('prepared_by_name')
                    ->where(function ($lq) use ($leaderLookup) {
                        $lq->where('prepared_by_name', $leaderLookup)
                            ->orWhere('prepared_by_name', 'like', "%{$leaderLookup}%");
                    })
                    ->value('prepared_by_name');
            }

            if ($matchingLeaderName) {
                $leaderProps = ExtensionProposal::where('tenant_id', $tenantId)
                    ->where('project_leader_name', $matchingLeaderName)
                    ->get();
                $propCount = $leaderProps->count();
                $totalBudget = (float) $leaderProps->sum('approved_budget');
                $latestProp = $leaderProps->first();
                $unitAffiliation = $latestProp?->unit?->unit_name ?: 'Academic Unit';

                $propIds = $leaderProps->pluck('id')->toArray();
                $docCount = UploadedDocument::where('tenant_id', $tenantId)
                    ->where(function ($dq) use ($propIds, $matchingLeaderName) {
                        $dq->where('documentable_type', ExtensionProposal::class)->whereIn('documentable_id', $propIds)
                            ->orWhere('uploader_name', $matchingLeaderName);
                    })->count();

                $words = explode(' ', trim($matchingLeaderName));
                $initials = '';
                foreach (array_slice($words, 0, 2) as $w) {
                    $initials .= strtoupper(substr($w, 0, 1));
                }

                $matchedLeader = [
                    'name' => $matchingLeaderName,
                    'initials' => $initials ?: 'PL',
                    'unit' => $unitAffiliation,
                    'projects_count' => $propCount,
                    'total_budget' => number_format($totalBudget, 2),
                    'documents_count' => $docCount,
                    'overview_url' => route('dss.rie.unit_monitoring.leader_transactions', ['name' => $matchingLeaderName]),
                    'proposals_url' => route('dss.rie.proposals.index', ['search' => $matchingLeaderName]),
                ];
            }
        }

        // Resolve Matched Academic Unit Profile Card (if filtered or search matches a unit)
        $matchedUnit = null;
        if ($unitId) {
            $unitModel = Unit::with(['campus', 'headEmployee', 'extensionHeadEmployee'])->find($unitId);
        } elseif ($search) {
            $unitModel = Unit::with(['campus', 'headEmployee', 'extensionHeadEmployee'])
                ->where('tenant_id', $tenantId)
                ->where(function ($uq) use ($search) {
                    $uq->where('unit_code', $search)
                        ->orWhere('unit_name', $search)
                        ->orWhere('unit_code', 'like', "%{$search}%")
                        ->orWhere('unit_name', 'like', "%{$search}%");
                })->first();
        } else {
            $unitModel = null;
        }

        if ($unitModel) {
            $uProps = ExtensionProposal::where('tenant_id', $tenantId)->where('unit_id', $unitModel->id)->get();
            $uPropCount = $uProps->count();
            $uBudget = (float) $uProps->sum('approved_budget');

            $planIds = ExtensionPlan::where('tenant_id', $tenantId)
                ->where(function ($pq) use ($unitModel) {
                    $pq->whereHas('academicProgram', fn ($aq) => $aq->where('department_id', $unitModel->id))
                        ->orWhereHas('extensionProposal', fn ($uq) => $uq->where('unit_id', $unitModel->id));
                })->pluck('id')->toArray();
            $propIds = $uProps->pluck('id')->toArray();

            $uDocCount = UploadedDocument::where('tenant_id', $tenantId)
                ->where(function ($dq) use ($propIds, $planIds) {
                    $dq->where(function ($sq) use ($propIds) {
                        $sq->where('documentable_type', ExtensionProposal::class)->whereIn('documentable_id', $propIds);
                    })->orWhere(function ($sq) use ($planIds) {
                        $sq->where('documentable_type', ExtensionPlan::class)->whereIn('documentable_id', $planIds);
                    });
                })->count();

            $headName = $unitModel->extensionHeadEmployee?->full_name ?: ($unitModel->head_name ?: ($unitModel->headEmployee?->full_name ?: 'Unit Head'));

            $matchedUnit = [
                'id' => $unitModel->id,
                'name' => $unitModel->unit_name,
                'code' => $unitModel->unit_code ?: 'UNIT',
                'campus' => $unitModel->campus?->campus_name ?: 'Main Campus',
                'head' => $headName,
                'projects_count' => $uPropCount,
                'documents_count' => $uDocCount,
                'total_budget' => number_format($uBudget, 2),
                'overview_url' => route('dss.rie.unit_monitoring.unit_transactions', ['id' => $unitModel->id]),
                'transactions_url' => route('dss.rie.unit_monitoring.unit_transactions', ['id' => $unitModel->id]),
            ];
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'viewMode' => $viewMode,
                'html' => view('dss.documents.partials.repository-canvas', compact(
                    'documents',
                    'campuses',
                    'units',
                    'projectLeaders',
                    'totalCount',
                    'signedCount',
                    'rieCount',
                    'gdriveCount',
                    'policyCount',
                    'dtsCount',
                    'totalStorageBytes',
                    'systemStorage',
                    'sort',
                    'moduleFilter',
                    'categoryFilter',
                    'physicallySignedOnly',
                    'formatFilter',
                    'storageFilter',
                    'search',
                    'campusId',
                    'unitId',
                    'projectLeader',
                    'viewMode',
                    'matchedLeader',
                    'matchedUnit'
                ))->render(),
                'counts' => [
                    'total' => $totalCount,
                    'signed' => $signedCount,
                    'rie' => $rieCount,
                    'gdrive' => $gdriveCount,
                    'policy' => $policyCount,
                    'dts' => $dtsCount,
                    'storage_mb' => number_format($totalStorageBytes / (1024 * 1024), 1),
                    'gdrive_count' => $gdriveCount,
                    'system_storage' => $systemStorage,
                ],
                'filters' => [
                    'module' => $moduleFilter,
                    'category' => $categoryFilter,
                    'physically_signed' => $physicallySignedOnly,
                    'format' => $formatFilter,
                    'storage' => $storageFilter,
                    'search' => $search,
                    'campus_id' => $campusId,
                    'unit_id' => $unitId,
                    'project_leader' => $projectLeader,
                    'sort' => $sort,
                    'view' => $viewMode,
                ],
                'pagination' => [
                    'total' => $documents->total(),
                    'first_item' => $documents->firstItem(),
                    'last_item' => $documents->lastItem(),
                    'current_page' => $documents->currentPage(),
                    'last_page' => $documents->lastPage(),
                ],
            ]);
        }

        return view('dss.documents.repository', compact(
            'documents',
            'campuses',
            'units',
            'projectLeaders',
            'totalCount',
            'signedCount',
            'rieCount',
            'gdriveCount',
            'policyCount',
            'dtsCount',
            'totalStorageBytes',
            'systemStorage',
            'recentAuditTrails',
            'institution',
            'theme',
            'sort',
            'moduleFilter',
            'categoryFilter',
            'physicallySignedOnly',
            'formatFilter',
            'storageFilter',
            'search',
            'campusId',
            'unitId',
            'projectLeader',
            'viewMode',
            'matchedLeader',
            'matchedUnit'
        ));
    }

    /**
     * Provide smart search suggestions for Units, Project Leaders, and Plans/Proposals.
     */
    public function suggestions(Request $request): JsonResponse
    {
        $tenant = TenantContext::getTenant();
        $tenantId = $tenant?->id ?? 1;
        $q = trim((string) $request->query('q', ''));

        // 1. Units / Departments with enriched metadata
        $units = Unit::where('tenant_id', $tenantId)
            ->with(['campus', 'headEmployee', 'extensionHeadEmployee'])
            ->whereNotNull('unit_name')
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('unit_name', 'like', "%{$q}%")
                        ->orWhere('unit_code', 'like', "%{$q}%");
                });
            })
            ->orderBy('unit_name')
            ->limit(5)
            ->get()
            ->map(function ($u) use ($tenantId) {
                $proposals = ExtensionProposal::where('tenant_id', $tenantId)->where('unit_id', $u->id)->get();
                $proposalCount = $proposals->count();
                $totalBudget = (float) $proposals->sum('approved_budget');

                $planIds = ExtensionPlan::where('tenant_id', $tenantId)
                    ->where(function ($pq) use ($u) {
                        $pq->whereHas('academicProgram', fn ($aq) => $aq->where('department_id', $u->id))
                            ->orWhereHas('extensionProposal', fn ($uq) => $uq->where('unit_id', $u->id));
                    })->pluck('id')->toArray();
                $propIds = $proposals->pluck('id')->toArray();

                $docCount = UploadedDocument::where('tenant_id', $tenantId)
                    ->where(function ($dq) use ($propIds, $planIds) {
                        $dq->where(function ($sq) use ($propIds) {
                            $sq->where('documentable_type', ExtensionProposal::class)->whereIn('documentable_id', $propIds);
                        })->orWhere(function ($sq) use ($planIds) {
                            $sq->where('documentable_type', ExtensionPlan::class)->whereIn('documentable_id', $planIds);
                        });
                    })->count();

                $headName = $u->extensionHeadEmployee?->full_name ?: ($u->head_name ?: ($u->headEmployee?->full_name ?: 'Unit Head'));

                return [
                    'id' => $u->id,
                    'name' => $u->unit_name,
                    'code' => $u->unit_code ?: 'UNIT',
                    'campus' => $u->campus?->campus_name ?: 'Main Campus',
                    'head' => $headName,
                    'projects_count' => $proposalCount,
                    'documents_count' => $docCount,
                    'total_budget' => number_format($totalBudget, 2),
                    'overview_url' => route('dss.rie.unit_monitoring.unit_transactions', ['id' => $u->id]),
                    'transactions_url' => route('dss.rie.unit_monitoring.unit_transactions', ['id' => $u->id]),
                    'action_label' => 'View all projects & files under '.$u->unit_name.($u->unit_code ? ' ('.$u->unit_code.')' : ''),
                ];
            });

        // 2. Project Leaders with profile card details
        $leaderNames = ExtensionProposal::where('tenant_id', $tenantId)
            ->whereNotNull('project_leader_name')
            ->where('project_leader_name', '!=', '')
            ->when($q, fn ($sub) => $sub->where('project_leader_name', 'like', "%{$q}%"))
            ->pluck('project_leader_name')
            ->merge(
                ExtensionPlan::where('tenant_id', $tenantId)
                    ->whereNotNull('prepared_by_name')
                    ->where('prepared_by_name', '!=', '')
                    ->when($q, fn ($sub) => $sub->where('prepared_by_name', 'like', "%{$q}%"))
                    ->pluck('prepared_by_name')
            )
            ->filter()
            ->unique()
            ->values()
            ->take(5);

        $leaders = $leaderNames->map(function ($name) use ($tenantId) {
            $proposals = ExtensionProposal::where('tenant_id', $tenantId)->where('project_leader_name', $name)->get();
            $proposalCount = $proposals->count();
            $totalBudget = (float) $proposals->sum('approved_budget');

            $latestProposal = $proposals->first();
            $unitName = $latestProposal?->unit?->unit_name ?: 'Academic Unit';

            $propIds = $proposals->pluck('id')->toArray();
            $docCount = UploadedDocument::where('tenant_id', $tenantId)
                ->where(function ($dq) use ($propIds, $name) {
                    $dq->where('documentable_type', ExtensionProposal::class)->whereIn('documentable_id', $propIds)
                        ->orWhere('uploader_name', $name);
                })->count();

            $words = explode(' ', trim($name));
            $initials = '';
            foreach (array_slice($words, 0, 2) as $w) {
                $initials .= strtoupper(substr($w, 0, 1));
            }

            return [
                'name' => $name,
                'initials' => $initials ?: 'PL',
                'unit' => $unitName,
                'projects_count' => $proposalCount,
                'total_budget' => number_format($totalBudget, 2),
                'documents_count' => $docCount,
                'overview_url' => route('dss.rie.unit_monitoring.leader_transactions', ['name' => $name]),
                'proposals_url' => route('dss.rie.proposals.index', ['search' => $name]),
                'action_label' => 'View all projects & files led by '.$name,
            ];
        });

        // 3. Extension Plans & Proposals
        $plans = ExtensionPlan::where('tenant_id', $tenantId)
            ->when($q, function ($query) use ($q) {
                $query->where('plan_code', 'like', "%{$q}%")
                    ->orWhere('program_name', 'like', "%{$q}%")
                    ->orWhere('prepared_by_name', 'like', "%{$q}%");
            })
            ->limit(4)
            ->get(['id', 'plan_code', 'program_name', 'prepared_by_name'])
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'code' => $p->plan_code,
                    'title' => $p->program_name,
                    'leader' => $p->prepared_by_name,
                    'type' => 'Extension Plan',
                    'action_label' => 'View files for '.$p->plan_code.' — '.$p->program_name,
                ];
            });

        $proposals = ExtensionProposal::where('tenant_id', $tenantId)
            ->when($q, function ($query) use ($q) {
                $query->where('proposal_code', 'like', "%{$q}%")
                    ->orWhere('title', 'like', "%{$q}%")
                    ->orWhere('project_leader_name', 'like', "%{$q}%");
            })
            ->limit(4)
            ->get(['id', 'proposal_code', 'title', 'project_leader_name'])
            ->map(function ($pr) {
                return [
                    'id' => $pr->id,
                    'code' => $pr->proposal_code ?: 'PROP-'.$pr->id,
                    'title' => $pr->title,
                    'leader' => $pr->project_leader_name,
                    'type' => 'Extension Proposal',
                    'action_label' => 'View files for '.($pr->proposal_code ?: 'PROP-'.$pr->id).' — '.$pr->title,
                ];
            });

        return response()->json([
            'query' => $q,
            'units' => $units,
            'leaders' => $leaders,
            'plans' => $plans,
            'proposals' => $proposals,
        ]);
    }

    /**
     * Upload and attach files/links to repository (Simultaneous Multi-File & Multi-GDrive support).
     */
    public function upload(Request $request): JsonResponse|RedirectResponse
    {
        // Sanitize and filter Google Drive links before validation
        if ($request->has('gdrive_urls') && is_array($request->input('gdrive_urls'))) {
            $cleaned = [];
            foreach ($request->input('gdrive_urls') as $u) {
                $trimmed = trim((string) $u);
                if ($trimmed !== '') {
                    if (! preg_match('#^https?://#i', $trimmed)) {
                        $trimmed = 'https://'.$trimmed;
                    }
                    $cleaned[] = $trimmed;
                }
            }
            $request->merge(['gdrive_urls' => $cleaned]);
        }
        if ($request->filled('gdrive_url')) {
            $trimmed = trim((string) $request->input('gdrive_url'));
            if (! preg_match('#^https?://#i', $trimmed)) {
                $trimmed = 'https://'.$trimmed;
            }
            $request->merge(['gdrive_url' => $trimmed]);
        }

        $validated = $request->validate([
            'file' => 'nullable|file|max:51200|mimes:pdf',
            'files' => 'nullable|array',
            'files.*' => 'nullable|file|max:51200|mimes:pdf',
            'gdrive_url' => 'nullable|string|max:1000',
            'gdrive_urls' => 'nullable|array',
            'gdrive_urls.*' => 'nullable|string|max:1000',
            'external_url' => 'nullable|string|max:1000',
            'documentable_type' => 'nullable|string|max:150',
            'documentable_id' => 'nullable|integer',
            'activity_id' => 'nullable|integer',
            'category' => 'nullable|string|max:80',
            'title' => 'nullable|string|max:255',
            'is_physically_signed' => 'nullable',
            'signed_date' => 'nullable|date',
            'signatory_notes' => 'nullable|string|max:1000',
            'module' => 'nullable|string|max:60',
        ], [
            'file.mimes' => 'Only PDF documents (.pdf) are accepted.',
            'files.*.mimes' => 'All uploaded files must be in PDF format (.pdf).',
        ]);

        // Collect all physical files
        $uploadedFiles = [];
        if ($request->hasFile('files')) {
            $uploadedFiles = array_filter((array) $request->file('files'));
        }
        if ($request->hasFile('file')) {
            $uploadedFiles[] = $request->file('file');
        }

        // Collect all Google Drive links
        $gdriveUrls = [];
        if ($request->has('gdrive_urls') && is_array($request->input('gdrive_urls'))) {
            foreach ($request->input('gdrive_urls') as $url) {
                $trimmed = trim((string) $url);
                if ($trimmed !== '') {
                    $gdriveUrls[] = $trimmed;
                }
            }
        }
        if (! empty($validated['gdrive_url'])) {
            $trimmed = trim((string) $validated['gdrive_url']);
            if ($trimmed !== '' && ! in_array($trimmed, $gdriveUrls, true)) {
                $gdriveUrls[] = $trimmed;
            }
        }
        if (! empty($validated['external_url'])) {
            $trimmed = trim((string) $validated['external_url']);
            if ($trimmed !== '' && ! in_array($trimmed, $gdriveUrls, true)) {
                $gdriveUrls[] = $trimmed;
            }
        }

        if (empty($uploadedFiles) && empty($gdriveUrls)) {
            $msg = 'Please upload at least one document file or provide a valid Google Drive link.';

            return $request->wantsJson() || $request->ajax()
                ? response()->json(['success' => false, 'message' => $msg], 422)
                : back()->withErrors(['file' => $msg]);
        }

        $user = Auth::user();
        $tenant = TenantContext::getTenant();
        $tenantId = $tenant?->id ?? 1;
        $campusId = $user?->campus_id ?? $tenant?->campuses()->first()?->id;

        $modelClass = self::resolveModelClass($validated['documentable_type'] ?? null);
        $targetId = $validated['documentable_id'] ?? null;
        $module = $validated['module'] ?? self::resolveModule($validated['documentable_type'] ?? null);
        $category = $validated['category'] ?: ($request->boolean('is_physically_signed') ? 'physically_signed_plan' : 'general');
        $isPhysicallySigned = $request->boolean('is_physically_signed') || $category === 'physically_signed_plan';
        $targetActivityId = $request->input('activity_id') ?: ($modelClass === ExtensionPlanActivity::class ? $targetId : null);

        $createdDocs = [];

        // 1. Process Physical Files
        foreach ($uploadedFiles as $file) {
            $originalFilename = $file->getClientOriginalName();
            $extension = strtolower($file->getClientOriginalExtension());
            $mimeType = $file->getMimeType();
            $fileSize = $file->getSize();
            $sha256Hash = hash_file('sha256', $file->getRealPath());

            $docTitle = ! empty($validated['title']) && count($uploadedFiles) === 1 && empty($gdriveUrls)
                ? trim($validated['title'])
                : ($isPhysicallySigned ? 'Physically Signed Copy — '.pathinfo($originalFilename, PATHINFO_FILENAME) : pathinfo($originalFilename, PATHINFO_FILENAME));

            $year = date('Y');
            $month = date('m');
            $safeHash = Str::random(24);
            $fileName = "{$safeHash}.{$extension}";
            $storageDirectory = "documents/{$module}/{$year}/{$month}";
            $storedPath = $file->storeAs($storageDirectory, $fileName, 'public');

            $doc = UploadedDocument::create([
                'tenant_id' => $tenantId,
                'campus_id' => $campusId,
                'module' => $module,
                'documentable_type' => $modelClass,
                'documentable_id' => $targetId,
                'category' => $category,
                'title' => $docTitle,
                'original_filename' => $originalFilename,
                'file_path' => $storedPath,
                'file_size' => $fileSize,
                'mime_type' => $mimeType,
                'extension' => $extension,
                'disk' => 'public',
                'sha256_hash' => $sha256Hash,
                'is_physically_signed' => $isPhysicallySigned,
                'signed_date' => $validated['signed_date'] ?? ($isPhysicallySigned ? now()->toDateString() : null),
                'signatory_notes' => $validated['signatory_notes'] ?? null,
                'version' => '1.0',
                'status' => 'active',
                'uploaded_by_user_id' => $user?->id,
                'uploader_name' => $user?->employee?->full_name ?: ($user?->name ?: 'Authorized Personnel'),
                'metadata' => [
                    'client_ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'uploaded_at' => now()->toIso8601String(),
                    'is_external' => false,
                ],
            ]);

            DocumentAuditTrail::record([
                'tenant_id' => $tenantId,
                'campus_id' => $campusId,
                'module' => $module,
                'uploaded_document_id' => $doc->id,
                'documentable_type' => $modelClass,
                'documentable_id' => $targetId,
                'action' => $isPhysicallySigned ? 'physically_signed_tagged' : 'uploaded',
                'version' => '1.0',
                'summary' => "Uploaded file {$originalFilename} to institutional repository",
                'sha256_hash' => $sha256Hash,
                'filename' => $originalFilename,
                'file_size' => $fileSize,
            ], $request);

            if ($targetActivityId) {
                $this->linkDocumentToMilestoneActivity($doc, $targetActivityId, $user, $validated['signatory_notes'] ?? null);
            }

            $createdDocs[] = $doc;
        }

        // 2. Process Google Drive URLs
        foreach ($gdriveUrls as $url) {
            $isGdrive = str_contains($url, 'drive.google.com');
            $originalFilename = count($gdriveUrls) === 1 && ! empty($validated['title'])
                ? trim($validated['title'])
                : ($isGdrive ? 'Google Drive Document' : 'Cloud Document Link');
            $sha256Hash = hash('sha256', $url);

            $doc = UploadedDocument::create([
                'tenant_id' => $tenantId,
                'campus_id' => $campusId,
                'module' => $module,
                'documentable_type' => $modelClass,
                'documentable_id' => $targetId,
                'category' => $category,
                'title' => $originalFilename,
                'original_filename' => $originalFilename,
                'file_path' => $url,
                'file_size' => 0,
                'mime_type' => 'application/vnd.google-apps.document',
                'extension' => $isGdrive ? 'gdrive' : 'url',
                'disk' => 'external',
                'sha256_hash' => $sha256Hash,
                'is_physically_signed' => $isPhysicallySigned,
                'signed_date' => $validated['signed_date'] ?? ($isPhysicallySigned ? now()->toDateString() : null),
                'signatory_notes' => $validated['signatory_notes'] ?? null,
                'version' => '1.0',
                'status' => 'active',
                'uploaded_by_user_id' => $user?->id,
                'uploader_name' => $user?->employee?->full_name ?: ($user?->name ?: 'Authorized Personnel'),
                'metadata' => [
                    'client_ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'uploaded_at' => now()->toIso8601String(),
                    'is_external' => true,
                    'is_gdrive' => $isGdrive,
                    'external_url' => $url,
                ],
            ]);

            DocumentAuditTrail::record([
                'tenant_id' => $tenantId,
                'campus_id' => $campusId,
                'module' => $module,
                'uploaded_document_id' => $doc->id,
                'documentable_type' => $modelClass,
                'documentable_id' => $targetId,
                'action' => 'gdrive_link_attached',
                'version' => '1.0',
                'summary' => "Registered Google Drive shareable link to repository: {$url}",
                'sha256_hash' => $sha256Hash,
                'filename' => $originalFilename,
                'file_size' => 0,
            ], $request);

            if ($targetActivityId) {
                $this->linkDocumentToMilestoneActivity($doc, $targetActivityId, $user, $validated['signatory_notes'] ?? null);
            }

            $createdDocs[] = $doc;
        }

        $count = count($createdDocs);
        $successMsg = $count > 1
            ? "Successfully registered {$count} documents/links to the institutional repository."
            : 'Document successfully registered to the institutional repository.';

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $successMsg,
                'data' => $createdDocs[0] ?? null,
                'documents' => $createdDocs,
                'count' => $count,
            ]);
        }

        return redirect()->route('dss.documents.repository')->with('success', $successMsg);
    }

    /**
     * Link uploaded document to an extension plan milestone activity.
     */
    protected function linkDocumentToMilestoneActivity(UploadedDocument $doc, int $activityId, ?User $user, ?string $notes = null): void
    {
        $act = ExtensionPlanActivity::find($activityId);
        if (! $act) {
            return;
        }

        $act->update([
            'uploaded_document_id' => $doc->id,
            'document_url' => $doc->file_path,
            'document_filename' => $doc->original_filename,
            'document_disk' => $doc->disk,
            'document_mime_type' => $doc->mime_type,
            'document_size' => $doc->file_size,
            'document_hash' => $doc->sha256_hash,
            'uploaded_at' => now(),
            'uploaded_by_user_id' => $user?->id,
            'file_path' => $doc->file_path,
            'file_name' => $doc->original_filename,
            'status' => 'completed',
            'completed_at' => now(),
            'remarks' => $notes ?: $act->remarks,
        ]);

        if ($act->extensionPlan && method_exists($act->extensionPlan, 'recordAuditTrail')) {
            $act->extensionPlan->recordAuditTrail(
                'Milestone Proof Uploaded via Repository',
                "Document proof for milestone '{$act->title}' was uploaded ({$doc->original_filename}) by ".($user?->name ?: 'Authorized User').'.',
                $user,
                $doc->id
            );
        }
    }

    /**
     * Secure file download.
     */
    public function download(int $id): StreamedResponse|BinaryFileResponse|Response|RedirectResponse
    {
        $doc = UploadedDocument::find($id);

        if (! $doc) {
            $inst = TenantContext::getInstitution();

            return response()->view('errors.document-not-found', [
                'doc' => null,
                'requestedId' => $id,
                'expectedPath' => null,
                'disk' => null,
                'inst' => $inst,
                'errorMessage' => "Document record #{$id} was not found in the institutional vault registry.",
            ], 404);
        }

        if ($doc->disk === 'external' || str_starts_with((string) $doc->file_path, 'http')) {
            DocumentAuditTrail::record([
                'tenant_id' => $doc->tenant_id,
                'campus_id' => $doc->campus_id,
                'module' => $doc->module,
                'uploaded_document_id' => $doc->id,
                'documentable_type' => $doc->documentable_type,
                'documentable_id' => $doc->documentable_id,
                'action' => 'downloaded',
                'version' => $doc->version,
                'summary' => "Accessed external document link: {$doc->original_filename} (v{$doc->version})",
                'sha256_hash' => $doc->sha256_hash,
                'filename' => $doc->original_filename,
                'file_size' => $doc->file_size,
            ], request());

            return redirect()->away($doc->file_path);
        }

        $exists = false;
        $expectedPath = null;
        try {
            $exists = Storage::disk($doc->disk)->exists($doc->file_path);
            $expectedPath = Storage::disk($doc->disk)->path($doc->file_path);
        } catch (\Throwable $e) {
            $expectedPath = $doc->file_path;
        }

        if (! $exists) {
            $inst = TenantContext::getInstitution();

            return response()->view('errors.document-not-found', [
                'doc' => $doc,
                'requestedId' => $id,
                'expectedPath' => $expectedPath,
                'disk' => $doc->disk,
                'inst' => $inst,
                'errorMessage' => "Physical file \"{$doc->original_filename}\" is missing on server storage volume.",
            ], 404);
        }

        // Record Centralized Download Audit Trail
        DocumentAuditTrail::record([
            'tenant_id' => $doc->tenant_id,
            'campus_id' => $doc->campus_id,
            'module' => $doc->module,
            'uploaded_document_id' => $doc->id,
            'documentable_type' => $doc->documentable_type,
            'documentable_id' => $doc->documentable_id,
            'action' => 'downloaded',
            'version' => $doc->version,
            'summary' => "Downloaded document: {$doc->original_filename} (v{$doc->version})",
            'sha256_hash' => $doc->sha256_hash,
            'filename' => $doc->original_filename,
            'file_size' => $doc->file_size,
            'file_size' => $doc->file_size,
        ], request());

        return DocumentWatermarkService::stream($doc, Auth::user(), true);
    }

    /**
     * Inline file stream / preview (PDF or image).
     */
    public function preview(int $id): BinaryFileResponse|StreamedResponse|Response|RedirectResponse
    {
        $doc = UploadedDocument::find($id);

        if (! $doc) {
            $inst = TenantContext::getInstitution();

            return response()->view('errors.document-not-found', [
                'doc' => null,
                'requestedId' => $id,
                'expectedPath' => null,
                'disk' => null,
                'inst' => $inst,
                'errorMessage' => "Document record #{$id} was not found in the institutional vault registry.",
            ], 404);
        }

        if ($doc->disk === 'external' || str_starts_with((string) $doc->file_path, 'http')) {
            DocumentAuditTrail::record([
                'tenant_id' => $doc->tenant_id,
                'campus_id' => $doc->campus_id,
                'module' => $doc->module,
                'uploaded_document_id' => $doc->id,
                'documentable_type' => $doc->documentable_type,
                'documentable_id' => $doc->documentable_id,
                'action' => 'previewed',
                'version' => $doc->version,
                'summary' => "Opened external document link preview: {$doc->original_filename} (v{$doc->version})",
                'sha256_hash' => $doc->sha256_hash,
                'filename' => $doc->original_filename,
                'file_size' => $doc->file_size,
            ], request());

            return redirect()->away($doc->file_path);
        }

        $exists = false;
        $expectedPath = null;
        try {
            $exists = Storage::disk($doc->disk)->exists($doc->file_path);
            $expectedPath = Storage::disk($doc->disk)->path($doc->file_path);
        } catch (\Throwable $e) {
            $expectedPath = $doc->file_path;
        }

        if (! $exists) {
            $inst = TenantContext::getInstitution();

            return response()->view('errors.document-not-found', [
                'doc' => $doc,
                'requestedId' => $id,
                'expectedPath' => $expectedPath,
                'disk' => $doc->disk,
                'inst' => $inst,
                'errorMessage' => "Physical file \"{$doc->original_filename}\" is missing on server storage volume.",
            ], 404);
        }

        // Record Centralized Preview Audit Trail
        DocumentAuditTrail::record([
            'tenant_id' => $doc->tenant_id,
            'campus_id' => $doc->campus_id,
            'module' => $doc->module,
            'uploaded_document_id' => $doc->id,
            'documentable_type' => $doc->documentable_type,
            'documentable_id' => $doc->documentable_id,
            'action' => 'previewed',
            'version' => $doc->version,
            'summary' => "Previewed/Streamed document: {$doc->original_filename} (v{$doc->version})",
            'sha256_hash' => $doc->sha256_hash,
            'filename' => $doc->original_filename,
            'file_size' => $doc->file_size,
        ], request());

        return DocumentWatermarkService::stream($doc, Auth::user(), false);
    }

    /**
     * Soft delete uploaded document.
     */
    public function destroy(int $id): JsonResponse|RedirectResponse
    {
        $doc = UploadedDocument::findOrFail($id);
        $filename = $doc->original_filename;

        // Record Centralized Deletion Audit Trail
        DocumentAuditTrail::record([
            'tenant_id' => $doc->tenant_id,
            'campus_id' => $doc->campus_id,
            'module' => $doc->module,
            'uploaded_document_id' => $doc->id,
            'documentable_type' => $doc->documentable_type,
            'documentable_id' => $doc->documentable_id,
            'action' => 'deleted',
            'version' => $doc->version,
            'summary' => "Archived/Deleted document: {$filename} (v{$doc->version})",
            'sha256_hash' => $doc->sha256_hash,
            'filename' => $filename,
            'file_size' => $doc->file_size,
        ], request());

        // Find any linked activity before deleting
        $linkedActivity = ExtensionPlanActivity::where('uploaded_document_id', $doc->id)
            ->orWhere(function ($q) use ($doc) {
                $q->where('id', $doc->documentable_id)
                    ->where('documentable_type', ExtensionPlanActivity::class);
            })->first();

        $doc->delete();

        if ($linkedActivity) {
            $remaining = UploadedDocument::where('documentable_type', ExtensionPlanActivity::class)
                ->where('documentable_id', $linkedActivity->id)
                ->where('id', '!=', $doc->id)
                ->count();
            if ($remaining === 0) {
                $linkedActivity->update([
                    'uploaded_document_id' => null,
                    'document_url' => null,
                    'document_filename' => null,
                    'document_disk' => null,
                    'document_mime_type' => null,
                    'document_size' => null,
                    'document_hash' => null,
                    'uploaded_at' => null,
                    'uploaded_by_user_id' => null,
                    'file_path' => null,
                    'file_name' => null,
                    'status' => 'pending',
                    'completed_at' => null,
                ]);
            }
        }

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Attachment \"{$filename}\" removed successfully.",
            ]);
        }

        return back()->with('success', "Attachment \"{$filename}\" removed successfully.");
    }

    /**
     * Fetch list of attachments for a specific model (JSON).
     */
    public function list(Request $request): JsonResponse
    {
        $modelClass = self::resolveModelClass($request->query('documentable_type'));
        $modelId = (int) $request->query('documentable_id');

        abort_unless($modelClass && $modelId, 400, 'Invalid documentable parameters.');

        $documents = UploadedDocument::with(['uploader'])
            ->where('documentable_type', $modelClass)
            ->where('documentable_id', $modelId)
            ->orderBy('is_physically_signed', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'count' => $documents->count(),
            'physically_signed_count' => $documents->where('is_physically_signed', true)->count(),
            'documents' => $documents,
        ]);
    }

    /**
     * Compute actual server and system disk metrics.
     *
     * @return array<string, mixed>
     */
    protected function getSystemStorageMetrics(int $vaultBytes): array
    {
        $storageDir = storage_path();
        $diskTotal = (float) (@disk_total_space($storageDir) ?: 0);
        $diskFree = (float) (@disk_free_space($storageDir) ?: 0);
        $diskUsed = max(0, $diskTotal - $diskFree);
        $percent = $diskTotal > 0 ? (int) round(($diskUsed / $diskTotal) * 100) : 0;

        $status = $percent >= 90 ? 'danger' : ($percent >= 75 ? 'warning' : 'success');
        $statusLabel = $percent >= 90 ? 'CRITICAL' : ($percent >= 75 ? 'WARNING' : 'HEALTHY');

        return [
            'total_bytes' => $diskTotal,
            'free_bytes' => $diskFree,
            'used_bytes' => $diskUsed,
            'percent' => $percent,
            'status' => $status,
            'status_label' => $statusLabel,
            'total_formatted' => $this->formatBytes($diskTotal),
            'free_formatted' => $this->formatBytes($diskFree),
            'used_formatted' => $this->formatBytes($diskUsed),
            'vault_formatted' => $this->formatBytes($vaultBytes),
        ];
    }

    /**
     * Format byte values into human-readable strings.
     */
    protected function formatBytes(float|int $bytes, int $precision = 1): string
    {
        if ($bytes <= 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $base = log($bytes, 1024);
        $floor = (int) floor($base);
        $index = min($floor, count($units) - 1);

        return round(pow(1024, $base - $floor), $precision).' '.$units[$index];
    }
}

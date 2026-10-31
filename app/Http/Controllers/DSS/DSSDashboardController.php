<?php

namespace App\Http\Controllers\DSS;

use App\Http\Controllers\Controller;
use App\Models\Dts\Unit;
use App\Models\RIE\ExtensionAgenda;
use App\Models\RIE\ExtensionBannerProgram;
use App\Models\RIE\ExtensionBudget;
use App\Models\RIE\ExtensionPlan;
use App\Models\RIE\ExtensionProposal;
use App\Models\RIE\SustainableDevelopmentGoal;
use App\Models\SaaS\Campus;
use App\Models\SaaS\DocumentSignatory;
use App\Models\User;
use App\Services\DSS\TenantContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class DSSDashboardController extends Controller
{
    public function index(Request $request)
    {
        $tenant = TenantContext::getTenant();
        $institution = TenantContext::getInstitution();
        $theme = TenantContext::getTheme();
        $defaultFy = (int) ($institution->current_fy ?: date('Y'));
        $user = auth()->user();

        $effectiveRole = $user ? $user->getEffectiveDashboardRole() : 'director';
        $canSwitchView = $user && ($user->isSuperAdmin() || $user->isExtensionDirector() || $user->isExtensionOfficeStaff());
        $activeRoleView = $canSwitchView ? $request->query('view', $effectiveRole) : $effectiveRole;

        $rawPeriod = $request->query('period', $request->has('fy') ? $request->query('fy') : 'all');
        $isAllTime = ($rawPeriod === 'all' || $rawPeriod === '' || $rawPeriod === null);
        $selectedPeriod = $isAllTime ? 'all' : (string) (int) $rawPeriod;
        $fy = $isAllTime ? $defaultFy : (int) $rawPeriod;
        $periodLabel = $isAllTime ? 'Whole / All-Time' : 'FY '.$fy;

        $planYears = ExtensionPlan::where('tenant_id', $tenant->id)
            ->get(['conduct_activity_start', 'created_at'])
            ->flatMap(fn ($p) => [
                $p->conduct_activity_start?->year,
                $p->created_at?->year,
            ])
            ->filter()
            ->all();

        $proposalYears = ExtensionProposal::where('tenant_id', $tenant->id)
            ->get(['duration_start', 'created_at'])
            ->flatMap(fn ($p) => [
                $p->duration_start?->year,
                $p->created_at?->year,
            ])
            ->filter()
            ->all();

        $budgetYears = ExtensionBudget::where('tenant_id', $tenant->id)
            ->pluck('fiscal_year')
            ->map(fn ($y) => (int) $y)
            ->filter()
            ->all();

        $availableYears = collect(array_merge($planYears, $proposalYears, $budgetYears, [$defaultFy, (int) date('Y'), $defaultFy - 1]))
            ->map(fn ($y) => (int) $y)
            ->filter(fn ($y) => $y >= 2020 && $y <= 2035)
            ->unique()
            ->sortDesc()
            ->values()
            ->all();

        $applyPlanPeriodScope = function (Builder $q) use ($isAllTime, $fy): Builder {
            if ($isAllTime) {
                return $q;
            }

            return $q->where(function (Builder $sub) use ($fy) {
                $sub->whereYear('conduct_activity_start', $fy)
                    ->orWhere(function (Builder $fallback) use ($fy) {
                        $fallback->whereNull('conduct_activity_start')
                            ->whereYear('created_at', $fy);
                    })
                    ->orWhere('plan_code', 'like', '%-'.$fy.'-%');
            });
        };

        $applyProposalPeriodScope = function (Builder $q) use ($isAllTime, $fy): Builder {
            if ($isAllTime) {
                return $q;
            }

            return $q->where(function (Builder $sub) use ($fy) {
                $sub->whereYear('duration_start', $fy)
                    ->orWhere(function (Builder $fallback) use ($fy) {
                        $fallback->whereNull('duration_start')
                            ->whereYear('created_at', $fy);
                    })
                    ->orWhere('proposal_code', 'like', '%-'.$fy.'-%');
            });
        };

        $scopedPlans = fn () => $applyPlanPeriodScope(ExtensionPlan::where('tenant_id', $tenant->id));
        $scopedProposals = fn () => $applyProposalPeriodScope(ExtensionProposal::where('tenant_id', $tenant->id));

        $totalPlans = $scopedPlans()->count();
        $totalProposals = $scopedProposals()->count();
        $approvedCount = $scopedProposals()->whereIn('status', ['Approved', 'approved'])->count();
        $inReviewCount = $scopedProposals()->whereIn('status', ['submitted', 'reviewed', 'noted', 'concurred', 'recommended', 'In-Review', 'in-review'])->count();
        $draftCount = $scopedProposals()->whereIn('status', ['Draft', 'draft', 'pending', 'returned'])->count();
        $totalValuation = (float) $scopedProposals()->sum('budget_cash_amount');

        $totalProposedPlanBudget = (float) $scopedPlans()->sum('proposed_budget');
        $totalTargetBeneficiaries = (int) $scopedPlans()->sum('target_beneficiaries_count');

        $activeBudget = ExtensionBudget::where('tenant_id', $tenant->id)
            ->when(! $isAllTime, fn ($q) => $q->where('fiscal_year', $fy))
            ->orderByDesc('fiscal_year')
            ->first();

        $totalApprovedBudget = $activeBudget ? $activeBudget->effective_total : 0.00;
        $totalRemainingBalance = $activeBudget ? $activeBudget->remaining_balance : 0.00;
        $budgetBurnRate = $activeBudget ? $activeBudget->burn_rate : 0.0;

        $totalTrackedPlans = $scopedPlans()
            ->whereNotNull('tracking_number')
            ->where('tracking_number', '!=', '')
            ->count();
        $untrackedPlansCount = max(0, $totalPlans - $totalTrackedPlans);
        $trackingCoverageRate = $totalPlans > 0 ? round(($totalTrackedPlans / $totalPlans) * 100, 1) : 0.0;

        $pendingDirectorSignaturesCount = DocumentSignatory::where('tenant_id', $tenant->id)
            ->where('status', 'pending')
            ->whereIn('tier_role', ['concurred', 'approved'])
            ->count();

        $pendingDeanSignaturesCount = DocumentSignatory::where('tenant_id', $tenant->id)
            ->where('status', 'pending')
            ->where('tier_role', 'noted')
            ->count();

        $pendingCollegeSignaturesCount = DocumentSignatory::where('tenant_id', $tenant->id)
            ->where('status', 'pending')
            ->where('tier_role', 'reviewed')
            ->count();

        $pendingMySignaturesCount = 0;
        $pendingMySignatures = collect();
        if ($user) {
            $pendingMySignatures = DocumentSignatory::with(['document'])
                ->pending()
                ->forSignatoryUser($user)
                ->where('tenant_id', $tenant->id)
                ->latest()
                ->take(5)
                ->get();
            $pendingMySignaturesCount = $pendingMySignatures->count();
        }

        $campusCount = Campus::where('tenant_id', $tenant->id)->count();
        $deptCount = Unit::where('tenant_id', $tenant->id)->count();
        $usersCount = User::where('tenant_id', $tenant->id)->count();

        $campuses = Campus::where('tenant_id', $tenant->id)->get()->map(function ($campus) use ($scopedPlans, $scopedProposals) {
            $plansCount = $scopedPlans()->where('campus_id', $campus->id)->count();
            $proposalsCount = $scopedProposals()->where('campus_id', $campus->id)->count();
            $allocatedBudget = (float) $scopedPlans()->where('campus_id', $campus->id)->sum('proposed_budget');
            $trackedCount = $scopedPlans()
                ->where('campus_id', $campus->id)
                ->whereNotNull('tracking_number')
                ->where('tracking_number', '!=', '')
                ->count();

            return [
                'id' => $campus->id,
                'name' => $campus->campus_name,
                'code' => $campus->campus_code,
                'plans_count' => $plansCount,
                'proposals_count' => $proposalsCount,
                'allocated_budget' => $allocatedBudget,
                'tracked_count' => $trackedCount,
                'tracking_rate' => $plansCount > 0 ? round(($trackedCount / $plansCount) * 100) : 0,
            ];
        });

        $userCampusId = $user?->campus_id ?: ($campuses->first()['id'] ?? 1);

        $collegeProposals = $scopedProposals()->with(['campus'])
            ->where('campus_id', $userCampusId)
            ->latest()
            ->take(6)
            ->get();

        $collegePlans = $scopedPlans()->with(['campus'])
            ->where('campus_id', $userCampusId)
            ->latest()
            ->take(5)
            ->get();

        $collegeProposalsCount = $scopedProposals()->where('campus_id', $userCampusId)->count();
        $collegePlansCount = $scopedPlans()->where('campus_id', $userCampusId)->count();

        $recentProposals = $scopedProposals()->with(['campus'])
            ->latest()
            ->take(6)
            ->get();

        $recentPlans = $scopedPlans()->with(['campus'])
            ->latest()
            ->take(5)
            ->get();

        $myProposals = $user ? $scopedProposals()->with(['campus'])->where('prepared_by_user_id', $user->id)->latest()->take(5)->get() : collect();
        $myPlans = $user ? $scopedPlans()->with(['campus'])->where('prepared_by_user_id', $user->id)->latest()->take(5)->get() : collect();
        $myProposalsCount = $user ? $scopedProposals()->where('prepared_by_user_id', $user->id)->count() : 0;
        $myPlansCount = $user ? $scopedPlans()->where('prepared_by_user_id', $user->id)->count() : 0;

        $bannerPrograms = ExtensionBannerProgram::active()->ordered()->get();
        $extensionAgendas = ExtensionAgenda::active()->ordered()->get();
        $sdgs = SustainableDevelopmentGoal::ordered()->take(17)->get();

        $defaultSdgCatalog = [
            1 => ['name' => 'No Poverty', 'short_name' => 'No Poverty', 'color_hex' => '#E5243B', 'icon' => 'mdi-home-heart'],
            2 => ['name' => 'Zero Hunger', 'short_name' => 'Zero Hunger', 'color_hex' => '#DDA63A', 'icon' => 'mdi-food-apple-outline'],
            3 => ['name' => 'Good Health & Well-being', 'short_name' => 'Good Health', 'color_hex' => '#4C9F38', 'icon' => 'mdi-heart-pulse'],
            4 => ['name' => 'Quality Education', 'short_name' => 'Quality Education', 'color_hex' => '#C5192D', 'icon' => 'mdi-school-outline'],
            5 => ['name' => 'Gender Equality', 'short_name' => 'Gender Equality', 'color_hex' => '#FF3A21', 'icon' => 'mdi-gender-male-female'],
            6 => ['name' => 'Clean Water & Sanitation', 'short_name' => 'Clean Water', 'color_hex' => '#26BDE2', 'icon' => 'mdi-water-outline'],
            7 => ['name' => 'Affordable & Clean Energy', 'short_name' => 'Clean Energy', 'color_hex' => '#FCC30B', 'icon' => 'mdi-solar-power-variant-outline'],
            8 => ['name' => 'Decent Work & Economic Growth', 'short_name' => 'Decent Work', 'color_hex' => '#A21942', 'icon' => 'mdi-chart-line'],
            9 => ['name' => 'Industry, Innovation & Infrastructure', 'short_name' => 'Industry & Innovation', 'color_hex' => '#FD6925', 'icon' => 'mdi-lightbulb-on-outline'],
            10 => ['name' => 'Reduced Inequalities', 'short_name' => 'Reduced Inequalities', 'color_hex' => '#DD1367', 'icon' => 'mdi-scale-balance'],
            11 => ['name' => 'Sustainable Cities & Communities', 'short_name' => 'Sustainable Cities', 'color_hex' => '#FD9D24', 'icon' => 'mdi-city-variant-outline'],
            12 => ['name' => 'Responsible Consumption & Production', 'short_name' => 'Responsible Consumption', 'color_hex' => '#BF8B2E', 'icon' => 'mdi-recycle'],
            13 => ['name' => 'Climate Action', 'short_name' => 'Climate Action', 'color_hex' => '#3F7E44', 'icon' => 'mdi-weather-partly-cloudy'],
            14 => ['name' => 'Life Below Water', 'short_name' => 'Life Below Water', 'color_hex' => '#0A97D9', 'icon' => 'mdi-fish'],
            15 => ['name' => 'Life on Land', 'short_name' => 'Life on Land', 'color_hex' => '#56C02B', 'icon' => 'mdi-tree-outline'],
            16 => ['name' => 'Peace, Justice & Strong Institutions', 'short_name' => 'Peace & Justice', 'color_hex' => '#00689D', 'icon' => 'mdi-shield-check-outline'],
            17 => ['name' => 'Partnerships for the Goals', 'short_name' => 'Partnerships', 'color_hex' => '#19486A', 'icon' => 'mdi-handshake-outline'],
        ];

        $sdgsByGoal = $sdgs->keyBy('goal_number');
        $sdgMonitoring = [];

        foreach ($defaultSdgCatalog as $goalNum => $meta) {
            $dbSdg = $sdgsByGoal->get($goalNum);
            $sdgMonitoring[$goalNum] = [
                'goal_number' => $goalNum,
                'code' => $dbSdg?->code ?: 'SDG '.$goalNum,
                'name' => $dbSdg?->name ?: $meta['name'],
                'short_name' => $dbSdg?->short_name ?: $meta['short_name'],
                'color_hex' => $dbSdg?->color_hex ?: $meta['color_hex'],
                'icon' => $dbSdg?->icon ?: $meta['icon'],
                'extensions_count' => 0,
                'direct_count' => 0,
                'indirect_count' => 0,
                'approved_count' => 0,
                'total_budget' => 0.0,
                'proposals' => [],
            ];
        }

        // Initialize Banner Program Monitoring
        $bannerProgramMonitoring = [];
        $defaultBanners = $bannerPrograms->isNotEmpty()
            ? $bannerPrograms->map(fn ($b) => ['code' => $b->code ?: $b->name, 'name' => $b->name, 'description' => $b->description])->all()
            : [
                ['code' => 'TECHASSIST', 'name' => 'TECHASSIST', 'description' => 'Technology Assistance & Digital Transfer'],
                ['code' => 'FOODPRO', 'name' => 'FOODPRO', 'description' => 'Food Processing & Nutrition Security'],
                ['code' => 'AGRIYAMAN', 'name' => 'AGRIYAMAN', 'description' => 'Sustainable Agriculture & Fishery Development'],
                ['code' => 'MODFARM 360', 'name' => 'MODFARM 360', 'description' => 'Modernized Smart Farming Systems'],
                ['code' => 'GOVeRN', 'name' => 'GOVeRN', 'description' => 'Good Governance & Public Administration'],
                ['code' => 'SUSTINAHAN', 'name' => 'SUSTINAHAN', 'description' => 'Sustainable Livelihood & Community Enterprise'],
                ['code' => 'RENEWTECH', 'name' => 'RENEWTECH', 'description' => 'Renewable Energy & Clean Engineering'],
                ['code' => 'STEAMUP', 'name' => 'STEAMUP', 'description' => 'STEAM Education & Capacity Building'],
                ['code' => 'ECOREVIVE', 'name' => 'ECOREVIVE', 'description' => 'Ecological Restoration & Marine Conservation'],
                ['code' => 'GADLIGHT', 'name' => 'GADLIGHT', 'description' => 'Gender and Development Empowerment'],
            ];

        foreach ($defaultBanners as $bItem) {
            $bKey = trim((string) $bItem['name']);
            if ($bKey === '') {
                continue;
            }
            $bannerProgramMonitoring[$bKey] = [
                'code' => $bItem['code'] ?: $bKey,
                'name' => $bKey,
                'description' => $bItem['description'] ?: 'Institutional Extension Banner Program',
                'proposals_count' => 0,
                'approved_count' => 0,
                'total_budget' => 0.0,
                'proposals' => [],
            ];
        }

        // Initialize Extension Agenda Priorities Monitoring
        $agendaMonitoring = [];
        $defaultAgendas = $extensionAgendas->isNotEmpty()
            ? $extensionAgendas->pluck('name')->all()
            : [
                'Transformative Learning',
                'Livelihood Generation',
                'Biodiversity Conservation',
                'Climate Change Adoption & Risk Management',
                'Good Governance',
            ];

        foreach ($defaultAgendas as $agName) {
            $agKey = trim((string) $agName);
            if ($agKey === '') {
                continue;
            }
            $agendaMonitoring[$agKey] = [
                'name' => $agKey,
                'proposals_count' => 0,
                'approved_count' => 0,
                'total_budget' => 0.0,
                'percentage' => 0.0,
                'proposals' => [],
            ];
        }

        // Initialize 6Ps + 1P Expected Outputs Monitoring
        $expectedOutputsMonitoring = [
            'People' => ['category' => 'People', 'label' => 'People / Beneficiaries Trained', 'icon' => 'mdi-account-group-outline', 'color' => '#3b82f6', 'target_total' => 0, 'proposals_count' => 0, 'items' => []],
            'Product' => ['category' => 'Product', 'label' => 'Products / Technologies', 'icon' => 'mdi-cube-outline', 'color' => '#8b5cf6', 'target_total' => 0, 'proposals_count' => 0, 'items' => []],
            'Service' => ['category' => 'Service', 'label' => 'Technical Services Rendered', 'icon' => 'mdi-hand-heart-outline', 'color' => '#10b981', 'target_total' => 0, 'proposals_count' => 0, 'items' => []],
            'Partnership' => ['category' => 'Partnership', 'label' => 'Partnerships / MOAs Forged', 'icon' => 'mdi-handshake-outline', 'color' => '#f59e0b', 'target_total' => 0, 'proposals_count' => 0, 'items' => []],
            'Publication' => ['category' => 'Publication', 'label' => 'Publications / Manuals', 'icon' => 'mdi-book-open-page-variant-outline', 'color' => '#0ea5e9', 'target_total' => 0, 'proposals_count' => 0, 'items' => []],
            'Policy' => ['category' => 'Policy', 'label' => 'Policies Adopted', 'icon' => 'mdi-gavel', 'color' => '#ec4899', 'target_total' => 0, 'proposals_count' => 0, 'items' => []],
            'Patent/IP' => ['category' => 'Patent/IP', 'label' => 'Patents / Utility Models / IP', 'icon' => 'mdi-certificate-outline', 'color' => '#6366f1', 'target_total' => 0, 'proposals_count' => 0, 'items' => []],
        ];

        // Initialize GAD Monitoring
        $gadMonitoring = [
            'male_beneficiaries' => 0,
            'female_beneficiaries' => 0,
            'unspecified_beneficiaries' => 0,
            'direct_beneficiaries_total' => 0,
            'indirect_beneficiaries_total' => 0,
            'beneficiary_groups_count' => 0,
            'beneficiary_groups' => [],
            'male_leaders_count' => 0,
            'female_leaders_count' => 0,
            'unspecified_leaders_count' => 0,
        ];

        $collegesMonitoring = [];
        $interCollegeProposalsCount = 0;
        $partnerAgenciesMonitoring = [];
        $fundingStructureMonitoring = [
            'with_cash_count' => 0,
            'in_kind_count' => 0,
            'total_cash_budget' => 0.0,
            'with_partner_share_count' => 0,
            'approved_budget_total' => 0.0,
        ];

        $allProposalsForSdg = $scopedProposals()
            ->with(['sdgs', 'campus', 'academicProgram', 'unit'])
            ->latest()
            ->get();

        $proposalsWithSdgCount = 0;
        $proposalsWithBannerCount = 0;

        foreach ($allProposalsForSdg as $proposal) {
            $isApprovedProposal = in_array(strtolower((string) $proposal->status), ['approved'], true);
            $badge = $proposal->status_badge;
            $propSummary = [
                'id' => $proposal->id,
                'code' => $proposal->proposal_code ?: 'PROP-'.str_pad((string) $proposal->id, 4, '0', STR_PAD_LEFT),
                'title' => $proposal->title,
                'campus' => $proposal->campus?->campus_name ?? 'Main Campus',
                'program' => $proposal->academicProgram?->name ?? 'General Extension',
                'leader' => $proposal->project_leader_name ?: ($proposal->prepared_by_name ?: 'Extension Proponent'),
                'status' => ucfirst((string) ($proposal->status ?: 'draft')),
                'status_label' => $badge['label'] ?? strtoupper((string) ($proposal->status ?: 'DRAFT')),
                'status_class' => $badge['class'] ?? 'bg-secondary text-white',
                'budget' => (float) $proposal->budget_cash_amount,
                'budget_formatted' => $proposal->budget_cash_amount > 0 ? '₱'.number_format((float) $proposal->budget_cash_amount, 2) : 'In-Kind / Non-Cash',
                'url' => route('dss.rie.proposals.show', $proposal->getRouteKey()),
            ];

            // 1. SDG Monitoring
            $resolvedAlignments = $proposal->resolved_sdg_alignments;
            $directNums = array_values(array_unique(array_map('intval', (array) ($resolvedAlignments['selected'] ?? []))));
            $indirectNums = array_values(array_unique(array_map('intval', (array) ($resolvedAlignments['indirect'] ?? []))));

            foreach ($proposal->sdgs as $sdgRel) {
                $gNum = (int) $sdgRel->goal_number;
                $isPrimaryPivot = (bool) ($sdgRel->pivot->is_primary ?? true);
                if ($isPrimaryPivot && ! in_array($gNum, $indirectNums, true) && ! in_array($gNum, $directNums, true)) {
                    $directNums[] = $gNum;
                } elseif (! $isPrimaryPivot && ! in_array($gNum, $directNums, true) && ! in_array($gNum, $indirectNums, true)) {
                    $indirectNums[] = $gNum;
                }
            }

            $allTaggedForProposal = array_values(array_unique(array_merge($directNums, $indirectNums)));
            $validTagged = array_filter($allTaggedForProposal, fn ($n) => isset($sdgMonitoring[$n]));

            if (! empty($validTagged)) {
                $proposalsWithSdgCount++;
            }

            foreach ($validTagged as $goalNum) {
                $isDirect = in_array($goalNum, $directNums, true);
                $sdgMonitoring[$goalNum]['extensions_count']++;
                if ($isDirect) {
                    $sdgMonitoring[$goalNum]['direct_count']++;
                } else {
                    $sdgMonitoring[$goalNum]['indirect_count']++;
                }
                if ($isApprovedProposal) {
                    $sdgMonitoring[$goalNum]['approved_count']++;
                }
                $sdgMonitoring[$goalNum]['total_budget'] += (float) $proposal->budget_cash_amount;
                $sdgMonitoring[$goalNum]['proposals'][] = array_merge($propSummary, [
                    'alignment_type' => $isDirect ? 'Direct' : 'Indirect',
                ]);
            }

            // 2. Banner Program Monitoring
            $bannerName = trim((string) ($proposal->banner_program === 'Others' && ! empty($proposal->banner_program_other) ? $proposal->banner_program_other : $proposal->banner_program));
            if ($bannerName !== '') {
                $proposalsWithBannerCount++;
                if (! isset($bannerProgramMonitoring[$bannerName])) {
                    $bannerProgramMonitoring[$bannerName] = [
                        'code' => strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $bannerName), 0, 12)) ?: $bannerName,
                        'name' => $bannerName,
                        'description' => 'Institutional Extension Banner Program',
                        'proposals_count' => 0,
                        'approved_count' => 0,
                        'total_budget' => 0.0,
                        'proposals' => [],
                    ];
                }
                $bannerProgramMonitoring[$bannerName]['proposals_count']++;
                if ($isApprovedProposal) {
                    $bannerProgramMonitoring[$bannerName]['approved_count']++;
                }
                $bannerProgramMonitoring[$bannerName]['total_budget'] += (float) $proposal->budget_cash_amount;
                $bannerProgramMonitoring[$bannerName]['proposals'][] = $propSummary;
            }

            // 3. Extension Agenda Priorities Monitoring
            $proposalAgendas = is_array($proposal->agenda_priorities) ? $proposal->agenda_priorities : [];
            foreach ($proposalAgendas as $agItem) {
                $agTitle = trim((string) $agItem);
                if ($agTitle === '') {
                    continue;
                }
                if (! isset($agendaMonitoring[$agTitle])) {
                    $agendaMonitoring[$agTitle] = [
                        'name' => $agTitle,
                        'proposals_count' => 0,
                        'approved_count' => 0,
                        'total_budget' => 0.0,
                        'percentage' => 0.0,
                        'proposals' => [],
                    ];
                }
                $agendaMonitoring[$agTitle]['proposals_count']++;
                if ($isApprovedProposal) {
                    $agendaMonitoring[$agTitle]['approved_count']++;
                }
                $agendaMonitoring[$agTitle]['total_budget'] += (float) $proposal->budget_cash_amount;
                $agendaMonitoring[$agTitle]['proposals'][] = $propSummary;
            }

            // 4. 6Ps + 1P Expected Outputs Monitoring
            $rawOutputs = $proposal->expected_outputs;
            if (is_array($rawOutputs)) {
                $contributedCategories = [];
                foreach ($rawOutputs as $outItem) {
                    if (is_array($outItem) && isset($outItem['category'])) {
                        $cat = trim((string) $outItem['category']);
                        $targetVal = (int) ($outItem['target'] ?? 0);
                        $desc = trim((string) ($outItem['description'] ?? ''));
                        if (isset($expectedOutputsMonitoring[$cat]) && ($targetVal > 0 || $desc !== '')) {
                            $expectedOutputsMonitoring[$cat]['target_total'] += max(1, $targetVal);
                            $contributedCategories[$cat] = true;
                            if ($desc !== '' && count($expectedOutputsMonitoring[$cat]['items']) < 5) {
                                $expectedOutputsMonitoring[$cat]['items'][] = $desc.' ('.$propSummary['code'].')';
                            }
                        }
                    } elseif (is_string($outItem) && trim($outItem) !== '') {
                        $text = trim($outItem);
                        $lower = strtolower($text);
                        $qty = 1;
                        if (preg_match('/^(\d+)\s+/', $text, $m)) {
                            $qty = (int) $m[1];
                        }
                        $inferredCat = 'Service';
                        if (str_contains($lower, 'memorandum') || str_contains($lower, 'moa') || str_contains($lower, 'mou') || str_contains($lower, 'partner')) {
                            $inferredCat = 'Partnership';
                        } elseif (str_contains($lower, 'personnel') || str_contains($lower, 'certified') || str_contains($lower, 'trained') || str_contains($lower, 'beneficiar') || str_contains($lower, 'participant')) {
                            $inferredCat = 'People';
                        } elseif (str_contains($lower, 'system') || str_contains($lower, 'prototype') || str_contains($lower, 'software') || str_contains($lower, 'product')) {
                            $inferredCat = 'Product';
                        } elseif (str_contains($lower, 'handbook') || str_contains($lower, 'manual') || str_contains($lower, 'publication') || str_contains($lower, 'journal') || str_contains($lower, 'module')) {
                            $inferredCat = 'Publication';
                        } elseif (str_contains($lower, 'policy') || str_contains($lower, 'ordinance') || str_contains($lower, 'protocol')) {
                            $inferredCat = 'Policy';
                        } elseif (str_contains($lower, 'patent') || str_contains($lower, 'utility model') || str_contains($lower, 'copyright')) {
                            $inferredCat = 'Patent/IP';
                        }
                        $expectedOutputsMonitoring[$inferredCat]['target_total'] += $qty;
                        $contributedCategories[$inferredCat] = true;
                        if (count($expectedOutputsMonitoring[$inferredCat]['items']) < 5) {
                            $expectedOutputsMonitoring[$inferredCat]['items'][] = $text.' ('.$propSummary['code'].')';
                        }
                    }
                }
                foreach (array_keys($contributedCategories) as $cKey) {
                    $expectedOutputsMonitoring[$cKey]['proposals_count']++;
                }
            }

            // 5. GAD Beneficiaries & Project Leader Gender Monitoring
            $leaderGender = strtolower(trim((string) $proposal->project_leader_gender));
            if (in_array($leaderGender, ['male', 'm'], true)) {
                $gadMonitoring['male_leaders_count']++;
            } elseif (in_array($leaderGender, ['female', 'f'], true)) {
                $gadMonitoring['female_leaders_count']++;
            } else {
                $gadMonitoring['unspecified_leaders_count']++;
            }

            $benData = $proposal->target_beneficiaries_data;
            if (is_array($benData)) {
                if (isset($benData['direct']) || isset($benData['indirect'])) {
                    $directBen = (int) ($benData['direct'] ?? 0);
                    $indirectBen = (int) ($benData['indirect'] ?? 0);
                    $gadMonitoring['unspecified_beneficiaries'] += $directBen;
                    $gadMonitoring['direct_beneficiaries_total'] += $directBen;
                    $gadMonitoring['indirect_beneficiaries_total'] += $indirectBen;
                    if (! empty($benData['sectors']) && is_array($benData['sectors'])) {
                        foreach ($benData['sectors'] as $sec) {
                            if (trim((string) $sec) !== '') {
                                $gadMonitoring['beneficiary_groups_count']++;
                                $gadMonitoring['beneficiary_groups'][] = [
                                    'name' => trim((string) $sec),
                                    'male' => 0,
                                    'female' => 0,
                                    'total' => $directBen,
                                    'proposal_code' => $propSummary['code'],
                                ];
                            }
                        }
                    }
                } else {
                    foreach ($benData as $benRow) {
                        if (! is_array($benRow)) {
                            continue;
                        }
                        $gName = trim((string) ($benRow['name'] ?? ''));
                        $mCount = (int) ($benRow['male'] ?? 0);
                        $fCount = (int) ($benRow['female'] ?? 0);
                        $tCount = (int) ($benRow['total'] ?? ($mCount + $fCount));
                        if ($gName !== '' || $tCount > 0) {
                            $gadMonitoring['male_beneficiaries'] += $mCount;
                            $gadMonitoring['female_beneficiaries'] += $fCount;
                            $gadMonitoring['direct_beneficiaries_total'] += $tCount;
                            $gadMonitoring['beneficiary_groups_count']++;
                            if ($gName !== '') {
                                $gadMonitoring['beneficiary_groups'][] = [
                                    'name' => $gName,
                                    'male' => $mCount,
                                    'female' => $fCount,
                                    'total' => $tCount,
                                    'proposal_code' => $propSummary['code'],
                                ];
                            }
                        }
                    }
                }
            }

            // 6. Implementing Colleges & Partner Agencies Monitoring
            $impColleges = is_array($proposal->implementing_colleges) ? array_filter(array_map('trim', $proposal->implementing_colleges)) : [];
            if (empty($impColleges)) {
                $fallbackCol = $proposal->unit?->unit_name ?: ($proposal->academicProgram?->name ?: 'General Extension Unit');
                $impColleges = [$fallbackCol];
            }
            if (count($impColleges) > 1) {
                $interCollegeProposalsCount++;
            }
            foreach ($impColleges as $colName) {
                if (! isset($collegesMonitoring[$colName])) {
                    $collegesMonitoring[$colName] = [
                        'name' => $colName,
                        'proposals_count' => 0,
                        'approved_count' => 0,
                        'total_budget' => 0.0,
                    ];
                }
                $collegesMonitoring[$colName]['proposals_count']++;
                if ($isApprovedProposal) {
                    $collegesMonitoring[$colName]['approved_count']++;
                }
                $collegesMonitoring[$colName]['total_budget'] += (float) $proposal->budget_cash_amount;
            }

            $agenciesList = $proposal->resolved_collaborating_agencies;
            foreach ($agenciesList as $agRow) {
                $agencyTitle = trim((string) ($agRow['agency'] ?? ($agRow['name'] ?? '')));
                $agencyRole = trim((string) ($agRow['role'] ?? 'Collaborating Partner'));
                if ($agencyTitle === '') {
                    continue;
                }
                $partnerAgenciesMonitoring[] = [
                    'agency' => $agencyTitle,
                    'role' => $agencyRole ?: 'Collaborating Partner',
                    'proposal_code' => $propSummary['code'],
                    'proposal_title' => $proposal->title,
                    'proposal_url' => $propSummary['url'],
                ];
            }

            // 7. Funding Structure Monitoring
            if ((float) $proposal->budget_cash_amount > 0) {
                $fundingStructureMonitoring['with_cash_count']++;
                $fundingStructureMonitoring['total_cash_budget'] += (float) $proposal->budget_cash_amount;
            } else {
                $fundingStructureMonitoring['in_kind_count']++;
            }
            if ($isApprovedProposal) {
                $fundingStructureMonitoring['approved_budget_total'] += (float) $proposal->budget_cash_amount;
            }
            if (! empty(trim((string) ($proposal->fund_source_external ?: $proposal->budget_partner_share)))) {
                $fundingStructureMonitoring['with_partner_share_count']++;
            }
        }

        // Compute percentages & sort monitoring collections
        $denomProposals = max(1, $totalProposals);
        foreach ($agendaMonitoring as $agKey => &$agVal) {
            $agVal['percentage'] = $totalProposals > 0 ? round(($agVal['proposals_count'] / $denomProposals) * 100, 1) : 0.0;
        }
        unset($agVal);

        uasort($collegesMonitoring, fn ($a, $b) => $b['proposals_count'] <=> $a['proposals_count']);

        $sdgCoveredCount = collect($sdgMonitoring)->filter(fn ($item) => $item['extensions_count'] > 0)->count();
        $sdgUncoveredCount = max(0, 17 - $sdgCoveredCount);
        $sdgCoverageRate = round(($sdgCoveredCount / 17) * 100, 1);
        $totalSdgTagsCount = collect($sdgMonitoring)->sum('extensions_count');

        $bannerCoveredCount = collect($bannerProgramMonitoring)->filter(fn ($item) => $item['proposals_count'] > 0)->count();
        $bannerUncoveredCount = max(0, count($bannerProgramMonitoring) - $bannerCoveredCount);

        return view('dss.dashboard.index', compact(
            'tenant',
            'institution',
            'theme',
            'fy',
            'selectedPeriod',
            'isAllTime',
            'periodLabel',
            'availableYears',
            'effectiveRole',
            'canSwitchView',
            'activeRoleView',
            'totalPlans',
            'totalProposals',
            'approvedCount',
            'inReviewCount',
            'draftCount',
            'totalValuation',
            'totalProposedPlanBudget',
            'totalTargetBeneficiaries',
            'totalApprovedBudget',
            'totalRemainingBalance',
            'budgetBurnRate',
            'activeBudget',
            'totalTrackedPlans',
            'untrackedPlansCount',
            'trackingCoverageRate',
            'pendingDirectorSignaturesCount',
            'pendingDeanSignaturesCount',
            'pendingCollegeSignaturesCount',
            'pendingMySignaturesCount',
            'pendingMySignatures',
            'campusCount',
            'deptCount',
            'usersCount',
            'campuses',
            'collegeProposals',
            'collegePlans',
            'collegeProposalsCount',
            'collegePlansCount',
            'recentProposals',
            'recentPlans',
            'myProposals',
            'myPlans',
            'myProposalsCount',
            'myPlansCount',
            'bannerPrograms',
            'sdgs',
            'sdgMonitoring',
            'sdgCoveredCount',
            'sdgUncoveredCount',
            'sdgCoverageRate',
            'totalSdgTagsCount',
            'proposalsWithSdgCount',
            'bannerProgramMonitoring',
            'bannerCoveredCount',
            'bannerUncoveredCount',
            'proposalsWithBannerCount',
            'agendaMonitoring',
            'expectedOutputsMonitoring',
            'gadMonitoring',
            'collegesMonitoring',
            'interCollegeProposalsCount',
            'partnerAgenciesMonitoring',
            'fundingStructureMonitoring'
        ));
    }

    public function superAdminDashboard(Request $request)
    {
        return $this->index($request);
    }
}

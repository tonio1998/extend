<?php

namespace App\Http\Controllers\DSS;

use App\Http\Controllers\Controller;
use App\Models\Dts\Unit;
use App\Models\RIE\ExtensionBannerProgram;
use App\Models\RIE\ExtensionBudget;
use App\Models\RIE\ExtensionPlan;
use App\Models\RIE\ExtensionProposal;
use App\Models\RIE\SustainableDevelopmentGoal;
use App\Models\SaaS\Campus;
use App\Models\SaaS\DocumentSignatory;
use App\Models\User;
use App\Services\DSS\TenantContext;
use Illuminate\Http\Request;

class DSSDashboardController extends Controller
{
    public function index(Request $request)
    {
        $tenant = TenantContext::getTenant();
        $institution = TenantContext::getInstitution();
        $theme = TenantContext::getTheme();
        $fy = (int) $request->get('fy', $institution->current_fy ?: date('Y'));
        $user = auth()->user();

        $effectiveRole = $user ? $user->getEffectiveDashboardRole() : 'director';
        $canSwitchView = $user && ($user->isSuperAdmin() || $user->isExtensionDirector() || $user->isExtensionOfficeStaff());
        $activeRoleView = $canSwitchView ? $request->query('view', $effectiveRole) : $effectiveRole;

        $totalPlans = ExtensionPlan::where('tenant_id', $tenant->id)->count();
        $totalProposals = ExtensionProposal::where('tenant_id', $tenant->id)->count();
        $approvedCount = ExtensionProposal::where('tenant_id', $tenant->id)->whereIn('status', ['Approved', 'approved'])->count();
        $inReviewCount = ExtensionProposal::where('tenant_id', $tenant->id)->whereIn('status', ['submitted', 'reviewed', 'noted', 'concurred', 'recommended', 'In-Review', 'in-review'])->count();
        $draftCount = ExtensionProposal::where('tenant_id', $tenant->id)->whereIn('status', ['Draft', 'draft', 'pending', 'returned'])->count();
        $totalValuation = (float) ExtensionProposal::where('tenant_id', $tenant->id)->sum('budget_cash_amount');

        $totalProposedPlanBudget = (float) ExtensionPlan::where('tenant_id', $tenant->id)->sum('proposed_budget');
        $totalTargetBeneficiaries = (int) ExtensionPlan::where('tenant_id', $tenant->id)->sum('target_beneficiaries_count');

        $activeBudget = ExtensionBudget::where('tenant_id', $tenant->id)
            ->where('fiscal_year', $fy)
            ->first();

        $totalApprovedBudget = $activeBudget ? $activeBudget->effective_total : 0.00;
        $totalRemainingBalance = $activeBudget ? $activeBudget->remaining_balance : 0.00;
        $budgetBurnRate = $activeBudget ? $activeBudget->burn_rate : 0.0;

        $totalTrackedPlans = ExtensionPlan::where('tenant_id', $tenant->id)
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

        $campuses = Campus::where('tenant_id', $tenant->id)->get()->map(function ($campus) use ($tenant) {
            $plansCount = ExtensionPlan::where('tenant_id', $tenant->id)->where('campus_id', $campus->id)->count();
            $proposalsCount = ExtensionProposal::where('tenant_id', $tenant->id)->where('campus_id', $campus->id)->count();
            $allocatedBudget = (float) ExtensionPlan::where('tenant_id', $tenant->id)->where('campus_id', $campus->id)->sum('proposed_budget');
            $trackedCount = ExtensionPlan::where('tenant_id', $tenant->id)
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

        $collegeProposals = ExtensionProposal::with(['campus'])
            ->where('tenant_id', $tenant->id)
            ->where('campus_id', $userCampusId)
            ->latest()
            ->take(6)
            ->get();

        $collegePlans = ExtensionPlan::with(['campus'])
            ->where('tenant_id', $tenant->id)
            ->where('campus_id', $userCampusId)
            ->latest()
            ->take(5)
            ->get();

        $collegeProposalsCount = ExtensionProposal::where('tenant_id', $tenant->id)->where('campus_id', $userCampusId)->count();
        $collegePlansCount = ExtensionPlan::where('tenant_id', $tenant->id)->where('campus_id', $userCampusId)->count();

        $recentProposals = ExtensionProposal::with(['campus'])
            ->where('tenant_id', $tenant->id)
            ->latest()
            ->take(6)
            ->get();

        $recentPlans = ExtensionPlan::with(['campus'])
            ->where('tenant_id', $tenant->id)
            ->latest()
            ->take(5)
            ->get();

        $myProposals = $user ? ExtensionProposal::with(['campus'])->where('tenant_id', $tenant->id)->where('prepared_by_user_id', $user->id)->latest()->take(5)->get() : collect();
        $myPlans = $user ? ExtensionPlan::with(['campus'])->where('tenant_id', $tenant->id)->where('prepared_by_user_id', $user->id)->latest()->take(5)->get() : collect();
        $myProposalsCount = $user ? ExtensionProposal::where('tenant_id', $tenant->id)->where('prepared_by_user_id', $user->id)->count() : 0;
        $myPlansCount = $user ? ExtensionPlan::where('tenant_id', $tenant->id)->where('prepared_by_user_id', $user->id)->count() : 0;

        $bannerPrograms = ExtensionBannerProgram::where('is_active', true)->get();
        $sdgs = SustainableDevelopmentGoal::take(17)->get();

        return view('dss.dashboard.index', compact(
            'tenant',
            'institution',
            'theme',
            'fy',
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
            'sdgs'
        ));
    }

    public function superAdminDashboard(Request $request)
    {
        return $this->index($request);
    }
}

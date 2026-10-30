<?php

namespace App\Http\Controllers\DSS\RIE;

use App\Http\Controllers\Controller;
use App\Models\RIE\ExtensionBudget;
use App\Models\RIE\ExtensionBudgetAllocation;
use App\Models\RIE\ExtensionBudgetTransaction;
use App\Models\SaaS\Campus;
use App\Services\DSS\TenantContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ExtensionBudgetController extends Controller
{
    public function index(Request $request): View
    {
        $tenant = TenantContext::getTenant();
        $institution = TenantContext::getInstitution();
        $tenantId = $tenant?->id ?? 1;

        $availableYears = ExtensionBudget::where('tenant_id', $tenantId)
            ->distinct()
            ->orderBy('fiscal_year', 'desc')
            ->pluck('fiscal_year')
            ->toArray();

        $currentYear = (int) date('Y');
        if (! in_array($currentYear, $availableYears, true)) {
            array_unshift($availableYears, $currentYear);
        }
        rsort($availableYears);

        $selectedYear = (int) $request->get('fy', $availableYears[0] ?? $currentYear);
        $selectedCampusId = $request->get('campus_id');
        $selectedStatus = $request->get('status');

        $query = ExtensionBudget::with(['campus', 'createdByUser', 'allocations'])
            ->where('tenant_id', $tenantId)
            ->where('fiscal_year', $selectedYear);

        if ($selectedCampusId) {
            $query->where('campus_id', $selectedCampusId);
        }

        if ($selectedStatus) {
            $query->where('status', $selectedStatus);
        }

        $budgets = $query->latest()->get();

        $totalApprovedBudget = $budgets->sum(fn ($b) => $b->effective_total);
        $totalUtilized = $budgets->sum(fn ($b) => $b->utilized_amount);
        $totalCommitted = $budgets->sum(fn ($b) => $b->committed_amount);
        $totalRemainingBalance = $totalApprovedBudget - $totalUtilized;
        $totalProjectedBalance = $totalApprovedBudget - ($totalUtilized + $totalCommitted);

        $macroBurnRate = $totalApprovedBudget > 0 ? round(($totalUtilized / $totalApprovedBudget) * 100, 1) : 0.0;
        $macroCommitmentRate = $totalApprovedBudget > 0 ? round((($totalUtilized + $totalCommitted) / $totalApprovedBudget) * 100, 1) : 0.0;

        $campuses = Campus::where('tenant_id', $tenantId)->get();

        return view('dss.rie.budgets.index', compact(
            'tenant',
            'institution',
            'budgets',
            'availableYears',
            'selectedYear',
            'selectedCampusId',
            'selectedStatus',
            'campuses',
            'totalApprovedBudget',
            'totalUtilized',
            'totalCommitted',
            'totalRemainingBalance',
            'totalProjectedBalance',
            'macroBurnRate',
            'macroCommitmentRate'
        ));
    }

    public function create(Request $request): View
    {
        $tenant = TenantContext::getTenant();
        $tenantId = $tenant?->id ?? 1;
        $campuses = Campus::where('tenant_id', $tenantId)->get();

        $defaultYear = (int) $request->get('fy', date('Y'));
        $suggestedCode = 'EXT-BUD-'.$defaultYear.'-'.strtoupper(substr(uniqid(), -4));

        $standardCategories = [
            'Direct Community Extension Activities',
            'Livelihood & Skills Capacity Training',
            'IEC Materials, Toolkits & Publications',
            'Extension Logistics, Travel & Field Subsistence',
            'Supplies, Consumables & Training Kits',
            'Project Impact Assessment & Community Monitoring',
            'Institutional Discretionary & Miscellaneous',
        ];

        return view('dss.rie.budgets.create', compact(
            'tenant',
            'campuses',
            'defaultYear',
            'suggestedCode',
            'standardCategories'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $tenant = TenantContext::getTenant();
        $tenantId = $tenant?->id ?? 1;

        $validated = $request->validate([
            'fiscal_year' => ['required', 'integer', 'min:2020', 'max:2040'],
            'budget_code' => ['required', 'string', 'max:50', 'unique:extension_budgets,budget_code'],
            'title' => ['required', 'string', 'max:255'],
            'fund_cluster' => ['required', 'string', 'max:100'],
            'campus_id' => ['nullable', 'exists:sys_campuses,id'],
            'allocated_gaa' => ['nullable', 'numeric', 'min:0'],
            'allocated_stf' => ['nullable', 'numeric', 'min:0'],
            'allocated_external' => ['nullable', 'numeric', 'min:0'],
            'board_resolution_no' => ['nullable', 'string', 'max:100'],
            'approved_by' => ['nullable', 'string', 'max:150'],
            'approved_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'categories' => ['nullable', 'array'],
            'categories.*.name' => ['nullable', 'string', 'max:150'],
            'categories.*.amount' => ['nullable', 'numeric', 'min:0'],
            'categories.*.remarks' => ['nullable', 'string', 'max:255'],
        ]);

        $gaa = (float) ($validated['allocated_gaa'] ?? 0);
        $stf = (float) ($validated['allocated_stf'] ?? 0);
        $external = (float) ($validated['allocated_external'] ?? 0);
        $total = $gaa + $stf + $external;

        if ($total <= 0) {
            return back()->withInput()->with('error', 'Total allocated budget must be greater than ₱0.00.');
        }

        DB::beginTransaction();
        try {
            $budget = ExtensionBudget::create([
                'tenant_id' => $tenantId,
                'campus_id' => $validated['campus_id'] ?: null,
                'fiscal_year' => $validated['fiscal_year'],
                'budget_code' => trim($validated['budget_code']),
                'title' => trim($validated['title']),
                'fund_cluster' => $validated['fund_cluster'],
                'total_allocation' => $total,
                'allocated_gaa' => $gaa,
                'allocated_stf' => $stf,
                'allocated_external' => $external,
                'realigned_amount' => 0.00,
                'status' => 'active',
                'board_resolution_no' => $validated['board_resolution_no'] ?? null,
                'approved_by' => $validated['approved_by'] ?? null,
                'approved_at' => $validated['approved_at'] ?? now()->toDateString(),
                'notes' => $validated['notes'] ?? null,
                'created_by_user_id' => Auth::id(),
            ]);

            if (! empty($validated['categories'])) {
                foreach ($validated['categories'] as $cat) {
                    $catAmt = (float) ($cat['amount'] ?? 0);
                    $catName = trim($cat['name'] ?? '');
                    if ($catName !== '' && $catAmt > 0) {
                        ExtensionBudgetAllocation::create([
                            'extension_budget_id' => $budget->id,
                            'campus_id' => $budget->campus_id,
                            'category_name' => $catName,
                            'allocated_amount' => $catAmt,
                            'remarks' => $cat['remarks'] ?? null,
                        ]);
                    }
                }
            }

            ExtensionBudgetTransaction::create([
                'extension_budget_id' => $budget->id,
                'transaction_type' => 'initial',
                'amount' => $total,
                'reference_no' => $budget->board_resolution_no ?: $budget->budget_code,
                'reason' => 'Approved Initial Annual Extension Appropriation for FY '.$budget->fiscal_year,
                'authorized_by' => $budget->approved_by ?: (Auth::user()?->name ?? 'Administration'),
                'created_by_user_id' => Auth::id(),
            ]);

            DB::commit();

            return redirect()->route('dss.rie.budgets.show', $budget->getRouteKey())
                ->with('success', "Annual Extension Budget [{$budget->budget_code}] for FY {$budget->fiscal_year} created successfully!");
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withInput()->with('error', 'Failed to initialize budget: '.$e->getMessage());
        }
    }

    public function show(Request $request, string $id): View
    {
        $budgetId = function_exists('dss_decrypt_id') ? dss_decrypt_id($id) : (int) $id;
        $budget = ExtensionBudget::with([
            'campus',
            'createdByUser',
            'allocations.campus',
            'transactions.createdByUser',
        ])->findOrFail($budgetId);

        $proposals = $budget->proposalsQuery()
            ->with(['campus', 'unit', 'projectLeaderUser'])
            ->latest()
            ->get();

        $approvedProposals = $proposals->filter(fn ($p) => in_array(strtolower((string) $p->status), ['approved'], true));
        $inReviewProposals = $proposals->filter(fn ($p) => in_array(strtolower((string) $p->status), ['submitted', 'reviewed', 'noted', 'concurred', 'recommended', 'in-review', 'in_review'], true));
        $draftProposals = $proposals->filter(fn ($p) => in_array(strtolower((string) $p->status), ['draft', 'pending', 'returned'], true));

        $bannerBreakdown = $approvedProposals->groupBy('banner_program')->map(function ($items, $key) {
            return [
                'name' => $key ?: 'General Community Extension',
                'count' => $items->count(),
                'total_amount' => $items->sum('budget_cash_amount'),
            ];
        })->sortByDesc('total_amount');

        return view('dss.rie.budgets.show', compact(
            'budget',
            'proposals',
            'approvedProposals',
            'inReviewProposals',
            'draftProposals',
            'bannerBreakdown'
        ));
    }

    public function edit(string $id): View
    {
        $budgetId = function_exists('dss_decrypt_id') ? dss_decrypt_id($id) : (int) $id;
        $budget = ExtensionBudget::with(['campus', 'allocations'])->findOrFail($budgetId);

        $tenantId = $budget->tenant_id;
        $campuses = Campus::where('tenant_id', $tenantId)->get();

        return view('dss.rie.budgets.edit', compact('budget', 'campuses'));
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $budgetId = function_exists('dss_decrypt_id') ? dss_decrypt_id($id) : (int) $id;
        $budget = ExtensionBudget::findOrFail($budgetId);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'fund_cluster' => ['required', 'string', 'max:100'],
            'campus_id' => ['nullable', 'exists:sys_campuses,id'],
            'status' => ['required', 'string', 'in:active,closed,draft'],
            'board_resolution_no' => ['nullable', 'string', 'max:100'],
            'approved_by' => ['nullable', 'string', 'max:150'],
            'approved_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'allocated_gaa' => ['nullable', 'numeric', 'min:0'],
            'allocated_stf' => ['nullable', 'numeric', 'min:0'],
            'allocated_external' => ['nullable', 'numeric', 'min:0'],
        ]);

        $gaa = (float) ($validated['allocated_gaa'] ?? $budget->allocated_gaa);
        $stf = (float) ($validated['allocated_stf'] ?? $budget->allocated_stf);
        $external = (float) ($validated['allocated_external'] ?? $budget->allocated_external);
        $newTotal = $gaa + $stf + $external;

        $budget->update([
            'title' => trim($validated['title']),
            'fund_cluster' => $validated['fund_cluster'],
            'campus_id' => $validated['campus_id'] ?: null,
            'status' => $validated['status'],
            'board_resolution_no' => $validated['board_resolution_no'] ?? null,
            'approved_by' => $validated['approved_by'] ?? null,
            'approved_at' => $validated['approved_at'] ?? $budget->approved_at,
            'notes' => $validated['notes'] ?? null,
            'allocated_gaa' => $gaa,
            'allocated_stf' => $stf,
            'allocated_external' => $external,
            'total_allocation' => $newTotal,
        ]);

        return redirect()->route('dss.rie.budgets.show', $budget->getRouteKey())
            ->with('success', "Budget configuration for [{$budget->budget_code}] updated successfully.");
    }

    public function realign(Request $request, string $id): RedirectResponse
    {
        $budgetId = function_exists('dss_decrypt_id') ? dss_decrypt_id($id) : (int) $id;
        $budget = ExtensionBudget::findOrFail($budgetId);

        $validated = $request->validate([
            'transaction_type' => ['required', 'string', 'in:augmentation,realignment,deduction'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'reference_no' => ['nullable', 'string', 'max:120'],
            'reason' => ['required', 'string', 'max:1000'],
            'authorized_by' => ['nullable', 'string', 'max:150'],
        ]);

        $delta = (float) $validated['amount'];
        if ($validated['transaction_type'] === 'deduction') {
            $delta = -abs($delta);
        }

        DB::transaction(function () use ($budget, $validated, $delta) {
            $budget->increment('realigned_amount', $delta);

            ExtensionBudgetTransaction::create([
                'extension_budget_id' => $budget->id,
                'transaction_type' => $validated['transaction_type'],
                'amount' => $delta,
                'reference_no' => $validated['reference_no'] ?? null,
                'reason' => $validated['reason'],
                'authorized_by' => $validated['authorized_by'] ?: (Auth::user()?->name ?? 'Administration'),
                'created_by_user_id' => Auth::id(),
            ]);
        });

        return back()->with('success', 'Budget adjustment recorded successfully. Real-time balance updated!');
    }

    public function destroy(string $id): RedirectResponse
    {
        $budgetId = function_exists('dss_decrypt_id') ? dss_decrypt_id($id) : (int) $id;
        $budget = ExtensionBudget::findOrFail($budgetId);

        $code = $budget->budget_code;
        $fy = $budget->fiscal_year;
        $budget->delete();

        return redirect()->route('dss.rie.budgets.index', ['fy' => $fy])
            ->with('success', "Budget [{$code}] has been deleted.");
    }

    public function export(Request $request)
    {
        $tenant = TenantContext::getTenant();
        $tenantId = $tenant?->id ?? 1;
        $fy = (int) $request->get('fy', date('Y'));

        $budgets = ExtensionBudget::with('campus')
            ->where('tenant_id', $tenantId)
            ->where('fiscal_year', $fy)
            ->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=snsu_extension_budget_monitoring_FY{$fy}.csv",
        ];

        $callback = function () use ($budgets, $fy) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, ['SURIGAO DEL NORTE STATE UNIVERSITY (SNSU)']);
            fputcsv($file, ['ANNUAL EXTENSION FISCAL YEAR BUDGET UTILIZATION REPORT - FY '.$fy]);
            fputcsv($file, ['Generated on: '.now()->toFormattedDateString()]);
            fputcsv($file, []);

            fputcsv($file, [
                'Budget Code',
                'Title',
                'Campus',
                'Fund Cluster',
                'Original Allocation (PHP)',
                'GAA Share (PHP)',
                'STF Share (PHP)',
                'External Grants (PHP)',
                'Realignments / Adjustments (PHP)',
                'Effective Total Budget (PHP)',
                'Approved Utilized (PHP)',
                'In-Review Committed (PHP)',
                'Available Remaining Balance (PHP)',
                'Projected Balance (PHP)',
                'Burn Rate (%)',
                'Status',
                'Board Resolution / Approval',
            ]);

            foreach ($budgets as $b) {
                fputcsv($file, [
                    $b->budget_code,
                    $b->title,
                    $b->campus?->campus_name ?? 'University-Wide (Main)',
                    $b->fund_cluster,
                    number_format($b->total_allocation, 2, '.', ''),
                    number_format($b->allocated_gaa, 2, '.', ''),
                    number_format($b->allocated_stf, 2, '.', ''),
                    number_format($b->allocated_external, 2, '.', ''),
                    number_format($b->realigned_amount, 2, '.', ''),
                    number_format($b->effective_total, 2, '.', ''),
                    number_format($b->utilized_amount, 2, '.', ''),
                    number_format($b->committed_amount, 2, '.', ''),
                    number_format($b->remaining_balance, 2, '.', ''),
                    number_format($b->projected_balance, 2, '.', ''),
                    $b->burn_rate.'%',
                    strtoupper($b->status),
                    $b->board_resolution_no ?: ($b->approved_by ?: 'N/A'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

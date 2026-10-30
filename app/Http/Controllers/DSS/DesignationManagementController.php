<?php

namespace App\Http\Controllers\DSS;

use App\Http\Controllers\Controller;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\EmployeeDesignation;
use App\Services\DSS\TenantContext;
use Illuminate\Http\Request;

class DesignationManagementController extends Controller
{
    /**
     * Display Master Institutional Designations & Positions Directory
     */
    public function index(Request $request)
    {
        $tenant = TenantContext::getTenant();
        $institution = TenantContext::getInstitution();
        $theme = TenantContext::getTheme();
        $isSuperAdmin = false;

        $query = Designation::where('tenant_id', $tenant->id);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($category = $request->input('category')) {
            $query->where('category', $category);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $designations = $query->orderBy('name')->get();

        // Calculate holder counts per designation for this tenant
        $holderCounts = EmployeeDesignation::where('tenant_id', $tenant->id)
            ->where('status', 'active')
            ->selectRaw('designation_id, count(distinct employee_id) as total_holders')
            ->groupBy('designation_id')
            ->pluck('total_holders', 'designation_id')
            ->toArray();

        // Summary Metrics
        $totalDesignations = Designation::where('tenant_id', $tenant->id)->count();
        $activeDesignations = Designation::where('tenant_id', $tenant->id)->where('status', 'active')->count();
        $academicCount = Designation::where('tenant_id', $tenant->id)->where('category', 'Academic')->count();
        $adminCount = Designation::where('tenant_id', $tenant->id)->where('category', 'Administrative')->count();
        $technicalCount = Designation::where('tenant_id', $tenant->id)->where('category', 'Technical')->count();

        $categories = ['Academic', 'Administrative', 'Technical', 'Executive', 'Support', 'General'];

        return view('dss.settings.designations.index', compact(
            'tenant',
            'institution',
            'theme',
            'designations',
            'holderCounts',
            'totalDesignations',
            'activeDesignations',
            'academicCount',
            'adminCount',
            'technicalCount',
            'categories',
            'isSuperAdmin'
        ));
    }

    /**
     * Store a newly created Designation
     */
    public function store(Request $request)
    {

        $tenant = TenantContext::getTenant();

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'code' => 'nullable|string|max:50',
            'category' => 'required|string|in:Academic,Administrative,Technical,Executive,Support,General',
            'description' => 'nullable|string',
            'status' => 'nullable|in:active,inactive',
        ]);

        $existing = Designation::where('tenant_id', $tenant->id)
            ->where('name', trim($validated['name']))
            ->first();

        if ($existing) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['result' => true, 'designation' => $existing, 'message' => 'Designation already exists.']);
            }

            return redirect()->back()->with('warning', "Designation '{$existing->name}' already exists.");
        }

        $designation = Designation::create([
            'tenant_id' => $tenant->id,
            'name' => trim($validated['name']),
            'code' => ! empty($validated['code']) ? strtoupper(trim($validated['code'])) : null,
            'category' => $validated['category'],
            'description' => trim($validated['description'] ?? ''),
            'status' => $validated['status'] ?? 'active',
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'result' => true,
                'designation' => $designation,
                'message' => "Designation '{$designation->name}' created successfully.",
            ]);
        }

        return redirect()->route('dss.settings.designations.index')
            ->with('success', "Designation '{$designation->name}' added to master repository.");
    }

    /**
     * Update Designation
     */
    public function update(Request $request, $id)
    {

        $tenant = TenantContext::getTenant();
        $designation = Designation::where('tenant_id', $tenant->id)->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'code' => 'nullable|string|max:50',
            'category' => 'required|string|in:Academic,Administrative,Technical,Executive,Support,General',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $oldName = $designation->name;
        $newName = trim($validated['name']);

        $designation->update([
            'name' => $newName,
            'code' => ! empty($validated['code']) ? strtoupper(trim($validated['code'])) : null,
            'category' => $validated['category'],
            'description' => trim($validated['description'] ?? ''),
            'status' => $validated['status'],
        ]);

        // If name changed, synchronize to employee_designations and employees
        if ($oldName !== $newName) {
            EmployeeDesignation::where('designation_id', $designation->id)->update(['designation' => $newName]);
            Employee::where('designation_id', $designation->id)->update(['designation' => $newName]);
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'result' => true,
                'designation' => $designation,
                'message' => "Designation '{$designation->name}' updated successfully.",
            ]);
        }

        return redirect()->route('dss.settings.designations.index')
            ->with('success', "Designation '{$designation->name}' updated successfully.");
    }

    /**
     * Delete Designation
     */
    public function destroy(Request $request, $id)
    {

        $tenant = TenantContext::getTenant();
        $designation = Designation::where('tenant_id', $tenant->id)->findOrFail($id);

        // Check if any active employee holds this designation
        $holderCount = EmployeeDesignation::where('designation_id', $designation->id)->where('status', 'active')->count();
        if ($holderCount > 0 && ! $request->boolean('force_delete')) {
            $msg = "Cannot archive designation '{$designation->name}' because {$holderCount} personnel currently hold this designation.";
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['result' => false, 'message' => $msg, 'holder_count' => $holderCount], 422);
            }

            return redirect()->back()->with('error', $msg);
        }

        $name = $designation->name;
        $designation->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'result' => true,
                'message' => "Designation '{$name}' archived successfully.",
            ]);
        }

        return redirect()->route('dss.settings.designations.index')
            ->with('success', "Designation '{$name}' archived successfully.");
    }

    /**
     * Return JSON for Select2 dropdowns across the application
     */
    public function listJson(Request $request)
    {
        $tenant = TenantContext::getTenant();
        $query = Designation::where('tenant_id', $tenant->id)->where('status', 'active');

        if ($q = $request->input('q')) {
            $query->where(function ($sq) use ($q) {
                $sq->where('name', 'like', "%{$q}%")
                    ->orWhere('code', 'like', "%{$q}%");
            });
        }

        $results = $query->orderBy('name')->get()->map(function ($d) {
            return [
                'id' => $d->id,
                'text' => $d->name,
                'code' => $d->code,
                'category' => $d->category,
            ];
        });

        return response()->json([
            'results' => $results,
        ]);
    }

    /**
     * Quick status toggle
     */
    public function toggleStatus(Request $request, $id)
    {
        $tenant = TenantContext::getTenant();
        $designation = Designation::where('tenant_id', $tenant->id)->findOrFail($id);

        $newStatus = $designation->status === 'active' ? 'inactive' : 'active';
        $designation->update(['status' => $newStatus]);

        return response()->json([
            'result' => true,
            'status' => $newStatus,
            'message' => "Designation status updated to {$newStatus}.",
        ]);
    }
}

<?php

namespace App\Http\Controllers\DSS;

use App\Http\Controllers\Controller;
use App\Models\Dts\Unit;
use App\Models\SaaS\Campus;
use App\Models\SaaS\Department;
use App\Models\SaaS\InstitutionSetting;
use App\Models\SaaS\Tenant;
use App\Models\SaaS\ThemeSetting;
use App\Models\User;
use App\Services\DSS\TenantContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class TenantManagementController extends Controller
{
    /**
     * List all registered institutions (SuperAdmin only)
     */
    public function index()
    {
        if (! Auth::user()->isSuperAdmin()) {
            abort(403, 'Unauthorized access. Only SuperAdmin can manage institutional tenants.');
        }

        // Clean up any incomplete/interrupted tenant provisioning (e.g. no users created)
        $incompleteTenants = Tenant::whereDoesntHave('users')
            ->where('id', '>', 1)
            ->where('created_at', '>=', now()->subHours(10))
            ->get();

        foreach ($incompleteTenants as $orphaned) {
            Campus::where('tenant_id', $orphaned->id)->delete();
            InstitutionSetting::where('tenant_id', $orphaned->id)->delete();
            ThemeSetting::where('tenant_id', $orphaned->id)->delete();
            Department::where('tenant_id', $orphaned->id)->delete();
            $orphaned->delete();
        }

        $tenant = TenantContext::getTenant();
        $institution = TenantContext::getInstitution();
        $theme = TenantContext::getTheme();

        $allTenants = Tenant::with(['institutionSetting', 'themeSetting', 'campuses', 'users'])->latest()->paginate(10);

        return view('dss.settings.tenants', compact('tenant', 'institution', 'theme', 'allTenants'));
    }

    /**
     * Show form to register a new school/tenant (SuperAdmin only)
     */
    public function create()
    {
        if (! Auth::user()->isSuperAdmin()) {
            abort(403, 'Unauthorized access. Only SuperAdmin can register new institutional tenants.');
        }

        $tenant = TenantContext::getTenant();
        $institution = TenantContext::getInstitution();
        $theme = TenantContext::getTheme();

        return view('dss.settings.create-tenant', compact('tenant', 'institution', 'theme'));
    }

    /**
     * Store newly registered institution (SuperAdmin only)
     */
    public function store(Request $request)
    {
        if (! Auth::user()->isSuperAdmin()) {
            abort(403, 'Unauthorized access. Only SuperAdmin can register new institutional tenants.');
        }

        $validated = $request->validate([
            'institution_name' => 'required|string|max:191',
            'school_acronym' => 'required|string|max:30',
            'school_type' => 'required|string|max:50',
            'campus_name' => 'required|string|max:191',
            'admin_name' => 'required|string|max:100',
            'admin_email' => 'required|string|email|max:191|unique:users,email',
            'admin_password' => 'required|string|min:8',
            'plan' => 'nullable|string|max:50',
        ]);

        $slug = Str::slug($validated['school_acronym'] ?: $validated['institution_name']);
        if (Tenant::where('slug', $slug)->exists()) {
            $slug .= '-'.Str::lower(Str::random(4));
        }

        return DB::transaction(function () use ($validated, $slug, $request) {
            // 1. Create Sovereign Tenant
            $newTenant = Tenant::create([
                'uuid' => (string) Str::uuid(),
                'name' => $validated['institution_name'],
                'slug' => $slug,
                'plan' => $validated['plan'] ?? 'Institutional Enterprise',
                'status' => 'active',
            ]);

            // 2. Create Institution Profile
            InstitutionSetting::create([
                'tenant_id' => $newTenant->id,
                'school_name' => $validated['institution_name'],
                'school_acronym' => $validated['school_acronym'],
                'school_type' => $validated['school_type'],
                'current_fy' => date('Y'),
            ]);

            // 3. Create Default Theme
            ThemeSetting::create([
                'tenant_id' => $newTenant->id,
                'primary_color' => '#047857',
                'secondary_color' => '#0284c7',
                'accent_color' => '#f59e0b',
                'sidebar_theme' => 'brand',
                'navbar_theme' => 'light',
                'border_radius' => '10px',
                'is_dark_mode' => false,
                'font_family' => "'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif",
            ]);

            // 4. Create Main Campus
            $campus = Campus::create([
                'tenant_id' => $newTenant->id,
                'campus_code' => 'MAIN',
                'campus_name' => $validated['campus_name'],
                'campus_type' => 'Main Campus',
                'status' => 'active',
            ]);

            // 5. Create Default Department / Unit
            $dept = Department::create([
                'tenant_id' => $newTenant->id,
                'campus_id' => $campus->id,
                'unit_code' => 'OP',
                'unit_name' => 'Office of the President',
                'dept_code' => 'OP',
                'dept_name' => 'Office of the President',
                'short_name' => 'OP',
                'unit_type' => 'Executive',
                'division' => 'Executive Management',
                'status' => 'active',
                'is_active' => true,
            ]);

            // 6. Create Initial Administrator User for that school (Sovereign Tenant Admin)
            $adminUser = User::create([
                'tenant_id' => $newTenant->id,
                'campus_id' => $campus->id,
                'dept_id' => $dept->id,
                'name' => $validated['admin_name'],
                'email' => $validated['admin_email'],
                'password' => Hash::make($validated['admin_password']),
                'role' => 'Admin',
                'designation' => 'Campus Information Systems Administrator',
                'status' => 'active',
            ]);

            // 7. Assign Spatie "Admin" Role (Global Role)
            $adminRole = Role::firstOrCreate([
                'name' => 'Admin',
                'guard_name' => 'web',
            ]);

            $adminUser->assignRole($adminRole);

            if ($request->wantsJson()) {
                return response()->json([
                    'result' => true,
                    'message' => "Institutional tenant '{$newTenant->name}' successfully provisioned!",
                    'redirect' => route('dss.settings.tenants.index'),
                ]);
            }

            return redirect()->route('dss.settings.tenants.index')->with('success', "Institutional tenant '{$newTenant->name}' successfully registered by SuperAdmin!");
        });
    }

    /**
     * Update specific institutional school details (SuperAdmin only)
     */
    public function update(Request $request, $id)
    {
        if (! Auth::user()->isSuperAdmin()) {
            abort(403, 'Unauthorized access. Only SuperAdmin can update institutional schools.');
        }

        $id = is_numeric($id) ? (int) $id : dss_decrypt_id($id);
        $tenant = Tenant::findOrFail($id);

        $validated = $request->validate([
            'institution_name' => 'required|string|max:191',
            'school_acronym' => 'required|string|max:30',
            'school_type' => 'required|string|max:50',
            'plan' => 'nullable|string|max:50',
            'domain' => 'nullable|string|max:191',
            'status' => 'required|string|in:active,inactive,disabled',
        ]);

        $tenant->update([
            'name' => $validated['institution_name'],
            'plan' => $validated['plan'] ?? $tenant->plan,
            'domain' => $validated['domain'],
            'status' => $validated['status'],
        ]);

        $institutionSetting = InstitutionSetting::firstOrNew(['tenant_id' => $tenant->id]);
        $institutionSetting->school_name = $validated['institution_name'];
        $institutionSetting->school_acronym = $validated['school_acronym'];
        $institutionSetting->school_type = $validated['school_type'];
        $institutionSetting->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'result' => true,
                'message' => "School '{$tenant->name}' details updated successfully!",
                'tenant' => $tenant->load('institutionSetting'),
            ]);
        }

        return redirect()->back()->with('success', "School '{$tenant->name}' details updated successfully!");
    }

    /**
     * Toggle active/disabled status for an institutional school (SuperAdmin only)
     */
    public function toggleStatus(Request $request, $id)
    {
        if (! Auth::user()->isSuperAdmin()) {
            abort(403, 'Unauthorized access. Only SuperAdmin can toggle school status.');
        }

        $id = is_numeric($id) ? (int) $id : dss_decrypt_id($id);
        $tenant = Tenant::findOrFail($id);
        $newStatus = ($tenant->status === 'active') ? 'disabled' : 'active';
        $tenant->update(['status' => $newStatus]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'result' => true,
                'status' => $newStatus,
                'message' => "School '{$tenant->name}' is now {$newStatus}!",
            ]);
        }

        return redirect()->back()->with('success', "School '{$tenant->name}' is now {$newStatus}!");
    }

    /**
     * Switch active tenant context to this school (SuperAdmin only)
     * Allows SuperAdmin to act as part of the tenant and access all institutional modules as a user of that school
     */
    public function switchContext(Request $request, $id)
    {
        if (! Auth::user()->isSuperAdmin()) {
            abort(403, 'Unauthorized access. Only SuperAdmin can switch school contexts.');
        }

        $id = is_numeric($id) ? (int) $id : dss_decrypt_id($id);
        $tenant = Tenant::with(['campuses.departments'])->findOrFail($id);

        session([
            'impersonating_tenant_id' => $tenant->id,
            'active_tenant_id' => $tenant->id,
        ]);

        $user = Auth::user();
        if ($user) {
            $firstCampus = $tenant->campuses->first();
            $firstDept = $firstCampus?->departments?->first() ?? Unit::where('tenant_id', $tenant->id)->first();

            $user->update([
                'tenant_id' => $tenant->id,
                'campus_id' => $firstCampus?->id ?? $user->campus_id,
                'dept_id' => $firstDept?->id ?? $user->dept_id,
            ]);
        }

        TenantContext::reset();

        return redirect()->route('dss.dashboard')
            ->with('success', "Switched context to {$tenant->name}. You are now acting as part of this school.");
    }

    /**
     * Exit tenant school context and return to SaaS Platform Master Governance (SuperAdmin only)
     */
    public function exitSwitch(Request $request)
    {
        session()->forget(['impersonating_tenant_id', 'active_tenant_id']);

        $user = Auth::user();
        if ($user && $user->isSuperAdmin()) {
            $defaultTenant = Tenant::with(['campuses.departments'])->first();
            if ($defaultTenant) {
                $firstCampus = $defaultTenant->campuses->first();
                $firstDept = $firstCampus?->departments?->first() ?? Unit::where('tenant_id', $defaultTenant->id)->first();

                $user->update([
                    'tenant_id' => $defaultTenant->id,
                    'campus_id' => $firstCampus?->id,
                    'dept_id' => $firstDept?->id,
                ]);
            }
        }

        TenantContext::reset();

        return redirect()->route('dss.settings.tenants.index')
            ->with('success', 'Exited school context. Returned to Multi-Tenant SaaS Platform Master Governance.');
    }
}

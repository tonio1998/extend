<?php

namespace App\Http\Controllers\DSS;

use App\Http\Controllers\Controller;
use App\Models\SaaS\Campus;
use App\Models\SaaS\Department;
use App\Models\User;
use App\Services\DSS\TenantContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionController extends Controller
{
    /**
     * Get grouped permission catalog across SUC domains
     */
    public static function getPermissionCatalog(): array
    {
        return [
            'Extension Program Management' => [
                'dashboard.view' => 'Access Extension Decision Intelligence Dashboard & Role Overview',
                'rie.plans.view' => 'Access Masterlist & Institutional Directory of Extension Plans',
                'rie.plans.create' => 'Draft & Submit Extension Plans',
                'rie.plans.manage' => 'Edit, Update, and Manage Extension Plans',
                'rie.plans.delete' => 'Delete & Archive Extension Plans',
            ],
            'Proposal Submission & Evaluation' => [
                'rie.proposals.view' => 'View & Browse Extension Proposals',
                'rie.proposals.create' => 'Draft & Submit Extension Proposals',
                'rie.proposals.manage' => 'Edit & Manage Extension Proposals',
                'rie.proposals.review' => 'Conduct Technical Review & Pre-screening of Proposals',
                'rie.proposals.evaluate' => 'Perform Formal Proposal Evaluation & Scoring',
            ],
            'Approval Workflow & Endorsements' => [
                'rie.signatures.sign' => 'Affix Digital Signatures & Official Endorsements',
                'rie.approvals.manage' => 'Manage Routing, Endorsement & Executive Approvals',
            ],
            'Implementation, Activities & Monitoring' => [
                'rie.implementation.manage' => 'Oversee & Manage Project Implementation',
                'rie.activities.manage' => 'Record Attendance & Monitor Extension Activities',
                'rie.beneficiaries.manage' => 'Manage Target Beneficiaries & Communities',
                'rie.partners.manage' => 'Manage Partner Agencies & MoA Linkages',
            ],
            'Accomplishment, Impact & Analytics' => [
                'rie.reports.accomplishment' => 'Generate Terminal & Accomplishment Reports',
                'rie.impact.evaluate' => 'Conduct Impact Assessment & Evaluation',
                'rie.reports.analytics' => 'Access Executive Analytics & Strategic Reports',
            ],
            'Document Repository & Tracking' => [
                'rie.documents.view' => 'Browse Repository & Download Approved Documents',
                'rie.documents.manage' => 'Upload Scanned Documents & Wet-Signed Artifacts',
                'rie.tracking.view' => 'View 3rd-Party DTS Tracking Numbers & Routing Status',
                'rie.tracking.manage' => 'Connect, Link & Update 3rd-Party Tracking Numbers',
                'rie.settings.manage' => 'Configure Extension Priorities, Banner Programs & Signatories',
            ],
            'Budget & Financial Monitoring' => [
                'rie.budgets.view' => 'Access Annual Budget Allocation & Burn Rate Charts',
                'rie.budgets.manage' => 'Manage, Realign & Reallocate Extension Budgets',
            ],
            'Governance & Audit Trail' => [
                'system.users.assign' => 'Assign Roles, Campuses, and Hierarchy to Faculty & Personnel',
                'system.roles.manage' => 'Create & Configure Institutional Roles & Permission Matrices',
                'system.hierarchy.manage' => 'Configure Campuses, Departments, and Signatories Matrix',
                'system.theme.customize' => 'Customize White-Labeling Theme, Seal & Color Tokens',
                'system.audit.view' => 'Access System Audit Trail & Compliance Activity Logs',
            ],
        ];
    }

    public static function isSuperAdminUser(?User $user = null): bool
    {
        $user = $user ?? auth()->user();

        return (bool) ($user && $user->isSuperAdmin());
    }

    /**
     * Separate Screen 1: Users Directory & Role/Permission Assignment
     */
    public function usersIndex(Request $request)
    {
        $tenant = TenantContext::getTenant();
        $institution = TenantContext::getInstitution();
        $theme = TenantContext::getTheme();
        $isSuperAdmin = self::isSuperAdminUser();

        $users = User::where('tenant_id', $tenant->id)
            ->with(['campus', 'department', 'roles', 'permissions', 'employee'])
            ->orderBy('name')
            ->get();

        $rolesQuery = Role::query();
        if (! $isSuperAdmin) {
            $rolesQuery->where('name', '!=', 'SuperAdmin');
        }
        $roles = $rolesQuery->get();

        $campuses = Campus::where('tenant_id', $tenant->id)->get();
        $departments = Department::where('tenant_id', $tenant->id)->get();
        $permissionCatalog = self::getPermissionCatalog();

        return view('dss.settings.users.index', compact(
            'tenant',
            'institution',
            'theme',
            'users',
            'roles',
            'campuses',
            'departments',
            'permissionCatalog',
            'isSuperAdmin'
        ));
    }

    /**
     * Separate Screen 2: Roles Catalog & Matrix Configuration
     */
    public function index(Request $request)
    {
        $tenant = TenantContext::getTenant();
        $institution = TenantContext::getInstitution();
        $theme = TenantContext::getTheme();
        $isSuperAdmin = self::isSuperAdminUser();

        $rolesQuery = Role::with(['permissions', 'users'])
            ->withCount('users');
        if (! $isSuperAdmin) {
            $rolesQuery->where('name', '!=', 'SuperAdmin');
        }
        $roles = $rolesQuery->get();

        $permissions = Permission::with('roles')->get();

        $permissionCatalog = self::getPermissionCatalog();

        return view('dss.settings.roles.index', compact(
            'tenant',
            'institution',
            'theme',
            'roles',
            'permissions',
            'permissionCatalog',
            'isSuperAdmin'
        ));
    }

    /**
     * Separate Screen 3: Permissions Master Registry
     */
    public function permissionsIndex(Request $request)
    {
        $tenant = TenantContext::getTenant();
        $institution = TenantContext::getInstitution();
        $theme = TenantContext::getTheme();
        $isSuperAdmin = self::isSuperAdminUser();

        $permissions = Permission::with('roles')->get();

        $roles = Role::all();
        $permissionCatalog = self::getPermissionCatalog();

        $users = User::where('tenant_id', $tenant->id)
            ->with('permissions')
            ->get();

        $directPermissionCounts = [];
        foreach ($users as $u) {
            foreach ($u->permissions as $p) {
                $directPermissionCounts[$p->name] = ($directPermissionCounts[$p->name] ?? 0) + 1;
            }
        }

        return view('dss.settings.permissions.index', compact(
            'tenant',
            'institution',
            'theme',
            'permissions',
            'roles',
            'permissionCatalog',
            'directPermissionCounts',
            'isSuperAdmin'
        ));
    }

    public function storeRole(Request $request)
    {
        if (! self::isSuperAdminUser()) {
            return response()->json([
                'result' => false,
                'message' => 'Unauthorized: Only SuperAdmin can create new institutional roles.',
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:80|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $role = Role::firstOrCreate([
            'name' => trim($validated['name']),
            'guard_name' => 'web',
        ]);

        if (isset($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        return response()->json([
            'result' => true,
            'message' => "Role '{$role->name}' configured successfully!",
            'role' => $role->load('permissions'),
            'redirect_url' => route('dss.settings.roles.configure', $role->id),
        ]);
    }

    public function updateRolePermissions(Request $request, $id)
    {
        if (! self::isSuperAdminUser()) {
            return response()->json([
                'result' => false,
                'message' => 'Unauthorized: Only SuperAdmin can configure permissions for roles.',
            ], 403);
        }

        $id = is_numeric($id) ? (int) $id : dss_decrypt_id($id);
        $role = Role::findOrFail($id);

        if ($role->name === 'SuperAdmin') {
            $msg = 'The SuperAdmin role is exclusively reserved for apiloton1@snsu.edu.ph and cannot be assigned.';
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['result' => false, 'message' => $msg], 403);
            }

            return redirect()->back()->with('error', $msg);
        }

        $validated = $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $role->syncPermissions($validated['permissions'] ?? []);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'result' => true,
                'message' => "Permissions for role '{$role->name}' updated successfully!",
                'role' => $role->load('permissions'),
            ]);
        }

        return redirect()->back()->with('success', "Capabilities matrix for role '{$role->name}' saved successfully.");
    }

    /**
     * Separate Screen: Role Configuration Studio (Drag-and-Drop Matrix & Personnel)
     */
    public function configureRole(Request $request, $id)
    {
        $id = is_numeric($id) ? (int) $id : dss_decrypt_id($id);
        $tenant = TenantContext::getTenant();
        $institution = TenantContext::getInstitution();
        $theme = TenantContext::getTheme();
        $isSuperAdmin = self::isSuperAdminUser();

        $role = Role::with(['permissions', 'users'])
            ->withCount(['permissions', 'users'])
            ->findOrFail($id);

        if ($role->name === 'SuperAdmin' && ! $isSuperAdmin) {
            abort(403, 'Unauthorized: The SuperAdmin platform role cannot be inspected by tenant administrators.');
        }

        $permissionCatalog = self::getPermissionCatalog();
        $allPermissions = Permission::orderBy('name')->get();
        $rolePermissionNames = $role->permissions->pluck('name')->toArray();

        // Categorize available vs assigned permissions
        $assignedPermissions = $role->permissions;
        $availablePermissions = $allPermissions->filter(fn ($p) => ! in_array($p->name, $rolePermissionNames));

        // Users in tenant
        $allTenantUsers = User::where('tenant_id', $tenant->id)
            ->with(['campus', 'department', 'employee'])
            ->orderBy('name')
            ->get();

        $assignedUserIds = $role->users->pluck('id')->toArray();
        $assignedUsers = $allTenantUsers->filter(fn ($u) => in_array($u->id, $assignedUserIds) || $u->role === $role->name);
        $availableUsers = $allTenantUsers->filter(fn ($u) => ! in_array($u->id, $assignedUserIds) && $u->role !== $role->name);

        return view('dss.settings.roles.configure', compact(
            'role',
            'tenant',
            'institution',
            'theme',
            'isSuperAdmin',
            'permissionCatalog',
            'allPermissions',
            'assignedPermissions',
            'availablePermissions',
            'rolePermissionNames',
            'assignedUsers',
            'availableUsers'
        ));
    }

    /**
     * Bulk update personnel assigned to this role (Drag-and-Drop save)
     */
    public function updateRolePersonnel(Request $request, $id)
    {
        $id = is_numeric($id) ? (int) $id : dss_decrypt_id($id);
        $tenant = TenantContext::getTenant();

        $role = Role::findOrFail($id);

        if ($role->name === 'SuperAdmin') {
            return response()->json([
                'result' => false,
                'message' => 'Unauthorized: The SuperAdmin role is exclusively reserved for apiloton1@snsu.edu.ph and cannot be assigned to any other personnel.',
            ], 403);
        }

        $validated = $request->validate([
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'integer|exists:users,id',
        ]);

        $newUserIds = $validated['user_ids'] ?? [];

        // Current users in this role
        $currentUsers = User::where('tenant_id', $tenant->id)
            ->where(function ($q) use ($role) {
                $q->where('role', $role->name)
                    ->orWhereHas('roles', function ($rq) use ($role) {
                        $rq->where('roles.id', $role->id);
                    });
            })->get();

        // Users removed from this role
        foreach ($currentUsers as $u) {
            if (! in_array($u->id, $newUserIds)) {
                $u->removeRole($role);
                if ($u->role === $role->name) {
                    $u->update(['role' => 'Staff']);
                }
            }
        }

        // Users added to this role
        foreach ($newUserIds as $uid) {
            $user = User::where('tenant_id', $tenant->id)->find($uid);
            if ($user) {
                $user->update(['role' => $role->name]);
                $user->syncRoles([$role]);
            }
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'result' => true,
                'message' => "Personnel assignments for role '{$role->name}' updated successfully!",
            ]);
        }

        return redirect()->back()->with('success', "Personnel assigned to role '{$role->name}' updated successfully.");
    }

    public function storePermission(Request $request)
    {
        if (! self::isSuperAdminUser()) {
            return response()->json([
                'result' => false,
                'message' => 'Unauthorized: Only SuperAdmin can create new permissions.',
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:120|unique:permissions,name',
            'roles' => 'nullable|array',
            'roles.*' => 'string',
        ]);

        $permission = Permission::firstOrCreate([
            'name' => trim(strtolower($validated['name'])),
            'guard_name' => 'web',
        ]);

        if (! empty($validated['roles'])) {
            foreach ($validated['roles'] as $roleName) {
                $role = Role::where('name', $roleName)->first();
                if ($role) {
                    $role->givePermissionTo($permission);
                }
            }
        }

        return response()->json([
            'result' => true,
            'message' => "Permission '{$permission->name}' registered successfully!",
            'permission' => $permission,
        ]);
    }

    public function getUserPermissions(Request $request, $id)
    {
        $id = is_numeric($id) ? (int) $id : dss_decrypt_id($id);
        $tenant = TenantContext::getTenant();

        $user = User::where('tenant_id', $tenant->id)
            ->with(['campus', 'department', 'roles', 'permissions'])
            ->findOrFail($id);

        return response()->json([
            'result' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'campus_id' => $user->campus_id,
                'dept_id' => $user->dept_id,
                'designation' => $user->designation,
                'status' => $user->status ?? 'Active',
                'direct_permissions' => $user->permissions->pluck('name')->values()->toArray(),
                'role_permissions' => $user->getPermissionsViaRoles()->pluck('name')->values()->toArray(),
            ],
        ]);
    }

    public function assignUserRole(Request $request)
    {
        $tenant = TenantContext::getTenant();

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_name' => 'required|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
            'campus_id' => 'nullable|exists:sys_campuses,id',
            'dept_id' => 'nullable',
            'designation' => 'nullable|string|max:120',
            'status' => 'nullable|string|in:Active,Inactive',
        ]);

        if ($validated['role_name'] === 'SuperAdmin') {
            return response()->json([
                'result' => false,
                'message' => 'Unauthorized: The SuperAdmin role is exclusively reserved for apiloton1@snsu.edu.ph and cannot be assigned to any user.',
            ], 403);
        }

        $user = User::where('tenant_id', $tenant->id)->findOrFail($validated['user_id']);

        $updateData = [
            'role' => $validated['role_name'],
            'campus_id' => $validated['campus_id'] ?? $user->campus_id,
            'dept_id' => $validated['dept_id'] ?? $user->dept_id,
            'designation' => $validated['designation'] ?? $user->designation,
        ];

        if (! empty($validated['status'])) {
            $updateData['status'] = $validated['status'];
        }

        $user->update($updateData);

        // Sync Spatie global role
        $role = Role::where('name', $validated['role_name'])->first();
        if ($role) {
            $user->syncRoles([$role]);
        }

        // Sync direct permissions specifically assigned to this user
        $user->syncPermissions($validated['permissions'] ?? []);

        return response()->json([
            'result' => true,
            'message' => "Role and specific permissions updated successfully for {$user->name}!",
            'user' => $user->load(['campus', 'department', 'roles', 'permissions']),
        ]);
    }

    /**
     * Reset User Password & Generate Temporary Credentials Slip
     */
    public function resetUserPassword(Request $request, $id)
    {
        $id = is_numeric($id) ? (int) $id : dss_decrypt_id($id);
        $tenant = TenantContext::getTenant();

        $user = User::where('tenant_id', $tenant->id)->findOrFail($id);

        $validated = $request->validate([
            'password' => 'nullable|string|min:6|max:64',
            'auto_generate' => 'nullable|boolean',
        ]);

        $clearPassword = trim($validated['password'] ?? '');
        if (empty($clearPassword) || $request->boolean('auto_generate')) {
            $clearPassword = rand(1000, 9999).Str::upper(Str::random(3));
        }

        $user->update([
            'password' => Hash::make($clearPassword),
            'email_verified_at' => $user->email_verified_at ?? now(),
        ]);

        return response()->json([
            'result' => true,
            'message' => "Password reset successfully for {$user->name}!",
            'credentials' => [
                'name' => $user->name,
                'email' => $user->email,
                'username' => $user->email,
                'password' => $clearPassword,
                'role' => $user->role ?: 'Unassigned',
                'campus' => $user->campus?->campus_name ?? 'Main Campus',
                'department' => $user->department?->unit_name ?: ($user->department?->dept_name ?? 'General Administration'),
                'institution' => $tenant->school_name ?? $tenant->name,
                'issued_at' => now()->format('M d, Y h:i A'),
            ],
        ]);
    }

    /**
     * Toggle User Account Status (Active, Inactive, Archived)
     */
    public function toggleUserStatus(Request $request, $id)
    {
        $id = is_numeric($id) ? (int) $id : dss_decrypt_id($id);
        $tenant = TenantContext::getTenant();

        $user = User::where('tenant_id', $tenant->id)->findOrFail($id);

        if ($user->id === auth()->id()) {
            return response()->json([
                'result' => false,
                'message' => 'Action Prohibited: You cannot alter the active status of your currently authenticated account.',
            ], 422);
        }

        $validated = $request->validate([
            'status' => 'required|string|in:Active,Inactive,Archived,active,inactive,archived',
        ]);

        $status = ucfirst(strtolower($validated['status']));
        $user->update(['status' => $status]);

        return response()->json([
            'result' => true,
            'message' => "User account for {$user->name} has been set to {$status}.",
            'status' => $status,
        ]);
    }

    /**
     * Quick Update User Profile Attributes
     */
    public function updateUser(Request $request, $id)
    {
        $id = is_numeric($id) ? (int) $id : dss_decrypt_id($id);
        $tenant = TenantContext::getTenant();

        $user = User::where('tenant_id', $tenant->id)->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:191',
            'email' => 'required|email|max:191|unique:users,email,'.$user->id,
            'campus_id' => 'nullable|exists:sys_campuses,id',
            'dept_id' => 'nullable',
            'designation' => 'nullable|string|max:120',
            'phone_number' => 'nullable|string|max:30',
        ]);

        $user->update($validated);

        return response()->json([
            'result' => true,
            'message' => "Profile details for {$user->name} updated successfully.",
            'user' => $user->load(['campus', 'department']),
        ]);
    }

    /**
     * Dedicated Page: Assign Roles and Permissions to a Specific User
     */
    public function assignUserAccessPage(Request $request, $id)
    {
        $id = is_numeric($id) ? (int) $id : dss_decrypt_id($id);
        $tenant = TenantContext::getTenant();
        $institution = TenantContext::getInstitution();
        $theme = TenantContext::getTheme();
        $isSuperAdmin = self::isSuperAdminUser();

        $user = User::where('tenant_id', $tenant->id)
            ->with(['campus', 'department', 'roles', 'permissions', 'employee'])
            ->findOrFail($id);

        $rolesQuery = Role::query();
        if (! $isSuperAdmin) {
            $rolesQuery->where('name', '!=', 'SuperAdmin');
        }
        $roles = $rolesQuery->orderBy('name')->get();

        // Human-readable descriptions for institutional roles
        $roleDescriptions = [
            'SuperAdmin' => 'Full platform sovereign administrator with unrestricted system-wide access and configuration.',
            'President' => 'University President with executive oversight, institutional concurrence, and policy authority.',
            'Admin' => 'Extension System Administrator with personnel assignment, role configuration, and full RIE oversight.',
            'Director' => 'University Extension Director with executive monitoring, concurrence authority, and budget oversight.',
            'Extension Coordinator' => 'Campus / Program Extension Coordinator responsible for proposal pre-screening, implementation monitoring, and accomplishment reporting.',
            'College Extension Head' => 'College Extension Head responsible for reviewing and endorsing college extension plans and proposals.',
            'Extension Staff' => 'Extension Office Staff supporting daily operations, partner linkages, beneficiary registry, and document tracking.',
            'Extension Tracking Officer' => 'Dedicated Records & DTS Officer responsible for connecting 3rd-party SNSU Central DTS tracking numbers and routing logs.',
            'Extension Budget Officer' => 'Financial & Budget Officer responsible for monitoring annual extension allocations, utilization, and burn rates.',
            'Extension M&E Officer' => 'Monitoring & Evaluation Officer overseeing project milestones, terminal reports, and community impact assessments.',
            'Extension Document Custodian' => 'Repository & Records Custodian managing scanned wet-signed artifacts, MoAs, and institutional extension archives.',
            'Evaluator' => 'Technical Evaluator & Panel Member authorized to score and evaluate submitted extension proposals and impact studies.',
            'Project Leader' => 'Extension Project Leader / Proponent authorized to author extension plans, submit proposals, and manage field activities.',
            'Dean' => 'College Dean with academic unit endorsement, signatory approval, and college extension performance oversight.',
            'UnitHead' => 'Department Chair / Unit Head with unit-level plan review, endorsement, and signatory authority.',
            'Auditor' => 'Internal Compliance Auditor with read-only access to audit trails, analytics, and extension repository records.',
            'Faculty' => 'Academic faculty member authorized to propose extension projects, author work plans, and participate as extensionist.',
            'Employee' => 'General university personnel with read access to extension directories, public programs, and assigned records.',
        ];

        $permissionCatalog = self::getPermissionCatalog();

        // Build flat catalog of all permissions with domain and human description
        $allPermissions = Permission::orderBy('name')->get()->map(function ($perm) use ($permissionCatalog) {
            $desc = 'General system access capability.';
            $domain = 'General System';
            foreach ($permissionCatalog as $dName => $pList) {
                if (isset($pList[$perm->name])) {
                    $desc = $pList[$perm->name];
                    $domain = $dName;
                    break;
                }
            }

            return [
                'id' => $perm->id,
                'name' => $perm->name,
                'domain' => $domain,
                'details' => $desc,
            ];
        });

        $userRoleNames = $user->roles->pluck('name')->toArray();
        $userDirectPermNames = $user->permissions->pluck('name')->toArray();

        return view('dss.settings.users.assign', compact(
            'tenant',
            'institution',
            'theme',
            'user',
            'roles',
            'roleDescriptions',
            'allPermissions',
            'permissionCatalog',
            'userRoleNames',
            'userDirectPermNames',
            'isSuperAdmin'
        ));
    }

    /**
     * AJAX Endpoint: Instant Toggle Specific Role for User
     */
    public function toggleUserRoleAjax(Request $request, $id)
    {
        $id = is_numeric($id) ? (int) $id : dss_decrypt_id($id);
        $tenant = TenantContext::getTenant();

        $user = User::where('tenant_id', $tenant->id)->findOrFail($id);

        $validated = $request->validate([
            'role_name' => 'required|string',
            'state' => 'required|boolean',
        ]);

        $roleName = $validated['role_name'];
        $state = (bool) $validated['state'];

        if ($roleName === 'SuperAdmin' && ! self::isSuperAdminUser()) {
            return response()->json([
                'result' => false,
                'message' => 'Unauthorized: Only SuperAdmin can assign the SuperAdmin role.',
            ], 403);
        }

        $role = Role::where('name', $roleName)->first();
        if (! $role) {
            return response()->json([
                'result' => false,
                'message' => "Role '{$roleName}' does not exist in the system catalog.",
            ], 404);
        }

        if ($state) {
            $user->assignRole($role);
            if (empty($user->role) || $user->role === 'Unassigned' || $user->role === 'Employee') {
                $user->update(['role' => $roleName]);
            }
            $message = "Role '{$roleName}' assigned to {$user->name}.";
        } else {
            $user->removeRole($role);
            if ($user->role === $roleName) {
                $remaining = $user->roles()->first();
                $user->update(['role' => $remaining ? $remaining->name : 'Employee']);
            }
            $message = "Role '{$roleName}' removed from {$user->name}.";
        }

        return response()->json([
            'result' => true,
            'message' => $message,
            'user_roles' => $user->roles()->pluck('name')->toArray(),
            'primary_role' => $user->role,
            'roles_count' => $user->roles()->count(),
        ]);
    }

    /**
     * AJAX Endpoint: Instant Toggle Specific Direct Permission for User
     */
    public function toggleUserPermissionAjax(Request $request, $id)
    {
        $id = is_numeric($id) ? (int) $id : dss_decrypt_id($id);
        $tenant = TenantContext::getTenant();

        $user = User::where('tenant_id', $tenant->id)->findOrFail($id);

        $validated = $request->validate([
            'permission_name' => 'required|string',
            'state' => 'required|boolean',
        ]);

        $permName = trim($validated['permission_name']);
        $state = (bool) $validated['state'];

        $perm = Permission::firstOrCreate([
            'name' => $permName,
            'guard_name' => 'web',
        ]);

        if ($state) {
            $user->givePermissionTo($perm);
            $message = "Permission '{$permName}' granted to {$user->name}.";
        } else {
            $user->revokePermissionTo($perm);
            $message = "Permission '{$permName}' revoked from {$user->name}.";
        }

        return response()->json([
            'result' => true,
            'message' => $message,
            'user_permissions' => $user->permissions()->pluck('name')->toArray(),
            'permissions_count' => $user->permissions()->count(),
        ]);
    }
}

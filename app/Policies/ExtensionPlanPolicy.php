<?php

namespace App\Policies;

use App\Models\RIE\ExtensionPlan;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExtensionPlanPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        if ($user->isSuperAdmin() || $user->isTenantAdmin()) {
            return true;
        }

        if ($user->can('rie.plans.view') || $user->can('rie.plans.manage')) {
            return true;
        }

        $rieRoles = [
            'Admin',
            'Administrator',
            'Director',
            'Extension Coordinator',
            'College Extension Head',
            'Extension Staff',
            'Extension Tracking Officer',
            'Extension Budget Officer',
            'Extension M&E Officer',
            'Extension Document Custodian',
            'Dean',
            'UnitHead',
        ];

        if ($this->safeHasAnyRole($user, $rieRoles)) {
            return true;
        }

        if (in_array(strtolower($user->role ?? ''), ['admin', 'superadmin', 'director', 'dean', 'staff'])) {
            return true;
        }

        if ($user->primaryDesignation && (
            str_contains(strtolower($user->primaryDesignation->designation_title ?? ''), 'rie') ||
            str_contains(strtolower($user->primaryDesignation->designation_title ?? ''), 'extension')
        )) {
            return true;
        }

        return false;
    }

    public function view(User $user, ExtensionPlan $plan): bool
    {
        if ($plan->prepared_by_user_id && $plan->prepared_by_user_id === $user->id) {
            return true;
        }

        if ($user->employee_id && in_array($user->employee_id, [
            $plan->reviewed_by_employee_id,
            $plan->noted_by_employee_id,
            $plan->approved_by_employee_id,
            $plan->concurred_by_employee_id,
            $plan->concurred_by_2_employee_id,
        ], true)) {
            return true;
        }

        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->can('rie.plans.create')
            || $this->safeHasAnyRole($user, ['Project Leader', 'Faculty', 'Employee', 'Admin', 'SuperAdmin', 'Director']);
    }

    public function update(User $user, ExtensionPlan $plan): bool
    {
        if ($this->safeHasAnyPermission($user, ['rie.plans.manage']) || $user->isSuperAdmin() || $user->isTenantAdmin()) {
            return true;
        }

        if ($plan->prepared_by_user_id === $user->id) {
            return in_array($plan->status, ['draft', 'returned', null]);
        }

        return false;
    }

    public function delete(User $user, ExtensionPlan $plan): bool
    {
        if ($this->safeHasAnyPermission($user, ['rie.plans.delete']) || $user->isSuperAdmin() || $user->isTenantAdmin()) {
            return true;
        }

        if ($plan->prepared_by_user_id === $user->id && $plan->status === 'draft') {
            return true;
        }

        return false;
    }

    protected function safeHasAnyPermission(User $user, array $permissions): bool
    {
        try {
            if (! method_exists($user, 'hasPermissionTo')) {
                return false;
            }

            foreach ($permissions as $permission) {
                try {
                    if ($user->hasPermissionTo($permission)) {
                        return true;
                    }
                } catch (\Throwable $e) {
                }
            }
        } catch (\Throwable $e) {
            return false;
        }

        return false;
    }

    protected function safeHasAnyRole(User $user, array $roles): bool
    {
        try {
            if (! method_exists($user, 'hasRole')) {
                return false;
            }

            foreach ($roles as $role) {
                try {
                    if ($user->hasRole($role)) {
                        return true;
                    }
                } catch (\Throwable $e) {
                }
            }
        } catch (\Throwable $e) {
            return false;
        }

        return false;
    }
}

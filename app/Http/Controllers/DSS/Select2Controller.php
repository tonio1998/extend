<?php

namespace App\Http\Controllers\DSS;

use App\Http\Controllers\Controller;
use App\Models\Accreditation\AcademicProgram;
use App\Models\Dts\DocumentAction;
use App\Models\Dts\DocumentType;
use App\Models\Dts\Unit;
use App\Models\Employee;
use App\Models\User;
use App\Services\DSS\TenantContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Select2Controller extends Controller
{
    /**
     * Select2 server-side search — Units / Offices (Flat list, ordered A-Z).
     *
     * Returns: { results: [{id, text, unit_name, unit_code, short_name, unit_type, building}], pagination: {more: bool} }
     */
    public function units(Request $request): JsonResponse
    {
        $tenant = TenantContext::getTenant();
        $tenantId = $tenant?->id ?? 1;
        $search = trim((string) $request->query('q', ''));
        $page = max(1, (int) $request->query('page', 1));
        $perPage = 30;

        $query = Unit::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->with(['campus'])
            ->orderBy('unit_name', 'asc');

        if ($request->filled('campus_id')) {
            $query->where('campus_id', $request->input('campus_id'));
        }

        if ($request->filled('ids')) {
            $ids = array_filter(array_map('intval', explode(',', (string) $request->input('ids'))));
            $query->whereIn('id', $ids);
        } elseif ($request->filled('id')) {
            $query->where('id', (int) $request->input('id'));
        } elseif ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('unit_name', 'like', "%{$search}%")
                    ->orWhere('unit_code', 'like', "%{$search}%")
                    ->orWhere('short_name', 'like', "%{$search}%")
                    ->orWhere('building', 'like', "%{$search}%")
                    ->orWhere('unit_type', 'like', "%{$search}%");
            });
        }

        $paginator = $query->paginate($perPage, ['id', 'unit_name', 'unit_code', 'short_name', 'unit_type', 'building'], 'page', $page);

        $results = [];
        foreach ($paginator->items() as $unit) {
            $label = $unit->unit_name.($unit->unit_code ? " ({$unit->unit_code})" : '');
            $results[] = [
                'id' => $unit->id,
                'text' => $label,
                'unit_name' => $unit->unit_name,
                'unit_code' => $unit->unit_code,
                'short_name' => $unit->short_name,
                'unit_type' => $unit->unit_type,
                'building' => $unit->building,
            ];
        }

        return response()->json([
            'results' => $results,
            'pagination' => ['more' => $paginator->hasMorePages()],
        ]);
    }

    /**
     * Backward-compatible alias for units.
     */
    public function departments(Request $request): JsonResponse
    {
        return $this->units($request);
    }

    /**
     * Select2 server-side search — Users (Flat list, ungrouped, ordered A-Z).
     *
     * Returns: { results: [{id, text, name, designation, office, campus, meta}], pagination: {more: bool} }
     */
    public function users(Request $request): JsonResponse
    {
        $tenant = TenantContext::getTenant();
        $tenantId = $tenant?->id ?? 1;
        $search = trim((string) $request->query('q', ''));
        $page = max(1, (int) $request->query('page', 1));
        $perPage = 30;

        $query = User::with(['department', 'campus'])
            ->where('tenant_id', $tenantId)
            ->where('status', '!=', 'inactive')
            ->orderBy('name', 'asc');

        if ($request->filled('ids')) {
            $ids = array_filter(array_map('intval', explode(',', (string) $request->input('ids'))));
            $query->whereIn('id', $ids);
        } elseif ($request->filled('id')) {
            $query->where('id', (int) $request->input('id'));
        } elseif ($request->filled('names')) {
            $names = array_filter(array_map('trim', explode(',', (string) $request->input('names'))));
            $query->whereIn('name', $names);
        } elseif ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('designation', 'like', "%{$search}%")
                    ->orWhereHas('department', function ($dq) use ($search) {
                        $dq->where('unit_name', 'like', "%{$search}%")
                            ->orWhere('unit_code', 'like', "%{$search}%");
                    });
            });
        }

        $paginator = $query->paginate($perPage, ['id', 'name', 'dept_id', 'campus_id', 'designation', 'role'], 'page', $page);

        $isNumericId = $request->query('id_type') === 'user_id' || $request->query('id_type') === 'id' || $request->boolean('numeric_id');

        $results = [];
        foreach ($paginator->items() as $user) {
            $office = $user->department?->unit_name ?: ($user->department?->dept_name ?: '');
            $designation = $user->designation ?: ($user->role ?: 'Unit Official');
            $label = $user->name.($designation ? " ({$designation})" : '');
            $meta = trim($designation.($office ? ' • '.$office : ''));

            $results[] = [
                'id' => $isNumericId ? $user->id : $user->name,
                'user_id' => $user->id,
                'text' => $label,
                'name' => $user->name,
                'designation' => $designation,
                'office' => $office,
                'campus' => $user->campus?->campus_name ?: 'Main Campus',
                'meta' => $meta,
            ];
        }

        return response()->json([
            'results' => $results,
            'pagination' => ['more' => $paginator->hasMorePages()],
        ]);
    }

    /**
     * Select2 server-side search — Document Types / Categories (Flat list, ordered A-Z).
     *
     * Returns: { results: [{id, text, name, code, cc_code, complexity, sla, client, fees, reqs, primary_unit, flow_steps}], pagination: {more: bool} }
     */
    public function docTypes(Request $request): JsonResponse
    {
        $tenant = TenantContext::getTenant();
        $tenantId = $tenant?->id ?? 1;
        $search = trim((string) $request->query('q', ''));
        $page = max(1, (int) $request->query('page', 1));
        $perPage = 30;

        $query = DocumentType::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->orderBy('name', 'asc');

        if ($request->filled('ids')) {
            $ids = array_filter(array_map('intval', explode(',', (string) $request->input('ids'))));
            $query->whereIn('id', $ids);
        } elseif ($request->filled('id')) {
            $query->where('id', (int) $request->input('id'));
        } elseif ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('citizen_charter_code', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            });
        }

        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        $results = [];
        foreach ($paginator->items() as $dt) {
            $flowSteps = collect();
            try {
                $rawSteps = $dt->getFlowSteps();
                if ($rawSteps && $rawSteps->isNotEmpty()) {
                    $flowSteps = $rawSteps->sortBy('step_order')->values()->map(function ($s) {
                        return [
                            'step' => $s->step_order,
                            'office' => $s->unit?->unit_name ?: 'Designated Station',
                            'unit_id' => $s->unit_id ?? $s->unit?->id,
                            'dept_id' => $s->unit_id ?? $s->unit?->id,
                            'sla' => $s->processing_time ?: ($s->sla_duration ? $s->sla_duration.' '.$s->sla_unit : ($s->sla_hours ? $s->sla_hours.'h' : '1d')),
                            'action' => $s->action_required ?: ($s->instructions ?: 'For processing'),
                        ];
                    });
                }
            } catch (\Throwable $e) {
                // Gracefully fallback if auxiliary routing template tables are not migrated
            }

            $label = $dt->name.($dt->code ? " ({$dt->code})" : '');
            if ($dt->complexity && $dt->standard_sla_days) {
                $label .= " — {$dt->complexity} ({$dt->standard_sla_days}d)";
            }

            $results[] = [
                'id' => $dt->id,
                'text' => $label,
                'name' => $dt->name,
                'code' => $dt->code,
                'cc_code' => $dt->citizen_charter_code,
                'complexity' => $dt->complexity ?: 'Simple',
                'sla' => (string) ($dt->standard_sla_days ?: 3),
                'client' => $dt->client_type ?: 'G2G',
                'fees' => $dt->fees ?: 'Free / None',
                'reqs' => $dt->getRequirementsArray(),
                'primary_unit' => $dt->primary_unit_id,
                'flow_steps' => $flowSteps->toArray(),
            ];
        }

        return response()->json([
            'results' => $results,
            'pagination' => ['more' => $paginator->hasMorePages()],
        ]);
    }

    /**
     * Select2 server-side search — Document Actions (Flat list, ordered A-Z).
     *
     * Returns: { results: [{id, text, is_default}], pagination: {more: bool} }
     */
    public function actions(Request $request): JsonResponse
    {
        $tenant = TenantContext::getTenant();
        $tenantId = $tenant?->id ?? 1;
        $search = trim((string) $request->query('q', ''));
        $page = max(1, (int) $request->query('page', 1));
        $perPage = 30;

        $query = DocumentAction::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->orderBy('name', 'asc');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        $results = [];
        if ($paginator->isEmpty() && $search === '') {
            $defaultActions = [
                'For Accounting Pre-Audit & Verification',
                'For Approval / Signature',
                'For Appropriate Action & Compliance',
                'For Budget & Allotment Clearance',
                'For Endorsement & Clearance',
                'For Information & Notation',
                'For Review & Recommendation',
            ];
            sort($defaultActions);
            foreach ($defaultActions as $act) {
                $results[] = [
                    'id' => $act,
                    'text' => $act,
                    'is_default' => $act === 'For Appropriate Action & Compliance',
                ];
            }

            return response()->json([
                'results' => $results,
                'pagination' => ['more' => false],
            ]);
        }

        foreach ($paginator->items() as $act) {
            $results[] = [
                'id' => $act->name,
                'text' => $act->name,
                'is_default' => (bool) $act->is_default,
            ];
        }

        return response()->json([
            'results' => $results,
            'pagination' => ['more' => $paginator->hasMorePages()],
        ]);
    }

    /**
     * Select2 server-side search — Academic Programs Catalog (Flat list ordered by code/name).
     *
     * Returns: { results: [{id, text, program_code, program_name, major, degree_level, discipline_cluster, default_instrument_template}] }
     */
    public function academicPrograms(Request $request): JsonResponse
    {
        $tenant = TenantContext::getTenant();
        $tenantId = $tenant?->id ?? 1;
        $search = trim((string) $request->query('q', ''));
        $level = trim((string) $request->query('degree_level', ''));
        $campusId = $request->filled('campus_id') ? (int) $request->query('campus_id') : null;

        $query = AcademicProgram::with(['campus'])
            ->where('is_active', true)
            ->where(function ($q) use ($tenantId) {
                $q->whereNull('tenant_id')->orWhere('tenant_id', $tenantId);
            })
            ->orderBy('degree_level', 'asc')
            ->orderBy('discipline_cluster', 'asc')
            ->orderBy('program_code', 'asc');

        if ($campusId) {
            $query->where(function ($q) use ($campusId) {
                $q->where('campus_id', $campusId)
                    ->orWhereNull('campus_id');
            });
        }

        if ($level !== '') {
            $query->where('degree_level', $level);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('program_code', 'like', "%{$search}%")
                    ->orWhere('program_name', 'like', "%{$search}%")
                    ->orWhere('major', 'like', "%{$search}%")
                    ->orWhere('discipline_cluster', 'like', "%{$search}%");
            });
        }

        $programs = $query->limit(80)->get();

        $results = [];
        foreach ($programs as $prog) {
            $majorStr = $prog->major ? " ({$prog->major})" : '';
            $campusStr = $prog->campus ? " [{$prog->campus->campus_name}]" : '';
            $results[] = [
                'id' => $prog->id,
                'text' => "[{$prog->program_code}] {$prog->program_name}{$majorStr}{$campusStr} — {$prog->degree_level}",
                'program_code' => $prog->program_code,
                'program_name' => $prog->program_name,
                'major' => $prog->major,
                'degree_level' => $prog->degree_level,
                'discipline_cluster' => $prog->discipline_cluster,
                'default_instrument_template' => $prog->default_instrument_template,
                'campus_id' => $prog->campus_id,
                'campus_name' => $prog->campus?->campus_name,
            ];
        }

        return response()->json(['results' => $results]);
    }

    /**
     * Select2 server-side search — Employees (Master Institutional Directory).
     *
     * Returns: { results: [{id, text, employee_id, employee_no, full_name, designation, department, campus, email, phone}], pagination: {more: bool} }
     */
    public function employees(Request $request): JsonResponse
    {
        $tenant = TenantContext::getTenant();
        $tenantId = $request->filled('tenant_id') ? (int) $request->input('tenant_id') : ($tenant?->id ?? 1);
        $search = trim((string) $request->query('q', ''));
        $page = max(1, (int) $request->query('page', 1));
        $perPage = 30;

        $query = Employee::where('tenant_id', $tenantId)
            ->with(['department', 'campus'])
            ->orderBy('last_name', 'asc')
            ->orderBy('first_name', 'asc');

        if ($request->filled('ids')) {
            $ids = array_filter(array_map('intval', explode(',', (string) $request->input('ids'))));
            $query->whereIn('id', $ids);
        } elseif ($request->filled('id')) {
            $query->where('id', (int) $request->input('id'));
        } elseif ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('middle_name', 'like', "%{$search}%")
                    ->orWhere('employee_no', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('designation', 'like', "%{$search}%")
                    ->orWhereHas('department', function ($dq) use ($search) {
                        $dq->where('unit_name', 'like', "%{$search}%")
                            ->orWhere('unit_code', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('campus_id')) {
            $query->where('campus_id', (int) $request->input('campus_id'));
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', (int) $request->input('department_id'));
        }

        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        $namesOnly = $request->boolean('names_only') || $request->has('clean');

        $results = [];
        foreach ($paginator->items() as $emp) {
            $fullName = $emp->full_name;
            $office = $emp->department?->unit_name ?: ($emp->department?->dept_name ?: '');
            $designation = $emp->designation ?: 'Institutional Personnel';

            $label = $namesOnly ? $fullName : $fullName.($designation ? " — {$designation}" : '').($office ? " ({$office})" : '');
            $meta = trim($designation.($office ? ' • '.$office : ''));

            $results[] = [
                'id' => $emp->id,
                'employee_id' => $emp->id,
                'text' => $label,
                'name' => $fullName,
                'full_name' => $fullName,
                'employee_no' => $emp->employee_no,
                'designation' => $designation,
                'office' => $office,
                'department_id' => $emp->department_id,
                'gender' => $emp->gender ?: '',
                'campus' => $emp->campus?->campus_name ?: 'Main Campus',
                'campus_id' => $emp->campus_id,
                'email' => $emp->email,
                'phone' => $emp->phone_number,
                'meta' => $meta,
            ];
        }

        return response()->json([
            'results' => $results,
            'pagination' => ['more' => $paginator->hasMorePages()],
        ]);
    }
}

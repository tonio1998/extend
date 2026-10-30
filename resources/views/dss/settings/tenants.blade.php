@extends('layouts.dss.app')

@section('title', 'SaaS Institutional Tenant Registry — SuperAdmin Control')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <h3 class="fw-bold text-dark mb-0">SaaS Multi-Tenant Institutional Registry</h3>
                <span class="badge bg-danger font-mono">SuperAdmin Only</span>
            </div>
            <p class="text-muted small mb-0">Provision and govern sovereign institutional databases, universities, colleges, and administrative domains.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('dss.settings.tenants.create') }}" class="btn btn-dss-primary btn-sm px-3.5 shadow-xs">
                <i class="mdi mdi-plus-circle-outline me-1"></i>Register New Institution / School
            </a>
        </div>
    </div>

    @if(session()->has('impersonating_tenant_id'))
        @php
            $actingTenant = $allTenants->firstWhere('id', session('impersonating_tenant_id'));
        @endphp
        <div class="alert alert-warning border-warning d-flex align-items-center justify-content-between p-3 mb-4 rounded-3 shadow-2xs flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2.5">
                <i class="mdi mdi-account-switch fs-3 text-warning flex-shrink-0"></i>
                <div>
                    <div class="fw-bold text-dark" style="font-size: 13.5px;">
                        Currently Acting as School: {{ $actingTenant?->name ?? 'Tenant #'.session('impersonating_tenant_id') }}
                        @if($actingTenant?->institutionSetting?->school_acronym)
                            <span class="badge bg-warning text-dark font-mono ms-1">{{ $actingTenant->institutionSetting->school_acronym }}</span>
                        @endif
                    </div>
                    <div class="text-muted extra-small">
                        You are actively in this institutional context and can navigate and interact with all modules as a member of this school.
                    </div>
                </div>
            </div>
            <form action="{{ route('dss.settings.tenants.exit_switch') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="btn btn-sm btn-dark d-flex align-items-center gap-1.5 shadow-2xs font-mono">
                    <i class="mdi mdi-arrow-left-bold-box-outline text-warning"></i>
                    <span>Exit School Context</span>
                </button>
            </form>
        </div>
    @endif

    <!-- Tenant Cards / Grid -->
    <div class="dss-card p-4">
        <div class="table-responsive" style="min-height: 260px;">
            <table class="table table-hover align-middle" style="font-size: 13px;">
                <thead class="table-light">
                    <tr>
                        <th style="width: 140px;" class="ps-3">Actions</th>
                        <th>UUID / Slug</th>
                        <th>Institution Name</th>
                        <th>Classification</th>
                        <th>Campuses</th>
                        <th>Personnel</th>
                        <th>Status</th>
                        <th class="pe-3">Created At</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($allTenants as $t)
                        <tr class="{{ session('impersonating_tenant_id') == $t->id ? 'table-warning bg-opacity-10' : '' }}">
                            <td class="ps-3">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-white border shadow-2xs dropdown-toggle d-flex align-items-center gap-1.5 px-2.5 py-1 text-dark fw-semibold"
                                            type="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-boundary="viewport">
                                        <i class="mdi mdi-dots-vertical text-muted"></i>
                                        <span>Actions</span>
                                    </button>
                                    <ul class="dropdown-menu shadow-sm border mt-1" style="font-size: 12.5px; min-width: 210px; border-radius: 8px;">
                                        <li>
                                            <button type="button" class="dropdown-item py-1.5 d-flex align-items-center gap-2"
                                                    onclick='openEditTenantModal({
                                                        id: {{ $t->id }},
                                                        name: @json($t->name),
                                                        acronym: @json($t->institutionSetting?->school_acronym ?: ""),
                                                        type: @json($t->institutionSetting?->school_type ?: "State University and College (SUC)"),
                                                        plan: @json($t->plan ?: "Institutional Enterprise"),
                                                        domain: @json($t->domain ?: ""),
                                                        status: @json($t->status)
                                                    })'>
                                                <i class="mdi mdi-pencil-outline text-primary fs-6"></i>
                                                <span>Update School Details</span>
                                            </button>
                                        </li>
                                        <li>
                                            <button type="button" class="dropdown-item py-1.5 d-flex align-items-center gap-2 {{ $t->status === 'active' ? 'text-danger' : 'text-success' }}"
                                                    onclick="toggleTenantStatus({{ $t->id }}, @json($t->name), @json($t->status))">
                                                <i class="mdi {{ $t->status === 'active' ? 'mdi-cancel text-danger' : 'mdi-check-circle-outline text-success' }} fs-6"></i>
                                                <span>{{ $t->status === 'active' ? 'Disable School' : 'Activate School' }}</span>
                                            </button>
                                        </li>
                                        <li><hr class="dropdown-divider my-1"></li>
                                        <li>
                                            @if(session('impersonating_tenant_id') == $t->id)
                                                <form action="{{ route('dss.settings.tenants.exit_switch') }}" method="POST" class="m-0">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item py-1.5 d-flex align-items-center gap-2 text-warning fw-bold">
                                                        <i class="mdi mdi-arrow-left-bold-box-outline text-warning fs-6"></i>
                                                        <span>Exit Current School</span>
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('dss.settings.tenants.switch', $t->id) }}" method="POST" class="m-0">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item py-1.5 d-flex align-items-center gap-2 text-dark">
                                                        <i class="mdi mdi-swap-horizontal text-info fs-6"></i>
                                                        <span>Switch to this School</span>
                                                    </button>
                                                </form>
                                            @endif
                                        </li>
                                    </ul>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-dss-primary font-mono px-2.5 py-1.5">{{ $t->slug }}</span>
                                <div class="extra-small text-muted font-mono mt-0.5" style="font-size: 10px;">{{ substr($t->uuid, 0, 13) }}...</div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark d-flex align-items-center gap-1.5">
                                    <span>{{ $t->name }}</span>
                                    @if(session('impersonating_tenant_id') == $t->id)
                                        <span class="badge bg-warning text-dark font-mono" style="font-size: 10px;">
                                            <i class="mdi mdi-check"></i> Acting Context
                                        </span>
                                    @endif
                                </div>
                                <div class="extra-small text-muted">{{ $t->institutionSetting?->school_acronym }} &bull; {{ $t->domain ?: 'Subdomain Tenant' }}</div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border font-mono">
                                    {{ $t->institutionSetting?->school_type ?: 'SUC' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-light text-secondary border font-mono">
                                    <i class="mdi mdi-office-building-marker me-0.5"></i>{{ $t->campuses->count() }} Campuses
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border font-mono">
                                    <i class="mdi mdi-account-group me-0.5"></i>{{ $t->users->count() }} Users
                                </span>
                            </td>
                            <td>
                                @if($t->status === 'active')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle font-mono px-2.5 py-1">
                                        <i class="mdi mdi-check-circle-outline me-1"></i>Active
                                    </span>
                                @elseif($t->status === 'disabled')
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle font-mono px-2.5 py-1">
                                        <i class="mdi mdi-cancel me-1"></i>Disabled
                                    </span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle font-mono px-2.5 py-1">
                                        {{ ucfirst($t->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="font-mono extra-small text-muted pe-3">
                                {{ $t->created_at->format('M d, Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">No institutional tenants found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $allTenants->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: UPDATE INSTITUTIONAL SCHOOL DETAILS (SUPERADMIN)                   -->
<!-- ========================================================================= -->
<div class="modal fade" id="editTenantModal" tabindex="-1" aria-labelledby="editTenantModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 540px;">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header border-bottom py-3 px-4 bg-white">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="rounded-3 bg-primary-subtle text-primary p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="mdi mdi-office-building-cog text-primary fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="editTenantModalLabel">Update School Details</h5>
                        <div class="extra-small text-muted" id="editTenantSubtitle">Modify institutional profile &amp; tenant settings</div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditTenant" onsubmit="submitEditTenant(event)">
                <input type="hidden" id="edit_tenant_id">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted mb-1">Official Institution Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_institution_name" required placeholder="e.g. NorthEastern Mindanao State University">
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted mb-1">School Acronym <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_school_acronym" required placeholder="e.g. NEMSU">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted mb-1">Classification <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_school_type" required>
                                <option value="State University and College (SUC)">State University / College (SUC)</option>
                                <option value="Local University and College (LUC)">Local University / College (LUC)</option>
                                <option value="CHED Autonomous / Deregulated HEI">CHED Autonomous / Deregulated HEI</option>
                                <option value="Private Higher Education Institution">Private HEI</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted mb-1">Subscription Plan</label>
                            <select class="form-select" id="edit_plan">
                                <option value="Institutional Enterprise">Institutional Enterprise</option>
                                <option value="Standard SUC">Standard SUC</option>
                                <option value="Academic Basic">Academic Basic</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted mb-1">Operational Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_status" required>
                                <option value="active">Active (Operational)</option>
                                <option value="disabled">Disabled (Suspended)</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted mb-1">Domain / Subdomain (Optional)</label>
                        <input type="text" class="form-control font-mono" id="edit_domain" placeholder="e.g. nemsu.edu.ph">
                        <div class="form-text extra-small text-muted">Primary DNS host domain or custom institutional gateway.</div>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light py-2.5 px-4 d-flex justify-content-between">
                    <button type="button" class="btn btn-light border btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dss-primary btn-sm px-4 fw-bold shadow-xs" id="btnSaveEditTenant">
                        <i class="mdi mdi-content-save-check me-1"></i>Save School Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let editTenantModalInstance = null;

    function openEditTenantModal(data) {
        document.getElementById('edit_tenant_id').value = data.id;
        document.getElementById('edit_institution_name').value = data.name;
        document.getElementById('edit_school_acronym').value = data.acronym;
        document.getElementById('edit_school_type').value = data.type;
        document.getElementById('edit_plan').value = data.plan;
        document.getElementById('edit_domain').value = data.domain;
        document.getElementById('edit_status').value = data.status;

        document.getElementById('editTenantSubtitle').innerText = `Managing ${data.name} (Team #${data.id})`;

        if (!editTenantModalInstance) {
            editTenantModalInstance = new bootstrap.Modal(document.getElementById('editTenantModal'));
        }
        editTenantModalInstance.show();
    }

    function submitEditTenant(e) {
        e.preventDefault();
        const tenantId = document.getElementById('edit_tenant_id').value;
        const btn = document.getElementById('btnSaveEditTenant');
        const origHtml = btn.innerHTML;

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Updating...';

        fetch(`/dss/settings/tenants/${tenantId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                institution_name: document.getElementById('edit_institution_name').value.trim(),
                school_acronym: document.getElementById('edit_school_acronym').value.trim(),
                school_type: document.getElementById('edit_school_type').value,
                plan: document.getElementById('edit_plan').value,
                domain: document.getElementById('edit_domain').value.trim() || null,
                status: document.getElementById('edit_status').value,
            })
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = origHtml;

            if (data.result) {
                editTenantModalInstance.hide();
                Swal.fire({
                    icon: 'success',
                    title: 'School Updated!',
                    text: data.message,
                    confirmButtonColor: 'var(--dss-primary)',
                    timer: 1500
                }).then(() => location.reload());
            } else {
                Swal.fire('Error', data.message || 'Failed to update school details.', 'error');
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = origHtml;
            Swal.fire('Error', 'An error occurred while updating school details.', 'error');
        });
    }

    function toggleTenantStatus(tenantId, tenantName, currentStatus) {
        const isDisabling = (currentStatus === 'active');
        const actionTitle = isDisabling ? `Disable ${tenantName}?` : `Activate ${tenantName}?`;
        const actionText = isDisabling
            ? 'Personnel belonging to this institution will be temporarily suspended from logging in until re-enabled.'
            : 'This school will be restored to active operational status.';
        const confirmBtnText = isDisabling ? 'Yes, Disable School' : 'Yes, Activate School';
        const confirmBtnColor = isDisabling ? '#dc2626' : 'var(--dss-primary, #059669)';

        Swal.fire({
            title: actionTitle,
            text: actionText,
            icon: isDisabling ? 'warning' : 'question',
            showCancelButton: true,
            confirmButtonText: confirmBtnText,
            confirmButtonColor: confirmBtnColor,
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/dss/settings/tenants/${tenantId}/toggle-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.result) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Status Updated!',
                            text: data.message,
                            confirmButtonColor: 'var(--dss-primary)',
                            timer: 1500
                        }).then(() => location.reload());
                    } else {
                        Swal.fire('Error', data.message || 'Failed to toggle school status.', 'error');
                    }
                })
                .catch(err => {
                    Swal.fire('Error', 'Unable to toggle school status.', 'error');
                });
            }
        });
    }
</script>
@endsection

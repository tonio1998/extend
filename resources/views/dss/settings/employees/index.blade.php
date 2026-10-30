@extends('layouts.dss.app')

@section('title', 'Employee Master Entity Directory — SNSU')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/libs/datatables/css/dataTables.bootstrap5.min.css') }}">
<style>
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0;
    }
    .dataTables_wrapper .dataTables_filter input {
        border-radius: 6px;
        border: 1px solid #cbd5e1;
        padding: 5px 12px;
        font-size: 13px;
    }
    .dataTables_wrapper .dataTables_length select {
        border-radius: 6px;
        border: 1px solid #cbd5e1;
        padding: 4px 10px;
        font-size: 13px;
    }
    .emp-avatar {
        width: 38px;
        height: 38px;
        min-width: 38px;
        font-size: 13px;
        font-weight: 700;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .filter-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        color: #475569;
        background-color: #f1f5f9;
        border: 1px solid #e2e8f0;
        text-decoration: none;
        transition: all 0.15s ease;
    }
    .filter-chip:hover, .filter-chip.active {
        background-color: #0f172a;
        color: #ffffff;
        border-color: #0f172a;
    }
    .filter-chip .chip-count {
        background: rgba(0,0,0,0.08);
        border-radius: 10px;
        padding: 1px 6px;
        font-size: 11px;
    }
    .filter-chip.active .chip-count {
        background: rgba(255,255,255,0.25);
        color: #ffffff;
    }
    .status-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        display: inline-block;
    }
    .status-dot-active { background-color: #10b981; }
    .status-dot-leave { background-color: #f59e0b; }
    .status-dot-inactive { background-color: #94a3b8; }
</style>
@endsection

@section('content')
<div class="container-fluid p-0">
    <!-- Header / Breadcrumb -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <h3 class="fw-bold text-dark mb-0">Employee Master Directory</h3>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 font-mono" style="font-size: 11px;">
                    <i class="mdi mdi-shield-account me-1 text-primary"></i>SNSU Personnel Directory
                </span>
            </div>
            <p class="text-muted small mb-0">
                Master institutional directory for <strong>{{ $tenant->school_name ?? $tenant->name ?? 'Surigao del Norte State University' }}</strong>.
            </p>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('dss.settings.designations.index') }}" class="btn btn-sm btn-white border shadow-2xs fw-semibold px-3 py-2 text-dark d-flex align-items-center gap-1.5" title="Manage master institutional positions and titles">
                <i class="mdi mdi-briefcase-account-outline text-primary fs-6"></i>
                <span>Manage Positions</span>
            </a>

            <a href="{{ route('dss.settings.employees.export', request()->query()) }}" class="btn btn-sm btn-white border shadow-2xs fw-semibold px-3 py-2 text-dark d-flex align-items-center gap-1.5" title="Export current employee roster to CSV">
                <i class="mdi mdi-download text-muted fs-6"></i>
                <span>Export CSV</span>
            </a>

            <a href="{{ route('dss.settings.employees.create') }}" class="btn btn-sm btn-success px-3.5 py-2 fw-semibold shadow-2xs d-flex align-items-center gap-1.5">
                <i class="mdi mdi-account-plus-outline fs-6"></i>
                <span>Add New Employee</span>
            </a>
        </div>
    </div>

    @php
        $activePreset = request('filter', '');
    @endphp

    <!-- Quick Filter Chips Row -->
    <div class="d-flex align-items-center gap-2 mb-3 flex-wrap">
        <span class="extra-small text-muted fw-bold text-uppercase tracking-wider me-1">Quick Views:</span>
        <button type="button" class="filter-chip border-0 {{ empty($activePreset) ? 'active' : '' }}" data-filter="">
            <span>All Personnel</span>
            <span class="chip-count">{{ number_format($totalEmployees) }}</span>
        </button>
        <button type="button" class="filter-chip border-0 {{ $activePreset === 'active' ? 'active' : '' }}" data-filter="active">
            <span class="status-dot status-dot-active"></span>
            <span>Active</span>
            <span class="chip-count">{{ number_format($activeEmployeesCount) }}</span>
        </button>
        <button type="button" class="filter-chip border-0 {{ $activePreset === 'with_user' ? 'active' : '' }}" data-filter="with_user">
            <i class="mdi mdi-check-decagram text-success" style="font-size: 13px;"></i>
            <span>With User Account</span>
            <span class="chip-count">{{ number_format($withUserCount) }}</span>
        </button>
        <button type="button" class="filter-chip border-0 {{ $activePreset === 'without_user' ? 'active' : '' }}" data-filter="without_user">
            <i class="mdi mdi-account-cancel-outline text-warning" style="font-size: 13px;"></i>
            <span>Entity Only (0..1)</span>
            <span class="chip-count">{{ number_format($withoutUserCount) }}</span>
        </button>
        <button type="button" class="filter-chip border-0 {{ $activePreset === 'permanent' ? 'active' : '' }}" data-filter="permanent">
            <i class="mdi mdi-shield-check-outline" style="font-size: 13px;"></i>
            <span>Permanent</span>
        </button>
        <button type="button" class="filter-chip border-0 {{ $activePreset === 'inactive' ? 'active' : '' }}" data-filter="inactive">
            <span class="status-dot status-dot-leave"></span>
            <span>On Leave / Inactive</span>
            <span class="chip-count">{{ number_format($inactiveEmployeesCount) }}</span>
        </button>
    </div>

    <!-- Filter Toolbar Card -->
    <div class="dss-card p-3 mb-4">
        <form id="employeeFilterForm" class="row g-2 align-items-center" onsubmit="return false;">
            <div class="col-12 col-md-3">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0"><i class="mdi mdi-magnify text-muted"></i></span>
                    <input type="text" id="filterSearchInput" class="form-control border-start-0" placeholder="Search name, ID, email, title..." value="{{ request('search') }}">
                </div>
            </div>

            <div class="col-6 col-md-2">
                <select id="filterCampusSelect" class="form-select form-select-sm" data-no-select2="true">
                    <option value="">All Campuses</option>
                    @foreach($campuses as $campus)
                        <option value="{{ $campus->id }}" {{ request('campus_id') == $campus->id ? 'selected' : '' }}>
                            {{ $campus->campus_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-6 col-md-2">
                <select id="filterDeptSelect" class="form-select form-select-sm" data-no-select2="true">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->unit_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-6 col-md-2">
                <select id="filterTypeSelect" class="form-select form-select-sm" data-no-select2="true">
                    <option value="">All Employment Types</option>
                    <option value="Permanent" {{ request('employment_type') == 'Permanent' ? 'selected' : '' }}>Permanent</option>
                    <option value="Contractual" {{ request('employment_type') == 'Contractual' ? 'selected' : '' }}>Contractual</option>
                    <option value="Temporary" {{ request('employment_type') == 'Temporary' ? 'selected' : '' }}>Temporary</option>
                    <option value="Part-time" {{ request('employment_type') == 'Part-time' ? 'selected' : '' }}>Part-time</option>
                    <option value="Casual" {{ request('employment_type') == 'Casual' ? 'selected' : '' }}>Casual</option>
                    <option value="Job Order" {{ request('employment_type') == 'Job Order' ? 'selected' : '' }}>Job Order</option>
                </select>
            </div>

            <div class="col-6 col-md-2">
                <select id="filterStatusSelect" class="form-select form-select-sm" data-no-select2="true">
                    <option value="">All Statuses</option>
                    <option value="Active" {{ request('employment_status') == 'Active' ? 'selected' : '' }}>Active</option>
                    <option value="On Leave" {{ request('employment_status') == 'On Leave' ? 'selected' : '' }}>On Leave</option>
                    <option value="Inactive" {{ request('employment_status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <div class="col-12 col-md-1 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-primary w-100 fw-semibold">Filter</button>
                <button type="button" id="resetFiltersBtn" class="btn btn-sm btn-light border" title="Reset Filters"><i class="mdi mdi-refresh"></i></button>
            </div>
        </form>
    </div>

    <!-- Employees DataTable Card -->
    <div class="dss-card p-4">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div>
                <h6 class="fw-bold text-dark mb-0">Institutional Personnel Records</h6>
                <div class="extra-small text-muted">Click on an employee's name or view button to open their complete 360° Profile Dossier.</div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-light text-dark border font-mono px-2 py-1" id="employeesCountBadge">
                    Loading...
                </span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle w-100" id="employeesTable" style="font-size: 13px;">
                <thead class="table-light">
                    <tr>
                        <th style="width: 145px;">Actions</th>
                        <th>Employee No. &amp; Name</th>
                        <th>Designation &amp; Status</th>
                        <th>Campus &amp; Office/Unit</th>
                        <th>Contact Details</th>
                        <th>System Account (0..1)</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

@include('dss.settings.employees.partials.generate-user-modal')
@endsection

@section('scripts')
<script src="{{ asset('assets/libs/datatables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables/js/dataTables.bootstrap5.min.js') }}"></script>
<script>
    let employeesDataTable = null;
    let activeQuickFilter = "{{ request('filter', '') }}";

    $(document).ready(function() {
        if (!$.fn.DataTable.isDataTable('#employeesTable')) {
            employeesDataTable = $('#employeesTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                autoWidth: false,
                pageLength: 15,
                lengthMenu: [[10, 15, 25, 50, 100], [10, 15, 25, 50, 100]],
                order: [[1, 'asc']],
                dom: "<'row'<'col-12'tr>><'d-flex flex-column flex-md-row align-items-center justify-content-between gap-3 pt-3 px-2 border-top'<'d-flex align-items-center gap-3'l i><'d-flex justify-content-center justify-content-md-end'p>>",
                ajax: {
                    url: "{{ route('dss.settings.employees.data') }}",
                    type: "GET",
                    data: function(d) {
                        d.search = $('#filterSearchInput').val() || '';
                        d.campus_id = $('#filterCampusSelect').val() || '';
                        d.department_id = $('#filterDeptSelect').val() || '';
                        d.employment_type = $('#filterTypeSelect').val() || '';
                        d.employment_status = $('#filterStatusSelect').val() || '';
                        d.filter = activeQuickFilter || '';
                    }
                },
                columns: [
                    { data: 'actions', name: 'actions', orderable: false, searchable: false, width: '145px' },
                    { data: 'name_info', name: 'last_name', width: '22%' },
                    { data: 'designation_status', name: 'designation', width: '20%' },
                    { data: 'campus_unit', name: 'department_id', width: '20%' },
                    { data: 'contact', name: 'email', width: '18%' },
                    { data: 'account', name: 'account', orderable: false, searchable: false, width: '18%' }
                ],
                language: {
                    processing: '<div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div> Loading records...',
                    search: "Quick Search:",
                    searchPlaceholder: "Filter records...",
                    lengthMenu: "Show _MENU_ employees",
                    info: "Showing _START_ to _END_ of _TOTAL_ employees",
                    infoEmpty: "No employee master records available",
                    infoFiltered: "(filtered from _MAX_ total records)",
                    emptyTable: `
                        <div class="py-5 text-center text-muted">
                            <i class="mdi mdi-badge-account-alert-outline fs-1 text-muted d-block mb-2"></i>
                            <div class="fw-semibold fs-6 text-dark">No employee master records found.</div>
                            <div class="extra-small text-muted mt-1">Get started by clicking <strong>Add New Employee</strong> to register personnel.</div>
                        </div>
                    `,
                    zeroRecords: `
                        <div class="py-4 text-center text-muted">
                            <i class="mdi mdi-magnify-close fs-2 text-muted d-block mb-1"></i>
                            <div class="fw-semibold text-dark">No matching employees found</div>
                            <div class="extra-small text-muted mt-0.5">Try adjusting your search query or clear the active filters.</div>
                        </div>
                    `
                },
                drawCallback: function(settings) {
                    var api = this.api();
                    var json = api.ajax.json();
                    if (json && json.recordsFiltered !== undefined) {
                        $('#employeesCountBadge').text(json.recordsFiltered.toLocaleString() + ' record' + (json.recordsFiltered === 1 ? '' : 's'));
                    }
                }
            });
        }

        // Quick Filter Chips Click Handler
        $('.filter-chip[data-filter]').on('click', function(e) {
            e.preventDefault();
            $('.filter-chip').removeClass('active');
            $(this).addClass('active');
            activeQuickFilter = $(this).data('filter');
            employeesDataTable.ajax.reload();
        });

        // Search Input with Debounce
        let searchDebounce = null;
        $('#filterSearchInput').on('input', function() {
            clearTimeout(searchDebounce);
            searchDebounce = setTimeout(function() {
                employeesDataTable.ajax.reload();
            }, 300);
        });

        // Dropdowns Change Handler
        $('#filterCampusSelect, #filterDeptSelect, #filterTypeSelect, #filterStatusSelect').on('change', function() {
            employeesDataTable.ajax.reload();
        });

        // Filter Form Submit
        $('#employeeFilterForm').on('submit', function(e) {
            e.preventDefault();
            employeesDataTable.ajax.reload();
        });

        // Reset Filters Button
        $('#resetFiltersBtn').on('click', function(e) {
            e.preventDefault();
            $('#filterSearchInput').val('');
            $('#filterCampusSelect').val('');
            $('#filterDeptSelect').val('');
            $('#filterTypeSelect').val('');
            $('#filterStatusSelect').val('');
            activeQuickFilter = '';
            $('.filter-chip').removeClass('active');
            $('.filter-chip[data-filter=""]').addClass('active');
            employeesDataTable.ajax.reload();
        });
    });

    function quickToggleStatus(empId, newStatus) {
        fetch('/dss/settings/employees/' + empId + '/toggle-status', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ status: newStatus })
        })
        .then(res => res.json())
        .then(data => {
            if (data.result) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: data.message,
                    showConfirmButton: false,
                    timer: 2500
                });
                if (employeesDataTable) {
                    employeesDataTable.ajax.reload(null, false);
                }
            } else {
                Swal.fire({ icon: 'error', title: 'Failed', text: data.message || 'Status update failed.' });
            }
        })
        .catch(err => {
            console.error(err);
            Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to update status.' });
        });
    }

    function revokeUserAccess(empId, empName, userEmail) {
        Swal.fire({
            title: 'Unlink User Account?',
            html: `Are you sure you want to unlink and deactivate the system login account (<strong>${userEmail}</strong>) for <strong>${empName}</strong>?<br><br><small class="text-muted">The employee will remain in the Institutional Master Directory as an entity (0..1), but will no longer be able to log in.</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f59e0b',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, Unlink Access',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('/dss/settings/employees/' + empId + '/revoke-user', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.result) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Access Revoked',
                            text: data.message,
                            confirmButtonColor: '#0f172a'
                        });
                        if (employeesDataTable) {
                            employeesDataTable.ajax.reload(null, false);
                        }
                    } else {
                        Swal.fire({ icon: 'error', title: 'Failed', text: data.message });
                    }
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Error revoking user access.' });
                });
            }
        });
    }

    function confirmDeleteEmployee(empId, empName, hasUser) {
        let warning = `Are you sure you want to archive <strong>${empName}</strong> from the Master Institutional Directory?`;
        if (hasUser) {
            warning = `<strong>${empName}</strong> has an active linked system user account.<br><br>Archiving will also detach and deactivate their login credentials. Continue?`;
        }

        Swal.fire({
            title: 'Archive Personnel Record?',
            html: warning,
            icon: hasUser ? 'warning' : 'question',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, Archive Record',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('/dss/settings/employees/' + empId, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ force_detach_user: 1 })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.result) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Archived',
                            text: data.message,
                            confirmButtonColor: '#0f172a'
                        });
                        if (employeesDataTable) {
                            employeesDataTable.ajax.reload(null, false);
                        }
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error', text: data.message });
                    }
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Error archiving employee record.' });
                });
            }
        });
    }
</script>
@endsection

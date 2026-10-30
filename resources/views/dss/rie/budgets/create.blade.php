@extends('layouts.dss.app')

@section('title', 'Initialize Annual Extension Budget — SNSU RIE')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0" style="font-size: 12px;">
                <li class="breadcrumb-item"><a href="{{ route('dss.dashboard') }}" class="text-decoration-none text-muted">RIE Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('dss.rie.budgets.index') }}" class="text-decoration-none text-muted">Annual Budgets</a></li>
                <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Initialize Annual Budget</li>
            </ol>
        </nav>
        <a href="{{ route('dss.rie.budgets.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="mdi mdi-arrow-left me-1"></i>Back to Budgets
        </a>
    </div>

    <div class="dss-card p-4 p-md-5 mb-4">
        <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
            <div class="rounded-3 bg-success-subtle p-3 text-success">
                <i class="mdi mdi-cash-plus fs-2"></i>
            </div>
            <div>
                <h4 class="fw-bold text-dark mb-1">Initialize Annual Extension Budget Appropriation</h4>
                <div class="text-muted extra-small">
                    Configure institutional appropriations, fund clusters, and target expense categories for Surigao del Norte State University.
                </div>
            </div>
        </div>

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show extra-small" role="alert">
                <i class="mdi mdi-alert-circle me-1"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('dss.rie.budgets.store') }}" method="POST" id="budgetForm">
            @csrf

            <div class="row g-4 mb-4">
                <div class="col-12 col-md-3">
                    <label class="form-label fw-bold extra-small text-uppercase tracking-wider text-muted">Fiscal Year <span class="text-danger">*</span></label>
                    <input type="number" name="fiscal_year" class="form-control font-mono fw-bold" value="{{ old('fiscal_year', $defaultYear) }}" min="2020" max="2040" required>
                    <div class="form-text extra-small">Academic &amp; government budget cycle year.</div>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-bold extra-small text-uppercase tracking-wider text-muted">Budget Code <span class="text-danger">*</span></label>
                    <input type="text" name="budget_code" class="form-control font-mono fw-bold text-uppercase" value="{{ old('budget_code', $suggestedCode) }}" required>
                    <div class="form-text extra-small">Unique tracking identifier for auditing.</div>
                </div>

                <div class="col-12 col-md-5">
                    <label class="form-label fw-bold extra-small text-uppercase tracking-wider text-muted">Campus Jurisdiction</label>
                    <select name="campus_id" class="form-select">
                        <option value="">University-Wide Consolidated (Main Campus)</option>
                        @foreach($campuses as $campus)
                            <option value="{{ $campus->id }}" {{ old('campus_id') == $campus->id ? 'selected' : '' }}>
                                {{ $campus->campus_name }} ({{ $campus->campus_code }})
                            </option>
                        @endforeach
                    </select>
                    <div class="form-text extra-small">Leave blank for all-campus university appropriation.</div>
                </div>

                <div class="col-12 col-md-7">
                    <label class="form-label fw-bold extra-small text-uppercase tracking-wider text-muted">Budget Title / Description <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" value="{{ old('title', 'FY ' . $defaultYear . ' Annual Extension Operational & Community Program Budget') }}" required>
                    <div class="form-text extra-small">Official label in executive reports and BOR submissions.</div>
                </div>

                <div class="col-12 col-md-5">
                    <label class="form-label fw-bold extra-small text-uppercase tracking-wider text-muted">Fund Cluster <span class="text-danger">*</span></label>
                    <select name="fund_cluster" class="form-select" required>
                        <option value="01 - Regular Agency Fund (GAA)" {{ old('fund_cluster') === '01 - Regular Agency Fund (GAA)' ? 'selected' : '' }}>01 - Regular Agency Fund (GAA)</option>
                        <option value="05 - Internally Generated Income (STF / Fiduciary)" {{ old('fund_cluster') === '05 - Internally Generated Income (STF / Fiduciary)' ? 'selected' : '' }}>05 - Internally Generated Income (STF / Fiduciary)</option>
                        <option value="06 - Business Related Funds (IGP)" {{ old('fund_cluster') === '06 - Business Related Funds (IGP)' ? 'selected' : '' }}>06 - Business Related Funds (IGP)</option>
                        <option value="07 - Trust Receipts / External Grants" {{ old('fund_cluster') === '07 - Trust Receipts / External Grants' ? 'selected' : '' }}>07 - Trust Receipts / External Grants</option>
                        <option value="Unified Multi-Cluster Institutional Appropriation" {{ old('fund_cluster') === 'Unified Multi-Cluster Institutional Appropriation' ? 'selected' : '' }}>Unified Multi-Cluster Institutional Appropriation</option>
                    </select>
                </div>
            </div>

            <div class="p-4 bg-light rounded-4 border mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="fw-bold text-dark mb-0">Fund Source Breakdown</h6>
                        <div class="extra-small text-muted">Enter specific appropriations per funding window. Live total computes automatically.</div>
                    </div>
                    <div class="text-end">
                        <div class="extra-small text-muted font-mono text-uppercase">Total Approved Budget</div>
                        <div class="fs-3 fw-bold text-success font-mono" id="liveTotalDisplay">₱0.00</div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-12 col-md-4">
                        <label class="form-label extra-small fw-bold text-muted text-uppercase">
                            1. GAA (General Appropriations Act)
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-white font-mono">₱</span>
                            <input type="number" step="0.01" min="0" name="allocated_gaa" id="inputGaa"
                                   class="form-control font-mono fw-semibold calc-trigger"
                                   value="{{ old('allocated_gaa', '2500000.00') }}" placeholder="0.00">
                        </div>
                        <div class="extra-small text-muted mt-1">National government regular subsidy.</div>
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label extra-small fw-bold text-muted text-uppercase">
                            2. STF (Special Trust Fund / Fiduciary Income)
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-white font-mono">₱</span>
                            <input type="number" step="0.01" min="0" name="allocated_stf" id="inputStf"
                                   class="form-control font-mono fw-semibold calc-trigger"
                                   value="{{ old('allocated_stf', '1000000.00') }}" placeholder="0.00">
                        </div>
                        <div class="extra-small text-muted mt-1">Internally generated university revenues.</div>
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label extra-small fw-bold text-muted text-uppercase">
                            3. External Grants / Partner Counterpart
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-white font-mono">₱</span>
                            <input type="number" step="0.01" min="0" name="allocated_external" id="inputExt"
                                   class="form-control font-mono fw-semibold calc-trigger"
                                   value="{{ old('allocated_external', '500000.00') }}" placeholder="0.00">
                        </div>
                        <div class="extra-small text-muted mt-1">LGU, CHED, DOST, or NGO co-funding.</div>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-12 col-md-4">
                    <label class="form-label fw-bold extra-small text-uppercase tracking-wider text-muted">Board Resolution No.</label>
                    <input type="text" name="board_resolution_no" class="form-control" value="{{ old('board_resolution_no') }}" placeholder="e.g. BOR Resolution No. 14, Series of 2026">
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-bold extra-small text-uppercase tracking-wider text-muted">Approving Authority</label>
                    <input type="text" name="approved_by" class="form-control" value="{{ old('approved_by', 'University President / Board of Regents') }}" placeholder="Official or Governing Body">
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-bold extra-small text-uppercase tracking-wider text-muted">Approval Date</label>
                    <input type="date" name="approved_at" class="form-control" value="{{ old('approved_at', date('Y-m-d')) }}">
                </div>

                <div class="col-12">
                    <label class="form-label fw-bold extra-small text-uppercase tracking-wider text-muted">Administrative Notes &amp; Guidelines</label>
                    <textarea name="notes" class="form-control" rows="2" placeholder="Specific spending caps, target banner programs, or compliance instructions for project leaders...">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="border rounded-4 p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="fw-bold text-dark mb-0">Initial Expense Category Allocations (Optional)</h6>
                        <div class="extra-small text-muted">Sub-allocate the total budget into standard institutional extension activities.</div>
                    </div>
                    <button type="button" class="btn btn-xs btn-outline-secondary" onclick="addCategoryRow()">
                        <i class="mdi mdi-plus me-1"></i>Add Category Row
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0" id="categoriesTable" style="font-size: 13px;">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 45%;">Category Name</th>
                                <th style="width: 25%;">Target Allocation (₱)</th>
                                <th style="width: 25%;">Remarks</th>
                                <th style="width: 5%;" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="categoriesBody">
                            @foreach($standardCategories as $idx => $catName)
                                <tr>
                                    <td>
                                        <input type="text" name="categories[{{ $idx }}][name]" class="form-control form-control-sm" value="{{ $catName }}">
                                    </td>
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text font-mono">₱</span>
                                            <input type="number" step="0.01" min="0" name="categories[{{ $idx }}][amount]" class="form-control font-mono" placeholder="0.00">
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" name="categories[{{ $idx }}][remarks]" class="form-control form-control-sm" placeholder="e.g. 10 approved proposals cap">
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-xs btn-outline-danger" onclick="this.closest('tr').remove()">
                                            <i class="mdi mdi-delete-outline"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="d-flex align-items-center justify-content-end gap-2 pt-3 border-top">
                <a href="{{ route('dss.rie.budgets.index') }}" class="btn btn-outline-secondary px-4">
                    Cancel
                </a>
                <button type="submit" class="btn btn-dss-primary px-4 fw-bold shadow-2xs">
                    <i class="mdi mdi-check-circle-outline me-1"></i>Save &amp; Initialize Budget
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function calculateTotal() {
        const gaa = parseFloat(document.getElementById('inputGaa').value) || 0;
        const stf = parseFloat(document.getElementById('inputStf').value) || 0;
        const ext = parseFloat(document.getElementById('inputExt').value) || 0;
        const total = gaa + stf + ext;

        document.getElementById('liveTotalDisplay').innerText = '₱' + total.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    document.querySelectorAll('.calc-trigger').forEach(input => {
        input.addEventListener('input', calculateTotal);
    });
    calculateTotal();

    let catRowIndex = {{ count($standardCategories) }};
    function addCategoryRow() {
        const tbody = document.getElementById('categoriesBody');
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>
                <input type="text" name="categories[${catRowIndex}][name]" class="form-control form-control-sm" placeholder="Category Name" required>
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <span class="input-group-text font-mono">₱</span>
                    <input type="number" step="0.01" min="0" name="categories[${catRowIndex}][amount]" class="form-control font-mono" placeholder="0.00" required>
                </div>
            </td>
            <td>
                <input type="text" name="categories[${catRowIndex}][remarks]" class="form-control form-control-sm" placeholder="Remarks">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-xs btn-outline-danger" onclick="this.closest('tr').remove()">
                    <i class="mdi mdi-delete-outline"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
        catRowIndex++;
    }
</script>
@endsection

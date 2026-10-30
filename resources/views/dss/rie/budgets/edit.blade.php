@extends('layouts.dss.app')

@section('title', "Edit {$budget->budget_code} — Annual Budget Configuration")

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0" style="font-size: 12px;">
                <li class="breadcrumb-item"><a href="{{ route('dss.dashboard') }}" class="text-decoration-none text-muted">RIE Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('dss.rie.budgets.index', ['fy' => $budget->fiscal_year]) }}" class="text-decoration-none text-muted">Annual Budgets</a></li>
                <li class="breadcrumb-item"><a href="{{ route('dss.rie.budgets.show', $budget->getRouteKey()) }}" class="text-decoration-none text-muted">{{ $budget->budget_code }}</a></li>
                <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Edit Configuration</li>
            </ol>
        </nav>
        <a href="{{ route('dss.rie.budgets.show', $budget->getRouteKey()) }}" class="btn btn-sm btn-outline-secondary">
            <i class="mdi mdi-arrow-left me-1"></i>Back to Workbench
        </a>
    </div>

    <div class="dss-card p-4 p-md-5 mb-4">
        <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
            <div class="rounded-3 bg-primary-subtle p-3 text-primary">
                <i class="mdi mdi-pencil-ruler fs-2"></i>
            </div>
            <div>
                <h4 class="fw-bold text-dark mb-1">Edit Budget Configuration: {{ $budget->budget_code }}</h4>
                <div class="text-muted extra-small">
                    Update administrative details, fund allocations, board resolution basis, and active lifecycle status.
                </div>
            </div>
        </div>

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show extra-small" role="alert">
                <i class="mdi mdi-alert-circle me-1"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('dss.rie.budgets.update', $budget->getRouteKey()) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-4 mb-4">
                <div class="col-12 col-md-3">
                    <label class="form-label fw-bold extra-small text-uppercase tracking-wider text-muted">Fiscal Year</label>
                    <input type="text" class="form-control font-mono fw-bold bg-light" value="FY {{ $budget->fiscal_year }}" readonly>
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label fw-bold extra-small text-uppercase tracking-wider text-muted">Budget Code</label>
                    <input type="text" class="form-control font-mono fw-bold bg-light text-uppercase" value="{{ $budget->budget_code }}" readonly>
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label fw-bold extra-small text-uppercase tracking-wider text-muted">Lifecycle Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" required>
                        <option value="active" {{ old('status', $budget->status) === 'active' ? 'selected' : '' }}>Active (Accepting Charges)</option>
                        <option value="closed" {{ old('status', $budget->status) === 'closed' ? 'selected' : '' }}>Closed (FY Concluded / Audited)</option>
                        <option value="draft" {{ old('status', $budget->status) === 'draft' ? 'selected' : '' }}>Draft (Pending Approval)</option>
                    </select>
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label fw-bold extra-small text-uppercase tracking-wider text-muted">Campus Jurisdiction</label>
                    <select name="campus_id" class="form-select">
                        <option value="">University-Wide Consolidated (Main Campus)</option>
                        @foreach($campuses as $campus)
                            <option value="{{ $campus->id }}" {{ old('campus_id', $budget->campus_id) == $campus->id ? 'selected' : '' }}>
                                {{ $campus->campus_name }} ({{ $campus->campus_code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-8">
                    <label class="form-label fw-bold extra-small text-uppercase tracking-wider text-muted">Budget Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" value="{{ old('title', $budget->title) }}" required>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-bold extra-small text-uppercase tracking-wider text-muted">Fund Cluster <span class="text-danger">*</span></label>
                    <select name="fund_cluster" class="form-select" required>
                        <option value="01 - Regular Agency Fund (GAA)" {{ old('fund_cluster', $budget->fund_cluster) === '01 - Regular Agency Fund (GAA)' ? 'selected' : '' }}>01 - Regular Agency Fund (GAA)</option>
                        <option value="05 - Internally Generated Income (STF / Fiduciary)" {{ old('fund_cluster', $budget->fund_cluster) === '05 - Internally Generated Income (STF / Fiduciary)' ? 'selected' : '' }}>05 - Internally Generated Income (STF / Fiduciary)</option>
                        <option value="06 - Business Related Funds (IGP)" {{ old('fund_cluster', $budget->fund_cluster) === '06 - Business Related Funds (IGP)' ? 'selected' : '' }}>06 - Business Related Funds (IGP)</option>
                        <option value="07 - Trust Receipts / External Grants" {{ old('fund_cluster', $budget->fund_cluster) === '07 - Trust Receipts / External Grants' ? 'selected' : '' }}>07 - Trust Receipts / External Grants</option>
                        <option value="Unified Multi-Cluster Institutional Appropriation" {{ old('fund_cluster', $budget->fund_cluster) === 'Unified Multi-Cluster Institutional Appropriation' ? 'selected' : '' }}>Unified Multi-Cluster Institutional Appropriation</option>
                    </select>
                </div>
            </div>

            <div class="p-4 bg-light rounded-4 border mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="fw-bold text-dark mb-0">Base Fund Source Appropriations</h6>
                        <div class="extra-small text-muted">Adjust base funding windows. Realignment transactions (₱{{ number_format($budget->realigned_amount, 2) }}) remain intact.</div>
                    </div>
                    <div class="text-end">
                        <div class="extra-small text-muted font-mono text-uppercase">New Base Total</div>
                        <div class="fs-4 fw-bold text-success font-mono" id="liveEditTotalDisplay">₱0.00</div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-12 col-md-4">
                        <label class="form-label extra-small fw-bold text-muted text-uppercase">GAA Share</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white font-mono">₱</span>
                            <input type="number" step="0.01" min="0" name="allocated_gaa" id="editGaa"
                                   class="form-control font-mono fw-semibold edit-calc-trigger"
                                   value="{{ old('allocated_gaa', $budget->allocated_gaa) }}">
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label extra-small fw-bold text-muted text-uppercase">STF / Income Share</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white font-mono">₱</span>
                            <input type="number" step="0.01" min="0" name="allocated_stf" id="editStf"
                                   class="form-control font-mono fw-semibold edit-calc-trigger"
                                   value="{{ old('allocated_stf', $budget->allocated_stf) }}">
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label extra-small fw-bold text-muted text-uppercase">External Grants / Co-Funding</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white font-mono">₱</span>
                            <input type="number" step="0.01" min="0" name="allocated_external" id="editExt"
                                   class="form-control font-mono fw-semibold edit-calc-trigger"
                                   value="{{ old('allocated_external', $budget->allocated_external) }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-12 col-md-4">
                    <label class="form-label fw-bold extra-small text-uppercase tracking-wider text-muted">Board Resolution No.</label>
                    <input type="text" name="board_resolution_no" class="form-control" value="{{ old('board_resolution_no', $budget->board_resolution_no) }}">
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-bold extra-small text-uppercase tracking-wider text-muted">Approving Authority</label>
                    <input type="text" name="approved_by" class="form-control" value="{{ old('approved_by', $budget->approved_by) }}">
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-bold extra-small text-uppercase tracking-wider text-muted">Approval Date</label>
                    <input type="date" name="approved_at" class="form-control" value="{{ old('approved_at', $budget->approved_at?->format('Y-m-d')) }}">
                </div>

                <div class="col-12">
                    <label class="form-label fw-bold extra-small text-uppercase tracking-wider text-muted">Administrative Notes &amp; Spending Caps</label>
                    <textarea name="notes" class="form-control" rows="3">{{ old('notes', $budget->notes) }}</textarea>
                </div>
            </div>

            <div class="d-flex align-items-center justify-content-end gap-2 pt-3 border-top">
                <a href="{{ route('dss.rie.budgets.show', $budget->getRouteKey()) }}" class="btn btn-outline-secondary px-4">
                    Cancel
                </a>
                <button type="submit" class="btn btn-dss-primary px-4 fw-bold shadow-2xs">
                    <i class="mdi mdi-content-save-outline me-1"></i>Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function calculateEditTotal() {
        const gaa = parseFloat(document.getElementById('editGaa').value) || 0;
        const stf = parseFloat(document.getElementById('editStf').value) || 0;
        const ext = parseFloat(document.getElementById('editExt').value) || 0;
        const total = gaa + stf + ext;

        document.getElementById('liveEditTotalDisplay').innerText = '₱' + total.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    document.querySelectorAll('.edit-calc-trigger').forEach(input => {
        input.addEventListener('input', calculateEditTotal);
    });
    calculateEditTotal();
</script>
@endsection

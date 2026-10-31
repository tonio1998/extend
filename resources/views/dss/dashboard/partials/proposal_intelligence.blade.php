@php
    $bannerList = $bannerProgramMonitoring ?? [];
    $bannerCoveredCount = $bannerCoveredCount ?? collect($bannerList)->filter(fn ($b) => ($b['proposals_count'] ?? 0) > 0)->count();
    $bannerUncoveredCount = $bannerUncoveredCount ?? max(0, count($bannerList) - $bannerCoveredCount);
    $agendaList = $agendaMonitoring ?? [];
    $outputsList = $expectedOutputsMonitoring ?? [];
    $gad = $gadMonitoring ?? [
        'male_beneficiaries' => 0,
        'female_beneficiaries' => 0,
        'unspecified_beneficiaries' => 0,
        'direct_beneficiaries_total' => 0,
        'indirect_beneficiaries_total' => 0,
        'beneficiary_groups_count' => 0,
        'beneficiary_groups' => [],
        'male_leaders_count' => 0,
        'female_leaders_count' => 0,
        'unspecified_leaders_count' => 0,
    ];
    $collegesList = $collegesMonitoring ?? [];
    $interCollegeCount = $interCollegeProposalsCount ?? 0;
    $partnersList = $partnerAgenciesMonitoring ?? [];
    $fundingStruct = $fundingStructureMonitoring ?? [
        'with_cash_count' => 0,
        'in_kind_count' => 0,
        'total_cash_budget' => 0.0,
        'with_partner_share_count' => 0,
        'approved_budget_total' => 0.0,
    ];

    $genderedBenTotal = max(1, (int) $gad['male_beneficiaries'] + (int) $gad['female_beneficiaries']);
    $maleBenPct = ((int) $gad['male_beneficiaries'] + (int) $gad['female_beneficiaries']) > 0
        ? round(((int) $gad['male_beneficiaries'] / $genderedBenTotal) * 100, 1)
        : 50;
    $femaleBenPct = ((int) $gad['male_beneficiaries'] + (int) $gad['female_beneficiaries']) > 0
        ? round(100 - $maleBenPct, 1)
        : 50;

    $genderedLeadersTotal = max(1, (int) $gad['male_leaders_count'] + (int) $gad['female_leaders_count']);
    $maleLeaderPct = ((int) $gad['male_leaders_count'] + (int) $gad['female_leaders_count']) > 0
        ? round(((int) $gad['male_leaders_count'] / $genderedLeadersTotal) * 100, 1)
        : 50;
    $femaleLeaderPct = ((int) $gad['male_leaders_count'] + (int) $gad['female_leaders_count']) > 0
        ? round(100 - $maleLeaderPct, 1)
        : 50;

    $totalOutputTargets = collect($outputsList)->sum('target_total');
    $agendaColors = ['#2563eb', '#059669', '#d97706', '#7c3aed', '#db2777', '#0891b2', '#4f46e5'];
@endphp

<style>
    .pi-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    }
    .pi-banner-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(185px, 1fr));
        gap: 8px;
    }
    .pi-banner-tile {
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 10px 12px;
        background: #f8fafc;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .pi-banner-tile:hover {
        border-color: #94a3b8;
        background: #ffffff;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.07);
    }
    .pi-banner-tile.is-active-banner {
        background: #f0fdf4;
        border-color: #86efac;
    }
    .pi-output-tile {
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 12px;
        background: #ffffff;
        transition: all 0.15s ease;
    }
    .pi-output-tile:hover {
        border-color: #cbd5e1;
        box-shadow: 0 3px 10px rgba(15, 23, 42, 0.05);
    }
</style>

{{-- ============================================================================
     ROW A: BANNER PROGRAMS MONITORING & EXTENSION AGENDA PRIORITIES
     ============================================================================ --}}
<div class="row g-3.5 mb-4">
    {{-- Left: Institutional Banner Programs Monitoring --}}
    <div class="col-xl-7">
        <div class="pi-card p-3.5 p-md-4 h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex align-items-start align-items-sm-center justify-content-between flex-wrap gap-2 mb-3 pb-2.5 border-bottom">
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill extra-small fw-bold px-2.5 py-1">
                                <i class="mdi mdi-flag-variant-outline me-1"></i>PART I-B &bull; BANNER PROGRAMS
                            </span>
                            <h6 class="fw-bold text-dark m-0" style="font-size: 15px;">Institutional Banner Programs Monitoring</h6>
                        </div>
                        <p class="text-muted extra-small mb-0 mt-1">
                            Tracks extension proposals tagged per university banner program ({{ $periodLabel ?? 'All-Time' }}). Click any banner program to inspect proposals.
                        </p>
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <button type="button" class="sdg-filter-btn active" onclick="window.filterDashboardBanners('all', this)">
                            All ({{ count($bannerList) }})
                        </button>
                        <button type="button" class="sdg-filter-btn" onclick="window.filterDashboardBanners('tagged', this)">
                            Active ({{ $bannerCoveredCount }})
                        </button>
                        <button type="button" class="sdg-filter-btn" onclick="window.filterDashboardBanners('untagged', this)">
                            0 Tagged ({{ $bannerUncoveredCount }})
                        </button>
                    </div>
                </div>

                <div class="pi-banner-grid" id="dashboardBannerGrid">
                    @foreach($bannerList as $bKey => $bItem)
                        @php
                            $bCount = (int) ($bItem['proposals_count'] ?? 0);
                            $bApproved = (int) ($bItem['approved_count'] ?? 0);
                            $bBudget = (float) ($bItem['total_budget'] ?? 0);
                            $hasB = $bCount > 0;
                        @endphp
                        <div class="pi-banner-tile {{ $hasB ? 'is-active-banner' : 'opacity-75' }}"
                             data-banner-tagged="{{ $hasB ? '1' : '0' }}"
                             onclick="window.openCategoryDrilldownModal('Banner Program', @js($bItem['name']), @js($bItem['description'] ?? ''), {{ $bCount }}, {{ $bApproved }}, {{ $bBudget }}, @js($bItem['proposals'] ?? []))">
                            <div class="d-flex align-items-start justify-content-between gap-2 mb-1">
                                <span class="fw-extrabold text-dark text-truncate" style="font-size: 12.5px;" title="{{ $bItem['name'] }}">
                                    {{ $bItem['name'] }}
                                </span>
                                <span class="badge rounded-pill font-mono extra-small {{ $hasB ? 'bg-success text-white' : 'bg-secondary-subtle text-secondary' }}">
                                    {{ $bCount }} {{ Str::plural('Ext', $bCount) }}
                                </span>
                            </div>
                            <div class="text-muted extra-small text-truncate mb-2" title="{{ $bItem['description'] ?? '' }}">
                                {{ $bItem['description'] ?: 'Institutional Banner Program' }}
                            </div>
                            <div class="d-flex align-items-center justify-content-between extra-small pt-1.5 border-top">
                                <span class="text-secondary fw-semibold">
                                    <i class="mdi mdi-check-decagram text-success me-0.5"></i>{{ $bApproved }} Approved
                                </span>
                                <span class="fw-bold text-dark font-mono">
                                    {{ $bBudget > 0 ? '₱'.number_format($bBudget, 0) : 'In-Kind' }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="pt-3 mt-3 border-top d-flex align-items-center justify-content-between flex-wrap gap-2 extra-small text-muted">
                <span>
                    <strong>{{ $bannerCoveredCount }}</strong> of <strong>{{ count($bannerList) }}</strong> Banner Programs have active extension proposals.
                </span>
                @can('rie.proposals.manage')
                    <a href="{{ route('dss.settings.rie_priorities.index') }}" class="text-primary fw-bold text-decoration-none">
                        <i class="mdi mdi-cog-outline me-0.5"></i>Manage Banner Programs &amp; Agendas
                    </a>
                @endcan
            </div>
        </div>
    </div>

    {{-- Right: Extension Agenda Priorities Monitoring --}}
    <div class="col-xl-5">
        <div class="pi-card p-3.5 p-md-4 h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3 pb-2.5 border-bottom">
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill extra-small fw-bold px-2.5 py-1">
                                <i class="mdi mdi-compass-outline me-1"></i>PART I-C &bull; EXTENSION AGENDA
                            </span>
                            <h6 class="fw-bold text-dark m-0" style="font-size: 15px;">Extension Agenda Priorities</h6>
                        </div>
                        <p class="text-muted extra-small mb-0 mt-1">
                            Distribution of extension proposals across priority institutional thrusts.
                        </p>
                    </div>
                </div>

                <div class="d-flex flex-column gap-2.5">
                    @forelse($agendaList as $agName => $agData)
                        @php
                            $agCount = (int) ($agData['proposals_count'] ?? 0);
                            $agPct = (float) ($agData['percentage'] ?? 0);
                            $agApproved = (int) ($agData['approved_count'] ?? 0);
                            $agBudget = (float) ($agData['total_budget'] ?? 0);
                            $barColor = $agendaColors[$loop->index % count($agendaColors)];
                        @endphp
                        <div class="p-2.5 rounded-3 border bg-light bg-opacity-50"
                             style="cursor: pointer;"
                             onclick="window.openCategoryDrilldownModal('Extension Agenda', @js($agData['name']), 'Priority Extension Agenda Thrust', {{ $agCount }}, {{ $agApproved }}, {{ $agBudget }}, @js($agData['proposals'] ?? []))">
                            <div class="d-flex align-items-center justify-content-between gap-2 mb-1">
                                <span class="fw-bold text-dark small text-truncate" title="{{ $agData['name'] }}">
                                    {{ $agData['name'] }}
                                </span>
                                <div class="d-flex align-items-center gap-1.5 flex-shrink-0">
                                    <span class="badge rounded-pill font-mono extra-small" style="background: {{ $barColor }}; color: #fff;">
                                        {{ $agCount }} {{ Str::plural('Ext', $agCount) }}
                                    </span>
                                    <span class="text-muted extra-small font-mono fw-bold">{{ $agPct }}%</span>
                                </div>
                            </div>
                            <div class="progress" style="height: 6px; background: #e2e8f0;">
                                <div class="progress-bar" role="progressbar" style="width: {{ max($agCount > 0 ? 8 : 0, min(100, $agPct)) }}%; background-color: {{ $barColor }};"></div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted extra-small">No extension agendas configured yet.</div>
                    @endforelse
                </div>
            </div>

            <div class="pt-3 mt-3 border-top d-flex align-items-center justify-content-between extra-small text-muted">
                <span>Click any agenda thrust above to view its tagged proposals.</span>
                <span class="fw-bold text-dark font-mono">{{ count($agendaList) }} Thrusts Tracked</span>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================================
     ROW B: 6Ps + 1P EXPECTED OUTPUTS TRACKER & GAD BENEFICIARY / LEADER MONITOR
     ============================================================================ --}}
<div class="row g-3.5 mb-4">
    {{-- Left: 6Ps + 1P Expected Outputs Tracker --}}
    <div class="col-xl-7">
        <div class="pi-card p-3.5 p-md-4 h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3 pb-2.5 border-bottom">
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-indigo-subtle text-primary border border-primary-subtle rounded-pill extra-small fw-bold px-2.5 py-1">
                                <i class="mdi mdi-bullseye-arrow me-1"></i>PART II-C &bull; 6Ps + 1P EXPECTED OUTPUTS
                            </span>
                            <h6 class="fw-bold text-dark m-0" style="font-size: 15px;">Expected Outputs &amp; Deliverables Monitoring</h6>
                        </div>
                        <p class="text-muted extra-small mb-0 mt-1">
                            Aggregated target deliverables across People, Products, Services, Partnerships, Publications, Policies, and Patents/IP.
                        </p>
                    </div>
                    <span class="badge bg-dark text-white rounded-pill px-3 py-1.5 font-mono extra-small">
                        {{ number_format($totalOutputTargets) }} Total Target Outputs
                    </span>
                </div>

                <div class="row g-2.5">
                    @foreach($outputsList as $catKey => $outData)
                        <div class="col-md-4 col-sm-6">
                            <div class="pi-output-tile h-100 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-1.5">
                                        <span class="d-inline-flex align-items-center justify-content-center rounded-3 text-white"
                                              style="width: 30px; height: 30px; background: {{ $outData['color'] }};">
                                            <i class="mdi {{ $outData['icon'] }}" style="font-size: 16px;"></i>
                                        </span>
                                        <span class="badge bg-light text-dark border font-mono extra-small">
                                            {{ $outData['proposals_count'] }} {{ Str::plural('Proposal', $outData['proposals_count']) }}
                                        </span>
                                    </div>
                                    <div class="fs-4 fw-extrabold text-dark font-mono lh-1 mb-1">
                                        {{ number_format($outData['target_total']) }}
                                    </div>
                                    <div class="fw-bold text-dark extra-small">{{ $outData['category'] }}</div>
                                    <div class="text-muted" style="font-size: 10.5px;">{{ $outData['label'] }}</div>
                                </div>
                                @if(!empty($outData['items']))
                                    <div class="mt-2 pt-1.5 border-top">
                                        <div class="text-muted text-truncate" style="font-size: 10px;" title="{{ $outData['items'][0] }}">
                                            <i class="mdi mdi-check-circle-outline text-success me-0.5"></i>{{ $outData['items'][0] }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="pt-3 mt-3 border-top d-flex align-items-center justify-content-between extra-small text-muted">
                <span>Aligned with SUC Levelling, PREXC, and AACCUP Extension Output Indicators.</span>
                <a href="{{ route('dss.rie.proposals.index') }}" class="text-primary fw-bold text-decoration-none">View Proposals <i class="mdi mdi-arrow-right"></i></a>
            </div>
        </div>
    </div>

    {{-- Right: GAD (Gender & Development) Disaggregated Beneficiaries & Leaders --}}
    <div class="col-xl-5">
        <div class="pi-card p-3.5 p-md-4 h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3 pb-2.5 border-bottom">
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill extra-small fw-bold px-2.5 py-1">
                                <i class="mdi mdi-gender-male-female me-1"></i>PART I-E &amp; I-H &bull; GAD MONITOR
                            </span>
                            <h6 class="fw-bold text-dark m-0" style="font-size: 15px;">GAD Beneficiaries &amp; Proponents</h6>
                        </div>
                        <p class="text-muted extra-small mb-0 mt-1">
                            Gender-disaggregated target beneficiaries and project leadership breakdown.
                        </p>
                    </div>
                </div>

                {{-- Beneficiary Totals Strip --}}
                <div class="row g-2 mb-3">
                    <div class="col-4">
                        <div class="p-2.5 rounded-3 border bg-light text-center">
                            <div class="extra-small text-muted fw-semibold">Direct Beneficiaries</div>
                            <div class="fs-5 fw-extrabold text-dark font-mono">{{ number_format($gad['direct_beneficiaries_total'] ?: $totalTargetBeneficiaries) }}</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-2.5 rounded-3 border text-center" style="background: #eff6ff; border-color: #bfdbfe !important;">
                            <div class="extra-small text-primary fw-semibold"><i class="mdi mdi-gender-male"></i> Male</div>
                            <div class="fs-5 fw-extrabold text-primary font-mono">{{ number_format($gad['male_beneficiaries']) }}</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-2.5 rounded-3 border text-center" style="background: #fdf2f8; border-color: #fbcfe8 !important;">
                            <div class="extra-small fw-semibold" style="color: #db2777;"><i class="mdi mdi-gender-female"></i> Female</div>
                            <div class="fs-5 fw-extrabold font-mono" style="color: #db2777;">{{ number_format($gad['female_beneficiaries']) }}</div>
                        </div>
                    </div>
                </div>

                {{-- Beneficiary Gender Ratio Bar --}}
                <div class="mb-3">
                    <div class="d-flex align-items-center justify-content-between extra-small fw-bold mb-1">
                        <span class="text-primary">Male Beneficiaries ({{ $maleBenPct }}%)</span>
                        <span style="color: #db2777;">Female Beneficiaries ({{ $femaleBenPct }}%)</span>
                    </div>
                    <div class="progress rounded-pill overflow-hidden" style="height: 8px;">
                        <div class="progress-bar" style="width: {{ $maleBenPct }}%; background-color: #3b82f6;"></div>
                        <div class="progress-bar" style="width: {{ $femaleBenPct }}%; background-color: #ec4899;"></div>
                    </div>
                    @if($gad['indirect_beneficiaries_total'] > 0)
                        <div class="text-muted extra-small mt-1">
                            <i class="mdi mdi-account-multiple-plus-outline me-1"></i>Plus <strong>{{ number_format($gad['indirect_beneficiaries_total']) }}</strong> estimated indirect community beneficiaries.
                        </div>
                    @endif
                </div>

                {{-- Project Leaders Gender Breakdown --}}
                <div class="p-2.5 rounded-3 border bg-light mb-3">
                    <div class="d-flex align-items-center justify-content-between extra-small fw-bold mb-1.5">
                        <span class="text-dark"><i class="mdi mdi-account-tie-outline me-1 text-primary"></i>Project Leaders by Gender</span>
                        <span class="font-mono text-muted">{{ $gad['male_leaders_count'] }} Male &bull; {{ $gad['female_leaders_count'] }} Female</span>
                    </div>
                    <div class="progress rounded-pill overflow-hidden" style="height: 6px;">
                        <div class="progress-bar" style="width: {{ $maleLeaderPct }}%; background-color: #2563eb;"></div>
                        <div class="progress-bar" style="width: {{ $femaleLeaderPct }}%; background-color: #db2777;"></div>
                    </div>
                </div>

                {{-- Partner Beneficiary Groups --}}
                <div>
                    <div class="extra-small fw-bold text-uppercase text-muted mb-1.5">
                        Targeted Beneficiary Groups ({{ count($gad['beneficiary_groups']) }})
                    </div>
                    <div class="d-flex flex-column gap-1.5" style="max-height: 120px; overflow-y: auto;">
                        @forelse(array_slice($gad['beneficiary_groups'], 0, 5) as $bGroup)
                            <div class="d-flex align-items-center justify-content-between gap-2 px-2.5 py-1.5 rounded-2 border bg-white extra-small">
                                <span class="fw-semibold text-dark text-truncate" title="{{ $bGroup['name'] }}">
                                    <i class="mdi mdi-map-marker-radius-outline text-success me-1"></i>{{ $bGroup['name'] }}
                                </span>
                                <span class="badge bg-light text-dark border font-mono flex-shrink-0">
                                    {{ number_format($bGroup['total']) }} pax
                                </span>
                            </div>
                        @empty
                            <div class="text-muted extra-small fst-italic py-2">No beneficiary groups listed in proposals yet.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="pt-3 mt-3 border-top d-flex align-items-center justify-content-between extra-small text-muted">
                <span>Supports PCW &amp; Institutional GAD Attribution Monitoring.</span>
                <span class="fw-bold text-dark font-mono">{{ $gad['beneficiary_groups_count'] }} Sectors</span>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================================
     ROW C: IMPLEMENTING COLLEGES, PARTNER AGENCIES & FUNDING STRUCTURE
     ============================================================================ --}}
<div class="row g-3.5 mb-4">
    {{-- Column 1: Implementing Colleges --}}
    <div class="col-xl-4">
        <div class="pi-card p-3.5 p-md-4 h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex align-items-center justify-content-between gap-2 mb-3 pb-2 border-bottom">
                    <div>
                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill extra-small fw-bold px-2 py-0.5">
                            PART I-F &bull; COLLEGES
                        </span>
                        <h6 class="fw-bold text-dark m-0 mt-1" style="font-size: 14.5px;">Implementing Colleges &amp; Units</h6>
                    </div>
                    @if($interCollegeCount > 0)
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-mono extra-small" title="Proposals with 2 or more collaborating colleges">
                            {{ $interCollegeCount }} Inter-College
                        </span>
                    @endif
                </div>

                <div class="d-flex flex-column gap-2">
                    @forelse(array_slice($collegesList, 0, 6) as $colItem)
                        <div class="p-2.5 rounded-3 border bg-light bg-opacity-50 d-flex align-items-center justify-content-between gap-2">
                            <div class="min-w-0">
                                <div class="fw-bold text-dark extra-small text-truncate" title="{{ $colItem['name'] }}">
                                    {{ $colItem['name'] }}
                                </div>
                                <div class="text-muted" style="font-size: 10.5px;">
                                    {{ $colItem['approved_count'] }} Approved &bull; ₱{{ number_format($colItem['total_budget'], 0) }}
                                </div>
                            </div>
                            <span class="badge bg-dark text-white rounded-pill font-mono extra-small flex-shrink-0">
                                {{ $colItem['proposals_count'] }} {{ Str::plural('Ext', $colItem['proposals_count']) }}
                            </span>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted extra-small">No implementing colleges tagged yet.</div>
                    @endforelse
                </div>
            </div>

            <div class="pt-2.5 mt-3 border-top d-flex align-items-center justify-content-between extra-small text-muted">
                <span>Active Academic &amp; Extension Units</span>
                <span class="fw-bold text-dark font-mono">{{ count($collegesList) }} Units</span>
            </div>
        </div>
    </div>

    {{-- Column 2: Collaborating Partner Agencies --}}
    <div class="col-xl-4">
        <div class="pi-card p-3.5 p-md-4 h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex align-items-center justify-content-between gap-2 mb-3 pb-2 border-bottom">
                    <div>
                        <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill extra-small fw-bold px-2 py-0.5">
                            PART I-G &bull; LINKAGES
                        </span>
                        <h6 class="fw-bold text-dark m-0 mt-1" style="font-size: 14.5px;">Collaborating Partner Agencies</h6>
                    </div>
                    <span class="badge bg-success-subtle text-success border border-success-subtle font-mono extra-small">
                        {{ count($partnersList) }} Linkages
                    </span>
                </div>

                <div class="d-flex flex-column gap-2" style="max-height: 255px; overflow-y: auto;">
                    @forelse($partnersList as $pItem)
                        <div class="p-2.5 rounded-3 border bg-white">
                            <div class="d-flex align-items-center justify-content-between gap-2 mb-0.5">
                                <span class="fw-bold text-dark extra-small text-truncate" title="{{ $pItem['agency'] }}">
                                    <i class="mdi mdi-domain text-primary me-1"></i>{{ $pItem['agency'] }}
                                </span>
                                <a href="{{ $pItem['proposal_url'] }}" class="badge bg-light text-primary border font-mono text-decoration-none" style="font-size: 9.5px;">
                                    {{ $pItem['proposal_code'] }}
                                </a>
                            </div>
                            <div class="text-muted text-truncate" style="font-size: 10.5px;" title="{{ $pItem['role'] }}">
                                Role: {{ $pItem['role'] }}
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted extra-small">No external collaborating agencies recorded yet.</div>
                    @endforelse
                </div>
            </div>

            <div class="pt-2.5 mt-3 border-top d-flex align-items-center justify-content-between extra-small text-muted">
                <span>External LGU, NGA, NGO &amp; Industry Partners</span>
                <span class="fw-bold text-dark font-mono">{{ collect($partnersList)->pluck('agency')->unique()->count() }} Unique</span>
            </div>
        </div>
    </div>

    {{-- Column 3: Proposal Funding & Resource Structure --}}
    <div class="col-xl-4">
        <div class="pi-card p-3.5 p-md-4 h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex align-items-center justify-content-between gap-2 mb-3 pb-2 border-bottom">
                    <div>
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill extra-small fw-bold px-2 py-0.5">
                            PART I-I &bull; BUDGET &amp; FUNDS
                        </span>
                        <h6 class="fw-bold text-dark m-0 mt-1" style="font-size: 14.5px;">Proposal Funding Structure</h6>
                    </div>
                    <a href="{{ route('dss.rie.budgets.index') }}" class="text-primary extra-small fw-bold text-decoration-none">Budget Ledger</a>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <div class="p-2.5 rounded-3 border bg-light">
                            <div class="extra-small text-muted">With Cash Budget</div>
                            <div class="fs-5 fw-extrabold text-dark font-mono">{{ $fundingStruct['with_cash_count'] }}</div>
                            <div class="text-success fw-semibold" style="font-size: 10px;">Cash-Funded Proposals</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2.5 rounded-3 border bg-light">
                            <div class="extra-small text-muted">In-Kind / Non-Cash</div>
                            <div class="fs-5 fw-extrabold text-dark font-mono">{{ $fundingStruct['in_kind_count'] }}</div>
                            <div class="text-secondary fw-semibold" style="font-size: 10px;">Expertise &amp; Facilities</div>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-column gap-2">
                    <div class="d-flex align-items-center justify-content-between px-3 py-2 rounded-3 border bg-white extra-small">
                        <span class="text-muted fw-semibold">Total Proposal Cash Requirement</span>
                        <span class="fw-bold text-dark font-mono">₱{{ number_format($fundingStruct['total_cash_budget'], 2) }}</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between px-3 py-2 rounded-3 border bg-white extra-small">
                        <span class="text-muted fw-semibold">Approved Proposals Cash Total</span>
                        <span class="fw-bold text-success font-mono">₱{{ number_format($fundingStruct['approved_budget_total'], 2) }}</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between px-3 py-2 rounded-3 border bg-white extra-small">
                        <span class="text-muted fw-semibold">With External Partner Share</span>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-mono">
                            {{ $fundingStruct['with_partner_share_count'] }} {{ Str::plural('Proposal', $fundingStruct['with_partner_share_count']) }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="pt-2.5 mt-3 border-top d-flex align-items-center justify-content-between extra-small text-muted">
                <span>Internal (SNSU) &amp; Partner Cost-Sharing</span>
                <span class="fw-bold text-dark font-mono">{{ $periodLabel ?? 'All-Time' }}</span>
            </div>
        </div>
    </div>
</div>

{{-- MODAL: Generic Drilldown for Banner Programs & Extension Agendas --}}
<div class="modal fade" id="categoryDrilldownModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-0 py-3 px-4 bg-dark text-white">
                <div>
                    <div class="extra-small text-uppercase fw-bold text-info" id="catModalType">BANNER PROGRAM MONITORING</div>
                    <h5 class="modal-title fw-extrabold text-white m-0" id="catModalTitle">Program</h5>
                    <p class="extra-small text-white-50 m-0 mt-0.5" id="catModalDesc"></p>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-2.5 mb-3">
                    <div class="col-4">
                        <div class="p-2.5 rounded-3 border bg-light text-center">
                            <div class="extra-small text-muted text-uppercase fw-semibold">Tagged Proposals</div>
                            <div class="fs-5 fw-extrabold text-dark font-mono" id="catModalTotal">0</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-2.5 rounded-3 border bg-light text-center">
                            <div class="extra-small text-muted text-uppercase fw-semibold">Approved</div>
                            <div class="fs-5 fw-extrabold text-success font-mono" id="catModalApproved">0</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-2.5 rounded-3 border bg-light text-center">
                            <div class="extra-small text-muted text-uppercase fw-semibold">Total Cash Budget</div>
                            <div class="fs-6 fw-bold text-dark font-mono mt-0.5" id="catModalBudget">₱0</div>
                        </div>
                    </div>
                </div>
                <div id="catModalProposalsList" class="border rounded-3 overflow-hidden"></div>
            </div>
        </div>
    </div>
</div>

<script>
    window.filterDashboardBanners = function (mode, btn) {
        if (btn && btn.parentElement) {
            btn.parentElement.querySelectorAll('.sdg-filter-btn').forEach(function (el) {
                el.classList.remove('active');
            });
            btn.classList.add('active');
        }
        document.querySelectorAll('#dashboardBannerGrid .pi-banner-tile').forEach(function (tile) {
            const isTagged = tile.getAttribute('data-banner-tagged') === '1';
            if (mode === 'all') {
                tile.style.display = '';
            } else if (mode === 'tagged') {
                tile.style.display = isTagged ? '' : 'none';
            } else if (mode === 'untagged') {
                tile.style.display = !isTagged ? '' : 'none';
            }
        });
    };

    window.openCategoryDrilldownModal = function (categoryType, title, desc, totalCount, approvedCount, totalBudget, proposals) {
        const typeEl = document.getElementById('catModalType');
        const titleEl = document.getElementById('catModalTitle');
        const descEl = document.getElementById('catModalDesc');
        const totalEl = document.getElementById('catModalTotal');
        const approvedEl = document.getElementById('catModalApproved');
        const budgetEl = document.getElementById('catModalBudget');
        const listEl = document.getElementById('catModalProposalsList');

        if (typeEl) typeEl.textContent = categoryType.toUpperCase() + ' • EXTENSION MONITORING';
        if (titleEl) titleEl.textContent = title;
        if (descEl) descEl.textContent = desc || '';
        if (totalEl) totalEl.textContent = totalCount || 0;
        if (approvedEl) approvedEl.textContent = approvedCount || 0;
        if (budgetEl) {
            const b = parseFloat(totalBudget || 0);
            budgetEl.textContent = '₱' + b.toLocaleString('en-PH', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
        }

        if (listEl) {
            const items = Array.isArray(proposals) ? proposals : [];
            if (items.length === 0) {
                listEl.innerHTML = '<div class="p-4 text-center text-muted extra-small bg-light">'
                    + '<i class="mdi mdi-folder-open-outline fs-3 d-block mb-1 text-secondary"></i>'
                    + 'No extension proposals are currently tagged under <strong>' + title + '</strong>.'
                    + '</div>';
            } else {
                let html = '<div class="list-group list-group-flush">';
                items.forEach(function (prop) {
                    html += '<div class="list-group-item p-3 d-flex align-items-center justify-content-between flex-wrap gap-2">'
                        + '<div class="min-w-0 flex-grow-1" style="max-width: 520px;">'
                        + '<div class="d-flex align-items-center gap-1.5 flex-wrap mb-1">'
                        + '<span class="badge bg-primary-subtle text-primary border border-primary-subtle font-mono extra-small">' + prop.code + '</span>'
                        + '<span class="badge ' + prop.status_class + ' font-mono extra-small">' + prop.status_label + '</span>'
                        + '</div>'
                        + '<a href="' + prop.url + '" class="fw-bold text-dark text-decoration-none d-block text-truncate" style="font-size: 13.5px;">' + prop.title + '</a>'
                        + '<div class="text-muted extra-small mt-0.5">'
                        + '<i class="mdi mdi-map-marker-outline me-0.5"></i>' + prop.campus
                        + ' &bull; <i class="mdi mdi-account-tie-outline me-0.5"></i>' + prop.leader
                        + ' &bull; <span class="fw-semibold text-dark">' + prop.budget_formatted + '</span>'
                        + '</div>'
                        + '</div>'
                        + '<div>'
                        + '<a href="' + prop.url + '" class="btn btn-xs btn-primary rounded-pill px-3 py-1.5 extra-small fw-bold">'
                        + '<i class="mdi mdi-eye-outline me-1"></i>View Proposal'
                        + '</a>'
                        + '</div>'
                        + '</div>';
                });
                html += '</div>';
                listEl.innerHTML = html;
            }
        }

        const modalEl = document.getElementById('categoryDrilldownModal');
        if (modalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        }
    };
</script>


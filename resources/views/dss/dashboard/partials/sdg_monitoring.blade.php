@php
    $sdgMonitoringList = $sdgMonitoring ?? [];
    $sdgCoveredCount = $sdgCoveredCount ?? collect($sdgMonitoringList)->filter(fn ($s) => ($s['extensions_count'] ?? 0) > 0)->count();
    $sdgUncoveredCount = $sdgUncoveredCount ?? max(0, 17 - $sdgCoveredCount);
    $sdgCoverageRate = $sdgCoverageRate ?? round(($sdgCoveredCount / 17) * 100, 1);
    $totalSdgTagsCount = $totalSdgTagsCount ?? collect($sdgMonitoringList)->sum('extensions_count');
    $proposalsWithSdgCount = $proposalsWithSdgCount ?? 0;

    $officialUnSdgSvgs = [
        1 => '<svg viewBox="0 0 64 44" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="9" cy="11" r="3.2" fill="#fff"/><path d="M5.5 16.5h7l1.2 9.5h-2v12h-2v-8.5h-1.4v8.5h-2v-12h-2l1.2-9.5z" fill="#fff"/><line x1="3.5" y1="25" x2="2" y2="38" stroke="#fff" stroke-width="1.6" stroke-linecap="round"/><circle cx="19" cy="17.5" r="2.4" fill="#fff"/><path d="M16.5 21.5h5l1 7h-1.5v9.5h-1.6v-6h-0.8v6h-1.6V28.5h-1.5l1-7z" fill="#fff"/><circle cx="29" cy="11.5" r="3.1" fill="#fff"/><path d="M25.5 16.5h7l2.2 11.5h-2.5v10h-2v-7.5h-1.4v7.5h-2V28h-2.5l1.2-11.5z" fill="#fff"/><circle cx="40" cy="11.5" r="3.1" fill="#fff"/><path d="M36.5 16.5h7l2.2 11.5h-2.5v10h-2v-7.5h-1.4v7.5h-2V28h-2.5l1.2-11.5z" fill="#fff"/><circle cx="49.5" cy="18" r="2.3" fill="#fff"/><path d="M47 22h5l1 6.5h-1.5v9.5h-1.6v-6h-0.8v6h-1.6v-9.5H46l1-6.5z" fill="#fff"/><circle cx="57.5" cy="11" r="3.2" fill="#fff"/><path d="M54 16.5h7l1.2 9.5h-2v12h-2v-8.5h-1.4v8.5h-2v-12h-2l1.2-9.5z" fill="#fff"/></svg>',
        2 => '<svg viewBox="0 0 64 44" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M23 6c-2.5 3.5 2.5 6.5 0 10M32 6c-2.5 3.5 2.5 6.5 0 10M41 6c-2.5 3.5 2.5 6.5 0 10" stroke="#fff" stroke-width="3" stroke-linecap="round"/><path d="M13 21h38c-1.5 9.5-8.5 15.5-19 15.5S14.5 30.5 13 21z" fill="#fff"/><path d="M24 38.5h16" stroke="#fff" stroke-width="3.2" stroke-linecap="round"/></svg>',
        3 => '<svg viewBox="0 0 64 44" fill="none" xmlns="http://www.w3.org/2000/svg"><polyline points="4,25 18,25 23,13 29,34 36,6 42,25 47,25" stroke="#fff" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round" fill="none"/><path d="M53.5 18.5c-1.8-2.2-5-2.2-6.8 0-1.8-2.2-5-2.2-6.8 0-2 2.4-1.6 6 0.8 8.3l6 5.7 6-5.7c2.4-2.3 2.8-5.9 0.8-8.3z" fill="#fff"/></svg>',
        4 => '<svg viewBox="0 0 64 44" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10 9l18 4.5v22L10 31V9zm36 0L28 13.5v22L46 31V9z" fill="#fff"/><path d="M8 12v21l20 4.8 20-4.8V12" stroke="#fff" stroke-width="2.4" stroke-linejoin="round" fill="none"/><rect x="53" y="8" width="3.8" height="24" rx="1" fill="#fff"/><polygon points="53,33.5 56.8,33.5 54.9,38.5" fill="#fff"/></svg>',
        5 => '<svg viewBox="0 0 64 44" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="30" cy="18" r="10.5" stroke="#fff" stroke-width="3.6" fill="none"/><path d="M38 10l8-7m0 0h-6.5m6.5 0v6.5" stroke="#fff" stroke-width="3.4" stroke-linecap="round" stroke-linejoin="round"/><path d="M30 28.5V41m-5.5-5H35.5" stroke="#fff" stroke-width="3.6" stroke-linecap="round"/><line x1="25" y1="16" x2="35" y2="16" stroke="#fff" stroke-width="3" stroke-linecap="round"/><line x1="25" y1="20.5" x2="35" y2="20.5" stroke="#fff" stroke-width="3" stroke-linecap="round"/></svg>',
        6 => '<svg viewBox="0 0 64 44" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M18 4h28l-3.8 26.5H21.8L18 4z" stroke="#fff" stroke-width="2.6" fill="none"/><path d="M19.5 11.5c4-2 8 2 12.5 0s8.5 2 12.5 0L42 30.5H22l-2.5-19z" fill="#fff"/><path d="M32 16c-3.2 4.5-4.8 7-4.8 9.2a4.8 4.8 0 009.6 0c0-2.2-1.6-4.7-4.8-9.2z" fill="currentColor"/><path d="M32 30.5v9m0 0l-4.5-4.5M32 39.5l4.5-4.5" stroke="#fff" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        7 => '<svg viewBox="0 0 64 44" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="32" cy="22" r="10.5" fill="#fff"/><path d="M32 15v7" stroke="#FCC30B" stroke-width="2.8" stroke-linecap="round"/><path d="M27.8 17.8a6.2 6.2 0 108.4 0" stroke="#FCC30B" stroke-width="2.6" stroke-linecap="round" fill="none"/><path d="M32 4v4.5M32 35.5V40M14 22h4.5M45.5 22H50M19.3 9.3l3.2 3.2M41.5 31.5l3.2 3.2M44.7 9.3l-3.2 3.2M22.5 31.5l-3.2 3.2" stroke="#fff" stroke-width="2.8" stroke-linecap="round"/></svg>',
        8 => '<svg viewBox="0 0 64 44" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14 23l11-10 8 7 15-14m0 0h-7m7 0v7" stroke="#fff" stroke-width="3.4" stroke-linecap="round" stroke-linejoin="round" fill="none"/><rect x="15" y="27" width="8" height="12" fill="#fff"/><rect x="27" y="22" width="8" height="17" fill="#fff"/><rect x="39" y="15" width="8" height="24" fill="#fff"/></svg>',
        9 => '<svg viewBox="0 0 64 44" fill="none" xmlns="http://www.w3.org/2000/svg"><polygon points="32,5 42,10.5 32,16 22,10.5" fill="#fff" stroke="#FD6925" stroke-width="1.2"/><polygon points="22,10.5 32,16 32,27 22,21.5" fill="#fff" stroke="#FD6925" stroke-width="1.2"/><polygon points="42,10.5 32,16 32,27 42,21.5" fill="none" stroke="#fff" stroke-width="2.2"/><polygon points="22,21.5 32,27 22,32.5 12,27" fill="#fff" stroke="#FD6925" stroke-width="1.2"/><polygon points="12,27 22,32.5 22,41 12,35.5" fill="none" stroke="#fff" stroke-width="2.2"/><polygon points="32,27 22,32.5 22,41 32,35.5" fill="#fff" stroke="#FD6925" stroke-width="1.2"/><polygon points="42,21.5 52,27 42,32.5 32,27" fill="#fff" stroke="#FD6925" stroke-width="1.2"/><polygon points="32,27 42,32.5 42,41 32,35.5" fill="none" stroke="#fff" stroke-width="2.2"/><polygon points="52,27 42,32.5 42,41 52,35.5" fill="#fff" stroke="#FD6925" stroke-width="1.2"/></svg>',
        10 => '<svg viewBox="0 0 64 44" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="24" y="16.5" width="16" height="4" fill="#fff"/><rect x="24" y="23.5" width="16" height="4" fill="#fff"/><polygon points="32,4 25,11 39,11" fill="#fff"/><polygon points="32,40 25,33 39,33" fill="#fff"/><polygon points="11,22 18,15 18,29" fill="#fff"/><polygon points="53,22 46,15 46,29" fill="#fff"/></svg>',
        11 => '<svg viewBox="0 0 64 44" fill="none" xmlns="http://www.w3.org/2000/svg"><polygon points="10,25 16,18 22,25 22,39 10,39" fill="#fff"/><rect x="14.5" y="31" width="3" height="8" fill="#FD9D24"/><rect x="24" y="7" width="12" height="32" fill="#fff"/><rect x="26.5" y="10" width="2.5" height="3" fill="#FD9D24"/><rect x="31" y="10" width="2.5" height="3" fill="#FD9D24"/><rect x="26.5" y="15.5" width="2.5" height="3" fill="#FD9D24"/><rect x="31" y="15.5" width="2.5" height="3" fill="#FD9D24"/><rect x="26.5" y="21" width="2.5" height="3" fill="#FD9D24"/><rect x="31" y="21" width="2.5" height="3" fill="#FD9D24"/><rect x="26.5" y="26.5" width="2.5" height="3" fill="#FD9D24"/><rect x="31" y="26.5" width="2.5" height="3" fill="#FD9D24"/><polygon points="38,18 47,12 47,39 38,39" fill="#fff"/><rect x="49" y="23" width="8" height="16" fill="#fff"/></svg>',
        12 => '<svg viewBox="0 0 64 44" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M26 26c-3 3.5-5.5 5-9 5a9 9 0 110-18c4.2 0 7.2 2.8 11 7.5l8 9.5c3.8 4.5 6.8 1 9 1a9 9 0 100-18c-3.5 0-6 1.5-9 5" stroke="#fff" stroke-width="4.2" stroke-linecap="round" fill="none"/><polygon points="22,22 29,24 24,30" fill="#fff"/></svg>',
        13 => '<svg viewBox="0 0 64 44" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 22C13 10 22 7 32 7s19 3 27 15c-8 12-17 15-27 15S13 34 5 22z" fill="#fff"/><circle cx="32" cy="22" r="10.5" fill="#3F7E44"/><path d="M27 15c2 1 4 0 5 2s-1 4 1 6 4 1 5 4-2 5-4 5-5-3-7-6-2-8 0-11z" fill="#fff"/></svg>',
        14 => '<svg viewBox="0 0 64 44" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 9c4-3 8 3 12 0s8 3 12 0 8 3 12 0M12 15c4-3 8 3 12 0s8 3 12 0 8 3 12 0" stroke="#fff" stroke-width="2.8" stroke-linecap="round" fill="none"/><path d="M21 29c6-7 16-9 25-4 4 2.2 6 4 6 4s-2 1.8-6 4c-9 5-19 3-25-4zm0 0l-8-6v12l8-6z" fill="#fff"/><circle cx="44" cy="28" r="1.6" fill="#0A97D9"/></svg>',
        15 => '<svg viewBox="0 0 64 44" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="24" cy="16" r="8" fill="#fff"/><circle cx="19" cy="20" r="5.5" fill="#fff"/><circle cx="29" cy="20" r="5.5" fill="#fff"/><rect x="22.2" y="23" width="3.6" height="9" fill="#fff"/><path d="M38 11c2-2 4.5-1.5 5.5 0.5 1-2 3.5-2.5 5.5-0.5M43 18c1.8-1.5 3.8-1 4.6 0.5 0.8-1.5 2.8-2 4.6-0.5" stroke="#fff" stroke-width="2.2" stroke-linecap="round" fill="none"/><line x1="12" y1="35" x2="52" y2="35" stroke="#fff" stroke-width="2.8" stroke-linecap="round"/><line x1="12" y1="39" x2="52" y2="39" stroke="#fff" stroke-width="2.8" stroke-linecap="round"/></svg>',
        16 => '<svg viewBox="0 0 64 44" fill="none" xmlns="http://www. w3.org/2000/svg"><path d="M18 14c5 6 11 7 16 4 3-2 6-6 9-5 1 3-1 7-4 10 3 0 6 2 6 2s-5 4-12 4c-6 0-12-3-15-9-1-2 0-6 0-6z" fill="#fff"/><path d="M43 13l5-5m-2 1l3 1m-4 2l1 3" stroke="#fff" stroke-width="1.8" stroke-linecap="round"/><rect x="16" y="34" width="24" height="2.8" rx="1" fill="#fff"/><rect x="38" y="31" width="7" height="8" rx="1" fill="#fff"/></svg>',
        17 => '<svg viewBox="0 0 64 44" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="32" cy="14" r="6.5" stroke="#fff" stroke-width="2.4" fill="none"/><circle cx="39.5" cy="19.5" r="6.5" stroke="#fff" stroke-width="2.4" fill="none"/><circle cx="36.5" cy="28" r="6.5" stroke="#fff" stroke-width="2.4" fill="none"/><circle cx="27.5" cy="28" r="6.5" stroke="#fff" stroke-width="2.4" fill="none"/><circle cx="24.5" cy="19.5" r="6.5" stroke="#fff" stroke-width="2.4" fill="none"/></svg>',
    ];
@endphp

<style>
    .sdg-monitor-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    }
    .sdg-monitor-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(126px, 1fr));
        gap: 10px;
    }
    .sdg-monitor-tile {
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 118px;
        padding: 10px 10px 8px;
        border-radius: 10px;
        background-color: var(--sdg-color, #19486A);
        color: #ffffff !important;
        text-decoration: none !important;
        cursor: pointer;
        overflow: hidden;
        border: 2px solid transparent;
        transition: transform 0.16s ease, box-shadow 0.16s ease, opacity 0.16s ease, filter 0.16s ease;
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.12);
        user-select: none;
    }
    .sdg-monitor-tile:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.22);
        opacity: 1 !important;
        filter: saturate(1.05) !important;
    }
    .sdg-monitor-tile.is-untagged {
        opacity: 0.58;
        filter: saturate(0.72);
    }
    .sdg-monitor-tile.is-tagged {
        opacity: 1;
        border-color: rgba(255, 255, 255, 0.85);
        box-shadow: 0 0 0 1px rgba(15, 23, 42, 0.08), 0 4px 12px rgba(15, 23, 42, 0.16);
    }
    .sdg-monitor-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 4px;
        z-index: 2;
    }
    .sdg-monitor-num {
        font-family: 'Oswald', 'Arial Narrow', 'Inter', sans-serif;
        font-size: 18px;
        font-weight: 800;
        line-height: 1;
        letter-spacing: -0.03em;
        color: #ffffff;
    }
    .sdg-monitor-count-pill {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        padding: 2px 7px;
        border-radius: 999px;
        font-size: 10.5px;
        font-weight: 800;
        line-height: 1.2;
        background: #ffffff;
        color: #0f172a;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.22);
        white-space: nowrap;
    }
    .sdg-monitor-tile.is-untagged .sdg-monitor-count-pill {
        background: rgba(15, 23, 42, 0.38);
        color: rgba(255, 255, 255, 0.92);
        box-shadow: none;
        font-weight: 700;
    }
    .sdg-monitor-title {
        font-family: 'Oswald', 'Arial Narrow', 'Inter', sans-serif;
        font-size: 9px;
        font-weight: 700;
        line-height: 1.12;
        letter-spacing: 0.02em;
        text-transform: uppercase;
        color: #ffffff;
        margin-top: 4px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        z-index: 2;
    }
    .sdg-monitor-art {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 36px;
        margin: 4px 0 2px;
        z-index: 1;
    }
    .sdg-monitor-art svg {
        width: 40px;
        height: 32px;
    }
    .sdg-monitor-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 9.5px;
        font-weight: 700;
        padding-top: 4px;
        border-top: 1px solid rgba(255, 255, 255, 0.24);
        color: rgba(255, 255, 255, 0.95);
        z-index: 2;
    }
    .sdg-filter-btn {
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        color: #475569;
        font-size: 11.5px;
        font-weight: 700;
        padding: 4px 11px;
        border-radius: 999px;
        transition: all 0.15s ease;
    }
    .sdg-filter-btn:hover {
        background: #f1f5f9;
        color: #0f172a;
    }
    .sdg-filter-btn.active {
        background: #0f172a;
        border-color: #0f172a;
        color: #ffffff;
    }
</style>

<div class="sdg-monitor-card p-3.5 p-md-4 mb-4">
    <div class="d-flex align-items-start align-items-lg-center justify-content-between flex-wrap gap-3 mb-3 pb-2.5 border-bottom">
        <div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="badge bg-primary text-white rounded-pill px-2.5 py-1 extra-small fw-bold">
                    <i class="mdi mdi-earth me-1"></i>UN SDG MONITORING
                </span>
                <h5 class="fw-bold text-dark m-0" style="font-size: 16px;">
                    Sustainable Development Goals (SDG) Extension Alignment &amp; Monitoring
                </h5>
            </div>
            <p class="text-muted extra-small mb-0 mt-1">
                Real-time count of Extension Proposals tagged per UN SDG target. Click any SDG tile to inspect the tagged extensions or filter in the masterlist.
            </p>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <div class="px-2.5 py-1.5 rounded-3 border bg-light d-flex align-items-center gap-2">
                <span class="rounded-circle d-inline-block bg-success" style="width: 8px; height: 8px;"></span>
                <span class="extra-small text-muted fw-semibold">Covered SDGs:</span>
                <span class="fw-bold text-dark extra-small font-mono">{{ $sdgCoveredCount }} / 17 ({{ $sdgCoverageRate }}%)</span>
            </div>
            <div class="px-2.5 py-1.5 rounded-3 border bg-light d-flex align-items-center gap-2">
                <span class="rounded-circle d-inline-block bg-primary" style="width: 8px; height: 8px;"></span>
                <span class="extra-small text-muted fw-semibold">Total SDG Tags:</span>
                <span class="fw-bold text-primary extra-small font-mono">{{ $totalSdgTagsCount }}</span>
            </div>
            <div class="px-2.5 py-1.5 rounded-3 border bg-light d-flex align-items-center gap-2">
                <span class="rounded-circle d-inline-block bg-warning" style="width: 8px; height: 8px;"></span>
                <span class="extra-small text-muted fw-semibold">Untagged SDGs:</span>
                <span class="fw-bold text-danger extra-small font-mono">{{ $sdgUncoveredCount }}</span>
            </div>

            <div class="d-flex align-items-center gap-1 ms-lg-1">
                <button type="button" class="sdg-filter-btn active" data-sdg-filter="all" onclick="window.filterDashboardSdgs('all', this)">
                    All (17)
                </button>
                <button type="button" class="sdg-filter-btn" data-sdg-filter="tagged" onclick="window.filterDashboardSdgs('tagged', this)">
                    Tagged ({{ $sdgCoveredCount }})
                </button>
                <button type="button" class="sdg-filter-btn" data-sdg-filter="untagged" onclick="window.filterDashboardSdgs('untagged', this)">
                    0 Tagged ({{ $sdgUncoveredCount }})
                </button>
            </div>
        </div>
    </div>

    {{-- 17 UN SDG Tiles Grid with Extension Counts --}}
    <div class="sdg-monitor-grid" id="dashboardSdgGrid">
        @foreach($sdgMonitoringList as $goalNum => $sdgItem)
            @php
                $extCount = (int) ($sdgItem['extensions_count'] ?? 0);
                $directCount = (int) ($sdgItem['direct_count'] ?? 0);
                $indirectCount = (int) ($sdgItem['indirect_count'] ?? 0);
                $isTagged = $extCount > 0;
                $colorHex = $sdgItem['color_hex'] ?? '#19486A';
                $svgMarkup = $officialUnSdgSvgs[$goalNum] ?? '';
            @endphp
            <div class="sdg-monitor-tile {{ $isTagged ? 'is-tagged' : 'is-untagged' }}"
                 style="--sdg-color: {{ $colorHex }}; color: {{ $colorHex }};"
                 data-sdg-goal="{{ $goalNum }}"
                 data-sdg-tagged="{{ $isTagged ? '1' : '0' }}"
                 onclick="window.openSdgMonitoringModal({{ $goalNum }})"
                 title="SDG {{ $goalNum }}: {{ $sdgItem['name'] }} — {{ $extCount }} {{ Str::plural('Extension', $extCount) }} Tagged (Click to view)">
                <div>
                    <div class="sdg-monitor-top">
                        <span class="sdg-monitor-num">{{ $goalNum }}</span>
                        <span class="sdg-monitor-count-pill">
                            @if($isTagged)
                                <i class="mdi mdi-tag-check text-success" style="font-size: 11px;"></i>
                            @endif
                            <span>{{ $extCount }} {{ Str::plural('Ext', $extCount) }}</span>
                        </span>
                    </div>
                    <div class="sdg-monitor-title">{{ $sdgItem['name'] }}</div>
                </div>

                <div class="sdg-monitor-art">
                    {!! $svgMarkup !!}
                </div>

                <div class="sdg-monitor-footer">
                    @if($isTagged)
                        <span>
                            {{ $directCount }} Direct{{ $indirectCount > 0 ? ' • '.$indirectCount.' Ind' : '' }}
                        </span>
                        <span><i class="mdi mdi-arrow-top-right"></i></span>
                    @else
                        <span class="opacity-75">No tagged ext.</span>
                        <span class="opacity-75">0</span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>

{{-- MODAL: SDG Extension Monitoring Drill-down --}}
<div class="modal fade" id="sdgMonitoringModal" tabindex="-1" aria-labelledby="sdgMonitoringModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-0 py-3 px-4 text-white" id="sdgModalHeader" style="background-color: #19486A;">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-3 bg-white bg-opacity-20 fw-bold font-mono"
                         style="width: 44px; height: 44px; font-size: 20px;" id="sdgModalGoalNum">
                        1
                    </div>
                    <div>
                        <div class="extra-small text-uppercase fw-bold opacity-85" id="sdgModalCode">UN SUSTAINABLE DEVELOPMENT GOAL</div>
                        <h5 class="modal-title fw-extrabold text-white m-0" id="sdgMonitoringModalLabel">No Poverty</h5>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                <div class="row g-2.5 mb-3">
                    <div class="col-sm-3 col-6">
                        <div class="p-2.5 rounded-3 border bg-light text-center">
                            <div class="extra-small text-muted text-uppercase fw-semibold">Tagged Extensions</div>
                            <div class="fs-5 fw-extrabold text-dark font-mono" id="sdgModalTotalCount">0</div>
                        </div>
                    </div>
                    <div class="col-sm-3 col-6">
                        <div class="p-2.5 rounded-3 border bg-light text-center">
                            <div class="extra-small text-muted text-uppercase fw-semibold">Direct / Indirect</div>
                            <div class="fs-6 fw-bold text-primary font-mono mt-0.5" id="sdgModalAlignmentBreakdown">0 / 0</div>
                        </div>
                    </div>
                    <div class="col-sm-3 col-6">
                        <div class="p-2.5 rounded-3 border bg-light text-center">
                            <div class="extra-small text-muted text-uppercase fw-semibold">Approved</div>
                            <div class="fs-5 fw-extrabold text-success font-mono" id="sdgModalApprovedCount">0</div>
                        </div>
                    </div>
                    <div class="col-sm-3 col-6">
                        <div class="p-2.5 rounded-3 border bg-light text-center">
                            <div class="extra-small text-muted text-uppercase fw-semibold">Total Budget</div>
                            <div class="fs-6 fw-bold text-dark font-mono mt-0.5" id="sdgModalTotalBudget">₱0</div>
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-between mb-2">
                    <h6 class="fw-bold text-dark extra-small text-uppercase m-0">
                        <i class="mdi mdi-format-list-checks text-primary me-1"></i>Tagged Extension Proposals
                    </h6>
                    <a href="{{ route('dss.rie.proposals.index') }}" id="sdgModalMasterlistLink" class="btn btn-xs btn-outline-primary rounded-pill px-2.5 py-1 extra-small fw-bold">
                        <i class="mdi mdi-filter-variant me-1"></i>Open in Proposals Masterlist
                    </a>
                </div>

                <div id="sdgModalProposalsList" class="border rounded-3 overflow-hidden"></div>
            </div>
        </div>
    </div>
</div>

<script>
    window.DSS_SDG_MONITORING_DATA = @json($sdgMonitoringList);
    window.DSS_PROPOSALS_INDEX_URL = "{{ route('dss.rie.proposals.index') }}";

    window.filterDashboardSdgs = function (mode, btn) {
        if (btn && btn.parentElement) {
            btn.parentElement.querySelectorAll('.sdg-filter-btn').forEach(function (el) {
                el.classList.remove('active');
            });
            btn.classList.add('active');
        }

        document.querySelectorAll('#dashboardSdgGrid .sdg-monitor-tile').forEach(function (tile) {
            const isTagged = tile.getAttribute('data-sdg-tagged') === '1';
            if (mode === 'all') {
                tile.style.display = '';
            } else if (mode === 'tagged') {
                tile.style.display = isTagged ? '' : 'none';
            } else if (mode === 'untagged') {
                tile.style.display = !isTagged ? '' : 'none';
            }
        });
    };

    window.openSdgMonitoringModal = function (goalNum) {
        const data = window.DSS_SDG_MONITORING_DATA ? window.DSS_SDG_MONITORING_DATA[goalNum] : null;
        if (!data) return;

        const header = document.getElementById('sdgModalHeader');
        const numEl = document.getElementById('sdgModalGoalNum');
        const codeEl = document.getElementById('sdgModalCode');
        const titleEl = document.getElementById('sdgMonitoringModalLabel');
        const totalEl = document.getElementById('sdgModalTotalCount');
        const alignEl = document.getElementById('sdgModalAlignmentBreakdown');
        const approvedEl = document.getElementById('sdgModalApprovedCount');
        const budgetEl = document.getElementById('sdgModalTotalBudget');
        const listEl = document.getElementById('sdgModalProposalsList');
        const masterlistLink = document.getElementById('sdgModalMasterlistLink');

        if (header) header.style.backgroundColor = data.color_hex || '#19486A';
        if (numEl) numEl.textContent = data.goal_number;
        if (codeEl) codeEl.textContent = (data.code || ('SDG ' + data.goal_number)) + ' • EXTENSION MONITORING';
        if (titleEl) titleEl.textContent = data.name;
        if (totalEl) totalEl.textContent = data.extensions_count || 0;
        if (alignEl) alignEl.textContent = (data.direct_count || 0) + ' Direct / ' + (data.indirect_count || 0) + ' Indirect';
        if (approvedEl) approvedEl.textContent = data.approved_count || 0;
        if (budgetEl) {
            const b = parseFloat(data.total_budget || 0);
            budgetEl.textContent = '₱' + b.toLocaleString('en-PH', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
        }
        if (masterlistLink) {
            masterlistLink.href = window.DSS_PROPOSALS_INDEX_URL + '?sdg=' + encodeURIComponent(data.goal_number);
        }

        if (listEl) {
            const proposals = Array.isArray(data.proposals) ? data.proposals : [];
            if (proposals.length === 0) {
                listEl.innerHTML = '<div class="p-4 text-center text-muted extra-small bg-light">'
                    + '<i class="mdi mdi-tag-off-outline fs-3 d-block mb-1 text-secondary"></i>'
                    + 'No extension proposals are currently tagged under <strong>SDG ' + data.goal_number + ': ' + data.name + '</strong>.'
                    + '</div>';
            } else {
                let html = '<div class="list-group list-group-flush">';
                proposals.forEach(function (prop) {
                    const alignBadge = prop.alignment_type === 'Indirect'
                        ? '<span class="badge bg-warning-subtle text-warning border border-warning-subtle extra-small font-mono">Indirect SDG</span>'
                        : '<span class="badge bg-success-subtle text-success border border-success-subtle extra-small font-mono">Direct SDG</span>';

                    html += '<div class="list-group-item p-3 d-flex align-items-center justify-content-between flex-wrap gap-2">'
                        + '<div class="min-w-0 flex-grow-1" style="max-width: 520px;">'
                        + '<div class="d-flex align-items-center gap-1.5 flex-wrap mb-1">'
                        + '<span class="badge bg-primary-subtle text-primary border border-primary-subtle font-mono extra-small">' + prop.code + '</span>'
                        + alignBadge
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

        const modalEl = document.getElementById('sdgMonitoringModal');
        if (modalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        }
    };
</script>

@include('dss.dashboard.partials.proposal_intelligence')



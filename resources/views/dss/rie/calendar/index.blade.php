@extends('layouts.dss.app')

@section('title', 'Extension Calendar - Duration & Range Monitoring — RIE System')

@section('content')
@php
    $sdgColors = [
        1 => '#E5243B', 2 => '#DDA63A', 3 => '#4C9F38', 4 => '#C5192D',
        5 => '#FF3A21', 6 => '#26BDE2', 7 => '#FCC30B', 8 => '#A21942',
        9 => '#FD6925', 10 => '#DD1367', 11 => '#FD9D24', 12 => '#BF8B2E',
        13 => '#3F7E44', 14 => '#0A97D9', 15 => '#56C02B', 16 => '#00689D',
        17 => '#19486A',
    ];
    $monthNamesShort = [1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec'];
@endphp

<style>
    /* Maximized Fullscreen Workspace Mode */
    #extCalWorkspaceWrapper.is-maximized {
        position: fixed;
        inset: 10px;
        z-index: 1055;
        background: #f8fafc;
        padding: 14px 18px;
        border-radius: 16px;
        box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.38);
        overflow-y: auto;
        border: 1px solid #cbd5e1;
    }

    /* Unified Top Strip (Compact KPIs + Inline Filters) */
    .ext-cal-topbar {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 10px 14px;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.03);
    }

    .ext-mini-kpi {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 11px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        font-size: 12px;
        line-height: 1;
        white-space: nowrap;
    }
    .ext-mini-kpi-dot {
        width: 8px;
        height: 8px;
        border-radius: 999px;
        flex-shrink: 0;
    }
    .ext-mini-kpi-label {
        color: #475569;
        font-weight: 600;
        font-size: 11.5px;
    }
    .ext-mini-kpi-val {
        font-weight: 800;
        font-size: 13px;
        color: #0f172a;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    }

    /* Native Inline Compact Selects (Protected from Select2 100%-width expansion) */
    .ext-inline-select {
        display: inline-block !important;
        width: auto !important;
        min-width: 120px;
        max-width: 190px;
        height: 32px;
        padding: 4px 28px 4px 10px;
        font-size: 12px;
        font-weight: 600;
        color: #1e293b;
        background-color: #f8fafc;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        cursor: pointer;
    }
    .ext-inline-select:focus {
        border-color: #2563eb;
        background-color: #ffffff;
        outline: none;
        box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.12);
    }

    /* Main Calendar Container Card */
    .ext-cal-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    }

    /* Category Filter Pills */
    .ext-cal-cat-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 11px;
        border-radius: 999px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        color: #334155;
        font-size: 11.5px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.15s ease;
        white-space: nowrap;
    }
    .ext-cal-cat-pill:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
    }
    .ext-cal-cat-pill.active {
        background: #0f172a;
        border-color: #0f172a;
        color: #ffffff;
    }

    /* Week-by-Week Continuous Range Calendar Grid */
    .ext-cal-grid-header {
        display: grid;
        grid-template-columns: repeat(7, minmax(0, 1fr));
        background: #0f172a;
        color: #f8fafc;
        text-align: center;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        padding: 10px 0;
        border-radius: 10px 10px 0 0;
    }
    .ext-cal-weeks-container {
        display: flex;
        flex-direction: column;
        border-left: 1px solid #cbd5e1;
        border-right: 1px solid #cbd5e1;
        border-bottom: 1px solid #cbd5e1;
        border-radius: 0 0 10px 10px;
        overflow: hidden;
        background: #ffffff;
    }
    .ext-cal-week-row {
        position: relative;
        min-height: 138px;
        border-top: 1px solid #cbd5e1;
        display: flex;
        flex-direction: column;
    }
    #extCalWorkspaceWrapper.is-maximized .ext-cal-week-row {
        min-height: 162px;
    }

    /* 7-Column Background Grid for Day Borders & Shading */
    .ext-cal-week-bg {
        position: absolute;
        inset: 0;
        display: grid;
        grid-template-columns: repeat(7, minmax(0, 1fr));
        pointer-events: none;
        z-index: 1;
    }
    .ext-cal-bg-cell {
        border-right: 1px solid #e2e8f0;
        background: #ffffff;
    }
    .ext-cal-bg-cell:last-child {
        border-right: none;
    }
    .ext-cal-bg-cell.is-outside {
        background: #f8fafc;
    }
    .ext-cal-bg-cell.is-weekend {
        background: #fafaf9;
    }
    .ext-cal-bg-cell.is-today {
        background: rgba(16, 185, 129, 0.06);
        box-shadow: inset 0 0 0 2px rgba(16, 185, 129, 0.35);
    }

    /* Day Number Header inside each Week Row */
    .ext-cal-week-day-headers {
        position: relative;
        z-index: 2;
        display: grid;
        grid-template-columns: repeat(7, minmax(0, 1fr));
        padding: 6px 0 4px;
    }
    .ext-cal-day-header-cell {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 8px;
        min-width: 0;
    }
    .ext-cal-day-header-cell.is-outside {
        opacity: 0.45;
    }
    .ext-cal-day-num {
        font-size: 12.5px;
        font-weight: 800;
        color: #1e293b;
        width: 24px;
        height: 24px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
    }
    .ext-cal-day-num.is-today {
        background: #059669;
        color: #ffffff;
        box-shadow: 0 2px 5px rgba(5, 150, 105, 0.35);
    }

    /* Multi-Day Continuous Range Bars Layer */
    .ext-cal-week-lanes {
        position: relative;
        z-index: 3;
        display: grid;
        grid-template-columns: repeat(7, minmax(0, 1fr));
        row-gap: 5px;
        column-gap: 0;
        padding: 2px 4px 12px;
        flex-grow: 1;
        align-content: start;
    }

    .ext-cal-range-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 6px;
        height: 26px;
        min-width: 0;
        max-width: 100%;
        box-sizing: border-box;
        padding: 0 8px;
        margin: 0 3px;
        border-radius: 6px;
        color: #ffffff !important;
        font-size: 11px;
        font-weight: 700;
        cursor: pointer;
        overflow: hidden;
        white-space: nowrap;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.16);
        transition: transform 0.12s ease, filter 0.12s ease, box-shadow 0.12s ease;
    }
    .ext-cal-range-bar:hover {
        transform: translateY(-1px);
        filter: brightness(1.06);
        box-shadow: 0 4px 10px rgba(15, 23, 42, 0.22);
        z-index: 5;
    }
    .ext-cal-range-bar.continues-left {
        margin-left: 0;
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
        border-left: 3px dashed rgba(255, 255, 255, 0.7);
    }
    .ext-cal-range-bar.continues-right {
        margin-right: 0;
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
        border-right: 3px dashed rgba(255, 255, 255, 0.7);
    }
    .ext-cal-range-bar-main {
        display: flex;
        align-items: center;
        gap: 5px;
        min-width: 0;
        flex: 1 1 auto;
        overflow: hidden;
    }
    .ext-cal-range-code {
        background: rgba(15, 23, 42, 0.28);
        padding: 1px 5px;
        border-radius: 4px;
        font-size: 9.5px;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        flex-shrink: 0;
    }
    .ext-cal-range-title {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        flex: 1 1 auto;
        min-width: 0;
    }
    .ext-cal-range-pill {
        background: rgba(15, 23, 42, 0.22);
        padding: 1px 7px;
        border-radius: 999px;
        font-size: 9.5px;
        font-weight: 800;
        flex-shrink: 0;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    }

    /* Gantt Roadmap Bars */
    .gantt-month-cell {
        width: 38px;
        min-width: 38px;
        text-align: center;
        padding: 4px 2px;
        position: relative;
    }
    .gantt-bar-segment {
        height: 18px;
        border-radius: 4px;
        background: #cbd5e1;
        transition: all 0.15s ease;
    }
    .gantt-bar-segment.is-active-ongoing {
        background: linear-gradient(90deg, #059669, #10b981);
        box-shadow: 0 1px 4px rgba(16, 185, 129, 0.35);
    }
    .gantt-bar-segment.is-active-upcoming {
        background: linear-gradient(90deg, #2563eb, #3b82f6);
        box-shadow: 0 1px 4px rgba(37, 99, 235, 0.3);
    }
    .gantt-bar-segment.is-active-completed {
        background: linear-gradient(90deg, #475569, #64748b);
    }
</style>

<div class="container-fluid p-0" id="extCalWorkspaceWrapper">
    {{-- ========================================================================
         COMPACT SINGLE-ROW TOP BAR (Mini KPIs + Inline Filters + Maximize)
         ======================================================================== --}}
    <div class="ext-cal-topbar mb-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            {{-- Left: Compact Mini KPI Strip --}}
            <div class="d-flex align-items-center flex-wrap gap-2">
                <div class="ext-mini-kpi" style="background: #f0fdf4; border-color: #bbf7d0;">
                    <span class="ext-mini-kpi-dot" style="background: #10b981;"></span>
                    <span class="ext-mini-kpi-label text-success-emphasis">Ongoing:</span>
                    <span class="ext-mini-kpi-val text-success">{{ $ongoingExtensionsCount }}</span>
                </div>
                <div class="ext-mini-kpi" style="background: #eff6ff; border-color: #bfdbfe;">
                    <span class="ext-mini-kpi-dot" style="background: #2563eb;"></span>
                    <span class="ext-mini-kpi-label text-primary-emphasis">Upcoming:</span>
                    <span class="ext-mini-kpi-val text-primary">{{ $upcomingCommencementsCount }}</span>
                </div>
                <div class="ext-mini-kpi">
                    <span class="ext-mini-kpi-dot" style="background: #475569;"></span>
                    <span class="ext-mini-kpi-label">Completed:</span>
                    <span class="ext-mini-kpi-val">{{ $completedExtensionsCount }}</span>
                </div>
                <div class="ext-mini-kpi" style="background: #fefce8; border-color: #fde68a;">
                    <span class="ext-mini-kpi-dot" style="background: #d97706;"></span>
                    <span class="ext-mini-kpi-label text-warning-emphasis">Scheduled Ranges:</span>
                    <span class="ext-mini-kpi-val text-dark" id="kpiTotalEvents">{{ $totalScheduledEventsCount }}</span>
                </div>
            </div>

            {{-- Right: Inline Native Filters + Maximize Button --}}
            <div class="d-flex align-items-center flex-wrap gap-2">
                <form method="GET" action="{{ route('dss.rie.calendar.index') }}" class="d-flex align-items-center flex-wrap gap-2 m-0">
                    <select name="period" data-no-select2="true" class="form-select ext-inline-select" onchange="this.form.submit()" title="Monitoring Scope / Year">
                        <option value="all" {{ $selectedPeriod === 'all' ? 'selected' : '' }}>Scope: All-Time (Whole)</option>
                        @foreach($availableYears as $yr)
                            <option value="{{ $yr }}" {{ (string) $selectedPeriod === (string) $yr ? 'selected' : '' }}>Fiscal Year {{ $yr }}</option>
                        @endforeach
                    </select>

                    <select name="campus_id" data-no-select2="true" class="form-select ext-inline-select" onchange="this.form.submit()" title="University Campus">
                        <option value="all" {{ $selectedCampus === 'all' ? 'selected' : '' }}>All Campuses</option>
                        @foreach($campuses as $camp)
                            <option value="{{ $camp->id }}" {{ (string) $selectedCampus === (string) $camp->id ? 'selected' : '' }}>
                                {{ $camp->campus_name }}
                            </option>
                        @endforeach
                    </select>

                    <select name="status" data-no-select2="true" class="form-select ext-inline-select" onchange="this.form.submit()" title="Approval Status">
                        <option value="all" {{ $selectedStatus === 'all' ? 'selected' : '' }}>All Statuses</option>
                        <option value="approved" {{ $selectedStatus === 'approved' ? 'selected' : '' }}>Approved Only</option>
                        <option value="in_review" {{ $selectedStatus === 'in_review' ? 'selected' : '' }}>In Review</option>
                        <option value="draft" {{ $selectedStatus === 'draft' ? 'selected' : '' }}>Draft / Returned</option>
                    </select>

                    @if($selectedPeriod !== 'all' || $selectedCampus !== 'all' || $selectedStatus !== 'all')
                        <a href="{{ route('dss.rie.calendar.index') }}" class="btn btn-sm btn-light border rounded-2 px-2.5 py-1 text-secondary" title="Reset Filters">
                            <i class="mdi mdi-refresh"></i>
                        </a>
                    @endif
                </form>

                <button type="button" class="btn btn-sm btn-dark rounded-2 px-3 py-1 fw-bold d-inline-flex align-items-center gap-1" style="height: 32px; font-size: 12px;" id="btnMaximizeCal" onclick="window.toggleMaximizeCalendar()">
                    <i class="mdi mdi-fullscreen" id="iconMaximizeCal"></i>
                    <span id="labelMaximizeCal">Maximize</span>
                </button>
            </div>
        </div>
    </div>

    {{-- ========================================================================
         MAIN MAXIMIZED CALENDAR & RANGE WORKSPACE CARD
         ======================================================================== --}}
    <div class="ext-cal-card p-3">
        {{-- Row 1: Month Navigation + View Mode Switcher --}}
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pb-2.5 mb-2.5 border-bottom">
            {{-- Left: Month Navigation Controls --}}
            <div class="d-flex align-items-center flex-wrap gap-2" id="calendarMonthNavToolbar">
                <div class="d-inline-flex align-items-center border rounded-3 bg-light p-0.5">
                    <button type="button" class="btn btn-sm btn-light border-0 rounded-2 px-2 py-1" onclick="window.navigateCalendarMonth(-1)" title="Previous Month">
                        <i class="mdi mdi-chevron-left fs-5"></i>
                    </button>
                    <h5 class="fw-extrabold text-dark m-0 px-3" id="calendarCurrentMonthLabel" style="min-width: 165px; text-align: center; font-size: 15.5px;">
                        October 2026
                    </h5>
                    <button type="button" class="btn btn-sm btn-light border-0 rounded-2 px-2 py-1" onclick="window.navigateCalendarMonth(1)" title="Next Month">
                        <i class="mdi mdi-chevron-right fs-5"></i>
                    </button>
                </div>

                <button type="button" class="btn btn-sm btn-outline-primary rounded-2 px-2.5 py-1 fw-bold" style="height: 32px; font-size: 12px;" onclick="window.jumpCalendarToToday()">
                    Today
                </button>

                <select id="calMonthSelect" data-no-select2="true" class="form-select ext-inline-select" style="min-width: 125px;" onchange="window.jumpCalendarToSelected()">
                    @foreach(['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $mIdx => $mName)
                        <option value="{{ $mIdx }}">{{ $mName }}</option>
                    @endforeach
                </select>

                <select id="calYearSelect" data-no-select2="true" class="form-select ext-inline-select" style="min-width: 90px;" onchange="window.jumpCalendarToSelected()">
                    @foreach($availableYears as $yr)
                        <option value="{{ $yr }}" {{ (int) $defaultYear === (int) $yr ? 'selected' : '' }}>{{ $yr }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Right: View Mode Switcher --}}
            <div class="pm-segment-group" role="tablist">
                <button type="button" class="pm-segment-pill active" id="btnViewCalendar" onclick="window.switchCalendarViewMode('calendar', this)" style="font-size: 11.5px; padding: 5px 12px;">
                    <i class="mdi mdi-calendar-range me-1"></i>Range Calendar
                </button>
                <button type="button" class="pm-segment-pill" id="btnViewGantt" onclick="window.switchCalendarViewMode('gantt', this)" style="font-size: 11.5px; padding: 5px 12px;">
                    <i class="mdi mdi-chart-gantt me-1"></i>Commencement &amp; Duration Roadmap ({{ count($durationRoadmapItems) }})
                </button>
                <button type="button" class="pm-segment-pill" id="btnViewList" onclick="window.switchCalendarViewMode('list', this)" style="font-size: 11.5px; padding: 5px 12px;">
                    <i class="mdi mdi-format-list-bulleted-square me-1"></i>Chronological Milestone Ledger ({{ count($calendarEvents) }})
                </button>
            </div>
        </div>

        {{-- Row 2: Category Range Filter Pills --}}
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
            <div class="d-flex align-items-center flex-wrap gap-1.5" id="calendarCategoryFilterGroup">
                <button type="button" class="ext-cal-cat-pill active" data-cat="all" onclick="window.filterCalendarByCategory('all', this)">
                    <span>All Date Ranges</span>
                </button>
                <button type="button" class="ext-cal-cat-pill" data-cat="proposal_duration" onclick="window.filterCalendarByCategory('proposal_duration', this)">
                    <span class="rounded-circle d-inline-block" style="width: 8px; height: 8px; background: #2563eb;"></span>
                    <span>Proposal Commencement &amp; Duration</span>
                </button>
                <button type="button" class="ext-cal-cat-pill" data-cat="conduct_activity" onclick="window.filterCalendarByCategory('conduct_activity', this)">
                    <span class="rounded-circle d-inline-block" style="width: 8px; height: 8px; background: #059669;"></span>
                    <span>Conduct of Activity Range</span>
                </button>
                <button type="button" class="ext-cal-cat-pill" data-cat="materials_procurement" onclick="window.filterCalendarByCategory('materials_procurement', this)">
                    <span class="rounded-circle d-inline-block" style="width: 8px; height: 8px; background: #d97706;"></span>
                    <span>Materials Arrival Range</span>
                </button>
                <button type="button" class="ext-cal-cat-pill" data-cat="moa_signing" onclick="window.filterCalendarByCategory('moa_signing', this)">
                    <span class="rounded-circle d-inline-block" style="width: 8px; height: 8px; background: #7c3aed;"></span>
                    <span>MOA Signing</span>
                </button>
                <button type="button" class="ext-cal-cat-pill" data-cat="monitoring_report" onclick="window.filterCalendarByCategory('monitoring_report', this)">
                    <span class="rounded-circle d-inline-block" style="width: 8px; height: 8px; background: #db2777;"></span>
                    <span>Monitoring &amp; Evaluation</span>
                </button>
            </div>

            <div class="extra-small text-muted fw-semibold d-none d-xl-block">
                <i class="mdi mdi-cursor-default-click-outline text-primary me-1"></i>Click any range bar to inspect full schedule details
            </div>
        </div>

        {{-- ====================================================================
             VIEW 1: MAXIMIZED CONTINUOUS RANGE-STYLE MONTHLY CALENDAR
             ==================================================================== --}}
        <div id="viewPaneCalendar">
            <div class="ext-cal-grid-header">
                <div>Sunday</div>
                <div>Monday</div>
                <div>Tuesday</div>
                <div>Wednesday</div>
                <div>Thursday</div>
                <div>Friday</div>
                <div>Saturday</div>
            </div>
            <div class="ext-cal-weeks-container" id="extCalendarGridBody"></div>
        </div>

        {{-- ====================================================================
             VIEW 2: COMMENCEMENT & DURATION GANTT ROADMAP (JAN–DEC)
             ==================================================================== --}}
        <div id="viewPaneGantt" style="display: none;">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2.5">
                <div class="extra-small text-muted fw-semibold">
                    <i class="mdi mdi-information-outline text-primary me-1"></i>Continuous 12-Month Commencement &amp; Duration Range Matrix (Start Date &rarr; End Date)
                </div>
                <div class="d-flex align-items-center gap-3 extra-small fw-bold">
                    <span class="d-inline-flex align-items-center gap-1.5"><span class="rounded-1 d-inline-block" style="width: 12px; height: 12px; background: #10b981;"></span>Ongoing Range</span>
                    <span class="d-inline-flex align-items-center gap-1.5"><span class="rounded-1 d-inline-block" style="width: 12px; height: 12px; background: #3b82f6;"></span>Upcoming Range</span>
                    <span class="d-inline-flex align-items-center gap-1.5"><span class="rounded-1 d-inline-block" style="width: 12px; height: 12px; background: #64748b;"></span>Completed Range</span>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 12.5px;">
                    <thead class="table-light extra-small text-uppercase text-muted">
                        <tr>
                            <th style="min-width: 250px;">Extension Proposal / Activity</th>
                            <th style="min-width: 185px;">Commencement &rarr; Completion Range</th>
                            <th style="min-width: 120px;">Duration</th>
                            <th style="min-width: 130px;">Timeline Status</th>
                            @foreach($monthNamesShort as $mNum => $mLabel)
                                <th class="gantt-month-cell">{{ $mLabel }}</th>
                            @endforeach
                            <th class="text-end pe-2" style="min-width: 80px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($durationRoadmapItems as $item)
                            @php
                                $tState = $item['timeline_state'] ?? 'upcoming';
                                $segClass = $tState === 'ongoing' ? 'is-active-ongoing' : ($tState === 'completed' ? 'is-active-completed' : 'is-active-upcoming');
                            @endphp
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-1.5 mb-0.5">
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-mono extra-small">{{ $item['code'] }}</span>
                                        <span class="badge {{ $item['status_class'] }} font-mono" style="font-size: 9.5px;">{{ $item['status_label'] }}</span>
                                    </div>
                                    <a href="{{ $item['url'] }}" class="fw-bold text-dark text-decoration-none d-block text-truncate" style="max-width: 270px;" title="{{ $item['title'] }}">
                                        {{ $item['title'] }}
                                    </a>
                                    <div class="text-muted extra-small text-truncate" style="max-width: 270px;">
                                        {{ $item['campus'] }} &bull; {{ $item['leader'] }}
                                    </div>
                                </td>
                                <td>
                                    <div class="d-inline-flex align-items-center gap-1.5 px-2 py-1 rounded-2 bg-light border font-mono extra-small fw-bold text-dark">
                                        <span class="text-success">{{ $item['start_formatted'] }}</span>
                                        <i class="mdi mdi-arrow-right text-muted"></i>
                                        <span class="text-danger">{{ $item['end_formatted'] }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border font-mono extra-small px-2 py-1">
                                        <i class="mdi mdi-timer-outline text-primary me-1"></i>{{ $item['duration_label'] }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $item['timeline_badge']['class'] ?? 'bg-secondary' }} font-mono extra-small d-inline-block mb-0.5">
                                        {{ $item['timeline_badge']['label'] ?? 'SCHEDULED' }}
                                    </span>
                                    <div class="text-muted" style="font-size: 10.5px;">
                                        {{ $item['timeline_badge']['days_note'] ?? '' }}
                                    </div>
                                </td>
                                @foreach($monthNamesShort as $mNum => $mLabel)
                                    @php
                                        $isActiveMonth = in_array($mNum, $item['active_months'] ?? [], true);
                                    @endphp
                                    <td class="gantt-month-cell">
                                        @if($isActiveMonth)
                                            <div class="gantt-bar-segment {{ $segClass }}" title="{{ $item['title'] }} ({{ $item['start_formatted'] }} → {{ $item['end_formatted'] }})"></div>
                                        @else
                                            <div style="height: 18px; border-radius: 4px; background: #f1f5f9;"></div>
                                        @endif
                                    </td>
                                @endforeach
                                <td class="text-end pe-2">
                                    <a href="{{ $item['url'] }}" class="btn btn-xs btn-outline-primary rounded-pill px-2.5 py-1 extra-small fw-bold">
                                        <i class="mdi mdi-eye-outline"></i> View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="17" class="text-center py-5 text-muted extra-small">
                                    No extension commencement or duration records found for the selected filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ====================================================================
             VIEW 3: CHRONOLOGICAL SCHEDULE & MILESTONES RANGE LEDGER
             ==================================================================== --}}
        <div id="viewPaneList" style="display: none;">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="milestoneLedgerTable" style="font-size: 13px;">
                    <thead class="table-light extra-small text-uppercase text-muted">
                        <tr>
                            <th style="width: 225px;">Date Range (Start &rarr; End)</th>
                            <th style="width: 190px;">Milestone Category</th>
                            <th>Extension Activity / Proposal Title</th>
                            <th style="width: 140px;">Duration Span</th>
                            <th style="width: 160px;">Campus &amp; Leader</th>
                            <th style="width: 145px;">Status</th>
                            <th class="text-end pe-2" style="width: 95px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($calendarEvents as $evIdx => $ev)
                            <tr class="cal-ledger-row" data-category="{{ $ev['category'] }}">
                                <td>
                                    <div class="d-inline-flex align-items-center gap-1.5 px-2 py-1 rounded-2 bg-light border font-mono extra-small fw-bold text-dark">
                                        <span>{{ $ev['range_label'] ?? $ev['start_formatted'] }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge text-white extra-small px-2.5 py-1 d-inline-flex align-items-center gap-1" style="background-color: {{ $ev['color'] }};">
                                        <i class="mdi {{ $ev['icon'] }}"></i>
                                        <span>{{ $ev['category_label'] }}</span>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-1.5 mb-0.5">
                                        <span class="badge bg-light text-dark border font-mono extra-small">{{ $ev['code'] }}</span>
                                        <span class="text-muted extra-small">{{ $ev['doc_type'] }}</span>
                                    </div>
                                    <a href="{{ $ev['url'] }}" class="fw-bold text-dark text-decoration-none d-block text-truncate" style="max-width: 340px;" title="{{ $ev['short_title'] }}">
                                        {{ $ev['short_title'] }}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border font-mono extra-small">{{ $ev['duration_label'] }}</span>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark extra-small text-truncate" style="max-width: 155px;">{{ $ev['campus'] }}</div>
                                    <div class="text-muted extra-small text-truncate" style="max-width: 155px;">{{ $ev['leader'] }}</div>
                                </td>
                                <td>
                                    <span class="badge {{ $ev['status_class'] }} font-mono extra-small">{{ $ev['status_label'] }}</span>
                                </td>
                                <td class="text-end pe-2">
                                    <button type="button" class="btn btn-xs btn-outline-primary rounded-pill px-2.5 py-1 extra-small fw-bold"
                                            onclick="window.openCalendarEventModal({{ $evIdx }})">
                                        <i class="mdi mdi-information-outline me-0.5"></i>Details
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted extra-small">
                                    No scheduled events or commencement dates found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- MODAL: Event & Duration Details Inspection --}}
<div class="modal fade" id="calendarEventModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-0 py-3 px-4 text-white" id="calModalHeader" style="background-color: #2563eb;">
                <div>
                    <div class="extra-small text-uppercase fw-bold opacity-85" id="calModalCategory">PROPOSAL COMMENCEMENT & DURATION</div>
                    <h5 class="modal-title fw-extrabold text-white m-0 mt-0.5" id="calModalTitle">Extension Title</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-mono extra-small px-2.5 py-1" id="calModalCode">PROP-0001</span>
                    <span class="badge bg-success text-white font-mono extra-small px-2.5 py-1" id="calModalStatus">APPROVED</span>
                </div>

                {{-- Highlighted Full Date Range Banner --}}
                <div class="p-3 rounded-3 border mb-3" style="background: #f8fafc;">
                    <div class="extra-small text-muted text-uppercase fw-bold mb-1">Scheduled Date Range (Commencement &rarr; Completion)</div>
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="fw-extrabold text-dark font-mono" style="font-size: 13.5px;">
                            <span id="calModalStart">—</span>
                            <i class="mdi mdi-arrow-right mx-1 text-primary"></i>
                            <span id="calModalEnd">—</span>
                        </div>
                        <span class="badge bg-primary text-white font-mono extra-small px-2.5 py-1" id="calModalDuration">—</span>
                    </div>
                </div>

                <div class="p-3 rounded-3 border bg-white mb-3 extra-small">
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted fw-semibold">Campus:</span>
                        <span class="fw-bold text-dark" id="calModalCampus">—</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted fw-semibold">Project Leader:</span>
                        <span class="fw-bold text-dark" id="calModalLeader">—</span>
                    </div>
                    <div class="d-flex justify-content-between py-1">
                        <span class="text-muted fw-semibold">Budget Valuation:</span>
                        <span class="fw-bold text-dark font-mono" id="calModalBudget">—</span>
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-end gap-2">
                    <a href="#" target="_blank" id="calModalPdfBtn" class="btn btn-sm btn-light border rounded-pill px-3 fw-bold text-danger">
                        <i class="mdi mdi-file-pdf-box me-1"></i>View PDF
                    </a>
                    <a href="#" id="calModalOpenBtn" class="btn btn-sm btn-primary rounded-pill px-4 fw-bold">
                        <i class="mdi mdi-open-in-new me-1"></i>Open Workbench
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    window.EXT_CALENDAR_EVENTS = @json($calendarEvents);
    window.EXT_CAL_ACTIVE_CAT = 'all';
    window.EXT_CAL_YEAR = {{ (int) $defaultYear }};
    window.EXT_CAL_MONTH = {{ (int) $defaultMonth - 1 }};

    // Default the initial calendar month to the most relevant ongoing/upcoming event month
    (function initSmartMonth() {
        const evs = window.EXT_CALENDAR_EVENTS || [];
        if (evs.length > 0) {
            const ongoingOrUpcoming = evs.find(e => e.timeline_state === 'ongoing' || e.timeline_state === 'upcoming') || evs[0];
            if (ongoingOrUpcoming && ongoingOrUpcoming.start) {
                const parts = ongoingOrUpcoming.start.split('-');
                if (parts.length === 3) {
                    window.EXT_CAL_YEAR = parseInt(parts[0], 10);
                    window.EXT_CAL_MONTH = parseInt(parts[1], 10) - 1;
                }
            }
        }
    })();

    /**
     * Renders a Week-by-Week Continuous Range Calendar where multi-day events
     * span horizontally across columns (`grid-column: startCol / span spanCols`).
     */
    window.renderExtensionCalendarGrid = function () {
        const gridBody = document.getElementById('extCalendarGridBody');
        const labelEl = document.getElementById('calendarCurrentMonthLabel');
        const monthSel = document.getElementById('calMonthSelect');
        const yearSel = document.getElementById('calYearSelect');
        if (!gridBody) return;

        const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        if (labelEl) {
            labelEl.textContent = monthNames[window.EXT_CAL_MONTH] + ' ' + window.EXT_CAL_YEAR;
        }
        if (monthSel) monthSel.value = String(window.EXT_CAL_MONTH);
        if (yearSel) yearSel.value = String(window.EXT_CAL_YEAR);

        const firstDay = new Date(window.EXT_CAL_YEAR, window.EXT_CAL_MONTH, 1);
        const startWeekday = firstDay.getDay();
        const daysInMonth = new Date(window.EXT_CAL_YEAR, window.EXT_CAL_MONTH + 1, 0).getDate();
        const prevMonthDays = new Date(window.EXT_CAL_YEAR, window.EXT_CAL_MONTH, 0).getDate();

        const todayObj = new Date();
        const todayStr = todayObj.getFullYear() + '-' + String(todayObj.getMonth() + 1).padStart(2, '0') + '-' + String(todayObj.getDate()).padStart(2, '0');

        const filteredEvents = (window.EXT_CALENDAR_EVENTS || [])
            .map((ev, idx) => Object.assign({}, ev, { _idx: idx }))
            .filter(ev => window.EXT_CAL_ACTIVE_CAT === 'all' || ev.category === window.EXT_CAL_ACTIVE_CAT);

        const totalCells = Math.ceil((startWeekday + daysInMonth) / 7) * 7;
        const totalWeeks = totalCells / 7;

        const cells = [];
        for (let i = 0; i < totalCells; i++) {
            let cellYear = window.EXT_CAL_YEAR;
            let cellMonth = window.EXT_CAL_MONTH;
            let dayNum = 1;
            let isOutside = false;

            if (i < startWeekday) {
                isOutside = true;
                dayNum = prevMonthDays - startWeekday + i + 1;
                cellMonth = window.EXT_CAL_MONTH - 1;
                if (cellMonth < 0) {
                    cellMonth = 11;
                    cellYear--;
                }
            } else if (i >= startWeekday + daysInMonth) {
                isOutside = true;
                dayNum = i - (startWeekday + daysInMonth) + 1;
                cellMonth = window.EXT_CAL_MONTH + 1;
                if (cellMonth > 11) {
                    cellMonth = 0;
                    cellYear++;
                }
            } else {
                dayNum = i - startWeekday + 1;
            }

            const dateStr = cellYear + '-' + String(cellMonth + 1).padStart(2, '0') + '-' + String(dayNum).padStart(2, '0');
            cells.push({
                dayNum: dayNum,
                dateStr: dateStr,
                isOutside: isOutside,
                isToday: dateStr === todayStr,
                isWeekend: (i % 7 === 0) || (i % 7 === 6),
                monthShort: monthNames[cellMonth].slice(0, 3)
            });
        }

        let html = '';

        for (let w = 0; w < totalWeeks; w++) {
            const weekCells = cells.slice(w * 7, w * 7 + 7);
            const weekStartStr = weekCells[0].dateStr;
            const weekEndStr = weekCells[6].dateStr;

            const weekSegments = [];
            filteredEvents.forEach(ev => {
                if (ev.end < weekStartStr || ev.start > weekEndStr) {
                    return;
                }
                const clampedStart = ev.start < weekStartStr ? weekStartStr : ev.start;
                const clampedEnd = ev.end > weekEndStr ? weekEndStr : ev.end;

                const startColIdx = weekCells.findIndex(c => c.dateStr === clampedStart);
                const endColIdx = weekCells.findIndex(c => c.dateStr === clampedEnd);
                if (startColIdx === -1 || endColIdx === -1) return;

                const startCol = startColIdx + 1;
                const spanCols = Math.max(1, endColIdx - startColIdx + 1);

                weekSegments.push({
                    ev: ev,
                    startCol: startCol,
                    endCol: endColIdx + 1,
                    spanCols: spanCols,
                    continuesLeft: ev.start < weekStartStr,
                    continuesRight: ev.end > weekEndStr
                });
            });

            weekSegments.sort((a, b) => {
                if (a.startCol !== b.startCol) return a.startCol - b.startCol;
                if (b.spanCols !== a.spanCols) return b.spanCols - a.spanCols;
                return String(a.ev.start).localeCompare(String(b.ev.start));
            });

            const lanes = [];
            weekSegments.forEach(seg => {
                let placedLane = -1;
                for (let l = 0; l < lanes.length; l++) {
                    const lastInLane = lanes[l][lanes[l].length - 1];
                    if (lastInLane.endCol < seg.startCol) {
                        placedLane = l;
                        break;
                    }
                }
                if (placedLane === -1) {
                    placedLane = lanes.length;
                    lanes.push([]);
                }
                seg.lane = placedLane + 1;
                lanes[placedLane].push(seg);
            });

            html += '<div class="ext-cal-week-row">';

            // 1. Background 7-Column Grid
            html += '<div class="ext-cal-week-bg">';
            weekCells.forEach(c => {
                const bgClasses = [
                    'ext-cal-bg-cell',
                    c.isOutside ? 'is-outside' : '',
                    c.isWeekend ? 'is-weekend' : '',
                    c.isToday ? 'is-today' : ''
                ].filter(Boolean).join(' ');
                html += '<div class="' + bgClasses + '"></div>';
            });
            html += '</div>';

            // 2. Day Numbers Header Row
            html += '<div class="ext-cal-week-day-headers">';
            weekCells.forEach(c => {
                const activeOnDay = filteredEvents.filter(ev => c.dateStr >= ev.start && c.dateStr <= ev.end).length;
                html += '<div class="ext-cal-day-header-cell ' + (c.isOutside ? 'is-outside' : '') + '">';
                html += '<div class="d-flex align-items-center gap-1">';
                html += '<span class="ext-cal-day-num ' + (c.isToday ? 'is-today' : '') + '">' + c.dayNum + '</span>';
                if (c.dayNum === 1 || (w === 0 && c === weekCells[0])) {
                    html += '<span class="text-muted extra-small fw-bold">' + c.monthShort + '</span>';
                }
                if (c.isToday) {
                    html += '<span class="badge bg-success-subtle text-success border border-success-subtle font-mono" style="font-size: 8.5px; padding: 1px 4px;">TODAY</span>';
                }
                html += '</div>';
                if (activeOnDay > 0) {
                    html += '<span class="badge bg-light text-secondary border font-mono" style="font-size: 9px; padding: 1px 5px;" title="' + activeOnDay + ' active ranges on this date">' + activeOnDay + '</span>';
                }
                html += '</div>';
            });
            html += '</div>';

            // 3. Foreground Continuous Multi-Day Range Bars Layer
            html += '<div class="ext-cal-week-lanes">';
            weekSegments.forEach(seg => {
                const ev = seg.ev;
                const barClasses = [
                    'ext-cal-range-bar',
                    seg.continuesLeft ? 'continues-left' : '',
                    seg.continuesRight ? 'continues-right' : ''
                ].filter(Boolean).join(' ');

                const rangeText = ev.range_label || (ev.start_formatted + ' – ' + ev.end_formatted);
                const tooltipText = '[' + ev.code + '] ' + ev.short_title + ' • Range: ' + rangeText + ' (' + ev.duration_label + ')';

                html += '<div class="' + barClasses + '" '
                    + 'style="grid-column: ' + seg.startCol + ' / span ' + seg.spanCols + '; grid-row: ' + seg.lane + '; background-color: ' + ev.color + ';" '
                    + 'onclick="window.openCalendarEventModal(' + ev._idx + ')" '
                    + 'title="' + tooltipText.replace(/"/g, '&quot;') + '">';

                html += '<div class="ext-cal-range-bar-main">';
                if (seg.continuesLeft) {
                    html += '<i class="mdi mdi-chevron-double-left opacity-85 flex-shrink-0"></i>';
                } else {
                    html += '<i class="mdi ' + (ev.icon || 'mdi-calendar-range') + ' flex-shrink-0"></i>';
                }
                if (seg.spanCols >= 2) {
                    html += '<span class="ext-cal-range-code">' + ev.code + '</span>';
                }
                html += '<span class="ext-cal-range-title">' + ev.short_title + '</span>';
                html += '</div>';

                html += '<div class="d-flex align-items-center gap-1 flex-shrink-0">';
                if (seg.spanCols >= 3) {
                    html += '<span class="ext-cal-range-pill">' + rangeText + '</span>';
                }
                if (seg.continuesRight) {
                    html += '<i class="mdi mdi-chevron-double-right opacity-85"></i>';
                }
                html += '</div>';

                html += '</div>';
            });
            html += '</div>';

            html += '</div>';
        }

        gridBody.innerHTML = html;
    };

    window.navigateCalendarMonth = function (delta) {
        window.EXT_CAL_MONTH += delta;
        if (window.EXT_CAL_MONTH < 0) {
            window.EXT_CAL_MONTH = 11;
            window.EXT_CAL_YEAR--;
        } else if (window.EXT_CAL_MONTH > 11) {
            window.EXT_CAL_MONTH = 0;
            window.EXT_CAL_YEAR++;
        }
        window.renderExtensionCalendarGrid();
    };

    window.jumpCalendarToToday = function () {
        const now = new Date();
        window.EXT_CAL_YEAR = now.getFullYear();
        window.EXT_CAL_MONTH = now.getMonth();
        window.renderExtensionCalendarGrid();
    };

    window.jumpCalendarToSelected = function () {
        const mSel = document.getElementById('calMonthSelect');
        const ySel = document.getElementById('calYearSelect');
        if (mSel) window.EXT_CAL_MONTH = parseInt(mSel.value, 10);
        if (ySel) window.EXT_CAL_YEAR = parseInt(ySel.value, 10);
        window.renderExtensionCalendarGrid();
    };

    window.filterCalendarByCategory = function (cat, btn) {
        window.EXT_CAL_ACTIVE_CAT = cat;
        document.querySelectorAll('#calendarCategoryFilterGroup .ext-cal-cat-pill').forEach(el => el.classList.remove('active'));
        if (btn) btn.classList.add('active');

        window.renderExtensionCalendarGrid();

        document.querySelectorAll('.cal-ledger-row').forEach(row => {
            if (cat === 'all' || row.getAttribute('data-category') === cat) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    };

    window.switchCalendarViewMode = function (mode, btn) {
        if (btn && btn.parentElement) {
            btn.parentElement.querySelectorAll('.pm-segment-pill').forEach(el => el.classList.remove('active'));
            btn.classList.add('active');
        }
        document.getElementById('viewPaneCalendar').style.display = mode === 'calendar' ? '' : 'none';
        document.getElementById('viewPaneGantt').style.display = mode === 'gantt' ? '' : 'none';
        document.getElementById('viewPaneList').style.display = mode === 'list' ? '' : 'none';
        const navToolbar = document.getElementById('calendarMonthNavToolbar');
        if (navToolbar) {
            navToolbar.style.opacity = mode === 'calendar' ? '1' : '0.45';
        }
    };

    window.toggleMaximizeCalendar = function () {
        const wrapper = document.getElementById('extCalWorkspaceWrapper');
        const icon = document.getElementById('iconMaximizeCal');
        const label = document.getElementById('labelMaximizeCal');
        if (!wrapper) return;

        const isMax = wrapper.classList.toggle('is-maximized');
        if (icon) {
            icon.className = isMax ? 'mdi mdi-fullscreen-exit' : 'mdi mdi-fullscreen';
        }
        if (label) {
            label.textContent = isMax ? 'Exit Maximize' : 'Maximize';
        }
    };

    window.openCalendarEventModal = function (idx) {
        const ev = (window.EXT_CALENDAR_EVENTS || [])[idx];
        if (!ev) return;

        document.getElementById('calModalHeader').style.backgroundColor = ev.color || '#2563eb';
        document.getElementById('calModalCategory').textContent = (ev.category_label || 'EXTENSION MILESTONE').toUpperCase();
        document.getElementById('calModalTitle').textContent = ev.short_title || ev.title;
        document.getElementById('calModalCode').textContent = ev.code + ' • ' + ev.doc_type;
        document.getElementById('calModalStatus').textContent = ev.status_label;
        document.getElementById('calModalStart').textContent = ev.start_formatted;
        document.getElementById('calModalEnd').textContent = ev.end_formatted;
        document.getElementById('calModalDuration').textContent = ev.duration_label;
        document.getElementById('calModalBudget').textContent = ev.budget_formatted;
        document.getElementById('calModalCampus').textContent = ev.campus;
        document.getElementById('calModalLeader').textContent = ev.leader;
        document.getElementById('calModalOpenBtn').href = ev.url;
        document.getElementById('calModalPdfBtn').href = ev.pdf_url;

        const modalEl = document.getElementById('calendarEventModal');
        if (modalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            bootstrap.Modal.getOrCreateInstance(modalEl).show();
        }
    };

    document.addEventListener('DOMContentLoaded', function () {
        window.renderExtensionCalendarGrid();
    });
</script>
@endsection

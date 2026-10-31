@php
    /**
     * Official 17 UN Sustainable Development Goals (SDGs) Inline Tile Component
     *
     * Supported variables:
     * - $selectedSdgs: array of integer goal numbers (1..17)
     * - $indirectSdgs: optional array of integer goal numbers for tangential/indirect alignment
     * - $inputName: optional checkbox input name (default: 'sdg_alignments[selected][]')
     * - $idPrefix: optional DOM ID prefix (default: 'un_sdg')
     * - $mode: 'checkbox' (form multi-select) | 'workbench' (Part II direct/indirect) | 'readonly' (display only selected)
     */
    $mode = $mode ?? 'checkbox';
    $inputName = $inputName ?? 'sdg_alignments[selected][]';
    $idPrefix = $idPrefix ?? 'un_sdg';
    $selectedSdgs = array_map('intval', (array) ($selectedSdgs ?? [4, 9, 17]));
    $indirectSdgs = array_map('intval', (array) ($indirectSdgs ?? []));

    $officialUnSdgs = [
        1 => [
            'title' => 'NO POVERTY',
            'full_title' => 'No Poverty',
            'color' => '#E5243B',
            'svg' => '<svg viewBox="0 0 64 44" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="9" cy="11" r="3.2" fill="#fff"/>
                <path d="M5.5 16.5h7l1.2 9.5h-2v12h-2v-8.5h-1.4v8.5h-2v-12h-2l1.2-9.5z" fill="#fff"/>
                <line x1="3.5" y1="25" x2="2" y2="38" stroke="#fff" stroke-width="1.6" stroke-linecap="round"/>
                <circle cx="19" cy="17.5" r="2.4" fill="#fff"/>
                <path d="M16.5 21.5h5l1 7h-1.5v9.5h-1.6v-6h-0.8v6h-1.6V28.5h-1.5l1-7z" fill="#fff"/>
                <circle cx="29" cy="11.5" r="3.1" fill="#fff"/>
                <path d="M25.5 16.5h7l2.2 11.5h-2.5v10h-2v-7.5h-1.4v7.5h-2V28h-2.5l1.2-11.5z" fill="#fff"/>
                <circle cx="40" cy="11.5" r="3.1" fill="#fff"/>
                <path d="M36.5 16.5h7l2.2 11.5h-2.5v10h-2v-7.5h-1.4v7.5h-2V28h-2.5l1.2-11.5z" fill="#fff"/>
                <circle cx="49.5" cy="18" r="2.3" fill="#fff"/>
                <path d="M47 22h5l1 6.5h-1.5v9.5h-1.6v-6h-0.8v6h-1.6v-9.5H46l1-6.5z" fill="#fff"/>
                <circle cx="57.5" cy="11" r="3.2" fill="#fff"/>
                <path d="M54 16.5h7l1.2 9.5h-2v12h-2v-8.5h-1.4v8.5h-2v-12h-2l1.2-9.5z" fill="#fff"/>
            </svg>',
        ],
        2 => [
            'title' => 'ZERO HUNGER',
            'full_title' => 'Zero Hunger',
            'color' => '#DDA63A',
            'svg' => '<svg viewBox="0 0 64 44" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M23 6c-2.5 3.5 2.5 6.5 0 10M32 6c-2.5 3.5 2.5 6.5 0 10M41 6c-2.5 3.5 2.5 6.5 0 10" stroke="#fff" stroke-width="3" stroke-linecap="round"/>
                <path d="M13 21h38c-1.5 9.5-8.5 15.5-19 15.5S14.5 30.5 13 21z" fill="#fff"/>
                <path d="M24 38.5h16" stroke="#fff" stroke-width="3.2" stroke-linecap="round"/>
            </svg>',
        ],
        3 => [
            'title' => 'GOOD HEALTH AND WELL-BEING',
            'full_title' => 'Good Health and Well-Being',
            'color' => '#4C9F38',
            'svg' => '<svg viewBox="0 0 64 44" fill="none" xmlns="http://www.w3.org/2000/svg">
                <polyline points="4,25 18,25 23,13 29,34 36,6 42,25 47,25" stroke="#fff" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                <path d="M53.5 18.5c-1.8-2.2-5-2.2-6.8 0-1.8-2.2-5-2.2-6.8 0-2 2.4-1.6 6 0.8 8.3l6 5.7 6-5.7c2.4-2.3 2.8-5.9 0.8-8.3z" fill="#fff"/>
            </svg>',
        ],
        4 => [
            'title' => 'QUALITY EDUCATION',
            'full_title' => 'Quality Education',
            'color' => '#C5192D',
            'svg' => '<svg viewBox="0 0 64 44" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M10 9l18 4.5v22L10 31V9zm36 0L28 13.5v22L46 31V9z" fill="#fff"/>
                <path d="M8 12v21l20 4.8 20-4.8V12" stroke="#fff" stroke-width="2.4" stroke-linejoin="round" fill="none"/>
                <rect x="53" y="8" width="3.8" height="24" rx="1" fill="#fff"/>
                <polygon points="53,33.5 56.8,33.5 54.9,38.5" fill="#fff"/>
            </svg>',
        ],
        5 => [
            'title' => 'GENDER EQUALITY',
            'full_title' => 'Gender Equality',
            'color' => '#FF3A21',
            'svg' => '<svg viewBox="0 0 64 44" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="30" cy="18" r="10.5" stroke="#fff" stroke-width="3.6" fill="none"/>
                <path d="M38 10l8-7m0 0h-6.5m6.5 0v6.5" stroke="#fff" stroke-width="3.4" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M30 28.5V41m-5.5-5H35.5" stroke="#fff" stroke-width="3.6" stroke-linecap="round"/>
                <line x1="25" y1="16" x2="35" y2="16" stroke="#fff" stroke-width="3" stroke-linecap="round"/>
                <line x1="25" y1="20.5" x2="35" y2="20.5" stroke="#fff" stroke-width="3" stroke-linecap="round"/>
            </svg>',
        ],
        6 => [
            'title' => 'CLEAN WATER AND SANITATION',
            'full_title' => 'Clean Water and Sanitation',
            'color' => '#26BDE2',
            'svg' => '<svg viewBox="0 0 64 44" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M18 4h28l-3.8 26.5H21.8L18 4z" stroke="#fff" stroke-width="2.6" fill="none"/>
                <path d="M19.5 11.5c4-2 8 2 12.5 0s8.5 2 12.5 0L42 30.5H22l-2.5-19z" fill="#fff"/>
                <path d="M32 16c-3.2 4.5-4.8 7-4.8 9.2a4.8 4.8 0 009.6 0c0-2.2-1.6-4.7-4.8-9.2z" fill="#26BDE2"/>
                <path d="M32 30.5v9m0 0l-4.5-4.5M32 39.5l4.5-4.5" stroke="#fff" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>',
        ],
        7 => [
            'title' => 'AFFORDABLE AND CLEAN ENERGY',
            'full_title' => 'Affordable and Clean Energy',
            'color' => '#FCC30B',
            'svg' => '<svg viewBox="0 0 64 44" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="32" cy="22" r="10.5" fill="#fff"/>
                <path d="M32 15v7" stroke="#FCC30B" stroke-width="2.8" stroke-linecap="round"/>
                <path d="M27.8 17.8a6.2 6.2 0 108.4 0" stroke="#FCC30B" stroke-width="2.6" stroke-linecap="round" fill="none"/>
                <path d="M32 4v4.5M32 35.5V40M14 22h4.5M45.5 22H50M19.3 9.3l3.2 3.2M41.5 31.5l3.2 3.2M44.7 9.3l-3.2 3.2M22.5 31.5l-3.2 3.2" stroke="#fff" stroke-width="2.8" stroke-linecap="round"/>
            </svg>',
        ],
        8 => [
            'title' => 'DECENT WORK AND ECONOMIC GROWTH',
            'full_title' => 'Decent Work and Economic Growth',
            'color' => '#A21942',
            'svg' => '<svg viewBox="0 0 64 44" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M14 23l11-10 8 7 15-14m0 0h-7m7 0v7" stroke="#fff" stroke-width="3.4" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                <rect x="15" y="27" width="8" height="12" fill="#fff"/>
                <rect x="27" y="22" width="8" height="17" fill="#fff"/>
                <rect x="39" y="15" width="8" height="24" fill="#fff"/>
            </svg>',
        ],
        9 => [
            'title' => 'INDUSTRY, INNOVATION AND INFRASTRUCTURE',
            'full_title' => 'Industry, Innovation and Infrastructure',
            'color' => '#FD6925',
            'svg' => '<svg viewBox="0 0 64 44" fill="none" xmlns="http://www.w3.org/2000/svg">
                <polygon points="32,5 42,10.5 32,16 22,10.5" fill="#fff" stroke="#FD6925" stroke-width="1.2"/>
                <polygon points="22,10.5 32,16 32,27 22,21.5" fill="#fff" stroke="#FD6925" stroke-width="1.2"/>
                <polygon points="42,10.5 32,16 32,27 42,21.5" fill="none" stroke="#fff" stroke-width="2.2"/>
                <polygon points="22,21.5 32,27 22,32.5 12,27" fill="#fff" stroke="#FD6925" stroke-width="1.2"/>
                <polygon points="12,27 22,32.5 22,41 12,35.5" fill="none" stroke="#fff" stroke-width="2.2"/>
                <polygon points="32,27 22,32.5 22,41 32,35.5" fill="#fff" stroke="#FD6925" stroke-width="1.2"/>
                <polygon points="42,21.5 52,27 42,32.5 32,27" fill="#fff" stroke="#FD6925" stroke-width="1.2"/>
                <polygon points="32,27 42,32.5 42,41 32,35.5" fill="none" stroke="#fff" stroke-width="2.2"/>
                <polygon points="52,27 42,32.5 42,41 52,35.5" fill="#fff" stroke="#FD6925" stroke-width="1.2"/>
            </svg>',
        ],
        10 => [
            'title' => 'REDUCED INEQUALITIES',
            'full_title' => 'Reduced Inequalities',
            'color' => '#DD1367',
            'svg' => '<svg viewBox="0 0 64 44" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="24" y="16.5" width="16" height="4" fill="#fff"/>
                <rect x="24" y="23.5" width="16" height="4" fill="#fff"/>
                <polygon points="32,4 25,11 39,11" fill="#fff"/>
                <polygon points="32,40 25,33 39,33" fill="#fff"/>
                <polygon points="11,22 18,15 18,29" fill="#fff"/>
                <polygon points="53,22 46,15 46,29" fill="#fff"/>
            </svg>',
        ],
        11 => [
            'title' => 'SUSTAINABLE CITIES AND COMMUNITIES',
            'full_title' => 'Sustainable Cities and Communities',
            'color' => '#FD9D24',
            'svg' => '<svg viewBox="0 0 64 44" fill="none" xmlns="http://www.w3.org/2000/svg">
                <polygon points="10,25 16,18 22,25 22,39 10,39" fill="#fff"/>
                <rect x="14.5" y="31" width="3" height="8" fill="#FD9D24"/>
                <rect x="24" y="7" width="12" height="32" fill="#fff"/>
                <rect x="26.5" y="10" width="2.5" height="3" fill="#FD9D24"/><rect x="31" y="10" width="2.5" height="3" fill="#FD9D24"/>
                <rect x="26.5" y="15.5" width="2.5" height="3" fill="#FD9D24"/><rect x="31" y="15.5" width="2.5" height="3" fill="#FD9D24"/>
                <rect x="26.5" y="21" width="2.5" height="3" fill="#FD9D24"/><rect x="31" y="21" width="2.5" height="3" fill="#FD9D24"/>
                <rect x="26.5" y="26.5" width="2.5" height="3" fill="#FD9D24"/><rect x="31" y="26.5" width="2.5" height="3" fill="#FD9D24"/>
                <polygon points="38,18 47,12 47,39 38,39" fill="#fff"/>
                <rect x="49" y="23" width="8" height="16" fill="#fff"/>
            </svg>',
        ],
        12 => [
            'title' => 'RESPONSIBLE CONSUMPTION AND PRODUCTION',
            'full_title' => 'Responsible Consumption and Production',
            'color' => '#BF8B2E',
            'svg' => '<svg viewBox="0 0 64 44" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M26 26c-3 3.5-5.5 5-9 5a9 9 0 110-18c4.2 0 7.2 2.8 11 7.5l8 9.5c3.8 4.5 6.8 1 9 1a9 9 0 100-18c-3.5 0-6 1.5-9 5" stroke="#fff" stroke-width="4.2" stroke-linecap="round" fill="none"/>
                <polygon points="22,22 29,24 24,30" fill="#fff"/>
            </svg>',
        ],
        13 => [
            'title' => 'CLIMATE ACTION',
            'full_title' => 'Climate Action',
            'color' => '#3F7E44',
            'svg' => '<svg viewBox="0 0 64 44" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M5 22C13 10 22 7 32 7s19 3 27 15c-8 12-17 15-27 15S13 34 5 22z" fill="#fff"/>
                <circle cx="32" cy="22" r="10.5" fill="#3F7E44"/>
                <path d="M27 15c2 1 4 0 5 2s-1 4 1 6 4 1 5 4-2 5-4 5-5-3-7-6-2-8 0-11z" fill="#fff"/>
            </svg>',
        ],
        14 => [
            'title' => 'LIFE BELOW WATER',
            'full_title' => 'Life Below Water',
            'color' => '#0A97D9',
            'svg' => '<svg viewBox="0 0 64 44" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 9c4-3 8 3 12 0s8 3 12 0 8 3 12 0M12 15c4-3 8 3 12 0s8 3 12 0 8 3 12 0" stroke="#fff" stroke-width="2.8" stroke-linecap="round" fill="none"/>
                <path d="M21 29c6-7 16-9 25-4 4 2.2 6 4 6 4s-2 1.8-6 4c-9 5-19 3-25-4zm0 0l-8-6v12l8-6z" fill="#fff"/>
                <circle cx="44" cy="28" r="1.6" fill="#0A97D9"/>
            </svg>',
        ],
        15 => [
            'title' => 'LIFE ON LAND',
            'full_title' => 'Life on Land',
            'color' => '#56C02B',
            'svg' => '<svg viewBox="0 0 64 44" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="24" cy="16" r="8" fill="#fff"/>
                <circle cx="19" cy="20" r="5.5" fill="#fff"/>
                <circle cx="29" cy="20" r="5.5" fill="#fff"/>
                <rect x="22.2" y="23" width="3.6" height="9" fill="#fff"/>
                <path d="M38 11c2-2 4.5-1.5 5.5 0.5 1-2 3.5-2.5 5.5-0.5M43 18c1.8-1.5 3.8-1 4.6 0.5 0.8-1.5 2.8-2 4.6-0.5" stroke="#fff" stroke-width="2.2" stroke-linecap="round" fill="none"/>
                <line x1="12" y1="35" x2="52" y2="35" stroke="#fff" stroke-width="2.8" stroke-linecap="round"/>
                <line x1="12" y1="39" x2="52" y2="39" stroke="#fff" stroke-width="2.8" stroke-linecap="round"/>
            </svg>',
        ],
        16 => [
            'title' => 'PEACE, JUSTICE AND STRONG INSTITUTIONS',
            'full_title' => 'Peace, Justice and Strong Institutions',
            'color' => '#00689D',
            'svg' => '<svg viewBox="0 0 64 44" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M18 14c5 6 11 7 16 4 3-2 6-6 9-5 1 3-1 7-4 10 3 0 6 2 6 2s-5 4-12 4c-6 0-12-3-15-9-1-2 0-6 0-6z" fill="#fff"/>
                <path d="M43 13l5-5m-2 1l3 1m-4 2l1 3" stroke="#fff" stroke-width="1.8" stroke-linecap="round"/>
                <rect x="16" y="34" width="24" height="2.8" rx="1" fill="#fff"/>
                <rect x="38" y="31" width="7" height="8" rx="1" fill="#fff"/>
            </svg>',
        ],
        17 => [
            'title' => 'PARTNERSHIPS FOR THE GOALS',
            'full_title' => 'Partnerships for the Goals',
            'color' => '#19486A',
            'svg' => '<svg viewBox="0 0 64 44" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="32" cy="14" r="6.5" stroke="#fff" stroke-width="2.4" fill="none"/>
                <circle cx="39.5" cy="19.5" r="6.5" stroke="#fff" stroke-width="2.4" fill="none"/>
                <circle cx="36.5" cy="28" r="6.5" stroke="#fff" stroke-width="2.4" fill="none"/>
                <circle cx="27.5" cy="28" r="6.5" stroke="#fff" stroke-width="2.4" fill="none"/>
                <circle cx="24.5" cy="19.5" r="6.5" stroke="#fff" stroke-width="2.4" fill="none"/>
            </svg>',
        ],
    ];
@endphp

@if($mode === 'readonly')
    <div class="un-sdg-inline-wrap">
        @php
            $hasAnySdg = false;
        @endphp
        @foreach($officialUnSdgs as $num => $sdg)
            @php
                $isDirect = in_array($num, $selectedSdgs, true);
                $isIndirect = in_array($num, $indirectSdgs, true);
            @endphp
            @if($isDirect || $isIndirect)
                @php $hasAnySdg = true; @endphp
                <div class="un-sdg-tile is-readonly {{ $isDirect ? 'is-selected' : 'is-indirect' }}"
                     style="--sdg-color: {{ $sdg['color'] }};"
                     title="SDG {{ $num }}: {{ $sdg['full_title'] }}{{ $isIndirect ? ' (Indirect Alignment)' : '' }}">
                    <span class="un-sdg-tile-check"><i class="mdi mdi-check"></i></span>
                    <span class="un-sdg-tile-indirect-badge">Indirect</span>
                    <div class="un-sdg-tile-header">
                        <span class="un-sdg-tile-num">{{ $num }}</span>
                        <span class="un-sdg-tile-title">{{ $sdg['title'] }}</span>
                    </div>
                    <div class="un-sdg-tile-art">
                        {!! $sdg['svg'] !!}
                    </div>
                </div>
            @endif
        @endforeach
        @if(!$hasAnySdg)
            <span class="text-muted extra-small fst-italic">No UN SDGs selected yet.</span>
        @endif
    </div>
@elseif($mode === 'workbench')
    <div class="un-sdg-inline-wrap {{ !($isEditable ?? true) ? 'pe-none opacity-90' : '' }}" id="sdgGridContainer">
        @foreach($officialUnSdgs as $num => $sdg)
            @php
                $isPrimary  = in_array($num, $selectedSdgs, true);
                $isIndirect = in_array($num, $indirectSdgs, true);
            @endphp
            <div class="un-sdg-tile sdg-card-item {{ $isPrimary ? 'selected is-selected' : ($isIndirect ? 'indirect is-indirect' : '') }}"
                 data-sdg="{{ $num }}"
                 data-color="{{ $sdg['color'] }}"
                 style="--sdg-color: {{ $sdg['color'] }}; background-color: {{ $sdg['color'] }};"
                 title="SDG {{ $num }}: {{ $sdg['full_title'] }} — Left-click: Direct | Right-click: Indirect">
                <span class="un-sdg-tile-check"><i class="mdi mdi-check"></i></span>
                <span class="un-sdg-tile-indirect-badge">Indirect</span>
                <div class="un-sdg-tile-header">
                    <span class="un-sdg-tile-num">{{ $num }}</span>
                    <span class="un-sdg-tile-title">{{ $sdg['title'] }}</span>
                </div>
                <div class="un-sdg-tile-art">
                    {!! $sdg['svg'] !!}
                </div>
            </div>
        @endforeach
    </div>
@else
    {{-- Interactive Form Checkbox Selector Mode --}}
    <div class="un-sdg-selector-wrapper" data-sdg-prefix="{{ $idPrefix }}">
        <div class="un-sdg-inline-wrap">
            @foreach($officialUnSdgs as $num => $sdg)
                @php
                    $isChecked = in_array($num, $selectedSdgs, true);
                    $checkboxId = $idPrefix . '_goal_' . $num;
                @endphp
                <label for="{{ $checkboxId }}"
                       class="un-sdg-tile {{ $isChecked ? 'is-selected' : '' }}"
                       style="--sdg-color: {{ $sdg['color'] }};"
                       title="SDG {{ $num }}: {{ $sdg['full_title'] }}">
                    <input type="checkbox"
                           name="{{ $inputName }}"
                           id="{{ $checkboxId }}"
                           value="{{ $num }}"
                           class="un-sdg-tile-input"
                           data-sdg-num="{{ $num }}"
                           data-sdg-title="{{ $sdg['full_title'] }}"
                           data-sdg-color="{{ $sdg['color'] }}"
                           {{ $isChecked ? 'checked' : '' }}
                           onchange="this.closest('.un-sdg-tile').classList.toggle('is-selected', this.checked); if (window.updateUnSdgSummary) window.updateUnSdgSummary(this.closest('.un-sdg-selector-wrapper'));">
                    <span class="un-sdg-tile-check"><i class="mdi mdi-check"></i></span>
                    <div class="un-sdg-tile-header">
                        <span class="un-sdg-tile-num">{{ $num }}</span>
                        <span class="un-sdg-tile-title">{{ $sdg['title'] }}</span>
                    </div>
                    <div class="un-sdg-tile-art">
                        {!! $sdg['svg'] !!}
                    </div>
                </label>
            @endforeach
        </div>
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mt-2 pt-1">
            <div class="d-flex flex-wrap align-items-center gap-1 un-sdg-selected-pills">
                @foreach($officialUnSdgs as $num => $sdg)
                    @if(in_array($num, $selectedSdgs, true))
                        <span class="un-sdg-pill-inline" style="--sdg-color: {{ $sdg['color'] }};">
                            SDG {{ $num }}: {{ $sdg['full_title'] }}
                        </span>
                    @endif
                @endforeach
            </div>
            <span class="badge bg-light text-dark border extra-small font-mono un-sdg-count-badge">
                {{ count($selectedSdgs) }} of 17 SDGs Selected
            </span>
        </div>
    </div>

    @once
        @push('scripts')
        <script>
            window.updateUnSdgSummary = function(wrapper) {
                if (!wrapper) return;
                const checkedInputs = wrapper.querySelectorAll('.un-sdg-tile-input:checked');
                const pillsContainer = wrapper.querySelector('.un-sdg-selected-pills');
                const countBadge = wrapper.querySelector('.un-sdg-count-badge');

                if (countBadge) {
                    countBadge.textContent = checkedInputs.length + ' of 17 SDGs Selected';
                }
                if (pillsContainer) {
                    if (checkedInputs.length === 0) {
                        pillsContainer.innerHTML = '<span class="extra-small text-muted fst-italic">Click any UN SDG tile above to align this proposal.</span>';
                        return;
                    }
                    let html = '';
                    checkedInputs.forEach(function(inp) {
                        const num = inp.getAttribute('data-sdg-num');
                        const title = inp.getAttribute('data-sdg-title');
                        const color = inp.getAttribute('data-sdg-color');
                        html += '<span class="un-sdg-pill-inline" style="--sdg-color: ' + color + ';">SDG ' + num + ': ' + title + '</span>';
                    });
                    pillsContainer.innerHTML = html;
                }
            };
        </script>
        @endpush
    @endonce
@endif


@if ($paginator->hasPages())
    <nav class="d-flex flex-wrap align-items-center justify-content-between gap-2 px-3 py-2 bg-white border rounded-3 shadow-2xs" aria-label="Pagination Navigation">
        <!-- Results Summary -->
        <div class="d-flex align-items-center gap-1 extra-small text-muted font-sans py-1">
            <span>Showing</span>
            <span class="fw-bold text-dark font-mono">{{ $paginator->firstItem() }}</span>
            <span>to</span>
            <span class="fw-bold text-dark font-mono">{{ $paginator->lastItem() }}</span>
            <span>of</span>
            <span class="fw-bold text-dark font-mono">{{ $paginator->total() }}</span>
            <span>results</span>
        </div>

        <!-- Page Buttons -->
        <div class="d-flex align-items-center overflow-x-auto py-1">
            <ul class="pagination pagination-sm m-0 gap-1 list-unstyled d-flex align-items-center">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                        <span class="page-link" aria-hidden="true">&lsaquo;</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link repo-page-btn" href="{{ $paginator->previousPageUrl() }}" data-page="{{ $paginator->currentPage() - 1 }}" rel="prev" aria-label="@lang('pagination.previous')">&lsaquo;</a>
                    </li>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <li class="page-item disabled" aria-disabled="true">
                            <span class="page-link dots">{{ $element }}</span>
                        </li>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <li class="page-item active" aria-current="page">
                                    <span class="page-link">{{ $page }}</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link repo-page-btn" href="{{ $url }}" data-page="{{ $page }}">{{ $page }}</a>
                                </li>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <li class="page-item">
                        <a class="page-link repo-page-btn" href="{{ $paginator->nextPageUrl() }}" data-page="{{ $paginator->currentPage() + 1 }}" rel="next" aria-label="@lang('pagination.next')">&rsaquo;</a>
                    </li>
                @else
                    <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                        <span class="page-link" aria-hidden="true">&rsaquo;</span>
                    </li>
                @endif
            </ul>
        </div>
    </nav>
@endif


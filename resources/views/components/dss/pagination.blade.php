@if ($paginator->hasPages())
    <div class="repo-pagination-wrapper d-flex flex-wrap align-items-center justify-content-between gap-2 px-3 py-2 bg-white border rounded-3 shadow-2xs">
        <!-- Results Counter -->
        <div class="d-flex align-items-center gap-1 extra-small text-muted font-sans py-1">
            <span>Showing</span>
            <span class="fw-bold text-dark font-mono">{{ $paginator->firstItem() }}</span>
            <span>to</span>
            <span class="fw-bold text-dark font-mono">{{ $paginator->lastItem() }}</span>
            <span>of</span>
            <span class="fw-bold text-dark font-mono">{{ $paginator->total() }}</span>
            <span>documents</span>
        </div>

        <!-- Page Controls -->
        <nav aria-label="Document pagination" class="d-flex align-items-center overflow-x-auto py-1">
            <ul class="repo-pagination d-flex align-items-center list-unstyled mb-0 gap-1">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <li class="page-item disabled" aria-disabled="true" aria-label="Previous">
                        <span class="page-link" aria-hidden="true" title="Previous page">
                            <i class="mdi mdi-chevron-left fs-6"></i>
                        </span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link repo-page-btn" href="{{ $paginator->previousPageUrl() }}" data-page="{{ $paginator->currentPage() - 1 }}" rel="prev" aria-label="Previous" title="Go to previous page">
                            <i class="mdi mdi-chevron-left fs-6"></i>
                        </a>
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
                                    <span class="page-link" title="Current page {{ $page }}">{{ $page }}</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link repo-page-btn" href="{{ $url }}" data-page="{{ $page }}" title="Go to page {{ $page }}">{{ $page }}</a>
                                </li>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <li class="page-item">
                        <a class="page-link repo-page-btn" href="{{ $paginator->nextPageUrl() }}" data-page="{{ $paginator->currentPage() + 1 }}" rel="next" aria-label="Next" title="Go to next page">
                            <i class="mdi mdi-chevron-right fs-6"></i>
                        </a>
                    </li>
                @else
                    <li class="page-item disabled" aria-disabled="true" aria-label="Next">
                        <span class="page-link" aria-hidden="true" title="Next page">
                            <i class="mdi mdi-chevron-right fs-6"></i>
                        </span>
                    </li>
                @endif
            </ul>
        </nav>
    </div>
@endif

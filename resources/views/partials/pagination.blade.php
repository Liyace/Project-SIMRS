@if ($paginator->hasPages())
    <nav class="simrs-pagination-nav" role="navigation" aria-label="Navigasi Halaman">
        <div class="simrs-pagination-info">
            Menampilkan <span>{{ $paginator->firstItem() }}</span> - <span>{{ $paginator->lastItem() }}</span> dari <span>{{ $paginator->total() }}</span> antrean
        </div>
        <div class="simrs-pagination-buttons">
            {{-- Tombol Halaman Sebelumnya --}}
            @if ($paginator->onFirstPage())
                <span class="page-btn disabled" aria-disabled="true" title="Halaman pertama">
                    <i class="fa-solid fa-chevron-left"></i>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="page-btn" title="Halaman sebelumnya">
                    <i class="fa-solid fa-chevron-left"></i>
                </a>
            @endif

            {{-- Elemen Nomor Halaman --}}
            @foreach ($elements as $element)
                {{-- Separator Tiga Titik --}}
                @if (is_string($element))
                    <span class="page-btn dots" aria-disabled="true">{{ $element }}</span>
                @endif

                {{-- Link Halaman --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="page-btn active" aria-current="page">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="page-btn">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Tombol Halaman Berikutnya --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="page-btn" title="Halaman berikutnya">
                    <i class="fa-solid fa-chevron-right"></i>
                </a>
            @else
                <span class="page-btn disabled" aria-disabled="true" title="Halaman terakhir">
                    <i class="fa-solid fa-chevron-right"></i>
                </span>
            @endif
        </div>
    </nav>
@endif

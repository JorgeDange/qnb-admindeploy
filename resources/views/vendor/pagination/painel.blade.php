@if($paginator->hasPages())
    <nav class="ul-painel-paginacao" aria-label="Paginação">
        {{-- ‹ Anterior --}}
        @if($paginator->onFirstPage())
            <span class="ul-painel-paginacao-seta" aria-hidden="true">&lsaquo;</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="ul-painel-paginacao-seta" rel="prev" aria-label="Página anterior">&lsaquo;</a>
        @endif

        {{-- Páginas e reticências --}}
        @foreach($elements as $element)
            {{-- "..." --}}
            @if(is_string($element))
                <span class="ul-painel-paginacao-ellipsis">{{ $element }}</span>
            @endif

            {{-- Números --}}
            @if(is_array($element))
                @foreach($element as $page => $url)
                    @if($page == $paginator->currentPage())
                        <span class="ul-painel-paginacao-item ul-painel-paginacao-item--ativo" aria-current="page">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="ul-painel-paginacao-item">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Próxima › --}}
        @if($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="ul-painel-paginacao-seta" rel="next" aria-label="Página seguinte">&rsaquo;</a>
        @else
            <span class="ul-painel-paginacao-seta ul-painel-paginacao-seta--desativada" aria-hidden="true">&rsaquo;</span>
        @endif
    </nav>
@endif

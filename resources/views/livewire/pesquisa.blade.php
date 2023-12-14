<div>
    @if(request()->is('pesquisa/*'))
    @else
    <form wire:submit.prevent="pesquisa"  class="search">
              
        <input autocomplete="off" type="text" wire:model="termo" class="searchinput" placeholder="Pesquisar..." aria-label="Pesquisar" >
        <button type="button" class="searchbutton">
        <i class="ri-search-2-line"></i>   
       </button>
    </form>
    @endif
    
    @if(!empty($resultados))
    <a href="javascript:history.back()" class="back-link text-primary">
        <i class="ri-arrow-left-line ri-lg arrow-icon" style="color: white;"></i>
    </a>
    <div id="teste" class="container mb-1">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="h3 text-white mb-0 d-inline">Resultados</h3>
            </div>
        </div>
    </div>
  
    <div class="container mb-4">
        <div class="row">
            @foreach($resultados as $resultado)
                @if(isset($resultado['poster_path']))
                    @php
                        $routeName = isset($resultado['title']) ? 'filmes.show' : 'series.show';
                        $routeParameters = [
                        'filmeId' => isset($resultado['title']) ? $resultado['id'] : null,
                        'serieId' => isset($resultado['name']) ? $resultado['id'] : null,
                    ];
                    @endphp

                    <div class="col-6 col-md-3">
                        <a href="{{ route($routeName, $routeParameters) }}" wire:navigate>
                            <img src="https://image.tmdb.org/t/p/original/{{ $resultado['poster_path'] }}" alt="{{ $resultado['title'] ?? $resultado['name'] }}" 
                            class="img-fluid rounded mb-4">
                        </a>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
    @endif
</div>

<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\HistoricoVisualizacao;
use Illuminate\Support\Facades\Cache;

class SerieDetail extends Component
{
    public $serie;
    public $seasonCount;
    public $user;
    public $isFavorite;

    public function mount($serieId)
    {
        $apiKey = env('TMDB_API_KEY');
    
        // Chave única para este cache com base no ID da série
        $cacheKey = "serie_{$serieId}";
    
        $this->serie = Cache::remember($cacheKey, now()->addHours(2), function () use ($apiKey, $serieId) {
            $response = Http::get("https://api.themoviedb.org/3/tv/{$serieId}?api_key={$apiKey}&language=pt-BR");
            $serieData = $response->json();
    
            return [
                'name' => $serieData['name'],
                'id' => $serieData['id'],
                'overview' => $serieData['overview'],
                'vote_average' => $serieData['vote_average'],
                'backdrop_path' => $serieData['backdrop_path'],
                'seasons' => $serieData['seasons'],
            ];
        });
    
        $this->user = Auth::user();
        $this->seasonCount = count($this->serie['seasons']);
    
    

        if (auth()->check()) {
            $user = auth()->user();
            $serieId = $this->serie['id'];
            $this->isFavorite = $user->favoriteSeries()->where('serie_id', $serieId)->exists();
        }

        $this->addToHistory();
    }
    
public function addToFavorites()
{
    if (auth()->check()) {
        $user = auth()->user();
        $serieId = $this->serie['id'];

        // Verifique se o filme já está na lista de favoritos do usuário
        $isFavorite = $user->favoriteSeries()->where('serie_id', $serieId)->exists();

        if ($isFavorite) {
            // Remova dos favoritos
            $user->favoriteSeries()->where('serie_id', $serieId)->delete();
        } else {
            // Adicione aos favoritos
            $user->favoriteSeries()->create([
                'serie_id' => $serieId,
            ]);
        }

        // Atualize o status de favoritos
        $this->isFavorite = !$isFavorite;
    }
     
}
public function addToHistory()
{
    if (auth()->check()) {
        $user = auth()->user();
        $serieId = $this->serie['id'];

        // Verifique se o filme já está no histórico de visualização do usuário
        $historicoDoUsuario = HistoricoVisualizacao::where('user_id', $user->id)
            ->where('media_id', $serieId)
            ->where('media_type', 'serie')
            ->exists();

        if (!$historicoDoUsuario) {
            // Adicione o filme ao histórico de visualização
            HistoricoVisualizacao::create([
                'user_id' => $user->id,
                'media_id' => $serieId,
                'media_type' => 'serie',
            ]);
        }
    }
}

    public function render()
    {
        $this->seasonCount = count($this->serie['seasons']);
        return view('livewire.serie-detail');
      
     
    }
}

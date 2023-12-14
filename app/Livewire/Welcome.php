<?php

namespace App\Livewire;

use Livewire\Component;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\UserSearchTerm; // Importe o modelo UserSearchTerm
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB; 

class Welcome extends Component
{
    

    private function getFrequentSearchTerms($user, $limit = 5)
    {
        // Verifique se o usuário está autenticado
        if (Auth::check()) {
            // Verifique se existem registros na tabela user_search_terms para o usuário
            if (UserSearchTerm::where('user_id', $user->id)->exists()) {
                // O usuário está autenticado e há registros na tabela, podemos continuar com a consulta
                $frequentTerms = UserSearchTerm::where('user_id', $user->id)
                    ->groupBy('search_term')
                    ->select('search_term', DB::raw('count(*) as total'))
                    ->orderByDesc(DB::raw('MAX(created_at)')) 
                    ->pluck('search_term');
    
                return $frequentTerms;
            } else {

                return [];
            }
        } else {
 
            return [];
        }
    }


  
    public function render()
    {
        
         $apiKey = env('TMDB_API_KEY');
         $language = 'pt-BR';
 
       
    $filmes = Cache::remember('filmes_cache_key', now()->addHours(2), function () use ($apiKey, $language) {
        $apiUrl = "https://api.themoviedb.org/3/movie/popular?api_key={$apiKey}&language={$language}&certification_country=BR&certification.lte=12&page=1";
        return $this->fetchCarouselData($apiUrl);
    });

  
    $series = Cache::remember('series_cache_key', now()->addHours(2), function () use ($apiKey, $language) {
        $disneyPlusSeries = $this->fetchCarouselData(
            "https://api.themoviedb.org/3/discover/tv?api_key={$apiKey}&language={$language}&with_networks=2739"
        );

        $netflixSeries = $this->fetchCarouselData(
            "https://api.themoviedb.org/3/discover/tv?api_key={$apiKey}&language={$language}&with_networks=213"
        );

        return array_merge($disneyPlusSeries, $netflixSeries);
    });

  
    $documentarios = Cache::remember('documentarios_cache_key', now()->addHours(2), function () use ($apiKey, $language) {
        $apiUrl = "https://api.themoviedb.org/3/discover/movie?api_key={$apiKey}&with_genres=99&language={$language}&certification_country=BR&certification.lte=12";
        return $this->fetchCarouselData($apiUrl);
    });
    

 $animes = Cache::remember('animes_cache_key', now()->addHours(2), function () use ($apiKey, $language) {
    $apiUrl = "https://api.themoviedb.org/3/discover/tv?api_key={$apiKey}&language={$language}&with_genres=16&certification_country=BR&certification.lte=12";
    return $this->fetchCarouselData($apiUrl);
});

        $user = Auth::user();
        

   
        $frequentSearchTerms = $this->getFrequentSearchTerms($user);
       

       

        return view('livewire.welcome', compact('frequentSearchTerms','user', 'series', 'filmes', 'documentarios', 'animes'));
        
    }

     
      private function fetchCarouselData($apiUrl)
      {
          $response = Http::get($apiUrl);
          $data = $response->json();
  
      
  
          return $data['results'] ?? [];
      }

  

}



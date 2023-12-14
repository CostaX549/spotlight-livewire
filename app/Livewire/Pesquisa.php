<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\UserSearchTerm; // Importe o modelo UserSearchTerm
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Pesquisa extends Component
{
    public $termo;



    public function pesquisa() {
     
        return $this->redirect('/pesquisa/'.$this->termo, navigate:true);
    }


    public function render() {
        $user = Auth::user();
        $resultados = [];
        $recommendedMovieIds = [];
        
        if ($this->termo) {
            $termo = $this->termo;
            $apiKey = env('TMDB_API_KEY');

            // Consulta de cache
           $resultados = Cache::remember('search_results_'.$termo, now()->addHours(1), function () use ($termo, $apiKey) {
            $response = Http::get("https://api.themoviedb.org/3/search/multi", [
                'api_key' => $apiKey,
                'language' => 'pt-BR',
                'query' => $termo,
                'certification_country' => 'BR',
                'certification.lte' => '16'
            ]);

            $data = $response->json();

            if (!empty($data['results'])) {
                $unwantedMovieIds = [617932]; // Substitua pelos IDs dos filmes indesejados
                
                // Filtrar os resultados, excluindo os filmes indesejados pelo ID e pela popularidade
                $resultados = array_filter($data['results'], function ($result) use ($unwantedMovieIds) {
                    $isUnwanted = in_array($result['id'], $unwantedMovieIds);
                    $isPopular = $result['popularity'] > 5; // Ajuste o valor da popularidade conforme necessário
                    return !$isUnwanted && $isPopular;
                });
                
         
           $termoPesquisa = strtolower($termo);
          $resultados = array_values(array_filter($resultados, function ($result) use ($termoPesquisa) {
            return str_contains(strtolower($result['title'] ?? $result['name']), $termoPesquisa);
        }));
                
                return $resultados; // Retorna os resultados filtrados
            }
            
            return []; // Retorna um array vazio se não houver resultados
        });
        if (!empty($resultados)) {
            // Salvar o termo de pesquisa do usuário
            if (Auth::check()) {
                $searchTerm = $this->termo;
                UserSearchTerm::create([
                    'user_id' => $user->id,
                    'search_term' => $searchTerm,
                ]);
        
                // Calcular a frequência dos termos de pesquisa
                $termFrequencies = UserSearchTerm::where('user_id', $user->id)
                    ->select('search_term', \DB::raw('count(*) as count'))
                    ->groupBy('search_term')
                    ->orderBy('count', 'desc')
                    ->get();
            }
        }
  
    } 
        return view('livewire.pesquisa', compact('resultados'));
    }
}

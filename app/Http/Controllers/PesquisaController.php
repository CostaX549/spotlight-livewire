<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\UserSearchTerm; // Importe o modelo UserSearchTerm
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB; // Importe a classe DB


class PesquisaController extends Controller
{
   public function index() {
    return view ('welcome');
   }

  

}

  



    


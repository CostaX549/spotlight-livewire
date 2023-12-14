<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;

class Login extends Component
{
    
    #[Layout('layouts.login')] 
    public function render()
    {
        return view('livewire.login');
      
    }
}

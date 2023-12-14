<?php


namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;

class Register extends Component
{
    
    #[Layout('layouts.login')] 
    public function render()
    {
        return view('livewire.register');
    }
}

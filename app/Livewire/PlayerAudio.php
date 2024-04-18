<?php

namespace App\Livewire;

use Livewire\Component;

class PlayerAudio extends Component
{
    public $path = '';
    public $id;

    public function mount($path , $id)
    {
        $this->path = $path;
        $this->id = $id;
    }

    public function render()
    {
        return view('livewire.player-audio');
    }
}

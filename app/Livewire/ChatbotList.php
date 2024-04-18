<?php

namespace App\Livewire;

use App\Models\Chat;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;

class ChatbotList extends Component
{
    #[Url(as: 'c')] 
    public $chat_id = null;
    
    public $chats = [];

    public function mount()
    {
        $this->chats = Chat::all()->reverse();
    }

    public function selectChat($chat_id)
    {
        if(route('chat.index') == request()->url()){
            $this->chat_id = $chat_id;
            $this->dispatch('chatbot:select-chat', $chat_id);
        }else{
            return redirect()->route('chat.index', ['c' => $chat_id]);
        }
    }

    public function newChat(){
        if(route('chat.index') == request()->url()){
            $this->chat_id = null;
            $this->dispatch('chatbot:select-chat' , null);
        }else{
            return redirect()->route('chat.index');
        }
    }

    #[On('chatbot:select-chat')]
    public function changeChat($chat_id)
    {
        $this->chat_id = $chat_id;
        $this->chats = Chat::all()->reverse();
    } 
    
    public function render()
    {
        return view('livewire.chatbot-list');
    }
}

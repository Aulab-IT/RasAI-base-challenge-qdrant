<?php

namespace App\Livewire;

use App\Models\Chat;
use Livewire\Component;
use Livewire\Attributes\Url;
use App\Services\EmbeddingService;
use Illuminate\Support\Facades\DB;
use OpenAI\Laravel\Facades\OpenAI;

class Chatbot extends Component
{
    #[Url(as: 'c')] 
    public $chat_id = null;

    public $chat = null;
    public $messages = [];
    public $message = '';

    public function mount()
    {   
        if($this->chat_id){
            $this->chat = Chat::find($this->chat_id);
            $this->messages = $this->chat->messages;
        }
    }

    public function ask()
    {
        // $this->validate([
        //     'message' => 'required'
        // ]);
        if(!$this->chat){
            $this->createChat();
        }
        
        $this->chat->messages()->create([
            'content' => $this->message,
            'role' => 'user'
        ]);
        
        $system_prompt = $this->generateSystemPrompt();

        $response = Openai::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'temperature' => 0.8,
            'messages' => [
                ['role' => 'system', 'content' => $system_prompt],
                ['role' => 'user', 'content' => $this->message],
            ],
        ]);

        $this->chat->messages()->create([
            'content' => $response->choices[0]->message->content,
            'role' => 'assistant'
        ]);

        $this->message = '';
        $this->messages = $this->chat->messages;
    }

    public function generateSystemPrompt(){
        $context = $this->getContextFromKnowledgeBase($this->message);

        $system_template = "
        Utilizza i seguenti elementi di contesto per rispondere alla domanda degli utenti. Se non conosci la risposta, rispondi semplicemente che non sai la risposta, non cercate di inventare una risposta.
        ----------------
        {context}
        ";
        
        $system_prompt = str_replace("{context}", $context, $system_template);

        return $system_prompt;
    }

    public function getContextFromKnowledgeBase($message){
        $vector = json_encode(EmbeddingService::createEmbedding($message));

        $result = DB::table('knowledge_bases')
                ->select("content")
                ->selectSub("embedding <=> '{$vector}'::vector", "distance")
                ->orderBy('distance', 'asc')
                ->limit(2)
                ->get();

        $context = collect($result)->map(function ($item) {
            return $item->content;
        })->implode("\n");

        return $context;
    }

    public function createChat(){
        $titleCreation = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'Sei un creatore di titoli'
                ],
                [
                    'role' => 'user',
                    'content' =>  "Crea un titolo di al massimo 30 caratteri per una chat con questo messaggio: {$this->message}"
                ]
            ]
        ]);

        $this->chat = Chat::create([
            'title' => $titleCreation->choices[0]->message->content
        ]);
        $this->chat_id = $this->chat->id;

        $this->chat->messages()->create([
            'message' => $this->message,
            'role' => 'user'
        ]);
    }

    public function render()
    {
        return view('livewire.chatbot');
    }
}

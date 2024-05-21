<?php

namespace App\Livewire;

use App\Models\Chat;
use App\Models\Message;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\WithFileUploads;
use App\Services\OpenAiService;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\DB;
use OpenAI\Laravel\Facades\OpenAI;

class Chatbot extends Component
{
    use WithFileUploads;

    const ROLE_USER = 'user';
    const ROLE_ASSISTANT = 'assistant';
    
    #[Url(as: 'c')] 
    public $chat_id = null;

    public $currentMessage = '';
    public $openAiResponse = '';
    public $userPrompt = '';

    public function mount()
    {   
        $this->loadChat($this->chat_id);
    }

    public function rules()
    {
        return [
            'currentMessage' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'currentMessage.required' => 'Please enter a message.'
        ];
    }

    #[On('chatbot:select-chat')]
    public function selectChat($chat_id)
    {
        $this->loadChat($chat_id);
    } 

    private function loadChat($chat_id)
    {
        if(!$chat_id){
            $this->resetChat();
            return;
        }
        
        $this->chat_id = $chat_id;
    }

    private function resetChat()
    {
        $this->chat_id = null;
    }

    public function ask()
    {
        $this->validate();
 
        $this->userPrompt = $this->currentMessage;

        if(!$this->chat_id){
            $this->createChat();
        }
        
        $chat = Chat::find($this->chat_id);
        $chat->messages()->create([
            'content' =>  $this->currentMessage,
            'role' => self::ROLE_USER
        ]);

        $this->currentMessage = '';

        $this->js('$wire.generateOpenAiResponse()');
        
    }

    public function generateOpenAiResponse()
    {
        $systemPrompt = $this->generateSystemPrompt();

        $stream = OpenAiService::createStreamedChat($systemPrompt, $this->userPrompt);

        foreach ($stream as $response) {
            $text = $response->choices[0]->delta->content;
            $this->openAiResponse .= $text;

            $this->stream(
                to: 'generateResponse',
                content : $this->openAiResponse,
                replace: true
            );

            if($response->choices[0]->finishReason == 'stop'){
                break;
            }

        }
        
        $message = Message::create([
            'content' => $this->openAiResponse,
            'role' => 'assistant',
            'chat_id' => $this->chat_id
        ]);

        $this->openAiResponse = '';
    }

    public function generateSystemPrompt()
    {
        $context = $this->getContextFromKnowledgeBase($this->userPrompt);

        $system_template = "
        Utilizza i seguenti elementi di contesto per rispondere alla domanda degli utenti. Se non conosci la risposta, rispondi semplicemente che non sai la risposta, non cercate di inventare una risposta.
        ----------------
        {context}
        ";
        
        $system_prompt = str_replace("{context}", $context, $system_template);

        return $system_prompt;
    }

    public function getContextFromKnowledgeBase($message)
    {
        $vector = json_encode(OpenAiService::createEmbedding($message));

        $result = DB::table('embeddings')
                ->select("content")
                ->selectSub("embedding <=> '{$vector}'::vector", "distance")
                ->orderBy('distance', 'asc')
                ->limit(3)
                ->get();

        $context = collect($result)->map(function ($item) {
            return $item->content;
        })->implode("\n");

        return $context;
    }

    public function createChat()
    {
        $title = OpenAiService::createChatTitle($this->userPrompt);

        $chat = Chat::create([
            'title' => $title
        ]);

        $this->chat_id = $chat->id;
        $this->dispatch('chatbot:select-chat', $this->chat_id);
    }

    public function render()
    {
        $chatMessages = [];
        $chatTitle = '';

        $chat = Chat::find($this->chat_id);
        
        if($chat){
            $chatMessages = $chat->messages;
            $chatTitle = $chat->title;
        }

        $this->dispatch('scrollChatToBottom');
        return view('livewire.chatbot' , compact('chatMessages' , 'chatTitle'));
    }
}

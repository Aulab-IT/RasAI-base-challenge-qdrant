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

    public $chat = null;
    public $chatMessages = [];

    public $currentMessage = '';
    public $openAiResponse = '';
    public $userPrompt = '';

    public $audioMessage = '';
    public $audioState = 'idle';

    public $imageMode = false;

    public function mount()
    {   
        $this->loadChat($this->chat_id);
    }

    public function rules()
    {
        return [
            'currentMessage' => 'required_without:audioMessage'
        ];
    }

    public function messages()
    {
        return [
            'currentMessage.required_without' => 'Please enter a message or record an audio message.'
        ];
    }

    #[On('chatbot:select-chat')]
    public function selectChat($chat_id)
    {
        $this->loadChat($chat_id);
    } 

    // #[On('startRecording')]
    // public function startRecording()
    // {
    //     $this->dispatch('startRecording')->self();
    // }

    private function loadChat($chat_id)
    {
        if(!$chat_id){
            $this->resetChat();
            $this->resetAudio();
            return;
        }
        
        $this->chat_id = $chat_id;
        $this->chat = Chat::find($chat_id);
        $this->chatMessages = $this->chat->messages;
        $this->resetAudio();
    }

    public function toggleImageMode()
    {
        $this->imageMode = !$this->imageMode;
    }

    private function resetChat()
    {
        $this->chat = null;
        $this->chatMessages = [];
        $this->chat_id = null;
    }

    private function resetAudio()
    {
        $this->audioMessage = null;
        $this->audioState = 'idle';
    }

    public function ask()
    {
        $this->validate();

        $audioPath = $this->handleAudioMessage();

        $this->userPrompt = $this->currentMessage;

        if(!$this->chat){
            $this->createChat();
        }
        
        $this->createMessage(self::ROLE_USER, $this->currentMessage , $audioPath);

        $this->currentMessage = '';

        // TODO :: Generare immagine nel caso imageMode sia attivo altrimenti generare risposta
        if($this->imageMode){
            $this->js('$wire.generateImage()');
        }else{
            $this->js('$wire.generateOpenAiResponse()');
        }
    }

    private function handleAudioMessage()
    {
        if($this->audioMessage){
           $path = $this->audioMessage->store('audio', 'public');
           $response = OpenAiService::speechToText(storage_path('app/public/' . $path));

           $this->currentMessage = $response;
           $this->audioMessage = null;
           $this->audioState = 'idle';

           return $path;
        }

        return null;
    }

    private function createMessage($role, $content , $audioPath = null)
    {
        $this->chat->messages()->create([
            'content' => $content,
            'role' => $role,
            'audio_path' => $audioPath
        ]);

        $this->chatMessages = $this->chat->messages;
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
        

        $message = $this->chat->messages()->create([
            'content' => $this->openAiResponse,
            'role' => 'assistant',
        ]);

        $this->chatMessages = $this->chat->messages;
        $this->openAiResponse = '';

        $this->js('$wire.generateTextToSpeech(' . $message->id . ')');

    }

    public function generateImage()
    {
        $prompt = $this->userPrompt;

        $image_url = OpenAiService::createImage($prompt);

        $message = $this->chat->messages()->create([
            'content' => $image_url,
            'role' => 'assistant',
            'is_image_content' => true
        ]);

        $this->chatMessages = $this->chat->messages;
    }

    public function generateTextToSpeech(Message $message)
    {
        $path = OpenAiService::textToSpeech($message->content);

        $message->update([
            'audio_path' => $path
        ]);

        $this->chatMessages = $this->chat->messages;
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

        $this->chat = Chat::create([
            'title' => $title
        ]);

        $this->chat_id = $this->chat->id;
        $this->dispatch('chatbot:select-chat', $this->chat_id);
    }

    public function render()
    {
        return view('livewire.chatbot');
    }
}

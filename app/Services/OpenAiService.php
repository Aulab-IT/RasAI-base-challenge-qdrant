<?php

namespace App\Services;

use OpenAI\Laravel\Facades\OpenAI;

class OpenAiService {
    public static function createEmbedding($content)
    {
        $embedding = OpenAI::embeddings()->create([
            'model' => 'text-embedding-ada-002',
            'input' => $content
        ]);

        return $embedding->embeddings[0]->embedding;
    }

    public static function createChatTitle($content)
    {   
        $response = OpenAI::chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'system', 'content' => 'Crea il titolo per una chat di al massimo 5 parole in base al messaggio dell\'utente'],
                ['role' => 'user', 'content' => $content],
            ]
            ]);

        return $response->choices[0]->message->content;
    }

    public static function createStreamedChat($systemPrompt, $userPrompt)
    {
        $stream = Openai::chat()->createStreamed([
            'model' => 'gpt-3.5-turbo',
            'temperature' => 0.8,
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userPrompt],
            ],
        ]);

        return $stream;
    }
}
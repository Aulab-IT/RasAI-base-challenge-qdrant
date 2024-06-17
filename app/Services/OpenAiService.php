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
        return "Hei, implementa il metodo createChat";

        // TODO>> Implement the createChatTitle method ask to GPT to create a title for a chat with the given content
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
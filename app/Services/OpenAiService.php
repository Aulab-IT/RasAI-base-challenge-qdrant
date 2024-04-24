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
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'Sei un creatore di titoli'
                ],
                [
                    'role' => 'user',
                    'content' =>  "Crea un titolo di al massimo 30 caratteri per una chat con questo messaggio: {$content}"
                ]
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

    public static function speechToText($audioPath)
    {
        $response = OpenAI::audio()->transcribe([
            'model' => 'whisper-1',
            'file' => fopen($audioPath , 'r'),
            'response_format' => 'verbose_json',
        ]);

        return $response->text;
    }

    public static function textToSpeech($text)
    {
        $response = OpenAI::audio()->speech([
            'model' => 'tts-1',
            'input' => $text,
            'voice' => 'alloy',
        ]);

        // Save the audio file in local storage in tts folder with laravel
        $audioFileName = 'tts/' . uniqid() . '.mp3';
        $storage_path = storage_path('app/public/' . $audioFileName);
        file_put_contents($storage_path, $response);

        return $audioFileName;
    }

    public static function createImage($prompt)
    {
        $response = OpenAI::images()->create([
            'model' => 'dall-e-3',
            'prompt' => $prompt,
            'n' => 1,
            'size' => '1024x1024',
            'response_format' => 'url',
        ]);

        $openAIUrl = $response->data[0]->url;

        $imageFileName = 'image/' . uniqid() . '.png';
        $storage_path = storage_path('app/public/' . $imageFileName);
        file_put_contents($storage_path, file_get_contents($openAIUrl));

        return $imageFileName;
    }
}
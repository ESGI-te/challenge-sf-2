<?php

namespace App\Service;

use Orhanerday\OpenAi\OpenAi;

class OpenAIService
{
    protected OpenAi $openAi;
    public function __construct($openAiKey) {
        $this->openAi = new OpenAi($openAiKey);
    }

    public function generateText(string $prompt): string {
        $completion = $this->openAi->chat([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [
                    "role" => "system",
                    "content" => "Tu es un excellent chef cuisinier"
                ],
                [
                    "role" => "user",
                    "content" => $prompt
                ]
            ],
            'temperature' => 1.0,
            'max_tokens' => 3200,
            'frequency_penalty' => 0,
            'presence_penalty' => 0,
        ]);
        $completionDecoded = json_decode($completion);

        return $completionDecoded->choices[0]->message->content;
    }

    public function generateImage(string $prompt): string {
        $imageResponse = $this->openAi->image([
            "prompt" => $prompt,
            "n" => 1,
            "size" => "1024x1024",
            "response_format" => "url",
        ]);
        $imageResponseDecoded = json_decode($imageResponse);
        return $imageResponseDecoded->data[0]->url;
    }
}
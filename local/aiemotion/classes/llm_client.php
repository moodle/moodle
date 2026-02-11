<?php
namespace local_aiemotion;

defined('MOODLE_INTERNAL') || die();

class llm_client {

    public static function analyze(string $text): array {
        $key = get_config('local_aiemotion', 'apikey');

        if (!$key) {
            return ['feedback' => 'AI feedback not configured.'];
        }

        $payload = [
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'system', 'content' => 'Give supportive emotional academic feedback'],
                ['role' => 'user', 'content' => $text]
            ]
        ];

        $ch = curl_init('https://api.openai.com/v1/chat/completions');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer $key",
                'Content-Type: application/json'
            ],
            CURLOPT_POSTFIELDS => json_encode($payload)
        ]);

        $res = json_decode(curl_exec($ch), true);
        curl_close($ch);

        return [
            'feedback' => $res['choices'][0]['message']['content'] ?? 'No feedback generated'
        ];
    }

    public static function detect_emotions(string $text): array {
        $map = [
            'positive' => '/happy|joy|excited|positive/i',
            'sad' => '/sad|cry|depressed/i',
            'stressed' => '/stress|anxious|pressure/i'
        ];

        $found = [];

        foreach ($map as $e => $r) {
            if (preg_match($r, $text)) $found[] = $e;
        }

        return $found ?: ['neutral'];
    }
}

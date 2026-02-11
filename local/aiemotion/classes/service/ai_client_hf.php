<?php
namespace local_aiemotion\service;
defined('MOODLE_INTERNAL') || die();

class ai_client_hf {

    public static function generate_feedback(string $text): string {
        global $CFG;

        $token = get_config('local_aiemotion', 'hf_token');
        if (empty($token)) {
            throw new \moodle_exception('HuggingFace token not configured');
        }

        $model = 'tiiuae/falcon-7b-instruct'; // or another instruct model

        $payload = [
            'inputs' => $text,
            'parameters' => [
                'max_new_tokens' => 150,
                'temperature' => 0.7
            ]
        ];

        $ch = curl_init("https://api.huggingface.co/models/$model");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer $token",
                'Content-Type: application/json'
            ],
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_TIMEOUT => 30,
        ]);

        $response = curl_exec($ch);
        if ($response === false) {
            throw new \moodle_exception('HuggingFace error: ' . curl_error($ch));
        }

        $data = json_decode($response, true);

        if (isset($data[0]['generated_text'])) {
            return trim($data[0]['generated_text']);
        }

        throw new \moodle_exception('Invalid HF response format');
    }
}

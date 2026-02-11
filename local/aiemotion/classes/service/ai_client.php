<?php
namespace local_aiemotion\service;

defined('MOODLE_INTERNAL') || die();

class ai_client_hf {

public static function generate_feedback(string $text): string {
    $apikey = get_config('local_aiemotion', 'apikey');

    if (empty($apikey)) {
        throw new \moodle_exception('API key not configured');
    }

    $payload = [
        'model' => 'gpt-4o',
        'input' => $text
    ];

    $ch = curl_init('https://api.openai.com/v1/responses');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apikey
        ],
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_TIMEOUT => 20
    ]);

    $response = curl_exec($ch);

    if ($response === false) {
        throw new \moodle_exception('Curl error: ' . curl_error($ch));
    }

    // 🔴 SHOW RAW RESPONSE
    debugging('AI RAW RESPONSE >>> ' . $response, DEBUG_DEVELOPER);

    // TEMP return so popup shows something
    return 'RAW RESPONSE logged — check debugging output';
}
}

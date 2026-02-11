<?php
namespace local_yourplugin;

defined('MOODLE_INTERNAL') || die();

class gemini_client {

    public static function generate_feedback(string $text): string {
        $apikey = get_config('local_yourplugin', 'gemini_api_key');

        if (empty($apikey)) {
            return self::mock_response($text);
        }

        $url = "https://generativelanguage.googleapis.com/v1/models/gemini-pro:generateContent?key={$apikey}";

        $payload = [
            "contents" => [[
                "parts" => [[
                    "text" => "Give constructive, emotionally supportive academic feedback for this assignment:\n\n{$text}"
                ]]
            ]]
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_TIMEOUT => 30
        ]);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error || !$response) {
            return self::mock_response($text);
        }

        $data = json_decode($response, true);

        return $data['candidates'][0]['content']['parts'][0]['text']
            ?? self::mock_response($text);
    }

    private static function mock_response(string $text): string {
        return "Your submission shows clear effort and understanding. The ideas are meaningful and demonstrate thoughtful engagement. You are encouraged to further refine clarity and structure to make your argument even stronger.";
    }
}

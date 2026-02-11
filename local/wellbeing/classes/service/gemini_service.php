<?php
namespace local_wellbeing\service;

defined('MOODLE_INTERNAL') || die();

class gemini_service {

    public static function analyse_text(string $text): ?array {
        $apikey = get_config('local_aiemotion', 'geminiapikey');

        if (empty($apikey)) {
            debugging('WB: Gemini API key not configured', DEBUG_DEVELOPER);
            return null;
        }

        // Prompt
        $prompt = <<<PROMPT
You are analysing a student's assignment submission for emotional wellbeing.

Classify the text into the following emotional metrics:
- very_happy
- happy
- sad
- depressed

Rules:
- Return ONLY valid JSON
- Values must be integers
- Total must equal 100
- No explanation or formatting

Example:
{
  "very_happy": 10,
  "happy": 60,
  "sad": 20,
  "depressed": 10
}

Text:
"""
{$text}
"""
PROMPT;

        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . $apikey;

        $payload = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ]
        ];

        debugging('WB: Sending request to Gemini', DEBUG_DEVELOPER);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_TIMEOUT        => 30,
        ]);

        $response = curl_exec($ch);

        if ($response === false) {
            debugging('WB: Gemini CURL error: ' . curl_error($ch), DEBUG_DEVELOPER);
            curl_close($ch);
            return null;
        }

        curl_close($ch);

        debugging('WB: Gemini raw response: ' . $response, DEBUG_DEVELOPER);

        $json = json_decode($response, true);

        $outputtext =
            $json['candidates'][0]['content']['parts'][0]['text']
            ?? null;

        if (!$outputtext) {
            debugging('WB: Gemini returned empty content', DEBUG_DEVELOPER);
            return null;
        }

        $metrics = json_decode($outputtext, true);

        if (!is_array($metrics)) {
            debugging('WB: Gemini output not valid JSON: ' . $outputtext, DEBUG_DEVELOPER);
            return null;
        }

        return $metrics;
    }
}

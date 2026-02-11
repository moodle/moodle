<?php
namespace local_wellbeing\service;

defined('MOODLE_INTERNAL') || die();

class analysis_service {
public static function process_submission(int $onlinetextid): void {
    global $DB;

    debugging("====================================", DEBUG_DEVELOPER);
    debugging("WB: process_submission() CALLED", DEBUG_DEVELOPER);
    debugging("WB: Received onlinetext.id = {$onlinetextid}", DEBUG_DEVELOPER);

    // 1️⃣ Fetch onlinetext
    $textrec = $DB->get_record(
        'assignsubmission_onlinetext',
        ['id' => $onlinetextid],
        'id, submission, onlinetext, timemodified',
        IGNORE_MISSING
    );

    if (!$textrec) {
        debugging("WB ERROR: No onlinetext record found for id {$onlinetextid}", DEBUG_DEVELOPER);
        return;
    }

    debugging("WB: onlinetext.submission = {$textrec->submission}", DEBUG_DEVELOPER);
    debugging("WB: onlinetext.timemodified = {$textrec->timemodified}", DEBUG_DEVELOPER);

    $submissionid = (int)$textrec->submission;

    // 2️⃣ Fetch assign_submission
    $submission = $DB->get_record(
        'assign_submission',
        ['id' => $submissionid],
        '*',
        IGNORE_MISSING
    );

    if (!$submission) {
        debugging("WB ERROR: assign_submission NOT FOUND for id {$submissionid}", DEBUG_DEVELOPER);
        return;
    }

    debugging("WB: assign_submission.timemodified = {$submission->timemodified}", DEBUG_DEVELOPER);

    // 3️⃣ Resolve course
    $assign = $DB->get_record(
        'assign',
        ['id' => $submission->assignment],
        'id, course',
        MUST_EXIST
    );

    $courseid = $assign->course;
    debugging("WB: courseid = {$courseid}", DEBUG_DEVELOPER);

    // 4️⃣ Validate text
    if (empty(trim($textrec->onlinetext))) {
        debugging("WB ERROR: onlinetext EMPTY", DEBUG_DEVELOPER);
        return;
    }

    $text = trim(strip_tags($textrec->onlinetext));
    debugging("WB: text length = " . strlen($text), DEBUG_DEVELOPER);

    // 5️⃣ Check if record exists
    $existing = $DB->get_record(
        'local_wellbeing_metrics',
        ['submissionid' => $submissionid],
        '*',
        IGNORE_MISSING
    );

    if ($existing) {
        debugging("WB: Existing metrics found (id={$existing->id})", DEBUG_DEVELOPER);
        debugging("WB: Existing timemodified = {$existing->timemodified}", DEBUG_DEVELOPER);
    } else {
        debugging("WB: No existing metrics found — will insert", DEBUG_DEVELOPER);
    }

    // 6️⃣ Call Gemini
    debugging("WB: Calling Gemini now...", DEBUG_DEVELOPER);

    $metrics = self::call_gemini_api($text);

    if (empty($metrics)) {
        debugging("WB ERROR: Gemini returned empty metrics", DEBUG_DEVELOPER);
        return;
    }

    debugging("WB: Gemini metrics = " . json_encode($metrics), DEBUG_DEVELOPER);

    // 7️⃣ UPSERT
    if ($existing) {

        debugging("WB: Performing UPDATE of metrics", DEBUG_DEVELOPER);

        $existing->metrics = json_encode($metrics);
        $existing->timemodified = time();

        $DB->update_record('local_wellbeing_metrics', $existing);

        debugging("WB: UPDATE COMPLETE", DEBUG_DEVELOPER);

    } else {

        debugging("WB: Performing INSERT of metrics", DEBUG_DEVELOPER);

        $record = (object)[
            'courseid'     => $courseid,
            'assignmentid' => $submission->assignment,
            'submissionid' => $submissionid,
            'userid'       => $submission->userid,
            'metrics'      => json_encode($metrics),
            'timecreated'  => time(),
            'timemodified' => time(),
        ];

        $DB->insert_record('local_wellbeing_metrics', $record);

        debugging("WB: INSERT COMPLETE", DEBUG_DEVELOPER);
    }

    debugging("WB: process_submission DONE ✔", DEBUG_DEVELOPER);
    debugging("====================================", DEBUG_DEVELOPER);
}


    private static function call_gemini_api(string $text): array {
        debugging("WB: Calling Gemini API", DEBUG_DEVELOPER);

        $apikey = get_config('local_aiemotion', 'geminiapikey');
        if (empty($apikey)) {
            debugging("WB ERROR: Gemini API key missing", DEBUG_DEVELOPER);
            return [];
        }

        $prompt = <<<PROMPT
        Return ONLY valid JSON.
        Keys: very_happy, happy, sad, depressed
        Values must be integers.
        Total must equal 100.
        No markdown. No explanation.

        Text:
        """
        {$text}
        """
        PROMPT;

        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . $apikey;

        $payload = [
            'contents' => [
                ['parts' => [['text' => $prompt]]]
            ]
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
        curl_close($ch);

        if (!$response) {
            debugging("WB ERROR: No Gemini response", DEBUG_DEVELOPER);
            return [];
        }

        $decoded = json_decode($response, true);
        $output = $decoded['candidates'][0]['content']['parts'][0]['text'] ?? '';

        debugging("WB: Gemini raw output = {$output}", DEBUG_DEVELOPER);

        // Clean markdown/json wrappers
        $output = preg_replace('/```json|```/', '', $output);
        $output = trim($output);

        $metrics = json_decode($output, true);

        if (!is_array($metrics)) {
            debugging("WB ERROR: Invalid JSON from Gemini", DEBUG_DEVELOPER);
            return [];
        }

        // Validate
        $required = ['very_happy', 'happy', 'sad', 'depressed'];
        $sum = 0;

        foreach ($required as $key) {
            if (!isset($metrics[$key])) {
                debugging("WB ERROR: Missing {$key}", DEBUG_DEVELOPER);
                return [];
            }
            $sum += (int)$metrics[$key];
        }

        if ($sum !== 100) {
            debugging("WB ERROR: Metrics sum {$sum} ≠ 100", DEBUG_DEVELOPER);
            return [];
        }

        return $metrics;
    }
}

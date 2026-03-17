<?php

namespace local_wellbeing\service;
use context_course;

defined('MOODLE_INTERNAL') || die();

class analysis_service {
    public static function process_submission(int $submissionid): void {
        global $DB;

        //debugging("====================================", DEBUG_DEVELOPER);
        //debugging("WB: process_submission() CALLED", DEBUG_DEVELOPER);
        //debugging("WB: Received submission.id = {$submissionid}", DEBUG_DEVELOPER);

        // 1️⃣ Fetch assign_submission (MAIN record)
        $submission = $DB->get_record(
            'assign_submission',
            ['id' => $submissionid],
            '*',
            IGNORE_MISSING
        );

        if (!$submission) {
            $DB->delete_records('local_wellbeing_metrics', [
                'submissionid' => $submissionid
            ]);
            //debugging("WB ERROR: assign_submission NOT FOUND for id {$submissionid}", DEBUG_DEVELOPER);
            return;
        }

        //debugging("WB: assign_submission.timemodified = {$submission->timemodified}", DEBUG_DEVELOPER);

        // 2️⃣ Fetch corresponding onlinetext
        $textrec = $DB->get_record(
            'assignsubmission_onlinetext',
            ['submission' => $submissionid],
            'id, onlinetext',
            IGNORE_MISSING
        );

        if (!$textrec) {
            $DB->delete_records('local_wellbeing_metrics', [
                'submissionid' => $submissionid
            ]);
            //debugging("WB ERROR: No onlinetext record found for submission {$submissionid}", DEBUG_DEVELOPER);
            return;
        }

        // 3️⃣ Validate text
        if (empty(trim($textrec->onlinetext))) {
            $DB->delete_records('local_wellbeing_metrics', [
                'submissionid' => $submissionid
            ]);
            return;
        }

        $text = trim(strip_tags($textrec->onlinetext));
        //debugging("WB: text length = " . strlen($text), DEBUG_DEVELOPER);

        // 4️⃣ Resolve course
        $assign = $DB->get_record(
            'assign',
            ['id' => $submission->assignment],
            'id, course',
            MUST_EXIST
        );

        $courseid = $assign->course;
        //debugging("WB: courseid = {$courseid}", DEBUG_DEVELOPER);

        // 5️⃣ Check if record exists
        $existing = $DB->get_record(
            'local_wellbeing_metrics',
            ['submissionid' => $submissionid],
            '*',
            IGNORE_MISSING
        );

        // 6️⃣ Call Gemini
        //debugging("WB: Calling Gemini now...", DEBUG_DEVELOPER);

        $metrics = self::call_gemini_api($text,$assign->id);

        if (empty($metrics)) {
            //debugging("WB ERROR: Gemini returned empty metrics", DEBUG_DEVELOPER);
            return;
        }

        //debugging("WB: Gemini metrics = " . json_encode($metrics), DEBUG_DEVELOPER);

        // 7️⃣ UPSERT
        if ($existing) {

            debugging("WB: Performing UPDATE of metrics", DEBUG_DEVELOPER);

            $existing->metrics      = json_encode($metrics);
            $existing->timemodified = time();

            $DB->update_record('local_wellbeing_metrics', $existing);

            //debugging("WB: UPDATE COMPLETE", DEBUG_DEVELOPER);

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

            //debugging("WB: INSERT COMPLETE", DEBUG_DEVELOPER);
        }

        //debugging("WB: process_submission DONE ✔", DEBUG_DEVELOPER);
        //debugging("====================================", DEBUG_DEVELOPER);
    }

private static function call_gemini_api(string $text, int $assignid): array {

    global $DB;

    debugging("WB: Starting Gemini call for assignid {$assignid}", DEBUG_DEVELOPER);

    $apikey = get_config('local_aiemotion', 'geminiapikey');
    if (empty($apikey)) {
        debugging("WB ERROR: Gemini API key missing", DEBUG_DEVELOPER);
        return [];
    }

    /*
    -----------------------------------
    1. GET ASSIGNMENT COURSE
    -----------------------------------
    */

    $cm = get_coursemodule_from_instance('assign', $assignid);

    if (!$cm) {
        debugging("WB ERROR: Course module not found for assignid {$assignid}", DEBUG_DEVELOPER);
        return [];
    }

    $courseid = $cm->course;
    debugging("WB: Course ID = {$courseid}", DEBUG_DEVELOPER);

    /*
    -----------------------------------
    2. GET PROMPT FROM COURSE TABLE
    -----------------------------------
    */

    $courseconfig = $DB->get_record(
        'local_wellbeing_courses',
        ['courseid' => $courseid],
        '*',
        IGNORE_MISSING
    );

    if (!$courseconfig || empty($courseconfig->metrics_prompt)) {
        debugging("WB ERROR: Prompt not found for course {$courseid}", DEBUG_DEVELOPER);
        return [];
    }

    $prompttemplate = $courseconfig->metrics_prompt;

    debugging("WB: Prompt template loaded", DEBUG_DEVELOPER);

    /*
    -----------------------------------
    3. GET SELECTED METRICS FOR ASSIGNMENT
    -----------------------------------
    */

    $assignmetrics = $DB->get_record(
        'local_wb_assign_metrics',
        ['assignid' => $cm->id],
        '*',
        IGNORE_MISSING
    );

    if (!$assignmetrics) {
        debugging("WB ERROR: No metrics record found for assign {$cm->id}", DEBUG_DEVELOPER);
        return [];
    }

    $metrics = json_decode($assignmetrics->metricname, true);

    if (!$metrics || !is_array($metrics)) {
        debugging("WB ERROR: Metrics JSON invalid", DEBUG_DEVELOPER);
        return [];
    }

    debugging("WB: Selected metrics = " . json_encode($metrics), DEBUG_DEVELOPER);

    /*
    -----------------------------------
    4. PREPARE METRICS LIST
    -----------------------------------
    */

    $metricslist = "";

    foreach ($metrics as $metric) {
        $metricslist .= "- {$metric}\n";
    }

    debugging("WB: Metrics list for prompt = {$metricslist}", DEBUG_DEVELOPER);

    /*
    -----------------------------------
    5. BUILD FINAL PROMPT
    -----------------------------------
    */

    $prompt = str_replace(
        ['{{METRICS}}', '{{TEXT}}'],
        [$metricslist, $text],
        $prompttemplate
    );

    debugging("WB: Final prompt sent to Gemini = {$prompt}", DEBUG_DEVELOPER);

    /*
    -----------------------------------
    6. GEMINI API CALL
    -----------------------------------
    */

    $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . $apikey;

    $payload = [
        'contents' => [
            ['parts' => [['text' => $prompt]]]
        ]
    ];

    debugging("WB: Calling Gemini API", DEBUG_DEVELOPER);

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
        debugging("WB ERROR: No response from Gemini", DEBUG_DEVELOPER);
        return [];
    }

    debugging("WB: Raw Gemini response = {$response}", DEBUG_DEVELOPER);

    $decoded = json_decode($response, true);
    $output = $decoded['candidates'][0]['content']['parts'][0]['text'] ?? '';

    /*
    -----------------------------------
    7. CLEAN RESPONSE
    -----------------------------------
    */

    $output = preg_replace('/```json|```/', '', $output);
    $output = trim($output);

    debugging("WB: Gemini cleaned output = {$output}", DEBUG_DEVELOPER);

    $metricsresult = json_decode($output, true);

    if (!is_array($metricsresult)) {
        debugging("WB ERROR: Gemini output not valid JSON", DEBUG_DEVELOPER);
        return [];
    }

    debugging("WB: Gemini parsed result = " . json_encode($metricsresult), DEBUG_DEVELOPER);

    /*
    -----------------------------------
    8. FILTER ONLY SELECTED METRICS
    -----------------------------------
    */

    $filtered = [];

    foreach ($metrics as $metric) {
        if (isset($metricsresult[$metric])) {
            $filtered[$metric] = (int)$metricsresult[$metric];
        }
    }

    debugging("WB: Filtered result (selected metrics only) = " . json_encode($filtered), DEBUG_DEVELOPER);

    return $filtered;
}

    public static function get_course_aggregated_metrics(int $courseid): array {
        global $DB;

        $records = $DB->get_records(
            'local_wellbeing_metrics',
            ['courseid' => $courseid]
        );

        $totals = [];

        foreach ($records as $record) {

            $metrics = json_decode($record->metrics, true);

            if (!is_array($metrics)) {
                continue;
            }

            foreach ($metrics as $key => $value) {

                if (!isset($totals[$key])) {
                    $totals[$key] = 0;
                }

                $totals[$key] += (int)$value;
            }
        }

        return $totals;
    }

    public static function get_user_course_metrics(int $courseid, int $userid): array {
        global $DB;

        $records = $DB->get_records(
            'local_wellbeing_metrics',
            [
                'courseid' => $courseid,
                'userid'   => $userid
            ]
        );

        $totals = [];

        foreach ($records as $record) {

            $metrics = json_decode($record->metrics, true);

            if (!is_array($metrics)) {
                continue;
            }

            foreach ($metrics as $key => $value) {

                if (!isset($totals[$key])) {
                    $totals[$key] = 0;
                }

                $totals[$key] += (int)$value;
            }
        }

        return $totals;
    }

    public static function get_student_assignment_progress($courseid, $userid) {
        global $DB;

        $sql = "
            SELECT a.name,
                s.status,
                s.timemodified
            FROM {assign_submission} s
            JOIN {assign} a ON a.id = s.assignment
            WHERE a.course = ?
            AND s.userid = ?
            ORDER BY s.timemodified ASC
        ";

        return $DB->get_records_sql($sql, [$courseid, $userid]);
    }
    
    public static function get_student_assignment_metrics($courseid, $userid) {
    global $DB;

    $sql = "
        SELECT a.name,
               m.metrics
        FROM {local_wellbeing_metrics} m
        JOIN {assign_submission} s ON s.id = m.submissionid
        JOIN {assign} a ON a.id = s.assignment
        WHERE a.course = ?
          AND s.userid = ?
    ";

        return $DB->get_records_sql($sql, [$courseid, $userid]);
    }
    
    public static function get_assignment_submission_overview($courseid) {

global $DB;

$totalstudents = count(get_enrolled_users(
    context_course::instance($courseid),
    'mod/assign:submit'
));

$sql = "
SELECT
a.id,
a.name AS assignment,
COUNT(DISTINCT s.userid) AS submitted
FROM {assign} a
LEFT JOIN {assign_submission} s
ON s.assignment = a.id
AND s.status = 'submitted'
WHERE a.course = :courseid
GROUP BY a.id,a.name
ORDER BY a.duedate
";

$data = $DB->get_records_sql($sql,['courseid'=>$courseid]);

foreach ($data as $d) {
    $d->totalstudents = $totalstudents;
}

return $data;
}
}

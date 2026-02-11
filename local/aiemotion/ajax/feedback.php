<?php
define('AJAX_SCRIPT', true);

require_once(__DIR__ . '/../../../config.php');
require_once(__DIR__ . '/../vendor/autoload.php'); // Composer autoload for PDF parser

use Smalot\PdfParser\Parser;

require_login();
require_sesskey();
$jsprompt = optional_param('prompt', '', PARAM_RAW);
$livetext = optional_param('text', '', PARAM_RAW);
error_log('AIEMOTION DEBUG: JS Prompt received → ' . var_export($jsprompt, true));


header('Content-Type: application/json');

global $DB, $USER;


// ----------------------
// Resolve CMID / Context
// ----------------------
$inputid = required_param('cmid', PARAM_INT);

if ($DB->record_exists('context', ['id' => $inputid, 'contextlevel' => CONTEXT_MODULE])) {
    $context = context::instance_by_id($inputid);
    $cmid = $context->instanceid;
    error_log("AIEMOTION: Received CONTEXT ID, resolved CMID = $cmid");
} else {
    $cmid = $inputid;
    $context = context_module::instance($cmid);
    error_log("AIEMOTION: Received CMID directly = $cmid");
}

// Fetch course module
try {
    $cm = get_coursemodule_from_id('assign', $cmid, 0, false, MUST_EXIST);
} catch (\dml_missing_record_exception $e) {
    echo json_encode([
        'error' => true,
        'feedback' => 'Cannot find assignment module in DB',
        'debug' => $e->getMessage()
    ]);
    exit;
}

$assignmentid = $cm->instance;
$courseid = $cm->course;
$userid = $USER->id;

error_log("AIEMOTION: FINAL CMID = {$cm->id}");
error_log("AIEMOTION: ASSIGN ID = {$assignmentid}");
error_log("AIEMOTION: COURSE ID = {$courseid}");
error_log("AIEMOTION: USER ID = $userid");

// ----------------------
// Fetch latest submission
// ----------------------
$submission = $DB->get_record('assign_submission', [
    'assignment' => $assignmentid,
    'userid' => $userid,
    'latest' => 1
]);

if (!$submission) {
    echo json_encode([
        'error' => true,
        'feedback' => 'No submission found for user.'
    ]);
    exit;
}

error_log("AIEMOTION: SUBMISSION ID = {$submission->id}");
error_log("AIEMOTION: SUBMISSION STATUS = {$submission->status}");

// ----------------------
// Read online text
// ----------------------
$text = trim(strip_tags($livetext));

if (empty($text)) {
    echo json_encode([
        'error' => true,
        'feedback' => 'No text received from editor.'
    ]);
    exit;
}

error_log('AIEMOTION: USING LIVE TEXT. Length = ' . strlen($text));


// ----------------------
// Read PDF files if no text
// ----------------------
$fs = get_file_storage();
$files = $fs->get_area_files(
    $context->id,
    'assignsubmission_file',
    'submission_files',
    $submission->id,
    'filename',
    false
);

if (empty($text) && count($files) > 0) {
   foreach ($files as $file) {
    $filename = $file->get_filename();
    $mimetype = $file->get_mimetype();

    if ($mimetype !== 'application/pdf') {
        echo json_encode([
            'error' => true,
            'feedback' => 'Only PDF files are supported.',
            'filename' => $filename
        ]);
        exit;
    }

    // Save to temp file
    $tempfile = tempnam(sys_get_temp_dir(), 'pdf_') . '.pdf';
    $file->copy_content_to($tempfile);

    try {
        $parser = new Parser();
        $pdf = $parser->parseFile($tempfile); // string path
        $text = $pdf->getText();
        error_log("AIEMOTION: PDF '{$filename}' text length = " . strlen($text));
    } catch (\Exception $e) {
        echo json_encode([
            'error' => true,
            'feedback' => 'Failed to parse PDF.',
            'filename' => $filename,
            'debug' => $e->getMessage()
        ]);
        @unlink($tempfile);
        exit;
    }

    @unlink($tempfile); // cleanup
}

}

// ----------------------
// Check if we have any text
// ----------------------
if (empty(trim($text))) {
    echo json_encode([
        'error' => true,
        'feedback' => 'No text found in submission.'
    ]);
    exit;
}

// ----------------------
// Gemini API integration
// ----------------------
$apikey = get_config('local_aiemotion', 'geminiapikey');
if (empty($apikey)) {
    echo json_encode([
        'error' => true,
        'feedback' => 'Gemini API key not configured'
    ]);
    exit;
}

$configprompt = get_config('local_aiemotion', 'aifeedbackprompt');

error_log('AIEMOTION DEBUG: DB prompt → ' . var_export($configprompt, true));


// Default prompt
// Default instruction
$defaultinstruction = 'Analyze the following student assignment and give short, empathetic feedback.
Detect emotional, academic, and motivational tone.
Avoid grading.';

// Decide instruction
if (!empty($jsprompt)) {
    $instruction = $jsprompt;
    error_log('AIEMOTION DEBUG: USING JS INSTRUCTION');
} else {
    $instruction = $defaultinstruction;
    error_log('AIEMOTION DEBUG: USING DEFAULT INSTRUCTION');
}

// Base wrapper (always constant)
$baseprompt = <<<PROMPT
{{INSTRUCTION}}

Student response:
{{TEXT}}
PROMPT;

// Inject values
$prompt = str_replace(
    ['{{INSTRUCTION}}', '{{TEXT}}'],
    [$instruction, $text],
    $baseprompt
);

// Debug final prompt
error_log("AIEMOTION FINAL PROMPT ↓↓↓\n" . $prompt);



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

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
    CURLOPT_POSTFIELDS => json_encode($payload),
    CURLOPT_TIMEOUT => 30,
    CURLOPT_SSL_VERIFYPEER => true
]);

$response = curl_exec($ch);
$curlerror = curl_error($ch);
curl_close($ch);

if ($response === false) {
    echo json_encode([
        'error' => true,
        'feedback' => 'Curl error',
        'debug' => $curlerror
    ]);
    exit;
}

$data = json_decode($response, true);

if (!$data) {
    echo json_encode([
        'error' => true,
        'feedback' => 'Gemini response not JSON',
        'raw' => $response
    ]);
    exit;
}

$feedback = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

if (!$feedback) {
    echo json_encode([
        'error' => true,
        'feedback' => 'No feedback generated (Gemini response malformed)',
        'raw' => $data
    ]);
    exit;
}

// ----------------------
// SUCCESS
// ----------------------
echo json_encode([
    'error' => false,
    'feedback' => trim($feedback)
]);
exit;

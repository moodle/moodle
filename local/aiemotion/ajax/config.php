<?php
define('AJAX_SCRIPT', true);

require_once(__DIR__ . '/../../../../config.php');

require_login();
header('Content-Type: application/json');

global $DB;

$cmid = required_param('cmid', PARAM_INT);

error_log("AIEMOTION CONFIG: Fetch request for CMID={$cmid}");

$config = $DB->get_record(
    'local_aiemotion',
    ['cmid' => $cmid],
    'enableaifeedback, aifeedbackprompt',
    IGNORE_MISSING
);

$response = [
    'enableaifeedback' => isset($config->enableaifeedback)
        ? (int)$config->enableaifeedback
        : 0,
    'aifeedbackprompt' => $config->aifeedbackprompt ?? ''
];

error_log('AIEMOTION CONFIG RESPONSE: ' . json_encode($response));

echo json_encode($response);
exit;

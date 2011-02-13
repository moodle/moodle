<?php

// This file is executed right after the install.xml
//

function xmldb_quiz_statistics_install() {
    global $DB;

    $record = new stdClass();
    $record->name         = 'statistics';
    $record->displayorder = 8000;
    $record->cron         = 18000;
    $record->capability   = 'quizreport/statistics:view';
    $DB->insert_record('quiz_report', $record);

}
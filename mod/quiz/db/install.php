<?php  //$Id$

// This file replaces:
//   * STATEMENTS section in db/install.xml
//   * lib.php/modulename_install() post installation hook
//   * partially defaults.php

function xmldb_quiz_install() {
    global $DB;

/// Install logging support
    upgrade_log_display_entry('quiz', 'add', 'quiz', 'name');
    upgrade_log_display_entry('quiz', 'update', 'quiz', 'name');
    upgrade_log_display_entry('quiz', 'view', 'quiz', 'name');
    upgrade_log_display_entry('quiz', 'report', 'quiz', 'name');
    upgrade_log_display_entry('quiz', 'attempt', 'quiz', 'name');
    upgrade_log_display_entry('quiz', 'submit', 'quiz', 'name');
    upgrade_log_display_entry('quiz', 'review', 'quiz', 'name');
    upgrade_log_display_entry('quiz', 'editquestions', 'quiz', 'name');
    upgrade_log_display_entry('quiz', 'preview', 'quiz', 'name');
    upgrade_log_display_entry('quiz', 'start attempt', 'quiz', 'name');
    upgrade_log_display_entry('quiz', 'close attempt', 'quiz', 'name');
    upgrade_log_display_entry('quiz', 'continue attempt', 'quiz', 'name');

    $record = new object();
    $record->name         = 'overview';
    $record->displayorder = '10000';
    $DB->insert_record('quiz_report', $record);

    $record = new object();
    $record->name         = 'responses';
    $record->displayorder = '9000';
    $DB->insert_record('quiz_report', $record);

    $record = new object();
    $record->name         = 'grading';
    $record->displayorder = '6000';
    $DB->insert_record('quiz_report', $record);

}

<?php

// This file replaces:
//   * STATEMENTS section in db/install.xml
//   * lib.php/modulename_install() post installation hook
//   * partially defaults.php

function xmldb_quiz_install() {
    global $DB;

    $record = new stdClass();
    $record->name         = 'overview';
    $record->displayorder = '10000';
    $DB->insert_record('quiz_report', $record);

    $record = new stdClass();
    $record->name         = 'responses';
    $record->displayorder = '9000';
    $DB->insert_record('quiz_report', $record);

    $record = new stdClass();
    $record->name         = 'grading';
    $record->displayorder = '6000';
    $DB->insert_record('quiz_report', $record);

}

<?php  // $Id$

// This file keeps track of upgrades to
// the quiz module
//
// Sometimes, changes between versions involve
// alterations to database structures and other
// major things that may break installations.
//
// The upgrade function in this file will attempt
// to perform all the necessary actions to upgrade
// your older installtion to the current version.
//
// If there's something it cannot do itself, it
// will tell you what you need to do.
//
// The commands in here will all be database-neutral,
// using the methods of database_manager class

function xmldb_quiz_upgrade($oldversion=0) {

    global $CFG, $THEME, $DB;
    
    $dbman = $DB->get_manager();

    $result = true;

//===== 1.9.0 upgrade line ======//

    if ($result && $oldversion < 2008062000) {

    /// Define table quiz_report to be created
        $table = new xmldb_table('quiz_report');

    /// Adding fields to table quiz_report
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, null, null, null, null, null);
        $table->add_field('displayorder', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);

    /// Adding keys to table quiz_report
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

    /// Conditionally launch create table for quiz_report
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        upgrade_mod_savepoint($result, 2008062000, 'quiz');
    }

    if ($result && $oldversion < 2008062001) {
        $reporttoinsert = new object();
        $reporttoinsert->name = 'overview';
        $reporttoinsert->displayorder = 10000;
        $result = $result && $DB->insert_record('quiz_report', $reporttoinsert);

        $reporttoinsert = new object();
        $reporttoinsert->name = 'responses';
        $reporttoinsert->displayorder = 9000;
        $result = $result && $DB->insert_record('quiz_report', $reporttoinsert);

        $reporttoinsert = new object();
        $reporttoinsert->name = 'statistics';
        $reporttoinsert->displayorder = 8000;
        $result = $result && $DB->insert_record('quiz_report', $reporttoinsert);

        $reporttoinsert = new object();
        $reporttoinsert->name = 'regrade';
        $reporttoinsert->displayorder = 7000;
        $result = $result && $DB->insert_record('quiz_report', $reporttoinsert);

        $reporttoinsert = new object();
        $reporttoinsert->name = 'grading';
        $reporttoinsert->displayorder = 6000;
        $result = $result && $DB->insert_record('quiz_report', $reporttoinsert);

        upgrade_mod_savepoint($result, 2008062001, 'quiz');
    }
    
    if ($result and $oldversion < 2008072401) {
        $eventdata = new object();
        $eventdata->modulename = 'quiz';
        $eventdata->modulefile = 'mod/quiz/index.php';
        events_trigger('message_provider_register', $eventdata);
        
        upgrade_mod_savepoint($result, 2008072401, 'quiz');
    }



    return $result;
}

?>

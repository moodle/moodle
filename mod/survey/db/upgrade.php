<?php
// This file keeps track of upgrades to
// the survey module
//
// Sometimes, changes between versions involve
// alterations to database structures and other
// major things that may break installations.
//
// The upgrade function in this file will attempt
// to perform all the necessary actions to upgrade
// your older installation to the current version.
//
// If there's something it cannot do itself, it
// will tell you what you need to do.
//
// The commands in here will all be database-neutral,
// using the methods of database_manager class
//
// Please do not forget to use upgrade_set_timeout()
// before any action that may take longer time to finish.

defined('MOODLE_INTERNAL') || die();

function xmldb_survey_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager(); // Loads ddl manager and xmldb classes.

    // Moodle v3.1.0 release upgrade line.
    // Put any upgrade step following this.
    if ($oldversion < 2016061400) {

        // Define field completionsubmit to be added to survey.
        $table = new xmldb_table('survey');
        $field = new xmldb_field('completionsubmit', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'questions');

        // Conditionally launch add field completionsubmit.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Survey savepoint reached.
        upgrade_mod_savepoint(true, 2016061400, 'survey');
    }

    // Automatically generated Moodle v3.2.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.3.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.4.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}

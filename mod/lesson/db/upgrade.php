<?php

// This file keeps track of upgrades to
// the lesson module
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
//
// Please do not forget to use upgrade_set_timeout()
// before any action that may take longer time to finish.

function xmldb_lesson_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();
    $result = true;

//===== 1.9.0 upgrade line ======//

    if ($result && $oldversion < 2007072201) {

        $table = new xmldb_table('lesson');
        $field = new xmldb_field('usegrademax');
        $field2 = new xmldb_field('usemaxgrade');

    /// Rename lesson->usegrademax to lesson->usemaxgrade. Some old sites can have it incorrect. MDL-13177
        if ($dbman->field_exists($table, $field) && !$dbman->field_exists($table, $field2)) {
        /// Set field specs
            $field->set_attributes(XMLDB_TYPE_INTEGER, '3', null, XMLDB_NOTNULL, null, '0', 'ongoing');
        /// Launch rename field usegrademax to usemaxgrade
            $dbman->rename_field($table, $field, 'usemaxgrade');
        }

        upgrade_mod_savepoint($result, 2007072201, 'lesson');
    }

    if ($result && $oldversion < 2008112601) {
        require_once($CFG->dirroot.'/mod/lesson/lib.php');

        lesson_upgrade_grades();

        upgrade_mod_savepoint($result, 2008112601, 'lesson');
    }

    return $result;
}



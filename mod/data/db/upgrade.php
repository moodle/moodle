<?php
// This file keeps track of upgrades to
// the data module
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

function xmldb_data_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2017032800) {

        // Define field completionentries to be added to data. Require a number of entries to be considered complete.
        $table = new xmldb_table('data');
        $field = new xmldb_field('completionentries', XMLDB_TYPE_INTEGER, '10', null, null, null, '0', 'config');

        // Conditionally launch add field timemodified.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Data savepoint reached.
        upgrade_mod_savepoint(true, 2017032800, 'data');
    }

    // Automatically generated Moodle v3.3.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.4.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.5.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.6.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.7.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2019052001) {

        $columns = $DB->get_columns('data');

        $oldclass = "mod-data-default-template ##approvalstatus##";
        $newclass = "mod-data-default-template ##approvalstatusclass##";

        // Update existing classes.
        $DB->replace_all_text('data', $columns['singletemplate'], $oldclass, $newclass);
        $DB->replace_all_text('data', $columns['listtemplate'], $oldclass, $newclass);
        $DB->replace_all_text('data', $columns['addtemplate'], $oldclass, $newclass);
        $DB->replace_all_text('data', $columns['rsstemplate'], $oldclass, $newclass);
        $DB->replace_all_text('data', $columns['asearchtemplate'], $oldclass, $newclass);

        // Data savepoint reached.
        upgrade_mod_savepoint(true, 2019052001, 'data');
    }
    // Automatically generated Moodle v3.8.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}

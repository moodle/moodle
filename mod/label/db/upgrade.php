<?php

// This file keeps track of upgrades to
// the label module
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

function xmldb_label_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

//===== 1.9.0 upgrade line ======//

    if ($oldversion < 2007101510) {
        $sql = "UPDATE {log_display} SET mtable = 'label' WHERE module = 'label'";
        $DB->execute($sql);
        upgrade_mod_savepoint(true, 2007101510, 'label');
    }

    if ($oldversion < 2009042200) {

    /// Rename field content on table label to intro
        $table = new xmldb_table('label');
        $field = new xmldb_field('content', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null, 'name');

    /// Launch rename field content
        $dbman->rename_field($table, $field, 'intro');

    /// label savepoint reached
        upgrade_mod_savepoint(true, 2009042200, 'label');
    }

    if ($oldversion < 2009042201) {

    /// Define field introformat to be added to label
        $table = new xmldb_table('label');
        $field = new xmldb_field('introformat', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, null, null, '0', 'intro');

    /// Launch add field introformat
        $dbman->add_field($table, $field);

    /// label savepoint reached
        upgrade_mod_savepoint(true, 2009042201, 'label');
    }

    if ($oldversion < 2009080400) {

    /// Define field introformat to be added to label
        $table = new xmldb_table('label');
        $field = new xmldb_field('introformat', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, null, null, '0', 'intro');

    /// Launch add field introformat
        $dbman->change_field_default($table, $field);

    /// Convert existing markdown formats to 0 (due to an existing bug in early versions of label upgrade, defaulting to 4)
        $DB->set_field('label', 'introformat', 0, array('introformat' => 4));

    /// label savepoint reached
        upgrade_mod_savepoint(true, 2009080400, 'label');
    }

    return true;
}



<?php  //$Id$

// This file keeps track of upgrades to
// the data module
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

function xmldb_data_upgrade($oldversion=0) {

    global $CFG, $THEME, $DB;

    $dbman = $DB->get_manager();

    $result = true;

//===== 1.9.0 upgrade line ======//

    if ($result && $oldversion < 2007101512) {
    /// Launch add field asearchtemplate again if does not exists yet - reported on several sites

        $table = new xmldb_table('data');
        $field = new xmldb_field('asearchtemplate', XMLDB_TYPE_TEXT, 'small', null, null, null, null, null, null, 'jstemplate');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_mod_savepoint($result, 2007101512, 'data');
    }

    if ($result && $oldversion <  2007101513) {
        // Upgrade all the data->notification currently being
        // NULL to 0
        $sql = "UPDATE {data} SET notification=0 WHERE notification IS NULL";
        $result = $DB->execute($sql);

        $table = new xmldb_table('data');
        $field = new xmldb_field('notification', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'editany');
        // First step, Set NOT NULL
        $dbman->change_field_notnull($table, $field);
        // Second step, Set default to 0
        $dbman->change_field_default($table, $field);
        upgrade_mod_savepoint($result, 2007101513, 'data');
    }

    if ($result && $oldversion < 2008081400) {
        if ($datainstances = $DB->get_records('data')) {
            $pattern = '/\#\#delete\#\#(\s+)\#\#approve\#\#/';
            $replacement = '##delete##$1##approve##$1##export##';
            foreach ($datainstances as $data) {
                $data->listtemplate = preg_replace($pattern, $replacement, $data->listtemplate);
                $data->singletemplate = preg_replace($pattern, $replacement, $data->singletemplate);
                $DB->update_record('data', $data);
            }
        }
    }

    return $result;
}

?>

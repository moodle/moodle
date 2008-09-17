<?php  // $Id$

// This file keeps track of upgrades to 
// the calculated qtype plugin
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

function xmldb_qtype_calculated_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();
    $result = true;

    // MDL-16505.
    if ($result && $oldversion < 2008091700) { //New version in version.php
        if (get_config('qtype_datasetdependent', 'version')) {
            $result = $result && unset_config('version', 'qtype_datasetdependent');
        }
        upgrade_plugin_savepoint($result, 2008091700, 'qtype', 'calculated');
    }

/// if ($result && $oldversion < YYYYMMDD00) { //New version in version.php
///     $result = result of database_manager methods
///     upgrade_plugin_savepoint($result, YYYYMMDD00, 'qtype', 'calculated');
/// }

    return $result;
}

?>

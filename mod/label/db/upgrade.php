<?php  //$Id$

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
    $result = true;

//===== 1.9.0 upgrade line ======//

    if ($oldversion < 2007101510) {
        $sql = "UPDATE {log_display} SET mtable = 'label' WHERE module = 'label'";
        $result = $DB->execute($sql);
        upgrade_mod_savepoint($result, 2007101510, 'label');
    }

    return $result;
}

?>

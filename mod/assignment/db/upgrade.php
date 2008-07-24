<?php  //$Id$

// This file keeps track of upgrades to
// the assignment module
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

function xmldb_assignment_upgrade($oldversion=0) {

    global $CFG, $THEME, $DB;

    $result = true;

//===== 1.9.0 upgrade line ======//

    if ($result && $oldversion < 2007101511) {
        notify('Processing assignment grades, this may take a while if there are many assignments...', 'notifysuccess');
        // change grade typo to text if no grades MDL-13920
        require_once $CFG->dirroot.'/mod/assignment/lib.php';
        // too much debug output
        $DB->set_debug(false);
        assignment_update_grades();
        $DB->set_debug(true);
        upgrade_mod_savepoint($result, 2007101511, 'assignment');
    }
    
    if ($result && $oldversion < 2008072401) {
        $eventdata = new object();
        $eventdata->modulename = 'assignment';
        $eventdata->modulefile = 'mod/assignment/index.php';
        events_trigger('message_provider_register', $eventdata); 
        
        upgrade_mod_savepoint($result, 2008072401, 'assignment');
    }

    return $result;
}

?>

<?php  //$Id$

// This file keeps track of upgrades to the hotpot module

function xmldb_hotpot_upgrade($oldversion=0) {

    global $CFG, $THEME, $db;

    $result = true;

//===== 1.9.0 upgrade line ======//

    // update hotpot grades from sites earlier than Moodle 1.9, 27th March 2008
    if ($result && $oldversion < 2007101511) {

        // ensure "hotpot_update_grades" function is available
        require_once $CFG->dirroot.'/mod/hotpot/lib.php';

        // disable display of debugging messages
        $db_debug_save = $db->debug;
        $db->debug = false;

        notify('Processing hotpot grades, this may take a while if there are many hotpots...', 'notifysuccess');
        hotpot_update_grades();

        // restore $db->debug
        $db->debug = $db_debug_save;
    }

    return $result;
}

?>

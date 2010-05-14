<?php

// This file keeps track of upgrades to the hotpot module
//
// Please do not forget to use upgrade_set_timeout()
// before any action that may take longer time to finish.

function xmldb_hotpot_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();
    $result = true;

//===== 1.9.0 upgrade line ======//

    // update hotpot grades from sites earlier than Moodle 1.9, 27th March 2008
    if ($result && $oldversion < 2007101511) {
        // ensure "hotpot_upgrade_grades" function is available
        require_once $CFG->dirroot.'/mod/hotpot/lib.php';
        hotpot_upgrade_grades();
        upgrade_mod_savepoint($result, 2007101511, 'hotpot');
    }

    if ($result && $oldversion < 2008011200) {
        // remove not used setting
        unset_config('hotpot_initialdisable');
        upgrade_mod_savepoint($result, 2008011200, 'hotpot');
    }

    return $result;
}

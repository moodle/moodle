<?PHP // $Id$

////////////////////////////////////////////////////////////////////////////////
//  Code fragment to define the version of quiz
//  This fragment is called by moodle_needs_upgrading() and /admin/index.php
////////////////////////////////////////////////////////////////////////////////

$module->version  = 2002100300;   // The (date) version of this module
$module->cron     = 0;            // How often should cron check this module (seconds)?

function quiz_upgrade($oldversion) {
// This function does anything necessary to upgrade
// older versions to match current functionality

    global $CFG;

    if ($oldversion < 2002100300) {

    }

    return true;
}

?>

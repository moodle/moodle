<?PHP // $Id$

////////////////////////////////////////////////////////////////////////////////
//  Code fragment to define the module version etc.
//  This fragment is called by /admin/index.php
////////////////////////////////////////////////////////////////////////////////

$module->version  = 20020801;
$module->cron     = 60;

function assignment_upgrade($oldversion) {
// This function does anything necessary to upgrade
// older versions to match current functionality

    return true;
}


?>


<?PHP // $Id$

////////////////////////////////////////////////////////////////////////////////
//  Code fragment to define the module version etc.
//  This fragment is called by /admin/index.php
////////////////////////////////////////////////////////////////////////////////

$module->fullname = "Choice";
$module->version  = 20011110;
$module->cron     = 0;
$module->search   = "";

function choice_upgrade($oldversion) {
// This function does anything necessary to upgrade
// older versions to match current functionality

    return true;
}


?>


<?PHP // $Id$

////////////////////////////////////////////////////////////////////////////////
//  Code fragment to define the module version etc.
//  This fragment is called by /admin/index.php
////////////////////////////////////////////////////////////////////////////////

$module->version  = 2002080500;
$module->cron     = 0;

function survey_upgrade($oldversion) {
// This function does anything necessary to upgrade
// older versions to match current functionality

    return true;
}


?>


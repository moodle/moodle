<?PHP // $Id$

////////////////////////////////////////////////////////////////////////////////
//  Code fragment to define the module version etc.
//  This fragment is called by /admin/index.php
////////////////////////////////////////////////////////////////////////////////

$module->version  = 2002090800;
$module->cron     = 0;

function choice_upgrade($oldversion) {
// This function does anything necessary to upgrade
// older versions to match current functionality

    if ($oldversion < 2002090800) {
        execute_sql(" ALTER TABLE `choice` CHANGE `answer1` `answer1` VARCHAR( 255 )");
        execute_sql(" ALTER TABLE `choice` CHANGE `answer2` `answer2` VARCHAR( 255 )");
    }
    return true;
}


?>


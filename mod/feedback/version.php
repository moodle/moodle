<?php // $Id$
/**
* Code fragment to define the version of feedback
* This fragment is called by moodle_needs_upgrading() and /admin/index.php
*
* @version $Id$
* @author Andreas Grabs
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
* @package feedback
*/

   
    $module->version = 2008072401; // The current module version (Date: YYYYMMDDXX)
    $module->requires = 2008072401;  // Requires this Moodle version
    $feedback_version_intern = 1; //this version is used for restore older backups
    $module->cron = 0; // Period for cron to check this module (secs)

?>

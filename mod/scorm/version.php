<?PHP // $Id$

/////////////////////////////////////////////////////////////////////////////////
///  Code fragment to define the version of scorm
///  This fragment is called by moodle_needs_upgrading() and /admin/index.php
/////////////////////////////////////////////////////////////////////////////////

$module->version  = 2004070800;   // The (date) version of this module
$module->requires = 2004051600;   // The version of Moodle that is required
$module->cron     = 0;            // How often should cron check this module (seconds)?

?>

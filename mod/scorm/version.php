<?php // $Id$

/////////////////////////////////////////////////////////////////////////////////
///  Code fragment to define the version of scorm
///  This fragment is called by moodle_needs_upgrading() and /admin/index.php
/////////////////////////////////////////////////////////////////////////////////

$module->version  = 2006102702;   // The (date) version of this module  (NOTE THIS WAS SET WRONG TO AN OCTOBER DATE IN SEPTEMBER!)
$module->requires = 2006080900;   // The version of Moodle that is required
$module->cron     = 0;            // How often should cron check this module (seconds)?

?>

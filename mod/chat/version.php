<?php // $Id$

/////////////////////////////////////////////////////////////////////////////////
///  Code fragment to define the version of chat
///  This fragment is called by moodle_needs_upgrading() and /admin/index.php
/////////////////////////////////////////////////////////////////////////////////

$module->version  = 2009031100;   // The (date) version of this module
$module->requires = 2007101509;  // Requires this Moodle version
$module->cron     = 300;          // How often should cron check this module (seconds)?

?>

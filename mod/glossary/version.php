<?php // $Id$

/////////////////////////////////////////////////////////////////////////////////
///  Code fragment to define the version of glossary
///  This fragment is called by moodle_needs_upgrading() and /admin/index.php
/////////////////////////////////////////////////////////////////////////////////

$module->version  = 2007060300;
$module->requires = 2007060100;  // Requires this Moodle version
$module->cron     = 0;           // Period for cron to check this module (secs)

?>

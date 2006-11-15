<?php // $Id$

/////////////////////////////////////////////////////////////////////////////////
///  Code fragment to define the version of glossary
///  This fragment is called by moodle_needs_upgrading() and /admin/index.php
/////////////////////////////////////////////////////////////////////////////////

$module->version  = 2006111400;
$module->requires = 2006082600;  // Requires this Moodle version
$module->cron     = 0;           // Period for cron to check this module (secs)

?>

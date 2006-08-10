<?php // $Id$

/////////////////////////////////////////////////////////////////////////////////
///  Code fragment to define the version of glossary
///  This fragment is called by moodle_needs_upgrading() and /admin/index.php
/////////////////////////////////////////////////////////////////////////////////

$module->version  = 2006080701;  // change only last two digits in 1.6.x!!
$module->requires = 2006050512;  // Requires this Moodle version
$module->cron     = 0;           // Period for cron to check this module (secs)

?>

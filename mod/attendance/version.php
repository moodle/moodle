<?php // $Id$

/////////////////////////////////////////////////////////////////////////////////
///  Code fragment to define the version of attendance
///  This fragment is called by moodle_needs_upgrading() and /admin/index.php
/////////////////////////////////////////////////////////////////////////////////

$module->version  = 2005021600;  // The current module version (Date: YYYYMMDDXX)
$module->requires = 2005021600;  // Requires this Moodle version
$module->cron     = 3600;        // Period for cron to check this module (secs)

?>

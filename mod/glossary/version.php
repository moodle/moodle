<?PHP // $Id$

/////////////////////////////////////////////////////////////////////////////////
///  Code fragment to define the version of glossary
///  This fragment is called by moodle_needs_upgrading() and /admin/index.php
/////////////////////////////////////////////////////////////////////////////////

$module->version  = 2004072700;  // The current module version (Date: YYYYMMDDXX)
$module->requires = 2004052501;  // Requires this Moodle version
$module->cron     = 0;           // Period for cron to check this module (secs)

$release = "1.4 development";   // User-friendly version number

?>

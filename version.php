<?PHP // $Id: version.php,v 1.2 2007/04/06 17:13:31 skodak Exp $

/////////////////////////////////////////////////////////////////////////////////
///  Called by moodle_needs_upgrading() and /admin/index.php
/////////////////////////////////////////////////////////////////////////////////

$module->version  = 2007040600;  // The current module version (Date: YYYYMMDDXX)
$module->requires = 2006101020;  // Requires this Moodle version
$module->cron     = 0;           // Period for cron to check this module (secs)

$release = "1.3alpha+";          // User-friendly version number

?>

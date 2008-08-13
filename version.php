<?PHP // $Id: version.php,v 1.5 2008/08/13 23:21:13 skodak Exp $

/////////////////////////////////////////////////////////////////////////////////
///  Called by moodle_needs_upgrading() and /admin/index.php
/////////////////////////////////////////////////////////////////////////////////

$module->version  = 2008081402;  // The current module version (Date: YYYYMMDDXX)
$module->requires = 2007101512;  // Requires this Moodle version
$module->cron     = 0;           // Period for cron to check this module (secs)

$release = "1.5beta";             // User-friendly version number

?>

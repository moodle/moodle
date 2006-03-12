<?PHP // $Id: version.php,v 1.1 2006/03/12 18:39:59 skodak Exp $

/////////////////////////////////////////////////////////////////////////////////
///  Called by moodle_needs_upgrading() and /admin/index.php
/////////////////////////////////////////////////////////////////////////////////

$module->version  = 2006031200;  // The current module version (Date: YYYYMMDDXX)
$module->requires = 2006031000;  // Requires this Moodle version
$module->cron     = 0;           // Period for cron to check this module (secs)

$release = "1.3alpha";                // User-friendly version number

?>

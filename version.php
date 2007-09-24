<?PHP // $Id: version.php,v 1.4 2007/09/24 08:47:36 skodak Exp $

/////////////////////////////////////////////////////////////////////////////////
///  Called by moodle_needs_upgrading() and /admin/index.php
/////////////////////////////////////////////////////////////////////////////////

$module->version  = 2007052001;  // The current module version (Date: YYYYMMDDXX)
$module->requires = 2007021505;  // Requires this Moodle version
$module->cron     = 0;           // Period for cron to check this module (secs)

$release = "1.4alpha";          // User-friendly version number

?>

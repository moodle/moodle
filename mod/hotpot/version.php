<?PHP // $Id$
/////////////////////////////////////////////////////////////////////////////////
///  Code fragment to define the version of hotpot
///  This fragment is called by moodle_needs_upgrading() and /admin/index.php
/////////////////////////////////////////////////////////////////////////////////
$module->version  = 2007040200;   // release date of this version (see note below)
$module->release  = 'v2.4.2';    // human-friendly version name (used in mod/hotpot/lib.php)
$module->requires = 2007020200;  // Requires this Moodle version
$module->cron     = 0;            // period for cron to check this module (secs)
// interpretation of YYYYMMDDXY version numbers
//     YYYY : year
//     MM   : month
//     DD   : day
//     X    : point release version 1,2,3 etc
//     Y    : increment between point releases
?>

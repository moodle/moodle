<?PHP // $Id$

/////////////////////////////////////////////////////////////////////////////////
///  Code fragment to define the version of hotpot
///  This fragment is called by moodle_needs_upgrading() and /admin/index.php
/////////////////////////////////////////////////////////////////////////////////

$module->version  = 2005031420;  // release date of this version (see note below)
$module->release  = 'v2.0.10';    // human-friendly version name (used in mod/hotpot/lib.php)
$module->requires = 2003091111;  // Requires at least Moodle version 1.1.1
$module->cron     = 0;           // period for cron to check this module (secs)

// interpretation of YYYYMMDDXY version numbers
//     YYYY : year
//     MM   : month
//     DD   : day
//     X    : point release version 1,2,3 etc
//     Y    : increment between point releases

?>

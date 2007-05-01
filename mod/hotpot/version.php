<?PHP // $Id$
/////////////////////////////////////////////////////////////////////////////////
///  Code fragment to define the version of hotpot
///  This fragment is called by moodle_needs_upgrading() and /admin/index.php
/////////////////////////////////////////////////////////////////////////////////
$module->version  = 2006082901;   // change only last two digits in 1.6.x!!
$module->release  = 'v2.1.24';    // human-friendly version name (used in mod/hotpot/lib.php)
$module->requires = 2003091111;   // replace with 2005060241 if you want to use it with latest 1.5.4+
$module->cron     = 0;            // period for cron to check this module (secs)
// interpretation of YYYYMMDDXY version numbers
//     YYYY : year
//     MM   : month
//     DD   : day
//     X    : point release version 1,2,3 etc
//     Y    : increment between point releases
?>

<?php // $Id$

/////////////////////////////////////////////////////////////////////////////////
///  Code fragment to define the version of glossary
///  This fragment is called by moodle_needs_upgrading() and /admin/index.php
/////////////////////////////////////////////////////////////////////////////////

$module->version  = 2004091700;  // YYYYMMDD   = current module version
                                 //         X  = point release version 1,2,3 etc
                                 //          Y = increments between point releases
$module->requires = 2004091700;  // Requires this Moodle version
$module->cron     = 0;           // Period for cron to check this module (secs)

$release = "1.5 development";   // User-friendly version number

?>

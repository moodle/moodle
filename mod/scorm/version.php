<?php // $Id$

/////////////////////////////////////////////////////////////////////////////////
///  Code fragment to define the version of scorm
///  This fragment is called by moodle_needs_upgrading() and /admin/index.php
/////////////////////////////////////////////////////////////////////////////////


// NOTE  The version below was accidentally set a month into the future!  We need to 
//       catch up now, so until 27th October please only increment in very tiny steps 
//       in HEAD, until we get past that date..

$module->version  = 2007110503;   // The (date) version of this module
$module->requires = 2007101509;   // The version of Moodle that is required
$module->cron     = 300;            // How often should cron check this module (seconds)?

?>
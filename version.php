<?php

/////////////////////////////////////////////////////////////////////////////////
///  Called by moodle_needs_upgrading() and /admin/index.php
/////////////////////////////////////////////////////////////////////////////////

$module->version  = 2010120803;  // The current module version (Date: YYYYMMDDXX)
$module->requires = 2010120700;  // Requires this Moodle version
$module->cron     = 0;           // Period for cron to check this module (secs)

$release = "2.0dev";             // User-friendly version number

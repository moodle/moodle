<?php

/////////////////////////////////////////////////////////////////////////////////
///  Code fragment to define the version of scorm
///  This fragment is called by moodle_needs_upgrading() and /admin/index.php
/////////////////////////////////////////////////////////////////////////////////


$module->version  = 2010070800;   // The (date) version of this module
$module->requires = 2009041700;   // The version of Moodle that is required
$module->cron     = 300;            // How often should cron check this module (seconds)?


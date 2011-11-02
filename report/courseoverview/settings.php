<?php

defined('MOODLE_INTERNAL') || die;

$ADMIN->add('reports', new admin_externalpage('reportcourseoverview', get_string('pluginname', 'report_courseoverview'), "$CFG->wwwroot/report/courseoverview/index.php",'report/courseoverview:view'));

// no report settings
$settings = null;

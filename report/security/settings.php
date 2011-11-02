<?php

defined('MOODLE_INTERNAL') || die;

$ADMIN->add('reports', new admin_externalpage('reportsecurity', get_string('pluginname', 'report_security'), "$CFG->wwwroot/report/security/index.php",'report/security:view'));

// no report settings
$settings = null;

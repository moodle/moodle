<?php

defined('MOODLE_INTERNAL') || die;

$ADMIN->add('reports', new admin_externalpage('reportconfiglog', get_string('configlog', 'report_configlog'), "$CFG->wwwroot/$CFG->admin/report/configlog/index.php"));

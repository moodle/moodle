<?php

defined('MOODLE_INTERNAL') || die;

$ADMIN->add('roles', new admin_externalpage('reportcapability', get_string('pluginname', 'report_capability'), "$CFG->wwwroot/$CFG->admin/report/capability/index.php",'moodle/role:manage'));

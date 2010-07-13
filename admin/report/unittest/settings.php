<?php

defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) {
    $ADMIN->add('development', new admin_externalpage('reportsimpletest', get_string('simpletest', 'admin'), "$CFG->wwwroot/$CFG->admin/report/unittest/index.php",'report/unittest:view'));
    $ADMIN->add('development', new admin_externalpage('reportdbtest', get_string('dbtest', 'admin'), "$CFG->wwwroot/$CFG->admin/report/unittest/dbtest.php",'report/unittest:view'));
}

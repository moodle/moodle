<?php  //$Id$
$ADMIN->add('development', new admin_externalpage('reportsimpletest', get_string('simpletest', 'admin'), "$CFG->wwwroot/$CFG->admin/report/simpletest/index.php",'moodle/site:config'));
$ADMIN->add('development', new admin_externalpage('reportdbtest', get_string('dbtest', 'admin'), "$CFG->wwwroot/$CFG->admin/report/simpletest/dbtest.php",'moodle/site:config'));
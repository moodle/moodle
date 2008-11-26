<?php  //$Id$
$ADMIN->add('reports', new admin_externalpage('reportunittest', get_string('simpletest', 'admin'), "$CFG->wwwroot/$CFG->admin/report/unittest/index.php", 'report/unittest:view'));
?>
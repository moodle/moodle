<?php  //$Id$

$reportname = get_string('simpletest', 'report_simpletest');
if ($reportname[1] == '[') {
    $reportname = get_string('simpletest', 'admin');
}
$ADMIN->add('reports', new admin_externalpage('reportsimpletest', $reportname, "$CFG->wwwroot/$CFG->admin/report/simpletest/index.php",'moodle/site:config'));

//TODO: localise
$ADMIN->add('reports', new admin_externalpage('reportdbtest', 'Functional DB tests', "$CFG->wwwroot/$CFG->admin/report/simpletest/dbtest.php",'moodle/site:config', true));

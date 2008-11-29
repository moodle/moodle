<?php  // $Id$
// just a link to course report
$ADMIN->add('reports', new admin_externalpage('reportlog', get_string('log', 'admin'), "$CFG->wwwroot/course/report/log/index.php?id=".SITEID, 'coursereport/log:view'));
?>
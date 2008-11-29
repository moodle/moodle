<?php  // $Id$
// just a link to course report
$ADMIN->add('reports', new admin_externalpage('reportstats', get_string('stats', 'admin'), "$CFG->wwwroot/course/report/stats/index.php", 'coursereport/stats:view'));
?>
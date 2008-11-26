<?php  // $Id$
$ADMIN->add('reports', new admin_externalpage('reportlog', get_string('log', 'admin'), "$CFG->wwwroot/$CFG->admin/report/log/index.php",'moodle/site:viewreports'));
?>
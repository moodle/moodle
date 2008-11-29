<?php  // $Id$
$ADMIN->add('reports', new admin_externalpage('reportcourseoverview', get_string('courseoverview', 'admin'), "$CFG->wwwroot/$CFG->admin/report/courseoverview/index.php",'report/courseoverview:view'));
?>
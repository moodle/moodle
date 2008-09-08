<?php  // $Id$
$ADMIN->add('reports', new admin_externalpage('reportbackups', get_string('backups', 'admin'), "$CFG->wwwroot/$CFG->admin/report/backups/index.php",'moodle/site:backup'));
?>
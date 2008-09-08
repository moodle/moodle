<?php  // $Id$
if (!empty($CFG->enablestats)) {
    $ADMIN->add('reports', new admin_externalpage('reportstats', get_string('stats', 'admin'), "$CFG->wwwroot/$CFG->admin/report/stats/index.php",'moodle/site:viewreports'));
}
?>
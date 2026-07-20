<?php
defined('MOODLE_INTERNAL') || die();
$ADMIN->add('reports', new admin_externalpage('courseanalyticsreport', get_string('report', 'local_courseanalytics'), new moodle_url('/local/courseanalytics/report.php'), 'local/courseanalytics:viewreport'));

<?php

defined('MOODLE_INTERNAL') || die;

// just a link to course report
$ADMIN->add('reports', new admin_externalpage('reportlog', get_string('log', 'admin'), "$CFG->wwwroot/course/report/log/index.php?id=".SITEID, 'coursereport/log:view'));
$ADMIN->add('reports', new admin_externalpage('reportloglive', get_string('loglive', 'coursereport_log'), "$CFG->wwwroot/course/report/log/indexlive.php?id=".SITEID, 'coursereport/log:viewlive'));

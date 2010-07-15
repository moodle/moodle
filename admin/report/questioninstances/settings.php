<?php

defined('MOODLE_INTERNAL') || die;

$ADMIN->add('reports', new admin_externalpage('reportquestioninstances', get_string('pluginname', 'report_questioninstances'), "$CFG->wwwroot/$CFG->admin/report/questioninstances/index.php", 'report/questioninstances:view'));

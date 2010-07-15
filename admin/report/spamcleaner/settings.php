<?php

defined('MOODLE_INTERNAL') || die;

// spam cleaner
$ADMIN->add('reports', new admin_externalpage('reportspamcleaner', get_string('pluginname', 'report_spamcleaner'), "$CFG->wwwroot/$CFG->admin/report/spamcleaner/index.php", 'moodle/site:config'));


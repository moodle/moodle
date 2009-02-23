<?php

// spam cleaner
$ADMIN->add('reports', new admin_externalpage('reportspamcleaner', get_string('spamcleaner','report_spamcleaner'), "$CFG->wwwroot/$CFG->admin/report/spamcleaner/index.php", 'moodle/site:config'));


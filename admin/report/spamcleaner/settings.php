<?php

// spam cleaner
$ADMIN->add('reports', new admin_externalpage('spamcleaner', get_string('spamcleaner','admin'), "$CFG->wwwroot/$CFG->admin/report/spamcleaner/index.php", 'moodle/site:config'));


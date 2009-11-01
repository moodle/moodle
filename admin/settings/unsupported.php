<?php

// This file defines settingpages and externalpages in the "unsupported" hidden category, use wisely!

if ($hassiteconfig) { // speedup for non-admins, add all caps used on this page

    $ADMIN->add('unsupported', new admin_externalpage('purgemoodledata', 'Purge moodledata', $CFG->wwwroot.'/'.$CFG->admin.'/delete.php', 'moodle/site:config', true));
    $ADMIN->add('unsupported', new admin_externalpage('healthcenter', get_string('healthcenter'), $CFG->wwwroot.'/'.$CFG->admin.'/health.php', 'moodle/site:config', true));
    $ADMIN->add('unsupported', new admin_externalpage('toinodb', 'Convert to InnoDB', $CFG->wwwroot.'/'.$CFG->admin.'/innodb.php', 'moodle/site:config', true));
    $ADMIN->add('unsupported', new admin_externalpage('replace', 'Search and replace', $CFG->wwwroot.'/'.$CFG->admin.'/replace.php', 'moodle/site:config', true));

} // end of speedup

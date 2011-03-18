<?php

defined('MOODLE_INTERNAL') || die;

// profiling report, added to development
if (extension_loaded('xhprof') && function_exists('xhprof_enable') && (!empty($CFG->profilingenabled) || !empty($CFG->earlyprofilingenabled))) {
    $ADMIN->add('development', new admin_externalpage('reportprofiling', get_string('pluginname', 'report_profiling'), "$CFG->wwwroot/$CFG->admin/report/profiling/index.php", 'moodle/site:config'));
}

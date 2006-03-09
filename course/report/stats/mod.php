<?php

    if (!defined('MOODLE_INTERNAL')) {
        die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
    }

    if (!empty($CFG->enablestats)) {
        echo '<a href="'.$CFG->wwwroot.'/course/report/stats/index.php?course='.$course->id.'">'.get_string('stats').'</a>';
    }
?>

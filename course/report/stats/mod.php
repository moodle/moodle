<?php  //$Id$

    if (!defined('MOODLE_INTERNAL')) {
        die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
    }

    if (has_capability('coursereport/stats:view', $context)) {
        if (!empty($CFG->enablestats)) {
            echo '<p>';
            echo '<a href="'.$CFG->wwwroot.'/course/report/stats/index.php?course='.$course->id.'">'.get_string('stats').'</a>';
            echo '</p>';
        } else {
            echo '<p>';
            echo get_string('statsoff');
            echo '</p>';
        }
    }
?>

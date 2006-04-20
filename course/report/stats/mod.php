<?php  //$Id$

    if (!defined('MOODLE_INTERNAL')) {
        die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
    }
    
    echo '<p style="text-align:center;">';
    if (!empty($CFG->enablestats)) {
        echo '<a href="'.$CFG->wwwroot.'/course/report/stats/index.php?course='.$course->id.'">'.get_string('stats').'</a>';
    } else {
        echo '<a href="'.$CFG->wwwroot.'/admin/config.php#configsectionstats">'.get_string('statsoff').'</a>';
    }
    echo '</p>';
?>
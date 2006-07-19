<?php  //$Id$

    if (!defined('MOODLE_INTERNAL')) {
        die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
    }
    
    if (!empty($CFG->enablestats)) {
        echo '<p style="text-align:center;">';
        echo '<a href="'.$CFG->wwwroot.'/'.$CFG->admin.'/report/stats/index.php?course='.$course->id.'">'.get_string('stats').'</a>';
        echo '</p>';
        $statsstatus = stats_check_uptodate($course->id);
        if ($statsstatus !== NULL) {
            notify ($statsstatus);
        }
    }
?>
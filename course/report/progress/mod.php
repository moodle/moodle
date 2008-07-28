<?php  //$Id$

    if (!defined('MOODLE_INTERNAL')) {
        die('Direct access to this script is forbidden.'); // It must be included from a Moodle page
    }

    $completion=new completion_info($course);
    if ($completion->is_enabled() && has_capability('moodle/course:viewprogress',$context)) {
        echo '<p>';
        echo '<a href="'.$CFG->wwwroot.'/course/report/progress/?course='.$course->id.'">'.get_string('completionreport','completion').'</a>';
        echo '</p>';
    } 
?>

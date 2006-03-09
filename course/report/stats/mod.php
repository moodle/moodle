<?php

    if (!empty($CFG->enablestats)) {
        echo '<a href="'.$CFG->wwwroot.'/course/report/stats/index.php?course='.$course->id.'">'.get_string('stats').'</a>';
    }
?>

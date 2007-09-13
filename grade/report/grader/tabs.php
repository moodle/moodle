<?php  // $Id$
    $row = $tabs = array();

    $row[] = new tabobject('graderreport',
                           $CFG->wwwroot.'/grade/report/grader/index.php?id='.$courseid,
                           get_string('modulename', 'gradereport_grader'));
    if (has_capability('moodle/grade:manage', get_context_instance(CONTEXT_COURSE, $COURSE->id))) {
        $row[] = new tabobject('preferences',
                               $CFG->wwwroot.'/grade/report/grader/preferences.php?id='.$courseid,
                               get_string('preferences'));
    }

    $tabs[] = $row;
    echo '<div class="gradedisplay">';
    print_tabs($tabs, $currenttab);
    echo '</div>';
?>

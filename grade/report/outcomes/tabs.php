<?php  // $Id$
    $row = $tabs = array();


    // Needs capability here
    if ($courseid && ($courseid != SITEID)) {

        $row[] = new tabobject('outcomereport',
                               $CFG->wwwroot.'/grade/report/outcomes/index.php?id='.$courseid,
                               get_string('outcomereport', 'grades'));

        $coursecontext = get_context_instance(CONTEXT_COURSE, $courseid);
        if (has_capability('gradereport/outcomes:manage', $coursecontext)) {
            $row[] = new tabobject('courseoutcomes',
                                   $CFG->wwwroot.'/grade/report/outcomes/course.php?id='.$courseid,
                                   get_string('courseoutcomes', 'gradereport_outcomes'));
        }
    }

    $sitecontext = get_context_instance(CONTEXT_SYSTEM);
    if (has_capability('gradereport/outcomes:manage', $sitecontext, NULL, false)) {
        $row[] = new tabobject('siteoutcomes',
                               $CFG->wwwroot.'/grade/report/outcomes/site.php?id='.$courseid,
                               get_string('siteoutcomes', 'gradereport_outcomes'));
    }

    $tabs[] = $row;

    echo '<div class="outcomedisplay">';
    print_tabs($tabs, $currenttab);
    echo '</div>';
?>

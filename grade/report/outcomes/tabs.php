<?php  // $Id$
    $row = $tabs = array();

    $row[] = new tabobject('outcomereport',
                           $CFG->wwwroot.'/grade/report/outcomes/index.php?id='.$courseid,
                           get_string('outcomereport', 'grades'));

    // Needs capability here
    if ($courseid != SITEID) {
        $row[] = new tabobject('courseoutcomes',
                               $CFG->wwwroot.'/grade/report/outcomes/course.php?id='.$courseid,
                               get_string('courseoutcomes', 'gradereport_outcomes'));
    }

    // Needs capability here
    $row[] = new tabobject('siteoutcomes',
                           $CFG->wwwroot.'/grade/report/outcomes/site.php?id='.$courseid,
                           get_string('siteoutcomes', 'gradereport_outcomes'));

    $tabs[] = $row;

    echo '<div class="outcomedisplay">';
    print_tabs($tabs, $currenttab);
    echo '</div>';
?>

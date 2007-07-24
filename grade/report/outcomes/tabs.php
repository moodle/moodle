<?php  // $Id$
    $row = $tabs = array();
    $row[] = new tabobject('outcomereport',
                           $CFG->wwwroot.'/grade/report/outcomes/index.php?id='.$courseid,
                           get_string('outcomereport', 'grades'));

    if ($courseid != SITEID) {
        $row[] = new tabobject('outcomesettings',
                               $CFG->wwwroot.'/grade/report/outcomes/course.php?id='.$courseid,
                               get_string('settings'));
    } else {
        $row[] = new tabobject('outcomesettings',
                               $CFG->wwwroot.'/grade/report/outcomes/site.php?id='.$courseid,
                               get_string('settings'));
    }

    $row[] = new tabobject('editoutcomes',
                           $CFG->wwwroot.'/grade/report/outcomes/editoutcomes.php?courseid='.$courseid,
                           get_string('editoutcomes', 'grades'));

    $tabs[] = $row;
    echo '<div class="outcomedisplay">';
    print_tabs($tabs, $currenttab);
    echo '</div>';
?>

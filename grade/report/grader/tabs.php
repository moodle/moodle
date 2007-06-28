<?php  // $Id$
    $row = $tabs = array();
    $row[] = new tabobject('graderreport',
                           $CFG->wwwroot.'/grade/report.php?id='.$courseid.'&amp;report=grader',
                           get_string('graderreport', 'grades'));

    $row[] = new tabobject('editcategory',
                           $CFG->wwwroot.'/grade/report/grader/category.php?id='.$courseid,
                           get_string('categoriesedit', 'grades'));

    $row[] = new tabobject('preferences',
                           $CFG->wwwroot.'/grade/report/grader/preferences.php?id='.$courseid,
                           get_string('preferences'));

    $tabs[] = $row;
    echo '<div class="gradedisplay">';
    print_tabs($tabs, $currenttab);
    echo '</div>';
?>

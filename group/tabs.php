<?php  // $Id$
    $row = $tabs = array();
    $row[] = new tabobject('groups',
                           $CFG->wwwroot.'/group/index.php?id='.$courseid,
                           get_string('groups'));

    $row[] = new tabobject('groupings',
                           $CFG->wwwroot.'/group/groupings.php?id='.$courseid,
                           get_string('groupings', 'group'));

    $tabs[] = $row;
    echo '<div class="groupdisplay">';
    print_tabs($tabs, $currenttab);
    echo '</div>';
?>

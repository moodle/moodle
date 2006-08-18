<?php
// this page deals with the 2 tabs for manage.php and grant.php

    $toprow[] = new tabobject('manage', $CFG->wwwroot.'/admin/roles/manage.php', get_string('manage'));
    
    $toprow[] = new tabobject('allowassign', $CFG->wwwroot.'/admin/roles/allowassign.php', get_string('allowassign'));

    $toprow[] = new tabobject('allowoverride', $CFG->wwwroot.'/admin/roles/allowoverride.php', get_string('allowoverride'));
    
    $tabs = array($toprow);
    
    print_tabs($tabs, $currenttab);

?>

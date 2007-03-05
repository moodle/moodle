<?php
// this page deals with the 2 tabs for manage.php and grant.php

    if (!defined('MOODLE_INTERNAL')) {
        die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
    }

    $toprow = array();

    $toprow[] = new tabobject('manage', $CFG->wwwroot.'/'.$CFG->admin.'/roles/manage.php', get_string('manageroles', 'role'),'', true);

    $toprow[] = new tabobject('allowassign', $CFG->wwwroot.'/'.$CFG->admin.'/roles/allowassign.php', get_string('allowassign', 'role'));

    $toprow[] = new tabobject('allowoverride', $CFG->wwwroot.'/'.$CFG->admin.'/roles/allowoverride.php', get_string('allowoverride', 'role'));

    $tabs = array($toprow);

    print_tabs($tabs, $currenttab);

?>

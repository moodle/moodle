<?php  //$Id$

// This file replaces:
//   * STATEMENTS section in db/install.xml
//   * lib.php/modulename_install() post installation hook
//   * partially defaults.php

function xmldb_assignment_install() {
    global $DB;

/// Install logging support
    upgrade_log_display_entry('assignment', 'view', 'assignment', 'name');
    upgrade_log_display_entry('assignment', 'add', 'assignment', 'name');
    upgrade_log_display_entry('assignment', 'update', 'assignment', 'name');
    upgrade_log_display_entry('assignment', 'view submission', 'assignment', 'name');
    upgrade_log_display_entry('assignment', 'upload', 'assignment', 'name');

}

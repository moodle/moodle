<?php

// This file replaces:
//   * STATEMENTS section in db/install.xml
//   * lib.php/modulename_install() post installation hook
//   * partially defaults.php

function xmldb_assignment_install() {
    global $DB;

/// Install logging support
    update_log_display_entry('assignment', 'view', 'assignment', 'name');
    update_log_display_entry('assignment', 'add', 'assignment', 'name');
    update_log_display_entry('assignment', 'update', 'assignment', 'name');
    update_log_display_entry('assignment', 'view submission', 'assignment', 'name');
    update_log_display_entry('assignment', 'upload', 'assignment', 'name');

}

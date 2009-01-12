<?php  //$Id$

// This file replaces:
//   * STATEMENTS section in db/install.xml
//   * lib.php/modulename_install() post installation hook
//   * partially defaults.php

function xmldb_data_install() {
    global $DB;

/// Install logging support
    upgrade_log_display_entry('data', 'view', 'data', 'name');
    upgrade_log_display_entry('data', 'add', 'data', 'name');
    upgrade_log_display_entry('data', 'update', 'data', 'name');
    upgrade_log_display_entry('data', 'record delete', 'data', 'name');
    upgrade_log_display_entry('data', 'fields add', 'data_fields', 'name');
    upgrade_log_display_entry('data', 'fields update', 'data_fields', 'name');
    upgrade_log_display_entry('data', 'templates saved', 'data', 'name');
    upgrade_log_display_entry('data', 'templates def', 'data', 'name');

}

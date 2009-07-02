<?php  //$Id$

// This file replaces:
//   * STATEMENTS section in db/install.xml
//   * lib.php/modulename_install() post installation hook
//   * partially defaults.php

function xmldb_resource_install() {
    global $DB;

/// Install logging support

    update_log_display_entry('resource', 'view', 'resource', 'name');
    update_log_display_entry('resource', 'update', 'resource', 'name');
    update_log_display_entry('resource', 'add', 'resource', 'name');

    set_config("resource_hide_repository", "1");
}

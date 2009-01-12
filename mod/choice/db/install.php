<?php  //$Id$

// This file replaces:
//   * STATEMENTS section in db/install.xml
//   * lib.php/modulename_install() post installation hook
//   * partially defaults.php

function xmldb_choice_install() {
    global $DB;

/// Install logging support
    upgrade_log_display_entry('choice', 'view', 'choice', 'name');
    upgrade_log_display_entry('choice', 'update', 'choice', 'name');
    upgrade_log_display_entry('choice', 'add', 'choice', 'name');
    upgrade_log_display_entry('choice', 'report', 'choice', 'name');
    upgrade_log_display_entry('choice', 'choose', 'choice', 'name');
    upgrade_log_display_entry('choice', 'choose again', 'choice', 'name');

}

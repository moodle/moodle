<?php  //$Id$

// This file replaces:
//   * STATEMENTS section in db/install.xml
//   * lib.php/modulename_install() post installation hook
//   * partially defaults.php

function xmldb_feedback_install() {
    global $DB;

/// Install logging support
    upgrade_log_display_entry('feedback', 'startcomplete', 'feedback', 'name');
    upgrade_log_display_entry('feedback', 'submit', 'feedback', 'name');
    upgrade_log_display_entry('feedback', 'delete', 'feedback', 'name');
    upgrade_log_display_entry('feedback', 'view', 'feedback', 'name');
    upgrade_log_display_entry('feedback', 'view all', 'course', 'shortname');

}

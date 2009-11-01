<?php

// This file replaces:
//   * STATEMENTS section in db/install.xml
//   * lib.php/modulename_install() post installation hook
//   * partially defaults.php

function xmldb_scorm_install() {
    global $DB;

/// Install logging support
    update_log_display_entry('scorm', 'view', 'scorm', 'name');
    update_log_display_entry('scorm', 'review', 'scorm', 'name');
    update_log_display_entry('scorm', 'update', 'scorm', 'name');
    update_log_display_entry('scorm', 'add', 'scorm', 'name');

}

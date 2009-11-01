<?php

// This file replaces:
//   * STATEMENTS section in db/install.xml
//   * lib.php/modulename_install() post installation hook
//   * partially defaults.php

function xmldb_chat_install() {
    global $DB;

/// Install logging support
    update_log_display_entry('chat', 'view', 'chat', 'name');
    update_log_display_entry('chat', 'add', 'chat', 'name');
    update_log_display_entry('chat', 'update', 'chat', 'name');
    update_log_display_entry('chat', 'report', 'chat', 'name');
    update_log_display_entry('chat', 'talk', 'chat', 'name');
}

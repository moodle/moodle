<?php

// This file replaces:
//   * STATEMENTS section in db/install.xml
//   * lib.php/modulename_install() post installation hook
//   * partially defaults.php

function xmldb_glossary_install() {
    global $DB;

/// Install logging support
    update_log_display_entry('glossary', 'add', 'glossary', 'name');
    update_log_display_entry('glossary', 'update', 'glossary', 'name');
    update_log_display_entry('glossary', 'view', 'glossary', 'name');
    update_log_display_entry('glossary', 'view all', 'glossary', 'name');
    update_log_display_entry('glossary', 'add entry', 'glossary', 'name');
    update_log_display_entry('glossary', 'update entry', 'glossary', 'name');
    update_log_display_entry('glossary', 'add category', 'glossary', 'name');
    update_log_display_entry('glossary', 'update category', 'glossary', 'name');
    update_log_display_entry('glossary', 'delete category', 'glossary', 'name');
    update_log_display_entry('glossary', 'add comment', 'glossary', 'name');
    update_log_display_entry('glossary', 'update comment', 'glossary', 'name');
    update_log_display_entry('glossary', 'delete comment', 'glossary', 'name');
    update_log_display_entry('glossary', 'approve entry', 'glossary', 'name');
    update_log_display_entry('glossary', 'view entry', 'glossary_entries', 'concept');

}

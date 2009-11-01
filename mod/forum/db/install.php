<?php

// This file replaces:
//   * STATEMENTS section in db/install.xml
//   * lib.php/modulename_install() post installation hook
//   * partially defaults.php

function xmldb_forum_install() {
    global $DB;

/// Install logging support
    update_log_display_entry('forum', 'add', 'forum', 'name');
    update_log_display_entry('forum', 'update', 'forum', 'name');
    update_log_display_entry('forum', 'add discussion', 'forum_discussions', 'name');
    update_log_display_entry('forum', 'add post', 'forum_posts', 'subject');
    update_log_display_entry('forum', 'update post', 'forum_posts', 'subject');
    update_log_display_entry('forum', 'user report', 'user', 'CONCAT(firstname,&quot; &quot;,lastname)');
    update_log_display_entry('forum', 'move discussion', 'forum_discussions', 'name');
    update_log_display_entry('forum', 'view subscribers', 'forum', 'name');
    update_log_display_entry('forum', 'view discussion', 'forum_discussions', 'name');
    update_log_display_entry('forum', 'view forum', 'forum', 'name');
    update_log_display_entry('forum', 'subscribe', 'forum', 'name');
    update_log_display_entry('forum', 'unsubscribe', 'forum', 'name');

}

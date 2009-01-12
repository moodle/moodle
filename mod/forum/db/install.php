<?php  //$Id$

// This file replaces:
//   * STATEMENTS section in db/install.xml
//   * lib.php/modulename_install() post installation hook
//   * partially defaults.php

function xmldb_forum_install() {
    global $DB;

/// Install logging support
    upgrade_log_display_entry('forum', 'add', 'forum', 'name');
    upgrade_log_display_entry('forum', 'update', 'forum', 'name');
    upgrade_log_display_entry('forum', 'add discussion', 'forum_discussions', 'name');
    upgrade_log_display_entry('forum', 'add post', 'forum_posts', 'subject');
    upgrade_log_display_entry('forum', 'update post', 'forum_posts', 'subject');
    upgrade_log_display_entry('forum', 'user report', 'user', 'CONCAT(firstname,&quot; &quot;,lastname)');
    upgrade_log_display_entry('forum', 'move discussion', 'forum_discussions', 'name');
    upgrade_log_display_entry('forum', 'view subscribers', 'forum', 'name');
    upgrade_log_display_entry('forum', 'view discussion', 'forum_discussions', 'name');
    upgrade_log_display_entry('forum', 'view forum', 'forum', 'name');
    upgrade_log_display_entry('forum', 'subscribe', 'forum', 'name');
    upgrade_log_display_entry('forum', 'unsubscribe', 'forum', 'name');

}

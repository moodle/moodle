<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This file replaces:
 *  - STATEMENTS section in db/install.xml
 *  - lib.php/modulename_install() post installation hook
 *  - partially defaults.php
 *
 * @package mod-forum
 * @copyright  2009 Petr Skoda (http://skodak.org)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

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

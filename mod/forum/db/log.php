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
 * Definition of log events
 *
 * @package    mod_forum
 * @category   log
 * @copyright  2010 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $DB; // TODO: this is a hack, we should really do something with the SQL in SQL tables.

$logs = array(
    array('module' => 'forum', 'action' => 'add', 'mtable' => 'forum', 'field' => 'name'),
    array('module' => 'forum', 'action' => 'update', 'mtable' => 'forum', 'field' => 'name'),
    array('module' => 'forum', 'action' => 'add discussion', 'mtable' => 'forum_discussions', 'field' => 'name'),
    array('module' => 'forum', 'action' => 'add post', 'mtable' => 'forum_posts', 'field' => 'subject'),
    array('module' => 'forum', 'action' => 'update post', 'mtable' => 'forum_posts', 'field' => 'subject'),
    array('module' => 'forum', 'action' => 'user report', 'mtable' => 'user',
          'field'  => $DB->sql_concat('firstname', "' '", 'lastname')),
    array('module' => 'forum', 'action' => 'move discussion', 'mtable' => 'forum_discussions', 'field' => 'name'),
    array('module' => 'forum', 'action' => 'view subscribers', 'mtable' => 'forum', 'field' => 'name'),
    array('module' => 'forum', 'action' => 'view discussion', 'mtable' => 'forum_discussions', 'field' => 'name'),
    array('module' => 'forum', 'action' => 'view forum', 'mtable' => 'forum', 'field' => 'name'),
    array('module' => 'forum', 'action' => 'subscribe', 'mtable' => 'forum', 'field' => 'name'),
    array('module' => 'forum', 'action' => 'unsubscribe', 'mtable' => 'forum', 'field' => 'name'),
    array('module' => 'forum', 'action' => 'pin discussion', 'mtable' => 'forum_discussions', 'field' => 'name'),
    array('module' => 'forum', 'action' => 'unpin discussion', 'mtable' => 'forum_discussions', 'field' => 'name'),
);
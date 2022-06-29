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
 * Strings for component qbank_comment, language 'en'.
 *
 * @package    qbank_comment
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Question comments';
$string['privacy:metadata:core_comment'] = 'The Question comments question bank plugin enables users to comment on questions.';
// Column.
$string['comment'] = 'Comment';
$string['commentplural'] = 'Comments';
// Modal.
$string['addcomment'] = 'Add comment';
$string['close'] = 'Close';
$string['commentheader'] = 'Question comments';
$string['commentdisabled'] = 'Comments are currently disabled on this site. Please contact your site administrator.';
// Events.
$string['comment_added'] = 'The user with ID \'{$a->userid}\' added the comment with ID \'{$a->objectid}\'
 to the \'{$a->component}\' for the question with ID \'{$a->itemid}\'.';
$string['comment_removed'] = 'The user with ID \'{$a->userid}\' deleted the comment with ID \'{$a->objectid}\'
 from the \'{$a->component}\' for the question with ID \'{$a->itemid}\'.';

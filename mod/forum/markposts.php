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
 * Set tracking option for the forum.
 *
 * @package   mod_forum
 * @copyright 2005 mchurch
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../config.php");
require_once("lib.php");

$f          = required_param('f',PARAM_INT); // The forum to mark
$mark       = required_param('mark',PARAM_ALPHA); // Read or unread?
$d          = optional_param('d',0,PARAM_INT); // Discussion to mark.
$return     = optional_param('return', null, PARAM_LOCALURL);    // Page to return to.

$url = new moodle_url('/mod/forum/markposts.php', array('f'=>$f, 'mark'=>$mark));
if ($d !== 0) {
    $url->param('d', $d);
}
if (null !== $return) {
    $url->param('return', $return);
}
$PAGE->set_url($url);

if (! $forum = $DB->get_record("forum", array("id" => $f))) {
    throw new \moodle_exception('invalidforumid', 'forum');
}

if (! $course = $DB->get_record("course", array("id" => $forum->course))) {
    throw new \moodle_exception('invalidcourseid');
}

if (!$cm = get_coursemodule_from_instance("forum", $forum->id, $course->id)) {
    throw new \moodle_exception('invalidcoursemodule');
}

$user = $USER;

require_login($course, false, $cm);
require_sesskey();

if (null === $return) {
    $returnto = new moodle_url("/mod/forum/index.php", ['id' => $course->id]);
} else {
    $returnto = new moodle_url($return);
}

if (isguestuser()) {   // Guests can't change forum
    $PAGE->set_title($course->shortname);
    $PAGE->set_heading($course->fullname);
    echo $OUTPUT->header();
    echo $OUTPUT->confirm(get_string('noguesttracking', 'forum').'<br /><br />'.get_string('liketologin'), get_login_url(), $returnto);
    echo $OUTPUT->footer();
    exit;
}

$info = new stdClass();
$info->name  = fullname($user);
$info->forum = format_string($forum->name);

if ($mark == 'read') {
    if (!empty($d)) {
        if (! $discussion = $DB->get_record('forum_discussions', array('id'=> $d, 'forum'=> $forum->id))) {
            throw new \moodle_exception('invaliddiscussionid', 'forum');
        }

        forum_tp_mark_discussion_read($user, $d);
    } else {
        // Mark all messages read in current group
        $currentgroup = groups_get_activity_group($cm);
        if(!$currentgroup) {
            // mark_forum_read requires ===false, while get_activity_group
            // may return 0
            $currentgroup=false;
        }
        forum_tp_mark_forum_read($user, $forum->id, $currentgroup);
    }
}

redirect($returnto);


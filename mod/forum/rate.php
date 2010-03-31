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
 * Collect ratings, store them, then return to where we came from
 *
 * TODO: Centralise duplicate code in rate.php and rate_ajax.php
 *
 * @package mod-forum
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once('lib.php');

$forumid = required_param('forumid', PARAM_INT); // The forum the rated posts are from

$PAGE->set_url('/mod/forum/rate.php', array('forumid'=>$forumid));

if (!$forum = $DB->get_record('forum', array('id' => $forumid))) {
    print_error('invalidforumid', 'forum');
}

if (!$course = $DB->get_record('course', array('id' => $forum->course))) {
    print_error('invalidcourseid');
}

if (!$cm = get_coursemodule_from_instance('forum', $forum->id)) {
    print_error('invalidcoursemodule');
} else {
    $forum->cmidnumber = $cm->id; //MDL-12961
}

require_login($course, false, $cm);

$context = get_context_instance(CONTEXT_MODULE, $cm->id);
require_capability('mod/forum:rate', $context);


if (!$forum->assessed) {
    print_error('norate', 'forum');
}

if ($data = data_submitted() and confirm_sesskey()) {

    $discussionid = false;

/// Calculate scale values
    $scale_values = make_grades_menu($forum->scale);

    foreach ((array)$data as $postid => $rating) {
        if (!is_numeric($postid)) {
            continue;
        }

        // following query validates the submitted postid too
        $sql = "SELECT fp.*
                  FROM {forum_posts} fp, {forum_discussions} fd
                 WHERE fp.id = ? AND fp.discussion = fd.id AND fd.forum = ?";

        if (!$post = $DB->get_record_sql($sql, array($postid, $forum->id))) {
            print_error('invalidpostid', 'forum', '', $postid);
        }

        $discussionid = $post->discussion;

        if ($forum->assesstimestart and $forum->assesstimefinish) {
            if ($post->created < $forum->assesstimestart or $post->created > $forum->assesstimefinish) {
                // we can not rate this, ignore it - this should not happen anyway unless teacher changes setting
                continue;
            }
        }

    /// Check rate is valid for for that forum scale values
        if (!array_key_exists($rating, $scale_values) && $rating != FORUM_UNSET_POST_RATING) {
            print_error('invalidrate', 'forum', '', $rating);
        }

        if ($rating == FORUM_UNSET_POST_RATING) {
            $DB->delete_records('forum_ratings', array('post' => $postid, 'userid' => $USER->id));
            forum_update_grades($forum, $post->userid);

        } else if ($oldrating = $DB->get_record('forum_ratings', array('userid' => $USER->id, 'post' => $post->id))) {
            if ($rating != $oldrating->rating) {
                $oldrating->rating = $rating;
                $oldrating->time   = time();
                $DB->update_record('forum_ratings', $oldrating);
                forum_update_grades($forum, $post->userid);
            }

        } else {
            $newrating = new object();
            $newrating->userid = $USER->id;
            $newrating->time   = time();
            $newrating->post   = $post->id;
            $newrating->rating = $rating;

            $DB->insert_record('forum_ratings', $newrating);
            forum_update_grades($forum, $post->userid);
        }
    }

    if ($forum->type == 'single' or !$discussionid) {
        redirect("$CFG->wwwroot/mod/forum/view.php?id=$cm->id", get_string('ratingssaved', 'forum'));
    } else {
        redirect("$CFG->wwwroot/mod/forum/discuss.php?d=$discussionid", get_string('ratingssaved', 'forum'));
    }

} else {
    print_error('invalidaccess', 'forum');
}


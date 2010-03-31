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
 * Accept, process and reply to ajax calls to rate forums
 *
 * TODO: Centralise duplicate code in rate.php and rate_ajax.php
 *
 * @package mod-forum
 * @copyright 2001 Eloy Lafuente (stronk7) http://contiento.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->dirroot . '/mod/forum/lib.php');

/// In developer debug mode, when there is a debug=1 in the URL send as plain text
/// for easier debugging.
if (debugging('', DEBUG_DEVELOPER) && optional_param('debug', false, PARAM_BOOL)) {
    header('Content-type: text/plain; charset=UTF-8');
    $debugmode = true;
} else {
    header('Content-type: application/json');
    $debugmode = false;
}

/// Here we maintain response contents
$response = array('status'=> 'Error', 'message'=>'kk');

if (!confirm_sesskey()) {
    print_error('invalidsesskey');
}


/// Check required params
$postid = required_param('postid', PARAM_INT); // The postid to rate
$rate   = required_param('rate', PARAM_INT); // The rate to apply

$PAGE->set_url('/mod/forum/rate_ajax.php', array('postid'=>$postid,'rate'=>$rate));

/// Check postid is valid
if (!$post = $DB->get_record_sql('SELECT p.*,
                                         d.forum AS forumid
                                    FROM {forum_posts} p
                                    JOIN {forum_discussions} d ON p.discussion = d.id
                                   WHERE p.id = ?', array($postid))) {
    print_error('invalidpostid', 'forum', '', $postid);;
}

/// Check forum
if (!$forum = $DB->get_record('forum', array('id' => $post->forumid))) {
    print_error('invalidforumid', 'forum');
}

/// Check course
if (!$course = $DB->get_record('course', array('id' => $forum->course))) {
    print_error('invalidcourseid');
}

/// Check coursemodule
if (!$cm = get_coursemodule_from_instance('forum', $forum->id)) {
    print_error('invalidcoursemodule');
} else {
    $forum->cmidnumber = $cm->id; //MDL-12961
}

/// Check forum can be rated
if (!$forum->assessed) {
    print_error('norate', 'forum');
}

/// Check user can rate
$context = get_context_instance(CONTEXT_MODULE, $cm->id);
require_login($course, false, $cm);
require_capability('mod/forum:rate', $context);

/// Check timed ratings
if ($forum->assesstimestart and $forum->assesstimefinish) {
    if ($post->created < $forum->assesstimestart or $post->created > $forum->assesstimefinish) {
        // we can not rate this, ignore it - this should not happen anyway unless teacher changes setting
        print_error('norate', 'forum');
    }
}

/// Calculate scale values
$scale_values = make_grades_menu($forum->scale);

/// Check rate is valid for for that forum scale values
if (!array_key_exists($rate, $scale_values) && $rate != FORUM_UNSET_POST_RATING) {
    print_error('invalidrate', 'forum');
}

/// Everything ready, process rate

/// Deleting rate
if ($rate == FORUM_UNSET_POST_RATING) {
    $DB->delete_records('forum_ratings', array('post' => $postid, 'userid' => $USER->id));

/// Updating rate
} else if ($oldrating = $DB->get_record('forum_ratings', array('userid' => $USER->id, 'post' => $post->id))) {
    if ($rate != $oldrating->rating) {
        $oldrating->rating = $rate;
        $oldrating->time   = time();
        $DB->update_record('forum_ratings', $oldrating);
    }

/// Inserting rate
} else {
    $newrating = new object();
    $newrating->userid = $USER->id;
    $newrating->time   = time();
    $newrating->post   = $post->id;
    $newrating->rating = $rate;
    $DB->insert_record('forum_ratings', $newrating);
}

/// Update grades
forum_update_grades($forum, $post->userid);

/// Check user can see any rate
$canviewanyrating = has_capability('mod/forum:viewanyrating', $context);

/// Decide if rates info is displayed
$rateinfo = '';
if ($canviewanyrating) {
    $rateinfo = forum_print_ratings($postid, $scale_values, $forum->assessed, true, NULL, true);
}

/// Calculate response
$response['status']  = 'Ok';
$response['message'] = $rateinfo;
echo json_encode($response);


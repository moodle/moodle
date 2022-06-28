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
 * File to mark posts as read.
 *
 * @package   mod_moodleoverflow
 * @copyright 2017 Kennet Winter <k_wint10@uni-muenster.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// We do not need the locallib here.
require_once('../../config.php');
require_once($CFG->dirroot . '/mod/moodleoverflow/locallib.php');

// Define the parameters.
$moodleoverflowid = required_param('m', PARAM_INT);         // The moodleoverflowinstance to mark.
$discussionid     = optional_param('d', 0, PARAM_INT);      // The discussion to mark.
$returndiscussion = optional_param('return', 0, PARAM_INT); // The page to return to.

// Prepare the array that should be used to return to this page.
$url = new moodle_url('/mod/moodleoverflow/markposts.php', array('m' => $moodleoverflowid));

// Check the optional params.
if ($discussionid !== 0) {
    $url->param('d', $discussionid);
}
if ($returndiscussion !== 0) {
    $url->param('returndiscussion', $returndiscussion);
}

// Set the url that should be used to return to this page.
$PAGE->set_url($url);

// Retrieve the connected moodleoverflow instance.
if (!$moodleoverflow = $DB->get_record('moodleoverflow', array('id' => $moodleoverflowid))) {
    throw new moodle_exception('invalidmoodleoverflowid', 'moodleoverflow');
}

// Retrieve the connected course.
if (!$course = $DB->get_record('course', array('id' => $moodleoverflow->course))) {
    throw new moodle_exception('invalidcourseid');
}

// Get the coursemodule.
if (!$cm = get_coursemodule_from_instance('moodleoverflow', $moodleoverflow->id, $course->id)) {
    throw new moodle_exception('invalidcoursemodule');
}

// Get the current user.
$user = $USER;

// From now on, the user must be logged in and enrolled.
require_login($course, false, $cm);

// Default relink address.
if ($returndiscussion === 0) {

    // If no parameter is set, relink to the view.
    $returnto = new moodle_url("/mod/moodleoverflow/view.php", array('m' => $moodleoverflow->id));

} else {

    // Else relink back to the discussion we are coming from.
    $returnto = new moodle_url("/mod/moodleoverflow/discussion.php", array('d' => $returndiscussion));
}

// Guests can't mark posts as read.
if (isguestuser()) {

    // Set Page-Parameter.
    $PAGE->set_title($course->shortname);
    $PAGE->set_heading($course->fullname);

    // Create the message.
    $message = get_string('noguesttracking', 'moodleoverflow') . '<br /><br />' . get_string('liketologin');

    // Display the page with a confirm-element.
    echo $OUTPUT->header();
    echo $OUTPUT->confirm($message, get_login_url(), $returnto);
    echo $OUTPUT->footer();
    exit;
}

// Delete a single discussion.
if (!empty($discussionid)) {

    // Check if the discussion exists.
    $options    = array('id' => $discussionid, 'moodleoverflow' => $moodleoverflow->id);
    $discussion = $DB->get_record('moodleoverflow_discussions', $options);
    if (!$discussion) {
        throw new moodle_exception('invaliddiscussionid', 'moodleoverflow');
    }

    // Mark all the discussions read.
    if (!\mod_moodleoverflow\readtracking::moodleoverflow_mark_discussion_read($discussionid, $user->id)) {

        // Display an error, if something failes.
        $message = get_string('markreadfailed', 'moodleoverflow');
        $status  = \core\output\notification::NOTIFY_ERROR;

    } else {

        // The discussion is successfully marked as read.
        $message = get_string('markmoodleoverflowreadsuccessful', 'moodleoverflow');
        $status  = \core\output\notification::NOTIFY_SUCCESS;
    }

    // Redirect the user.
    redirect(moodleoverflow_go_back_to($returnto), $message, null, $status);
    exit;

} else {

    // Mark all message read in the current instance.
    if (!\mod_moodleoverflow\readtracking::moodleoverflow_mark_moodleoverflow_read($cm, $user->id)) {

        // Display an error, if something fails.
        $message = get_string('markreadfailed', 'moodleoverflow');
        $status  = \core\output\notification::NOTIFY_ERROR;

    } else {

        // All posts of the instance have been marked as read.
        $message = get_string('markdiscussionreadsuccessful', 'moodleoverflow');
        $status  = \core\output\notification::NOTIFY_SUCCESS;
    }

    // Redirect the user back to the view.php.
    redirect(moodleoverflow_go_back_to($returnto), $message, null, $status);
    exit;
}

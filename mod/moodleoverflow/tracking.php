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
 * Set tracking option for the moodleoverflow.
 *
 * @package   mod_moodleoverflow
 * @copyright 2017 Kennet Winter <k_wint10@uni-muenster.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Require needed files.
require_once("../../config.php");
require_once("locallib.php");

// Get submitted parameters.
$id         = required_param('id', PARAM_INT);                       // The moodleoverflow to track or untrack.
$returnpage = optional_param('returnpage', 'index.php', PARAM_FILE); // The page to return to.

// A session key is needed to change the tracking options.
require_sesskey();

// Retrieve the moodleoverflow instance to track or untrack.
if (!$moodleoverflow = $DB->get_record("moodleoverflow", array("id" => $id))) {
    throw new moodle_exception('invalidmoodleoverflowid', 'moodleoverflow');
}

// Retrieve the course of the instance.
if (!$course = $DB->get_record("course", array("id" => $moodleoverflow->course))) {
    throw new moodle_exception('invalidcoursemodule');
}

// Retrieve the course module of that course.
if (!$cm = get_coursemodule_from_instance("moodleoverflow", $moodleoverflow->id, $course->id)) {
    throw new moodle_exception('invalidcoursemodule');
}

// From now on the user needs to be logged in and enrolled.
require_login($course, false, $cm);

// Set the page to return to.
$url           = '/mod/moodleoverflow/' . $returnpage;
$params        = array('id' => $course->id, 'm' => $moodleoverflow->id);
$returnpageurl = new moodle_url($url, $params);
$returnto      = moodleoverflow_go_back_to($returnpageurl);

// Check whether the user can track the moodleoverflow instance.
$cantrack = \mod_moodleoverflow\readtracking::moodleoverflow_can_track_moodleoverflows($moodleoverflow);

// Do not continue if the user is not allowed to track the moodleoverflow. Redirect the user back.
if (!$cantrack) {
    redirect($returnto);
    exit;
}

// Create an info object.
$info                 = new stdClass();
$info->name           = fullname($USER);
$info->moodleoverflow = format_string($moodleoverflow->name);

// Set parameters for an event.
$eventparams = array(
    'context'       => context_module::instance($cm->id),
    'relateduserid' => $USER->id,
    'other'         => array('moodleoverflowid' => $moodleoverflow->id),
);

// Check whether the moodleoverflow is tracked.
$istracked = \mod_moodleoverflow\readtracking::moodleoverflow_is_tracked($moodleoverflow);
if ($istracked) {
    // The moodleoverflow instance is tracked. The next step is to untrack.

    // Untrack the moodleoverflow instance.
    if (\mod_moodleoverflow\readtracking::moodleoverflow_stop_tracking($moodleoverflow->id)) {
        // Successful stopped to track.

        // Trigger the readtracking disabled event.
        $event = \mod_moodleoverflow\event\readtracking_disabled::create($eventparams);
        $event->trigger();

        // Redirect the user back to where he is coming from.
        redirect($returnpageurl, get_string('nownottracking', 'moodleoverflow', $info), 1);

    } else {
        // The insertion failed.

        // Print an error message.
        throw new moodle_exception('cannottrack', 'moodleoverflow', get_local_referer(false));
    }

} else {
    // The moodleoverflow instance is not tracked. The next step is to track.

    // Track the moodleoverflow instance.
    if (\mod_moodleoverflow\readtracking::moodleoverflow_start_tracking($moodleoverflow->id)) {
        // Successfully started to track.

        // Trigger the readtracking event.
        $event = \mod_moodleoverflow\event\readtracking_enabled::create($eventparams);
        $event->trigger();

        // Redirect the user back to where he is coming from.
        redirect($returnto, get_string('nowtracking', 'moodleoverflow', $info), 1);

    } else {
        // The deletion failed.

        // Print an error message.
        throw new moodle_exception('cannottrack', 'moodleoverflow', get_local_referer(false));
    }
}

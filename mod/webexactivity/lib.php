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
 * An activity to interface with WebEx.
 *
 * @package    mod_webexactvity
 * @author     Eric Merrill <merrill@oakland.edu>
 * @copyright  2014 Oakland University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/calendar/lib.php');

/**
 * Return the list if Moodle features this module supports
 *
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, null if doesn't know
 */
function webexactivity_supports($feature) {
    switch($feature) {
        case FEATURE_MOD_ARCHETYPE:
            return MOD_ARCHETYPE_ASSIGNMENT;
        case FEATURE_GROUPS:
            return false;
        case FEATURE_GROUPINGS:
            return false;
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS:
            return true;
        case FEATURE_GRADE_HAS_GRADE:
            return false;
        case FEATURE_GRADE_OUTCOMES:
            return false;
        case FEATURE_BACKUP_MOODLE2:
            return false;
        case FEATURE_SHOW_DESCRIPTION:
            return true;

        default:
            return null;
    }
}

/**
 * Adds an WebEx Meeting instance.
 *
 * @param stdClass              $data Form data
 * @param mod_assign_mod_form   $form The form
 * @return int The instance id of the new assignment
 */
function webexactivity_add_instance($data, $mform) {
    global $PAGE;

    $meeting = \mod_webexactivity\meeting::create_new($data->type);
    $meeting->starttime = $data->starttime;
    $meeting->duration = $data->duration;
    $meeting->calpublish = !empty($data->calpublish) ? 1 : 0;
    if (isset($data->longavailability)) {
        $meeting->endtime = $data->endtime;
        $meeting->calpublish = 0;
    } else {
        $meeting->endtime = null;
    }
    $meeting->intro = $data->intro;
    $meeting->introformat = $data->introformat;
    $meeting->name = $data->name;
    $meeting->course = $data->course;

    if (isset($data->password) && !empty($data->password)) {
        $meeting->password = $data->password;
    } else {
        $meeting->password = null;
    }

    $meeting->status = \mod_webexactivity\webex::WEBEXACTIVITY_STATUS_NEVER_STARTED;
    if (isset($data->studentdownload) && $data->studentdownload) {
        $meeting->studentdownload = 1;
    } else {
        $meeting->studentdownload = 0;
    }

    $meeting->cmid = $data->coursemodule;

    if (!$meeting->save()) {
        return false;
    }

    return $meeting->id;

}

/**
 * Update an WebEx Meeting instance.
 *
 * @param stdClass              $data Form data
 * @param mod_assign_mod_form   $form The form
 * @return bool                 If the update passed (true) or failed
 */
function webexactivity_update_instance($data, $mform) {
    global $PAGE;

    $cmid = $data->coursemodule;
    $cm = get_coursemodule_from_id('webexactivity', $cmid, 0, false, MUST_EXIST);
    $meeting = \mod_webexactivity\meeting::load($cm->instance);

    $meeting->starttime = $data->starttime;
    $meeting->duration = $data->duration;
    $meeting->calpublish = !empty($data->calpublish) ? 1 : 0;
    if (isset($data->longavailability)) {
        $meeting->endtime = $data->endtime;
        $meeting->calpublish = 0;
    } else {
        $meeting->endtime = null;
    }
    $meeting->intro = $data->intro;
    $meeting->introformat = $data->introformat;
    $meeting->name = $data->name;
    $meeting->course = $data->course;

    if (isset($data->password) && !empty($data->password)) {
        $meeting->password = $data->password;
    } else {
        $meeting->password = null;
    }

    if (isset($data->studentdownload) && $data->studentdownload) {
        $meeting->studentdownload = 1;
    } else {
        $meeting->studentdownload = 0;
    }

    $meeting->cmid = $data->coursemodule;

    try {
        return $meeting->save();
    } catch (Exception $e) {
        $collision = ($e instanceof \mod_webexactivity\exception\webex_user_collision);
        $password = ($e instanceof \mod_webexactivity\exception\bad_password);
        if ($collision || $password) {
            \mod_webexactivity\webex::password_redirect($PAGE->url);
        } else {
            throw $e;
        }
        throw $e;
    }

    // TODO - update cal event
}

/**
 * Print an overview of all WebEx Meetings for the courses.
 *
 * @param mixed   $courses The list of courses to print the overview for
 * @param array   $htmlarray The array of html to return
 */
function webexactivity_print_overview($courses, &$htmlarray) {
    global $USER, $CFG, $DB;

    if (empty($courses) || !is_array($courses) || count($courses) == 0) {
        return;
    }

    if (!$meetings = get_all_instances_in_courses('webexactivity', $courses)) {
        return;
    }

    $displaymeetings = array();

    foreach ($meetings as $rec) {
        $meeting = \mod_webexactivity\meeting::load($rec);
        if ($meeting->is_available()) {
            $displaymeetings[] = $meeting;
        }
    }

    if (count($displaymeetings) == 0) {
        return;
    }

    $strmodname = get_string('modulename', 'webexactivity');
    $strinprogress = get_string('inprogress', 'webexactivity');
    $strstartsoon = get_string('startssoon', 'webexactivity');
    $strstarttime = get_string('starttime', 'webexactivity');
    $strstatus = get_string('status');

    foreach ($displaymeetings as $meeting) {
        $href = $CFG->wwwroot . '/mod/webexactivity/view.php?id=' . $meeting->coursemodule;
        $str = '<div class="webexactivity overview"><div class="name">';
        $str .= $strmodname.': <a title="'.$strmodname.'" href="'.$href.'">';
        $str .= format_string($meeting->name).'</a></div>';

        $status = $meeting->get_time_status();
        if (!isset($meeting->endtime)) {
            $str .= '<div class="start">'.$strstarttime.': '.userdate($meeting->starttime).'</div>';
        }
        if ($status == \mod_webexactivity\webex::WEBEXACTIVITY_TIME_IN_PROGRESS) {
            $str .= '<div class="status">'.$strstatus.': '.$strinprogress.'</div>';
        } else if ($status == \mod_webexactivity\webex::WEBEXACTIVITY_TIME_AVAILABLE) {
            $str .= '<div class="status">'.$strstatus.': '.$strstartsoon.'</div>';
        }
        $str .= '</div>';

        if (isset($htmlarray[$meeting->course]['webexactivity'])) {
            $htmlarray[$meeting->course]['webexactivity'] .= $str;
        } else {
            $htmlarray[$meeting->course]['webexactivity'] = $str;
        }
    }

}

/**
 * Delete a WebEx instance.
 *
 * @param int   $id     Record id to delete.
 * @return bool
 */
function webexactivity_delete_instance($id) {
    $meeting = \mod_webexactivity\meeting::load($id);
    return $meeting->delete();
}


/**
 * This function receives a calendar event and returns the action associated with it, or null if there is none.
 *
 * This is used by block_myoverview in order to display the event appropriately. If null is returned then the event
 * is not displayed on the block.
 *
 * @param calendar_event $event
 * @param \core_calendar\action_factory $factory
 * @param int $userid User id to use for all capability checks, etc. Set to 0 for current user (default).
 * @return \core_calendar\local\event\entities\action_interface|null
 */
function mod_webexactivity_core_calendar_provide_event_action(calendar_event $event,
                                                     \core_calendar\action_factory $factory,
                                                     int $userid = 0) {
    global $USER, $DB;

    if ($userid) {
        $user = core_user::get_user($userid, 'id, timezone');
    } else {
        $user = $USER;
    }

    $cm = get_fast_modinfo($event->courseid, $user->id)->instances['webexactivity'][$event->instance];

    return $factory->create_instance(
        get_string('entermeeting', 'webexactivity'),
        new \moodle_url('/mod/webexactivity/view.php', ['id' => $cm->id]),
        1,
        false
    );

}

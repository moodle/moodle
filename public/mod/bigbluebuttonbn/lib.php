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
 * Library calls for Moodle and BigBlueButton.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2010 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Jesus Federico  (jesus [at] blindsidenetworks [dt] com)
 * @author    Fred Dixon  (ffdixon [at] blindsidenetworks [dt] com)
 */
defined('MOODLE_INTERNAL') || die;

use core_calendar\action_factory;
use core_calendar\local\event\entities\action_interface;
use mod_bigbluebuttonbn\completion\custom_completion;
use mod_bigbluebuttonbn\extension;
use mod_bigbluebuttonbn\instance;
use mod_bigbluebuttonbn\local\bigbluebutton;
use mod_bigbluebuttonbn\local\exceptions\server_not_available_exception;
use mod_bigbluebuttonbn\local\helpers\files;
use mod_bigbluebuttonbn\local\helpers\mod_helper;
use mod_bigbluebuttonbn\local\helpers\reset;
use mod_bigbluebuttonbn\local\proxy\bigbluebutton_proxy;
use mod_bigbluebuttonbn\logger;
use mod_bigbluebuttonbn\meeting;
use mod_bigbluebuttonbn\recording;
use mod_bigbluebuttonbn\local\config;

global $CFG;

/**
 * Indicates API features that the bigbluebuttonbn supports.
 *
 * @param string $feature
 * @return mixed True if yes (some features may use other values)
 * @uses FEATURE_IDNUMBER
 * @uses FEATURE_GROUPS
 * @uses FEATURE_GROUPINGS
 * @uses FEATURE_GROUPMEMBERSONLY
 * @uses FEATURE_MOD_INTRO
 * @uses FEATURE_BACKUP_MOODLE2
 * @uses FEATURE_COMPLETION_TRACKS_VIEWS
 * @uses FEATURE_COMPLETION_HAS_RULES
 * @uses FEATURE_GRADE_HAS_GRADE
 * @uses FEATURE_GRADE_OUTCOMES
 * @uses FEATURE_SHOW_DESCRIPTION
 */
function bigbluebuttonbn_supports($feature) {
    return match ($feature) {
        FEATURE_IDNUMBER => true,
        FEATURE_GROUPS => true,
        FEATURE_GROUPINGS => true,
        FEATURE_MOD_INTRO => true,
        FEATURE_BACKUP_MOODLE2 => true,
        FEATURE_COMPLETION_TRACKS_VIEWS => true,
        FEATURE_COMPLETION_HAS_RULES => true,
        FEATURE_GRADE_HAS_GRADE => true,
        FEATURE_GRADE_OUTCOMES => false,
        FEATURE_SHOW_DESCRIPTION => true,
        FEATURE_MOD_PURPOSE => MOD_PURPOSE_COMMUNICATION,
        default => null,
    };
}

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param stdClass $bigbluebuttonbn An object from the form in mod_form.php
 * @return int The id of the newly inserted bigbluebuttonbn record
 */
function bigbluebuttonbn_add_instance($bigbluebuttonbn) {
    global $DB;
    // Excecute preprocess.
    mod_helper::process_pre_save($bigbluebuttonbn);
    // Pre-set initial values.
    $bigbluebuttonbn->presentation = files::save_media_file($bigbluebuttonbn);
    // Encode meetingid.
    $bigbluebuttonbn->meetingid = meeting::get_unique_meetingid_seed();
    [$bigbluebuttonbn->guestlinkuid, $bigbluebuttonbn->guestpassword] =
        \mod_bigbluebuttonbn\plugin::generate_guest_meeting_credentials();
    // Insert a record.
    $bigbluebuttonbn->id = $DB->insert_record('bigbluebuttonbn', $bigbluebuttonbn);
    // Log insert action.
    logger::log_instance_created($bigbluebuttonbn);
    // Complete the process.
    mod_helper::process_post_save($bigbluebuttonbn);

    // Call any active subplugin so to signal a new creation.
    extension::add_instance($bigbluebuttonbn);

    // Create new grade item.
    bigbluebuttonbn_grade_item_update($bigbluebuttonbn);

    return $bigbluebuttonbn->id;
}

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param stdClass $bigbluebuttonbn An object from the form in mod_form.php
 * @return bool Success/Fail
 */
function bigbluebuttonbn_update_instance($bigbluebuttonbn) {
    global $DB;
    // Excecute preprocess.
    mod_helper::process_pre_save($bigbluebuttonbn);

    // Pre-set initial values.
    $bigbluebuttonbn->id = $bigbluebuttonbn->instance;
    $bigbluebuttonbn->presentation = files::save_media_file($bigbluebuttonbn);

    if (empty($bigbluebuttonbn->guestjoinurl) || empty($bigbluebuttonbn->guestpassword)) {
        [$bigbluebuttonbn->guestlinkuid, $bigbluebuttonbn->guestpassword] =
            \mod_bigbluebuttonbn\plugin::generate_guest_meeting_credentials();
    }
    // Update a record.
    $DB->update_record('bigbluebuttonbn', $bigbluebuttonbn);

    bigbluebuttonbn_grade_item_update($bigbluebuttonbn);

    // Get the meetingid column in the bigbluebuttonbn table.
    $bigbluebuttonbn->meetingid = (string) $DB->get_field('bigbluebuttonbn', 'meetingid', ['id' => $bigbluebuttonbn->id]);

    // Log update action.
    logger::log_instance_updated(instance::get_from_instanceid($bigbluebuttonbn->id));

    // Complete the process.
    mod_helper::process_post_save($bigbluebuttonbn);
    // Call any active subplugin so to signal update.
    extension::update_instance($bigbluebuttonbn);
    return true;
}

/**
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 *
 * @return bool Success/Failure
 */
function bigbluebuttonbn_delete_instance($id) {
    global $DB;

    $instance = instance::get_from_instanceid($id);
    if (empty($instance)) {
        return false;
    }
    // End all meeting if any still running.
    try {
        $meeting = new meeting($instance);
        $meeting->end_meeting();
    } catch (moodle_exception $e) {
        // Do not log any issue when testing.
        if (!(defined('PHPUNIT_TEST') && PHPUNIT_TEST) && !defined('BEHAT_SITE_RUNNING')) {
            debugging($e->getMessage(), DEBUG_DEVELOPER, $e->getTrace());
        }
    }
    // Get all possible groups (course and course module).
    $groupids = [];
    if (groups_get_activity_groupmode($instance->get_cm())) {
        $coursegroups = groups_get_activity_allowed_groups($instance->get_cm());
        $groupids = array_map(
            function($gp) {
                return $gp->id;
            },
            $coursegroups);
    }
    // End all meetings for all groups.
    foreach ($groupids as $groupid) {
        try {
            $instance->set_group_id($groupid);
            $meeting = new meeting($instance);
            $meeting->end_meeting();
        } catch (moodle_exception $e) {
            debugging($e->getMessage() . ' for group ' . $groupid, DEBUG_NORMAL, $e->getTrace());
        }
    }

    $result = true;

    // Delete grades.
    $bigbluebuttonbn = $DB->get_record('bigbluebuttonbn', ['id' => $id]);
    bigbluebuttonbn_grade_item_delete($bigbluebuttonbn);

    // Call any active subplugin so to signal deletion.
    extension::delete_instance($id);

    // Delete the instance.
    if (!$DB->delete_records('bigbluebuttonbn', ['id' => $id])) {
        $result = false;
    }

    // Delete dependant events.
    if (!$DB->delete_records('event', ['modulename' => 'bigbluebuttonbn', 'instance' => $id])) {
        $result = false;
    }

    // Log action performed.
    logger::log_instance_deleted($instance);

    // Mark dependent recordings as headless.
    foreach (recording::get_records(['bigbluebuttonbnid' => $id]) as $recording) {
        $recording->set('headless', recording::RECORDING_HEADLESS);
        $recording->update();
    }

    return $result;
}

/**
 * Return a small object with summary information about what a
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 *
 * @param stdClass $course
 * @param stdClass $user
 * @param cm_info $mod
 * @param stdClass $bigbluebuttonbn
 *
 * @return stdClass with info and time (timestamp of the last log)
 */
function bigbluebuttonbn_user_outline(stdClass $course, stdClass $user, cm_info $mod, stdClass $bigbluebuttonbn): stdClass {
    [$infos, $logtimestamps] = \mod_bigbluebuttonbn\local\helpers\user_info::get_user_info_outline($course, $user, $mod);
    return (object) [
        'info' => join(',', $infos),
        'time' => !empty($logtimestamps) ? max($logtimestamps) : 0
    ];
}

/**
 * Print a detailed representation of what a user has done with
 * a given particular instance of this module, for user activity reports.
 *
 * @param stdClass $course
 * @param stdClass $user
 * @param cm_info $mod
 * @param stdClass $bigbluebuttonbn
 *
 */
function bigbluebuttonbn_user_complete(stdClass $course, stdClass $user, cm_info $mod, stdClass $bigbluebuttonbn) {
    [$infos] = \mod_bigbluebuttonbn\local\helpers\user_info::get_user_info_outline($course, $user, $mod);
    echo join(', ', $infos);
}

/**
 * This flags this module with the capability to override the completion status with the custom completion rules.
 *
 *
 * @return int
 */
function bigbluebuttonbn_get_completion_aggregation_state() {
    return COMPLETION_CUSTOM_MODULE_FLOW;
}

/**
 * Returns all other caps used in module.
 *
 * @return string[]
 */
function bigbluebuttonbn_get_extra_capabilities() {
    return ['moodle/site:accessallgroups'];
}

/**
 * Called by course/reset.php
 *
 * @param MoodleQuickForm $mform
 */
function bigbluebuttonbn_reset_course_form_definition(&$mform) {
    $items = reset::reset_course_items();
    $mform->addElement('header', 'bigbluebuttonbnheader', get_string('modulenameplural', 'bigbluebuttonbn'));
    foreach ($items as $item => $default) {
        $mform->addElement(
            'advcheckbox',
            "reset_bigbluebuttonbn_{$item}",
            get_string("reset{$item}", 'bigbluebuttonbn')
        );
        if ($item == 'logs' || $item == 'recordings') {
            $mform->addHelpButton("reset_bigbluebuttonbn_{$item}", "reset{$item}", 'bigbluebuttonbn');
        }
    }
}

/**
 * Course reset form defaults.
 *
 * @param stdClass $course
 * @return array
 */
function bigbluebuttonbn_reset_course_form_defaults(stdClass $course) {
    $formdefaults = [];
    $items = reset::reset_course_items();
    // All unchecked by default.
    foreach ($items as $item => $default) {
        $formdefaults["reset_bigbluebuttonbn_{$item}"] = $default;
    }
    return $formdefaults;
}

/**
 * This function is used by the reset_course_userdata function in moodlelib.
 *
 * @param stdClass $data the data submitted from the reset course.
 * @return array status array
 */
function bigbluebuttonbn_reset_userdata(stdClass $data) {
    $items = reset::reset_course_items();
    $status = [];

    // Any changes to the list of dates that needs to be rolled should be same during course restore and course reset.
    // See MDL-9367.
    if (array_key_exists('recordings', $items) && !empty($data->reset_bigbluebuttonbn_recordings)) {
        // Remove all the recordings from a BBB server that are linked to the room/activities in this course.
        reset::reset_recordings($data->courseid);
        unset($items['recordings']);
        $status[] = reset::reset_getstatus('recordings');
    }

    if (!empty($data->reset_bigbluebuttonbn_tags)) {
        // Remove all the tags linked to the room/activities in this course.
        reset::reset_tags($data->courseid);
        unset($items['tags']);
        $status[] = reset::reset_getstatus('tags');
    }

    if (!empty($data->reset_bigbluebuttonbn_logs)) {
        // Remove all the tags linked to the room/activities in this course.
        reset::reset_logs($data->courseid);
        unset($items['logs']);
        $status[] = reset::reset_getstatus('logs');
    }
    // Remove all grades from gradebook.
    if (!empty($data->reset_gradebook_grades)) {
        bigbluebuttonbn_reset_gradebook($data->courseid);
    }
    return $status;
}

/**
 * Given a course_module object, this function returns any
 * "extra" information that may be needed when printing
 * this activity in a course listing.
 * See get_array_of_activities() in course/lib.php.
 *
 * @param stdClass $coursemodule
 *
 * @return null|cached_cm_info
 */
function bigbluebuttonbn_get_coursemodule_info($coursemodule) {
    $instance = instance::get_from_instanceid($coursemodule->instance);
    if (empty($instance)) {
        return null;
    }
    $info = new cached_cm_info();
    // Warning here: if any of the instance method calls ::get_cm this will result is a recursive call.
    // So best is just to access instance variables not linked to the cm.
    $info->name = $instance->get_instance_var('name');
    if ($coursemodule->showdescription) {
        // Convert intro to html. Do not filter cached version, filters run at display time.
        $info->content = format_module_intro('bigbluebuttonbn', $instance->get_instance_data(), $coursemodule->id, false);
    }
    $customcompletionfields = custom_completion::get_defined_custom_rules();
    // Populate the custom completion rules as key => value pairs, but only if the completion mode is 'automatic'.
    if ($coursemodule->completion == COMPLETION_TRACKING_AUTOMATIC) {
        foreach ($customcompletionfields as $completiontype) {
            $info->customdata['customcompletionrules'][$completiontype] =
                $instance->get_instance_var($completiontype) ?? 0;
        }
    }

    return $info;
}

/**
 * Serves the bigbluebuttonbn attachments. Implements needed access control ;-).
 *
 * @param stdClass $course course object
 * @param stdClass $cm course module object
 * @param context $context context object
 * @param string $filearea file area
 * @param array $args extra arguments
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 *
 * @return false|null false if file not found, does not return if found - justsend the file
 * @category files
 *
 */
function bigbluebuttonbn_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = []) {
    if (!files::pluginfile_valid($context, $filearea)) {
        return false;
    }
    $file = files::pluginfile_file($course, $cm, $context, $filearea, $args);
    if (empty($file)) {
        return false;
    }
    // Finally send the file.
    return send_stored_file($file, 0, 0, $forcedownload, $options); // Download MUST be forced - security!
}

/**
 * Mark the activity completed (if required) and trigger the course_module_viewed event.
 *
 * @param stdClass $bigbluebuttonbn bigbluebuttonbn object
 * @param stdClass $course course object
 * @param cm_info $cm course module object
 * @param context $context context object
 * @since Moodle 3.0
 */
function bigbluebuttonbn_view($bigbluebuttonbn, $course, $cm, $context) {

    // Trigger course_module_viewed event.
    $params = [
        'context' => $context,
        'objectid' => $bigbluebuttonbn->id
    ];

    $event = \mod_bigbluebuttonbn\event\course_module_viewed::create($params); // Fix event name.
    $cmrecord = $cm->get_course_module_record();
    $event->add_record_snapshot('course_modules', $cmrecord);
    $event->add_record_snapshot('course', $course);
    $event->add_record_snapshot('bigbluebuttonbn', $bigbluebuttonbn);
    $event->trigger();

    // Completion.
    $completion = new completion_info($course);
    $completion->set_module_viewed($cm);
}

/**
 * Check if the module has any update that affects the current user since a given time.
 *
 * @param cm_info $cm course module data
 * @param int $from the time to check updates from
 * @param array $filter if we need to check only specific updates
 * @return stdClass an object with the different type of areas indicating if they were updated or not
 * @since Moodle 3.2
 */
function bigbluebuttonbn_check_updates_since(cm_info $cm, $from, $filter = []) {
    $updates = course_check_module_updates_since($cm, $from, ['content'], $filter);
    return $updates;
}

/**
 * This function receives a calendar event and returns the action associated with it, or null if there is none.
 *
 * This is used by block_myoverview in order to display the event appropriately. If null is returned then the event
 * is not displayed on the block.
 *
 * @param calendar_event $event
 * @param action_factory $factory
 * @return action_interface|null
 */
function mod_bigbluebuttonbn_core_calendar_provide_event_action(
    calendar_event $event,
    action_factory $factory
) {
    global $DB;

    $time = time();

    // Get mod info.
    $cm = get_fast_modinfo($event->courseid)->instances['bigbluebuttonbn'][$event->instance];

    // Get bigbluebuttonbn activity.
    $bigbluebuttonbn = $DB->get_record('bigbluebuttonbn', ['id' => $event->instance], '*', MUST_EXIST);

    // Set flag haspassed if closingtime has already passed only if it is defined.
    $haspassed = ($bigbluebuttonbn->closingtime) && $bigbluebuttonbn->closingtime < $time;

    // Set flag hasstarted if startingtime has already passed or not defined.
    $hasstarted = $bigbluebuttonbn->openingtime < $time;

    // Return null if it has passed or not started.
    if ($haspassed || !$hasstarted) {
        return null;
    }

    // Get if the user has joined in live session or viewed the recorded.
    $customcompletion = new custom_completion($cm, $event->userid);
    $usercomplete = $customcompletion->get_overall_completion_state();
    $instance = instance::get_from_instanceid($bigbluebuttonbn->id);
    // Get if the room is available.
    $roomavailable = $instance->is_currently_open();

    $meetinginfo = null;
    // Check first if the server can be contacted.
    try {
        if (empty(bigbluebutton_proxy::get_server_version())) {
            // In this case we should already have debugging message printed.
            return null;
        }
        // Get if the user can join.
        $meetinginfo = meeting::get_meeting_info_for_instance($instance);
    } catch (moodle_exception $e) {
        debugging('Error - Cannot retrieve info from meeting ('.$instance->get_meeting_id().') ' . $e->getMessage());
        return null;
    }
    $usercanjoin = $meetinginfo->canjoin;

    // Check if the room is closed and the user has already joined this session or played the record.
    if (!$roomavailable && $usercomplete) {
        return null;
    }

    // Check if the user can join this session.
    $actionable = ($roomavailable && $usercanjoin);

    // Action data.
    $string = get_string('view_room', 'bigbluebuttonbn');
    $url = new moodle_url('/mod/bigbluebuttonbn/view.php', ['id' => $cm->id]);
    if (groups_get_activity_groupmode($cm) == NOGROUPS) {
        // No groups mode.
        $string = get_string('view_conference_action_join', 'bigbluebuttonbn');
        $url = new moodle_url('/mod/bigbluebuttonbn/bbb_view.php', [
                'action' => 'join',
                'id' => $cm->id,
                'bn' => $bigbluebuttonbn->id,
                'timeline' => 1,
                ]
        );
    }

    return $factory->create_instance($string, $url, 1, $actionable);
}

/**
 * Is the event visible?
 *
 * @param calendar_event $event
 * @return bool Returns true if the event is visible to the current user, false otherwise.
 */
function mod_bigbluebuttonbn_core_calendar_is_event_visible(calendar_event $event) {
    $instance = instance::get_from_instanceid($event->instance);
    if (!$instance) {
        return false;
    }
    $activitystatus = mod_bigbluebuttonbn\local\proxy\bigbluebutton_proxy::view_get_activity_status($instance);
    return $activitystatus != 'ended';
}

/**
 * Adds module specific settings to the settings block
 *
 * @param settings_navigation $settingsnav The settings navigation object
 * @param navigation_node $nodenav The node to add module settings to
 */
function bigbluebuttonbn_extend_settings_navigation(settings_navigation $settingsnav, navigation_node $nodenav) {
    // Check for overrides.
    $overriden = extension::override_settings_navigation($settingsnav, $nodenav);
    if ($overriden) {
        return;
    }

    // Run core/default logic here.
    global $USER;
    $context = context_module::instance($settingsnav->get_page()->cm->id);
    // Add validate completion if the callback for meetingevents is enabled and user is allowed to edit the activity.
    if (
        (bool) \mod_bigbluebuttonbn\local\config::get('meetingevents_enabled') &&
        has_capability('moodle/course:manageactivities', $context, $USER->id)
    ) {
        $completionvalidate = '#action=completion_validate&bigbluebuttonbn=' . $settingsnav->get_page()->cm->instance;
        $nodenav->add(
            get_string('completionvalidatestate', 'bigbluebuttonbn'),
            $completionvalidate,
            navigation_node::TYPE_CONTAINER
        );
    }

    // Call all appends.
    extension::append_settings_navigation($settingsnav, $nodenav);
}

/**
 * In place editable for the recording table
 *
 * @param string $itemtype
 * @param string $itemid
 * @param mixed $newvalue
 * @return mixed|null
 */
function bigbluebuttonbn_inplace_editable($itemtype, $itemid, $newvalue) {
    $editableclass = "\\mod_bigbluebuttonbn\\output\\recording_{$itemtype}_editable";
    if (class_exists($editableclass)) {
        return call_user_func([$editableclass, 'update'], $itemid, $newvalue);
    }
    return null; // Will raise an exception in core update_inplace_editable method.
}

/**
 * Returns all events since a given time in specified bigbluebutton activity.
 * We focus here on the two events: play and join.
 *
 * @param array $activities
 * @param int $index
 * @param int $timestart
 * @param int $courseid
 * @param int $cmid
 * @param int $userid
 * @param int $groupid
 * @return array
 */
function bigbluebuttonbn_get_recent_mod_activity(&$activities, &$index, $timestart, $courseid, $cmid, $userid = 0,
    $groupid = 0): array {
    $instance = instance::get_from_cmid($cmid);
    $instance->set_group_id($groupid);
    $cm = $instance->get_cm();
    $logs =
        logger::get_user_completion_logs_with_userfields($instance,
            $userid ?? null,
            [logger::EVENT_JOIN, logger::EVENT_PLAYED],
            $timestart);

    foreach ($logs as $log) {
        $activity = new stdClass();

        $activity->type = 'bigbluebuttonbn';
        $activity->cmid = $cm->id;
        $activity->name = format_string($instance->get_meeting_name(), true);
        $activity->sectionnum = $cm->sectionnum;
        $activity->timestamp = $log->timecreated;
        $activity->user = new stdClass();
        $userfields = explode(',', implode(',', \core_user\fields::get_picture_fields()));
        foreach ($userfields as $userfield) {
            if ($userfield == 'id') {
                // Aliased in SQL above.
                $activity->user->{$userfield} = $log->userid;
            } else {
                $activity->user->{$userfield} = $log->{$userfield};
            }
        }
        $activity->user->fullname = fullname($log);
        $activity->content = '';
        $activity->eventname = logger::get_printable_event_name($log);
        if ($log->log == logger::EVENT_PLAYED) {
            if (!empty($log->meta)) {
                $meta = json_decode($log->meta);
                if (!empty($meta->recordingid)) {
                    $recording = recording::get_record(['id' => $meta->recordingid]);
                    if ($recording) {
                        $activity->content = $recording->get('name');
                    }
                }
            }
        }
        $activities[$index++] = $activity;
    }
    return $activities;
}

/**
 * Outputs the bigbluebutton logs indicated by $activity.
 *
 * @param stdClass $activity the activity object the bigbluebuttonbn resides in
 * @param int $courseid the id of the course the bigbluebuttonbn resides in
 * @param bool $detail not used, but required for compatibilty with other modules
 * @param array $modnames not used, but required for compatibilty with other modules
 * @param bool $viewfullnames not used, but required for compatibilty with other modules
 */
function bigbluebuttonbn_print_recent_mod_activity(stdClass $activity, int $courseid, bool $detail, array $modnames,
    bool $viewfullnames) {
    global $OUTPUT;
    $modinfo = [];
    $userpicture = $OUTPUT->user_picture($activity->user);

    $template = ['userpicture' => $userpicture,
        'submissiontimestamp' => $activity->timestamp,
        'modinfo' => $modinfo,
        'userurl' => new moodle_url('/user/view.php', array('id' => $activity->user->id, 'course' => $courseid)),
        'fullname' => $activity->user->fullname];
    if (isset($activity->eventname)) {
        $template['eventname'] = $activity->eventname;
    }
    echo $OUTPUT->render_from_template('mod_bigbluebuttonbn/recentactivity', $template);
}

/**
 * Given a course and a date, prints a summary of all the activity for this module
 *
 * @param object $course
 * @param bool $viewfullnames capability
 * @param int $timestart
 * @return bool success
 */
function bigbluebuttonbn_print_recent_activity(object $course, bool $viewfullnames, int $timestart): bool {
    global $OUTPUT;
    $modinfo = get_fast_modinfo($course);
    if (empty($modinfo->instances['bigbluebuttonbn'])) {
        return true;
    }
    $out = '';
    foreach ($modinfo->instances['bigbluebuttonbn'] as $cm) {
        if (!$cm->uservisible) {
            continue;
        }
        $instance = instance::get_from_cmid($cm->id);
        $logs = logger::get_user_completion_logs_with_userfields($instance,
            null,
            [logger::EVENT_JOIN, logger::EVENT_PLAYED],
            $timestart);
        if ($logs) {
            echo $OUTPUT->heading(get_string('new_bigblubuttonbn_activities', 'bigbluebuttonbn') . ':', 6);
            foreach ($logs as $log) {
                $activityurl = new moodle_url('/mod/bigbluebuttonbn/index.php', ['id' => $course->id]);
                print_recent_activity_note($log->timecreated,
                    $log,
                    logger::get_printable_event_name($log) . ' - ' . $instance->get_meeting_name(),
                    $activityurl->out(),
                    false,
                    $viewfullnames);
            }
        }

        echo $out;
    }
    return true;
}


/**
 * Creates a number of BigblueButtonBN activities.
 *
 * @param tool_generator_course_backend $backend
 * @param testing_data_generator $generator
 * @param int $courseid
 * @param int $number
 * @return void
 */
function bigbluebuttonbn_course_backend_generator_create_activity(tool_generator_course_backend $backend,
    testing_data_generator $generator,
    int $courseid,
    int $number
) {
    // Set up generator.
    $bbbgenerator = $generator->get_plugin_generator('mod_bigbluebuttonbn');

    // Create assignments.
    $backend->log('createbigbluebuttonbn', $number, true, 'mod_bigbluebuttonbn');
    for ($i = 0; $i < $number; $i++) {
        $record = array('course' => $courseid);
        $options = array('section' => $backend->get_target_section());
        $bbbgenerator->create_instance($record, $options);
        $backend->dot($i, $number);
    }
    $backend->end_log();
}

/**
 * Whether the activity is branded.
 * This information is used, for instance, to decide if a filter should be applied to the icon or not.
 *
 * @return bool True if the activity is branded, false otherwise.
 */
function bigbluebuttonbn_is_branded(): bool {
    return true;
}

/**
 * Update/create grade item for given BigBlueButtonBN activity
 *
 * @category grade
 * @param stdClass $bigbluebuttonbn instance object
 * @param array|object|string|null $grades Optional array/object of grade(s); 'reset' means reset grades in gradebook
 * @return int 0 if ok, error code otherwise
 */
function bigbluebuttonbn_grade_item_update(stdClass $bigbluebuttonbn, array|object|string|null $grades=null): int {
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');
    $params = ['itemname' => $bigbluebuttonbn->name];
    if ($bigbluebuttonbn->grade == 0) {
        $params['gradetype'] = GRADE_TYPE_NONE;
    } else if ($bigbluebuttonbn->grade > 0) {
        $params['gradetype'] = GRADE_TYPE_VALUE;
        $params['grademax'] = $bigbluebuttonbn->grade;
        $params['grademin'] = 0;
    } else if ($bigbluebuttonbn->grade < 0) {
        $params['gradetype'] = GRADE_TYPE_SCALE;
        $params['scaleid'] = -$bigbluebuttonbn->grade;
    }
    if ($grades === 'reset') {
        $params['reset'] = true;
        $grades = null;
    }
    return grade_update(
        source: 'mod/bigbluebuttonbn',
        courseid: $bigbluebuttonbn->course,
        itemtype: 'mod',
        itemmodule: 'bigbluebuttonbn',
        iteminstance: $bigbluebuttonbn->id,
        itemnumber: 0,
        grades: $grades,
        itemdetails: $params
    );
}

/**
 * Update activity grades.
 *
 * @param stdClass $bigbluebuttonbn instance object
 */
function bigbluebuttonbn_update_grades(stdClass $bigbluebuttonbn): void {
    // BigBlueButtonBN does not have a grades table, so we will only update grade item.
    bigbluebuttonbn_grade_item_update($bigbluebuttonbn);
}

/**
 * Removes all grades from gradebook
 *
 * @param int $courseid
 */
function bigbluebuttonbn_reset_gradebook(int $courseid): void {
    global $DB;
    $sql = "SELECT b.*, cm.idnumber as cmidnumber, b.course as courseid
              FROM {bigbluebuttonbn} b, {course_modules} cm, {modules} m
             WHERE m.name='bigbluebuttonbn' AND m.id=cm.module AND cm.instance=b.id AND b.course=?";

    if ($bigbluebuttonbns = $DB->get_records_sql($sql, [$courseid])) {
        foreach ($bigbluebuttonbns as $bigbluebuttonbn) {
            bigbluebuttonbn_grade_item_update($bigbluebuttonbn, 'reset');
        }
    }
}

/**
 * Delete grade item for given activity
 *
 * @param stdClass $bigbluebuttonbn instance object
 * @return int Returns GRADE_UPDATE_OK, GRADE_UPDATE_FAILED, GRADE_UPDATE_MULTIPLE or GRADE_UPDATE_ITEM_LOCKED
 */
function bigbluebuttonbn_grade_item_delete(stdClass $bigbluebuttonbn): int {
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');
    return grade_update(
        source: 'mod/bigbluebuttonbn',
        courseid: $bigbluebuttonbn->course,
        itemtype: 'mod',
        itemmodule: 'bigbluebuttonbn',
        iteminstance: $bigbluebuttonbn->id,
        itemnumber: 0,
        grades: null,
        itemdetails: ['deleted' => 1]
    );
}

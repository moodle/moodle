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
 * Local helper functions for reminders cron function.
 *
 * @package    local_reminders
 * @author     Isuru Weerarathna <uisurumadushanka89@gmail.com>
 * @copyright  2012 Isuru Madushanka Weerarathna
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/course/lib.php');

require_once($CFG->dirroot . '/local/reminders/reminder.class.php');
require_once($CFG->dirroot . '/local/reminders/contents/site_reminder.class.php');
require_once($CFG->dirroot . '/local/reminders/contents/user_reminder.class.php');
require_once($CFG->dirroot . '/local/reminders/contents/course_reminder.class.php');
require_once($CFG->dirroot . '/local/reminders/contents/category_reminder.class.php');
require_once($CFG->dirroot . '/local/reminders/contents/group_reminder.class.php');
require_once($CFG->dirroot . '/local/reminders/contents/due_reminder.class.php');

/**
 * Returns a list of upcoming activities for the given course,
 *
 * @param int $courseid course id.
 * @param int $currtime epoch time to compare.
 * @return array list of event records.
 */
function get_upcoming_events_for_course($courseid, $currtime) {
    global $DB, $CFG;

    // We default exclude these kind of event types.
    $excludedstatuses = ['site', 'user', 'open'];

    // When activity openings separation is enabled in global settings, we will retrieve those events too.
    if (isset($CFG->local_reminders_separateactivityopenings) && $CFG->local_reminders_separateactivityopenings) {
        $excludedstatuses = array_filter($excludedstatuses, function ($it) {
            return $it != 'open';
        });
    }
    list($insql, $inparams) = $DB->get_in_or_equal($excludedstatuses, SQL_PARAMS_QM, 'param', false);

    return $DB->get_records_sql("SELECT *
        FROM {event}
        WHERE courseid = $courseid
            AND timestart > $currtime
            AND visible = 1
            AND eventtype $insql
        ORDER BY timestart",
        $inparams);
}

/**
 * Returns all settings associated with given course and event which
 * was set in course reminder settings.
 *
 * @param int $courseid course id.
 * @param int $eventid event id.
 * @return array all settings related to this course event.
 */
function fetch_course_activity_settings($courseid, $eventid) {
    global $DB;

    $records = $DB->get_records_sql("SELECT settingkey, settingvalue
        FROM {local_reminders_activityconf}
        WHERE courseid = :courseid AND eventid = :eventid",
        ['courseid' => $courseid, 'eventid' => $eventid]);
    $pairs = [];
    if (!empty($records)) {
        foreach ($records as $record) {
            $pairs[$record->settingkey] = $record->settingvalue;
        }
    }
    return $pairs;
}

/**
 * Returns true if no reminders to send has been scheduled in course settings
 * page for the provided activity.
 *
 * @param int $courseid course id.
 * @param int $eventid event id.
 * @param string $keytocheck key to check for.
 * @return bool return true if reminders disabled for activity.
 */
function has_disabled_reminders_for_activity($courseid, $eventid, $keytocheck=REMINDERS_ENABLED_KEY) {
    $activitysettings = fetch_course_activity_settings($courseid, $eventid);
    if (array_key_exists($keytocheck, $activitysettings) && !$activitysettings[$keytocheck]) {
        return true;
    }
    return false;
}

/**
 * Returns true if reminders can be sent to the given event based on Moodle configured settings.
 *
 * @param object $event event instance reference.
 * @param object $options context options.
 * @param number $aheadday number of days ahead this activity belongs to.
 * @param object $customtime contains the custom time value and unit (if configured).
 * @return bool true if reminders can sent, otherwise false.
 */
function should_run_for_activity($event, $options, $aheadday=null, $customtime=null) {
    global $DB, $CFG;

    $showtrace = $options->showtrace;
    $aheadday = $options->aheadday;
    $courseid = $event->courseid;
    $eventid = $event->id;
    $aheaddayskey = "days$aheadday";
    $explicitenable = isset($CFG->local_reminders_explicitenable) && $CFG->local_reminders_explicitenable;

    $activitysettings = fetch_course_activity_settings($courseid, $eventid);
    if (array_key_exists(REMINDERS_ENABLED_KEY, $activitysettings) && !$activitysettings[REMINDERS_ENABLED_KEY]) {
        $showtrace && mtrace("  [Local Reminder] Reminders for activity event#$eventid (title=$event->name) ".
            "have been disabled in the course settings.");
        return false;
    } else if (array_key_exists($aheaddayskey, $activitysettings) && !$activitysettings[$aheaddayskey]) {
        $showtrace && mtrace("  [Local Reminder] Reminders for activity event#$eventid (title=$event->name) ".
            "have been disabled for $aheadday days ahead.");
        return false;
    } else if ($customtime && array_key_exists("custom", $activitysettings) && !$activitysettings["custom"]) {
        $showtrace && mtrace("  [Local Reminder] Reminders for activity event#$eventid (title=$event->name) ".
            "have been disabled for custom time ($customtime->value  $customtime->unit) ahead.");
        return false;
    }

    if ($explicitenable) {
        // Must be explicitly enabled the reminders to be sent.
        if (array_key_exists(REMINDERS_ENABLED_KEY, $activitysettings)
            && $activitysettings[REMINDERS_ENABLED_KEY]
            && array_key_exists($aheaddayskey, $activitysettings)
            && $activitysettings[$aheaddayskey]) {
            return true;
        }

        // Handle custom setting.
        if (array_key_exists(REMINDERS_ENABLED_KEY, $activitysettings)
            && $activitysettings[REMINDERS_ENABLED_KEY]
            && array_key_exists("custom", $activitysettings)
            && $activitysettings["custom"]) {
            return true;
        }

        $showtrace && mtrace("  [Local Reminder] Reminders for activity event#$eventid (title=$event->name) ".
            "have explicitly not been enabled in the course settings.");
        return false;
    }
    return true;
}

/**
 * This method will filter out all the activity events finished recently
 * and send reminders for users who still have not yet completed that activity.
 * Only once user will receive emails.
 *
 * @param int $curtime current time to check for cutoff.
 * @param int $timewindowstart time window start.
 * @param array $activityroleids role ids for acitivities.
 * @param object $fromuser from user for emails.
 * @return void.
 */
function send_overdue_activity_reminders($curtime, $timewindowstart, $activityroleids, $fromuser) {
    global $DB, $CFG;

    mtrace('[LOCAL REMINDERS] Overdue Activity Reminder Cron Started. Events between @('.$timewindowstart.', '.$curtime.')');

    if (isset($CFG->local_reminders_enableoverdueactivityreminders) && !$CFG->local_reminders_enableoverdueactivityreminders) {
        mtrace('[LOCAL REMINDERS] Overdue Activity reminders are not enabled from settings! Skipped.');
        return;
    }

    $rangestart = $timewindowstart;
    $statuses = ['due', 'close', 'expectcompletionon', 'gradingdue'];
    list($insql, $inparams) = $DB->get_in_or_equal($statuses);

    $querysql = "SELECT e.*
        FROM {event} e
            LEFT JOIN {local_reminders_post_act} lrpa ON e.id = lrpa.eventid
        WHERE
            e.timestart >= $rangestart AND e.timestart < $curtime
            AND lrpa.eventid IS NULL
            AND e.eventtype $insql
            AND e.visible = 1";

    $allexpiredevents = $DB->get_records_sql($querysql, $inparams);
    if (!$allexpiredevents || count($allexpiredevents) == 0) {
        mtrace('[LOCAL REMINDERS] No expired events found for this cron cycle! Skipped.');
        return;
    }

    $excludedmodules = [];
    if (isset($CFG->local_reminders_excludedmodulenames)) {
        $excludedmodules = explode(',', $CFG->local_reminders_excludedmodulenames);
    }

    mtrace('[LOCAL REMINDERS] Number of expired events found for this cron cycle: '.count($allexpiredevents));
    foreach ($allexpiredevents as $event) {
        $event = new calendar_event($event);

        if (in_array($event->modulename, $excludedmodules)) {
            mtrace("  [Local Reminder] xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx");
            mtrace("  [Local Reminder]   Skipping overdue event #$event->id in excluded module '$event->modulename'!");
            mtrace("  [Local Reminder] xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx");
            return;
        }

        if (has_disabled_reminders_for_activity($event->courseid, $event->id, 'enabledoverdue')) {
            mtrace("[LOCAL REMINDERS] Activity event $event->id overdue reminders disabled in the course settings");
            continue;
        }

        $reminderref = process_activity_event($event, -1, null, $activityroleids, true, REMINDERS_CALL_TYPE_OVERDUE);
        if (!isset($reminderref)) {
            mtrace('[LOCAL REMINDERS] Skipped post-activity event for '.$event->id);
            continue;
        }
        mtrace('[LOCAL REMINDERS] Processing post-activity event for '.$event->id.' occurred @ '.$event->timestart);

        $sendusers = $reminderref->get_sending_users();
        $ctxinfo = new \stdClass;
        $ctxinfo->overduemessage = $CFG->local_reminders_overduewarnmessage ?? '';
        $ctxinfo->overduetitle = $CFG->local_reminders_overduewarnprefix ?? '';
        $alreadysentuserids = [];

        foreach ($sendusers as $touser) {
            try {

                // Check whether already an overdue email is sent or not...
                if (in_array($touser->id, $alreadysentuserids)) {
                    mtrace("[LOCAL REMINDERS] An overdue reminder has been sent to user $touser->id ($touser->username) " .
                    "already for this event! Skipping.");
                    continue;
                }
                $alreadysentuserids[] = $touser->id;

                $eventdata = $reminderref->get_updating_send_event(REMINDERS_CALL_TYPE_OVERDUE, $fromuser, $touser, $ctxinfo);

                $mailresult = message_send($eventdata);

                if (!$mailresult) {
                    mtrace("[LOCAL REMINDERS] Could not send out reminder for event#$event->id to user $touser->id");
                } else {
                    mtrace('[LOCAL_REMINDERS] Post Activity Mail sent to user: '.$touser->id);
                }
            } catch (\Throwable $mex) {
                mtrace('[LOCAL REMINDERS] Error: local/reminders/locallib.php send_post_activity_reminders(): '.$mex->getMessage());
            }
        }

        try {
            $activityrecord = new \stdClass();
            $activityrecord->sendtime = $curtime;
            $activityrecord->eventid = $event->id;
            $DB->insert_record('local_reminders_post_act', $activityrecord, false);
            mtrace('[LOCAL REMINDERS] Successfully marked event#'.$event->id.' as overdue sent completed in db.');

        } catch (\Exception $dex) {
            // Catastrophic failure and not sure what to do at this moment.
            mtrace('[LOCAL REMINDERS] Error: It seems Local Reminders plugin cannot write to database!'
                .'Please disable overdue reminders until database write access provided!'
                .$dex->getMessage());
        }
    }
}

/**
 * Common function handling a specific activity which falls into a course.
 *
 * @param object $event event instance.
 * @param object $course course instance belongs to.
 * @param object $cm course module instance.
 * @param object $options misc options to process.
 * @return reminder_ref reminder reference instance.
 */
function handle_course_activity_event($event, $course, $cm, $options) {
    global $DB;

    $showtrace = $options->showtrace;
    $aheadday = $options->aheadday;
    $customtime = $options->customtime;

    if ($event->courseid > 0) {
        $coursesettings = $DB->get_record('local_reminders_course', ['courseid' => $event->courseid]);
        if (isset($coursesettings->status_activities) && $coursesettings->status_activities == 0) {
            $showtrace && mtrace("  [Local Reminder] Reminders for activities has been restricted in the configs.");
            return null;
        }
    }

    if (is_course_hidden_and_denied($course)) {
        $showtrace && mtrace("  [Local Reminder] Course is hidden. No reminders will be sent.");
        return null;
    } else if (!should_run_for_activity($event, $options, $aheadday, $customtime)) {
        return null;
    }

    $activityobj = fetch_module_instance($event->modulename, $event->instance, $event->courseid, $showtrace);
    $context = context_module::instance($cm->id);
    $sendusers = [];
    $reminder = new due_reminder($event, $course, $context, $cm, $aheadday, $customtime);

    mtrace("   [Local Reminder] Finding out users for event#".$event->id."...");
    if ($event->courseid <= 0 && $event->userid > 0) {
        // A user overridden activity.
        $showtrace && mtrace("  [Local Reminder] Event #".$event->id." is a user overridden ".$event->modulename." event.");
        $user = $DB->get_record('user', ['id' => $event->userid]);
        $sendusers[] = $user;
    } else if ($event->groupid > 0) {
        // A group overridden activity.
        $showtrace && mtrace("  [Local Reminder] Event #".$event->id." is a group overridden ".$event->modulename." event.");
        $group = $DB->get_record('groups', ['id' => $event->groupid]);
        $sendusers = get_users_in_group($group);
    } else {
        // Here 'ra.id field added to avoid printing debug message,
        // from get_role_users (has odd behaivior when called with an array for $roleid param'.
        $sendusers = get_active_role_users($options->activityroleids, $context);
        $sendusers = filter_user_group_overrides($event, $sendusers, $showtrace);

        // The user id is required but NOT the role_assignment id for filter_user_list which is returned from get_active_role_users.
        // Need to switch to the user id below before filtering.
        if ($sendusers) {
            $userarray = [];
            foreach ($sendusers as $roleassignmentuser) {
                $userarray += [$roleassignmentuser->id => \core_user::get_user($roleassignmentuser->id)];
            }
            $sendusers = $userarray;
        }

        // Filter user list,
        // see: https://docs.moodle.org/dev/Availability_API.
        $info = new \core_availability\info_module($cm);
        $sendusers = $info->filter_user_list($sendusers);
    }

    // This is not pretty. But we can do better.
    if (strcmp($event->eventtype, 'gradingdue') == 0 && isset($context)) {
        $filteredusers = [];
        foreach ($sendusers as $guser) {
            if (has_capability('mod/assign:grade', $context, $guser)) {
                $filteredusers[] = $guser;
            }
        }
        $sendusers = $filteredusers;
    }

    $reminder->set_activity($event->modulename, $activityobj);
    $filteredusers = $reminder->filter_authorized_users($sendusers, $options->calltype);
    return new reminder_ref($reminder, $filteredusers);
}

/**
 * Process activity event and creates a reminder instance wrapping it.
 *
 * @param object $event calendar event.
 * @param int $aheadday number of days ahead.
 * @param object $customtime contains the custom time value and unit (if configured).
 * @param array $activityroleids role ids for activities.
 * @param boolean $showtrace whether to print logs or not.
 * @param string $calltype calling type PRE|OVERDUE.
 * @return reminder_ref reminder reference instance.
 */
function process_activity_event($event, $aheadday, $customtime=null, $activityroleids=null, $showtrace=true,
    $calltype=REMINDERS_CALL_TYPE_PRE) {

    if (isemptystring($event->modulename)) {
        return null;
    }

    try {
        // When a calendar event added, this is being called and moodle throws invalid module ID: ${a},
        // Due to it tries to get from a cache, but yet not exist.
        $courseandcm = get_course_and_cm_from_instance($event->instance, $event->modulename, $event->courseid);
    } catch (Exception $ex) {
        return null;
    }
    $course = $courseandcm[0];
    $cm = $courseandcm[1];

    if (!empty($course) && !empty($cm)) {
        $options = new \stdClass;
        $options->aheadday = $aheadday;
        $options->customtime = $customtime;
        $options->showtrace = $showtrace;
        $options->activityroleids = $activityroleids;
        $options->calltype = $calltype;

        return handle_course_activity_event($event, $course, $cm, $options);
    }
    return null;
}

/**
 * Process unknown event and creates a reminder instance wrapping it if unknown
 * event is a module level activity.
 *
 * @param object $event calendar event.
 * @param int $aheadday number of days ahead.
 * @param object $customtime contains the custom time value and unit (if configured).
 * @param array $activityroleids role ids for activities.
 * @param boolean $showtrace whether to print logs or not.
 * @param string $calltype calling type PRE|OVERDUE.
 * @return reminder_ref reminder reference instance.
 */
function process_unknown_event($event, $aheadday, $customtime=null, $activityroleids=null, $showtrace=true,
    $calltype=REMINDERS_CALL_TYPE_PRE) {

    if (isemptystring($event->modulename)) {
        $showtrace && mtrace("  [Local Reminder] Unknown event type [$event->eventtype]!");
        return null;
    }

    return process_activity_event($event, $aheadday, $customtime, $activityroleids, $showtrace, $calltype);
}

/**
 * Process course event and creates a reminder instance wrapping it.
 *
 * @param object $event calendar event.
 * @param int $aheadday number of days ahead.
 * @param object $customtime contains the custom time value and unit (if configured).
 * @param array $courseroleids role ids for course.
 * @param boolean $showtrace whether to print logs or not.
 * @return reminder_ref reminder reference instance.
 */
function process_course_event($event, $aheadday, $customtime=null, $courseroleids=null, $showtrace=true) {
    global $DB, $PAGE;

    $course = $DB->get_record('course', ['id' => $event->courseid]);
    if (is_course_hidden_and_denied($course)) {
        $showtrace && mtrace("  [Local Reminder] Course is hidden. No reminders will be sent.");
        return null;
    } else if (has_disabled_reminders_for_activity($event->courseid, $event->id)) {
        $showtrace && mtrace("  [Local Reminder] Specific course reminders are disabled. Skipping.");
        return null;
    }

    $coursesettings = $DB->get_record('local_reminders_course', ['courseid' => $event->courseid]);
    if (isset($coursesettings->status_course) && $coursesettings->status_course == 0) {
        $showtrace && mtrace("  [Local Reminder] Reminders for course events has been restricted.");
        return null;
    }

    if (!empty($course)) {
        $sendusers = [];
        get_users_of_course($course->id, $courseroleids, $sendusers);

        $reminder = new course_reminder($event, $course, $aheadday, $customtime);
        return new reminder_ref($reminder, $sendusers);
    }
    return null;
}

/**
 * Process course category event and creates a reminder instance wrapping it.
 *
 * @param object $event calendar event.
 * @param int $aheadday number of days ahead.
 * @param object $customtime contains the custom time value and unit (if configured).
 * @param array $courseroleids role ids for course.
 * @param boolean $showtrace whether to print logs or not.
 * @return reminder_ref reminder reference instance.
 */
function process_category_event($event, $aheadday, $customtime=null, $courseroleids=null, $showtrace=true) {
    global $CFG;

    $catid = $event->categoryid;
    $cat = null;
    // From Moodle 3.6+ coursecat is deprecated.
    if (class_exists('core_course_category')) {
        $cat = core_course_category::get($catid, IGNORE_MISSING);
    } else {
        require_once($CFG->libdir . '/coursecatlib.php');
        $cat = coursecat::get($catid, IGNORE_MISSING);
    }
    if (is_null($cat)) {
        // Course category not found or not visible.
        $showtrace && mtrace("  [LOCAL REMINDERS] Course category is not visible or exists! Skipping.");
        return null;
    }
    $showtrace && mtrace("   [LOCAL REMINDERS] Course category: $catid => $cat->name");
    $childrencourses = $cat->get_courses(['recursive' => true]);
    $allusers = [];
    $currenttime = time();
    $allcourses = isset($CFG->local_reminders_category_noforcompleted) && !$CFG->local_reminders_category_noforcompleted;
    foreach ($childrencourses as $course) {
        if ($allcourses || $currenttime < $course->enddate) {
            get_users_of_course($course->id, $courseroleids, $allusers);
        } else {
            $showtrace && mtrace("   [LOCAL REMINDERS]   - Course skipped: $course->id => $course->fullname");
        }
    }
    $showtrace && mtrace("   [LOCAL REMINDERS] Total users to send = ".count($allusers));

    $reminder = new category_reminder($event, $cat, $aheadday, $customtime);
    return new reminder_ref($reminder, $allusers);
}

/**
 * Process group event and creates a reminder instance wrapping it.
 *
 * @param object $event calendar event.
 * @param int $aheadday number of days ahead.
 * @param object $customtime contains the custom time value and unit (if configured).
 * @param boolean $showtrace whether to print logs or not.
 * @return reminder_ref reminder reference instance.
 */
function process_group_event($event, $aheadday, $customtime=null, $showtrace=true) {
    global $DB, $PAGE;

    $group = $DB->get_record('groups', ['id' => $event->groupid]);
    if (!empty($group)) {
        if (isset($group->courseid) && !empty($group->courseid)) {
            $PAGE->set_context(context_course::instance($group->courseid));
        }
        $coursesettings = $DB->get_record('local_reminders_course', ['courseid' => $group->courseid]);
        if (isset($coursesettings->status_group) && $coursesettings->status_group == 0) {
            $showtrace && mtrace("  [Local Reminder] Reminders for group events has been restricted in the configs.");
            return null;
        }

        $reminder = new group_reminder($event, $group, $aheadday, $customtime);

        // Add module details, if this event is a mod type event.
        if (!isemptystring($event->modulename) && $event->courseid > 0) {
            $activityobj = fetch_module_instance($event->modulename, $event->instance, $event->courseid, $showtrace);
            $reminder->set_activity($event->modulename, $activityobj);
        }
        $sendusers = get_users_in_group($group);
        return new reminder_ref($reminder, $sendusers);
    }
}

/**
 * Process user event and creates a reminder instance wrapping it.
 *
 * @param object $event calendar event.
 * @param int $aheadday number of days ahead.
 * @param object $customtime contains the custom time value and unit (if configured).
 * @return reminder_ref reminder reference instance.
 */
function process_user_event($event, $aheadday, $customtime=null) {
    global $DB;

    $user = $DB->get_record('user', ['id' => $event->userid, 'deleted' => 0]);

    if (!empty($user)) {
        $reminder = new user_reminder($event, $user, $aheadday, $customtime);
        $sendusers[] = $user;
        return new reminder_ref($reminder, $sendusers);
    }
    return null;
}

/**
 * Process site event and creates a reminder instance wrapping it.
 *
 * @param object $event calendar event.
 * @param int $aheadday number of days ahead.
 * @param object $customtime contains the custom time value and unit (if configured).
 * @return reminder_ref reminder reference instance.
 */
function process_site_event($event, $aheadday, $customtime=null) {
    global $DB, $PAGE;

    $reminder = new site_reminder($event, $aheadday, $customtime);
    $sendusers = $DB->get_records_sql("SELECT *
        FROM {user}
        WHERE id > 1 AND deleted=0 AND suspended=0 AND confirmed=1;");
    $PAGE->set_context(context_system::instance());
    return new reminder_ref($reminder, $sendusers);
}

/**
 * Returns course roles and activity role ids globally defined in moodle.
 *
 * @return array containing two elements course roles ids and activity role ids.
 */
function get_roles_for_reminders() {
    global $CFG;

    $allroles = get_all_roles();
    $courseroleids = [];
    $activityroleids = [];
    $categoryroleids = [];
    if (!empty($allroles)) {
        $flag = 0;
        foreach ($allroles as $arole) {
            $roleoptionactivity = $CFG->local_reminders_activityroles;
            if (isset($roleoptionactivity[$flag]) && $roleoptionactivity[$flag] == '1') {
                $activityroleids[] = $arole->id;
            }
            $roleoption = $CFG->local_reminders_courseroles;
            if (isset($roleoption[$flag]) && $roleoption[$flag] == '1') {
                $courseroleids[] = $arole->id;
            }
            $roleoptioncat = $CFG->local_reminders_categoryroles;
            if (isset($roleoptioncat[$flag]) && $roleoptioncat[$flag] == '1') {
                $categoryroleids[] = $arole->id;
            }
            $flag++;
        }
    }
    return [
        $courseroleids,
        $activityroleids,
        $categoryroleids,
    ];
}

/**
 * Appends all users in the course for the given array.
 *
 * @param int $courseid course id to search users for.
 * @param array $courseroleids course role id array.
 * @param array $arraytoappend user array to append new unique users.
 * @return void nothing.
 */
function get_users_of_course($courseid, $courseroleids, &$arraytoappend) {
    global $PAGE;

    $context = context_course::instance($courseid);
    $PAGE->set_context($context);
    $roleusers = get_role_users($courseroleids, $context, true, 'ra.id as ra_id, u.*');
    $senduserids = array_map(
    function($u) {
        return $u->id;
    }, $roleusers);
    $senduserrefs = array_combine($senduserids, $roleusers);
    foreach ($senduserids as $userid) {
        if (!array_key_exists($userid, $arraytoappend)) {
            $arraytoappend[$userid] = $senduserrefs[$userid];
        }
    }
}

/**
 * This function returns user timezone.
 *
 * @param object $user user object
 * @return object timezone of the user
 */
function reminders_get_timezone($user) {
    global $CFG;
    if ($CFG->local_reminders_timezone) {
        return core_date::get_default_php_timezone(); // Server timezone.
    } else {
        return core_date::get_user_timezone($user); // User selected timezone.
    }
}

/**
 * This function formats the due time of the event appropiately. If this event
 * has a duration then formatted time will be [starttime]-[endtime].
 *
 * @param object $user user object
 * @param object $event event instance
 * @param array $tzstyle css style string for tz
 * @param boolean $includetz whether to include timezone or not.
 * @param string $mode mode of rendering. html or plain.
 * @return string formatted time string
 */
function format_event_time_duration($user, $event, $tzstyle=null, $includetz=true, $mode='html') {
    $followedtimeformat = get_string('strftimedaydate', 'langconfig');
    $usertimeformat = get_correct_timeformat_user($user);

    $tzone = 99;
    if (isset($user) && !empty($user)) {
        $tzone = reminders_get_timezone($user);
    }

    $addflag = false;
    $formattedtimeprefix = userdate($event->timestart, $followedtimeformat, $tzone);
    $formattedtime = userdate($event->timestart, $usertimeformat, $tzone);
    $sdate = usergetdate($event->timestart, $tzone);
    if ($event->timeduration > 0) {
        $etime = $event->timestart + $event->timeduration;
        $ddate = usergetdate($etime, $tzone);

        // Falls in the same day.
        if ($sdate['year'] == $ddate['year'] && $sdate['mon'] == $ddate['mon'] && $sdate['mday'] == $ddate['mday']) {
            // Bug fix for not correctly displaying times in incorrect formats.
            // Issue report: https://tracker.moodle.org/browse/CONTRIB-3647?focusedCommentId=408657.
            $formattedtime .= ' - '.userdate($etime, $usertimeformat, $tzone);
            $addflag = true;
        } else {
            $formattedtime .= ' - '.
                userdate($etime, $followedtimeformat, $tzone)." ".
                userdate($etime, $usertimeformat, $tzone);
        }

        if ($addflag) {
            $formattedtime = $formattedtimeprefix.'  ['.$formattedtime.']';
        } else {
            $formattedtime = $formattedtimeprefix.' '.$formattedtime;
        }

    } else {
        $formattedtime = $formattedtimeprefix.' '.$formattedtime;
    }

    if (!$includetz) {
        return $formattedtime;
    }

    $tzstr = local_reminders_tz_info::get_human_readable_tz($tzone);
    if ($mode == 'html') {
        if (!isemptystring($tzstyle)) {
            $tzstr = '<span style="'.$tzstyle.'">'.$tzstr.'</span>';
        } else {
            $tzstr = '<span style="font-size:13px;color: #888;">'.$tzstr.'</span>';
        }
        return $formattedtime.' &nbsp;&nbsp;'.$tzstr;
    } else {
        return $formattedtime.' - '.$tzstr;
    }
}

/**
 * This function would return time formats relevent for the given user.
 * Sometimes a user might have changed time display format in his/her preferences.
 *
 * @param object $user user instance to get specific time format.
 * @return string date time format for user.
 */
function get_correct_timeformat_user($user) {
    static $langtimeformat = null;
    if ($langtimeformat === null) {
        $langtimeformat = get_string('strftimetime', 'langconfig');
    }

    // We get user time formattings... if such exist, will return non-empty value.
    $utimeformat = get_user_preferences('calendar_timeformat', '', $user);
    if (empty($utimeformat)) {
        $utimeformat = get_config(null, 'calendar_site_timeformat');
    }
    return empty($utimeformat) ? $langtimeformat : $utimeformat;
}

/**
 * Returns array of users active (not suspended) in the provided contexts and
 * at the same time belongs to the given roles.
 *
 * @param array $activityroleids role ids
 * @param object $context context to search for users
 * @return array of user records
 */
function get_active_role_users($activityroleids, $context) {
    return get_role_users($activityroleids, $context, true, 'ra.id as ra_id, u.*',
                    null, false, '', '', '',
                    'ue.status = :userenrolstatus',
                    ['userenrolstatus' => ENROL_USER_ACTIVE]);
}

/**
 * Filter and return eligible set of users after excluding users who belongs
 * in overridden extensions.
 *
 * @param object $event source event object.
 * @param array $sendusers all users for this activity instance.
 * @param bool $showtrace to print logs or not.
 * @return array filtered out users.
 */
function filter_user_group_overrides($event, $sendusers, $showtrace) {
    global $DB;

    if (!in_array($event->modulename, REMINDERS_SUPPORTED_OVERRIDES)) {
        return $sendusers;
    }

    $showtrace && mtrace("  [Local Reminder] Event supports overrides for key ");
    $idcolumn = REMINDERS_SUPPORTED_OVERRIDES_REF_IDS[$event->modulename];
    $overridesrecords = $DB->get_records($event->modulename.'_overrides', [$idcolumn => $event->instance]);
    if (empty($overridesrecords)) {
        $showtrace && mtrace("  [Local Reminder] No overrides for activity ".$event->instance."!");
        return $sendusers;
    }

    $extendedusers = [];
    foreach ($overridesrecords as $record) {
        if ($record->userid > 0) {
            $showtrace && mtrace("     Overrides for user id: ".$record->userid);
            $extendedusers[] = $record->userid;
        } else if ($record->groupid > 0) {
            $showtrace && mtrace("     Overrides for group id: ".$record->groupid);
            $groupmemberroles = groups_get_members_by_role($record->groupid, $event->courseid, 'u.id');
            if (!empty($groupmemberroles)) {
                foreach ($groupmemberroles as $roleid => $roledata) {
                    foreach ($roledata->users as $member) {
                        $extendedusers[] = $member->id;
                    }
                }
            }
        }
    }

    $finalarray = array_filter($sendusers, function($it) use ($extendedusers) {
        return !in_array($it->id, $extendedusers);
    });
    return $finalarray;
}


/**
 * Returns all users belong to the given group.
 *
 * @param object $group group object as received from db.
 * @return array users in an array
 */
function get_users_in_group($group) {
    global $DB;

    $sendusers = [];
    $groupmemberroles = groups_get_members_by_role($group->id, $group->courseid, 'u.id');
    if ($groupmemberroles) {
        foreach ($groupmemberroles as $roleid => $roledata) {
            foreach ($roledata->users as $member) {
                $sendusers[] = $DB->get_record('user', ['id' => $member->id]);
            }
        }
    }
    return $sendusers;
}

/**
 * Returns true if the activity belongs to a hidden course. And prevents sending reminders.
 *
 * @param object $course course instance.
 * @return bool status of course hidden filter should apply or not.
 */
function is_course_hidden_and_denied($course) {
    global $CFG;

    if (isset($CFG->local_reminders_filterevents)) {
        if ($CFG->local_reminders_filterevents == REMINDERS_SEND_ONLY_VISIBLE && $course->visible == 0) {
            return true;
        }
    }
    return false;
}

/**
 * Returns true if input string is empty/whitespaces only, otherwise false.
 *
 * @param string $str text to compare.
 * @return boolean true if string is empty or whitespace.
 */
function isemptystring($str) {
    return !isset($str) || empty($str) || trim($str) === '';
}

/**
 * Function to retrive module instace from corresponding module
 * table. This function is written because when sending reminders
 * it can restrict showing some fields in the message which are sensitive
 * to user. (Such as some descriptions are hidden until defined date)
 * Function is very similar to the function in datalib.php/get_coursemodule_from_instance,
 * but by below it returns all fields of the module.
 *
 * Eg: can get the quiz instace from quiz table, can get the new assignment
 * instace from assign table, etc.
 *
 * @param string $modulename name of module type, eg. resource, assignment,...
 * @param int $instance module instance number (id in resource, assignment etc. table)
 * @param int $courseid optional course id for extra validation
 * @param boolean $showtrace optional to print trace logs.
 * @return individual module instance (a quiz, a assignment, etc).
 *          If fails returns null
 */
function fetch_module_instance($modulename, $instance, $courseid=0, $showtrace=true) {
    global $DB;

    $params = ['instance' => $instance, 'modulename' => $modulename];

    $courseselect = "";

    if ($courseid) {
        $courseselect = "AND cm.course = :courseid";
        $params['courseid'] = $courseid;
    }

    $sql = "SELECT m.*
              FROM {course_modules} cm
                   JOIN {modules} md ON md.id = cm.module
                   JOIN {".$modulename."} m ON m.id = cm.instance
             WHERE m.id = :instance AND md.name = :modulename
                   $courseselect";

    try {
        return $DB->get_record_sql($sql, $params, IGNORE_MISSING);
    } catch (moodle_exception $mex) {
        $showtrace && mtrace('  [Local Reminder - ERROR] Failed to fetch module instance! '.$mex.getMessage);
        return null;
    }
}

/**
 * Returns the from user instance which should be send notifications.
 *
 * @return object from user object.
 */
function get_from_user() {
    global $CFG;

    $fromuser = core_user::get_noreply_user();
    if (isset($CFG->local_reminders_sendasname) && !empty($CFG->local_reminders_sendasname)) {
        $fromuser->firstname = $CFG->local_reminders_sendasname;
    }
    if (isset($CFG->local_reminders_sendas) && $CFG->local_reminders_sendas == REMINDERS_SEND_AS_ADMIN) {
        $fromuser = get_admin();
    }
    return $fromuser;
}

/**
 * Reminder specific timezone data holder.
 *
 * Note: you must have at least Moodle 3.5 or higher.
 *
 * @package    local_reminders
 * @copyright  2012 Isuru Madushanka Weerarathna
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_reminders_tz_info extends \core_date {
    /**
     * hold the timezone mappings.
     *
     * @var array
     */
    protected static $mapping;

    /**
     * Returns human readable timezone name for given timezone.
     *
     * @param string $tz input time zone.
     * @return string human readable tz.
     */
    public static function get_human_readable_tz($tz) {
        if (!isset(self::$mapping)) {
            static::load_tz_info();
        }

        if (is_numeric($tz)) {
            return static::get_localised_timezone($tz);
        }
        if (array_key_exists($tz, self::$mapping)) {
            return self::$mapping[$tz];
        }
        return static::get_localised_timezone($tz);
    }

    /**
     * Load timezone information from base class.
     *
     * @return void.
     */
    private static function load_tz_info() {
        self::$mapping = [];
        foreach (static::$badzones as $detailname => $abbr) {
            if (!is_numeric($detailname)) {
                self::$mapping[$abbr] = $detailname;
            }
        }
    }
}

/**
 * Reminder reference class.
 *
 * @package    local_reminders
 * @copyright  2012 Isuru Madushanka Weerarathna
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class reminder_ref {
    /**
     * created reminder reference.
     *
     * @var local_reminder
     */
    protected $reminder;
    /**
     * Array of users to send this reminder.
     *
     * @var array
     */
    protected $sendusers;

    /**
     * Creates new reminder reference.
     *
     * @param local_reminder $reminder created reminder.
     * @param array $sendusers array of users.
     */
    public function __construct($reminder, $sendusers) {
        $this->reminder = $reminder;
        $this->sendusers = $sendusers;
    }

    /**
     * Returns total number of users eligible to send this reminder.
     *
     * @return int total number of users.
     */
    public function get_total_users_to_send() {
        return count($this->sendusers);
    }

    /**
     * Returns the ultimate notification event instance to send for given user.
     *
     * @param object $fromuser from user.
     * @param object $touser user to send.
     * @return object new notification instance.
     */
    public function get_event_to_send($fromuser, $touser) {
        return $this->reminder->get_sending_event($fromuser, $touser);
    }

    /**
     * Returns the notification event instance based on change type.
     *
     * @param string $changetype change type PRE|OVERDUE.
     * @param object $fromuser from user.
     * @param object $touser user to send.
     * @param stdClass $ctxinfo additional context info needed to process.
     * @return object new notification instance.
     */
    public function get_updating_send_event($changetype, $fromuser, $touser, $ctxinfo) {
        return $this->reminder->get_updating_event_message($changetype, $fromuser, $touser, $ctxinfo);
    }

    /**
     * Returns eligible sending users as array.
     *
     * @return array users eligible to receive message.
     */
    public function get_sending_users() {
        return $this->sendusers;
    }

    /**
     * Cleanup the reminder memory.
     *
     * @return void nothing.
     */
    public function cleanup() {
        unset($this->sendusers);
        if (isset($this->reminder)) {
            $this->reminder->cleanup();
        }
    }
}

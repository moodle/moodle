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
 * All avitivity specific classes required for specific handling.
 *
 * @package    local_reminders
 * @copyright  2012 Isuru Madushanka Weerarathna
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Abstract class for formatting reminder message based on activity type.
 *
 * @package    local_reminders
 * @copyright  2012 Isuru Madushanka Weerarathna
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class local_reminder_activity_handler {

    /**
     * This function will format/append reminder messages with necessary info
     * based on constraints in that activity instance.
     *
     * @param string $htmlmail email content.
     * @param string $modulename module name as 'lesson'.
     * @param object $activity lesson instance.
     * @param object $user user to prepare the message for.
     * @param object $event event instance.
     * @param object $reminder reminder reference.
     * @return void nothing.
     */
    public function append_info(&$htmlmail, $modulename, $activity, $user=null, $event=null, $reminder=null) {
        // Do nothing.
    }

    /**
     * Returns associated description of the given activity.
     *
     * @param object $activity activity instance
     * @param object $event event instance
     * @return string description related to this activity.
     */
    abstract public function get_description($activity, $event);

    /**
     * Filter out users who still does not have completed this activity.
     *
     * @param array $users user array to check.
     * @param string $type reminder call type PRE|POST.
     * @param object $activity activity instance.
     * @param object $course course instance belong to.
     * @param object $coursemodule course module instance.
     * @param object $coursemodulecontext course module context instance.
     * @return array array of filtered users.
     */
    public function filter_authorized_users($users, $type, $activity, $course, $coursemodule, $coursemodulecontext) {
        return $users;
    }

    /**
     * Formats given date and time based on given user's timezone.
     *
     * @param number $datetime epoch time.
     * @param object $user user to format for.
     * @param object $reminder reminder reference.
     * @return string formatted date time according to give user.
     */
    protected function format_datetime($datetime, $user, $reminder) {
        $tzone = 99;
        if (isset($user) && !empty($user)) {
            $tzone = reminders_get_timezone($user);
        }

        $daytimeformat = get_string('strftimedaydate', 'langconfig');
        $utimeformat = get_correct_timeformat_user($user);
        return userdate($datetime, $daytimeformat, $tzone).
            ' '.userdate($datetime, $utimeformat, $tzone).
            ' &nbsp;&nbsp;<span style="'.$reminder->tzshowstyle.'">'.
            local_reminders_tz_info::get_human_readable_tz($tzone).'</span>';
    }

    /**
     * Returns completion status for the given course module of the user id.
     *
     * @param object $course course instance.
     * @param object $coursemodule course module instance.
     * @param int $userid user id.
     * @return bool true if completed. false otherwise.
     */
    protected function check_completion_status($course, $coursemodule, $userid) {
        $completion = new completion_info($course);
        if ($completion->is_enabled($coursemodule)) {
            return $completion->get_data($coursemodule, false, $userid)->completionstate;
        }
        return false;
    }

}

/**
 * Supports generic activity completion using Moodle's completion_info API.
 *
 * This class will be used when no specific handler being implemented for the given
 * module. And also will gracefully failed, if filtering cannot be fulfilled.
 *
 * @package    local_reminders
 * @copyright  2012 Isuru Madushanka Weerarathna
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_reminder_generic_handler extends local_reminder_activity_handler {

    /**
     * Filter out users who still does not have completed this module using Moodle core completion API.
     *
     * @param array $users user array to check.
     * @param string $type reminder call type PRE|POST.
     * @param object $activity activity instance.
     * @param object $course course instance belong to.
     * @param object $coursemodule course module instance.
     * @param object $coursemodulecontext course module context instance.
     * @return array array of filtered users.
     */
    public function filter_authorized_users($users, $type, $activity, $course, $coursemodule, $coursemodulecontext) {
        $filteredusers = [];
        foreach ($users as $auser) {
            $status = $this->check_completion_status($course, $coursemodule, $auser->id);
            if (!$status) {
                $filteredusers[] = $auser;
            }
        }
        return $filteredusers;
    }

    /**
     * Return survey intro field as description.
     *
     * @param object $activity activity instance
     * @param object $event event instance
     * @return string description related to this activity.
     */
    public function get_description($activity, $event) {
        if (isset($activity->intro)) {
            return $activity->intro;
        }
        return null;
    }
}


/**
 * Supports quiz related information.
 *
 * @package    local_reminders
 * @copyright  2012 Isuru Madushanka Weerarathna
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_reminder_quiz_handler extends local_reminder_activity_handler {

    /**
     * Returns description of the quiz activity if only quiz has not yet started.
     *
     * @param object $activity quiz activity instance.
     * @param object $event calendar event.
     * @return string|null description of the quiz if not yet started.
     */
    public function get_description($activity, $event) {
        if (isset($activity->timeopen)) {
            $utime = time();
            if ($utime > $activity->timeopen) {
                return $activity->intro;
            }
        }
        return null;
    }

    /**
     * Filter out users who still does not have finished the quiz.
     *
     * @param array $users user array to check.
     * @param string $type reminder call type PRE|POST.
     * @param object $activity activity instance.
     * @param object $course course instance belong to.
     * @param object $coursemodule course module instance.
     * @param object $coursemodulecontext course module context instance.
     * @return array array of filtered users.
     */
    public function filter_authorized_users($users, $type, $activity, $course, $coursemodule, $coursemodulecontext) {
        global $CFG;
        require_once($CFG->dirroot . '/mod/quiz/lib.php');

        $filteredusers = [];
        foreach ($users as $auser) {
            $canattempt = has_capability('mod/quiz:attempt', $coursemodulecontext, $auser);
            if (!$canattempt) {
                continue;
            }
            $status = $this->check_completion_status($course, $coursemodule, $auser->id);
            if (!$status) {
                $filteredusers[] = $auser;
            }
        }
        return $filteredusers;
    }

    /**
     * Appends quiz time limit into the email.
     *
     * @param string $htmlmail email content.
     * @param string $modulename module name as 'quiz'.
     * @param object $activity quiz instance.
     * @param object $user user to prepare the message for.
     * @param object $event event instance.
     * @param object $reminder reminder reference.
     * @return void nothing.
     */
    public function append_info(&$htmlmail, $modulename, $activity, $user=null, $event=null, $reminder=null) {
        if (isset($activity->timelimit) && $activity->timelimit > 0) {
            $htmlmail .= $reminder->write_table_row(
                get_string('timelimit', 'quiz'),
                format_time($activity->timelimit));
        }
    }
}

/**
 * Supports assignment related information.
 *
 * @package    local_reminders
 * @copyright  2012 Isuru Madushanka Weerarathna
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_reminder_assign_handler extends local_reminder_activity_handler {

    /**
     * Filter out users who still does not have submitted assignment.
     *
     * @param array $users user array to check.
     * @param string $type reminder call type PRE|POST.
     * @param object $activity activity instance.
     * @param object $course course instance belong to.
     * @param object $coursemodule course module instance.
     * @param object $coursemodulecontext course module context instance.
     * @return array array of filtered users.
     */
    public function filter_authorized_users($users, $type, $activity, $course, $coursemodule, $coursemodulecontext) {
        global $CFG;
        require_once($CFG->dirroot . '/mod/assign/lib.php');
        require_once($CFG->dirroot . '/lib/completionlib.php');

        $filteredusers = [];
        foreach ($users as $auser) {
            $cansubmit = has_capability('mod/assign:submit', $coursemodulecontext, $auser);
            if (!$cansubmit) {
                continue;
            }
            $status = $this->check_completion_status($course, $coursemodule, $auser->id);
            if (!$status) {
                $filteredusers[] = $auser;
            }
        }
        return $filteredusers;
    }

    /**
     * Appends assignment cutoff time into the email.
     *
     * @param string $htmlmail email content.
     * @param string $modulename module name as 'assign'.
     * @param object $activity assignment instance.
     * @param object $user user to prepare the message for.
     * @param object $event event instance.
     * @param object $reminder reminder reference.
     * @return void nothing.
     */
    public function append_info(&$htmlmail, $modulename, $activity, $user=null, $event=null, $reminder=null) {
        if (isset($activity->cutoffdate) && $activity->cutoffdate > 0) {
            $htmlmail .= $reminder->write_table_row(
                get_string('cutoffdate', 'assign'),
                $this->format_datetime($activity->cutoffdate, $user, $reminder));
        }
    }

    /**
     * Returns description of the assignment activity if only show description is allowed.
     *
     * @param object $activity assignment activity instance.
     * @param object $event calendar event.
     * @return string|null description of the assignment if allowed.
     */
    public function get_description($activity, $event) {
        if (isset($activity->alwaysshowdescription)) {
            $utime = time();
            if ($activity->alwaysshowdescription > 0 || $utime > $activity->allowsubmissionsfromdate) {
                return $event->description;
            }
        }
        return null;
    }
}

/**
 * Supports choice related information.
 *
 * @package    local_reminders
 * @copyright  2012 Isuru Madushanka Weerarathna
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_reminder_choice_handler extends local_reminder_activity_handler {

    /**
     * Filter out users who still does not have submitted choice.
     *
     * @param array $users user array to check.
     * @param string $type reminder call type PRE|POST.
     * @param object $activity activity instance.
     * @param object $course course instance belong to.
     * @param object $coursemodule course module instance.
     * @param object $coursemodulecontext course module context instance.
     * @return array array of filtered users.
     */
    public function filter_authorized_users($users, $type, $activity, $course, $coursemodule, $coursemodulecontext) {
        global $CFG;
        require_once($CFG->dirroot . '/mod/choice/lib.php');
        require_once($CFG->dirroot . '/lib/completionlib.php');

        $filteredusers = [];
        foreach ($users as $auser) {
            $cansubmit = has_capability('mod/choice:choose', $coursemodulecontext, $auser);
            if (!$cansubmit) {
                continue;
            }
            $status = $this->check_completion_status($course, $coursemodule, $auser->id);
            if (!$status) {
                $filteredusers[] = $auser;
            }
        }
        return $filteredusers;
    }

    /**
     * Return choice intro field as description.
     *
     * @param object $activity activity instance
     * @param object $event event instance
     * @return string description related to this activity.
     */
    public function get_description($activity, $event) {
        if (isset($activity->intro)) {
            return $activity->intro;
        }
        return null;
    }
}

/**
 * Supports feedback related information.
 *
 * @package    local_reminders
 * @copyright  2012 Isuru Madushanka Weerarathna
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_reminder_feedback_handler extends local_reminder_activity_handler {

    /**
     * Filter out users who still does not have submitted feedback.
     *
     * @param array $users user array to check.
     * @param string $type reminder call type PRE|POST.
     * @param object $activity activity instance.
     * @param object $course course instance belong to.
     * @param object $coursemodule course module instance.
     * @param object $coursemodulecontext course module context instance.
     * @return array array of filtered users.
     */
    public function filter_authorized_users($users, $type, $activity, $course, $coursemodule, $coursemodulecontext) {
        global $CFG;
        require_once($CFG->dirroot . '/mod/feedback/lib.php');
        require_once($CFG->dirroot . '/lib/completionlib.php');

        $filteredusers = [];
        foreach ($users as $auser) {
            $cansubmit = has_capability('mod/feedback:complete', $coursemodulecontext, $auser);
            if (!$cansubmit) {
                continue;
            }

            $status = $this->check_completion_status($course, $coursemodule, $auser->id);
            if (!$status) {
                $filteredusers[] = $auser;
            }
        }
        return $filteredusers;
    }

    /**
     * Return feedback intro field as description.
     *
     * @param object $activity activity instance
     * @param object $event event instance
     * @return string description related to this activity.
     */
    public function get_description($activity, $event) {
        if (isset($activity->intro)) {
            return $activity->intro;
        }
        return null;
    }
}

/**
 * Supports Lesson module related information.
 *
 * @package    local_reminders
 * @copyright  2012 Isuru Madushanka Weerarathna
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_reminder_lesson_handler extends local_reminder_activity_handler {

    /**
     * Filter out users who still does not have completed lesson activity.
     *
     * @param array $users user array to check.
     * @param string $type reminder call type PRE|POST.
     * @param object $activity activity instance.
     * @param object $course course instance belong to.
     * @param object $coursemodule course module instance.
     * @param object $coursemodulecontext course module context instance.
     * @return array array of filtered users.
     */
    public function filter_authorized_users($users, $type, $activity, $course, $coursemodule, $coursemodulecontext) {
        global $CFG;
        require_once($CFG->dirroot . '/mod/lesson/lib.php');

        $filteredusers = [];
        foreach ($users as $auser) {
            $cansubmit = has_capability('mod/lesson:view', $coursemodulecontext, $auser);
            if (!$cansubmit) {
                continue;
            }
            $status = lesson_get_completion_state($course, $coursemodule, $auser->id, false);
            if (!$status) {
                $filteredusers[] = $auser;
            }
        }
        return $filteredusers;
    }

    /**
     * Appends lesson time limit into the email.
     *
     * @param string $htmlmail email content.
     * @param string $modulename module name as 'lesson'.
     * @param object $activity lesson instance.
     * @param object $user user to prepare the message for.
     * @param object $event event instance.
     * @param object $reminder reminder reference.
     * @return void nothing.
     */
    public function append_info(&$htmlmail, $modulename, $activity, $user=null, $event=null, $reminder=null) {
        if (isset($activity->timelimit) && $activity->timelimit > 0) {
            $htmlmail .= $reminder->write_table_row(
                get_string('timelimit', 'lesson'),
                format_time($activity->timelimit));
        }
    }

    /**
     * Return feedback intro field as description.
     *
     * @param object $activity activity instance
     * @param object $event event instance
     * @return string description related to this activity.
     */
    public function get_description($activity, $event) {
        if (isset($activity->intro)) {
            return $activity->intro;
        }
        return null;
    }
}

/**
 * Supports survey related information.
 *
 * @package    local_reminders
 * @copyright  2012 Isuru Madushanka Weerarathna
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_reminder_survey_handler extends local_reminder_activity_handler {

    /**
     * Filter out users who still does not have submitted survey.
     *
     * @param array $users user array to check.
     * @param string $type reminder call type PRE|POST.
     * @param object $activity activity instance.
     * @param object $course course instance belong to.
     * @param object $coursemodule course module instance.
     * @param object $coursemodulecontext course module context instance.
     * @return array array of filtered users.
     */
    public function filter_authorized_users($users, $type, $activity, $course, $coursemodule, $coursemodulecontext) {
        global $CFG;
        require_once($CFG->dirroot . '/mod/survey/lib.php');

        $filteredusers = [];
        foreach ($users as $auser) {
            $cansubmit = has_capability('mod/survey:participate', $coursemodulecontext, $auser);
            if (!$cansubmit) {
                continue;
            }
            $status = survey_get_completion_state($course, $coursemodule, $auser->id, false);
            if (!$status) {
                $filteredusers[] = $auser;
            }
        }
        return $filteredusers;
    }

    /**
     * Return survey intro field as description.
     *
     * @param object $activity activity instance
     * @param object $event event instance
     * @return string description related to this activity.
     */
    public function get_description($activity, $event) {
        if (isset($activity->intro)) {
            return $activity->intro;
        }
        return null;
    }
}

/**
 * Supports resource module related filtering.
 *
 * @package    local_reminders
 * @copyright  2012 Isuru Madushanka Weerarathna
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_reminder_resource_handler extends local_reminder_activity_handler {

    /**
     * Filter out users who still does not have completed this resource.
     *
     * @param array $users user array to check.
     * @param string $type reminder call type PRE|POST.
     * @param object $activity activity instance.
     * @param object $course course instance belong to.
     * @param object $coursemodule course module instance.
     * @param object $coursemodulecontext course module context instance.
     * @return array array of filtered users.
     */
    public function filter_authorized_users($users, $type, $activity, $course, $coursemodule, $coursemodulecontext) {
        global $CFG;
        require_once($CFG->dirroot . '/mod/resource/lib.php');

        $filteredusers = [];
        foreach ($users as $auser) {
            $cansubmit = has_capability('mod/resource:view', $coursemodulecontext, $auser);
            if (!$cansubmit) {
                continue;
            }
            $status = $this->check_completion_status($course, $coursemodule, $auser->id);
            if (!$status) {
                $filteredusers[] = $auser;
            }
        }
        return $filteredusers;
    }

    /**
     * Return survey intro field as description.
     *
     * @param object $activity activity instance
     * @param object $event event instance
     * @return string description related to this activity.
     */
    public function get_description($activity, $event) {
        if (isset($activity->intro)) {
            return $activity->intro;
        }
        return null;
    }
}

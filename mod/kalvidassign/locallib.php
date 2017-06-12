<?php
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
 * Kaltura video assignment locallib script.
 *
 * @package    mod_kalvidassign
 * @author     Remote-Learner.net Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  (C) 2014 Remote Learner.net Inc http://www.remote-learner.net
 */

/**
 * This function returns true if the assignment submission period is over
 *
 * @param kalvidassign obj
 *
 * @return bool - true if assignment submission period is over else false
 */

define('KALASSIGN_ALL', 0);
define('KALASSIGN_REQ_GRADING', 1);
define('KALASSIGN_SUBMITTED', 2);

require_once(dirname(dirname(dirname(__FILE__))).'/lib/gradelib.php');
require_once($CFG->dirroot.'/mod/kalvidassign/renderable.php');
require_once(dirname(dirname(dirname(__FILE__))).'/local/kaltura/locallib.php');

/**
 * Check if the assignment submission end date has passed or if late submissions
 * are prohibited
 *
 * @param object - Kaltura instance video assignment object
 * @return bool - true if expired, otherwise false
 */
function kalvidassign_assignemnt_submission_expired($kalvidassign) {
    $expired = false;

    if ($kalvidassign->preventlate) {
        $expired = (0 != $kalvidassign->timedue) && (time() > $kalvidassign->timedue);
    }

    return $expired;
}

/**
 * Retrieve a list of users who have submitted assignments
 * 
 * @param int $kalvidassignid The assignment id.
 * @param string $filter Filter results by assignments that have been submitted or
 * assignment that need to be graded or no filter at all.
 * @return mixed collection of users or false.
 */
function kalvidassign_get_submissions($kalvidassignid, $filter = '') {
    global $DB;

    $where = '';
    switch ($filter) {
        case KALASSIGN_SUBMITTED:
            $where = ' timemodified > 0 AND ';
            break;
        case KALASSIGN_REQ_GRADING:
            $where = ' timemarked < timemodified AND ';
            break;
    }

    $param = array('instanceid' => $kalvidassignid);
    $where .= ' vidassignid = :instanceid';

    // Reordering the fields returned to make it easier to use in the grade_get_grades function.
    $columns = 'userid,vidassignid,entry_id,grade,submissioncomment,format,teacher,mailed,timemarked,timecreated,timemodified,source,width,height';
    $records = $DB->get_records_select('kalvidassign_submission', $where, $param, 'timemodified DESC', $columns);

    if (empty($records)) {
        return false;
    }

    return $records;
}

/**
 * This function retrives the user's submission record.
 * @param int $kalvidassignid The activity instance id.
 * @param int $userid The user id.
 * @return object A data object consisting of the user's submission.
 */
function kalvidassign_get_submission($kalvidassignid, $userid) {
    global $DB;

    $param = array('instanceid' => $kalvidassignid, 'userid' => $userid);
    $where = '';
    $where .= ' vidassignid = :instanceid AND userid = :userid';

    // Reordering the fields returned to make it easier to use in the grade_get_grades function.
    $columns = 'userid,id,vidassignid,entry_id,grade,submissioncomment,format,teacher,mailed,timemarked,timecreated,timemodified,source,width,height';
    $record = $DB->get_record_select('kalvidassign_submission', $where, $param, '*');

    if (empty($record)) {
        return false;
    }

    return $record;

}

/**
 * This function retrieves the submission grade object.
 * @param int $instanceid The activity instance id.
 * @param int $userid The user id.
 * @return object A data object consisting of the user's submission.
 */
function kalvidassign_get_submission_grade_object($instanceid, $userid) {
    global $DB;

    $param = array('kvid' => $instanceid, 'userid' => $userid);

    $sql = "SELECT u.id, u.id AS userid, s.grade AS rawgrade, s.submissioncomment AS feedback, s.format AS feedbackformat,
                   s.teacher AS usermodified, s.timemarked AS dategraded, s.timemodified AS datesubmitted
              FROM {user} u, {kalvidassign_submission} s
             WHERE u.id = s.userid AND s.vidassignid = :kvid
                   AND u.id = :userid";

    $data = $DB->get_record_sql($sql, $param);

    if (-1 == $data->rawgrade) {
        $data->rawgrade = null;
    }

    return $data;
}

/**
 * This function validates the course module id and returns the course module object, course object and activity instance object.
 * @return array an array with the following values array(course module object, $course object, activity instance object).
 */
function kalvidassign_validate_cmid ($cmid) {
    global $DB;

    if (!$cm = get_coursemodule_from_id('kalvidassign', $cmid)) {
        print_error('invalidcoursemodule');
    }

    if (!$course = $DB->get_record('course', array('id' => $cm->course))) {
        print_error('coursemisconf');
    }

    if (!$kalvidassignobj = $DB->get_record('kalvidassign', array('id' => $cm->instance))) {
        print_error('invalidid', 'kalvidassign');
    }

    return array($cm, $course, $kalvidassignobj);
}

/**
 * This function returns HTML markup to signify a submission was late.
 * @return string HTML markup
 */
function kalvidassign_display_lateness($timesubmitted, $timedue) {
    if (!$timedue) {
        return '';
    }
    $time = $timedue - $timesubmitted;
    if ($time < 0) {
        $timetext = get_string('late', 'kalvidassign', format_time($time));
        return ' (<span class="late">'.$timetext.'</span>)';
    } else {
        $timetext = get_string('early', 'kalvidassign', format_time($time));
        return ' (<span class="early">'.$timetext.'</span>)';
    }
}

/**
 * Alerts teachers by email of new or changed assignments that need grading
 *
 * First checks whether the option to email teachers is set for this assignment.
 * Sends an email to ALL teachers in the course (or in the group if using separate groups).
 * Uses the methods kalvidassign_email_teachers_text() and kalvidassign_email_teachers_html() to construct the content.
 *
 * @global object
 * @global object
 * @param object $cm Kaltura video assignment course module object.
 * @param string $name Name of the video assignment instance.
 * @param object $submission The submission that has changed.
 * @param object $context The context object.
 * @return void
 */
function kalvidassign_email_teachers($cm, $name, $submission, $context) {
    global $CFG, $DB;

    $user = $DB->get_record('user', array('id'=>$submission->userid));

    if ($teachers = kalvidassign_get_graders($cm, $user, $context)) {

        $strassignments = get_string('modulenameplural', 'kalvidassign');
        $strassignment  = get_string('modulename', 'kalvidassign');
        $strsubmitted   = get_string('submitted', 'kalvidassign');

        foreach ($teachers as $teacher) {
            $info = new stdClass();
            $info->username = fullname($user, true);
            $info->assignment = format_string($name, true);
            $info->url = $CFG->wwwroot.'/mod/kalvidassign/grade_submissions.php?cmid='.$cm->id;
            $info->timeupdated = strftime('%c', $submission->timemodified);
            $info->courseid = $cm->course;
            $info->cmid     = $cm->id;

            $postsubject = $strsubmitted.': '.$user->username.' -> '.$name;
            $posttext = kalvidassign_email_teachers_text($info);
            $posthtml = ($teacher->mailformat == 1) ? kalvidassign_email_teachers_html($info) : '';

            $eventdata = new \core\message\message();
            $eventdata->modulename       = 'kalvidassign';
            $eventdata->userfrom         = $user;
            $eventdata->userto           = $teacher;
            $eventdata->subject          = $postsubject;
            $eventdata->fullmessage      = $posttext;
            $eventdata->fullmessageformat = FORMAT_PLAIN;
            $eventdata->fullmessagehtml  = $posthtml;
            $eventdata->smallmessage     = $postsubject;

            $eventdata->name            = 'kalvidassign_updates';
            $eventdata->component       = 'mod_kalvidassign';
            $eventdata->notification    = 1;
            $eventdata->contexturl      = $info->url;
            $eventdata->contexturlname  = $info->assignment;

            message_send($eventdata);
        }
    }
}

/**
 * Returns a list of teachers that should be grading given submission.
 *
 * @param object $cm Kaltura video assignment course module object.
 * @param object $user The Moodle user object.
 * @param object $context A context object.
 * @return array An array of grading userids
 */
function kalvidassign_get_graders($cm, $user, $context) {
    // Potential graders.
    $potgraders = get_users_by_capability($context, 'mod/kalvidassign:gradesubmission', '', '', '', '', '', '', false, false);

    $graders = array();
    // Separate groups are being used.
    if (groups_get_activity_groupmode($cm) == SEPARATEGROUPS) {
        // Try to find all groups.
        if ($groups = groups_get_all_groups($cm->course, $user->id)) {
            foreach ($groups as $group) {
                foreach ($potgraders as $t) {
                    if ($t->id == $user->id) {
                        continue; // do not send self
                    }
                    if (groups_is_member($group->id, $t->id)) {
                        $graders[$t->id] = $t;
                    }
                }
            }
        } else {
            // user not in group, try to find graders without group
            foreach ($potgraders as $t) {
                if ($t->id == $user->id) {
                    // do not send self.
                    continue;
                }
                // ugly hack.
                if (!groups_get_all_groups($cm->course, $t->id)) {
                    $graders[$t->id] = $t;
                }
            }
        }
    } else {
        foreach ($potgraders as $t) {
            if ($t->id == $user->id) {
                // do not send self.
                continue;
            }
            $graders[$t->id] = $t;
        }
    }
    return $graders;
}

/**
 * Creates the text content for emails to teachers
 *
 * @param $info object The info used by the 'emailteachermail' language string
 * @return string
 */
function kalvidassign_email_teachers_text($info) {
    global $DB;

    $param    = array('id' => $info->courseid);
    $course   = $DB->get_record('course', $param);
    $posttext = '';

    if (!empty($course)) {
        $posttext  = format_string($course->shortname, true, $course->id).' -> '.get_string('modulenameplural', 'kalvidassign').'  -> ';
        $posttext .= format_string($info->assignment, true, $course->id)."\n";
        $posttext .= '---------------------------------------------------------------------'."\n";
        $posttext .= get_string("emailteachermail", "kalvidassign", $info)."\n";
        $posttext .= "\n---------------------------------------------------------------------\n";
    }

    return $posttext;
}

/**
 * Creates the html content for emails to teachers
 *
 * @param object $info The info used by the 'emailteachermailhtml' language string
 * @return string
 */
function kalvidassign_email_teachers_html($info) {
    global $CFG, $DB;

    $param    = array('id' => $info->courseid);
    $course   = $DB->get_record('course', $param);
    $posthtml = '';

    if (!empty($course)) {
        $posthtml .= html_writer::start_tag('p');
        $attr = array('href' => new moodle_url('/course/view.php', array('id' => $course->id)));
        $posthtml .= html_writer::tag('a', format_string($course->shortname, true, $course->id), $attr);
        $posthtml .= '->';
        $attr = array('href' => new moodle_url('/mod/kalvidassign/view.php', array('id' => $info->cmid)));
        $posthtml .= html_writer::tag('a', format_string($info->assignment, true, $course->id), $attr);
        $posthtml .= html_writer::end_tag('p');
        $posthtml .= html_writer::start_tag('hr');
        $posthtml .= html_writer::tag('p', get_string('emailteachermailhtml', 'kalvidassign', $info));
        $posthtml .= html_writer::end_tag('hr');
    }
    return $posthtml;
}

/**
 * This function retrieves a list of enrolled users with the capability to submit to the activity.
 * @return array An array of user objects.
 */
function kalvidassign_get_assignment_students($cm) {
    $context = context_module::instance($cm->id);
    $users = get_enrolled_users($context, 'mod/kalvidassign:submit', 0, 'u.id');

    return $users;
}
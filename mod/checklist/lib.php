<?php
// This file is part of the Checklist plugin for Moodle - http://moodle.org/
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

defined('MOODLE_INTERNAL') || die();

/**
 * Library of functions and constants for module checklist
 * This file should have two well differenced parts:
 *   - All the core Moodle functions, neeeded to allow
 *     the module to work integrated in Moodle.
 *   - All the checklist specific functions, needed
 *     to implement all the module logic. Please, note
 *     that, if the module become complex and this lib
 *     grows a lot, it's HIGHLY recommended to move all
 *     these module specific functions to a new php file,
 *     called "locallib.php" (see forum, quiz...). This will
 *     help to save some memory when Moodle is performing
 *     actions across all modules.
 */

define("CHECKLIST_EMAIL_NO", 0);
define("CHECKLIST_EMAIL_STUDENT", 1);
define("CHECKLIST_EMAIL_TEACHER", 2);
define("CHECKLIST_EMAIL_BOTH", 3);

define("CHECKLIST_TEACHERMARK_NO", 2);
define("CHECKLIST_TEACHERMARK_YES", 1);
define("CHECKLIST_TEACHERMARK_UNDECIDED", 0);

define("CHECKLIST_MARKING_STUDENT", 0);
define("CHECKLIST_MARKING_TEACHER", 1);
define("CHECKLIST_MARKING_BOTH", 2);

define("CHECKLIST_AUTOUPDATE_NO", 0);
define("CHECKLIST_AUTOUPDATE_YES", 2);
define("CHECKLIST_AUTOUPDATE_YES_OVERRIDE", 1);

define("CHECKLIST_AUTOPOPULATE_NO", 0);
define("CHECKLIST_AUTOPOPULATE_SECTION", 2);
define("CHECKLIST_AUTOPOPULATE_COURSE", 1);

define("CHECKLIST_MAX_INDENT", 10);

global $CFG;
require_once(dirname(__FILE__).'/locallib.php');
require_once($CFG->libdir.'/completionlib.php');

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param object $checklist An object from the form in mod_form.php
 * @return int The id of the newly inserted checklist record
 */
function checklist_add_instance($checklist) {
    global $DB;

    $checklist->timecreated = time();
    $checklist->id = $DB->insert_record('checklist', $checklist);

    checklist_grade_item_update($checklist);

    return $checklist->id;
}

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param object $checklist An object from the form in mod_form.php
 * @return boolean Success/Fail
 */
function checklist_update_instance($checklist) {
    global $DB;

    $checklist->timemodified = time();
    $checklist->id = $checklist->instance;

    $oldrecord = $DB->get_record('checklist', ['id' => $checklist->id]);

    $newmax = $checklist->maxgrade;
    $oldmax = $oldrecord->maxgrade;

    $oldcompletion = $oldrecord->completionpercent;
    if (isset($checklist->completionpercent)) {
        $newcompletion = $checklist->completionpercent;
    } else {
        $newcompletion = $oldcompletion;
    }

    $newautoupdate = $checklist->autoupdate;
    $oldautoupdate = $oldrecord->autoupdate;

    $newteacheredit = $checklist->teacheredit;
    $oldteacheredit = $oldrecord->teacheredit;

    $DB->update_record('checklist', $checklist);

    // Add or remove all calendar events, as needed.
    $course = $DB->get_record('course', array('id' => $checklist->course));
    $cm = get_coursemodule_from_instance('checklist', $checklist->id, $course->id);
    $chk = new checklist_class($cm->id, 0, $checklist, $cm, $course);
    $chk->setallevents();

    checklist_grade_item_update($checklist);
    if ($newmax != $oldmax) {
        checklist_update_grades($checklist);
    } else if ($newcompletion && ($newcompletion != $oldcompletion)) {
        // This will already be updated if checklist_update_grades() is called.
        $ci = new completion_info($course);
        $context = context_module::instance($cm->id);
        if (get_config('mod_checklist', 'onlyenrolled')) {
            $users = get_enrolled_users($context, 'mod/checklist:updateown', 0, 'u.id', null, 0, 0, true);
        } else {
            $users = get_users_by_capability($context, 'mod/checklist:updateown', 'u.id');
        }
        foreach ($users as $user) {
            $ci->update_state($cm, COMPLETION_UNKNOWN, $user->id);
        }
    }
    if ($newautoupdate) {
        if (!$oldautoupdate) {
            $chk->update_all_autoupdate_checks();
        } else {
            $oldautoteacher = ($oldteacheredit == CHECKLIST_MARKING_TEACHER);
            $newautoteacher = ($newteacheredit == CHECKLIST_MARKING_TEACHER);
            if ($oldautoteacher != $newautoteacher) {
                // Just switched to/from teacher-only marking => automatic checkmarks need updating
                // (as they are updating a different value from before).
                $chk->update_all_autoupdate_checks();
            }
        }
    }

    return true;
}

/**
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 */
function checklist_delete_instance($id) {
    global $DB;

    if (!$checklist = $DB->get_record('checklist', array('id' => $id))) {
        return false;
    }

    // Remove all calendar events.
    if ($checklist->duedatesoncalendar) {
        $checklist->duedatesoncalendar = false;
        $course = $DB->get_record('course', array('id' => $checklist->course));
        $cm = get_coursemodule_from_instance('checklist', $checklist->id, $course->id);
        if ($cm) { // Should not be false, but check, just in case...
            $chk = new checklist_class($cm->id, 0, $checklist, $cm, $course);
            $chk->setallevents();
        }
    }

    $items = $DB->get_records('checklist_item', array('checklist' => $checklist->id), '', 'id');
    if (!empty($items)) {
        $items = array_keys($items);
        $DB->delete_records_list('checklist_check', 'item', $items);
        $DB->delete_records_list('checklist_comment', 'itemid', $items);
        $DB->delete_records('checklist_item', array('checklist' => $checklist->id));
    }
    $DB->delete_records('checklist', array('id' => $checklist->id));

    checklist_grade_item_delete($checklist);

    return true;
}

/**
 *
 */
function checklist_update_all_grades() {
    global $DB;

    $checklists = $DB->get_records('checklist');
    foreach ($checklists as $checklist) {
        checklist_update_grades($checklist);
    }
}

/**
 * @param object $checklist
 * @param int $userid
 */
function checklist_update_grades($checklist, $userid = 0) {
    global $DB;

    $params = array(
        'checklist' => $checklist->id,
        'userid' => 0,
        'itemoptional' => CHECKLIST_OPTIONAL_NO,
        'hidden' => CHECKLIST_HIDDEN_NO
    );
    $items = \mod_checklist\local\checklist_item::fetch_all($params);

    if (!$items) {
        return;
    }
    if (!$course = $DB->get_record('course', array('id' => $checklist->course))) {
        return;
    }
    if (!$cm = get_coursemodule_from_instance('checklist', $checklist->id, $course->id)) {
        return;
    }
    $context = context_module::instance($cm->id);

    $checkgroupings = false; // Don't check items against groupings unless we really have to.
    $groupings = checklist_class::get_course_groupings($course->id);
    foreach ($items as $item) {
        if ($item->grouping && isset($groupings[$item->grouping])) {
            $checkgroupings = true;
            break;
        }
    }

    if ($checklist->teacheredit == CHECKLIST_MARKING_STUDENT) {
        $date = ', MAX(c.usertimestamp) AS datesubmitted';
        $where = 'c.usertimestamp > 0';
    } else {
        $date = ', MAX(c.teachertimestamp) AS dategraded';
        $where = 'c.teachermark = '.CHECKLIST_TEACHERMARK_YES;
    }

    if ($checkgroupings) {
        if ($userid) {
            $users = $DB->get_records('user', array('id' => $userid), null, 'id, firstname, lastname');
        } else {
            if (get_config('mod_checklist', 'onlyenrolled')) {
                $users = get_enrolled_users($context, 'mod/checklist:updateown', 0, 'u.id, u.firstname, u.lastname',
                                            null, 0, 0, true);
            } else {
                $users = get_users_by_capability($context, 'mod/checklist:updateown', 'u.id, u.firstname, u.lastname');
            }
            if (!$users) {
                return;
            }
        }

        $grades = array();

        // With groupings, need to update each user individually (as each has different groupings).
        foreach ($users as $uid => $user) {
            $groupings = checklist_class::get_user_groupings($uid, $course->id);

            $total = 0;
            $itemlist = [];
            foreach ($items as $item) {
                if ($item->grouping) {
                    if (!in_array($item->grouping, $groupings)) {
                        continue;
                    }
                }
                $itemlist[] = $item->id;
                $total++;
            }
            $itemlist = implode(',', $itemlist);

            if (!$total) { // No items - set score to 0.
                $ugrade = new stdClass;
                $ugrade->userid = $uid;
                $ugrade->rawgrade = 0;
                $ugrade->date = time();

            } else {
                $sql = 'SELECT (SUM(CASE WHEN '.$where.' THEN 1 ELSE 0 END) * ? / ? ) AS rawgrade'.$date;
                $sql .= " FROM {checklist_check} c ";
                $sql .= " WHERE c.item IN ($itemlist)";
                $sql .= ' AND c.userid = ? ';

                $ugrade = $DB->get_record_sql($sql, array($checklist->maxgrade, $total, $uid));
                if (!$ugrade) {
                    $ugrade = new stdClass;
                    $ugrade->rawgrade = 0;
                    $ugrade->date = time();
                }
                $ugrade->userid = $uid;
            }

            $ugrade->firstname = $user->firstname;
            $ugrade->lastname = $user->lastname;

            $grades[$uid] = $ugrade;
        }

    } else {
        // No need to check groupings, so update all student grades at once.

        if ($userid) {
            $users = $userid;
        } else {
            if (get_config('mod_checklist', 'onlyenrolled')) {
                $users = get_enrolled_users($context, 'mod/checklist:updateown', 0, 'u.id', null, 0, 0, true);
            } else {
                $users = get_users_by_capability($context, 'mod/checklist:updateown', 'u.id', '', '', '', '', '', false);
            }
            if (!$users) {
                return;
            }
            $users = array_keys($users);
        }

        $total = count($items);

        list($usql, $uparams) = $DB->get_in_or_equal($users);
        list($isql, $iparams) = $DB->get_in_or_equal(array_keys($items));

        $namefields = get_all_user_name_fields(true, 'u');

        $sql = 'SELECT u.id AS userid, (SUM(CASE WHEN '.$where.' THEN 1 ELSE 0 END) * ? / ? ) AS rawgrade'.$date;
        $sql .= ' , '.$namefields;
        $sql .= ' FROM {user} u LEFT JOIN {checklist_check} c ON u.id = c.userid';
        $sql .= " WHERE u.id $usql";
        $sql .= " AND c.item $isql";
        $sql .= ' GROUP BY u.id, '.$namefields;

        $params = array_merge($uparams, $iparams);
        $params = array_merge(array($checklist->maxgrade, $total), $params);

        $grades = $DB->get_records_sql($sql, $params);
    }

    foreach ($grades as $grade) {
        // Log completion of checklist.
        if ($grade->rawgrade == $checklist->maxgrade) {
            if ($checklist->emailoncomplete) {
                // Do not send another email if this checklist was already 'completed' in the last hour.
                if (!checklist_sent_email_recently($cm->id)) {
                    if (!isset($context)) {
                        $context = context_module::instance($cm->id);
                    }

                    // Prepare email content.
                    $details = new stdClass();
                    $details->user = fullname($grade);
                    $details->checklist = s($checklist->name);
                    $details->coursename = $course->fullname;

                    if ($checklist->emailoncomplete == CHECKLIST_EMAIL_TEACHER
                        || $checklist->emailoncomplete == CHECKLIST_EMAIL_BOTH
                    ) {
                        // Email will be sent to the all teachers who have capability.
                        $subj = get_string('emailoncompletesubject', 'checklist', $details);
                        $content = get_string('emailoncompletebody', 'checklist', $details);
                        $content .= new moodle_url('/mod/checklist/view.php', array('id' => $cm->id));

                        if ($recipients = get_users_by_capability($context, 'mod/checklist:emailoncomplete', 'u.*')) {
                            foreach ($recipients as $recipient) {
                                email_to_user($recipient, $grade, $subj, $content, '', '', '', false);
                            }
                        }
                    }
                    if ($checklist->emailoncomplete == CHECKLIST_EMAIL_STUDENT
                        || $checklist->emailoncomplete == CHECKLIST_EMAIL_BOTH
                    ) {
                        // Email will be sent to the student who completes this checklist.
                        $subj = get_string('emailoncompletesubjectown', 'checklist', $details);
                        $content = get_string('emailoncompletebodyown', 'checklist', $details);
                        $content .= new moodle_url('/mod/checklist/view.php', array('id' => $cm->id));

                        $recipientstudent = $DB->get_record('user', array('id' => $grade->userid));
                        email_to_user($recipientstudent, $grade, $subj, $content, '', '', '', false);
                    }
                }
            }
            $params = array(
                'contextid' => $context->id,
                'objectid' => $checklist->id,
                'userid' => $grade->userid,
            );
            $event = \mod_checklist\event\checklist_completed::create($params);
            $event->trigger();
        }
        $ci = new completion_info($course);
        if ($cm->completion == COMPLETION_TRACKING_AUTOMATIC) {
            $ci->update_state($cm, COMPLETION_UNKNOWN, $grade->userid);
        }
    }

    checklist_grade_item_update($checklist, $grades);
}

/**
 * Make sure multiple completion emails are not sent from the same user within the last hour.
 * (Assuming they don't log out and log back in again).
 *
 * @param int $cmid
 * @return bool - true if an email has already been sent recently
 */
function checklist_sent_email_recently($cmid) {
    global $SESSION;
    if (!isset($SESSION->checklist_recent_email)) {
        $SESSION->checklist_recent_email = array();
    }
    if (!empty($SESSION->checklist_recent_email[$cmid])) {
        $nexttime = $SESSION->checklist_recent_email[$cmid] + HOURSECS;
        if (time() < $nexttime) {
            return true;
        }
    }
    $SESSION->checklist_recent_email[$cmid] = time();
    return false;
}

/**
 * @param $checklist
 * @return int
 */
function checklist_grade_item_delete($checklist) {
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');
    if (!isset($checklist->courseid)) {
        $checklist->courseid = $checklist->course;
    }

    return grade_update('mod/checklist', $checklist->courseid, 'mod', 'checklist', $checklist->id, 0, null, array('deleted' => 1));
}

/**
 * @param $checklist
 * @param null $grades
 * @return int
 */
function checklist_grade_item_update($checklist, $grades = null) {
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');

    if (!isset($checklist->courseid)) {
        $checklist->courseid = $checklist->course;
    }

    $params = array('itemname' => $checklist->name);
    if ($checklist->maxgrade > 0) {
        $params['gradetype'] = GRADE_TYPE_VALUE;
        $params['grademax'] = $checklist->maxgrade;
        $params['grademin'] = 0;
    } else {
        $params['gradetype'] = GRADE_TYPE_NONE;
    }

    if ($grades === 'reset') {
        $params['reset'] = true;
        $grades = null;
    }

    return grade_update('mod/checklist', $checklist->courseid, 'mod', 'checklist', $checklist->id, 0, $grades, $params);
}

/**
 * Return a small object with summary information about what a
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 * $return->time = the time they did it
 * $return->info = a short text description
 *
 * @param $course
 * @param $user
 * @param $mod
 * @param $checklist
 * @return null
 */
function checklist_user_outline($course, $user, $mod, $checklist) {
    global $DB;

    // Handle groupings.
    $groupingsql = checklist_class::get_grouping_sql($user->id, $checklist->course);

    $sel = 'checklist = ? AND userid = 0 AND itemoptional = '.CHECKLIST_OPTIONAL_NO;
    $sel .= ' AND hidden = '.CHECKLIST_HIDDEN_NO." AND $groupingsql";
    $items = $DB->get_records_select('checklist_item', $sel, array($checklist->id), '', 'id');
    if (!$items) {
        return null;
    }

    $total = count($items);
    list($isql, $iparams) = $DB->get_in_or_equal(array_keys($items));

    $sql = "userid = ? AND item $isql AND ";
    if ($checklist->teacheredit == CHECKLIST_MARKING_STUDENT) {
        $sql .= 'usertimestamp > 0';
        $order = 'usertimestamp DESC';
    } else {
        $sql .= 'teachermark = '.CHECKLIST_TEACHERMARK_YES;
        $order = 'teachertimestamp DESC';
    }
    $params = array_merge(array($user->id), $iparams);

    $checks = $DB->get_records_select('checklist_check', $sql, $params, $order);

    $return = null;
    if ($checks) {
        $return = new stdClass;

        $ticked = count($checks);
        $check = reset($checks);
        if ($checklist->teacheredit == CHECKLIST_MARKING_STUDENT) {
            $return->time = $check->usertimestamp;
        } else {
            $return->time = $check->teachertimestamp;
        }
        $percent = sprintf('%0d', ($ticked * 100) / $total);
        $return->info = get_string('progress', 'checklist').': '.$ticked.'/'.$total.' ('.$percent.'%)';
    }

    return $return;
}

/**
 * Print a detailed representation of what a user has done with
 * a given particular instance of this module, for user activity reports.
 *
 * @param $course
 * @param $user
 * @param $mod
 * @param $checklist
 * @return boolean
 */
function checklist_user_complete($course, $user, $mod, $checklist) {
    $chk = new checklist_class($mod->id, $user->id, $checklist, $mod, $course);

    $chk->user_complete();

    return true;
}

/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in checklist activities and print it out.
 * Return true if there was output, or false is there was none.
 *
 * @param $course
 * @param $isteacher
 * @param $timestart
 * @return boolean
 */
function checklist_print_recent_activity($course, $isteacher, $timestart) {
    return false;  // True if anything was printed, otherwise false.
}

/**
 * @param $courses
 * @param $htmlarray
 */
function checklist_print_overview($courses, &$htmlarray) {
    global $USER, $CFG, $DB;

    $config = get_config('checklist');
    if (isset($config->showmymoodle) && !$config->showmymoodle) {
        return; // Disabled via global config.
    }
    if (!isset($config->showcompletemymoodle)) {
        $config->showcompletemymoodle = 1;
    }
    if (!isset($config->showupdateablemymoodle)) {
        $config->showupdateablemymoodle = 1;
    }
    if (empty($courses) || !is_array($courses) || count($courses) == 0) {
        return;
    }

    if (!$checklists = get_all_instances_in_courses('checklist', $courses)) {
        return;
    }

    $strchecklist = get_string('modulename', 'checklist');

    foreach ($checklists as $checklist) {
        $showall = true;
        $context = context_module::instance($checklist->coursemodule);

        // If only the student is responsible for updating the checklist.
        if ($checklist->teacheredit == CHECKLIST_MARKING_STUDENT) {
            if ($showall = !has_capability('mod/checklist:updateown', $context, null, false)) {
                if ($config->showupdateablemymoodle) {
                    continue;
                }
            }
        } else { // If the teacher is involved with updating the checklist.
            if ($config->showupdateablemymoodle) {
                continue;
            }
        }

        $progressbar = checklist_class::print_user_progressbar($checklist->id, $USER->id,
                                                               '270px', true, true,
                                                               !$config->showcompletemymoodle);
        if (empty($progressbar)) {
            continue;
        }

        if ($showall) { // Show all items whether or not they are checked off (as this user is unable to check them off).
            $groupingsql = checklist_class::get_grouping_sql($USER->id, $checklist->course);
            $dateitems = $DB->get_records_select('checklist_item',
                                                 "checklist = ? AND duetime > 0 AND $groupingsql",
                                                 array($checklist->id),
                                                 'duetime');
        } else { // Show only items that have not been checked off.
            $groupingsql = checklist_class::get_grouping_sql($USER->id, $checklist->course, 'i.');
            $dateitems = $DB->get_records_sql("SELECT i.* FROM {checklist_item} i
                                                 JOIN {checklist_check} c ON c.item = i.id
                                                WHERE i.checklist = ? AND i.duetime > 0 AND c.userid = ? AND usertimestamp = 0
                                                  AND $groupingsql
                                                ORDER BY i.duetime", array($checklist->id, $USER->id));
        }

        $str = '<div class="checklist overview"><div class="name">'.$strchecklist.': '.
            '<a title="'.$strchecklist.'" href="'.$CFG->wwwroot.'/mod/checklist/view.php?id='.$checklist->coursemodule.'">'.
            $checklist->name.'</a></div>';
        $str .= '<div class="info">'.$progressbar.'</div>';
        foreach ($dateitems as $item) {
            $str .= '<div class="info">'.format_string($item->displaytext).': ';
            if ($item->duetime > time()) {
                $str .= '<span class="itemdue">';
            } else {
                $str .= '<span class="itemoverdue">';
            }
            $str .= date('j M Y', $item->duetime).'</span></div>';
        }
        $str .= '</div>';
        if (empty($htmlarray[$checklist->course]['checklist'])) {
            $htmlarray[$checklist->course]['checklist'] = $str;
        } else {
            $htmlarray[$checklist->course]['checklist'] .= $str;
        }
    }
}

/**
 * Must return an array of user records (all data) who are participants
 * for a given instance of newmodule. Must include every user involved
 * in the instance, independient of his role (student, teacher, admin...)
 * See other modules as example.
 *
 * @param int $checklistid ID of an instance of this module
 * @return mixed boolean/array of students
 */
function checklist_get_participants($checklistid) {
    global $DB;

    $params = array($checklistid);
    $sql = 'SELECT DISTINCT u.id
              FROM {user} u
              JOIN {checklist_item} i ON i.userid = u.id
             WHERE i.checklist = ?';
    $userids1 = $DB->get_records_sql($sql, $params);

    $sql = 'SELECT DISTINCT u.id
              FROM {user} u
              JOIN {checklist_check} c ON c.userid = u.id
              JOIN {checklist_item} i ON i.id = c.item
             WHERE i.checklist = ?';
    $userids2 = $DB->get_records_sql($sql, $params);

    return $userids1 + $userids2;
}

/**
 * This function returns if a scale is being used by one checklist
 * if it has support for grading and scales. Commented code should be
 * modified if necessary. See forum, glossary or journal modules
 * as reference.
 *
 * @param int $checklistid ID of an instance of this module
 * @param int $scaleid
 * @return bool
 */
function checklist_scale_used($checklistid, $scaleid) {
    return false;
}

/**
 * Checks if scale is being used by any instance of checklist.
 * This function was added in 1.9
 *
 * This is used to find out if scale used anywhere
 * @param $scaleid int
 * @return boolean True if the scale is used by any checklist
 */
function checklist_scale_used_anywhere($scaleid) {
    return false;
}

/**
 * Execute post-install custom actions for the module
 * This function was added in 1.9
 *
 * @return boolean true if success, false on error
 */
function checklist_install() {
    return true;
}

/**
 * Execute post-uninstall custom actions for the module
 * This function was added in 1.9
 *
 * @return boolean true if success, false on error
 */
function checklist_uninstall() {
    return true;
}

/**
 * @param HTML_QuickForm $mform
 */
function checklist_reset_course_form_definition(&$mform) {
    $mform->addElement('header', 'checklistheader', get_string('modulenameplural', 'checklist'));
    $mform->addElement('checkbox', 'reset_checklist_progress', get_string('resetchecklistprogress', 'checklist'));
}

/**
 * @param object $course
 * @return array
 */
function checklist_reset_course_form_defaults($course) {
    return array('reset_checklist_progress' => 1);
}

/**
 * @param object $data
 * @return array
 */
function checklist_reset_userdata($data) {
    global $DB;

    $status = array();
    $component = get_string('modulenameplural', 'checklist');
    $typestr = get_string('resetchecklistprogress', 'checklist');
    $status[] = array('component' => $component, 'item' => $typestr, 'error' => false);

    if (!empty($data->reset_checklist_progress)) {
        $checklists = $DB->get_records('checklist', array('course' => $data->courseid));
        if (!$checklists) {
            return $status;
        }

        list($csql, $cparams) = $DB->get_in_or_equal(array_keys($checklists));
        $items = $DB->get_records_select('checklist_item', 'checklist '.$csql, $cparams);
        if (!$items) {
            return $status;
        }

        $itemids = array_keys($items);
        $DB->delete_records_list('checklist_check', 'item', $itemids);
        $DB->delete_records_list('checklist_comment', 'itemid', $itemids);

        $sql = "checklist $csql AND userid <> 0";
        $DB->delete_records_select('checklist_item', $sql, $cparams);

        // Reset the grades.
        foreach ($checklists as $checklist) {
            checklist_grade_item_update($checklist, 'reset');
        }
    }

    return $status;
}

/**
 * @param int $courseid
 * @return bool
 */
function checklist_refresh_events($courseid = 0) {
    global $DB;

    if ($courseid) {
        $checklists = $DB->get_records('checklist', array('course' => $courseid));
        $course = $DB->get_record('course', array('id' => $courseid));
    } else {
        $checklists = $DB->get_records('checklist');
        $course = null;
    }

    foreach ($checklists as $checklist) {
        if ($checklist->duedatesoncalendar) {
            $cm = get_coursemodule_from_instance('checklist', $checklist->id, $checklist->course);
            $chk = new checklist_class($cm->id, 0, $checklist, $cm, $course);
            $chk->setallevents();
        }
    }

    return true;
}

/**
 * @param string $feature
 * @return bool|null
 */
function checklist_supports($feature) {
    global $CFG;
    if (!defined('FEATURE_SHOW_DESCRIPTION')) {
        // For backwards compatibility.
        define('FEATURE_SHOW_DESCRIPTION', 'showdescription');
    }

    if ((int)$CFG->branch < 28) {
        if ($feature === FEATURE_GROUPMEMBERSONLY) {
            return true;
        }
    }

    switch ($feature) {
        case FEATURE_GROUPS:
            return true;
        case FEATURE_GROUPINGS:
            return true;
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_GRADE_HAS_GRADE:
            return true;
        case FEATURE_COMPLETION_HAS_RULES:
            return true;
        case FEATURE_BACKUP_MOODLE2:
            return true;
        case FEATURE_SHOW_DESCRIPTION:
            return true;

        default:
            return null;
    }
}

/**
 * @param object $course
 * @param object $cm
 * @param int $userid
 * @param int $type
 * @return bool
 */
function checklist_get_completion_state($course, $cm, $userid, $type) {
    global $DB;

    if (!($checklist = $DB->get_record('checklist', array('id' => $cm->instance)))) {
        throw new Exception("Can't find checklist {$cm->instance}");
    }

    $result = $type; // Default return value.

    if ($checklist->completionpercent) {
        list($ticked, $total) = checklist_class::get_user_progress($cm->instance, $userid);
        $value = $checklist->completionpercent <= ($ticked * 100 / $total);
        if ($type == COMPLETION_AND) {
            $result = $result && $value;
        } else {
            $result = $result || $value;
        }
    }

    return $result;
}

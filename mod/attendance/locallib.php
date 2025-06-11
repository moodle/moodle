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
 * local functions and constants for module attendance
 *
 * @package   mod_attendance
 * @copyright  2011 Artem Andreev <andreev.artem@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/gradelib.php');
require_once(dirname(__FILE__).'/renderhelpers.php');

define('ATT_VIEW_DAYS', 1);
define('ATT_VIEW_WEEKS', 2);
define('ATT_VIEW_MONTHS', 3);
define('ATT_VIEW_ALLPAST', 4);
define('ATT_VIEW_ALL', 5);
define('ATT_VIEW_NOTPRESENT', 6);
define('ATT_VIEW_SUMMARY', 7);

define('ATT_SORT_DEFAULT', 0);
define('ATT_SORT_LASTNAME', 1);
define('ATT_SORT_FIRSTNAME', 2);

define('ATTENDANCE_AUTOMARK_DISABLED', 0);
define('ATTENDANCE_AUTOMARK_ALL', 1);
define('ATTENDANCE_AUTOMARK_CLOSE', 2);
define('ATTENDANCE_AUTOMARK_ACTIVITYCOMPLETION', 3);


define('ATTENDANCE_SHAREDIP_DISABLED', 0);
define('ATTENDANCE_SHAREDIP_MINUTES', 1);
define('ATTENDANCE_SHAREDIP_FORCE', 2);

// Max number of sessions available in the warnings set form to trigger warnings.
define('ATTENDANCE_MAXWARNAFTER', 100);

/**
 * Get statuses,
 *
 * @param int $attid
 * @param bool $onlyvisible
 * @param int $statusset
 * @return array
 */
function attendance_get_statuses($attid, $onlyvisible=true, $statusset = -1) {
    global $DB;

    // Set selector.
    $params = ['aid' => $attid];
    $setsql = '';
    if ($statusset >= 0) {
        $params['statusset'] = $statusset;
        $setsql = ' AND setnumber = :statusset ';
    }

    if ($onlyvisible) {
        $statuses = $DB->get_records_select('attendance_statuses', "attendanceid = :aid AND visible = 1 AND deleted = 0 $setsql",
                                            $params, 'setnumber ASC, grade DESC');
    } else {
        $statuses = $DB->get_records_select('attendance_statuses', "attendanceid = :aid AND deleted = 0 $setsql",
                                            $params, 'setnumber ASC, grade DESC');
    }

    return $statuses;
}

/**
 * Get the name of the status set.
 *
 * @param int $attid
 * @param int $statusset
 * @param bool $includevalues
 * @return string
 */
function attendance_get_setname($attid, $statusset, $includevalues = true) {
    $statusname = get_string('statusset', 'mod_attendance', $statusset + 1);
    if ($includevalues) {
        $statuses = attendance_get_statuses($attid, true, $statusset);
        $statusesout = [];
        foreach ($statuses as $status) {
            $statusesout[] = $status->acronym;
        }
        if ($statusesout) {
            if (count($statusesout) > 6) {
                $statusesout = array_slice($statusesout, 0, 6);
                $statusesout[] = '...';
            }
            $statusesout = implode(' ', $statusesout);
            $statusname .= ' ('.$statusesout.')';
        }
    }

    return $statusname;
}

/**
 * Get full filtered log.
 * @param int $userid
 * @param stdClass $pageparams
 * @return array
 */
function attendance_get_user_sessions_log_full($userid, $pageparams) {
    global $DB;
    // All taken sessions (including previous groups).

    $usercourses = enrol_get_users_courses($userid);
    list($usql, $uparams) = $DB->get_in_or_equal(array_keys($usercourses), SQL_PARAMS_NAMED, 'cid0');

    $coursesql = "(1 = 1)";
    $courseparams = [];
    $now = time();
    if ($pageparams->sesscourses === 'current') {
        $coursesql = "(c.startdate = 0 OR c.startdate <= :now1) AND (c.enddate = 0 OR c.enddate >= :now2)";
        $courseparams = [
            'now1' => $now,
            'now2' => $now,
        ];
    }

    $datesql = "(1 = 1)";
    $dateparams = [];
    if ($pageparams->startdate && $pageparams->enddate) {
        $datesql = "ats.sessdate >= :sdate AND ats.sessdate < :edate";
        $dateparams = [
            'sdate'     => $pageparams->startdate,
            'edate'     => $pageparams->enddate,
        ];
    }

    if ($pageparams->groupby === 'date') {
        $ordersql = "ats.sessdate ASC, c.fullname ASC, att.name ASC, att.id ASC";
    } else {
        $ordersql = "c.fullname ASC, att.name ASC, att.id ASC, ats.sessdate ASC";
    }

    // WHERE clause is important:
    // gm.userid not null => get unmarked attendances for user's current groups
    // ats.groupid 0 => get all sessions that are for all students enrolled in course
    // al.id not null => get all marked sessions whether or not user currently still in group.
    $sql = "SELECT ats.id, ats.groupid, ats.sessdate, ats.duration, ats.description, ats.statusset,
                   al.statusid, al.remarks, ats.studentscanmark, ats.allowupdatestatus, ats.autoassignstatus,
                   ats.preventsharedip, ats.preventsharediptime, ats.studentsearlyopentime,
                   ats.attendanceid, att.name AS attname, att.course AS courseid, c.fullname AS cname
              FROM {attendance_sessions} ats
              JOIN {attendance} att
                ON att.id = ats.attendanceid
              JOIN {course} c
                ON att.course = c.id
         LEFT JOIN {attendance_log} al
                ON ats.id = al.sessionid AND al.studentid = :uid
         LEFT JOIN {groups_members} gm
                ON (ats.groupid = gm.groupid AND gm.userid = :uid1)
             WHERE (gm.userid IS NOT NULL OR ats.groupid = 0 OR al.id IS NOT NULL)
               AND att.course $usql
               AND $datesql
               AND $coursesql
          ORDER BY $ordersql";

    $params = [
        'uid'       => $userid,
        'uid1'      => $userid,
    ];
    $params = array_merge($params, $uparams);
    $params = array_merge($params, $dateparams);
    $params = array_merge($params, $courseparams);
    $sessions = $DB->get_records_sql($sql, $params);

    foreach ($sessions as $sess) {
        if (empty($sess->description)) {
            $sess->description = get_string('nodescription', 'attendance');
        } else {
            $modinfo = get_fast_modinfo($sess->courseid);
            $cmid = $modinfo->instances['attendance'][$sess->attendanceid]->get_course_module_record()->id;
            $ctx = context_module::instance($cmid);
            $sess->description = format_text(file_rewrite_pluginfile_urls($sess->description,
            'pluginfile.php', $ctx->id, 'mod_attendance', 'session', $sess->id));
        }
    }

    return $sessions;
}

/**
 * Get users courses and the relevant attendances.
 *
 * @param int $userid
 * @return array
 */
function attendance_get_user_courses_attendances($userid) {
    global $DB;

    $usercourses = enrol_get_users_courses($userid);

    list($usql, $uparams) = $DB->get_in_or_equal(array_keys($usercourses), SQL_PARAMS_NAMED, 'cid0');

    $sql = "SELECT att.id as attid, att.course as courseid, course.fullname as coursefullname,
                   course.startdate as coursestartdate, att.name as attname, att.grade as attgrade
              FROM {attendance} att
              JOIN {course} course
                   ON att.course = course.id
             WHERE att.course $usql
          ORDER BY coursefullname ASC, attname ASC";

    $params = array_merge($uparams, ['uid' => $userid]);

    return $DB->get_records_sql($sql, $params);
}

/**
 * Used to calculate a fraction based on the part and total values
 *
 * @param float $part - part of the total value
 * @param float $total - total value.
 * @return float the calculated fraction.
 */
function attendance_calc_fraction($part, $total) {
    if ($total == 0) {
        return 0;
    } else {
        return $part / $total;
    }
}

/**
 * Check to see if statusid in use to help prevent deletion etc.
 *
 * @param integer $statusid
 */
function attendance_has_logs_for_status($statusid) {
    global $DB;
    return $DB->record_exists('attendance_log', ['statusid' => $statusid]);
}

/**
 * Helper function to add sessiondate_selector to add/update forms.
 *
 * @param MoodleQuickForm $mform
 */
function attendance_form_sessiondate_selector (MoodleQuickForm $mform) {

    $mform->addElement('date_selector', 'sessiondate', get_string('sessiondate', 'attendance'));

    for ($i = 0; $i <= 23; $i++) {
        $hours[$i] = sprintf("%02d", $i);
    }
    for ($i = 0; $i < 60; $i++) {
        $minutes[$i] = sprintf("%02d", $i);
    }

    $sesendtime = [];
    if (!right_to_left()) {
        $sesendtime[] =& $mform->createElement('static', 'from', '', get_string('from', 'attendance'));
        $sesendtime[] =& $mform->createElement('select', 'starthour', get_string('hour', 'form'), $hours, false, true);
        $sesendtime[] =& $mform->createElement('select', 'startminute', get_string('minute', 'form'), $minutes, false, true);
        $sesendtime[] =& $mform->createElement('static', 'to', '', get_string('to', 'attendance'));
        $sesendtime[] =& $mform->createElement('select', 'endhour', get_string('hour', 'form'), $hours, false, true);
        $sesendtime[] =& $mform->createElement('select', 'endminute', get_string('minute', 'form'), $minutes, false, true);
    } else {
        $sesendtime[] =& $mform->createElement('static', 'from', '', get_string('from', 'attendance'));
        $sesendtime[] =& $mform->createElement('select', 'startminute', get_string('minute', 'form'), $minutes, false, true);
        $sesendtime[] =& $mform->createElement('select', 'starthour', get_string('hour', 'form'), $hours, false, true);
        $sesendtime[] =& $mform->createElement('static', 'to', '', get_string('to', 'attendance'));
        $sesendtime[] =& $mform->createElement('select', 'endminute', get_string('minute', 'form'), $minutes, false, true);
        $sesendtime[] =& $mform->createElement('select', 'endhour', get_string('hour', 'form'), $hours, false, true);
    }
    $mform->addGroup($sesendtime, 'sestime', get_string('time', 'attendance'), [' '], true);
}

/**
 * Count the number of status sets that exist for this instance.
 *
 * @param int $attendanceid
 * @return int
 */
function attendance_get_max_statusset($attendanceid) {
    global $DB;

    $max = $DB->get_field_sql('SELECT MAX(setnumber) FROM {attendance_statuses} WHERE attendanceid = ? AND deleted = 0',
        [$attendanceid]);
    if ($max) {
        return $max;
    }
    return 0;
}

/**
 * Returns the maxpoints for each statusset
 *
 * @param array $statuses
 * @return array
 */
function attendance_get_statusset_maxpoints($statuses) {
    $statussetmaxpoints = [];
    foreach ($statuses as $st) {
        if (!isset($statussetmaxpoints[$st->setnumber])) {
            $statussetmaxpoints[$st->setnumber] = $st->grade;
        }
    }
    return $statussetmaxpoints;
}

/**
 * Update user grades
 *
 * @param mod_attendance_structure|stdClass $attendance
 * @param array $userids
 */
function attendance_update_users_grade($attendance, $userids=[]) {
    global $DB;

    if (empty($attendance->grade)) {
        return false;
    }

    list($course, $cm) = get_course_and_cm_from_instance($attendance->id, 'attendance');

    $summary = new mod_attendance_summary($attendance->id, $userids);

    if (empty($userids)) {
        $context = context_module::instance($cm->id);
        $userids = array_keys(get_enrolled_users($context, 'mod/attendance:canbelisted', 0, 'u.id'));
    }

    if ($attendance->grade < 0) {
        $dbparams = ['id' => -($attendance->grade)];
        $scale = $DB->get_record('scale', $dbparams);
        $scalearray = explode(',', $scale->scale);
        $attendancegrade = count($scalearray);
    } else {
        $attendancegrade = $attendance->grade;
    }

    $grades = [];
    foreach ($userids as $userid) {
        $grades[$userid] = new stdClass();
        $grades[$userid]->userid = $userid;

        if ($summary->has_taken_sessions($userid)) {
            $usersummary = $summary->get_taken_sessions_summary_for($userid);
            $grades[$userid]->rawgrade = $usersummary->takensessionspercentage * $attendancegrade;
        } else {
            $grades[$userid]->rawgrade = null;
        }
    }

    return grade_update('mod/attendance', $course->id, 'mod', 'attendance', $attendance->id, 0, $grades);
}

/**
 * Update grades for specified users for specified attendance
 *
 * @param integer $attendanceid - the id of the attendance to update
 * @param integer $grade - the value of the 'grade' property of the specified attendance
 * @param array $userids - the userids of the users to be updated
 */
function attendance_update_users_grades_by_id($attendanceid, $grade, $userids) {
    global $DB;

    if (empty($grade)) {
        return false;
    }

    list($course, $cm) = get_course_and_cm_from_instance($attendanceid, 'attendance');

    $summary = new mod_attendance_summary($attendanceid, $userids);

    if (empty($userids)) {
        $context = context_module::instance($cm->id);
        $userids = array_keys(get_enrolled_users($context, 'mod/attendance:canbelisted', 0, 'u.id'));
    }

    if ($grade < 0) {
        $dbparams = ['id' => -($grade)];
        $scale = $DB->get_record('scale', $dbparams);
        $scalearray = explode(',', $scale->scale);
        $attendancegrade = count($scalearray);
    } else {
        $attendancegrade = $grade;
    }

    $grades = [];
    foreach ($userids as $userid) {
        $grades[$userid] = new stdClass();
        $grades[$userid]->userid = $userid;

        if ($summary->has_taken_sessions($userid)) {
            $usersummary = $summary->get_taken_sessions_summary_for($userid);
            $grades[$userid]->rawgrade = $usersummary->takensessionspercentage * $attendancegrade;
        } else {
            $grades[$userid]->rawgrade = null;
        }
    }

    return grade_update('mod/attendance', $course->id, 'mod', 'attendance', $attendanceid, 0, $grades);
}

/**
 * Add an attendance status variable
 *
 * @param stdClass $status
 * @return bool
 */
function attendance_add_status($status) {
    global $DB;
    if (empty($status->context)) {
        $status->context = context_system::instance();
    }

    if (!empty($status->acronym) && !empty($status->description)) {
        $status->deleted = 0;
        $status->visible = 1;
        $status->setunmarked = 0;
        $status->availablebeforesession = 0;

        $id = $DB->insert_record('attendance_statuses', $status);
        $status->id = $id;

        $event = \mod_attendance\event\status_added::create([
            'objectid' => $status->attendanceid,
            'context' => $status->context,
            'other' => ['acronym' => $status->acronym,
                             'description' => $status->description,
                             'grade' => $status->grade, ]]);
        if (!empty($status->cm)) {
            $event->add_record_snapshot('course_modules', $status->cm);
        }
        $event->add_record_snapshot('attendance_statuses', $status);
        $event->trigger();
        return true;
    } else {
        return false;
    }
}

/**
 * Remove a status variable from an attendance instance
 *
 * @param stdClass $status
 * @param stdClass $context
 * @param stdClass $cm
 */
function attendance_remove_status($status, $context = null, $cm = null) {
    global $DB;
    if (empty($context)) {
        $context = context_system::instance();
    }
    $DB->set_field('attendance_statuses', 'deleted', 1, ['id' => $status->id]);
    $event = \mod_attendance\event\status_removed::create([
        'objectid' => $status->id,
        'context' => $context,
        'other' => [
            'acronym' => $status->acronym,
            'description' => $status->description,
        ]]);
    if (!empty($cm)) {
        $event->add_record_snapshot('course_modules', $cm);
    }
    $event->add_record_snapshot('attendance_statuses', $status);
    $event->trigger();
}

/**
 * Update status variable for a particular Attendance module instance
 *
 * @param stdClass $status
 * @param string $acronym
 * @param string $description
 * @param int $grade
 * @param bool $visible
 * @param stdClass $context
 * @param stdClass $cm
 * @param int $studentavailability
 * @param bool $availablebeforesession
 * @param bool $setunmarked
 * @return array
 */
function attendance_update_status($status, $acronym, $description, $grade, $visible,
                                  $context = null, $cm = null, $studentavailability = null,
                                  $availablebeforesession = false, $setunmarked = false) {
    global $DB;

    if (empty($context)) {
        $context = context_system::instance();
    }

    if (isset($visible)) {
        $status->visible = $visible;
        $updated[] = $visible ? get_string('show') : get_string('hide');
    } else if (empty($acronym) || empty($description)) {
        return ['acronym' => $acronym, 'description' => $description];
    }

    $updated = [];

    if ($acronym) {
        $status->acronym = $acronym;
        $updated[] = $acronym;
    }
    if ($description) {
        $status->description = $description;
        $updated[] = $description;
    }
    if (isset($grade)) {
        $status->grade = $grade;
        $updated[] = $grade;
    }
    if (isset($studentavailability)) {
        if (empty($studentavailability)) {
            if ($studentavailability !== '0') {
                $studentavailability = null;
            }
        }

        $status->studentavailability = $studentavailability;
        $updated[] = $studentavailability;
    }
    if (strpos(strval($availablebeforesession), 'on') === false) {
        $status->availablebeforesession = 0;
    } else {
        $status->availablebeforesession = 1;
    }
    if ($setunmarked) {
        $status->setunmarked = 1;
    } else {
        $status->setunmarked = 0;
    }
    $DB->update_record('attendance_statuses', $status);

    $event = \mod_attendance\event\status_updated::create([
        'objectid' => $status->attendanceid,
        'context' => $context,
        'other' => ['acronym' => $acronym, 'description' => $description, 'grade' => $grade,
            'updated' => implode(' ', $updated), ]]);
    if (!empty($cm)) {
        $event->add_record_snapshot('course_modules', $cm);
    }
    $event->add_record_snapshot('attendance_statuses', $status);
    $event->trigger();
}

/**
 * Similar to core random_string function but only lowercase letters.
 * designed to make it relatively easy to provide a simple password in class.
 *
 * @param int $length The length of the string to be created.
 * @return string
 */
function attendance_random_string($length=6) {
    $randombytes = random_bytes($length);
    $pool = 'abcdefghijklmnopqrstuvwxyz';
    $pool .= '0123456789';
    $poollen = strlen($pool);
    $string = '';
    for ($i = 0; $i < $length; $i++) {
        $rand = ord($randombytes[$i]);
        $string .= substr($pool, ($rand % ($poollen)), 1);
    }
    return $string;
}

/**
 * This functions checks if this session is open for students.
 *
 * @param stdclass $sess the session record from attendance_sessions.
 * @return boolean
 */
function attendance_session_open_for_students($sess) {
    $sessionopens = empty($sess->studentsearlyopentime) ? $sess->sessdate : $sess->sessdate - $sess->studentsearlyopentime;
    if (time() > $sessionopens) {
        return true;
    } else {
        return false;
    }
}

/**
 * Is the allowupdatestatus setting enabled for this session?
 *
 * @param int $sessionid the id in attendance_sessions.
 * @return boolean
 */
function attendance_check_allow_update($sessionid) {
    global $DB;
    return $DB->record_exists('attendance_sessions', ['studentscanmark' => 1, 'allowupdatestatus' => 1, 'id' => $sessionid]);
}

/**
 * Check to see if this session have a status with availablebeforesession enabled.
 *
 * @param int $sessionid the id in attendance_sessions.
 * @param int $statusid - optionally pass the status id to see if it is availablebefore session.
 * @return boolean
 */
function attendance_is_status_availablebeforesession($sessionid, $statusid = null) {
    global $DB;
    $attendanceid = $DB->get_field('attendance_sessions', 'attendanceid', ['id' => $sessionid]);
    $params = ['deleted' => 0, 'visible' => 1, 'availablebeforesession' => 1,
               'attendanceid' => $attendanceid, ];

    if (!empty($statusid)) {
        $params['id'] = $statusid;
    }
    return $DB->record_exists('attendance_statuses', $params);
}

/**
 * Check to see if this session is open for student marking.
 *
 * @param stdclass $sess the session record from attendance_sessions.
 * @param boolean $log - if student cannot mark, generate log event.
 * @return array (boolean, string reason for failure)
 */
function attendance_can_student_mark($sess, $log = true) {
    global $DB, $USER, $OUTPUT;
    $canmark = false;
    $reason = 'closed';
    $attconfig = get_config('attendance');

    if (!empty($attconfig->studentscanmark) && !empty($sess->studentscanmark)) {
        if (empty($attconfig->studentscanmarksessiontime) ||
            (attendance_is_status_availablebeforesession($sess->id)) && time() < $sess->sessdate) {
            $canmark = true;
            $reason = '';
        } else {
            $duration = $sess->duration;
            if (empty($duration)) {
                $duration = $attconfig->studentscanmarksessiontimeend * 60;
            }
            if (!isset($sess->studentsearlyopentime)) {
                // Sanity check just in case not set in this session.
                $sess->studentsearlyopentime = 0;
            }
            if (($sess->sessdate - $sess->studentsearlyopentime) < time() && time() < ($sess->sessdate + $duration)) {
                $canmark = true;
                $reason = '';
            }
        }
    }
    // Check if another student has marked attendance from this IP address recently.
    if ($canmark && !empty($sess->preventsharedip)) {
        if ($sess->preventsharedip == ATTENDANCE_SHAREDIP_MINUTES) {
            $time = time() - ($sess->preventsharediptime * 60);
            $sql = 'sessionid = ? AND studentid <> ? AND timetaken > ? AND ipaddress = ?';
            $params = [$sess->id, $USER->id, $time, getremoteaddr()];
            $record = $DB->get_record_select('attendance_log', $sql, $params);
        } else {
            // Assume ATTENDANCE_SHAREDIP_FORCED.
            $sql = 'sessionid = ? AND studentid <> ? AND ipaddress = ?';
            $params = [$sess->id, $USER->id, getremoteaddr()];
            $record = $DB->get_record_select('attendance_log', $sql, $params);
        }

        if (!empty($record)) {
            $canmark = false;
            $reason = 'preventsharederror';
            if ($log) {
                // Trigger an ip_shared event.
                $attendanceid = $DB->get_field('attendance_sessions', 'attendanceid', ['id' => $record->sessionid]);
                $cm = get_coursemodule_from_instance('attendance', $attendanceid);
                $event = \mod_attendance\event\session_ip_shared::create([
                    'objectid' => 0,
                    'context' => \context_module::instance($cm->id),
                    'other' => [
                        'sessionid' => $record->sessionid,
                        'otheruser' => $record->studentid,
                    ],
                ]);

                $event->trigger();
            }
        }
    }
    return [$canmark, $reason];
}

/**
 * Generate worksheet for Attendance export
 *
 * @param stdclass $data The data for the report
 * @param string $filename The name of the file
 * @param string $format excel|ods
 *
 */
function attendance_exporttotableed($data, $filename, $format) {
    global $CFG;

    if ($format === 'excel') {
        require_once("$CFG->libdir/excellib.class.php");
        $filename .= ".xls";
        $workbook = new MoodleExcelWorkbook("-");
    } else {
        require_once("$CFG->libdir/odslib.class.php");
        $filename .= ".ods";
        $workbook = new MoodleODSWorkbook("-");
    }
    // Sending HTTP headers.
    $workbook->send($filename);
    // Creating the first worksheet.
    $myxls = $workbook->add_worksheet(get_string('modulenameplural', 'attendance'));
    // Format types.
    $formatbc = $workbook->add_format();
    $formatbc->set_bold(1);

    $myxls->write(0, 0, get_string('course'), $formatbc);
    $myxls->write(0, 1, $data->course);
    $myxls->write(1, 0, get_string('group'), $formatbc);
    $myxls->write(1, 1, $data->group);

    $i = 3;
    $j = 0;
    foreach ($data->tabhead as $cell) {
        // Merge cells if the heading would be empty (remarks column).
        if (empty($cell)) {
            $myxls->merge_cells($i, $j - 1, $i, $j);
        } else {
            $myxls->write($i, $j, $cell, $formatbc);
        }
        $j++;
    }
    $i++;
    $j = 0;
    foreach ($data->table as $row) {
        foreach ($row as $cell) {
            $myxls->write($i, $j++, $cell);
        }
        $i++;
        $j = 0;
    }
    $workbook->close();
}

/**
 * Generate csv for Attendance export
 *
 * @param stdclass $data The data for the report
 * @param string $filename The name of the file
 *
 */
function attendance_exporttocsv($data, $filename) {
    $filename .= ".txt";

    header("Content-Type: application/download\n");
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header("Expires: 0");
    header("Cache-Control: must-revalidate,post-check=0,pre-check=0");
    header("Pragma: public");

    echo get_string('course')."\t".$data->course."\n";
    echo get_string('group')."\t".$data->group."\n\n";

    echo implode("\t", $data->tabhead)."\n";
    foreach ($data->table as $row) {
        echo implode("\t", $row)."\n";
    }
}

/**
 * Get session data for form.
 * @param stdClass $formdata moodleform - attendance form.
 * @param mod_attendance_structure $att - used to get attendance level subnet.
 * @return array.
 */
function attendance_construct_sessions_data_for_add($formdata, mod_attendance_structure $att) {
    global $CFG;

    $sesstarttime = $formdata->sestime['starthour'] * HOURSECS + $formdata->sestime['startminute'] * MINSECS;
    $sesendtime = $formdata->sestime['endhour'] * HOURSECS + $formdata->sestime['endminute'] * MINSECS;
    $sessiondate = $formdata->sessiondate + $sesstarttime;
    $duration = $sesendtime - $sesstarttime;
    if (empty(get_config('attendance', 'enablewarnings'))) {
        $absenteereport = get_config('attendance', 'absenteereport_default');
    } else {
        $absenteereport = empty($formdata->absenteereport) ? 0 : 1;
    }

    $now = time();

    if (empty(get_config('attendance', 'studentscanmark'))) {
        $formdata->studentscanmark = 0;
    }

    if (empty(get_config('attendance', 'allowupdatestatus'))) {
        $formdata->allowupdatestatus = 0;
    }

    $calendarevent = 0;
    if (isset($formdata->calendarevent)) { // Calendar event should be created.
        $calendarevent = 1;
    }

    $sessions = [];
    if (isset($formdata->addmultiply)) {
        $startdate = $sessiondate;
        $enddate = $formdata->sessionenddate + DAYSECS; // Because enddate in 0:0am.

        if ($enddate < $startdate) {
            return null;
        }

        // Getting first day of week.
        $sdate = $startdate;
        $dinfo = usergetdate($sdate);
        if ($CFG->calendar_startwday === '0') { // Week start from sunday.
            $startweek = $startdate - $dinfo['wday'] * DAYSECS; // Call new variable.
        } else {
            $wday = $dinfo['wday'] === 0 ? 7 : $dinfo['wday'];
            $startweek = $startdate - ($wday - 1) * DAYSECS;
        }

        $wdaydesc = [0 => 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

        while ($sdate < $enddate) {
            if ($sdate < strtotime('+1 week', $startweek)) {
                $dinfo = usergetdate($sdate);
                if (isset($formdata->sdays) && array_key_exists($wdaydesc[$dinfo['wday']], $formdata->sdays)) {
                    $sess = new stdClass();
                    $sess->sessdate = make_timestamp($dinfo['year'], $dinfo['mon'], $dinfo['mday'],
                        $formdata->sestime['starthour'], $formdata->sestime['startminute']);
                    $sess->duration = $duration;
                    $sess->descriptionitemid = $formdata->sdescription['itemid'];
                    $sess->description = $formdata->sdescription['text'];
                    $sess->descriptionformat = $formdata->sdescription['format'];
                    $sess->calendarevent = $calendarevent;
                    $sess->timemodified = $now;
                    $sess->absenteereport = $absenteereport;
                    $sess->studentpassword = '';
                    $sess->includeqrcode = 0;
                    $sess->rotateqrcode = 0;
                    $sess->rotateqrcodesecret = '';
                    $sess->automark = !empty($formdata->automark) ? $formdata->automark : 0;
                    $sess->automarkcmid = !empty($formdata->automarkcmid) ? $formdata->automarkcmid : 0;
                    $sess->automarkcompleted = 0;

                    if (!empty($formdata->usedefaultsubnet)) {
                        $sess->subnet = $att->subnet;
                    } else {
                        $sess->subnet = $formdata->subnet;
                    }
                    if (!empty($formdata->preventsharedip)) {
                        $sess->preventsharedip = $formdata->preventsharedip;
                    }
                    if (!empty($formdata->preventsharediptime)) {
                        $sess->preventsharediptime = $formdata->preventsharediptime;
                    }

                    if (isset($formdata->studentscanmark)) { // Students will be able to mark their own attendance.
                        $sess->studentscanmark = 1;
                        if (isset($formdata->allowupdatestatus)) {
                            $sess->allowupdatestatus = $formdata->allowupdatestatus;
                        } else {
                            $sess->allowupdatestatus = 0;
                        }
                        if (isset($formdata->autoassignstatus)) {
                            $sess->autoassignstatus = 1;
                        }
                        if (isset($formdata->studentsearlyopentime)) {
                            $sess->studentsearlyopentime = $formdata->studentsearlyopentime;
                        }

                        if (!empty($formdata->randompassword)) {
                            $sess->studentpassword = attendance_random_string();
                        } else if (!empty($formdata->studentpassword)) {
                            $sess->studentpassword = $formdata->studentpassword;
                        }
                        if (!empty($formdata->includeqrcode)) {
                            $sess->includeqrcode = $formdata->includeqrcode;
                        }
                        if (!empty($formdata->rotateqrcode)) {
                            $sess->rotateqrcode = $formdata->rotateqrcode;
                            $sess->studentpassword = attendance_random_string();
                            $sess->rotateqrcodesecret = attendance_random_string();
                        }
                        if (!empty($formdata->preventsharedip)) {
                            $sess->preventsharedip = $formdata->preventsharedip;
                        }
                        if (!empty($formdata->preventsharediptime)) {
                            $sess->preventsharediptime = $formdata->preventsharediptime;
                        }
                    } else {
                        $sess->subnet = '';
                        $sess->automark = 0;
                        $sess->automarkcompleted = 0;
                        $sess->preventsharedip = 0;
                        $sess->preventsharediptime = '';
                    }
                    $sess->statusset = $formdata->statusset;

                    attendance_fill_groupid($formdata, $sessions, $sess);
                }

                $sdate = strtotime("+1 day", $sdate); // Set start to tomorrow.
            } else {
                $startweek = strtotime("+".$formdata->period.' weeks', $startweek);
                $sdate = $startweek;
            }
        }
    } else {
        $sess = new stdClass();
        $sess->sessdate = $sessiondate;
        $sess->duration = $duration;
        $sess->descriptionitemid = $formdata->sdescription['itemid'];
        $sess->description = $formdata->sdescription['text'];
        $sess->descriptionformat = $formdata->sdescription['format'];
        $sess->calendarevent = $calendarevent;
        $sess->timemodified = $now;
        $sess->studentscanmark = 0;
        $sess->allowupdatestatus = 0;
        $sess->autoassignstatus = 0;
        $sess->subnet = '';
        $sess->studentpassword = '';
        $sess->automark = 0;
        $sess->automarkcompleted = 0;

        if (!empty($formdata->automarkcmid)) {
            $sess->automarkcmid = $formdata->automarkcmid;
        } else {
            $sess->automarkcmid = 0;
        }

        $sess->absenteereport = $absenteereport;
        $sess->includeqrcode = 0;
        $sess->rotateqrcode = 0;
        $sess->rotateqrcodesecret = '';

        if (!empty($formdata->usedefaultsubnet)) {
            $sess->subnet = $att->subnet;
        } else {
            $sess->subnet = $formdata->subnet;
        }

        if (!empty($formdata->automark)) {
            $sess->automark = $formdata->automark;
        }
        if (!empty($formdata->automark)) {
            $sess->automark = $formdata->automark;
        }
        if (!empty($formdata->preventsharedip)) {
            $sess->preventsharedip = $formdata->preventsharedip;
        }
        if (!empty($formdata->preventsharediptime)) {
            $sess->preventsharediptime = $formdata->preventsharediptime;
        }

        if (isset($formdata->studentscanmark) && !empty($formdata->studentscanmark)) {
            // Students will be able to mark their own attendance.
            $sess->studentscanmark = 1;
            if (!empty($formdata->allowupdatestatus)) {
                $sess->allowupdatestatus = $formdata->allowupdatestatus;
            } else {
                $sess->allowupdatestatus = 0;
            }
            if (isset($formdata->autoassignstatus) && !empty($formdata->autoassignstatus)) {
                $sess->autoassignstatus = 1;
            }
            if (!empty($formdata->randompassword)) {
                $sess->studentpassword = attendance_random_string();
            } else if (!empty($formdata->studentpassword)) {
                $sess->studentpassword = $formdata->studentpassword;
            }
            if (!empty($formdata->includeqrcode)) {
                $sess->includeqrcode = $formdata->includeqrcode;
            }
            if (!empty($formdata->rotateqrcode)) {
                $sess->rotateqrcode = $formdata->rotateqrcode;
                $sess->studentpassword = attendance_random_string();
                $sess->rotateqrcodesecret = attendance_random_string();
            }
            if (!empty($formdata->usedefaultsubnet)) {
                $sess->subnet = $att->subnet;
            } else {
                $sess->subnet = $formdata->subnet;
            }

            if (!empty($formdata->automark)) {
                $sess->automark = $formdata->automark;
            }
            if (!empty($formdata->preventsharedip)) {
                $sess->preventsharedip = $formdata->preventsharedip;
            }
            if (!empty($formdata->studentsearlyopentime)) {
                $sess->studentsearlyopentime = $formdata->studentsearlyopentime;
            }
        }
        $sess->statusset = $formdata->statusset;

        attendance_fill_groupid($formdata, $sessions, $sess);
    }

    return $sessions;
}

/**
 * Helper function for attendance_construct_sessions_data_for_add().
 *
 * @param stdClass $formdata
 * @param stdClass $sessions
 * @param stdClass $sess
 */
function attendance_fill_groupid($formdata, &$sessions, $sess) {
    if ($formdata->sessiontype == mod_attendance_structure::SESSION_COMMON) {
        $sess = clone $sess;
        $sess->groupid = 0;
        $sessions[] = $sess;
    } else {
        foreach ($formdata->groups as $groupid) {
            $sess = clone $sess;
            $sess->groupid = $groupid;
            $sessions[] = $sess;
        }
    }
}

/**
 * Generates a summary of points for the courses selected.
 *
 * @param array $courseids optional list of courses to return
 * @param string $orderby - optional order by param
 * @return stdClass
 */
function attendance_course_users_points($courseids = [], $orderby = '') {
    global $DB;

    $where = '';
    $params = [];
    $where .= ' AND ats.sessdate < :enddate ';
    $params['enddate'] = time();

    $joingroup = 'LEFT JOIN {groups_members} gm ON (gm.userid = atl.studentid AND gm.groupid = ats.groupid)';
    $where .= ' AND (ats.groupid = 0 or gm.id is NOT NULL)';

    if (!empty($courseids)) {
        list($insql, $inparams) = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED);
        $where .= ' AND c.id ' . $insql;
        $params = array_merge($params, $inparams);
    }

    $sql = "SELECT courseid, coursename, sum(points) / sum(maxpoints) as percentage FROM (
SELECT a.id, a.course as courseid, c.fullname as coursename, atl.studentid AS userid, COUNT(DISTINCT ats.id) AS numtakensessions,
                        SUM(stg.grade) AS points, SUM(stm.maxgrade) AS maxpoints
                   FROM {attendance_sessions} ats
                   JOIN {attendance} a ON a.id = ats.attendanceid
                   JOIN {course} c ON c.id = a.course
                   JOIN {attendance_log} atl ON (atl.sessionid = ats.id)
                   JOIN {attendance_statuses} stg ON (stg.id = atl.statusid AND stg.deleted = 0 AND stg.visible = 1)
                   JOIN (SELECT attendanceid, setnumber, MAX(grade) AS maxgrade
                           FROM {attendance_statuses}
                          WHERE deleted = 0
                            AND visible = 1
                         GROUP BY attendanceid, setnumber) stm
                     ON (stm.setnumber = ats.statusset AND stm.attendanceid = ats.attendanceid)
                  {$joingroup}
                  WHERE ats.sessdate >= c.startdate
                    AND ats.lasttaken != 0
                    AND stm.maxgrade > 0
                    {$where}
                GROUP BY a.id, a.course, c.fullname, atl.studentid
                ) p GROUP by courseid, coursename {$orderby}";

    return $DB->get_records_sql($sql, $params);
}

/**
 * Generates a list of users flagged absent.
 *
 * @param array $courseids optional list of courses to return
 * @param string $orderby how to order results.
 * @param bool $allfornotify get notification list for scheduled task.
 * @return stdClass
 */
function attendance_get_users_to_notify($courseids = [], $orderby = '', $allfornotify = false) {
    global $DB, $CFG;

    $joingroup = 'LEFT JOIN {groups_members} gm ON (gm.userid = atl.studentid AND gm.groupid = ats.groupid)';
    $where = ' AND (ats.groupid = 0 or gm.id is NOT NULL)';
    $having = '';
    $params = [];

    if (!empty($courseids)) {
        list($insql, $inparams) = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED);
        $where .= ' AND c.id ' . $insql;
        $params = array_merge($params, $inparams);
    }
    if ($allfornotify) {
        // Exclude warnings that have already sent the max num.
        $having .= ' AND n.maxwarn > COUNT(DISTINCT ns.id) ';
    }
    $userfieldsapi = \core_user\fields::for_name();
    $unames = $userfieldsapi->get_sql('', false, '', '', false)->selects.',';
    $unames2 = $userfieldsapi->get_sql('u', false, '', '', false)->selects.',';

    if (!empty($CFG->showuseridentity)) {
        $extrafields = explode(',', $CFG->showuseridentity);
        foreach ($extrafields as $field) {
            if (strpos($field, 'profile_field') !== 0) {
                $unames .= $field . ', ';
                $unames2 .= 'u.' . $field . ', ';
            }
        }
    }

    $idfield = $DB->sql_concat('cm.id', 'atl.studentid', 'n.id');
    $params['yesterday'] = time() - DAYSECS;
    $sql = "SELECT {$idfield} as uniqueid, a.id as aid, {$unames2} a.name as aname, cm.id as cmid, c.id as courseid,
                    c.fullname as coursename, atl.studentid AS userid, n.id as notifyid, n.warningpercent, n.emailsubject,
                    n.emailcontent, n.emailcontentformat, n.emailuser, n.thirdpartyemails, n.warnafter, n.maxwarn,
                     COUNT(DISTINCT ats.id) AS numtakensessions, SUM(stg.grade) AS points, SUM(stm.maxgrade) AS maxpoints,
                      COUNT(DISTINCT ns.id) as nscount, MAX(ns.timesent) as timesent,
                      SUM(stg.grade) / SUM(stm.maxgrade) AS percent
                   FROM {attendance_sessions} ats
                   JOIN {attendance} a ON a.id = ats.attendanceid
                   JOIN {course_modules} cm ON cm.instance = a.id
                   JOIN {course} c on c.id = cm.course
                   JOIN {modules} md ON md.id = cm.module AND md.name = 'attendance'
                   JOIN {attendance_log} atl ON (atl.sessionid = ats.id)
                   JOIN {user} u ON (u.id = atl.studentid)
                   JOIN {attendance_statuses} stg ON (stg.id = atl.statusid AND stg.deleted = 0 AND stg.visible = 1)
                   JOIN {attendance_warning} n ON n.idnumber = a.id
                   LEFT JOIN {attendance_warning_done} ns ON ns.notifyid = n.id AND ns.userid = atl.studentid
                   JOIN (SELECT attendanceid, setnumber, MAX(grade) AS maxgrade
                           FROM {attendance_statuses}
                          WHERE deleted = 0
                            AND visible = 1
                         GROUP BY attendanceid, setnumber) stm
                     ON (stm.setnumber = ats.statusset AND stm.attendanceid = ats.attendanceid)
                  {$joingroup}
                  WHERE ats.absenteereport = 1 AND ats.attendanceid IN (SELECT distinct attendanceid
                                                                          FROM {attendance_sessions}
                                                                         WHERE sessdate > :yesterday)
                  {$where}
                GROUP BY uniqueid, a.id, a.name, a.course, c.fullname, atl.studentid, n.id, n.warningpercent,
                         n.emailsubject, n.emailcontent, n.emailcontentformat, n.warnafter, n.maxwarn,
                         n.emailuser, n.thirdpartyemails, cm.id, c.id, {$unames2} ns.userid
                HAVING n.warnafter <= COUNT(DISTINCT ats.id) AND n.warningpercent > ((SUM(stg.grade) / SUM(stm.maxgrade)) * 100)
                {$having}
                      {$orderby}";

    if (!$allfornotify) {
        $idfield = $DB->sql_concat('cmid', 'userid');
        // Only show one record per attendance for teacher reports.
        $sql = "SELECT DISTINCT {$idfield} as id, {$unames} aid, cmid, courseid, aname, coursename, userid,
                        numtakensessions, percent, MAX(timesent) as timesent
              FROM ({$sql}) as m
         GROUP BY id, aid, cmid, courseid, aname, userid, numtakensessions,
                  percent, {$unames} coursename {$orderby}";
    }

    return $DB->get_records_sql($sql, $params);

}

/**
 * Template variables into place in supplied email content.
 *
 * @param object $record db record of details
 * @return array - the content of the fields after templating.
 */
function attendance_template_variables($record) {
    $templatevars = [
        '/%coursename%/' => $record->coursename,
        '/%courseid%/' => $record->courseid,
        '/%userfirstname%/' => $record->firstname,
        '/%userlastname%/' => $record->lastname,
        '/%userid%/' => $record->userid,
        '/%warningpercent%/' => $record->warningpercent,
        '/%attendancename%/' => $record->aname,
        '/%cmid%/' => $record->cmid,
        '/%numtakensessions%/' => $record->numtakensessions,
        '/%points%/' => $record->points,
        '/%maxpoints%/' => $record->maxpoints,
        '/%percent%/' => round($record->percent * 100),
    ];
    $extrauserfields = \core_user\fields::get_name_fields();
    foreach ($extrauserfields as $extra) {
        $templatevars['/%'.$extra.'%/'] = $record->$extra;
    }
    $patterns = array_keys($templatevars); // The placeholders which are to be replaced.
    $replacements = array_values($templatevars); // The values which are to be templated in for the placeholders.
    // Array to describe which fields in reengagement object should have a template replacement.
    $replacementfields = ['emailsubject', 'emailcontent'];

    // Replace %variable% with relevant value everywhere it occurs in reengagement->field.
    foreach ($replacementfields as $field) {
        $record->$field = preg_replace($patterns, $replacements, $record->$field);
    }
    return $record;
}

/**
 * Find highest available status for a user.
 *
 * @param mod_attendance_structure $att attendance structure
 * @param stdclass $attforsession attendance_session record.
 * @param int $scantime - time that session should be recorded against.
 * @return bool/int
 */
function attendance_session_get_highest_status(mod_attendance_structure $att, $attforsession, $scantime = null) {
    // Find the status to set here.
    $statuses = $att->get_statuses();
    $highestavailablegrade = 0;
    $highestavailablestatus = new stdClass();
    // Override time used in status recording.
    $scantime = empty($scantime) ? time() : $scantime;
    foreach ($statuses as $status) {
        if ($status->studentavailability === '0') {
            // This status is never available to students.
            continue;
        }
        if (!empty($status->studentavailability)) {
            $toolateforstatus = (($attforsession->sessdate + ($status->studentavailability * 60)) < $scantime);
            if ($toolateforstatus) {
                continue;
            }
        }
        // This status is available to the student.
        if ($status->grade >= $highestavailablegrade) {
            // This is the most favourable grade so far; save it.
            $highestavailablegrade = $status->grade;
            $highestavailablestatus = $status;
        }
    }
    if (empty($highestavailablestatus)) {
        return false;
    }
    return $highestavailablestatus->id;
}

/**
 * Get available automark options.
 *
 * @return array
 */
function attendance_get_automarkoptions() {
    global $COURSE;

    $options = [];

    $options[ATTENDANCE_AUTOMARK_DISABLED] = get_string('noautomark', 'attendance');
    if (strpos(get_config('tool_log', 'enabled_stores'), 'logstore_standard') !== false) {
        $options[ATTENDANCE_AUTOMARK_ALL] = get_string('automarkall', 'attendance');
    }
    $options[ATTENDANCE_AUTOMARK_CLOSE] = get_string('automarkclose', 'attendance');
    if ($COURSE->id == SITEID || $COURSE->enablecompletion == COMPLETION_ENABLED) {
        $options[ATTENDANCE_AUTOMARK_ACTIVITYCOMPLETION] = get_string('onactivitycompletion', 'attendance');
    }

    return $options;
}

/**
 * Get course module names associated to this course, if they're visible and complete.
 * @param int $id - course id.
 * @return array $automarkcmoptions - list of course module names associated to this course.
 */
function attendance_get_coursemodulenames($id) {
    $coursecontext = context_course::instance($id);
    $modinfo = get_fast_modinfo($coursecontext->instanceid);
    $automarkcmoptions = [];
    foreach ($modinfo->get_instances() as $instances) {
        foreach ($instances as $cm) {
            if (!$cm->uservisible) {
                continue;
            }
            if (empty($cm->completion)) {
                continue;
            }
            $automarkcmoptions[$cm->id] = shorten_text($cm->get_formatted_name()). ' ';
        }
    }
    return $automarkcmoptions;
}

/**
 * Get available sharedip options.
 *
 * @return array
 */
function attendance_get_sharedipoptions() {
    $options = [];
    $options[ATTENDANCE_SHAREDIP_DISABLED] = get_string('no');
    $options[ATTENDANCE_SHAREDIP_FORCE] = get_string('yes');
    $options[ATTENDANCE_SHAREDIP_MINUTES] = get_string('setperiod', 'attendance');

    return $options;
}

/**
 * Used to print simple time - 1am instead of 1:00am.
 *
 * @param int $time - unix timestamp.
 */
function attendance_strftimehm($time) {
    $mins = userdate($time, '%M');

    if ($mins == '00') {
        $format = get_string('strftimeh', 'attendance');
    } else {
        $format = get_string('strftimehm', 'attendance');
    }

    $userdate = userdate($time, $format);
    if (stripos($format, '%p') && empty($userdate)) {
        // Failover - if %p is in use, but resulting time is empty (windows server), make sure a time is still returned.
        $userdate = userdate($time, "%I:%M");
        if (userdate($time, '%H') > 11) {
            $userdate .= 'pm';
        } else {
            $userdate .= 'am';
        }
        return $userdate;
    }

    // Some Lang packs use %p to suffix with AM/PM but not all strftime support this.
    // Check if %p is in use and make sure it's being respected.
    if (stripos($format, '%p')) {
        // Check if $userdate did something with %p by checking userdate against the same format without %p.
        $formatwithoutp = str_ireplace('%p', '', $format);
        if (userdate($time, $formatwithoutp) == $userdate) {
            // The date is the same with and without %p - we have a problem.
            if (userdate($time, '%H') > 11) {
                $userdate .= 'pm';
            } else {
                $userdate .= 'am';
            }
        }
        // Some locales and O/S don't respect correct intended case of %p vs %P
        // This can cause problems with behat which expects AM vs am.
        if (strpos($format, '%p')) { // Should be upper case according to PHP spec.
            $userdate = str_replace('am', 'AM', $userdate);
            $userdate = str_replace('pm', 'PM', $userdate);
        }
    }

    return $userdate;
}

/**
 * Used to print simple time - 1am instead of 1:00am.
 *
 * @param int $datetime - unix timestamp.
 * @param int $duration - number of seconds.
 */
function attendance_construct_session_time($datetime, $duration) {
    $starttime = attendance_strftimehm($datetime);
    $endtime = attendance_strftimehm($datetime + $duration);

    return $starttime . ($duration > 0 ? ' - ' . $endtime : '');
}

/**
 * Used to print session time.
 *
 * @param int $datetime - unix timestamp.
 * @param int $duration - number of seconds duration.
 * @return string.
 */
function construct_session_full_date_time($datetime, $duration) {
    $sessinfo = userdate($datetime, get_string('strftimedmyw', 'attendance'));
    $sessinfo .= ' '.attendance_construct_session_time($datetime, $duration);

    return $sessinfo;
}

/**
 * Render the session QR code.
 *
 * @param stdClass $session
 */
function attendance_renderqrcode($session) {
    global $CFG;

    if (strlen($session->studentpassword) > 0) {
        $qrcodeurl = $CFG->wwwroot . '/mod/attendance/attendance.php?qrpass=' .
            $session->studentpassword . '&sessid=' . $session->id;
    } else {
        $qrcodeurl = $CFG->wwwroot . '/mod/attendance/attendance.php?sessid=' . $session->id;
    }

    $barcode = new TCPDF2DBarcode($qrcodeurl, 'QRCODE');
    $image = $barcode->getBarcodePngData(15, 15);
    echo html_writer::img('data:image/png;base64,' . base64_encode($image), get_string('qrcode', 'attendance'));
}

/**
 * Generate QR code passwords.
 *
 * @param stdClass $session
 */
function attendance_generate_passwords($session) {
    global $DB;
    $attconfig = get_config('attendance');
    $password = [];

    for ($i = 0; $i < 30; $i++) {
        array_push($password, ["attendanceid" => $session->id,
            "password" => random_string(), "expirytime" => time() + ($attconfig->rotateqrcodeinterval * $i)]);
    }

    $DB->insert_records('attendance_rotate_passwords', $password);
}

/**
 * Render JS for rotate QR code passwords.
 *
 * @param stdClass $session
 */
function attendance_renderqrcoderotate($session) {
    // Load required js.
    echo html_writer::tag('script', '',
        [
            'src' => 'js/qrcode/qrcode.min.js',
            'type' => 'text/javascript',
        ]
    );
    echo html_writer::tag('script', '',
        [
            'src' => 'js/password/attendance_QRCodeRotate.js',
            'type' => 'text/javascript',
        ]
    );
    echo html_writer::div('', '', ['id' => 'qrcode']); // Div to display qr code.
    echo html_writer::div(get_string('qrcodevalidbefore', 'attendance').' '.
                          html_writer::span('0', '', ['id' => 'rotate-time']).' '
                          .get_string('qrcodevalidafter', 'attendance'), 'qrcodevalid'); // Div to display timer.
    // Js to start the password manager.
    echo '
    <script type="text/javascript">
        let qrCodeRotate = new attendance_QRCodeRotate();
        qrCodeRotate.start(' . $session->id . ', document.getElementById("qrcode"), document.getElementById("rotate-time"), '.
        time() .');
    </script>';
}

/**
 * Return QR code passwords.
 *
 * @param stdClass $session
 */
function attendance_return_passwords($session) {
    global $DB;

    $sql = 'SELECT * FROM {attendance_rotate_passwords} WHERE attendanceid = ? AND expirytime > ? ORDER BY expirytime ASC';
    return json_encode($DB->get_records_sql($sql, ['attendanceid' => $session->id, time()], $strictness = IGNORE_MISSING));
}

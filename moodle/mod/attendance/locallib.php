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
    $params = array('aid' => $attid);
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
        $statusesout = array();
        foreach ($statuses as $status) {
            $statusesout[] = $status->acronym;
        }
        if ($statusesout) {
            if (count($statusesout) > 6) {
                $statusesout = array_slice($statusesout, 0, 6);
                $statusesout[] = '&helip;';
            }
            $statusesout = implode(' ', $statusesout);
            $statusname .= ' ('.$statusesout.')';
        }
    }

    return $statusname;
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

    $params = array_merge($uparams, array('uid' => $userid));

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
    return $DB->record_exists('attendance_log', array('statusid' => $statusid));
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
    for ($i = 0; $i < 60; $i += 5) {
        $minutes[$i] = sprintf("%02d", $i);
    }

    $sesendtime = array();
    $sesendtime[] =& $mform->createElement('static', 'from', '', get_string('from', 'attendance'));
    $sesendtime[] =& $mform->createElement('select', 'starthour', get_string('hour', 'form'), $hours, false, true);
    $sesendtime[] =& $mform->createElement('select', 'startminute', get_string('minute', 'form'), $minutes, false, true);
    $sesendtime[] =& $mform->createElement('static', 'to', '', get_string('to', 'attendance'));
    $sesendtime[] =& $mform->createElement('select', 'endhour', get_string('hour', 'form'), $hours, false, true);
    $sesendtime[] =& $mform->createElement('select', 'endminute', get_string('minute', 'form'), $minutes, false, true);
    $mform->addGroup($sesendtime, 'sestime', get_string('time', 'attendance'), array(' '), true);
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
        array($attendanceid));
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
    $statussetmaxpoints = array();
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
function attendance_update_users_grade($attendance, $userids=array()) {
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
        $dbparams = array('id' => -($attendance->grade));
        $scale = $DB->get_record('scale', $dbparams);
        $scalearray = explode(',', $scale->scale);
        $attendancegrade = count($scalearray);
    } else {
        $attendancegrade = $attendance->grade;
    }

    $grades = array();
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

        $id = $DB->insert_record('attendance_statuses', $status);
        $status->id = $id;

        $event = \mod_attendance\event\status_added::create(array(
            'objectid' => $status->attendanceid,
            'context' => $status->context,
            'other' => array('acronym' => $status->acronym,
                             'description' => $status->description,
                             'grade' => $status->grade)));
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
    $DB->set_field('attendance_statuses', 'deleted', 1, array('id' => $status->id));
    $event = \mod_attendance\event\status_removed::create(array(
        'objectid' => $status->id,
        'context' => $context,
        'other' => array(
            'acronym' => $status->acronym,
            'description' => $status->description
        )));
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
 * @param bool $setunmarked
 * @return array
 */
function attendance_update_status($status, $acronym, $description, $grade, $visible,
                                  $context = null, $cm = null, $studentavailability = null, $setunmarked = false) {
    global $DB;

    if (empty($context)) {
        $context = context_system::instance();
    }

    if (isset($visible)) {
        $status->visible = $visible;
        $updated[] = $visible ? get_string('show') : get_string('hide');
    } else if (empty($acronym) || empty($description)) {
        return array('acronym' => $acronym, 'description' => $description);
    }

    $updated = array();

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
    if ($setunmarked) {
        $status->setunmarked = 1;
    } else {
        $status->setunmarked = 0;
    }
    $DB->update_record('attendance_statuses', $status);

    $event = \mod_attendance\event\status_updated::create(array(
        'objectid' => $status->attendanceid,
        'context' => $context,
        'other' => array('acronym' => $acronym, 'description' => $description, 'grade' => $grade,
            'updated' => implode(' ', $updated))));
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
    $randombytes = random_bytes_emulate($length);
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
 * Check to see if this session is open for student marking.
 *
 * @param stdclass $sess the session record from attendance_sessions.
 * @return boolean
 */
function attendance_can_student_mark($sess) {
    $canmark = false;
    $attconfig = get_config('attendance');
    if (!empty($attconfig->studentscanmark) && !empty($sess->studentscanmark)) {
        if (empty($attconfig->studentscanmarksessiontime)) {
            $canmark = true;
        } else {
            $duration = $sess->duration;
            if (empty($duration)) {
                $duration = $attconfig->studentscanmarksessiontimeend * 60;
            }
            if ($sess->sessdate < time() && time() < ($sess->sessdate + $duration)) {
                $canmark = true;
            }
        }
    }
    return $canmark;
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
    $myxls = $workbook->add_worksheet('Attendances');
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
 * $param mod_attendance_structure $att - used to get attendance level subnet.
 * @return array.
 */
function attendance_construct_sessions_data_for_add($formdata, mod_attendance_structure $att) {
    global $CFG;

    $sesstarttime = $formdata->sestime['starthour'] * HOURSECS + $formdata->sestime['startminute'] * MINSECS;
    $sesendtime = $formdata->sestime['endhour'] * HOURSECS + $formdata->sestime['endminute'] * MINSECS;
    $sessiondate = $formdata->sessiondate + $sesstarttime;
    $duration = $sesendtime - $sesstarttime;
    $now = time();

    if (empty(get_config('attendance', 'studentscanmark'))) {
        $formdata->studentscanmark = 0;
    }

    $sessions = array();
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

        $wdaydesc = array(0 => 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');

        while ($sdate < $enddate) {
            if ($sdate < $startweek + WEEKSECS) {
                $dinfo = usergetdate($sdate);
                if (isset($formdata->sdays) && array_key_exists($wdaydesc[$dinfo['wday']], $formdata->sdays)) {
                    $sess = new stdClass();
                    $sess->sessdate = make_timestamp($dinfo['year'], $dinfo['mon'], $dinfo['mday'],
                        $formdata->sestime['starthour'], $formdata->sestime['startminute']);
                    $sess->duration = $duration;
                    $sess->descriptionitemid = $formdata->sdescription['itemid'];
                    $sess->description = $formdata->sdescription['text'];
                    $sess->descriptionformat = $formdata->sdescription['format'];
                    $sess->timemodified = $now;
                    if (isset($formdata->studentscanmark)) { // Students will be able to mark their own attendance.
                        $sess->studentscanmark = 1;
                        if (!empty($formdata->usedefaultsubnet)) {
                            $sess->subnet = $att->subnet;
                        } else {
                            $sess->subnet = $formdata->subnet;
                        }
                        $sess->automark = $formdata->automark;
                        $sess->automarkcompleted = 0;
                        if (!empty($formdata->randompassword)) {
                            $sess->studentpassword = attendance_random_string();
                        } else {
                            $sess->studentpassword = $formdata->studentpassword;
                        }
                    } else {
                        $sess->studentpassword = '';
                        $sess->subnet = '';
                        $sess->automark = 0;
                        $sess->automarkcompleted = 0;
                    }
                    $sess->statusset = $formdata->statusset;

                    attendance_fill_groupid($formdata, $sessions, $sess);
                }
                $sdate += DAYSECS;
            } else {
                $startweek += WEEKSECS * $formdata->period;
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
        $sess->timemodified = $now;
        $sess->studentscanmark = 0;
        $sess->subnet = '';
        $sess->studentpassword = '';
        $sess->automark = 0;
        $sess->automarkcompleted = 0;

        if (isset($formdata->studentscanmark) && !empty($formdata->studentscanmark)) {
            // Students will be able to mark their own attendance.
            $sess->studentscanmark = 1;
            if (!empty($formdata->randompassword)) {
                $sess->studentpassword = attendance_random_string();
            } else if (!empty($formdata->studentpassword)) {
                $sess->studentpassword = $formdata->studentpassword;
            }
            if (!empty($formdata->usedefaultsubnet)) {
                $sess->subnet = $att->subnet;
            } else {
                $sess->subnet = $formdata->subnet;
            }

            if (!empty($formdata->automark)) {
                $sess->automark = $formdata->automark;
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
function attendance_course_users_points($courseids = array(), $orderby = '') {
    global $DB;

    $where = '';
    $params = array();
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
                    {$where}
                GROUP BY a.id, a.course, c.fullname, atl.studentid
                ) p GROUP by courseid, coursename {$orderby}";

    return $DB->get_records_sql($sql, $params);
}

/**
 * Generates a list of users flagged absent.
 *
 * @param array $courseids optional list of courses to return
 * @param bool $allfornotify get notification list for scheduled task.
 * @return stdClass
 */
function attendance_get_users_to_notify($courseids = array(), $orderby = '', $allfornotify = false) {
    global $DB;

    $joingroup = 'LEFT JOIN {groups_members} gm ON (gm.userid = atl.studentid AND gm.groupid = ats.groupid)';
    $where = ' AND (ats.groupid = 0 or gm.id is NOT NULL)';
    $having = '';
    $params = array();

    if (!empty($courseids)) {
        list($insql, $inparams) = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED);
        $where .= ' AND c.id ' . $insql;
        $params = array_merge($params, $inparams);
    }
    if ($allfornotify) {
        // Exclude warnings that have already sent the max num.
        $having .= ' AND n.maxwarn > COUNT(DISTINCT ns.id) ';
    }

    $unames = get_all_user_name_fields(true);
    $unames2 = get_all_user_name_fields(true, 'u');

    $idfield = $DB->sql_concat('cm.id', 'atl.studentid', 'n.id');
    $sql = "SELECT {$idfield} as uniqueid, a.id as aid, {$unames2}, a.name as aname, cm.id as cmid, c.id as courseid,
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
                  WHERE 1 = 1 {$where}
                GROUP BY uniqueid, a.id, a.name, a.course, c.fullname, atl.studentid, n.id, n.warningpercent,
                         n.emailsubject, n.emailcontent, n.emailcontentformat, n.warnafter, n.maxwarn,
                         n.emailuser, n.thirdpartyemails, cm.id, c.id, {$unames2}, ns.userid
                HAVING n.warnafter <= COUNT(DISTINCT ats.id) AND n.warningpercent > ((SUM(stg.grade) / SUM(stm.maxgrade)) * 100)
                {$having}
                      {$orderby}";

    if (!$allfornotify) {
        $idfield = $DB->sql_concat('cmid', 'userid');
        // Only show one record per attendance for teacher reports.
        $sql = "SELECT DISTINCT {$idfield} as id, {$unames}, aid, cmid, courseid, aname, coursename, userid,
                        numtakensessions, percent, MAX(timesent) as timesent
              FROM ({$sql}) as m
         GROUP BY id, aid, cmid, courseid, aname, userid, numtakensessions,
                  percent, coursename, {$unames} {$orderby}";
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
    $templatevars = array(
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
        '/%percent%/' => $record->percent,
    );
    $extrauserfields = get_all_user_name_fields();
    foreach ($extrauserfields as $extra) {
        $templatevars['/%'.$extra.'%/'] = $record->$extra;
    }
    $patterns = array_keys($templatevars); // The placeholders which are to be replaced.
    $replacements = array_values($templatevars); // The values which are to be templated in for the placeholders.
    // Array to describe which fields in reengagement object should have a template replacement.
    $replacementfields = array('emailsubject', 'emailcontent');

    // Replace %variable% with relevant value everywhere it occurs in reengagement->field.
    foreach ($replacementfields as $field) {
        $record->$field = preg_replace($patterns, $replacements, $record->$field);
    }
    return $record;
}
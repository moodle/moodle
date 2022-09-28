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
 * Attendance module renderable component.
 *
 * @package    mod_attendance
 * @copyright  2022 Dan Marsden
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_attendance\output;

use renderable;
use mod_attendance_structure;
use mod_attendance_view_page_params;
use mod_attendance_summary;
use moodle_url;
use moodle_exception;
use context_module;
use stdClass;

/**
 * Class user data.
 *
 * @copyright  2011 Artem Andreev <andreev.artem@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_data implements renderable {
    /** @var mixed|object  */
    public $user;
    /** @var array|null|stdClass  */
    public $pageparams;
    /** @var array  */
    public $statuses;
    /** @var array  */
    public $summary;
    /** @var filter_controls  */
    public $filtercontrols;
    /** @var array  */
    public $sessionslog;
    /** @var array  */
    public $groups;
    /** @var array  */
    public $coursesatts;
    /** @var string  */
    private $urlpath;
    /** @var array */
    private $urlparams;

    /**
     * attendance_user_data constructor.
     * @param mod_attendance_structure $att
     * @param int $userid
     * @param boolean $mobile - this is called by the mobile code, don't generate everything.
     */
    public function  __construct(mod_attendance_structure $att, $userid, $mobile = false) {
        $this->user = $att->get_user($userid);

        $this->pageparams = $att->pageparams;

        if ($this->pageparams->mode == mod_attendance_view_page_params::MODE_THIS_COURSE) {
            $this->statuses = $att->get_statuses(true, true);

            if (!$mobile) {
                $this->summary = new mod_attendance_summary($att->id, array($userid), $att->pageparams->startdate,
                    $att->pageparams->enddate);

                $this->filtercontrols = new filter_controls($att);
            }

            $this->sessionslog = $att->get_user_filtered_sessions_log_extended($userid);

            $this->groups = groups_get_all_groups($att->course->id);
        } else if ($this->pageparams->mode == mod_attendance_view_page_params::MODE_ALL_SESSIONS) {
            $this->coursesatts = attendance_get_user_courses_attendances($userid);
            $this->statuses = array();
            $this->summaries = array();
            $this->groups = array();

            foreach ($this->coursesatts as $atid => $ca) {
                // Check to make sure the user can view this cm.
                $modinfo = get_fast_modinfo($ca->courseid);
                if (!$modinfo->instances['attendance'][$ca->attid]->uservisible) {
                    unset($this->coursesatts[$atid]);
                    continue;
                } else {
                    $this->coursesatts[$atid]->cmid = $modinfo->instances['attendance'][$ca->attid]->get_course_module_record()->id;
                }
                $this->statuses[$ca->attid] = attendance_get_statuses($ca->attid);
                $this->summaries[$ca->attid] = new mod_attendance_summary($ca->attid, array($userid));

                if (!array_key_exists($ca->courseid, $this->groups)) {
                    $this->groups[$ca->courseid] = groups_get_all_groups($ca->courseid);
                }
            }

            if (!$mobile) {
                $this->summary = new mod_attendance_summary($att->id, array($userid), $att->pageparams->startdate,
                    $att->pageparams->enddate);

                $this->filtercontrols = new filter_controls($att);
            }

            $this->sessionslog = attendance_get_user_sessions_log_full($userid, $this->pageparams);

            foreach ($this->sessionslog as $sessid => $sess) {
                if (array_key_exists($sess->attendanceid, $this->coursesatts)) {
                    $this->sessionslog[$sessid]->cmid = $this->coursesatts[$sess->attendanceid]->cmid;
                } else {
                    // Session attendanceid not found in coursesatts, probably because it is not uservisible.
                    unset($this->sessionslog[$sessid]);
                }
            }

        } else {
            $this->coursesatts = attendance_get_user_courses_attendances($userid);
            $this->statuses = array();
            $this->summary = array();
            foreach ($this->coursesatts as $atid => $ca) {
                // Check to make sure the user can view this cm.
                $modinfo = get_fast_modinfo($ca->courseid);
                if (!$modinfo->instances['attendance'][$ca->attid]->uservisible) {
                    unset($this->coursesatts[$atid]);
                    continue;
                } else {
                    $this->coursesatts[$atid]->cmid = $modinfo->instances['attendance'][$ca->attid]->get_course_module_record()->id;
                }
                $this->statuses[$ca->attid] = attendance_get_statuses($ca->attid);
                $this->summary[$ca->attid] = new mod_attendance_summary($ca->attid, array($userid));
            }
        }
        $this->urlpath = $att->url_view()->out_omit_querystring();
        $params = $att->pageparams->get_significant_params();
        $params['id'] = $att->cm->id;
        $this->urlparams = $params;
    }

    /**
     * Url function
     * @param array $params
     * @param array $excludeparams
     * @return moodle_url
     */
    public function url($params=array(), $excludeparams=array()) {
        $params = array_merge($this->urlparams, $params);

        foreach ($excludeparams as $paramkey) {
            unset($params[$paramkey]);
        }

        return new moodle_url($this->urlpath, $params);
    }

    /**
     * Take multiple sessions attendance from form data.
     *
     * @param stdClass $formdata
     */
    public function take_sessions_from_form_data($formdata) {
        global $DB, $USER;
        // TODO: WARNING - $formdata is unclean - comes from direct $_POST - ideally needs a rewrite but we do some cleaning below.
        // This whole function could do with a nice clean up.

        $now = time();
        $sesslog = array();
        $formdata = (array)$formdata;
        $updatedsessions = array();
        $sessionatt = array();

        foreach ($formdata as $key => $value) {
            // Look at Remarks field because the user options may not be passed if empty.
            if (substr($key, 0, 7) == 'remarks') {
                $parts = explode('sess', substr($key, 7));
                $stid = $parts[0];
                if (!(is_numeric($stid))) { // Sanity check on $stid.
                    throw new moodle_exception('nonnumericid', 'attendance');
                }
                $sessid = $parts[1];
                if (!(is_numeric($sessid))) { // Sanity check on $sessid.
                    throw new moodle_exception('nonnumericid', 'attendance');
                }
                $dbsession = $this->sessionslog[$sessid];

                $context = context_module::instance($dbsession->cmid);
                if (!has_capability('mod/attendance:takeattendances', $context)) {
                    // How do we tell user about this?
                    \core\notification::warning(get_string("nocapabilitytotakethisattendance", "attendance", $dbsession->cmid));
                    continue;
                }

                $formkey = 'user'.$stid.'sess'.$sessid;
                $attid = $dbsession->attendanceid;
                $statusset = array_filter($this->statuses[$attid],
                    function($x) use($dbsession) {
                        return $x->setnumber === $dbsession->statusset;
                    });
                $sessionatt[$sessid] = $attid;
                $formlog = new stdClass();
                if (array_key_exists($formkey, $formdata) && is_numeric($formdata[$formkey])) {
                    $formlog->statusid = $formdata[$formkey];
                }
                $formlog->studentid = $stid; // We check is_numeric on this above.
                $formlog->statusset = implode(',', array_keys($statusset));
                $formlog->remarks = $value;
                $formlog->sessionid = $sessid;
                $formlog->timetaken = $now;
                $formlog->takenby = $USER->id;

                if (!array_key_exists($stid, $sesslog)) {
                    $sesslog[$stid] = array();
                }
                $sesslog[$stid][$sessid] = $formlog;
            }
        }

        $updateatts = array();
        foreach ($sesslog as $stid => $userlog) {
            $dbstudlog = $DB->get_records('attendance_log', array('studentid' => $stid), '',
                'sessionid,statusid,remarks,id,statusset');
            foreach ($userlog as $log) {
                if (array_key_exists($log->sessionid, $dbstudlog)) {
                    $attid = $sessionatt[$log->sessionid];
                    // Check if anything important has changed before updating record.
                    // Don't update timetaken/takenby records if nothing has changed.
                    if ($dbstudlog[$log->sessionid]->remarks != $log->remarks ||
                        $dbstudlog[$log->sessionid]->statusid != $log->statusid ||
                        $dbstudlog[$log->sessionid]->statusset != $log->statusset) {

                        $log->id = $dbstudlog[$log->sessionid]->id;
                        $DB->update_record('attendance_log', $log);

                        $updatedsessions[$log->sessionid] = $log->sessionid;
                        if (!array_key_exists($attid, $updateatts)) {
                            $updateatts[$attid] = array();
                        }
                        array_push($updateatts[$attid], $log->studentid);
                    }
                } else {
                    $DB->insert_record('attendance_log', $log, false);
                    $updatedsessions[$log->sessionid] = $log->sessionid;
                    if (!array_key_exists($attid, $updateatts)) {
                        $updateatts[$attid] = array();
                    }
                    array_push($updateatts[$attid], $log->studentid);
                }
            }
        }

        foreach ($updatedsessions as $sessionid) {
            $session = $this->sessionslog[$sessionid];
            $session->lasttaken = $now;
            $session->lasttakenby = $USER->id;
            $DB->update_record('attendance_sessions', $session);
        }

        if (!empty($updateatts)) {
            $attendancegrade = $DB->get_records_list('attendance', 'id', array_keys($updateatts), '', 'id, grade');
            foreach ($updateatts as $attid => $updateusers) {
                if ($attendancegrade[$attid] != 0) {
                    attendance_update_users_grades_by_id($attid, $grade, $updateusers);
                }
            }
        }
    }
}

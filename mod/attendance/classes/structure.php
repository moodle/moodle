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
 * Class definition for mod_attendance_structure
 *
 * @package   mod_attendance
 * @copyright  2016 Dan Marsden http://danmarsden.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

global $CFG; // This class is included inside existing functions.
require_once(dirname(__FILE__) . '/calendar_helpers.php');
require_once($CFG->libdir .'/filelib.php');

/**
 * Main class with all Attendance related info.
 *
 * @copyright  2016 Dan Marsden http://danmarsden.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_attendance_structure {
    /** Common sessions */
    const SESSION_COMMON        = 0;
    /** Group sessions */
    const SESSION_GROUP         = 1;

    /** @var stdclass course module record */
    public $cm;

    /** @var int cmid - needed for calendar internal tests (see Issue #473) */
    public $cmid;

    /** @var stdclass course record */
    public $course;

    /** @var stdclass context object */
    public $context;

    /** @var int attendance instance identifier */
    public $id;

    /** @var string attendance activity name */
    public $name;

    /** @var float number (10, 5) unsigned, the maximum grade for attendance */
    public $grade;

    /** @var int last time attendance was modified - used for global search */
    public $timemodified;

    /** @var string required field for activity modules and searching */
    public $intro;

    /** @var int format of the intro (see above) */
    public $introformat;

    /** @var array current page parameters */
    public $pageparams;

    /** @var string subnets (IP range) for student self selection. */
    public $subnet;

    /** @var string subnets (IP range) for student self selection. */
    public $automark;

    /** @var boolean flag set when automarking is complete. */
    public $automarkcompleted;

    /** @var int Define if extra user details should be shown in reports */
    public $showextrauserdetails;

    /** @var int Define if session details should be shown in reports */
    public $showsessiondetails;

    /** @var int Position for the session detail columns related to summary columns.*/
    public $sessiondetailspos;

    /** @var int groupmode  */
    private $groupmode;

    /** @var  array */
    private $statuses;
    /** @var  array Cache list of all statuses (not just one used by current session). */
    private $allstatuses;

    /** @var array of sessionid. */
    private $sessioninfo = array();

    /** @var float number [0..1], the threshold for student to be shown at low grade report */
    private $lowgradethreshold;


    /**
     * Initializes the attendance API instance using the data from DB
     *
     * Makes deep copy of all passed records properties. Replaces integer $course attribute
     * with a full database record (course should not be stored in instances table anyway).
     *
     * @param stdClass $dbrecord Attandance instance data from {attendance} table
     * @param stdClass $cm       Course module record as returned by {@see get_coursemodule_from_id()}
     * @param stdClass $course   Course record from {course} table
     * @param stdClass $context  The context of the attendance instance
     * @param stdClass $pageparams
     */
    public function __construct(stdClass $dbrecord, stdClass $cm, stdClass $course, stdClass $context=null, $pageparams=null) {
        global $DB;

        foreach ($dbrecord as $field => $value) {
            if (property_exists('mod_attendance_structure', $field)) {
                $this->{$field} = $value;
            } else {
                throw new coding_exception('The attendance table has a field with no property in the attendance class');
            }
        }
        $this->cm           = $cm;
        if (empty($this->cmid)) {
            $this->cmid = $cm->id;
        }
        $this->course       = $course;
        if (is_null($context)) {
            $this->context = context_module::instance($this->cm->id);
        } else {
            $this->context = $context;
        }

        $this->pageparams = $pageparams;

        if (isset($pageparams->showextrauserdetails) && $pageparams->showextrauserdetails != $this->showextrauserdetails) {
            $DB->set_field('attendance', 'showextrauserdetails', $pageparams->showextrauserdetails, array('id' => $this->id));
        }
        if (isset($pageparams->showsessiondetails) && $pageparams->showsessiondetails != $this->showsessiondetails) {
            $DB->set_field('attendance', 'showsessiondetails', $pageparams->showsessiondetails, array('id' => $this->id));
        }
        if (isset($pageparams->sessiondetailspos) && $pageparams->sessiondetailspos != $this->sessiondetailspos) {
            $DB->set_field('attendance', 'sessiondetailspos', $pageparams->sessiondetailspos, array('id' => $this->id));
        }
    }

    /**
     * Get group mode.
     *
     * @return int
     */
    public function get_group_mode() : int {
        if (is_null($this->groupmode)) {
            $this->groupmode = groups_get_activity_groupmode($this->cm, $this->course);
        }
        return $this->groupmode;
    }

    /**
     * Returns current sessions for this attendance
     *
     * Fetches data from {attendance_sessions}
     *
     * @return array of records or an empty array
     */
    public function get_current_sessions() : array {
        global $DB;

        $today = time(); // Because we compare with database, we don't need to use usertime().

        $sql = "SELECT *
                  FROM {attendance_sessions}
                 WHERE :time BETWEEN sessdate AND (sessdate + duration)
                   AND attendanceid = :aid";
        $params = array(
            'time'  => $today,
            'aid'   => $this->id);

        return $DB->get_records_sql($sql, $params);
    }

    /**
     * Returns today sessions for this attendance
     *
     * Fetches data from {attendance_sessions}
     *
     * @return array of records or an empty array
     */
    public function get_today_sessions() : array {
        global $DB;

        $start = usergetmidnight(time());
        $end = $start + DAYSECS;

        $sql = "SELECT *
                  FROM {attendance_sessions}
                 WHERE sessdate >= :start AND sessdate < :end
                   AND attendanceid = :aid";
        $params = array(
            'start' => $start,
            'end'   => $end,
            'aid'   => $this->id);

        return $DB->get_records_sql($sql, $params);
    }

    /**
     * Returns today sessions suitable for copying attendance log
     *
     * Fetches data from {attendance_sessions}
     * @param stdClass $sess
     * @return array of records or an empty array
     */
    public function get_today_sessions_for_copy($sess) : array {
        global $DB;

        $start = usergetmidnight($sess->sessdate);

        $sql = "SELECT *
                  FROM {attendance_sessions}
                 WHERE sessdate >= :start AND sessdate <= :end AND
                       (groupid = 0 OR groupid = :groupid) AND
                       lasttaken > 0 AND attendanceid = :aid";
        $params = array(
            'start'     => $start,
            'end'       => $sess->sessdate,
            'groupid'   => $sess->groupid,
            'aid'       => $this->id);

        return $DB->get_records_sql($sql, $params);
    }

    /**
     * Returns count of hidden sessions for this attendance
     *
     * Fetches data from {attendance_sessions}
     *
     * @return int count of hidden sessions
     */
    public function get_hidden_sessions_count() : int {
        global $DB;

        $where = "attendanceid = :aid AND sessdate < :csdate";
        $params = array(
            'aid'   => $this->id,
            'csdate' => $this->course->startdate);

        return $DB->count_records_select('attendance_sessions', $where, $params);
    }

    /**
     * Returns the hidden sessions for this attendance
     *
     * Fetches data from {attendance_sessions}
     *
     * @return array hidden sessions
     */
    public function get_hidden_sessions() : array {
        global $DB;

        $where = "attendanceid = :aid AND sessdate < :csdate";
        $params = array(
            'aid'   => $this->id,
            'csdate' => $this->course->startdate);

        return $DB->get_records_select('attendance_sessions', $where, $params);
    }

    /**
     * Get filtered sessions.
     *
     * @return array
     */
    public function get_filtered_sessions() : array {
        global $DB;

        if ($this->pageparams->startdate && $this->pageparams->enddate) {
            $where = "attendanceid = :aid AND sessdate >= :csdate AND sessdate >= :sdate AND sessdate < :edate";
        } else if ($this->pageparams->enddate) {
            $where = "attendanceid = :aid AND sessdate >= :csdate AND sessdate < :edate";
        } else {
            $where = "attendanceid = :aid AND sessdate >= :csdate";
        }

        if ($this->pageparams->get_current_sesstype() > mod_attendance_page_with_filter_controls::SESSTYPE_ALL) {
            $where .= " AND (groupid = :cgroup OR groupid = 0)";
        }
        $params = array(
            'aid'       => $this->id,
            'csdate'    => $this->course->startdate,
            'sdate'     => $this->pageparams->startdate,
            'edate'     => $this->pageparams->enddate,
            'cgroup'    => $this->pageparams->get_current_sesstype());
        $sessions = $DB->get_records_select('attendance_sessions', $where, $params, 'sessdate asc');
        $statussetmaxpoints = attendance_get_statusset_maxpoints($this->get_statuses(true, true));
        foreach ($sessions as $sess) {
            if (empty($sess->description)) {
                $sess->description = get_string('nodescription', 'attendance');
            } else {
                $sess->description = file_rewrite_pluginfile_urls($sess->description,
                    'pluginfile.php', $this->context->id, 'mod_attendance', 'session', $sess->id);
            }
            $sess->maxpoints = $statussetmaxpoints[$sess->statusset];
        }

        return $sessions;
    }

    /**
     * Get manage url.
     * @param array $params
     * @return moodle_url of manage.php for attendance instance
     */
    public function url_manage($params=array()) : moodle_url {
        $params = array_merge(array('id' => $this->cm->id), $params);
        return new moodle_url('/mod/attendance/manage.php', $params);
    }

    /**
     * Get manage temp users url.
     * @param array $params optional
     * @return moodle_url of tempusers.php for attendance instance
     */
    public function url_managetemp($params=array()) : moodle_url {
        $params = array_merge(array('id' => $this->cm->id), $params);
        return new moodle_url('/mod/attendance/tempusers.php', $params);
    }

    /**
     * Get temp delete url.
     *
     * @param array $params optional
     * @return moodle_url of tempdelete.php for attendance instance
     */
    public function url_tempdelete($params=array()) : moodle_url {
        $params = array_merge(array('id' => $this->cm->id, 'action' => 'delete'), $params);
        return new moodle_url('/mod/attendance/tempedit.php', $params);
    }

    /**
     * Get temp edit url.
     *
     * @param array $params optional
     * @return moodle_url of tempedit.php for attendance instance
     */
    public function url_tempedit($params=array()) : moodle_url {
        $params = array_merge(array('id' => $this->cm->id), $params);
        return new moodle_url('/mod/attendance/tempedit.php', $params);
    }

    /**
     * Get temp merge url
     *
     * @param array $params optional
     * @return moodle_url of tempedit.php for attendance instance
     */
    public function url_tempmerge($params=array()) : moodle_url {
        $params = array_merge(array('id' => $this->cm->id), $params);
        return new moodle_url('/mod/attendance/tempmerge.php', $params);
    }

    /**
     * Get url for sessions.
     * @param array $params
     * @return moodle_url of sessions.php for attendance instance
     */
    public function url_sessions($params=array()) : moodle_url {
        $params = array_merge(array('id' => $this->cm->id), $params);
        return new moodle_url('/mod/attendance/sessions.php', $params);
    }

    /**
     * Get url for report.
     * @param array $params
     * @return moodle_url of report.php for attendance instance
     */
    public function url_report($params=array()) : moodle_url {
        $params = array_merge(array('id' => $this->cm->id), $params);
        return new moodle_url('/mod/attendance/report.php', $params);
    }

    /**
     * Get url for report.
     * @param array $params
     * @return moodle_url of report.php for attendance instance
     */
    public function url_absentee($params=array()) : moodle_url {
        $params = array_merge(array('id' => $this->cm->id), $params);
        return new moodle_url('/mod/attendance/absentee.php', $params);
    }

    /**
     * Get url for export.
     *
     * @return moodle_url of export.php for attendance instance
     */
    public function url_export() : moodle_url {
        $params = array('id' => $this->cm->id);
        return new moodle_url('/mod/attendance/export.php', $params);
    }

    /**
     * Get preferences url
     * @param array $params
     * @return moodle_url of attsettings.php for attendance instance
     */
    public function url_preferences($params=array()) : moodle_url {
        // Add the statusset params.
        if (isset($this->pageparams->statusset) && !isset($params['statusset'])) {
            $params['statusset'] = $this->pageparams->statusset;
        }
        $params = array_merge(array('id' => $this->cm->id), $params);
        return new moodle_url('/mod/attendance/preferences.php', $params);
    }

    /**
     * Get preferences url
     * @param array $params
     * @return moodle_url of attsettings.php for attendance instance
     */
    public function url_warnings($params=array()) : moodle_url {
        // Add the statusset params.
        if (isset($this->pageparams->statusset) && !isset($params['statusset'])) {
            $params['statusset'] = $this->pageparams->statusset;
        }
        $params = array_merge(array('id' => $this->cm->id), $params);
        return new moodle_url('/mod/attendance/warnings.php', $params);
    }

    /**
     * Get take url.
     * @param array $params
     * @return moodle_url of attendances.php for attendance instance
     */
    public function url_take($params=array()) : moodle_url {
        $params = array_merge(array('id' => $this->cm->id), $params);
        return new moodle_url('/mod/attendance/take.php', $params);
    }

    /**
     * Get view url.
     * @param array $params
     * @return moodle_url
     */
    public function url_view($params=array()) : moodle_url {
        $params = array_merge(array('id' => $this->cm->id), $params);
        return new moodle_url('/mod/attendance/view.php', $params);
    }

    /**
     * Add sessions.
     *
     * @param array $sessions
     */
    public function add_sessions($sessions) {
        foreach ($sessions as $sess) {
            $this->add_session($sess);
        }
    }

    /**
     * Add single session.
     *
     * @param stdClass $sess
     * @return int $sessionid
     */
    public function add_session($sess) : int {
        global $DB;
        $config = get_config('attendance');

        $sess->attendanceid = $this->id;
        $sess->automarkcompleted = 0;
        if (!isset($sess->automark)) {
            $sess->automark = 0;
        }
        if (empty($config->enablecalendar)) {
            // If calendard disabled at site level, don't use it.
            $sess->calendarevent = 0;
        }
        $sess->id = $DB->insert_record('attendance_sessions', $sess);
        $description = file_save_draft_area_files($sess->descriptionitemid,
            $this->context->id, 'mod_attendance', 'session', $sess->id,
            array('subdirs' => false, 'maxfiles' => -1, 'maxbytes' => 0),
            $sess->description);
        $DB->set_field('attendance_sessions', 'description', $description, array('id' => $sess->id));

        $sess->caleventid = 0;
        attendance_create_calendar_event($sess);

        $infoarray = array();
        $infoarray[] = construct_session_full_date_time($sess->sessdate, $sess->duration);

        // Trigger a session added event.
        $event = \mod_attendance\event\session_added::create(array(
            'objectid' => $this->id,
            'context' => $this->context,
            'other' => array('info' => implode(',', $infoarray))
        ));
        $event->add_record_snapshot('course_modules', $this->cm);
        $sess->description = $description;
        $sess->lasttaken = 0;
        $sess->lasttakenby = 0;
        if (!isset($sess->studentscanmark)) {
            $sess->studentscanmark = 0;
        }
        if (!isset($sess->autoassignstatus)) {
            $sess->autoassignstatus = 0;
        }
        if (!isset($sess->studentpassword)) {
            $sess->studentpassword = '';
        }
        if (!isset($sess->subnet)) {
            $sess->subnet = '';
        }

        if (!isset($sess->preventsharedip)) {
            $sess->preventsharedip = 0;
        }

        if (!isset($sess->preventsharediptime)) {
            $sess->preventsharediptime = '';
        }
        if (!isset($sess->includeqrcode)) {
            $sess->includeqrcode = 0;
        }
        if (!isset($sess->rotateqrcode)) {
            $sess->rotateqrcode = 0;
            $sess->rotateqrcodesecret = '';
        }
        $event->add_record_snapshot('attendance_sessions', $sess);
        $event->trigger();

        return $sess->id;
    }

    /**
     * Update session from form.
     *
     * @param stdClass $formdata
     * @param int $sessionid
     */
    public function update_session_from_form_data($formdata, $sessionid) {
        global $DB;

        if (!$sess = $DB->get_record('attendance_sessions', array('id' => $sessionid) )) {
            throw new moodle_exception('No such session in this course');
        }

        $sesstarttime = $formdata->sestime['starthour'] * HOURSECS + $formdata->sestime['startminute'] * MINSECS;
        $sesendtime = $formdata->sestime['endhour'] * HOURSECS + $formdata->sestime['endminute'] * MINSECS;

        $sess->sessdate = $formdata->sessiondate + $sesstarttime;
        $sess->duration = $sesendtime - $sesstarttime;

        $description = file_save_draft_area_files($formdata->sdescription['itemid'],
            $this->context->id, 'mod_attendance', 'session', $sessionid,
            array('subdirs' => false, 'maxfiles' => -1, 'maxbytes' => 0), $formdata->sdescription['text']);
        $sess->description = $description;
        $sess->descriptionformat = $formdata->sdescription['format'];
        $sess->calendarevent = empty($formdata->calendarevent) ? 0 : $formdata->calendarevent;

        $sess->studentscanmark = 0;
        $sess->autoassignstatus = 0;
        $sess->studentpassword = '';
        $sess->subnet = '';
        $sess->automark = 0;
        $sess->automarkcompleted = 0;
        $sess->preventsharedip = 0;
        $sess->preventsharediptime = '';
        $sess->includeqrcode = 0;
        $sess->rotateqrcode = 0;
        $sess->rotateqrcodesecret = '';

        if (!empty(get_config('attendance', 'enablewarnings'))) {
            $sess->absenteereport = empty($formdata->absenteereport) ? 0 : 1;
        }
        if (!empty($formdata->autoassignstatus)) {
            $sess->autoassignstatus = $formdata->autoassignstatus;
        }
        $studentscanmark = get_config('attendance', 'studentscanmark');

        if (!empty($studentscanmark) &&
            !empty($formdata->studentscanmark)) {
            $sess->studentscanmark = $formdata->studentscanmark;
            $sess->studentpassword = $formdata->studentpassword;
            $sess->autoassignstatus = $formdata->autoassignstatus;
            if (!empty($formdata->includeqrcode)) {
                $sess->includeqrcode = $formdata->includeqrcode;
            }
            if (!empty($formdata->rotateqrcode)) {
                $sess->rotateqrcode = $formdata->rotateqrcode;
                $sess->studentpassword = attendance_random_string();
                $sess->rotateqrcodesecret = attendance_random_string();
            }
        }
        if (!empty($formdata->usedefaultsubnet)) {
            $sess->subnet = $this->subnet;
        } else {
            $sess->subnet = $formdata->subnet;
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

        $sess->timemodified = time();
        $DB->update_record('attendance_sessions', $sess);

        if (empty($sess->caleventid)) {
             // This shouldn't really happen, but just in case to prevent fatal error.
            attendance_create_calendar_event($sess);
        } else {
            attendance_update_calendar_event($sess);
        }

        $info = construct_session_full_date_time($sess->sessdate, $sess->duration);
        $event = \mod_attendance\event\session_updated::create(array(
            'objectid' => $this->id,
            'context' => $this->context,
            'other' => array('info' => $info, 'sessionid' => $sessionid,
                'action' => mod_attendance_sessions_page_params::ACTION_UPDATE)));
        $event->add_record_snapshot('course_modules', $this->cm);
        $event->add_record_snapshot('attendance_sessions', $sess);
        $event->trigger();
    }

    /**
     * Used to record attendance submitted by the student.
     *
     * @param stdClass $mformdata
     * @return boolean
     */
    public function take_from_student($mformdata) : bool {
        global $DB, $USER;

        $statuses = implode(',', array_keys( (array)$this->get_statuses() ));
        $now = time();

        $record = new stdClass();
        $record->studentid = $USER->id;
        $record->statusid = $mformdata->status;
        $record->statusset = $statuses;
        $record->remarks = get_string('set_by_student', 'mod_attendance');
        $record->sessionid = $mformdata->sessid;
        $record->timetaken = $now;
        $record->takenby = $USER->id;
        $record->ipaddress = getremoteaddr(null);

        $existingattendance = $DB->record_exists('attendance_log',
            array('sessionid' => $mformdata->sessid, 'studentid' => $USER->id));

        if ($existingattendance) {
            // Already recorded do not save.
            return false;
        }

        $logid = $DB->insert_record('attendance_log', $record, false);
        $record->id = $logid;

        // Update the session to show that a register has been taken, or staff may overwrite records.
        $session = $this->get_session_info($mformdata->sessid);
        $session->lasttaken = $now;
        $session->lasttakenby = $USER->id;
        $DB->update_record('attendance_sessions', $session);

        // Update the users grade.
        $this->update_users_grade(array($USER->id));

        /* create url for link in log screen
         * need to set grouptype to 0 to allow take attendance page to be called
         * from report/log page */

        $params = array(
            'sessionid' => $this->pageparams->sessionid,
            'grouptype' => 0);

        // Log the change.
        $event = \mod_attendance\event\attendance_taken_by_student::create(array(
            'objectid' => $this->id,
            'context' => $this->context,
            'other' => $params));
        $event->add_record_snapshot('course_modules', $this->cm);
        $event->add_record_snapshot('attendance_sessions', $session);
        $event->add_record_snapshot('attendance_log', $record);
        $event->trigger();

        return true;
    }

    /**
     * Take attendance from form data.
     *
     * @param stdClass $data
     */
    public function take_from_form_data($data) {
        global $USER;
        // WARNING - $data is unclean - comes from direct $_POST - ideally needs a rewrite but we do some cleaning below.

        $statuses = implode(',', array_keys( (array)$this->get_statuses() ));
        $now = time();
        $sesslog = array();

        $formdata = (array)$data;

        foreach ($formdata as $key => $value) {
            // Look at Remarks field because the user options may not be passed if empty.
            if (substr($key, 0, 7) == 'remarks') {
                $sid = substr($key, 7);
                if (!(is_numeric($sid))) { // Sanity check on $sid.
                    throw new moodle_exception('nonnumericid', 'attendance');
                }
                $sesslog[$sid] = new stdClass();
                $sesslog[$sid]->studentid = $sid; // We check is_numeric on this above.
                if (array_key_exists('user' . $sid, $formdata) && is_numeric($formdata['user' . $sid])) {
                    $sesslog[$sid]->statusid = $formdata['user' . $sid];
                }
                $sesslog[$sid]->statusset = $statuses;
                $sesslog[$sid]->remarks = $value;
                $sesslog[$sid]->sessionid = $this->pageparams->sessionid;
                $sesslog[$sid]->timetaken = $now;
                $sesslog[$sid]->takenby = $USER->id;
            }
        }

        $this->save_log($sesslog);
    }

    /**
     * Helper function to save attendance and trigger events.
     *
     * @param array $sesslog
     * @throws coding_exception
     * @throws dml_exception
     */
    public function save_log($sesslog) {
        global $DB, $USER;
        // Get existing session log.
        $dbsesslog = $this->get_session_log($this->pageparams->sessionid);
        foreach ($sesslog as $log) {
            // Don't save a record if no statusid or remark.
            if (!empty($log->statusid) || !empty($log->remarks)) {
                if (array_key_exists($log->studentid, $dbsesslog)) {
                    // Check if anything important has changed before updating record.
                    // Don't update timetaken/takenby records if nothing has changed.
                    if ($dbsesslog[$log->studentid]->remarks <> $log->remarks ||
                        $dbsesslog[$log->studentid]->statusid <> $log->statusid ||
                        $dbsesslog[$log->studentid]->statusset <> $log->statusset) {

                        $log->id = $dbsesslog[$log->studentid]->id;
                        $DB->update_record('attendance_log', $log);
                    }
                } else {
                    $DB->insert_record('attendance_log', $log, false);
                }
            }
        }

        $session = $this->get_session_info($this->pageparams->sessionid);
        $session->lasttaken = time();
        $session->lasttakenby = $USER->id;

        $DB->update_record('attendance_sessions', $session);

        if ($this->grade != 0) {
            $this->update_users_grade(array_keys($sesslog));
        }

        // Create url for link in log screen.
        $params = array(
            'sessionid' => $this->pageparams->sessionid,
            'grouptype' => $this->pageparams->grouptype);
        $event = \mod_attendance\event\attendance_taken::create(array(
            'objectid' => $this->id,
            'context' => $this->context,
            'other' => $params));
        $event->add_record_snapshot('course_modules', $this->cm);
        $event->add_record_snapshot('attendance_sessions', $session);
        $event->trigger();
    }

    /**
     * Get users with enrolment status (Feature request MDL-27591)
     *
     * @param int $groupid
     * @param int $page
     * @return array
     */
    public function get_users($groupid = 0, $page = 1) : array {
        global $DB;

        $fields = array('username' , 'idnumber' , 'institution' , 'department', 'city', 'country');
        // Get user identity fields if required - doesn't return original $fields array.
        $extrafields = get_extra_user_fields($this->context, $fields);
        $fields = array_merge($fields, $extrafields);

        $userfields = user_picture::fields('u', $fields);

        if (empty($this->pageparams->sort)) {
            $this->pageparams->sort = ATT_SORT_DEFAULT;
        }
        if ($this->pageparams->sort == ATT_SORT_FIRSTNAME) {
            $orderby = $DB->sql_fullname('u.firstname', 'u.lastname') . ', u.id';
        } else if ($this->pageparams->sort == ATT_SORT_LASTNAME) {
            $orderby = 'u.lastname, u.firstname, u.id';
        } else {
            list($orderby, $sortparams) = users_order_by_sql('u');
        }

        if ($page) {
            $usersperpage = $this->pageparams->perpage;
            if (!empty($this->cm->groupingid)) {
                $startusers = ($page - 1) * $usersperpage;
                if ($groupid == 0) {
                    $groups = array_keys(groups_get_all_groups($this->cm->course, 0, $this->cm->groupingid, 'g.id'));
                } else {
                    $groups = $groupid;
                }
                $users = get_users_by_capability($this->context, 'mod/attendance:canbelisted',
                    $userfields,
                    $orderby, $startusers, $usersperpage, $groups,
                    '', false, true);
            } else {
                $startusers = ($page - 1) * $usersperpage;
                $users = get_enrolled_users($this->context, 'mod/attendance:canbelisted', $groupid, $userfields,
                    $orderby, $startusers, $usersperpage);
            }
        } else {
            if (!empty($this->cm->groupingid)) {
                if ($groupid == 0) {
                    $groups = array_keys(groups_get_all_groups($this->cm->course, 0, $this->cm->groupingid, 'g.id'));
                } else {
                    $groups = $groupid;
                }
                $users = get_users_by_capability($this->context, 'mod/attendance:canbelisted',
                    $userfields,
                    $orderby, '', '', $groups,
                    '', false, true);
            } else {
                $users = get_enrolled_users($this->context, 'mod/attendance:canbelisted', $groupid, $userfields, $orderby);
            }
        }

        // Add a flag to each user indicating whether their enrolment is active.
        if (!empty($users)) {
            list($sql, $params) = $DB->get_in_or_equal(array_keys($users), SQL_PARAMS_NAMED, 'usid0');

            // See CONTRIB-4868.
            $mintime = 'MIN(CASE WHEN (ue.timestart > :zerotime) THEN ue.timestart ELSE ue.timecreated END)';
            $maxtime = 'CASE WHEN MIN(ue.timeend) = 0 THEN 0 ELSE MAX(ue.timeend) END';

            // See CONTRIB-3549.
            $sql = "SELECT ue.userid, MIN(ue.status) as status,
                           $mintime AS mintime,
                           $maxtime AS maxtime
                      FROM {user_enrolments} ue
                      JOIN {enrol} e ON e.id = ue.enrolid
                     WHERE ue.userid $sql
                           AND e.status = :estatus
                           AND e.courseid = :courseid
                  GROUP BY ue.userid";
            $params += array('zerotime' => 0, 'estatus' => ENROL_INSTANCE_ENABLED, 'courseid' => $this->course->id);
            $enrolments = $DB->get_records_sql($sql, $params);

            foreach ($users as $user) {
                $users[$user->id]->fullname = fullname($user);
                $users[$user->id]->enrolmentstatus = $enrolments[$user->id]->status;
                $users[$user->id]->enrolmentstart = $enrolments[$user->id]->mintime;
                $users[$user->id]->enrolmentend = $enrolments[$user->id]->maxtime;
                $users[$user->id]->type = 'standard'; // Mark as a standard (not a temporary) user.
            }
        }

        // Add the 'temporary' users to this list.
        $tempusers = $DB->get_records('attendance_tempusers', array('courseid' => $this->course->id));
        foreach ($tempusers as $tempuser) {
            $users[$tempuser->studentid] = self::tempuser_to_user($tempuser);
        }

        return $users;
    }

    /**
     * Convert a tempuser record into a user object.
     *
     * @param stdClass $tempuser
     * @return object
     */
    protected static function tempuser_to_user($tempuser) {
        global $CFG;

        $ret = (object)array(
            'id' => $tempuser->studentid,
            'firstname' => $tempuser->fullname,
            'email' => $tempuser->email,
            'username' => '',
            'enrolmentstatus' => 0,
            'enrolmentstart' => 0,
            'enrolmentend' => 0,
            'picture' => 0,
            'type' => 'temporary',
        );
        $allfields = get_all_user_name_fields();
        if (!empty($CFG->showuseridentity)) {
            $allfields = array_merge($allfields, explode(',', $CFG->showuseridentity));
        }

        foreach ($allfields as $namefield) {
            if (!isset($ret->$namefield)) {
                $ret->$namefield = '';
            }
        }

        return $ret;
    }

    /**
     * Get user and include extra info.
     *
     * @param int $userid
     * @return mixed|object
     */
    public function get_user($userid) {
        global $DB;

        $user = $DB->get_record('user', array('id' => $userid), '*', MUST_EXIST);

        // Look for 'temporary' users and return their details from the attendance_tempusers table.
        if ($user->idnumber == 'tempghost') {
            $tempuser = $DB->get_record('attendance_tempusers', array('studentid' => $userid), '*', MUST_EXIST);
            return self::tempuser_to_user($tempuser);
        }

        $user->type = 'standard';

        // See CONTRIB-4868.
        $mintime = 'MIN(CASE WHEN (ue.timestart > :zerotime) THEN ue.timestart ELSE ue.timecreated END)';
        $maxtime = 'CASE WHEN MIN(ue.timeend) = 0 THEN 0 ELSE MAX(ue.timeend) END';

        $sql = "SELECT ue.userid, ue.status,
                       $mintime AS mintime,
                       $maxtime AS maxtime
                  FROM {user_enrolments} ue
                  JOIN {enrol} e ON e.id = ue.enrolid
                 WHERE ue.userid = :uid
                       AND e.status = :estatus
                       AND e.courseid = :courseid
              GROUP BY ue.userid, ue.status";
        $params = array('zerotime' => 0, 'uid' => $userid, 'estatus' => ENROL_INSTANCE_ENABLED, 'courseid' => $this->course->id);
        $enrolments = $DB->get_record_sql($sql, $params);
        if (!empty($enrolments)) {
            $user->enrolmentstatus = $enrolments->status;
            $user->enrolmentstart = $enrolments->mintime;
            $user->enrolmentend = $enrolments->maxtime;
        } else {
            $user->enrolmentstatus = '';
            $user->enrolmentstart = 0;
            $user->enrolmentend = 0;
        }

        return $user;
    }

    /**
     * Get possible statuses.
     *
     * @param bool $onlyvisible
     * @param bool $allsets
     * @return array
     */
    public function get_statuses($onlyvisible = true, $allsets = false) : array {
        if (!isset($this->statuses)) {
            // Get the statuses for the current set only.
            $statusset = 0;
            if (isset($this->pageparams->statusset)) {
                $statusset = $this->pageparams->statusset;
            } else if (isset($this->pageparams->sessionid)) {
                $sessioninfo = $this->get_session_info($this->pageparams->sessionid);
                $statusset = $sessioninfo->statusset;
            }
            $this->statuses = attendance_get_statuses($this->id, $onlyvisible, $statusset);
            $this->allstatuses = attendance_get_statuses($this->id, $onlyvisible);
        }

        // Return all sets, if requested.
        if ($allsets) {
            return $this->allstatuses;
        }
        return $this->statuses;
    }

    /**
     * Get session info.
     * @param int $sessionid
     * @return mixed
     */
    public function get_session_info($sessionid) {
        global $DB;

        if (!array_key_exists($sessionid, $this->sessioninfo)) {
            $this->sessioninfo[$sessionid] = $DB->get_record('attendance_sessions', array('id' => $sessionid));
        }
        if (empty($this->sessioninfo[$sessionid]->description)) {
            $this->sessioninfo[$sessionid]->description = get_string('nodescription', 'attendance');
        } else {
            $this->sessioninfo[$sessionid]->description = file_rewrite_pluginfile_urls($this->sessioninfo[$sessionid]->description,
                'pluginfile.php', $this->context->id, 'mod_attendance', 'session', $this->sessioninfo[$sessionid]->id);
        }
        return $this->sessioninfo[$sessionid];
    }

    /**
     * Get sessions info
     *
     * @param array $sessionids
     * @return array
     */
    public function get_sessions_info($sessionids) : array {
        global $DB;

        list($sql, $params) = $DB->get_in_or_equal($sessionids);
        $sessions = $DB->get_records_select('attendance_sessions', "id $sql", $params, 'sessdate asc');

        foreach ($sessions as $sess) {
            if (empty($sess->description)) {
                $sess->description = get_string('nodescription', 'attendance');
            } else {
                $sess->description = file_rewrite_pluginfile_urls($sess->description,
                    'pluginfile.php', $this->context->id, 'mod_attendance', 'session', $sess->id);
            }
        }

        return $sessions;
    }

    /**
     * Get log.
     *
     * @param int $sessionid
     * @return array
     */
    public function get_session_log($sessionid) : array {
        global $DB;

        return $DB->get_records('attendance_log', array('sessionid' => $sessionid), '', 'studentid,statusid,remarks,id,statusset');
    }

    /**
     * Update user grade.
     * @param array $userids
     */
    public function update_users_grade($userids) {
        attendance_update_users_grade($this, $userids);
    }

    /**
     * Get filtered log.
     * @param int $userid
     * @return array
     */
    public function get_user_filtered_sessions_log($userid) : array {
        global $DB;

        if ($this->pageparams->startdate && $this->pageparams->enddate) {
            $where = "ats.attendanceid = :aid AND ats.sessdate >= :csdate AND
                      ats.sessdate >= :sdate AND ats.sessdate < :edate";
        } else {
            $where = "ats.attendanceid = :aid AND ats.sessdate >= :csdate";
        }
        if ($this->get_group_mode()) {
            $sql = "SELECT ats.id, ats.sessdate, ats.groupid, al.statusid, al.remarks,
                           ats.preventsharediptime, ats.preventsharedip
                  FROM {attendance_sessions} ats
                  JOIN {attendance_log} al ON ats.id = al.sessionid AND al.studentid = :uid
                  LEFT JOIN {groups_members} gm ON gm.userid = al.studentid AND gm.groupid = ats.groupid
                 WHERE $where AND (ats.groupid = 0 or gm.id is NOT NULL)
              ORDER BY ats.sessdate ASC";

            $params = array(
                'uid'       => $userid,
                'aid'       => $this->id,
                'csdate'    => $this->course->startdate,
                'sdate'     => $this->pageparams->startdate,
                'edate'     => $this->pageparams->enddate);

        } else {
            $sql = "SELECT ats.id, ats.sessdate, ats.groupid, al.statusid, al.remarks,
                           ats.preventsharediptime, ats.preventsharedip
                  FROM {attendance_sessions} ats
                  JOIN {attendance_log} al
                    ON ats.id = al.sessionid AND al.studentid = :uid
                 WHERE $where
              ORDER BY ats.sessdate ASC";

            $params = array(
                'uid'       => $userid,
                'aid'       => $this->id,
                'csdate'    => $this->course->startdate,
                'sdate'     => $this->pageparams->startdate,
                'edate'     => $this->pageparams->enddate);
        }
        $sessions = $DB->get_records_sql($sql, $params);

        return $sessions;
    }

    /**
     * Get filtered log extended.
     * @param int $userid
     * @return array
     */
    public function get_user_filtered_sessions_log_extended($userid) : array {
        global $DB;
        // All taked sessions (including previous groups).

        if ($this->pageparams->startdate && $this->pageparams->enddate) {
            $where = "ats.attendanceid = :aid AND ats.sessdate >= :csdate AND
                      ats.sessdate >= :sdate AND ats.sessdate < :edate";
        } else {
            $where = "ats.attendanceid = :aid AND ats.sessdate >= :csdate";
        }

        // We need to add this concatination so that moodle will use it as the array index that is a string.
        // If the array's index is a number it will not merge entries.
        // It would be better as a UNION query but unfortunatly MS SQL does not seem to support doing a
        // DISTINCT on a the description field.
        $id = $DB->sql_concat(':value', 'ats.id');
        if ($this->get_group_mode()) {
            $sql = "SELECT $id, ats.id, ats.groupid, ats.sessdate, ats.duration, ats.description,
                           al.statusid, al.remarks, ats.studentscanmark, ats.autoassignstatus,
                           ats.preventsharedip, ats.preventsharediptime, ats.rotateqrcode
                      FROM {attendance_sessions} ats
                RIGHT JOIN {attendance_log} al
                        ON ats.id = al.sessionid AND al.studentid = :uid
                 LEFT JOIN {groups_members} gm ON gm.userid = al.studentid AND gm.groupid = ats.groupid
                     WHERE $where AND (ats.groupid = 0 or gm.id is NOT NULL)
                  ORDER BY ats.sessdate ASC";
        } else {
            $sql = "SELECT $id, ats.id, ats.groupid, ats.sessdate, ats.duration, ats.description, ats.statusset,
                           al.statusid, al.remarks, ats.studentscanmark, ats.autoassignstatus,
                           ats.preventsharedip, ats.preventsharediptime, ats.rotateqrcode
                      FROM {attendance_sessions} ats
                RIGHT JOIN {attendance_log} al
                        ON ats.id = al.sessionid AND al.studentid = :uid
                     WHERE $where
                  ORDER BY ats.sessdate ASC";
        }

        $params = array(
            'uid'       => $userid,
            'aid'       => $this->id,
            'csdate'    => $this->course->startdate,
            'sdate'     => $this->pageparams->startdate,
            'edate'     => $this->pageparams->enddate,
            'value'     => 'c');
        $sessions = $DB->get_records_sql($sql, $params);

        // All sessions for current groups.

        $groups = array_keys(groups_get_all_groups($this->course->id, $userid));
        $groups[] = 0;
        list($gsql, $gparams) = $DB->get_in_or_equal($groups, SQL_PARAMS_NAMED, 'gid0');

        if ($this->pageparams->startdate && $this->pageparams->enddate) {
            $where = "ats.attendanceid = :aid AND ats.sessdate >= :csdate AND
                      ats.sessdate >= :sdate AND ats.sessdate < :edate AND ats.groupid $gsql";
        } else {
            $where = "ats.attendanceid = :aid AND ats.sessdate >= :csdate AND ats.groupid $gsql";
        }
        $sql = "SELECT $id, ats.id, ats.groupid, ats.sessdate, ats.duration, ats.description, ats.statusset,
                       al.statusid, al.remarks, ats.studentscanmark, ats.autoassignstatus,
                       ats.preventsharedip, ats.preventsharediptime, ats.rotateqrcode
                  FROM {attendance_sessions} ats
             LEFT JOIN {attendance_log} al
                    ON ats.id = al.sessionid AND al.studentid = :uid
                 WHERE $where
              ORDER BY ats.sessdate ASC";

        $params = array_merge($params, $gparams);
        $sessions = array_merge($sessions, $DB->get_records_sql($sql, $params));

        foreach ($sessions as $sess) {
            if (empty($sess->description)) {
                $sess->description = get_string('nodescription', 'attendance');
            } else {
                $sess->description = file_rewrite_pluginfile_urls($sess->description,
                    'pluginfile.php', $this->context->id, 'mod_attendance', 'session', $sess->id);
            }
        }

        return $sessions;
    }

    /**
     * Delete sessions.
     * @param array $sessionsids
     */
    public function delete_sessions($sessionsids) {
        global $DB;
        if (attendance_existing_calendar_events_ids($sessionsids)) {
            attendance_delete_calendar_events($sessionsids);
        }

        list($sql, $params) = $DB->get_in_or_equal($sessionsids);
        $DB->delete_records_select('attendance_log', "sessionid $sql", $params);
        $DB->delete_records_list('attendance_sessions', 'id', $sessionsids);
        $event = \mod_attendance\event\session_deleted::create(array(
            'objectid' => $this->id,
            'context' => $this->context,
            'other' => array('info' => implode(', ', $sessionsids))));
        $event->add_record_snapshot('course_modules', $this->cm);
        $event->trigger();
    }

    /**
     * Update duration.
     *
     * @param array $sessionsids
     * @param int $duration
     */
    public function update_sessions_duration($sessionsids, $duration) {
        global $DB;

        $now = time();
        $sessions = $DB->get_recordset_list('attendance_sessions', 'id', $sessionsids);
        foreach ($sessions as $sess) {
            $sess->duration = $duration;
            $sess->timemodified = $now;
            $DB->update_record('attendance_sessions', $sess);
            if ($sess->caleventid) {
                attendance_update_calendar_event($sess);
            }
            $event = \mod_attendance\event\session_duration_updated::create(array(
                'objectid' => $this->id,
                'context' => $this->context,
                'other' => array('info' => implode(', ', $sessionsids))));
            $event->add_record_snapshot('course_modules', $this->cm);
            $event->add_record_snapshot('attendance_sessions', $sess);
            $event->trigger();
        }
        $sessions->close();
    }

    /**
     * Check if the email address is already in use by either another temporary user,
     * or a real user.
     *
     * @param string $email the address to check for
     * @param int $tempuserid optional the ID of the temporary user (to avoid matching against themself)
     * @return null|string the error message to display, null if there is no error
     */
    public static function check_existing_email($email, $tempuserid = 0) {
        global $DB;

        if (empty($email)) {
            return null; // Fine to create temporary users without an email address.
        }
        if ($tempuser = $DB->get_record('attendance_tempusers', array('email' => $email), 'id')) {
            if ($tempuser->id != $tempuserid) {
                return get_string('tempexists', 'attendance');
            }
        }
        if ($DB->record_exists('user', array('email' => $email))) {
            return get_string('userexists', 'attendance');
        }

        return null;
    }

    /**
     * Gets the status to use when auto-marking.
     *
     * @param int $time the time the user first accessed the course.
     * @param int $sessionid the related sessionid to check.
     * @return int the statusid to assign to this user.
     */
    public function get_automark_status($time, $sessionid) {
        $statuses = $this->get_statuses();
        // Statuses are returned highest grade first, find the first high grade we can assign to this user.

        // Get status to use when unmarked.
        $session = $this->sessioninfo[$sessionid];
        $duration = $session->duration;
        if (empty($duration)) {
            $duration = get_config('attendance', 'studentscanmarksessiontimeend') * 60;
        }
        if ($time > $session->sessdate + $duration) {
            // This session closed after the users access - use the unmarked state.
            foreach ($statuses as $status) {
                if (!empty($status->setunmarked)) {
                    return $status->id;
                }
            }
        } else {
            foreach ($statuses as $status) {
                if ($status->studentavailability !== '0' &&
                    $this->sessioninfo[$sessionid]->sessdate + ($status->studentavailability * 60) > $time) {

                    // Found first status we could set.
                    return $status->id;
                }
            }
        }
        return;
    }

    /**
     * Gets the lowgrade threshold to use.
     *
     */
    public function get_lowgrade_threshold() {
        if (!isset($this->lowgradethreshold)) {
            $this->lowgradethreshold = 1;

            if ($this->grade > 0) {
                $gradeitem = grade_item::fetch(array('courseid' => $this->course->id, 'itemtype' => 'mod',
                    'itemmodule' => 'attendance', 'iteminstance' => $this->id));
                if ($gradeitem->gradepass > 0 && $gradeitem->grademax != $gradeitem->grademin) {
                    $this->lowgradethreshold = ($gradeitem->gradepass - $gradeitem->grademin) /
                        ($gradeitem->grademax - $gradeitem->grademin);
                }
            }
        }

        return $this->lowgradethreshold;
    }
}

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
 * Attendance module renderable components are defined here
 *
 * @package    mod_attendance
 * @copyright  2011 Artem Andreev <andreev.artem@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__).'/locallib.php');


/**
 * Represents info about attendance tabs.
 *
 * Proxy class for security reasons (renderers must not have access to all attendance methods)
 *
 * @copyright  2011 Artem Andreev <andreev.artem@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */
class attendance_tabs implements renderable {
    /** Sessions tab */
    const TAB_SESSIONS      = 1;
    /** Add tab */
    const TAB_ADD           = 2;
    /** Rerort tab */
    const TAB_REPORT        = 3;
    /** Export tab */
    const TAB_EXPORT        = 4;
    /** Preferences tab */
    const TAB_PREFERENCES   = 5;
    /** Temp users tab */
    const TAB_TEMPORARYUSERS = 6; // Tab for managing temporary users.
    /** Update tab */
    const TAB_UPDATE        = 7;
    /** Warnings tab */
    const TAB_WARNINGS = 8;
    /** Absentee tab */
    const TAB_ABSENTEE      = 9;
    /** @var int current tab */
    public $currenttab;

    /** @var stdClass attendance */
    private $att;

    /**
     * Prepare info about sessions for attendance taking into account view parameters.
     *
     * @param mod_attendance_structure $att
     * @param int $currenttab - one of attendance_tabs constants
     */
    public function  __construct(mod_attendance_structure $att, $currenttab=null) {
        $this->att = $att;
        $this->currenttab = $currenttab;
    }

    /**
     * Return array of rows where each row is an array of tab objects
     * taking into account permissions of current user
     */
    public function get_tabs() {
        $toprow = array();
        $context = $this->att->context;
        $capabilities = array(
            'mod/attendance:manageattendances',
            'mod/attendance:takeattendances',
            'mod/attendance:changeattendances'
        );
        if (has_any_capability($capabilities, $context)) {
            $toprow[] = new tabobject(self::TAB_SESSIONS, $this->att->url_manage()->out(),
                            get_string('sessions', 'attendance'));
        }

        if (has_capability('mod/attendance:manageattendances', $context)) {
            $toprow[] = new tabobject(self::TAB_ADD,
                            $this->att->url_sessions()->out(true,
                                array('action' => mod_attendance_sessions_page_params::ACTION_ADD)),
                                get_string('addsession', 'attendance'));
        }
        if (has_capability('mod/attendance:viewreports', $context)) {
            $toprow[] = new tabobject(self::TAB_REPORT, $this->att->url_report()->out(),
                            get_string('report', 'attendance'));
        }

        if (has_capability('mod/attendance:viewreports', $context) &&
            get_config('attendance', 'enablewarnings')) {
            $toprow[] = new tabobject(self::TAB_ABSENTEE, $this->att->url_absentee()->out(),
                get_string('absenteereport', 'attendance'));
        }

        if (has_capability('mod/attendance:export', $context)) {
            $toprow[] = new tabobject(self::TAB_EXPORT, $this->att->url_export()->out(),
                            get_string('export', 'attendance'));
        }

        if (has_capability('mod/attendance:changepreferences', $context)) {
            $toprow[] = new tabobject(self::TAB_PREFERENCES, $this->att->url_preferences()->out(),
                            get_string('statussetsettings', 'attendance'));

            if (get_config('attendance', 'enablewarnings')) {
                $toprow[] = new tabobject(self::TAB_WARNINGS, $this->att->url_warnings()->out(),
                    get_string('warnings', 'attendance'));
            }
        }
        if (has_capability('mod/attendance:managetemporaryusers', $context)) {
            $toprow[] = new tabobject(self::TAB_TEMPORARYUSERS, $this->att->url_managetemp()->out(),
                            get_string('tempusers', 'attendance'));
        }
        if ($this->currenttab == self::TAB_UPDATE && has_capability('mod/attendance:manageattendances', $context)) {
            $toprow[] = new tabobject(self::TAB_UPDATE,
                            $this->att->url_sessions()->out(true,
                                array('action' => mod_attendance_sessions_page_params::ACTION_UPDATE)),
                                get_string('changesession', 'attendance'));
        }

        return array($toprow);
    }
}

/**
 * Class attendance_filter_controls
 * @copyright  2011 Artem Andreev <andreev.artem@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class attendance_filter_controls implements renderable {
    /** @var int current view mode */
    public $pageparams;
    /** @var stdclass  */
    public $cm;
    /** @var int  */
    public $curdate;
    /** @var int  */
    public $prevcur;
    /** @var int  */
    public $nextcur;
    /** @var string  */
    public $curdatetxt;
    /** @var boolean  */
    public $reportcontrol;
    /** @var string  */
    private $urlpath;
    /** @var array  */
    private $urlparams;
    /** @var mod_attendance_structure */
    public $att;

    /**
     * attendance_filter_controls constructor.
     * @param mod_attendance_structure $att
     * @param bool $report
     */
    public function __construct(mod_attendance_structure $att, $report = false) {
        global $PAGE;

        $this->pageparams = $att->pageparams;

        $this->cm = $att->cm;

        // This is a report control only if $reports is true and the attendance block can be graded.
        $this->reportcontrol = $report;

        $this->curdate = $att->pageparams->curdate;

        $date = usergetdate($att->pageparams->curdate);
        $mday = $date['mday'];
        $mon = $date['mon'];
        $year = $date['year'];

        switch ($this->pageparams->view) {
            case ATT_VIEW_DAYS:
                $format = get_string('strftimedm', 'attendance');
                $this->prevcur = make_timestamp($year, $mon, $mday - 1);
                $this->nextcur = make_timestamp($year, $mon, $mday + 1);
                $this->curdatetxt = userdate($att->pageparams->startdate, $format);
                break;
            case ATT_VIEW_WEEKS:
                $format = get_string('strftimedm', 'attendance');
                $this->prevcur = $att->pageparams->startdate - WEEKSECS;
                $this->nextcur = $att->pageparams->startdate + WEEKSECS;
                $this->curdatetxt = userdate($att->pageparams->startdate, $format).
                                    " - ".userdate($att->pageparams->enddate, $format);
                break;
            case ATT_VIEW_MONTHS:
                $format = '%B';
                $this->prevcur = make_timestamp($year, $mon - 1);
                $this->nextcur = make_timestamp($year, $mon + 1);
                $this->curdatetxt = userdate($att->pageparams->startdate, $format);
                break;
        }

        $this->urlpath = $PAGE->url->out_omit_querystring();
        $params = $att->pageparams->get_significant_params();
        $params['id'] = $att->cm->id;
        $this->urlparams = $params;

        $this->att = $att;
    }

    /**
     * Helper function for url.
     *
     * @param array $params
     * @return moodle_url
     */
    public function url($params=array()) {
        $params = array_merge($this->urlparams, $params);

        return new moodle_url($this->urlpath, $params);
    }

    /**
     * Helper function for url path.
     * @return string
     */
    public function url_path() {
        return $this->urlpath;
    }

    /**
     * Helper function for url_params.
     * @param array $params
     * @return array
     */
    public function url_params($params=array()) {
        $params = array_merge($this->urlparams, $params);

        return $params;
    }

    /**
     * Return groupmode.
     * @return int
     */
    public function get_group_mode() {
        return $this->att->get_group_mode();
    }

    /**
     * Return groupslist.
     * @return mixed
     */
    public function get_sess_groups_list() {
        return $this->att->pageparams->get_sess_groups_list();
    }

    /**
     * Get current session type.
     * @return mixed
     */
    public function get_current_sesstype() {
        return $this->att->pageparams->get_current_sesstype();
    }
}

/**
 * Represents info about attendance sessions taking into account view parameters.
 *
 * @copyright  2011 Artem Andreev <andreev.artem@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class attendance_manage_data implements renderable {
    /** @var array of sessions*/
    public $sessions;

    /** @var int number of hidden sessions (sessions before $course->startdate)*/
    public $hiddensessionscount;
    /** @var array  */
    public $groups;
    /** @var  int */
    public $hiddensesscount;

    /** @var mod_attendance_structure */
    public $att;
    /**
     * Prepare info about attendance sessions taking into account view parameters.
     *
     * @param mod_attendance_structure $att instance
     */
    public function __construct(mod_attendance_structure $att) {

        $this->sessions = $att->get_filtered_sessions();

        $this->groups = groups_get_all_groups($att->course->id);

        $this->hiddensessionscount = $att->get_hidden_sessions_count();

        $this->att = $att;
    }

    /**
     * Helper function to return urls.
     * @param int $sessionid
     * @param int $grouptype
     * @return mixed
     */
    public function url_take($sessionid, $grouptype) {
        return url_helpers::url_take($this->att, $sessionid, $grouptype);
    }

    /**
     * Must be called without or with both parameters
     *
     * @param int $sessionid
     * @param null $action
     * @return mixed
     */
    public function url_sessions($sessionid=null, $action=null) {
        return url_helpers::url_sessions($this->att, $sessionid, $action);
    }
}

/**
 * class take data.
 *
 * @copyright  2011 Artem Andreev <andreev.artem@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class attendance_take_data implements renderable {
    /** @var array  */
    public $users;
    /** @var array|null|stdClass  */
    public $pageparams;
    /** @var int  */
    public $groupmode;
    /** @var stdclass  */
    public $cm;
    /** @var array  */
    public $statuses;
    /** @var mixed  */
    public $sessioninfo;
    /** @var array  */
    public $sessionlog;
    /** @var array  */
    public $sessions4copy;
    /** @var bool  */
    public $updatemode;
    /** @var string  */
    private $urlpath;
    /** @var array */
    private $urlparams;
    /** @var mod_attendance_structure  */
    public $att;

    /**
     * attendance_take_data constructor.
     * @param mod_attendance_structure $att
     */
    public function  __construct(mod_attendance_structure $att) {
        if ($att->pageparams->grouptype) {
            $this->users = $att->get_users($att->pageparams->grouptype, $att->pageparams->page);
        } else {
            $this->users = $att->get_users($att->pageparams->group, $att->pageparams->page);
        }

        $this->pageparams = $att->pageparams;

        $this->groupmode = $att->get_group_mode();
        $this->cm = $att->cm;

        $this->statuses = $att->get_statuses();

        $this->sessioninfo = $att->get_session_info($att->pageparams->sessionid);
        $this->updatemode = $this->sessioninfo->lasttaken > 0;

        if (isset($att->pageparams->copyfrom)) {
            $this->sessionlog = $att->get_session_log($att->pageparams->copyfrom);
        } else if ($this->updatemode) {
            $this->sessionlog = $att->get_session_log($att->pageparams->sessionid);
        } else {
            $this->sessionlog = array();
        }

        if (!$this->updatemode) {
            $this->sessions4copy = $att->get_today_sessions_for_copy($this->sessioninfo);
        }

        $this->urlpath = $att->url_take()->out_omit_querystring();
        $params = $att->pageparams->get_significant_params();
        $params['id'] = $att->cm->id;
        $this->urlparams = $params;

        $this->att = $att;
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
     * Url view helper.
     * @param array $params
     * @return mixed
     */
    public function url_view($params=array()) {
        return url_helpers::url_view($this->att, $params);
    }

    /**
     * Url path helper.
     * @return string
     */
    public function url_path() {
        return $this->urlpath;
    }
}

/**
 * Class user data.
 *
 * @copyright  2011 Artem Andreev <andreev.artem@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class attendance_user_data implements renderable {
    /** @var mixed|object  */
    public $user;
    /** @var array|null|stdClass  */
    public $pageparams;
    /** @var array  */
    public $statuses;
    /** @var array  */
    public $summary;
    /** @var attendance_filter_controls  */
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
     */
    public function  __construct(mod_attendance_structure $att, $userid) {
        $this->user = $att->get_user($userid);

        $this->pageparams = $att->pageparams;

        if ($this->pageparams->mode == mod_attendance_view_page_params::MODE_THIS_COURSE) {
            $this->statuses = $att->get_statuses(true, true);

            $this->summary = new mod_attendance_summary($att->id, array($userid), $att->pageparams->startdate,
                                                        $att->pageparams->enddate);

            $this->filtercontrols = new attendance_filter_controls($att);

            $this->sessionslog = $att->get_user_filtered_sessions_log_extended($userid);

            $this->groups = groups_get_all_groups($att->course->id);
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
     * url helper.
     * @return moodle_url
     */
    public function url() {
        return new moodle_url($this->urlpath, $this->urlparams);
    }
}

/**
 * Class report data.
 *
 * @copyright  2011 Artem Andreev <andreev.artem@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class attendance_report_data implements renderable {
    /** @var array|null|stdClass  */
    public $pageparams;
    /** @var array  */
    public $users;
    /** @var array  */
    public $groups;
    /** @var array  */
    public $sessions;
    /** @var array  */
    public $statuses;
    /** @var array includes disablrd/deleted statuses. */
    public $allstatuses;
    /** @var array  */
    public $usersgroups = array();
    /** @var array  */
    public $sessionslog = array();
    /** @var array|mod_attendance_summary  */
    public $summary = array();
    /** @var mod_attendance_structure  */
    public $att;

    /**
     * attendance_report_data constructor.
     * @param mod_attendance_structure $att
     */
    public function  __construct(mod_attendance_structure $att) {
        $currenttime = time();
        if ($att->pageparams->view == ATT_VIEW_NOTPRESENT) {
            $att->pageparams->enddate = $currenttime;
        }

        $this->pageparams = $att->pageparams;

        $this->users = $att->get_users($att->pageparams->group, $att->pageparams->page);

        if (isset($att->pageparams->userids)) {
            foreach ($this->users as $key => $user) {
                if (!in_array($user->id, $att->pageparams->userids)) {
                    unset($this->users[$key]);
                }
            }
        }

        $this->groups = groups_get_all_groups($att->course->id);

        $this->sessions = $att->get_filtered_sessions();

        $this->statuses = $att->get_statuses(true, true);
        $this->allstatuses = $att->get_statuses(false, true);

        if ($att->pageparams->view == ATT_VIEW_SUMMARY) {
            $this->summary = new mod_attendance_summary($att->id);
        } else {
            $this->summary = new mod_attendance_summary($att->id, array_keys($this->users),
                                                        $att->pageparams->startdate, $att->pageparams->enddate);
        }

        foreach ($this->users as $key => $user) {
            $usersummary = $this->summary->get_taken_sessions_summary_for($user->id);
            if ($att->pageparams->view != ATT_VIEW_NOTPRESENT ||
                    $usersummary->takensessionspoints < $usersummary->takensessionsmaxpoints ||
                    $usersummary->takensessionsmaxpoints == 0) {
                $this->usersgroups[$user->id] = groups_get_all_groups($att->course->id, $user->id);

                $this->sessionslog[$user->id] = $att->get_user_filtered_sessions_log($user->id);
            } else {
                unset($this->users[$key]);
            }
        }

        $this->att = $att;
    }

    /**
     * url take helper.
     * @param int $sessionid
     * @param int $grouptype
     * @return mixed
     */
    public function url_take($sessionid, $grouptype) {
        return url_helpers::url_take($this->att, $sessionid, $grouptype);
    }

    /**
     * url view helper.
     * @param array $params
     * @return mixed
     */
    public function url_view($params=array()) {
        return url_helpers::url_view($this->att, $params);
    }

    /**
     * url helper.
     * @param array $params
     * @return moodle_url
     */
    public function url($params=array()) {
        $params = array_merge($params, $this->pageparams->get_significant_params());

        return $this->att->url_report($params);
    }

}

/**
 * Class preferences data.
 *
 * @copyright  2011 Artem Andreev <andreev.artem@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class attendance_preferences_data implements renderable {
    /** @var array  */
    public $statuses;
    /** @var mod_attendance_structure  */
    private $att;
    /** @var array  */
    public $errors;

    /**
     * attendance_preferences_data constructor.
     * @param mod_attendance_structure $att
     * @param array $errors
     */
    public function __construct(mod_attendance_structure $att, $errors) {
        $this->statuses = $att->get_statuses(false);
        $this->errors = $errors;

        foreach ($this->statuses as $st) {
            $st->haslogs = attendance_has_logs_for_status($st->id);
        }

        $this->att = $att;
    }

    /**
     * url helper function
     * @param array $params
     * @param bool $significantparams
     * @return moodle_url
     */
    public function url($params=array(), $significantparams=true) {
        if ($significantparams) {
            $params = array_merge($this->att->pageparams->get_significant_params(), $params);
        }

        return $this->att->url_preferences($params);
    }
}

/**
 * Default status set
 *
 * @copyright  2011 Artem Andreev <andreev.artem@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class attendance_default_statusset implements renderable {
    /** @var array  */
    public $statuses;
    /** @var array  */
    public $errors;

    /**
     * attendance_default_statusset constructor.
     * @param array $statuses
     * @param array $errors
     */
    public function __construct($statuses, $errors) {
        $this->statuses = $statuses;
        $this->errors = $errors;
    }

    /**
     * url helper.
     * @param stdClass $params
     * @return moodle_url
     */
    public function url($params) {
        return new moodle_url('/mod/attendance/defaultstatus.php', $params);
    }
}

/**
 * Output a selector to change between status sets.
 *
 * @copyright  2011 Artem Andreev <andreev.artem@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class attendance_set_selector implements renderable {
    /** @var int  */
    public $maxstatusset;
    /** @var mod_attendance_structure  */
    private $att;

    /**
     * attendance_set_selector constructor.
     * @param mod_attendance_structure $att
     * @param int $maxstatusset
     */
    public function __construct(mod_attendance_structure $att, $maxstatusset) {
        $this->att = $att;
        $this->maxstatusset = $maxstatusset;
    }

    /**
     * url helper
     * @param array $statusset
     * @return moodle_url
     */
    public function url($statusset) {
        $params = array();
        $params['statusset'] = $statusset;

        return $this->att->url_preferences($params);
    }

    /**
     * get current statusset.
     * @return int
     */
    public function get_current_statusset() {
        if (isset($this->att->pageparams->statusset)) {
            return $this->att->pageparams->statusset;
        }
        return 0;
    }

    /**
     * get statusset name.
     * @param int $statusset
     * @return string
     */
    public function get_status_name($statusset) {
        return attendance_get_setname($this->att->id, $statusset, true);
    }
}

/**
 * Url helpers
 *
 * @copyright  2011 Artem Andreev <andreev.artem@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class url_helpers {
    /**
     * Url take.
     * @param stdClass $att
     * @param int $sessionid
     * @param int $grouptype
     * @return mixed
     */
    public static function url_take($att, $sessionid, $grouptype) {
        $params = array('sessionid' => $sessionid);
        if (isset($grouptype)) {
            $params['grouptype'] = $grouptype;
        }

        return $att->url_take($params);
    }

    /**
     * Must be called without or with both parameters
     * @param stdClass $att
     * @param null $sessionid
     * @param null $action
     * @return mixed
     */
    public static function url_sessions($att, $sessionid=null, $action=null) {
        if (isset($sessionid) && isset($action)) {
            $params = array('sessionid' => $sessionid, 'action' => $action);
        } else {
            $params = array();
        }

        return $att->url_sessions($params);
    }

    /**
     * Url view helper.
     * @param stdClass $att
     * @param array $params
     * @return mixed
     */
    public static function url_view($att, $params=array()) {
        return $att->url_view($params);
    }
}

/**
 * Data structure representing an attendance password icon.
 *
 * @copyright 2017 Dan Marsden
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class attendance_password_icon implements renderable, templatable {

    /**
     * @var string text to show
     */
    public $text;

    /**
     * @var string Extra descriptive text next to the icon
     */
    public $linktext = null;

    /**
     * Constructor
     *
     * @param string $text string for help page title,
     *  string with _help suffix is used for the actual help text.
     *  string with _link suffix is used to create a link to further info (if it exists)
     * @param string $sessionid
     */
    public function __construct($text, $sessionid) {
        $this->text  = $text;
        $this->sessionid = $sessionid;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output Used to do a final render of any components that need to be rendered for export.
     * @return array
     */
    public function export_for_template(renderer_base $output) {

        $title = get_string('password', 'attendance');

        $data = new stdClass();
        $data->heading = '';
        $data->text = $this->text;

        $data->alt = $title;
        $data->icon = (new pix_icon('key', '', 'attendance'))->export_for_template($output);
        $data->linktext = '';
        $data->title = $title;
        $data->url = (new moodle_url('/mod/attendance/password.php', [
            'session' => $this->sessionid]))->out(false);

        $data->ltr = !right_to_left();
        return $data;
    }
}

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
 * Log report renderer.
 *
 * @package    report_log
 * @copyright  2014 Rajesh Taneja <rajesh.taneja@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;
use core\log\manager;

/**
 * Report log renderable class.
 *
 * @package    report_log
 * @copyright  2014 Rajesh Taneja <rajesh.taneja@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_log_renderable implements renderable {
    /** @var manager log manager */
    protected $logmanager;

    /** @var string selected log reader pluginname */
    public $selectedlogreader = null;

    /** @var int page number */
    public $page;

    /** @var int perpage records to show */
    public $perpage;

    /** @var stdClass course record */
    public $course;

    /** @var moodle_url url of report page */
    public $url;

    /** @var int selected date from which records should be displayed */
    public $date;

    /** @var int selected user id for which logs are displayed */
    public $userid;

    /** @var int selected moduleid */
    public $modid;

    /** @var string selected action filter */
    public $action;

    /** @var int educational level */
    public $edulevel;

    /** @var bool show courses */
    public $showcourses;

    /** @var bool show users */
    public $showusers;

    /** @var bool show report */
    public $showreport;

    /** @var bool show selector form */
    public $showselectorform;

    /** @var string selected log format */
    public $logformat;

    /** @var string order to sort */
    public $order;

    /** @var int group id */
    public $groupid;

    /** @var table_log table log which will be used for rendering logs */
    public $tablelog;

    /**
     * Constructor.
     *
     * @param string $logreader (optional)reader pluginname from which logs will be fetched.
     * @param stdClass|int $course (optional) course record or id
     * @param int $userid (optional) id of user to filter records for.
     * @param int|string $modid (optional) module id or site_errors for filtering errors.
     * @param string $action (optional) action name to filter.
     * @param int $groupid (optional) groupid of user.
     * @param int $edulevel (optional) educational level.
     * @param bool $showcourses (optional) show courses.
     * @param bool $showusers (optional) show users.
     * @param bool $showreport (optional) show report.
     * @param bool $showselectorform (optional) show selector form.
     * @param moodle_url|string $url (optional) page url.
     * @param int $date date (optional) timestamp of start of the day for which logs will be displayed.
     * @param string $logformat log format.
     * @param int $page (optional) page number.
     * @param int $perpage (optional) number of records to show per page.
     * @param string $order (optional) sortorder of fetched records
     */
    public function __construct($logreader = "", $course = 0, $userid = 0, $modid = 0, $action = "", $groupid = 0, $edulevel = -1,
            $showcourses = false, $showusers = false, $showreport = true, $showselectorform = true, $url = "", $date = 0,
            $logformat='showashtml', $page = 0, $perpage = 100, $order = "timecreated ASC") {

        global $PAGE;

        // Use first reader as selected reader, if not passed.
        if (empty($logreader)) {
            $readers = $this->get_readers();
            if (!empty($readers)) {
                reset($readers);
                $logreader = key($readers);
            } else {
                $logreader = null;
            }
        }
        // Use page url if empty.
        if (empty($url)) {
            $url = new moodle_url($PAGE->url);
        } else {
            $url = new moodle_url($url);
        }
        $this->selectedlogreader = $logreader;
        $url->param('logreader', $logreader);

        // Use site course id, if course is empty.
        if (!empty($course) && is_int($course)) {
            $course = get_course($course);
        }
        $this->course = $course;

        $this->userid = $userid;
        $this->date = $date;
        $this->page = $page;
        $this->perpage = $perpage;
        $this->url = $url;
        $this->order = $order;
        $this->modid = $modid;
        $this->action = $action;
        $this->groupid = $groupid;
        $this->edulevel = $edulevel;
        $this->showcourses = $showcourses;
        $this->showusers = $showusers;
        $this->showreport = $showreport;
        $this->showselectorform = $showselectorform;
        $this->logformat = $logformat;
    }

    /**
     * Get a list of enabled sql_reader objects/name
     *
     * @param bool $nameonly if true only reader names will be returned.
     * @return array core\log\sql_reader object or name.
     */
    public function get_readers($nameonly = false) {
        if (!isset($this->logmanager)) {
            $this->logmanager = get_log_manager();
        }

        $readers = $this->logmanager->get_readers('core\log\sql_reader');
        if ($nameonly) {
            foreach ($readers as $pluginname => $reader) {
                $readers[$pluginname] = $reader->get_name();
            }
        }
        return $readers;
    }

    /**
     * Helper function to return list of activities to show in selection filter.
     *
     * @return array list of activities.
     */
    public function get_activities_list() {
        $activities = array();

        // For site just return site errors option.
        $sitecontext = context_system::instance();
        if ($this->course->id == SITEID && has_capability('report/log:view', $sitecontext)) {
            $activities["site_errors"] = get_string("siteerrors");
            return $activities;
        }

        $modinfo = get_fast_modinfo($this->course);
        if (!empty($modinfo->cms)) {
            $section = 0;
            $thissection = array();
            foreach ($modinfo->cms as $cm) {
                if (!$cm->uservisible || !$cm->has_view()) {
                    continue;
                }
                if ($cm->sectionnum > 0 and $section <> $cm->sectionnum) {
                    $activities[] = $thissection;
                    $thissection = array();
                }
                $section = $cm->sectionnum;
                $modname = strip_tags($cm->get_formatted_name());
                if (core_text::strlen($modname) > 55) {
                    $modname = core_text::substr($modname, 0, 50)."...";
                }
                if (!$cm->visible) {
                    $modname = "(".$modname.")";
                }
                $key = get_section_name($this->course, $cm->sectionnum);
                if (!isset($thissection[$key])) {
                    $thissection[$key] = array();
                }
                $thissection[$key][$cm->id] = $modname;
            }
            if (!empty($thissection)) {
                $activities[] = $thissection;
            }
        }
        return $activities;
    }

    /**
     * Helper function to get selected group.
     *
     * @return int selected group.
     */
    public function get_selected_group() {
        global $SESSION, $USER;

        // No groups for system.
        if (empty($this->course)) {
            return 0;
        }

        $context = context_course::instance($this->course->id);

        $selectedgroup = 0;
        // Setup for group handling.
        $groupmode = groups_get_course_groupmode($this->course);
        if ($groupmode == SEPARATEGROUPS and !has_capability('moodle/site:accessallgroups', $context)) {
            $selectedgroup = -1;
        } else if ($groupmode) {
            $selectedgroup = $this->groupid;
        } else {
            $selectedgroup = 0;
        }

        if ($selectedgroup === -1) {
            if (isset($SESSION->currentgroup[$this->course->id])) {
                $selectedgroup = $SESSION->currentgroup[$this->course->id];
            } else {
                $selectedgroup = groups_get_all_groups($this->course->id, $USER->id);
                if (is_array($selectedgroup)) {
                    $groupids = array_keys($selectedgroup);
                    $selectedgroup = array_shift($groupids);
                    $SESSION->currentgroup[$this->course->id] = $selectedgroup;
                } else {
                    $selectedgroup = 0;
                }
            }
        }
        return $selectedgroup;
    }

    /**
     * Return list of actions for log reader.
     *
     * @todo MDL-44528 Get list from log_store.
     * @return array list of action options.
     */
    public function get_actions() {
        $actions = array(
                'c' => get_string('create'),
                'r' => get_string('view'),
                'u' => get_string('update'),
                'd' => get_string('delete'),
                'cud' => get_string('allchanges')
                );
        return $actions;
    }

    /**
     * Return selected user fullname.
     *
     * @return string user fullname.
     */
    public function get_selected_user_fullname() {
        $user = core_user::get_user($this->userid);
        return fullname($user);
    }

    /**
     * Return list of courses to show in selector.
     *
     * @return array list of courses.
     */
    public function get_course_list() {
        global $DB, $SITE;

        $courses = array();

        $sitecontext = context_system::instance();
        // First check to see if we can override showcourses and showusers.
        $numcourses = $DB->count_records("course");
        if ($numcourses < COURSE_MAX_COURSES_PER_DROPDOWN && !$this->showcourses) {
            $this->showcourses = 1;
        }

        // Check if course filter should be shown.
        if (has_capability('report/log:view', $sitecontext) && $this->showcourses) {
            if ($courserecords = $DB->get_records("course", null, "fullname", "id,shortname,fullname,category")) {
                foreach ($courserecords as $course) {
                    if ($course->id == SITEID) {
                        $courses[$course->id] = format_string($course->fullname) . ' (' . get_string('site') . ')';
                    } else {
                        $courses[$course->id] = format_string(get_course_display_name_for_list($course));
                    }
                }
            }
            core_collator::asort($courses);
        }
        return $courses;
    }

    /**
     * Return list of groups.
     *
     * @return array list of groups.
     */
    public function get_group_list() {

        // No groups for system.
        if (empty($this->course)) {
            return array();
        }

        $context = context_course::instance($this->course->id);
        $groups = array();
        $groupmode = groups_get_course_groupmode($this->course);
        if (($groupmode == VISIBLEGROUPS) ||
                ($groupmode == SEPARATEGROUPS and has_capability('moodle/site:accessallgroups', $context))) {
            // Get all groups.
            if ($cgroups = groups_get_all_groups($this->course->id)) {
                foreach ($cgroups as $cgroup) {
                    $groups[$cgroup->id] = $cgroup->name;
                }
            }
        }
        return $groups;
    }

    /**
     * Return list of users.
     *
     * @return array list of users.
     */
    public function get_user_list() {
        global $CFG, $SITE;

        $courseid = $SITE->id;
        if (!empty($this->course)) {
            $courseid = $this->course->id;
        }
        $context = context_course::instance($courseid);
        $limitfrom = empty($this->showusers) ? 0 : '';
        $limitnum  = empty($this->showusers) ? COURSE_MAX_USERS_PER_DROPDOWN + 1 : '';
        $courseusers = get_enrolled_users($context, '', $this->groupid, 'u.id, ' . get_all_user_name_fields(true, 'u'),
                null, $limitfrom, $limitnum);

        if (count($courseusers) < COURSE_MAX_USERS_PER_DROPDOWN && !$this->showusers) {
            $this->showusers = 1;
        }

        $users = array();
        if ($this->showusers) {
            if ($courseusers) {
                foreach ($courseusers as $courseuser) {
                     $users[$courseuser->id] = fullname($courseuser, has_capability('moodle/site:viewfullnames', $context));
                }
            }
            $users[$CFG->siteguest] = get_string('guestuser');
        }
        return $users;
    }

    /**
     * Return list of date options.
     *
     * @return array date options.
     */
    public function get_date_options() {
        global $SITE;

        $strftimedate = get_string("strftimedate");
        $strftimedaydate = get_string("strftimedaydate");

        // Get all the possible dates.
        // Note that we are keeping track of real (GMT) time and user time.
        // User time is only used in displays - all calcs and passing is GMT.
        $timenow = time(); // GMT.

        // What day is it now for the user, and when is midnight that day (in GMT).
        $timemidnight = usergetmidnight($timenow);

        // Put today up the top of the list.
        $dates = array("$timemidnight" => get_string("today").", ".userdate($timenow, $strftimedate) );

        // If course is empty, get it from frontpage.
        $course = $SITE;
        if (!empty($this->course)) {
            $course = $this->course;
        }
        if (!$course->startdate or ($course->startdate > $timenow)) {
            $course->startdate = $course->timecreated;
        }

        $numdates = 1;
        while ($timemidnight > $course->startdate and $numdates < 365) {
            $timemidnight = $timemidnight - 86400;
            $timenow = $timenow - 86400;
            $dates["$timemidnight"] = userdate($timenow, $strftimedaydate);
            $numdates++;
        }
        return $dates;
    }

    /**
     * Return list of edulevel.
     *
     * @todo MDL-44528 Get list from log_store.
     * @return array list of edulevels.
     */
    public function get_edulevel_options() {
        $edulevels = array(
                    -1 => get_string("edulevel"),
                    1 => get_string('edulevelteacher'),
                    2 => get_string('edulevelparticipating'),
                    0 => get_string('edulevelother')
                    );
        return $edulevels;
    }

    /**
     * Setup table log.
     */
    public function setup_table() {
        $readers = $this->get_readers();

        $filter = new \stdClass();
        if (!empty($this->course)) {
            $filter->courseid = $this->course->id;
        } else {
            $filter->courseid = 0;
        }

        $filter->userid = $this->userid;
        $filter->modid = $this->modid;
        $filter->groupid = $this->get_selected_group();
        $filter->logreader = $readers[$this->selectedlogreader];
        $filter->edulevel = $this->edulevel;
        $filter->action = $this->action;
        $filter->date = $this->date;
        $filter->orderby = $this->order;

        // If showing site_errors.
        if ('site_errors' === $this->modid) {
            $filter->siteerrors = true;
            $filter->modid = 0;
        }

        $this->tablelog = new report_log_table_log('report_log', $filter);
        $this->tablelog->define_baseurl($this->url);
        $this->tablelog->is_downloadable(true);
        $this->tablelog->show_download_buttons_at(array(TABLE_P_BOTTOM));
    }

    /**
     * Download logs in specified format.
     */
    public function download() {
        $filename = 'logs_' . userdate(time(), get_string('backupnameformat', 'langconfig'), 99, false);
        $this->tablelog->is_downloading($this->logformat, $filename);
        $this->tablelog->out($this->perpage, false);
    }
}

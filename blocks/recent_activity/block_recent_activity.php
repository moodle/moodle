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
 * class block_recent_activity
 *
 * @package    block_recent_activity
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot.'/course/lib.php');

/**
 * class block_recent_activity
 *
 * @package    block_recent_activity
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_recent_activity extends block_base {

    /**
     * Use {@link block_recent_activity::get_timestart()} to access
     *
     * @var int stores the time since when we want to show recent activity
     */
    protected $timestart = null;

    /**
     * Initialises the block
     */
    function init() {
        $this->title = get_string('pluginname', 'block_recent_activity');
    }

    /**
     * Returns the content object
     *
     * @return stdObject
     */
    function get_content() {
        if ($this->content !== NULL) {
            return $this->content;
        }

        if (empty($this->instance)) {
            $this->content = '';
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = '';
        $this->content->footer = '';

        $renderer = $this->page->get_renderer('block_recent_activity');
        $this->content->text = $renderer->recent_activity($this->page->course,
                $this->get_timestart(),
                $this->get_recent_enrolments(),
                $this->get_structural_changes(),
                $this->get_modules_recent_activity());

        return $this->content;
    }

    /**
     * Returns the time since when we want to show recent activity
     *
     * For guest users it is 2 days, for registered users it is the time of last access to the course
     *
     * @return int
     */
    protected function get_timestart() {
        global $USER;
        if ($this->timestart === null) {
            $this->timestart = round(time() - COURSE_MAX_RECENT_PERIOD, -2); // better db caching for guests - 100 seconds

            if (!isguestuser()) {
                if (!empty($USER->lastcourseaccess[$this->page->course->id])) {
                    if ($USER->lastcourseaccess[$this->page->course->id] > $this->timestart) {
                        $this->timestart = $USER->lastcourseaccess[$this->page->course->id];
                    }
                }
            }
        }
        return $this->timestart;
    }

    /**
     * Returns all recent enrollments
     *
     * @todo MDL-36993 this function always return empty array
     * @return array array of entries from {user} table
     */
    protected function get_recent_enrolments() {
        return get_recent_enrolments($this->page->course->id, $this->get_timestart());
    }

    /**
     * Returns list of recent changes in course structure
     *
     * It includes adding, editing or deleting of the resources or activities
     * Excludes changes on modules without a view link (i.e. labels), and also
     * if activity was both added and deleted
     *
     * @return array array of changes. Each element is an array containing attributes:
     *    'action' - one of: 'add mod', 'update mod', 'delete mod'
     *    'module' - instance of cm_info (for 'delete mod' it is an object with attributes modname and modfullname)
     */
    protected function get_structural_changes() {
        global $DB, $CFG;
        $course = $this->page->course;
        $timestart = $this->get_timestart();
        $changelist = array();
        $logs = $DB->get_records_select('log',
                "time > ? AND course = ? AND
                    module = 'course' AND
                    (action = 'add mod' OR action = 'update mod' OR action = 'delete mod')",
                array($timestart, $course->id), "id ASC");
        if ($logs) {
            $modinfo = get_fast_modinfo($course);
            $newgones = array(); // added and later deleted items
            foreach ($logs as $key => $log) {
                $info = explode(' ', $log->info);

                if (count($info) != 2) {
                    debugging("Incorrect log entry info: id = ".$log->id, DEBUG_DEVELOPER);
                    continue;
                }

                $modname    = $info[0];
                $instanceid = $info[1];

                if ($log->action == 'delete mod') {
                    if (plugin_supports('mod', $modname, FEATURE_NO_VIEW_LINK, false)) {
                        // we should better call cm_info::has_view() because it can be
                        // dynamic. But there is no instance of cm_info now
                        continue;
                    }
                    // unfortunately we do not know if the mod was visible
                    if (!array_key_exists($log->info, $newgones)) {
                        $changelist[$log->info] = array('action' => $log->action,
                            'module' => (object)array(
                                'modname' => $modname,
                                'modfullname' => get_string('modulename', $modname)
                             ));
                    }
                } else {
                    if (!isset($modinfo->instances[$modname][$instanceid])) {
                        if ($log->action == 'add mod') {
                            // do not display added and later deleted activities
                            $newgones[$log->info] = true;
                        }
                        continue;
                    }
                    $cm = $modinfo->instances[$modname][$instanceid];
                    if ($cm->has_view() && $cm->uservisible && empty($changelist[$log->info])) {
                        $changelist[$log->info] = array('action' => $log->action, 'module' => $cm);
                    }
                }
            }
        }
        return $changelist;
    }

    /**
     * Returns list of recent activity within modules
     *
     * For each used module type executes callback MODULE_print_recent_activity()
     *
     * @return array array of pairs moduletype => content
     */
    protected function get_modules_recent_activity() {
        $context = context_course::instance($this->page->course->id);
        $viewfullnames = has_capability('moodle/site:viewfullnames', $context);
        $hascontent = false;

        $modinfo = get_fast_modinfo($this->page->course);
        $usedmodules = $modinfo->get_used_module_names();
        $recentactivity = array();
        foreach ($usedmodules as $modname => $modfullname) {
            // Each module gets it's own logs and prints them
            ob_start();
            $hascontent = component_callback('mod_'. $modname, 'print_recent_activity',
                    array($this->page->course, $viewfullnames, $this->get_timestart()), false);
            if ($hascontent) {
                $recentactivity[$modname] = ob_get_contents();
            }
            ob_end_clean();
        }
        return $recentactivity;
    }

    /**
     * Which page types this block may appear on.
     *
     * @return array page-type prefix => true/false.
     */
    function applicable_formats() {
        return array('all' => true, 'my' => false, 'tag' => false);
    }
}


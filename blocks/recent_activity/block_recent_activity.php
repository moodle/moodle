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
     * Returns all recent enrolments.
     *
     * This function previously used get_recent_enrolments located in lib/deprecatedlib.php which would
     * return an empty array which was identified in MDL-36993. The use of this function outside the
     * deprecated lib was removed in MDL-40649.
     *
     * @todo MDL-36993 this function always return empty array
     * @return array array of entries from {user} table
     */
    protected function get_recent_enrolments() {
        return array();
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
        global $DB;
        $course = $this->page->course;
        $context = context_course::instance($course->id);
        $canviewdeleted = has_capability('block/recent_activity:viewdeletemodule', $context);
        $canviewupdated = has_capability('block/recent_activity:viewaddupdatemodule', $context);
        if (!$canviewdeleted && !$canviewupdated) {
            return;
        }

        $timestart = $this->get_timestart();
        $changelist = array();
        // The following query will retrieve the latest action for each course module in the specified course.
        // Also the query filters out the modules that were created and then deleted during the given interval.
        $sql = "SELECT
                    cmid, MIN(action) AS minaction, MAX(action) AS maxaction, MAX(modname) AS modname
                FROM {block_recent_activity}
                WHERE timecreated > ? AND courseid = ?
                GROUP BY cmid
                ORDER BY MAX(timecreated) ASC";
        $params = array($timestart, $course->id);
        $logs = $DB->get_records_sql($sql, $params);
        if (isset($logs[0])) {
            // If special record for this course and cmid=0 is present, migrate logs.
            self::migrate_logs($course);
            $logs = $DB->get_records_sql($sql, $params);
        }
        if ($logs) {
            $modinfo = get_fast_modinfo($course);
            foreach ($logs as $log) {
                // We used aggregate functions since constants CM_CREATED, CM_UPDATED and CM_DELETED have ascending order (0,1,2).
                $wasdeleted = ($log->maxaction == block_recent_activity_observer::CM_DELETED);
                $wascreated = ($log->minaction == block_recent_activity_observer::CM_CREATED);

                if ($wasdeleted && $wascreated) {
                    // Activity was created and deleted within this interval. Do not show it.
                    continue;
                } else if ($wasdeleted && $canviewdeleted) {
                    if (plugin_supports('mod', $log->modname, FEATURE_NO_VIEW_LINK, false)) {
                        // Better to call cm_info::has_view() because it can be dynamic.
                        // But there is no instance of cm_info now.
                        continue;
                    }
                    // Unfortunately we do not know if the mod was visible.
                    $modnames = get_module_types_names();
                    $changelist[$log->cmid] = array('action' => 'delete mod',
                        'module' => (object)array(
                            'modname' => $log->modname,
                            'modfullname' => isset($modnames[$log->modname]) ? $modnames[$log->modname] : $log->modname
                         ));

                } else if (!$wasdeleted && isset($modinfo->cms[$log->cmid]) && $canviewupdated) {
                    // Module was either added or updated during this interval and it currently exists.
                    // If module was both added and updated show only "add" action.
                    $cm = $modinfo->cms[$log->cmid];
                    if ($cm->has_view() && $cm->uservisible) {
                        $changelist[$log->cmid] = array(
                            'action' => $wascreated ? 'add mod' : 'update mod',
                            'module' => $cm
                        );
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

    /**
     * Migrates entries from table {log} into {block_recent_activity}
     *
     * We only migrate logs for the courses that actually have recent activity
     * block and that are being viewed within COURSE_MAX_RECENT_PERIOD time
     * after the upgrade.
     *
     * The presence of entry in {block_recent_activity} with the cmid=0 indicates
     * that the course needs log migration. Those entries were installed in
     * db/upgrade.php when the table block_recent_activity was created.
     *
     * @param stdClass $course
     */
    protected static function migrate_logs($course) {
        global $DB;
        if (!$logstarted = $DB->get_record('block_recent_activity',
                array('courseid' => $course->id, 'cmid' => 0),
                'id, timecreated')) {
            return;
        }
        $DB->delete_records('block_recent_activity', array('id' => $logstarted->id));
        try {
            $logs = $DB->get_records_select('log',
                    "time > ? AND time < ? AND course = ? AND
                        module = 'course' AND
                        (action = 'add mod' OR action = 'update mod' OR action = 'delete mod')",
                    array(time()-COURSE_MAX_RECENT_PERIOD, $logstarted->timecreated, $course->id),
                    'id ASC', 'id, time, userid, cmid, action, info');
        } catch (Exception $e) {
            // Probably table {log} was already removed.
            return;
        }
        if (!$logs) {
            return;
        }
        $modinfo = get_fast_modinfo($course);
        $entries = array();
        foreach ($logs as $log) {
            $info = explode(' ', $log->info);
            if (count($info) != 2) {
                continue;
            }
            $modname = $info[0];
            $instanceid = $info[1];
            $entry = array('courseid' => $course->id, 'userid' => $log->userid,
                'timecreated' => $log->time, 'modname' => $modname);
            if ($log->action == 'delete mod') {
                if (!$log->cmid) {
                    continue;
                }
                $entry['action'] = 2;
                $entry['cmid'] = $log->cmid;
            } else {
                if (!isset($modinfo->instances[$modname][$instanceid])) {
                    continue;
                }
                if ($log->action == 'add mod') {
                    $entry['action'] = 0;
                } else {
                    $entry['action'] = 1;
                }
                $entry['cmid'] = $modinfo->instances[$modname][$instanceid]->id;
            }
            $entries[] = $entry;
        }
        $DB->insert_records('block_recent_activity', $entries);
    }
}


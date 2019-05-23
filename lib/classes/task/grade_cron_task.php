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
 * A scheduled task.
 *
 * @package    core
 * @copyright  2013 onwards Martin Dougiamas  http://dougiamas.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core\task;

/**
 * Simple task to run the grade cron.
 */
class grade_cron_task extends scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('taskgradecron', 'admin');
    }

    /**
     * Do the job.
     * Throw exceptions on errors (the job will be retried).
     */
    public function execute() {
        global $CFG, $DB;
        require_once($CFG->libdir . '/gradelib.php');

        $now = time();
        $sql = "SELECT i.*
                  FROM {grade_items} i
                 WHERE i.locked = 0
                   AND i.locktime > 0
                   AND i.locktime < ?
                   AND EXISTS (SELECT 'x'
                                 FROM {grade_items} c
                                WHERE c.itemtype='course'
                                  AND c.needsupdate=0
                                  AND c.courseid=i.courseid)";
        // Go through all courses that have proper final grades and lock them if needed.
        $rs = $DB->get_recordset_sql($sql, array($now));
        foreach ($rs as $item) {
            $gradeitem = new \grade_item($item, false);
            $gradeitem->locked = $now;
            $gradeitem->update('locktime');
        }
        $rs->close();

        $gradeinst = new \grade_grade();
        $fields = 'g.' . implode(',g.', $gradeinst->required_fields);
        $sql = "SELECT $fields
                  FROM {grade_grades} g, {grade_items} i
                 WHERE g.locked = 0
                   AND g.locktime > 0
                   AND g.locktime < ?
                   AND g.itemid = i.id
                   AND EXISTS (SELECT 'x'
                                 FROM {grade_items} c
                                WHERE c.itemtype = 'course'
                                  AND c.needsupdate = 0
                                  AND c.courseid = i.courseid)";
        // Go through all courses that have proper final grades and lock them if needed.
        $rs = $DB->get_recordset_sql($sql, array($now));
        foreach ($rs as $grade) {
            $gradegrade = new \grade_grade($grade, false);
            $gradegrade->locked = $now;
            $gradegrade->update('locktime');
        }
        $rs->close();
    }

}

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
 * Upgrade scripts for course format "Weeks"
 *
 * @package    format_weeks
 * @copyright  2017 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade script for format_weeks
 *
 * @param int $oldversion the version we are upgrading from
 * @return bool result
 */
function xmldb_format_weeks_upgrade($oldversion) {
    global $CFG, $DB;

    require_once($CFG->dirroot . '/course/format/weeks/lib.php');
    require_once($CFG->dirroot . '/course/format/weeks/db/upgradelib.php');

    if ($oldversion < 2017020200) {

        // Remove 'numsections' option and hide or delete orphaned sections.
        format_weeks_upgrade_remove_numsections();

        upgrade_plugin_savepoint(true, 2017020200, 'format', 'weeks');
    }

    if ($oldversion < 2017050300) {
        // Go through the existing courses using the weeks format with no value set for the 'automaticenddate'.
        $sql = "SELECT c.id, c.enddate, cfo.id as cfoid
                  FROM {course} c
             LEFT JOIN {course_format_options} cfo
                    ON cfo.courseid = c.id
                   AND cfo.format = c.format
                   AND cfo.name = :optionname
                   AND cfo.sectionid = 0
                 WHERE c.format = :format
                   AND cfo.id IS NULL";
        $params = ['optionname' => 'automaticenddate', 'format' => 'weeks'];
        $courses = $DB->get_recordset_sql($sql, $params);
        foreach ($courses as $course) {
            $option = new stdClass();
            $option->courseid = $course->id;
            $option->format = 'weeks';
            $option->sectionid = 0;
            $option->name = 'automaticenddate';
            if (empty($course->enddate)) {
                $option->value = 1;
                $DB->insert_record('course_format_options', $option);

                // Now, let's update the course end date.
                format_weeks::update_end_date($course->id);
            } else {
                $option->value = 0;
                $DB->insert_record('course_format_options', $option);
            }
        }
        $courses->close();

        upgrade_plugin_savepoint(true, 2017050300, 'format', 'weeks');
    }

    // Automatically generated Moodle v3.3.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.4.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2018030900) {

        // During upgrade to Moodle 3.3 it could happen that general section (section 0) became 'invisible'.
        // It should always be visible.
        $DB->execute("UPDATE {course_sections} SET visible=1 WHERE visible=0 AND section=0 AND course IN
        (SELECT id FROM {course} WHERE format=?)", ['weeks']);

        upgrade_plugin_savepoint(true, 2018030900, 'format', 'weeks');
    }

    // Automatically generated Moodle v3.5.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.6.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}

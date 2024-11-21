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
 * Upgrade scripts for course format "Soft Course"
 *
 * @package    format_softcourse
 * @copyright  2021 Pimenko <contact@pimenko.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Upgrade script for format_softcourse
 *
 * @param int $oldversion the version we are upgrading from
 * @return bool result
 */
function xmldb_format_softcourse_upgrade($oldversion) {
    global $CFG, $DB;

    require_once($CFG->dirroot . '/course/format/softcourse/db/upgradelib.php');

    if ($oldversion < 2017020200) {

        // Remove 'numsections' option and hide or delete orphaned sections.
        format_softcourse_upgrade_remove_numsections();

        upgrade_plugin_savepoint(true, 2017020200, 'format', 'softcourse');
    }

    // Automatically generated Moodle v3.3.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.4.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2018030900) {

        // During upgrade to Moodle 3.3 it could happen that general section (section 0) became 'invisible'.
        // It should always be visible.
        $DB->execute("UPDATE {course_sections} SET visible=1 WHERE visible=0 AND section=0 AND course IN
        (SELECT id FROM {course} WHERE format=?)", ['softcourse']);

        upgrade_plugin_savepoint(true, 2018030900, 'format', 'softcourse');
    }
    if ($oldversion < 2019103100) {

        // Get all sections 0 of courses having softcourse course format.
        $sectionsrequest = 'SELECT s.id, s.course, s.summary
                            FROM {course_sections} s
                            INNER JOIN {course} c ON s.course = c.id
                            WHERE c.format = "softcourse"
                            AND s.section = 0';
        $sections = $DB->get_records_sql($sectionsrequest, []);

        // Insert the summary of section 0 in the new course format option.
        foreach ($sections as $section) {
            $courseformatoption = new stdClass();
            $courseformatoption->courseid = $section->course;
            $courseformatoption->format = 'introduction';
            $courseformatoption->sectionid = 0;
            $courseformatoption->name = 'introduction';
            $courseformatoption->value = $section->summary;
            $DB->insert_record('course_format_options', $courseformatoption);
        }

        // Delete the summary of sections 0 of courses having softcourse course format.
        $deleterequest = 'UPDATE {course_sections} SET summary = ""
                          WHERE course IN (SELECT id FROM {course} WHERE format = :format)
                          AND section = 0';
        $DB->execute($deleterequest, ['format' => 'softcourse']);

        upgrade_plugin_savepoint(true, 2019103100, 'format', 'softcourse');
    }

    return true;
}

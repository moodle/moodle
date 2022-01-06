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
 * Upgrade scripts for course format "Tiles"
 *
 * @package    format_tiles
 * @copyright  2018 David Watson {@link http://evolutioncode.uk} (part copied from "Topics" 2017 Marina Glancy)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 *
 * This method finds all courses in 'tiles' format that have actual number of sections
 * bigger than their 'numsections' course format option.
 * For each such course we call {@see format_tiles_upgrade_hide_extra_sections()} and
 * either delete or hide "orphaned" sections.
 * @throws dml_exception
 * @throws coding_exception
 */
function format_tiles_upgrade_remove_numsections() {
    global $DB;

    $sql1 = "SELECT c.id, max(cs.section) AS coursemaxsection
          FROM {course} c
          JOIN {course_sections} cs ON cs.course = c.id
          WHERE c.format = :format1
          GROUP BY c.id";

    $sql2 = "SELECT c.id, n.value AS numsections
          FROM {course} c
          JOIN {course_format_options} n ON n.courseid = c.id AND n.format = :format1 AND n.name = :numsections AND n.sectionid = 0
          WHERE c.format = :format2";

    $params = ['format1' => 'tiles', 'format2' => 'tiles', 'numsections' => 'numsections'];

    $coursemaxsections = $DB->get_records_sql_menu($sql1, $params);
    $numsections = $DB->get_records_sql_menu($sql2, $params);
    $needfixing = [];

    $defaultnumsections = get_config('moodlecourse', 'numsections');

    foreach ($coursemaxsections as $courseid => $coursemaxsection) {
        if (array_key_exists($courseid, $numsections)) {
            $coursenumrealsections = (int)$numsections[$courseid];
        } else {
            $coursenumrealsections = $defaultnumsections;
        }
        if ($coursemaxsection > $coursenumrealsections) {
            $needfixing[$courseid] = $coursenumrealsections;
        }

        // For this course (i.e. each course in this format), check if sec zero is hidden and unhide it if so.
        if ($section = $DB->get_record("course_sections", array("course" => $courseid, "section" => 0))) {
            if (!$section->visible) {
                // Set section zero to visible if it is hidden.
                // (It should never be hidden see https://moodle.org/mod/forum/discuss.php?d=356850 and MDL-37256).
                set_section_visible($courseid, 0, 1);
            }
        }
    }
    unset($coursemaxsections);
    unset($numsections);

    foreach ($needfixing as $courseid => $numsections) {
        format_tiles_upgrade_hide_extra_sections($courseid, $numsections);
    }

    $DB->delete_records('course_format_options', ['format' => 'tiles', 'sectionid' => 0, 'name' => 'numsections']);
}

/**
 * Find all sections in the course with sectionnum bigger than numsections.
 * Either delete these sections (if they contain no course modules) or hide them (if they contain cms)
 * In the latter case they were 'orphaned activities' so must be converted into hidden sections instead
 *
 * We will only delete a section if it is completely empty and all sections below
 * it are also empty
 *
 * @param int $courseid
 * @param int $numsections
 * @throws dml_exception
 * @throws coding_exception
 */
function format_tiles_upgrade_hide_extra_sections($courseid, $numsections) {
    global $DB;
    $sections = $DB->get_records_sql('SELECT id, name, summary, sequence, visible
        FROM {course_sections}
        WHERE course = ? AND section > ?
        ORDER BY section DESC', [$courseid, $numsections]);
    $candelete = true;
    $tohide = [];
    $todelete = [];
    foreach ($sections as $section) {
        // Variable $section->sequence contains the cm id list for this section.  If empty, section contains no content.
        if ($candelete && (!empty($section->summary) || !empty($section->sequence) || !empty($section->name))) {
            $candelete = false;
        }
        if ($candelete) {
            $todelete[] = $section->id;
        } else if ($section->visible) {
            $tohide[] = $section->id;
        }
    }
    if ($todelete) {
        // Delete empty sections in the end.
        // This is an upgrade script - no events or cache resets are needed.
        // We also know that these sections do not have any modules so it is safe to just delete records in the table.
        $DB->delete_records_list('course_sections', 'id', $todelete);
    }
    if ($tohide) {
        // Hide other orphaned sections.
        // This is different from what set_section_visible() does but we want to preserve actual
        // module visibility in this case.
        list($sql, $params) = $DB->get_in_or_equal($tohide);
        $DB->execute("UPDATE {course_sections} SET visible = 0 WHERE id " . $sql, $params);
    }
}

/**
 * Remove options which are no longer supported in this version
 * @throws dml_exception
 */
function format_tiles_remove_unused_format_options() {
    global $DB;
    $DB->delete_records('course_format_options', array('format' => 'tiles', 'name' => 'showachladderbutton'));
    $DB->delete_records('course_format_options', array('format' => 'tiles', 'name' => 'prefixtitlewithnumber'));
    $DB->delete_records('course_format_options', array('format' => 'tiles', 'name' => 'showgradesbutton'));
}

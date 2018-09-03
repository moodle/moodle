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
 * This method finds all courses in 'topics' format that have actual number of sections
 * different than their 'numsections' course format option.
 *
 * For courses where there are more sections than numsections, we call
 * {@link format_weeks_upgrade_hide_extra_sections()} and
 * either delete or hide "orphaned" sections. For courses where there are fewer sections
 * than numsections, we call {@link format_weeks_upgrade_add_empty_sections()} to add
 * these sections.
 */
function format_weeks_upgrade_remove_numsections() {
    global $DB;

    $sql1 = "SELECT c.id, max(cs.section) AS sectionsactual
          FROM {course} c
          JOIN {course_sections} cs ON cs.course = c.id
          WHERE c.format = :format1
          GROUP BY c.id";

    $sql2 = "SELECT c.id, n.value AS numsections
          FROM {course} c
          JOIN {course_format_options} n ON n.courseid = c.id AND n.format = :format1 AND n.name = :numsections AND n.sectionid = 0
          WHERE c.format = :format2";

    $params = ['format1' => 'weeks', 'format2' => 'weeks', 'numsections' => 'numsections'];

    $actual = $DB->get_records_sql_menu($sql1, $params);
    $numsections = $DB->get_records_sql_menu($sql2, $params);
    $needfixing = [];
    $needsections = [];

    $defaultnumsections = get_config('moodlecourse', 'numsections');

    foreach ($actual as $courseid => $sectionsactual) {
        if (array_key_exists($courseid, $numsections)) {
            $n = (int)$numsections[$courseid];
        } else {
            $n = $defaultnumsections;
        }
        if ($sectionsactual > $n) {
            $needfixing[$courseid] = $n;
        } else if ($sectionsactual < $n) {
            $needsections[$courseid] = $n;
        }
    }
    unset($actual);
    unset($numsections);

    foreach ($needfixing as $courseid => $numsections) {
        format_weeks_upgrade_hide_extra_sections($courseid, $numsections);
    }

    foreach ($needsections as $courseid => $numsections) {
        format_weeks_upgrade_add_empty_sections($courseid, $numsections);
    }

    $DB->delete_records('course_format_options', ['format' => 'weeks', 'sectionid' => 0, 'name' => 'numsections']);
}

/**
 * Find all sections in the course with sectionnum bigger than numsections.
 * Either delete these sections or hide them
 *
 * We will only delete a section if it is completely empty and all sections below
 * it are also empty
 *
 * @param int $courseid
 * @param int $numsections
 */
function format_weeks_upgrade_hide_extra_sections($courseid, $numsections) {
    global $DB;
    $sections = $DB->get_records_sql('SELECT id, name, summary, sequence, visible
        FROM {course_sections}
        WHERE course = ? AND section > ?
        ORDER BY section DESC', [$courseid, $numsections]);
    $candelete = true;
    $tohide = [];
    $todelete = [];
    foreach ($sections as $section) {
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
 * This method adds empty sections to courses which have fewer sections than their
 * 'numsections' course format option and adds these empty sections.
 *
 * @param int $courseid
 * @param int $numsections
 */
function format_weeks_upgrade_add_empty_sections($courseid, $numsections) {
    global $DB;
    $existingsections = $DB->get_fieldset_sql('SELECT section from {course_sections} WHERE course = ?', [$courseid]);
    $newsections = array_diff(range(0, $numsections), $existingsections);
    foreach ($newsections as $sectionnum) {
        course_create_section($courseid, $sectionnum, true);
    }
}

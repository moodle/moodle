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
 * Upgrade scripts for course format "onetopic"
 *
 * @package   format_onetopic
 * @copyright 2018 David Herney Bernal - cirano
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot. '/course/format/topics/db/upgradelib.php');

/**
 * This method finds all courses in 'onetopic' format that have actual number of sections
 * bigger than their 'numsections' course format option.
 * For each such course we call {@link format_topics_upgrade_hide_extra_sections()} and
 * either delete or hide "orphaned" sections.
 *
 * This method is based of the "topics" format.
 *
 */
function format_onetopic_upgrade_remove_numsections() {
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

    $params = ['format1' => 'onetopic', 'format2' => 'onetopic', 'numsections' => 'numsections'];

    $actual = $DB->get_records_sql_menu($sql1, $params);
    $numsections = $DB->get_records_sql_menu($sql2, $params);
    $needfixing = [];

    $defaultnumsections = get_config('moodlecourse', 'numsections');

    foreach ($actual as $courseid => $sectionsactual) {
        if (array_key_exists($courseid, $numsections)) {
            $n = (int)$numsections[$courseid];
        } else {
            $n = $defaultnumsections;
        }
        if ($sectionsactual > $n) {
            $needfixing[$courseid] = $n;
        }
    }
    unset($actual);
    unset($numsections);

    foreach ($needfixing as $courseid => $numsections) {
        format_topics_upgrade_hide_extra_sections($courseid, $numsections);
    }

    $DB->delete_records('course_format_options', ['format' => 'onetopic', 'sectionid' => 0, 'name' => 'numsections']);
}


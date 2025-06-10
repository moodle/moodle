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
 *
 * @package    block_ues_reprocess
 * @copyright  2014 Louisiana State University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

abstract class ues_reprocess {
    public static function select($sections) {
        global $OUTPUT;

        echo html_writer::start_tag('pre');

        foreach ($sections as $section) {
            ues_teacher::reset_status($section, ues::PROCESSED);
            ues_student::reset_status($section, ues::PROCESSED);
        }

        ues::reprocess_sections($sections, false);

        ues::reset_unenrollments($sections);

        // Build processed entries.
        foreach ($sections as $section) {
            $section->save_meta(array('section_reprocessed' => true));
        }

        echo html_writer::end_tag('pre');
    }

    public static function post($ownedsections, $data) {
        $sections = array();

        foreach ($data as $key => $checked) {
            if (preg_match('/course_(\d+)_(\d+)/', $key, $matches)) {
                $inuescourse = function($section) use ($matches) {

                    $samesemester = $section->semesterid == $matches[1];
                    $samecourse = $section->courseid == $matches[2];

                    $same = ($samesemester and $samecourse);

                    return ($same and empty($section->section_reprocessed));
                };

                $sections += array_filter($ownedsections, $inuescourse);
                continue;
            }

            if (preg_match('/section_(\d+)/', $key, $matches)) {
                $sections[$matches[1]] = $ownedsections[$matches[1]];
            }
        }

        return $sections;
    }
}

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
 * @package    enrol_ues
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Philip Cali, Adam Zapletal, Chad Mazilly, Robert Russo, Dave Elliott
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

interface semester_processor {
    public function semesters($datethreshold);
}

interface course_processor {
    public function courses($semester);
}

interface teacher_by_department {
    public function teachers($semester, $department);
}

interface student_by_department {
    public function students($semester, $department);
}

interface teacher_processor {
    public function teachers($semester, $course, $section);
}

interface student_processor {
    public function students($semester, $course, $section);
}

interface teacher_info_processor {
    public function teacher_info($semester, $teacher);
}

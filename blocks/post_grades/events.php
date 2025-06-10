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

defined('MOODLE_INTERNAL') || die();

abstract class post_grades_handler {
    public static function ues_semester_drop($semester) {
        global $DB;

        // At this point, I can be sure that only the posting periods remain.
        $params = array('semesterid' => $semester->id);
        return $DB->delete_records('block_post_grades_periods', $params);
    }

    public static function ues_section_drop($section) {
        global $DB;
        $params = array('sectionid' => $section->id);
        return $DB->delete_records('block_post_grades_postings', $params);
    }

    public static function user_deleted($user) {
        global $DB;
        $params = array('userid' => $user->id);
        return $DB->delete_records('block_post_grades_postings', $params);
    }

    public static function ues_people_outputs($data) {
        $sections = ues_section::from_course($data->course);

        require_once(dirname(__FILE__) . '/peoplelib.php');

        $data->outputs['student_audit'] = new post_grades_audit_people();

        return $data;
    }

    private static function injection_requirements() {
        global $CFG;

        if (!class_exists('post_grades')) {
            require_once($CFG->dirroot . '/blocks/post_grades/lib.php');
        }
    }

    private static function apply_incomplete($data) {
        self::injection_requirements();

        $sections = ues_section::from_course($data->course);

        if (empty($sections)) {
            return true;
        }

        $course = reset($sections)->course();

        //return true;

        $str = get_string('student_incomplete', 'block_post_grades');

        $originalheaders = $data->headers();
        $originalheaders[] = $str;
        $data->set_headers($originalheaders);

        $originaldefinition = $data->definition();
        $originaldefinition[] = 'incomplete';
        $data->set_definition($originaldefinition);

        return true;
    }
}

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

namespace theme_moove\util;

use core\exception\moodle_exception;
use core_course_list_element;

/**
 * My learning class.
 *
 * @package    theme_moove
 * @copyright  2023 Willian Mano <willianmano@conecti.me>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mylearning {
    /**
     * Returns the last accessed courses by the user.
     *
     * @param $limit
     * @return array
     * @throws moodle_exception
     * @throws \dml_exception
     */
    public function get_last_accessed_courses($limit) {
        global $DB, $USER;

        $sql = 'SELECT
                    c.*,
                    l.timeaccess
                FROM
                    {user_lastaccess} l
                INNER JOIN
                    {course} c ON c.id = l.courseid
                INNER JOIN
                    {enrol} e ON e.courseid = c.id
                INNER JOIN
                    {user_enrolments} ue ON ue.enrolid = e.id AND ue.userid = l.userid
                WHERE
                    l.courseid > 1 AND l.userid = :userid
                ORDER BY
                    l.timeaccess DESC';

        $params = ['userid' => $USER->id];

        if (!$courses = $DB->get_records_sql($sql, $params, 0, $limit)) {
            return [];
        }

        $data = [];
        foreach ($courses as $course) {
            $utilcourse = new course(new core_course_list_element($course));

            $url = new \moodle_url('/course/view.php', ['id' => $course->id]);

            $progress = \core_completion\progress::get_course_progress_percentage($course, $USER->id);

            $data[] = [
                'id' => $course->id,
                'shortnmame' => $course->shortname,
                'fullname' => $course->fullname,
                'image' => $utilcourse->get_summary_image(),
                'lastaccess' => userdate($course->timeaccess, '%d/%m/%Y %H:%M:%S'),
                'url' => $url->out(),
                'hasprogress' => !is_null($progress),
                'progress' => !is_null($progress) ? floor($progress) : 0,
            ];
        }

        return $data;
    }
}

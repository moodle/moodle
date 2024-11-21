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
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 *
 * @package    local_intelliboard
 * @copyright  2020 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @website    https://intelliboard.net/
 */

namespace local_intelliboard\output\tables\initial_reports;

use local_intelliboard\output\tables\intelliboard_table;

class report45 extends intelliboard_table
{
    function __construct($uniqueid, $params)
    {
        global $PAGE, $DB;

        parent::__construct($uniqueid, $params);

        $this->define_baseurl($PAGE->url);

        $fields = "t.*";

        $from = "(SELECT CONCAT(u.id, '_', q.id) AS id,
                         q.id AS quizid,
                         q.name AS quiz,
                         c.fullname AS course,
                         u.firstname,
                         u.lastname,
                         u.email,
                         COUNT(DISTINCT qa.id) AS num_attempts,
                         CASE WHEN ((MAX(qa.sumgrades) / q.sumgrades) * 100) IS NULL
                              THEN 0
                              ELSE (MAX(qa.sumgrades) / q.sumgrades) * 100
                         END AS highest_grade,
                         CASE WHEN ((MIN(qa.sumgrades) / q.sumgrades) * 100) IS NULL
                              THEN 0
                              ELSE (MIN(qa.sumgrades) / q.sumgrades) * 100
                         END AS lowest_grade,
                         SUM(qa.timefinish - qa.timestart) AS time_spent,
                         MAX(cmc.timemodified) AS timemodified
                    FROM {quiz_attempts} qa
                    JOIN {quiz} q ON q.id = qa.quiz
                    JOIN {user} u ON u.id = qa.userid
               LEFT JOIN {course} c ON c.id = q.course
               LEFT JOIN {modules} m ON m.name = 'quiz'
               LEFT JOIN {course_modules} cm ON cm.course = q.course AND cm.module = m.id AND cm.instance = q.id
               LEFT JOIN {course_modules_completion} cmc ON cmc.coursemoduleid = cm.id AND cmc.userid = qa.userid
                   WHERE q.id > 0
                GROUP BY u.id, q.sumgrades, q.name, c.fullname, q.id
                 ) t";

        $where = "t.quizid > 0";

        if ($this->search) {
            $searchsql1 = $DB->sql_like('t.quiz', ':tquiz', false);
            $searchsql2 = $DB->sql_like('t.course', ':tcourse', false);
            $searchsql3 = $DB->sql_like('t.firstname', ':tfirstname', false);
            $searchsql4 = $DB->sql_like('t.lastname', ':tlastname', false);
            $searchsql5 = $DB->sql_like('t.email', ':temail', false);
            $where .= " AND ({$searchsql1} OR {$searchsql2} OR {$searchsql3} OR {$searchsql4} OR {$searchsql5})";
            $this->sqlreqparams = [
                "tquiz" => "%{$this->search}%",
                "tcourse" => "%{$this->search}%",
                "tfirstname" => "%{$this->search}%",
                "tlastname" => "%{$this->search}%",
                "temail" => "%{$this->search}%",
            ];
        }

        $this->set_sql($fields, $from, $where, $this->sqlreqparams);
    }

    public function col_timemodified($row) {
        if (!$row->timemodified) {
            return get_string("incomplete", "local_intelliboard");
        }

        return get_string(
            "completed_on", "local_intelliboard", ($row->timemodified ? userdate($row->timemodified) : '-')
        );
    }

    protected function get_intelliboard_columns()
    {
        return [
            ["name" => "quiz", "title" => get_string('quiz_name', 'local_intelliboard'), "type" => "text"],
            ["name" => "course", "title" => get_string('course', 'local_intelliboard'), "type" => "text"],
            ["name" => "firstname", "title" => get_string('first_name', 'local_intelliboard'), "type" => "text"],
            ["name" => "lastname", "title" => get_string('last_name', 'local_intelliboard'), "type" => "text"],
            ["name" => "email", "title" => get_string('email', 'local_intelliboard'), "type" => "text"],
            ["name" => "num_attempts", "title" => get_string('num_attempts', 'local_intelliboard'), "type" => "text"],
            ["name" => "time_spent", "title" => get_string('total_time_spent', 'local_intelliboard'), "type" => "time"],
            ["name" => "highest_grade", "title" => get_string('highest_grade', 'local_intelliboard'), "type" => "float"],
            ["name" => "lowest_grade", "title" => get_string('lowest_grade', 'local_intelliboard'), "type" => "float"],
            ["name" => "timemodified", "title" => get_string('status', 'local_intelliboard'), "type" => "text"],
        ];
    }
}
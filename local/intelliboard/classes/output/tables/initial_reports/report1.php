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
use local_intelliboard\reports\report_trait;

class report1 extends intelliboard_table
{
    use report_trait;

    function __construct($uniqueid, $params)
    {
        global $PAGE, $DB;

        parent::__construct($uniqueid, $params);

        $this->define_baseurl($PAGE->url);

        $fields = "CONCAT(ue.id, '_', e.id) AS id,
                   (CASE WHEN ue.timestart > 0 THEN ue.timestart ELSE ue.timecreated END) AS enrolled,
                   CASE WHEN g.rawgrademax > 0 THEN (g.finalgrade/g.rawgrademax)*100 ELSE g.finalgrade END AS grade,
                   c.enablecompletion,
                   CASE WHEN cc.timecompleted IS NULL THEN 0 ELSE cc.timecompleted END AS complete,
                   u.email,
                   u.firstname,
                   u.lastname,
                   u.username,
                   e.enrol AS enrols,
                   c.fullname AS course,
                   c.shortname AS course_short_name";

        $from = "  {user_enrolments} ue
              JOIN {enrol} e ON e.id = ue.enrolid
              JOIN {user} u ON u.id = ue.userid
              JOIN {course} c ON c.id = e.courseid
         LEFT JOIN {user_lastaccess} ul ON ul.courseid = c.id AND ul.userid = u.id
         LEFT JOIN {course_completions} cc ON cc.course = e.courseid AND cc.userid = ue.userid
         LEFT JOIN {grade_items} gi ON gi.itemtype = 'course' AND gi.courseid = e.courseid
         LEFT JOIN {grade_grades} g ON g.userid = u.id AND g.itemid = gi.id AND g.finalgrade IS NOT NULL";

        $where = "ue.id > 0";

        if ($this->search) {
            $searchsql1  = $DB->sql_like('u.firstname', ':ufirstname', false);
            $searchsql2  = $DB->sql_like('u.lastname', ':ulastname', false);
            $searchsql3  = $DB->sql_like('u.email', ':uemail', false);
            $searchsql4  = $DB->sql_like('u.username', ':uusername', false);
            $searchsql11 = $DB->sql_like('c.fullname', ':cfullname', false);
            $searchsql12 = $DB->sql_like('c.shortname', ':cshortname', false);
            $searchsql13 = $DB->sql_like('e.enrol', ':eendrol', false);
            $where .= " AND ({$searchsql1} OR {$searchsql2} OR {$searchsql3} OR {$searchsql11} OR {$searchsql12} OR {$searchsql4} OR {$searchsql13})";
            $this->sqlreqparams = [
                "ufirstname" => "%{$this->search}%",
                "ulastname" => "%{$this->search}%",
                "uemail" => "%{$this->search}%",
                "cfullname" => "%{$this->search}%",
                "cshortname" => "%{$this->search}%",
                "uusername" => "%{$this->search}%",
                "eendrol" => "%{$this->search}%",
            ];
        }

        $this->set_sql(
            $fields, $from, $where, $this->sqlreqparams
        );
    }

    public function col_complete($row) {
        if (!$row->enablecompletion) {
            return get_string("completion_is_not_enabled", "local_intelliboard");
        }

        if ($row->complete > 0) {
            return get_string(
                "completed_on", "local_intelliboard", ($row->complete ? userdate($row->complete) : '-')
            );
        } else {
            return get_string("incomplete", "local_intelliboard");
        }
    }

    protected function get_intelliboard_columns()
    {
        return [
            ["name" => "firstname", "title" => get_string('first_name', 'local_intelliboard'), "type" => "text"],
            ["name" => "lastname", "title" => get_string('last_name', 'local_intelliboard'), "type" => "text"],
            ["name" => "username", "title" => get_string('username', 'local_intelliboard'), "type" => "text"],
            ["name" => "email", "title" => get_string('email', 'local_intelliboard'), "type" => "text"],
            ["name" => "course", "title" => get_string('course_name', 'local_intelliboard'), "type" => "text"],
            ["name" => "course_short_name", "title" => get_string('course_short_name', 'local_intelliboard'), "type" => "text"],
            ["name" => "enrols", "title" => get_string('enrolment_method', 'local_intelliboard'), "type" => "text"],
            ["name" => "grade", "title" => get_string('score', 'local_intelliboard'), "type" => "int"],
            ["name" => "complete", "title" => get_string('status', 'local_intelliboard'), "type" => "text"],
            ["name" => "enrolled", "title" => get_string('enroled_on', 'local_intelliboard'), "type" => "date"],
        ];
    }
}

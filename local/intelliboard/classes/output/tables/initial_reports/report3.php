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

class report3 extends intelliboard_table
{
    function __construct($uniqueid, $params)
    {
        global $PAGE, $DB;

        parent::__construct($uniqueid, $params);

        $this->define_baseurl($PAGE->url);

        $moduleisntancenamesql = $this->get_modules_sql();

        $fields = "t.*";

        $from = "(SELECT cm.id,
                         m.name AS moduletype,
                         cm.added AS created,
                         cm.completion,
                         c.fullname AS course,
                         l.timespend AS time_spent,
                         l.visits AS visits,
                         l.firstaccess AS first_access,
                         (SELECT COUNT(id) FROM {course_modules_completion} WHERE coursemoduleid = cm.id) AS num_completed,
                         (SELECT AVG(CASE WHEN g.rawgrademax > 0 THEN (g.finalgrade/g.rawgrademax) * 100 ELSE g.finalgrade END)
                            FROM {grade_items} gi, {grade_grades} g
                           WHERE gi.itemtype = 'mod' AND gi.itemmodule = m.name AND gi.iteminstance = cm.instance AND
                                 g.itemid = gi.id AND g.finalgrade IS NOT NULL
                         ) AS avg_score, {$moduleisntancenamesql}
                    FROM {course_modules} cm
                    JOIN {modules} m ON m.id = cm.module
                    JOIN {course} c ON c.id = cm.course
               LEFT JOIN (SELECT param,
                                 SUM(timespend) AS timespend,
                                 SUM(visits) AS visits,
                                 MIN(firstaccess) AS firstaccess
                            FROM {local_intelliboard_tracking}
                           WHERE page='module'
                        GROUP BY param
                         ) l ON l.param = cm.id
                  ) t";

        $where = "t.id > 0";

        if ($this->search) {
            $searchsql1 = $DB->sql_like('t.course', ':cfullname', false);
            $searchsql2 = $DB->sql_like('t.moduletype', ':tmoduletype', false);
            $searchsql3 = $DB->sql_like('t.activity', ':tactivity', false);
            $where .= " AND ({$searchsql1} OR {$searchsql2} OR {$searchsql3})";
            $this->sqlreqparams = [
                "cfullname" => "%{$this->search}%",
                "tmoduletype" => "%{$this->search}%",
                "tactivity" => "%{$this->search}%",
            ];
        }

        $this->set_sql($fields, $from, $where, $this->sqlreqparams);
    }

    public function col_num_completed($row)
    {
        if (!$row->completion) {
            return get_string("completion_not_enabled", "local_intelliboard");
        }

        return $row->num_completed;
    }

    public function col_moduletype($row) {
        global $OUTPUT;

        if ($this->isdownload) {
            return $row->moduletype;
        }

        $html = \html_writer::start_div(
            "table-module-icon", ["style" => "text-align: center"]
        );

        if ($row->moduletype) {
            $html .= $OUTPUT->pix_icon(
                'icon', '', 'mod_'.$row->moduletype
            );
        }

        $html .= \html_writer::start_span("module-name");
        $html .= $row->moduletype;
        $html .= \html_writer::end_div();
        $html .= \html_writer::end_div();

        return $html;
    }

    protected function get_intelliboard_columns() {
        return [
            ["name" => "course", "title" => get_string('course', 'local_intelliboard'), "type" => "text"],
            ["name" => "activity", "title" => get_string('activity', 'local_intelliboard'), "type" => "text"],
            ["name" => "moduletype", "title" => get_string('type', 'local_intelliboard'), "type" => "text"],
            ["name" => "num_completed", "title" => get_string('num_completed_activity', 'local_intelliboard'), "type" => "text"],
            ["name" => "visits", "title" => get_string('number_of_visits', 'local_intelliboard'), "type" => "int"],
            ["name" => "time_spent", "title" => get_string('total_time_spent', 'local_intelliboard'), "type" => "time"],
            ["name" => "avg_score", "title" => get_string('avg_score', 'local_intelliboard'), "type" => "percentgrade"],
            ["name" => "created", "title" => get_string('created', 'local_intelliboard'), "type" => "date"],
            ["name" => "first_access", "title" => get_string('first_access', 'local_intelliboard'), "type" => "date"],
        ];
    }
}
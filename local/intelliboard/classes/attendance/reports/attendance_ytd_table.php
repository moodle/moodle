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
 * @package    local_intelliboard
 * @copyright  2019 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @website    http://intelliboard.net/
 */

namespace local_intelliboard\attendance\reports;

use local_intelliboard\reports\entities\in_filter;
use local_intelliboard\reports\report_trait;

class attendance_ytd_table extends report {
    use report_trait;

    public function get_data($params) {
        global $DB;

        $order = '';
        $where = 'c.id <> 1';
        $sqlparams = ['coursecx' => CONTEXT_COURSE];

        if($params['order']) {
            $order = "ORDER BY {$params['order']['field']} {$params['order']['dir']}";
        }

        if ($params["teacher_id"]) {
            $coursefilter = new in_filter($this->get_teacher_courses($params["teacher_id"]), "cr1");
            $sqlparams = array_merge($sqlparams, $coursefilter->get_params());
            $where .= " AND c.id " . $coursefilter->get_sql();
        }

        if (!empty($params["courses"])) {
            $coursefilter = new in_filter($params["courses"], "cr2");
            $sqlparams = array_merge($sqlparams, $coursefilter->get_params());
            $where .= " AND c.id " . $coursefilter->get_sql();
        }

        $studentrolefilter = new in_filter($this->get_student_roles(), "role");
        $sqlparams = array_merge($sqlparams, $studentrolefilter->get_params());

        return $DB->get_records_sql(
            "SELECT c.id, c.fullname as course,
                    COUNT(DISTINCT ra.userid) as number_of_enrollments
               FROM {course} c
               JOIN {context} cx ON cx.instanceid = c.id AND
                                    cx.contextlevel = :coursecx
          LEFT JOIN {role_assignments} ra ON ra.contextid = cx.id AND
                                             ra.roleid " . $studentrolefilter->get_sql() . "
              WHERE {$where}
           GROUP BY c.id {$order}",
            $sqlparams,
            $params['offset'], $params['limit']
        );
    }
}
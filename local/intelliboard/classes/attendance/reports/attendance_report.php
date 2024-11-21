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

class attendance_report extends report {
    use report_trait;

    public function get_data($params) {
        global $DB;

        $order = '';
        $where = 'u.id > 0';
        $sqlparams = ['coursecxlvl' => CONTEXT_COURSE];

        if($params['order']) {
            $order = "ORDER BY {$params['order']['field']} {$params['order']['dir']}";
        }

        if($params['search']) {
            $where .= ' AND (' . $DB->sql_like(
                'CONCAT(u.firstname, \' \', u.lastname)', ':search', false
            );
            $where .= ' OR ' .$DB->sql_like(
                    'CONCAT(u.lastname, \' \', u.firstname)', ':search1', false
            ) . ')';
            $sqlparams['search'] = "%{$params['search']}%";
            $sqlparams['search1'] = "%{$params['search']}%";
        }

        if ($params["teacher_id"]) {
            $studentsfilter = new in_filter(
                $this->get_courses_students($this->get_teacher_courses($params["teacher_id"])), "st1"
            );
            $sqlparams = array_merge($sqlparams, $studentsfilter->get_params());
            $where .= " AND u.id " . $studentsfilter->get_sql();
        }

        $studentrolefilter = new in_filter($this->get_student_roles(), "strole");
        $sqlparams = array_merge($sqlparams, $studentrolefilter->get_params());

        return $DB->get_records_sql(
            "SELECT DISTINCT u.id, CONCAT(u.firstname, ' ', u.lastname) as fullname,
                    u.email
               FROM {user} u
               JOIN {context} cx ON cx.contextlevel = :coursecxlvl
               JOIN {role_assignments} ra ON ra.roleid " . $studentrolefilter->get_sql() . " AND
                                             ra.userid = u.id AND
                                             ra.contextid = cx.id
              WHERE {$where} {$order}",
            $sqlparams,
            $params['offset'], $params['limit']
        );
    }
}
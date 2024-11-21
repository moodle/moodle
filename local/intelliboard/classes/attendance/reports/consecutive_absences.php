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

class consecutive_absences extends report {
    use report_trait;

    public function get_data($params) {
        global $DB;

        $order = '';

        if(!$params['users']) {
          return [];
        }

        $studentrolefilter = new in_filter($this->get_student_roles(), "role");

        $userFilter = new in_filter($params['users'], "user");

        if($params['order']) {
            $order = "ORDER BY {$params['order']['field']} {$params['order']['dir']}";
        }

        return $DB->get_records_sql(
            "SELECT u.id, CONCAT(u.firstname, ' ', u.lastname) as fullname,
                    COUNT(DISTINCT ra.contextid) as student_courses,
                    (SELECT AVG(gg.finalgrade)
                       FROM {grade_grades} gg
                       JOIN {grade_items} gi ON gi.id = gg.itemid AND
                                                gi.itemtype = 'course'
                      WHERE gg.userid = u.id
                   GROUP BY u.id
                    ) as avg_grade
               FROM {user} u
               JOIN {role_assignments} ra ON ra.userid = u.id AND
                                             ra.roleid " . $studentrolefilter->get_sql() . "
               JOIN {context} cx ON cx.id = ra.contextid AND
                                    cx.contextlevel = :cxcourse
              WHERE u.id " . $userFilter->get_sql() . "
           GROUP BY u.id, fullname {$order}",
            array_merge(['cxcourse' => CONTEXT_COURSE], $userFilter->get_params(), $studentrolefilter->get_params()),
            $params['offset'], $params['limit']
        );
    }
}
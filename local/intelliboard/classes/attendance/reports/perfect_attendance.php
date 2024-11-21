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

class perfect_attendance extends report {
    use report_trait;

    public function get_data($params) {
        global $DB;

        if(!$params["users"]) {
            return [];
        }

        $studentrolefilter = new in_filter($this->get_student_roles(), "role");
        $userfilter = new in_filter($params["users"], "user");

        return $DB->get_records_sql(
            "SELECT u.id, u.firstname, u.lastname, COUNT(DISTINCT ra.contextid) AS student_courses,
                    AVG(CASE WHEN (gg.finalgrade / gg.rawgrademax) IS NULL THEN 0 ELSE ((gg.finalgrade / gg.rawgrademax) * 100) END) AS avg_grade
               FROM {user} u
               JOIN {role_assignments} ra ON ra.userid = u.id AND ra.roleid " . $studentrolefilter->get_sql() . "
               JOIN {context} cx ON cx.id = ra.contextid AND cx.contextlevel = :cxcourse
          LEFT JOIN {grade_items} gi ON gi.courseid = cx.instanceid AND gi.itemtype = 'course'
          LEFT JOIN {grade_grades} gg ON gg.itemid = gi.id AND gg.userid = u.id
              WHERE u.id " . $userfilter->get_sql() . "
           GROUP BY u.id, u.firstname, u.lastname",
            array_merge(['cxcourse' => CONTEXT_COURSE], $userfilter->get_params(), $studentrolefilter->get_params())
        );
    }
}
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
 * @copyright  2019 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @website    https://intelliboard.net/
 */

namespace local_intelliboard\repositories;

class category_repositoriy {
    /**
     * Get list of categories where user has specific roles
     *
     * @param int $userid User ID
     * @param string $roles Comma-separated list of category ids
     * @return array List of categories
     * @throws \dml_exception
     */
    public static function catgories_enrollments($userid, $roles) {
        global $DB;

        // category enrollments
        $params = [
            'userid' => $userid, 'categorycontextlevel' => CONTEXT_COURSECAT
        ];
        list($rolefilter, $params) = intelliboard_filter_in_sql($roles, "ra.roleid", $params);

        return $DB->get_records_sql(
            "SELECT cat.*
           FROM {context} cx
           JOIN {role_assignments} ra ON ra.contextid = cx.id AND 
                                         ra.userid = :userid {$rolefilter}
           JOIN {course_categories} cat ON cat.id = cx.instanceid
          WHERE cx.contextlevel = :categorycontextlevel",
            $params
        );
    }

    /**
     * Get list of categories + them subcategories
     *
     * @param $categories
     * @return array
     * @throws \dml_exception
     */
    public static function get_categories_with_subcategories($categories) {
        global $DB;

        if(empty($categories)) {
            return [];
        }

        $categoriesfilter = [];
        $params = [];
        $counter = 1;

        foreach ($categories as $category) {
            $categoriesfilter[] = $DB->sql_like('cc.path', ":ccat{$counter}");
            $params["ccat{$counter}"] = '%'.$DB->sql_like_escape($category->id).'%';
            $counter++;
        }

        $categoriesfilter = implode(' OR ', $categoriesfilter);

        return $DB->get_records_sql(
            "SELECT cc.*
               FROM {course_categories} cc
              WHERE {$categoriesfilter}",
            $params
        );
    }
}
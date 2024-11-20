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

declare(strict_types=1);

namespace core_admin\reportbuilder\local\filters;

use core\context\system;
use core_course_category;
use MoodleQuickForm;
use core_reportbuilder\local\filters\base;
use core_reportbuilder\local\helpers\database;

/**
 * Course role report filter (by role, category, course)
 *
 * The provided filter field SQL must refer/return an expression for the user ID (e.g. "{$user}.id")
 *
 * @package     core_admin
 * @copyright   2023 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class courserole extends base {

    /**
     * Setup form
     *
     * @param MoodleQuickForm $mform
     */
    public function setup_form(MoodleQuickForm $mform): void {
        $elements = [];

        // Role.
        $elements['role'] = $mform->createElement('select', "{$this->name}_role", get_string('rolefullname', 'core_role'),
            [0 => get_string('anyrole', 'core_filters')] + get_default_enrol_roles(system::instance()));

        // Category.
        $elements['category'] = $mform->createElement('select', "{$this->name}_category", get_string('category'),
            [0 => get_string('anycategory', 'core_filters')] + core_course_category::make_categories_list());

        // Course.
        $elements['course'] = $mform->createElement('text', "{$this->name}_course", get_string('shortnamecourse'));
        $mform->setType("{$this->name}_course", PARAM_RAW_TRIMMED);

        $mform->addGroup($elements, "{$this->name}_group", $this->get_header(), '', false)
            ->setHiddenLabel(true);
    }

    /**
     * Return filter SQL
     *
     * @param array $values
     * @return array
     */
    public function get_sql_filter(array $values): array {
        [$fieldsql, $params] = $this->filter->get_field_sql_and_params();

        [$contextalias, $rolealias, $coursealias] = database::generate_aliases(3);
        [$roleparam, $categoryparam, $courseparam] = database::generate_param_names(3);

        // Role.
        $role = (int) ($values["{$this->name}_role"] ?? 0);
        if ($role > 0) {
            $selects[] = "{$rolealias}.roleid = :{$roleparam}";
            $params[$roleparam] = $role;
        }

        // Category.
        $category = (int) ($values["{$this->name}_category"] ?? 0);
        if ($category > 0) {
            $selects[] = "{$coursealias}.category = :{$categoryparam}";
            $params[$categoryparam] = $category;
        }

        // Course.
        $course = trim($values["{$this->name}_course"] ?? '');
        if ($course !== '') {
            $selects[] = "{$coursealias}.shortname = :{$courseparam}";
            $params[$courseparam] = $course;
        }

        // Filter values are not set.
        if (empty($selects)) {
            return ['', []];
        }

        return ["{$fieldsql} IN (
            SELECT {$rolealias}.userid
              FROM {role_assignments} {$rolealias}
              JOIN {context} {$contextalias} ON {$contextalias}.id = {$rolealias}.contextid AND {$contextalias}.contextlevel = 50
              JOIN {course} {$coursealias} ON {$coursealias}.id = {$contextalias}.instanceid
             WHERE " . implode(' AND ', $selects)  . "
        )", $params];
    }
}

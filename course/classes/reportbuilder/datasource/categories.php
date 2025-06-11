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

namespace core_course\reportbuilder\datasource;

use core_cohort\reportbuilder\local\entities\cohort;
use core_course\reportbuilder\local\entities\course_category;
use core_reportbuilder\datasource;
use core_reportbuilder\local\entities\{course, user};
use core_role\reportbuilder\local\entities\role;

/**
 * Course categories datasource
 *
 * @package     core_course
 * @copyright   2023 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class categories extends datasource {

    /**
     * Return user friendly name of the datasource
     *
     * @return string
     */
    public static function get_name(): string {
        return get_string('coursecategories');
    }

    /**
     * Initialise report
     */
    protected function initialise(): void {
        $categoryentity = new course_category();

        $categoryalias = $categoryentity->get_table_alias('course_categories');
        $contextalias = $categoryentity->get_table_alias('context');

        $this->set_main_table('course_categories', $categoryalias);
        $this->add_entity($categoryentity);

        // Join course entity.
        $courseentity = new course();
        $coursealias = $courseentity->get_table_alias('course');
        $this->add_entity($courseentity
            ->add_join("LEFT JOIN {course} {$coursealias} ON {$coursealias}.category = {$categoryalias}.id"));

        // Join cohort entity (indicate context table join alias).
        $cohortentity = (new cohort())
            ->set_table_join_alias('context', $contextalias);
        $cohort = $cohortentity->get_table_alias('cohort');
        $this->add_entity($cohortentity
            ->add_join($categoryentity->get_context_join())
            ->add_join("LEFT JOIN {cohort} {$cohort} ON {$cohort}.contextid = {$contextalias}.id"));

        // Join role entity.
        $roleentity = (new role())
            ->set_table_alias('context', $contextalias);
        $role = $roleentity->get_table_alias('role');
        $this->add_entity($roleentity
            ->add_join($categoryentity->get_context_join())
            ->add_join("LEFT JOIN {role_assignments} ras ON ras.contextid = {$contextalias}.id")
            ->add_join("LEFT JOIN {role} {$role} ON {$role}.id = ras.roleid"));

        // Join user entity.
        $userentity = new user();
        $user = $userentity->get_table_alias('user');
        $this->add_entity($userentity
            ->add_joins($roleentity->get_joins())
            ->add_join("LEFT JOIN {user} {$user} ON {$user}.id = ras.userid"));

        // Add all elements from entities to be available in custom reports.
        $this->add_all_from_entities();
    }

    /**
     * Return the columns that will be added to the report as part of default setup
     *
     * @return string[]
     */
    public function get_default_columns(): array {
        return [
            'course_category:name',
            'course_category:idnumber',
            'course_category:coursecount',
        ];
    }

    /**
     * Return the default sorting that will be added to the report as part of default setup
     *
     * @return int[]
     */
    public function get_default_column_sorting(): array {
        return [
            'course_category:name' => SORT_ASC,
        ];
    }

    /**
     * Return the filters that will be added to the report as part of default setup
     *
     * @return string[]
     */
    public function get_default_filters(): array {
        return [
            'course_category:name',
        ];
    }

    /**
     * Return the conditions that will be added to the report as part of default setup
     *
     * @return string[]
     */
    public function get_default_conditions(): array {
        return [];
    }
}

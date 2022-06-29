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

use core_course\local\entities\course_category;
use core_reportbuilder\datasource;
use core_reportbuilder\local\entities\course;
use core_reportbuilder\local\helpers\database;

/**
 * Courses datasource
 *
 * @package     core_course
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class courses extends datasource {

    /**
     * Return user friendly name of the datasource
     *
     * @return string
     */
    public static function get_name(): string {
        return get_string('courses');
    }

    /**
     * Initialise report
     */
    protected function initialise(): void {
        $courseentity = new course();
        $coursetablealias = $courseentity->get_table_alias('course');

        // Exclude site course.
        $paramsiteid = database::generate_param_name();

        $this->set_main_table('course', $coursetablealias);
        $this->add_base_condition_sql("{$coursetablealias}.id != :{$paramsiteid}", [$paramsiteid => SITEID]);

        $this->add_entity($courseentity);

        // Join the course category entity.
        $coursecatentity = new course_category();
        $coursecattablealias = $coursecatentity->get_table_alias('course_categories');
        $this->add_entity($coursecatentity
            ->add_join("JOIN {course_categories} {$coursecattablealias}
                ON {$coursecattablealias}.id = {$coursetablealias}.category"));

        // Add all columns from entities to be available in custom reports.
        $this->add_columns_from_entity($coursecatentity->get_entity_name());
        $this->add_columns_from_entity($courseentity->get_entity_name());

        // Add all filters from entities to be available in custom reports.
        $this->add_filters_from_entity($coursecatentity->get_entity_name());
        $this->add_filters_from_entity($courseentity->get_entity_name());

        // Add all conditions from entities to be available in custom reports.
        $this->add_conditions_from_entity($coursecatentity->get_entity_name());
        $this->add_conditions_from_entity($courseentity->get_entity_name());
    }

    /**
     * Return the columns that will be added to the report as part of default setup
     *
     * @return string[]
     */
    public function get_default_columns(): array {
        return [
            'course_category:name',
            'course:shortname',
            'course:fullname',
            'course:idnumber',
        ];
    }

    /**
     * Return the filters that will be added to the report once is created
     *
     * @return string[]
     */
    public function get_default_filters(): array {
        return ['course_category:name', 'course:fullname', 'course:idnumber'];
    }

    /**
     * Return the conditions that will be added to the report once is created
     *
     * @return string[]
     */
    public function get_default_conditions(): array {
        return ['course_category:name'];
    }
}

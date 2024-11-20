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

namespace local_iomad\reportbuilder\datasource;

use lang_string;
use core_reportbuilder\datasource;
use core_reportbuilder\local\entities\{course, user};
use local_iomad\reportbuilder\local\entities\{iomadcourses};

/**
 * Local IOMAD datasource
 *
 * @package     local_iomad
 * @copyright   2024 Derick Turner e-Learn Design
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class coursesettings extends datasource {

    /**
     * Return user friendly name of the report source
     *
     * @return string
     */
    public static function get_name(): string {
        return get_string('iomadcourses', 'block_iomad_company_admin');
    }

    /**
     * Initialise report
     */
    protected function initialise(): void {
        $iomadcoursesentity = new iomadcourses();
        $iomadcoursesalias = $iomadcoursesentity->get_table_alias('iomadcourses');

        $this->set_main_table('iomad_courses', $iomadcoursesalias);

        $this->add_entity($iomadcoursesentity);

        // Get the tables and aliases
        $courseentity = new course();
        $coursealias = $courseentity->get_table_alias('course');

        // Join the course entity to the iomadcourses entity.

        $this->add_entity($courseentity
            ->add_join("JOIN {course} {$coursealias}
                ON {$iomadcoursesalias}.courseid = {$coursealias}.id")
        );

        // Add report elements from each of the entities we added to the report.
        $this->add_all_from_entities();
    }

    /**
     * Return the columns that will be added to the report upon creation
     *
     * @return string[]
     */
    public function get_default_columns(): array {
        return [
            'course:fullname',
            'course:visible',
            'iomadcourses:licensed',
            'iomadcourses:shared',
            'iomadcourses:validlength',
            'iomadcourses:warnexpire',
            'iomadcourses:expireafter',
            'iomadcourses:warncompletion',
            'iomadcourses:hasgrade',
        ];
    }

    /**
     * Return the filters that will be added to the report upon creation
     *
     * @return string[]
     */
    public function get_default_filters(): array {
        return [
            'course:fullname',
            'course:visible',
            'iomadcourses:licensed',
            'iomadcourses:shared',
            'iomadcourses:validlength',
            'iomadcourses:warnexpire',
            'iomadcourses:expireafter',
            'iomadcourses:warncompletion',
            'iomadcourses:hasgrade',
        ];
    }

    /**
     * Return the conditions that will be added to the report upon creation
     *
     * @return string[]
     */
    public function get_default_conditions(): array {
        return [
            'course:fullname',
            'course:visible',
            'iomadcourses:licensed',
            'iomadcourses:shared',
            'iomadcourses:validlength',
            'iomadcourses:warnexpire',
            'iomadcourses:expireafter',
            'iomadcourses:warncompletion',
            'iomadcourses:hasgrade',
        ];
    }

    /**
     * Return the default sorting that will be added to the report once it is created
     *
     * @return array|int[]
     */
    public function get_default_column_sorting(): array {
        return [
            'course:fullname' => SORT_ASC,
            'course:visible' => SORT_ASC,
            'iomadcourses:licensed' => SORT_ASC,
            'iomadcourses:shared' => SORT_ASC,
            'iomadcourses:validlength' => SORT_ASC,
            'iomadcourses:warnexpire' => SORT_ASC,
            'iomadcourses:expireafter' => SORT_ASC,
            'iomadcourses:warncompletion' => SORT_ASC,
            'iomadcourses:hasgrade' => SORT_ASC,
        ];
    }
}

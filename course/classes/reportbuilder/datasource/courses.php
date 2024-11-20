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

use core_course\reportbuilder\local\entities\course_category;
use core_files\reportbuilder\local\entities\file;
use core_reportbuilder\datasource;
use core_reportbuilder\local\entities\course;
use core_reportbuilder\local\helpers\database;
use core_tag\reportbuilder\local\entities\tag;
use lang_string;

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

        // Join the tag entity.
        $tagentity = (new tag())
            ->set_table_alias('tag', $courseentity->get_table_alias('tag'));
        $this->add_entity($tagentity
            ->add_joins($courseentity->get_tag_joins()));

        // Join the files entity.
        $contextalias = $courseentity->get_table_alias('context');
        $fileentity = (new file())
            ->set_entity_title(new lang_string('courseoverviewfiles'));
        $filesalias = $fileentity->get_table_alias('files');
        $this->add_entity($fileentity
            ->add_join($courseentity->get_context_join())
            ->add_join("LEFT JOIN {files} {$filesalias}
                ON {$filesalias}.contextid = {$contextalias}.id
               AND {$filesalias}.component = 'course'
               AND {$filesalias}.filearea = 'overviewfiles'
               AND {$filesalias}.itemid = 0
               AND {$filesalias}.filename != '.'"));

        // Add all columns/filters/conditions from entities to be available in custom reports.
        $this->add_all_from_entity($coursecatentity->get_entity_name());
        $this->add_all_from_entity($courseentity->get_entity_name());

        // Add specific tag entity elements.
        $this->add_columns_from_entity($tagentity->get_entity_name(), ['name', 'namewithlink']);
        $this->add_filter($tagentity->get_filter('name'));
        $this->add_condition($tagentity->get_condition('name'));

        // Add specific file entity elements.
        $this->add_columns_from_entity($fileentity->get_entity_name(), ['name', 'size', 'type', 'timecreated']);
        $this->add_filters_from_entity($fileentity->get_entity_name(), ['name', 'size', 'timecreated']);
        $this->add_conditions_from_entity($fileentity->get_entity_name(), ['name', 'size', 'timecreated']);

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

    /**
     * Return the default sorting that will be added to the report once it is created
     *
     * @return array|int[]
     */
    public function get_default_column_sorting(): array {
        return [
            'course_category:name' => SORT_ASC,
            'course:shortname' => SORT_ASC,
            'course:fullname' => SORT_ASC,
        ];
    }
}

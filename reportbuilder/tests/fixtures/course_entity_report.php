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

namespace core_reportbuilder;

use core_reportbuilder\local\entities\course;
use core_reportbuilder\local\helpers\database;

/**
 * Testable system report fixture for testing the course entity
 *
 * @package     core_reportbuilder
 * @copyright   2021 David Matamoros <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_entity_report extends system_report {

    /**
     * Initialise the report
     */
    protected function initialise(): void {
        $entity = new course();
        $coursetablealias = $entity->get_table_alias('course');
        $param = database::generate_param_name();

        $this->set_main_table('course', $coursetablealias);
        $this->add_entity($entity);
        // Add a base condition to hide the site course.
        $this->add_base_condition_sql("$coursetablealias.id <> :$param", [$param => SITEID]);

        $columns = [];
        foreach ($entity->get_columns() as $column) {
            $columns[] = $column->get_unique_identifier();
        }
        $this->add_columns_from_entities($columns);

        $filters = [];
        foreach ($entity->get_filters() as $filter) {
            $filters[] = $filter->get_unique_identifier();
        }
        $this->add_filters_from_entities($filters);
    }

    /**
     * Ensure we can view the report
     *
     * @return bool
     */
    protected function can_view(): bool {
        return true;
    }

    /**
     * Explicitly set availability of report
     *
     * @return bool
     */
    public static function is_available(): bool {
        return true;
    }
}

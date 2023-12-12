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

namespace core_files\reportbuilder\datasource;

use core\reportbuilder\local\entities\context;
use core_files\reportbuilder\local\entities\file;
use core_reportbuilder\datasource;
use core_reportbuilder\local\entities\user;
use core_reportbuilder\local\filters\boolean_select;

/**
 * Files datasource
 *
 * @package     core_files
 * @copyright   2022 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class files extends datasource {

    /**
     * Return user friendly name of the report source
     *
     * @return string
     */
    public static function get_name(): string {
        return get_string('files');
    }

    /**
     * Initialise report
     */
    protected function initialise(): void {
        $fileentity = new file();
        $filesalias = $fileentity->get_table_alias('files');

        $this->set_main_table('files', $filesalias);
        $this->add_entity($fileentity);

        // Join the context entity.
        $contextentity = new context();
        $contextalias = $contextentity->get_table_alias('context');
        $this->add_entity($contextentity
            ->add_join("LEFT JOIN {context} {$contextalias} ON {$contextalias}.id = {$filesalias}.contextid")
        );

        // Join the user entity.
        $userentity = new user();
        $useralias = $userentity->get_table_alias('user');
        $this->add_entity($userentity
            ->add_join("LEFT JOIN {user} {$useralias} ON {$useralias}.id = {$filesalias}.userid")
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
            'context:name',
            'user:fullname',
            'file:name',
            'file:type',
            'file:size',
            'file:timecreated',
        ];
    }

    /**
     * Return the column sorting that will be added to the report upon creation
     *
     * @return int[]
     */
    public function get_default_column_sorting(): array {
        return [
            'context:name' => SORT_ASC,
            'file:timecreated' => SORT_ASC,
        ];
    }

    /**
     * Return the filters that will be added to the report upon creation
     *
     * @return string[]
     */
    public function get_default_filters(): array {
        return [
            'file:size',
            'file:timecreated',
        ];
    }

    /**
     * Return the conditions that will be added to the report upon creation
     *
     * @return string[]
     */
    public function get_default_conditions(): array {
        return ['file:directory'];
    }

    /**
     * Return the condition values that will be set for the report upon creation
     *
     * @return array
     */
    public function get_default_condition_values(): array {
        return ['file:directory_operator' => boolean_select::NOT_CHECKED];
    }
}

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

namespace core_role\reportbuilder\datasource;

use core\reportbuilder\local\entities\context;
use core_reportbuilder\datasource;
use core_reportbuilder\local\entities\user;
use core_role\reportbuilder\local\entities\{role, role_assignment};

/**
 * Roles datasource
 *
 * @package     core_role
 * @copyright   2024 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class roles extends datasource {

    /**
     * Return user friendly name of the report source
     *
     * @return string
     */
    public static function get_name(): string {
        return get_string('roles', 'core_role');
    }

    /**
     * Initialise report
     */
    protected function initialise(): void {
        $contextentity = new context();
        $contextalias = $contextentity->get_table_alias('context');

        $roleentity = new role();
        $rolealias = $roleentity->get_table_alias('role');

        // Role table.
        $this->add_entity($roleentity->set_table_alias('context', $contextalias));
        $this->set_main_table('role', $rolealias);

        // Join role assignments.
        $roleassignmententity = new role_assignment();
        $roleassignmentalias = $roleassignmententity->get_table_alias('role_assignments');
        $this->add_entity($roleassignmententity);
        $this->add_join("JOIN {role_assignments} {$roleassignmentalias} ON {$roleassignmentalias}.roleid = {$rolealias}.id");

        // Join context.
        $this->add_entity($contextentity);
        $this->add_join("LEFT JOIN {context} {$contextalias} ON {$contextalias}.id = {$roleassignmentalias}.contextid");

        // Join user.
        $userentity = new user();
        $useralias = $userentity->get_table_alias('user');
        $this->add_entity($userentity
            ->add_join("LEFT JOIN {user} {$useralias} ON {$useralias}.id = {$roleassignmentalias}.userid"));

        $this->add_all_from_entities();
    }

    /**
     * Return the columns that will be added to the report upon creation
     *
     * @return string[]
     */
    public function get_default_columns(): array {
        return [
            'context:link',
            'role:originalname',
            'user:fullnamewithlink',
        ];
    }

    /**
     * Return the column sorting that will be added to the report upon creation
     *
     * @return int[]
     */
    public function get_default_column_sorting(): array {
        return [
            'context:link' => SORT_ASC,
            'role:originalname' => SORT_ASC,
            'user:fullnamewithlink' => SORT_ASC,
        ];
    }

    /**
     * Return the filters that will be added to the report upon creation
     *
     * @return string[]
     */
    public function get_default_filters(): array {
        return [
            'context:level',
            'role:name',
            'user:fullname',
        ];
    }

    /**
     * Return the conditions that will be added to the report upon creation
     *
     * @return string[]
     */
    public function get_default_conditions(): array {
        return [];
    }
}

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

namespace core_group\reportbuilder\datasource;

use core_group\reportbuilder\local\entities\{grouping, group, group_member};
use core_reportbuilder\datasource;
use core_reportbuilder\local\entities\{course, user};
use core_reportbuilder\local\helpers\database;

/**
 * Groups datasource
 *
 * @package     core_group
 * @copyright   2022 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class groups extends datasource {

    /**
     * Return user friendly name of the datasource
     *
     * @return string
     */
    public static function get_name(): string {
        return get_string('groups', 'core_group');
    }

    /**
     * Initialise report
     */
    protected function initialise(): void {
        $courseentity = new course();
        $coursealias = $courseentity->get_table_alias('course');

        $this->set_main_table('course', $coursealias);
        $this->add_entity($courseentity);

        $paramsiteid = database::generate_param_name();
        $this->add_base_condition_sql("{$coursealias}.id != :{$paramsiteid}", [$paramsiteid => SITEID]);

        // Re-use the context table alias/join from the course entity in subsequent entities.
        $contextalias = $courseentity->get_table_alias('context');
        $this->add_join($courseentity->get_context_join());

        // Group entity.
        $groupentity = (new group())
            ->set_table_alias('context', $contextalias);
        $groupsalias = $groupentity->get_table_alias('groups');
        $this->add_entity($groupentity
            ->add_join("LEFT JOIN {groups} {$groupsalias} ON {$groupsalias}.courseid = {$coursealias}.id"));

        // Grouping entity.
        $groupingentity = (new grouping())
            ->set_table_alias('context', $contextalias);
        $groupingsalias = $groupingentity->get_table_alias('groupings');

        // Sub-select for all groupings groups.
        $groupinginnerselect = "
            SELECT gr.*, grg.groupid
              FROM {groupings} gr
              JOIN {groupings_groups} grg ON grg.groupingid = gr.id";

        $this->add_entity($groupingentity
            ->add_joins($groupentity->get_joins())
            ->add_join("LEFT JOIN ({$groupinginnerselect}) {$groupingsalias}
                ON {$groupingsalias}.courseid = {$coursealias}.id AND {$groupingsalias}.groupid = {$groupsalias}.id"));

        // Group member entity.
        $groupmemberentity = new group_member();
        $groupsmembersalias = $groupmemberentity->get_table_alias('groups_members');
        $this->add_entity($groupmemberentity
            ->add_joins($groupentity->get_joins())
            ->add_join("LEFT JOIN {groups_members} {$groupsmembersalias} ON {$groupsmembersalias}.groupid = {$groupsalias}.id"));

        // User entity.
        $userentity = new user();
        $useralias = $userentity->get_table_alias('user');
        $this->add_entity($userentity
            ->add_joins($groupmemberentity->get_joins())
            ->add_join("LEFT JOIN {user} {$useralias} ON {$useralias}.id = {$groupsmembersalias}.userid"));

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
            'course:coursefullnamewithlink',
            'group:name',
            'user:fullname',
        ];
    }

    /**
     * Return the column sorting that will be added to the report upon creation
     *
     * @return int[]
     */
    public function get_default_column_sorting(): array {
        return [
            'course:coursefullnamewithlink' => SORT_ASC,
            'group:name' => SORT_ASC,
            'user:fullname' => SORT_ASC,
        ];
    }

    /**
     * Return the filters that will be added to the report as part of default setup
     *
     * @return string[]
     */
    public function get_default_filters(): array {
        return [
            'course:fullname',
            'group:name',
        ];
    }

    /**
     * Return the conditions that will be added to the report as part of default setup
     *
     * @return string[]
     */
    public function get_default_conditions(): array {
        return [
            'course:fullname',
            'group:name',
        ];
    }
}

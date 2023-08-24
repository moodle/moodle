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

namespace core_user\reportbuilder\datasource;

use lang_string;
use core_reportbuilder\datasource;
use core_reportbuilder\local\entities\user;
use core_reportbuilder\local\filters\boolean_select;
use core_reportbuilder\local\helpers\database;
use core_tag\reportbuilder\local\entities\tag;

/**
 * Users datasource
 *
 * @package   core_user
 * @copyright 2021 David Matamoros <davidmc@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class users extends datasource {

    /**
     * Return user friendly name of the datasource
     *
     * @return string
     */
    public static function get_name(): string {
        return get_string('users');
    }

    /**
     * Initialise report
     */
    protected function initialise(): void {
        global $CFG;

        $userentity = new user();
        $usertablealias = $userentity->get_table_alias('user');

        $this->set_main_table('user', $usertablealias);

        $userparamguest = database::generate_param_name();
        $this->add_base_condition_sql("{$usertablealias}.id != :{$userparamguest} AND {$usertablealias}.deleted = 0", [
            $userparamguest => $CFG->siteguest,
        ]);

        $this->add_entity($userentity);

        // Join the tag entity.
        $tagentity = (new tag())
            ->set_table_alias('tag', $userentity->get_table_alias('tag'))
            ->set_entity_title(new lang_string('interests'));
        $this->add_entity($tagentity
            ->add_joins($userentity->get_tag_joins()));

        // Add all columns/filters/conditions from entities to be available in custom reports.
        $this->add_all_from_entity($userentity->get_entity_name());

        // Add specific tag entity elements.
        $this->add_columns_from_entity($tagentity->get_entity_name(), ['name', 'namewithlink']);
        $this->add_filter($tagentity->get_filter('name'));
        $this->add_condition($tagentity->get_condition('name'));
    }

    /**
     * Return the columns that will be added to the report once is created
     *
     * @return string[]
     */
    public function get_default_columns(): array {
        return ['user:fullname', 'user:username', 'user:email'];
    }

    /**
     * Return the filters that will be added to the report once is created
     *
     * @return string[]
     */
    public function get_default_filters(): array {
        return ['user:fullname', 'user:username', 'user:email'];
    }

    /**
     * Return the conditions that will be added to the report once is created
     *
     * @return string[]
     */
    public function get_default_conditions(): array {
        return [
            'user:fullname',
            'user:username',
            'user:email',
            'user:suspended',
        ];
    }

    /**
     * Return the conditions values that will be added to the report once is created
     *
     * @return array
     */
    public function get_default_condition_values(): array {
        return [
            'user:suspended_operator' => boolean_select::NOT_CHECKED,
        ];
    }

    /**
     * Return the default sorting that will be added to the report once it is created
     *
     * @return array|int[]
     */
    public function get_default_column_sorting(): array {
        return [
            'user:fullname' => SORT_ASC,
        ];
    }
}

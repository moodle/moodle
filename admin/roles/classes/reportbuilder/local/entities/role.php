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

namespace core_role\reportbuilder\local\entities;

use context;
use context_helper;
use lang_string;
use stdClass;
use core_reportbuilder\local\entities\base;
use core_reportbuilder\local\filters\select;
use core_reportbuilder\local\report\{column, filter};

/**
 * Role entity
 *
 * @package     core_role
 * @copyright   2023 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class role extends base {

    /**
     * Database tables that this entity uses
     *
     * @return string[]
     */
    protected function get_default_tables(): array {
        return [
            'context',
            'role',
        ];
    }

    /**
     * The default title for this entity
     *
     * @return lang_string
     */
    protected function get_default_entity_title(): lang_string {
        return new lang_string('role');
    }

    /**
     * Initialise the entity
     *
     * @return base
     */
    public function initialise(): base {
        $columns = $this->get_all_columns();
        foreach ($columns as $column) {
            $this->add_column($column);
        }

        // All the filters defined by the entity can also be used as conditions.
        $filters = $this->get_all_filters();
        foreach ($filters as $filter) {
            $this
                ->add_filter($filter)
                ->add_condition($filter);
        }

        return $this;
    }

    /**
     * Returns list of all available columns
     *
     * @return column[]
     */
    protected function get_all_columns(): array {
        global $DB;

        $contextalias = $this->get_table_alias('context');
        $rolealias = $this->get_table_alias('role');

        // Name column.
        $columns[] = (new column(
            'name',
            new lang_string('rolefullname', 'core_role'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_fields("{$rolealias}.name, {$rolealias}.shortname, {$rolealias}.id, {$contextalias}.id AS contextid")
            ->add_fields(context_helper::get_preload_record_columns_sql($contextalias))
            // The sorting is on name, unless empty (determined by single space - thanks Oracle) then we use shortname.
            ->set_is_sortable(true, [
                "CASE WHEN " . $DB->sql_concat("{$rolealias}.name", "' '") . " = ' '
                      THEN {$rolealias}.shortname
                      ELSE {$rolealias}.name
                 END",
            ])
            ->set_callback(static function($name, stdClass $role): string {
                if ($name === null) {
                    return '';
                }

                context_helper::preload_from_record($role);
                $context = context::instance_by_id($role->contextid);

                return role_get_name($role, $context, ROLENAME_BOTH);
            });

        // Original name column.
        $columns[] = (new column(
            'originalname',
            new lang_string('roleoriginalname', 'core_role'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_fields("{$rolealias}.name, {$rolealias}.shortname")
            // The sorting is on name, unless empty (determined by single space - thanks Oracle) then we use shortname.
            ->set_is_sortable(true, [
                "CASE WHEN " . $DB->sql_concat("{$rolealias}.name", "' '") . " = ' '
                      THEN {$rolealias}.shortname
                      ELSE {$rolealias}.name
                 END",
            ])
            ->set_callback(static function($name, stdClass $role): string {
                if ($name === null) {
                    return '';
                }

                return role_get_name($role, null, ROLENAME_ORIGINAL);
            });

        // Short name column.
        $columns[] = (new column(
            'shortname',
            new lang_string('roleshortname', 'core_role'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_fields("{$rolealias}.shortname")
            ->set_is_sortable(true);

        // Description column.
        $descriptionfieldsql = "{$rolealias}.description";
        if ($DB->get_dbfamily() === 'oracle') {
            $descriptionfieldsql = $DB->sql_order_by_text($descriptionfieldsql, 1024);
        }
        $columns[] = (new column(
            'description',
            new lang_string('description'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_LONGTEXT)
            ->add_field($descriptionfieldsql, 'description')
            ->add_field("{$rolealias}.shortname")
            ->set_callback(static function($description, stdClass $role): string {
                if ($description === null) {
                    return '';
                }

                return role_get_description($role);
            });

        return $columns;
    }

    /**
     * Return list of all available filters
     *
     * @return filter[]
     */
    protected function get_all_filters(): array {
        $rolealias = $this->get_table_alias('role');

        // Name filter.
        $filters[] = (new filter(
            select::class,
            'name',
            new lang_string('rolefullname', 'core_role'),
            $this->get_entity_name(),
            "{$rolealias}.id"
        ))
            ->add_joins($this->get_joins())
            ->set_options_callback(static function(): array {
                return role_get_names(null, ROLENAME_ORIGINAL, true);
            });

        return $filters;
    }
}

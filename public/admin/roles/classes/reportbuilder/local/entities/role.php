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

use core\{context, context_helper};
use core\lang_string;
use core_reportbuilder\local\entities\base;
use core_reportbuilder\local\filters\select;
use core_reportbuilder\local\report\{column, filter};
use stdClass;

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
     * Returns list of all available columns
     *
     * @return column[]
     */
    protected function get_available_columns(): array {
        $contextalias = $this->get_table_alias('context');
        $rolealias = $this->get_table_alias('role');

        // Name column.
        $columns[] = (new column(
            'name',
            new lang_string('rolefullname', 'core_role'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->add_fields("{$rolealias}.name, {$rolealias}.shortname, {$rolealias}.id")
            ->add_fields(context_helper::get_preload_record_columns_sql($contextalias))
            // The sorting is on name, unless empty then we use shortname.
            ->set_is_sortable(true, [
                "CASE WHEN COALESCE({$rolealias}.name, '') = ''
                      THEN {$rolealias}.shortname
                      ELSE {$rolealias}.name
                 END",
            ])
            ->add_callback(static function(?string $name, stdClass $role): string {
                if ($name === null || $role->ctxid === null) {
                    return '';
                }

                context_helper::preload_from_record(clone $role);
                $context = context::instance_by_id($role->ctxid);

                return role_get_name($role, $context, ROLENAME_BOTH);
            });

        // Original name column.
        $columns[] = (new column(
            'originalname',
            new lang_string('roleoriginalname', 'core_role'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->add_fields("{$rolealias}.name, {$rolealias}.shortname")
            // The sorting is on name, unless empty then we use shortname.
            ->set_is_sortable(true, [
                "CASE WHEN COALESCE({$rolealias}.name, '') = ''
                      THEN {$rolealias}.shortname
                      ELSE {$rolealias}.name
                 END",
            ])
            ->add_callback(fn(?string $name, stdClass $role) => match ($name) {
                null => '',
                default => role_get_name($role, null, ROLENAME_ORIGINAL),
            });

        // Short name column.
        $columns[] = (new column(
            'shortname',
            new lang_string('roleshortname', 'core_role'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->add_field("{$rolealias}.shortname")
            ->set_is_sortable(true);

        // Archetype column.
        $columns[] = (new column(
            'archetype',
            new lang_string('archetype', 'core_role'),
            $this->get_entity_name(),
        ))
            ->add_joins($this->get_joins())
            ->add_field("{$rolealias}.archetype")
            ->add_callback(fn(?string $archetype) => match ($archetype) {
                null => '',
                '' => get_string('none'),
                default => get_string("archetype{$archetype}", 'core_role'),
            })
            ->set_is_sortable(true);

        // Description column.
        $columns[] = (new column(
            'description',
            new lang_string('description'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_LONGTEXT)
            ->add_fields("{$rolealias}.description, {$rolealias}.shortname")
            ->set_is_sortable(true)
            ->add_callback(fn(?string $description, stdClass $role) => match ($description) {
                null => '',
                default => role_get_description($role),
            });

        return $columns;
    }

    /**
     * Return list of all available filters
     *
     * @return filter[]
     */
    protected function get_available_filters(): array {
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

        // Archetype filter.
        $filters[] = (new filter(
            select::class,
            'archetype',
            new lang_string('archetype', 'core_role'),
            $this->get_entity_name(),
            "{$rolealias}.archetype",
        ))
            ->add_joins($this->get_joins())
            ->set_options_callback(static function(): array {
                return array_map(
                    fn(string $archetype) => get_string("archetype{$archetype}", 'core_role'),
                    get_role_archetypes(),
                );
            });

        return $filters;
    }
}

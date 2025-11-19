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

namespace core_tag\reportbuilder\local\entities;

use core_tag_collection;
use lang_string;
use stdClass;
use core_reportbuilder\local\entities\base;
use core_reportbuilder\local\filters\{boolean_select, select};
use core_reportbuilder\local\helpers\format;
use core_reportbuilder\local\report\{column, filter};

/**
 * Tag collection entity
 *
 * @package     core_tag
 * @copyright   2022 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class collection extends base {

    /**
     * Database tables that this entity uses
     *
     * @return string[]
     */
    protected function get_default_tables(): array {
        return [
            'tag_coll',
        ];
    }

    /**
     * The default title for this entity
     *
     * @return lang_string
     */
    protected function get_default_entity_title(): lang_string {
        return new lang_string('tagcollection', 'core_tag');
    }

    /**
     * Returns list of all available columns
     *
     * @return column[]
     */
    protected function get_available_columns(): array {
        $collectionalias = $this->get_table_alias('tag_coll');

        // Name.
        $columns[] = (new column(
            'name',
            new lang_string('name'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_fields("{$collectionalias}.name, {$collectionalias}.component, {$collectionalias}.isdefault,
                {$collectionalias}.id")
            ->set_is_sortable(true)
            ->add_callback(static function(?string $name, stdClass $collection): string {
                return core_tag_collection::display_name($collection);
            });

        // Default.
        $columns[] = (new column(
            'default',
            new lang_string('defautltagcoll', 'core_tag'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_BOOLEAN)
            ->add_fields("{$collectionalias}.isdefault")
            ->set_is_sortable(true)
            ->add_callback([format::class, 'boolean_as_text']);

        // Component.
        $columns[] = (new column(
            'component',
            new lang_string('component', 'core_tag'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_fields("{$collectionalias}.component")
            ->set_is_sortable(true);

        // Searchable.
        $columns[] = (new column(
            'searchable',
            new lang_string('searchable', 'core_tag'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_BOOLEAN)
            ->add_fields("{$collectionalias}.searchable")
            ->set_is_sortable(true)
            ->add_callback([format::class, 'boolean_as_text']);

        // Custom URL.
        $columns[] = (new column(
            'customurl',
            new lang_string('url'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_fields("{$collectionalias}.customurl")
            ->set_is_sortable(true);

        return $columns;
    }

    /**
     * Return list of all available filters
     *
     * @return filter[]
     */
    protected function get_available_filters(): array {
        $collectionalias = $this->get_table_alias('tag_coll');

        // Name.
        $filters[] = (new filter(
            select::class,
            'name',
            new lang_string('name'),
            $this->get_entity_name(),
            "{$collectionalias}.id"
        ))
            ->add_joins($this->get_joins())
            ->set_options_callback(static function(): array {
                global $DB;

                $collections = $DB->get_records('tag_coll', [], 'sortorder', 'id, name, component, isdefault');
                return array_map(static function(stdClass $collection): string {
                    return core_tag_collection::display_name($collection);
                }, $collections);
            });

        // Default.
        $filters[] = (new filter(
            boolean_select::class,
            'default',
            new lang_string('defautltagcoll', 'core_tag'),
            $this->get_entity_name(),
            "{$collectionalias}.isdefault"
        ))
            ->add_joins($this->get_joins());

        // Searchable.
        $filters[] = (new filter(
            boolean_select::class,
            'searchable',
            new lang_string('searchable', 'core_tag'),
            $this->get_entity_name(),
            "{$collectionalias}.searchable"
        ))
            ->add_joins($this->get_joins());

        return $filters;
    }
}

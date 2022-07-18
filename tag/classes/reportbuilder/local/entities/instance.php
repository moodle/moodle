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

use context;
use context_helper;
use core_collator;
use core_tag_area;
use html_writer;
use lang_string;
use stdClass;
use core_reportbuilder\local\entities\base;
use core_reportbuilder\local\filters\{date, select};
use core_reportbuilder\local\helpers\format;
use core_reportbuilder\local\report\{column, filter};

/**
 * Tag instance entity
 *
 * @package     core_tag
 * @copyright   2022 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class instance extends base {

    /**
     * Database tables that this entity uses and their default aliases
     *
     * @return array
     */
    protected function get_default_table_aliases(): array {
        return [
            'tag_instance' => 'ti',
            'context' => 'tictx',
        ];
    }

    /**
     * The default title for this entity
     *
     * @return lang_string
     */
    protected function get_default_entity_title(): lang_string {
        return new lang_string('taginstance', 'core_tag');
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
        $instancealias = $this->get_table_alias('tag_instance');
        $contextalias = $this->get_table_alias('context');

        // Area.
        $columns[] = (new column(
            'area',
            new lang_string('tagarea', 'core_tag'),
            $this->get_entity_name()

        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_fields("{$instancealias}.component, {$instancealias}.itemtype")
            ->set_is_sortable(true, ["{$instancealias}.component", "{$instancealias}.itemtype"])
            ->add_callback(static function($component, stdClass $area): string {
                if ($component === null) {
                    return '';
                }
                return (string) core_tag_area::display_name($area->component, $area->itemtype);
            });

        // Context.
        $columns[] = (new column(
            'context',
            new lang_string('context'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_join("LEFT JOIN {context} {$contextalias} ON {$contextalias}.id = {$instancealias}.contextid")
            ->add_fields("{$instancealias}.contextid, " . context_helper::get_preload_record_columns_sql($contextalias))
            // Sorting may not order alphabetically, but will at least group contexts together.
            ->set_is_sortable(true)
            ->add_callback(static function($contextid, stdClass $context): string {
                if ($contextid === null) {
                    return '';
                }

                context_helper::preload_from_record($context);
                return context::instance_by_id($contextid)->get_context_name();
            });

        // Context URL.
        $columns[] = (new column(
            'contexturl',
            new lang_string('contexturl'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_join("LEFT JOIN {context} {$contextalias} ON {$contextalias}.id = {$instancealias}.contextid")
            ->add_fields("{$instancealias}.contextid, " . context_helper::get_preload_record_columns_sql($contextalias))
            // Sorting may not order alphabetically, but will at least group contexts together.
            ->set_is_sortable(true)
            ->add_callback(static function($contextid, stdClass $context): string {
                if ($contextid === null) {
                    return '';
                }

                context_helper::preload_from_record($context);
                $context = context::instance_by_id($contextid);

                return html_writer::link($context->get_url(), $context->get_context_name());
            });

        // Component.
        $columns[] = (new column(
            'component',
            new lang_string('component', 'core_tag'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_fields("{$instancealias}.component")
            ->set_is_sortable(true);

        // Item type.
        $columns[] = (new column(
            'itemtype',
            new lang_string('itemtype', 'core_tag'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_fields("{$instancealias}.itemtype")
            ->set_is_sortable(true);

        // Item ID.
        $columns[] = (new column(
            'itemid',
            new lang_string('itemid', 'core_tag'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_INTEGER)
            ->add_fields("{$instancealias}.itemid")
            ->set_is_sortable(true)
            ->set_disabled_aggregation_all();

        // Time created.
        $columns[] = (new column(
            'timecreated',
            new lang_string('timecreated', 'core_reportbuilder'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TIMESTAMP)
            ->add_fields("{$instancealias}.timecreated")
            ->set_is_sortable(true)
            ->add_callback([format::class, 'userdate']);

        // Time modified.
        $columns[] = (new column(
            'timemodified',
            new lang_string('timemodified', 'core_reportbuilder'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TIMESTAMP)
            ->add_fields("{$instancealias}.timemodified")
            ->set_is_sortable(true)
            ->add_callback([format::class, 'userdate']);

        return $columns;
    }

    /**
     * Return list of all available filters
     *
     * @return filter[]
     */
    protected function get_all_filters(): array {
        global $DB;

        $instancealias = $this->get_table_alias('tag_instance');

        // Area.
        $filters[] = (new filter(
            select::class,
            'area',
            new lang_string('tagarea', 'core_tag'),
            $this->get_entity_name(),
            $DB->sql_concat("{$instancealias}.component", "'/'", "{$instancealias}.itemtype")
        ))
            ->add_joins($this->get_joins())
            ->set_options_callback(static function(): array {
                $options = [];
                foreach (core_tag_area::get_areas() as $areas) {
                    foreach ($areas as $area) {
                        $options["{$area->component}/{$area->itemtype}"] = core_tag_area::display_name(
                            $area->component, $area->itemtype);
                    }
                }

                core_collator::asort($options);
                return $options;
            });

        // Time created.
        $filters[] = (new filter(
            date::class,
            'timecreated',
            new lang_string('timecreated', 'core_reportbuilder'),
            $this->get_entity_name(),
            "{$instancealias}.timecreated"
        ))
            ->add_joins($this->get_joins())
            ->set_limited_operators([
                date::DATE_ANY,
                date::DATE_CURRENT,
                date::DATE_LAST,
                date::DATE_RANGE,
            ]);

        // Time modified.
        $filters[] = (new filter(
            date::class,
            'timemodified',
            new lang_string('timemodified', 'core_reportbuilder'),
            $this->get_entity_name(),
            "{$instancealias}.timemodified"
        ))
            ->add_joins($this->get_joins())
            ->set_limited_operators([
                date::DATE_ANY,
                date::DATE_CURRENT,
                date::DATE_LAST,
                date::DATE_RANGE,
            ]);

        return $filters;
    }
}

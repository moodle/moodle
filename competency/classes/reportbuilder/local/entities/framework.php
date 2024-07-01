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

namespace core_competency\reportbuilder\local\entities;

use core\{context, context_helper};
use core\lang_string;
use core_reportbuilder\local\entities\base;
use core_reportbuilder\local\filters\{boolean_select, date, select, text};
use core_reportbuilder\local\helpers\format;
use core_reportbuilder\local\report\{column, filter};
use stdClass;

/**
 * Competency framework entity
 *
 * @package     core_competency
 * @copyright   2024 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class framework extends base {

    /**
     * Database tables that this entity uses
     *
     * @return string[]
     */
    protected function get_default_tables(): array {
        return [
            'competency_framework',
            'context',
        ];
    }

    /**
     * The default title for this entity
     *
     * @return lang_string
     */
    protected function get_default_entity_title(): lang_string {
        return new lang_string('competencyframework', 'core_competency');
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
        $frameworkalias = $this->get_table_alias('competency_framework');
        $contextalias = $this->get_table_alias('context');

        // Name.
        $columns[] = (new column(
            'name',
            new lang_string('name'),
            $this->get_entity_name(),
        ))
            ->add_joins($this->get_joins())
            ->add_field("{$frameworkalias}.shortname")
            ->set_is_sortable(true);

        // Description.
        $columns[] = (new column(
            'description',
            new lang_string('description'),
            $this->get_entity_name(),
        ))
            ->add_joins($this->get_joins())
            ->add_join($this->get_context_join())
            ->set_type(column::TYPE_LONGTEXT)
            ->add_fields("{$frameworkalias}.description, {$frameworkalias}.descriptionformat")
            ->add_fields(context_helper::get_preload_record_columns_sql($contextalias))
            ->set_is_sortable(true)
            ->add_callback(static function(?string $description, stdClass $framework): string {
                if ($description === null) {
                    return '';
                }

                context_helper::preload_from_record(clone $framework);
                $context = context::instance_by_id($framework->ctxid);

                return format_text($description, $framework->descriptionformat, ['context' => $context->id]);
            });

        // ID number.
        $columns[] = (new column(
            'idnumber',
            new lang_string('idnumber'),
            $this->get_entity_name(),
        ))
            ->add_joins($this->get_joins())
            ->add_field("{$frameworkalias}.idnumber")
            ->set_is_sortable(true);

        // Scale.
        $columns[] = (new column(
            'scale',
            new lang_string('scale'),
            $this->get_entity_name(),
        ))
            ->add_joins($this->get_joins())
            ->add_field("{$frameworkalias}.scaleid")
            ->add_callback(static function(?string $scaleid): string {
                $scales = get_scales_menu();
                return (string) ($scales[(int) $scaleid] ?? $scaleid);
            });

        // Visible.
        $columns[] = (new column(
            'visible',
            new lang_string('visible'),
            $this->get_entity_name(),
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_BOOLEAN)
            ->add_field("{$frameworkalias}.visible")
            ->set_is_sortable(true)
            ->add_callback([format::class, 'boolean_as_text']);

        // Time created.
        $columns[] = (new column(
            'timecreated',
            new lang_string('timecreated', 'core_reportbuilder'),
            $this->get_entity_name(),
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TIMESTAMP)
            ->add_field("{$frameworkalias}.timecreated")
            ->set_is_sortable(true)
            ->add_callback([format::class, 'userdate']);

        // Time modified.
        $columns[] = (new column(
            'timemodified',
            new lang_string('timemodified', 'core_reportbuilder'),
            $this->get_entity_name(),
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TIMESTAMP)
            ->add_field("{$frameworkalias}.timemodified")
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
        $frameworkalias = $this->get_table_alias('competency_framework');

        // Name.
        $filters[] = (new filter(
            text::class,
            'name',
            new lang_string('name'),
            $this->get_entity_name(),
            "{$frameworkalias}.shortname",
        ))
            ->add_joins($this->get_joins());

        // ID number.
        $filters[] = (new filter(
            text::class,
            'idnumber',
            new lang_string('idnumber'),
            $this->get_entity_name(),
            "{$frameworkalias}.idnumber",
        ))
            ->add_joins($this->get_joins());

        // Scale.
        $filters[] = (new filter(
            select::class,
            'scale',
            new lang_string('scale'),
            $this->get_entity_name(),
            "{$frameworkalias}.scaleid",
        ))
            ->add_joins($this->get_joins())
            ->set_options_callback('get_scales_menu');

        // Visible.
        $filters[] = (new filter(
            boolean_select::class,
            'visible',
            new lang_string('visible'),
            $this->get_entity_name(),
            "{$frameworkalias}.visible",
        ))
            ->add_joins($this->get_joins());

        // Time created.
        $filters[] = (new filter(
            date::class,
            'timecreated',
            new lang_string('timecreated', 'core_reportbuilder'),
            $this->get_entity_name(),
            "{$frameworkalias}.timecreated",
        ))
            ->add_joins($this->get_joins());

        // Time modified.
        $filters[] = (new filter(
            date::class,
            'timemodified',
            new lang_string('timemodified', 'core_reportbuilder'),
            $this->get_entity_name(),
            "{$frameworkalias}.timemodified",
        ))
            ->add_joins($this->get_joins());

        return $filters;
    }

    /**
     * Return context join
     *
     * @return string
     */
    private function get_context_join(): string {

        // If the context table is already joined, we don't need to do that again.
        if ($this->has_table_join_alias('context')) {
            return '';
        }

        $frameworkalias = $this->get_table_alias('competency_framework');
        $contextalias = $this->get_table_alias('context');

        return "LEFT JOIN {context} {$contextalias} ON {$contextalias}.id = {$frameworkalias}.contextid";
    }
}

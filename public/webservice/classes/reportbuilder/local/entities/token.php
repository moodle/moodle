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

namespace core_webservice\reportbuilder\local\entities;

use lang_string;
use core_reportbuilder\local\entities\base;
use core_reportbuilder\local\filters\{text, date};
use core_reportbuilder\local\helpers\format;
use core_reportbuilder\local\report\{column, filter};

/**
 * External token report builder entity
 *
 * @package    core_webservice
 * @copyright  2023 Mikel Martín <mikel@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class token extends base {

    /**
     * Database tables that this entity uses and their default aliases
     *
     * @return array
     */
    protected function get_default_tables(): array {
        return [
            'external_tokens',
        ];
    }

    /**
     * The default title for this entity in the list of columns/conditions/filters in the report builder
     *
     * @return lang_string
     */
    protected function get_default_entity_title(): lang_string {
        return new lang_string('token', 'core_webservice');
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
        $tokenalias = $this->get_table_alias('external_tokens');

        // Token name column.
        $columnns[] = (new column(
            'name',
            new lang_string('name'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_field("{$tokenalias}.name")
            ->set_is_sortable(true);

        // IP restriction column.
        $columnns[] = (new column(
            'iprestriction',
            new lang_string('iprestriction', 'core_webservice'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_field("{$tokenalias}.iprestriction");

        // Valid until column.
        $columnns[] = (new column(
            'validuntil',
            new lang_string('validuntil', 'core_webservice'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TIMESTAMP)
            ->add_field("{$tokenalias}.validuntil")
            ->set_is_sortable(true)
            ->add_callback([format::class, 'userdate'], get_string('strftimedatetime', 'core_langconfig'))
            ->add_callback(fn($value) => $value ?: get_string('validuntil_empty', 'core_webservice'));

        // Last access column.
        $columnns[] = (new column(
            'lastaccess',
            new lang_string('lastaccess'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TIMESTAMP)
            ->add_field("{$tokenalias}.lastaccess")
            ->set_is_sortable(true)
            ->add_callback([format::class, 'userdate'])
            ->add_callback(fn($value) => $value ?: get_string('never'));

        return $columnns;
    }

    /**
     * Return list of all available filters
     *
     * @return filter[]
     */
    protected function get_all_filters(): array {
        global $DB;

        $tokenalias = $this->get_table_alias('external_tokens');

        // Name filter.
        $filters[] = (new filter(
            text::class,
            'name',
            new lang_string('tokenname', 'core_webservice'),
            $this->get_entity_name(),
            "{$tokenalias}.name"
        ))
            ->add_joins($this->get_joins());

        // Valid until filter.
        $filters[] = (new filter(
            date::class,
            'validuntil',
            new lang_string('validuntil', 'core_webservice'),
            $this->get_entity_name(),
            "{$tokenalias}.validuntil"
        ))
            ->add_joins($this->get_joins());

        return $filters;
    }
}

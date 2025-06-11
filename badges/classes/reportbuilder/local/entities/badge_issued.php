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

namespace core_badges\reportbuilder\local\entities;

use lang_string;
use core_reportbuilder\local\entities\base;
use core_reportbuilder\local\filters\{boolean_select, date};
use core_reportbuilder\local\helpers\format;
use core_reportbuilder\local\report\{column, filter};

/**
 * Badge issued entity
 *
 * @package     core_badges
 * @copyright   2022 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class badge_issued extends base {

    /**
     * Database tables that this entity uses
     *
     * @return string[]
     */
    protected function get_default_tables(): array {
        return [
            'badge_issued',
        ];
    }

    /**
     * The default title for this entity
     *
     * @return lang_string
     */
    protected function get_default_entity_title(): lang_string {
        return new lang_string('badgeissued', 'core_badges');
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
        $badgeissuedalias = $this->get_table_alias('badge_issued');

        // Date issued.
        $columns[] = (new column(
            'issued',
            new lang_string('dateawarded', 'core_badges'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TIMESTAMP)
            ->add_field("{$badgeissuedalias}.dateissued")
            ->set_is_sortable(true)
            ->add_callback([format::class, 'userdate']);

        // Date expires.
        $columns[] = (new column(
            'expire',
            new lang_string('expirydate', 'core_badges'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TIMESTAMP)
            ->add_field("{$badgeissuedalias}.dateexpire")
            ->set_is_sortable(true)
            ->add_callback([format::class, 'userdate']);

        // Visible.
        $columns[] = (new column(
            'visible',
            new lang_string('visible', 'core_badges'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_BOOLEAN)
            ->add_fields("{$badgeissuedalias}.visible")
            ->add_callback([format::class, 'boolean_as_text']);

        return $columns;
    }

    /**
     * Return list of all available filters
     *
     * @return filter[]
     */
    protected function get_all_filters(): array {
        $badgealias = $this->get_table_alias('badge_issued');

        // Date issued.
        $filters[] = (new filter(
            date::class,
            'issued',
            new lang_string('dateawarded', 'core_badges'),
            $this->get_entity_name(),
            "{$badgealias}.dateissued"
        ))
            ->add_joins($this->get_joins());

        // Date expires.
        $filters[] = (new filter(
            date::class,
            'expires',
            new lang_string('expirydate', 'core_badges'),
            $this->get_entity_name(),
            "{$badgealias}.dateexpire"
        ))
            ->add_joins($this->get_joins());

        // Visible.
        $filters[] = (new filter(
            boolean_select::class,
            'visible',
            new lang_string('visible', 'core_badges'),
            $this->get_entity_name(),
            "{$badgealias}.visible"
        ))
            ->add_joins($this->get_joins());

        return $filters;
    }
}

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

namespace core_group\reportbuilder\local\entities;

use core\lang_string;
use core_reportbuilder\local\filters\{date, text};
use core_reportbuilder\local\entities\base;
use core_reportbuilder\local\helpers\format;
use core_reportbuilder\local\report\{column, filter};

/**
 * Group member entity
 *
 * @package     core_group
 * @copyright   2022 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class group_member extends base {

    /**
     * Database tables that this entity uses
     *
     * @return string[]
     */
    protected function get_default_tables(): array {
        return [
            'groups_members',
        ];
    }

    /**
     * The default title for this entity
     *
     * @return lang_string
     */
    protected function get_default_entity_title(): lang_string {
        return new lang_string('groupmember', 'core_group');
    }

    /**
     * Returns list of all available columns
     *
     * @return column[]
     */
    protected function get_available_columns(): array {
        $groupsmembersalias = $this->get_table_alias('groups_members');

        // Time added column.
        $columns[] = (new column(
            'timeadded',
            new lang_string('timeadded', 'core_reportbuilder'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TIMESTAMP)
            ->add_fields("{$groupsmembersalias}.timeadded")
            ->set_is_sortable(true)
            ->set_callback([format::class, 'userdate']);

        // Component column.
        $columns[] = (new column(
            'component',
            new lang_string('plugin', 'core'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_fields("{$groupsmembersalias}.component")
            ->set_is_sortable(true);

        return $columns;
    }

    /**
     * Return list of all available filters
     *
     * @return filter[]
     */
    protected function get_available_filters(): array {
        $groupsmembersalias = $this->get_table_alias('groups_members');

        // Time added filter.
        $filters[] = (new filter(
            date::class,
            'timeadded',
            new lang_string('timeadded', 'core_reportbuilder'),
            $this->get_entity_name(),
            "{$groupsmembersalias}.timeadded"
        ))
            ->add_joins($this->get_joins());

        // Component filter.
        $filters[] = (new filter(
            text::class,
            'component',
            new lang_string('plugin', 'core'),
            $this->get_entity_name(),
            "{$groupsmembersalias}.component"
        ))
            ->add_joins($this->get_joins());

        return $filters;
    }
}

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

namespace mod_forum\reportbuilder\local\entities;

use core\{context, context_helper};
use core\lang_string;
use core_reportbuilder\local\entities\base;
use core_reportbuilder\local\filters\{date, text};
use core_reportbuilder\local\helpers\format;
use core_reportbuilder\local\report\{column, filter};
use stdClass;

/**
 * Forum discussion entity
 *
 * @package     mod_forum
 * @copyright   2025 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class discussion extends base {
    /**
     * Database tables that this entity uses
     *
     * @return string[]
     */
    protected function get_default_tables(): array {
        return [
            'context',
            'forum_discussions',
        ];
    }

    /**
     * The default title for this entity
     *
     * @return lang_string
     */
    protected function get_default_entity_title(): lang_string {
        return new lang_string('discussion', 'mod_forum');
    }

    /**
     * Returns list of all available columns
     *
     * @return column[]
     */
    protected function get_available_columns(): array {
        [
            'context' => $contextalias,
            'forum_discussions' => $discussionalias,
        ] = $this->get_table_aliases();

        // Name.
        $columns[] = (new column(
            'name',
            new lang_string('name'),
            $this->get_entity_name(),
        ))
            ->add_joins($this->get_joins())
            ->add_field("{$discussionalias}.name")
            ->add_fields(context_helper::get_preload_record_columns_sql($contextalias))
            ->set_is_sortable(true)
            ->set_callback(static function (?string $name, stdClass $discussion): string {
                if ($name === null || $discussion->ctxid === null) {
                    return '';
                }

                context_helper::preload_from_record(clone $discussion);
                $context = context::instance_by_id($discussion->ctxid);

                return format_string($name, true, ['context' => $context]);
            });

        // Time start.
        $columns[] = (new column(
            'timestart',
            new lang_string('displaystart', 'mod_forum'),
            $this->get_entity_name(),
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TIMESTAMP)
            ->add_fields("{$discussionalias}.timestart")
            ->set_is_sortable(true)
            ->set_callback([format::class, 'userdate']);

        // Time end.
        $columns[] = (new column(
            'timeend',
            new lang_string('displayend', 'mod_forum'),
            $this->get_entity_name(),
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TIMESTAMP)
            ->add_fields("{$discussionalias}.timeend")
            ->set_is_sortable(true)
            ->set_callback([format::class, 'userdate']);

        // Time modified.
        $columns[] = (new column(
            'timemodified',
            new lang_string('timemodified', 'core_reportbuilder'),
            $this->get_entity_name(),
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TIMESTAMP)
            ->add_fields("{$discussionalias}.timemodified")
            ->set_is_sortable(true)
            ->set_callback([format::class, 'userdate']);

        return $columns;
    }

    /**
     * Return list of all available filters
     *
     * @return filter[]
     */
    protected function get_available_filters(): array {
        $discussionalias = $this->get_table_alias('forum_discussions');

        // Name.
        $filters[] = (new filter(
            text::class,
            'name',
            new lang_string('name'),
            $this->get_entity_name(),
            "{$discussionalias}.name",
        ))
            ->add_joins($this->get_joins());

        // Time start.
        $filters[] = (new filter(
            date::class,
            'timestart',
            new lang_string('displaystart', 'mod_forum'),
            $this->get_entity_name(),
            "{$discussionalias}.timestart",
        ))
            ->add_joins($this->get_joins());

        // Time end.
        $filters[] = (new filter(
            date::class,
            'timeend',
            new lang_string('displayend', 'mod_forum'),
            $this->get_entity_name(),
            "{$discussionalias}.timeend",
        ))
            ->add_joins($this->get_joins());

        // Time modified.
        $filters[] = (new filter(
            date::class,
            'timemodified',
            new lang_string('timemodified', 'core_reportbuilder'),
            $this->get_entity_name(),
            "{$discussionalias}.timemodified",
        ))
            ->add_joins($this->get_joins());

        return $filters;
    }
}

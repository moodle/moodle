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

namespace core_comment\reportbuilder\local\entities;

use core\{context, context_helper};
use core\lang_string;
use core_reportbuilder\local\entities\base;
use core_reportbuilder\local\filters\{date, text};
use core_reportbuilder\local\helpers\format;
use core_reportbuilder\local\report\{column, filter};
use stdClass;

/**
 * Comment entity
 *
 * @package     core_comment
 * @copyright   2022 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class comment extends base {

    /**
     * Database tables that this entity uses
     *
     * @return string[]
     */
    protected function get_default_tables(): array {
        return [
            'comments',
            'context',
        ];
    }

    /**
     * The default title for this entity
     *
     * @return lang_string
     */
    protected function get_default_entity_title(): lang_string {
        return new lang_string('comment', 'core_comment');
    }

    /**
     * Returns list of all available columns
     *
     * @return column[]
     */
    protected function get_available_columns(): array {
        $commentalias = $this->get_table_alias('comments');
        $contextalias = $this->get_table_alias('context');

        // Content.
        $columns[] = (new column(
            'content',
            new lang_string('content'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_LONGTEXT)
            ->add_join($this->get_context_join())
            ->add_fields("{$commentalias}.content, {$commentalias}.format")
            ->add_fields(context_helper::get_preload_record_columns_sql($contextalias))
            ->set_is_sortable(true)
            ->add_callback(static function(?string $content, stdClass $comment): string {
                if ($content === null || $comment->ctxid === null) {
                    return '';
                }

                context_helper::preload_from_record(clone $comment);
                $context = context::instance_by_id($comment->ctxid);

                return format_text($content, $comment->format, ['context' => $context]);
            });

        // Component.
        $columns[] = (new column(
            'component',
            new lang_string('plugin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->add_fields("{$commentalias}.component")
            ->set_is_sortable(true);

        // Area.
        $columns[] = (new column(
            'area',
            new lang_string('pluginarea'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->add_fields("{$commentalias}.commentarea")
            ->set_is_sortable(true);

        // Item ID.
        $columns[] = (new column(
            'itemid',
            new lang_string('pluginitemid'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->add_fields("{$commentalias}.itemid")
            ->set_is_sortable(true);

        // Time created.
        $columns[] = (new column(
            'timecreated',
            new lang_string('timecreated', 'core_reportbuilder'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TIMESTAMP)
            ->add_fields("{$commentalias}.timecreated")
            ->set_is_sortable(true)
            ->add_callback([format::class, 'userdate']);

        return $columns;
    }

    /**
     * Return list of all available filters
     *
     * @return filter[]
     */
    protected function get_available_filters(): array {
        $commentalias = $this->get_table_alias('comments');

        // Content.
        $filters[] = (new filter(
            text::class,
            'content',
            new lang_string('content'),
            $this->get_entity_name(),
            "{$commentalias}.content"
        ))
            ->add_joins($this->get_joins());

        // Time created.
        $filters[] = (new filter(
            date::class,
            'timecreated',
            new lang_string('timecreated', 'core_reportbuilder'),
            $this->get_entity_name(),
            "{$commentalias}.timecreated"
        ))
            ->add_joins($this->get_joins())
            ->set_limited_operators([
                date::DATE_ANY,
                date::DATE_RANGE,
                date::DATE_LAST,
                date::DATE_CURRENT,
            ]);

        return $filters;
    }

    /**
     * Return syntax for joining on the context table
     *
     * @return string
     */
    public function get_context_join(): string {
        $commentalias = $this->get_table_alias('comments');
        $contextalias = $this->get_table_alias('context');

        return "LEFT JOIN {context} {$contextalias} ON {$contextalias}.id = {$commentalias}.contextid";
    }
}

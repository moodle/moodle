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

use context;
use context_helper;
use html_writer;
use lang_string;
use stdClass;
use core_reportbuilder\local\entities\base;
use core_reportbuilder\local\filters\{date, text};
use core_reportbuilder\local\helpers\format;
use core_reportbuilder\local\report\{column, filter};

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

        $commentalias = $this->get_table_alias('comments');
        $contextalias = $this->get_table_alias('context');

        // Content.
        $contentfieldsql = "{$commentalias}.content";
        if ($DB->get_dbfamily() === 'oracle') {
            $contentfieldsql = $DB->sql_order_by_text($contentfieldsql, 1024);
        }
        $columns[] = (new column(
            'content',
            new lang_string('content'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_LONGTEXT)
            ->add_join($this->get_context_join())
            ->add_field($contentfieldsql, 'content')
            ->add_fields("{$commentalias}.format, {$commentalias}.contextid, " .
                context_helper::get_preload_record_columns_sql($contextalias))
            ->add_callback(static function($content, stdClass $comment): string {
                if ($content === null) {
                    return '';
                }

                context_helper::preload_from_record($comment);
                $context = context::instance_by_id($comment->contextid);

                return format_text($content, $comment->format, ['context' => $context]);
            });

        // Context.
        $columns[] = (new column(
            'context',
            new lang_string('context'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_join($this->get_context_join())
            ->add_fields("{$commentalias}.contextid, " . context_helper::get_preload_record_columns_sql($contextalias))
            // Sorting may not order alphabetically, but will at least group contexts together.
            ->set_is_sortable(true)
            ->set_is_deprecated('See \'context:name\' for replacement')
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
            ->add_join($this->get_context_join())
            ->add_fields("{$commentalias}.contextid, " . context_helper::get_preload_record_columns_sql($contextalias))
            // Sorting may not order alphabetically, but will at least group contexts together.
            ->set_is_sortable(true)
            ->set_is_deprecated('See \'context:link\' for replacement')
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
            new lang_string('plugin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_fields("{$commentalias}.component")
            ->set_is_sortable(true);

        // Area.
        $columns[] = (new column(
            'area',
            new lang_string('pluginarea'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
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
    protected function get_all_filters(): array {
        global $DB;

        $commentalias = $this->get_table_alias('comments');

        // Content.
        $filters[] = (new filter(
            text::class,
            'content',
            new lang_string('content'),
            $this->get_entity_name(),
            $DB->sql_cast_to_char("{$commentalias}.content")
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

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

namespace core_message\reportbuilder\local\entities;

use core\lang_string;
use core_message\api;
use core_reportbuilder\local\entities\base;
use core_reportbuilder\local\filters\{boolean_select, date, select, text};
use core_reportbuilder\local\helpers\format;
use core_reportbuilder\local\report\{column, filter};

/**
 * Conversation entity
 *
 * @package     core_message
 * @copyright   2025 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class conversation extends base {
    /**
     * Database tables that this entity uses
     *
     * @return string[]
     */
    protected function get_default_tables(): array {
        return [
            'message_conversations',
        ];
    }

    /**
     * The default title for this entity
     *
     * @return lang_string
     */
    protected function get_default_entity_title(): lang_string {
        return new lang_string('conversation', 'core_message');
    }

    /**
     * Returns list of all available columns
     *
     * @return column[]
     */
    protected function get_available_columns(): array {
        $conversationalias = $this->get_table_alias('message_conversations');

        // Type.
        $columns[] = (new column(
            'type',
            new lang_string('conversationtype', 'core_message'),
            $this->get_entity_name(),
        ))
            ->add_joins($this->get_joins())
            ->add_field("{$conversationalias}.type")
            ->set_is_sortable(true)
            ->add_callback(static function (?string $type): string {
                $types = [
                    api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL => new lang_string('individualconversations', 'core_message'),
                    api::MESSAGE_CONVERSATION_TYPE_GROUP => new lang_string('groupconversations', 'core_message'),
                    api::MESSAGE_CONVERSATION_TYPE_SELF => new lang_string('selfconversation', 'core_message'),
                ];
                if ($type === null || !array_key_exists($type, $types)) {
                    return '';
                }
                return (string) $types[$type];
            });

        // Name.
        $columns[] = (new column(
            'name',
            new lang_string('name'),
            $this->get_entity_name(),
        ))
            ->add_joins($this->get_joins())
            ->add_field("{$conversationalias}.name")
            ->set_is_sortable(true)
            ->add_callback(static function (?string $name): string {
                if ($name === null) {
                    return '';
                }
                return format_string($name);
            });

        // Enabled.
        $columns[] = (new column(
            'enabled',
            new lang_string('enabled', 'core_message'),
            $this->get_entity_name(),
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_BOOLEAN)
            ->add_field("{$conversationalias}.enabled")
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
            ->add_field("{$conversationalias}.timecreated")
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
        $conversationalias = $this->get_table_alias('message_conversations');

        // Type.
        $filters[] = (new filter(
            select::class,
            'type',
            new lang_string('conversationtype', 'core_message'),
            $this->get_entity_name(),
            "{$conversationalias}.type",
        ))
            ->add_joins($this->get_joins())
            ->set_options([
                api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL => new lang_string('individualconversations', 'core_message'),
                api::MESSAGE_CONVERSATION_TYPE_GROUP => new lang_string('groupconversations', 'core_message'),
                api::MESSAGE_CONVERSATION_TYPE_SELF => new lang_string('selfconversation', 'core_message'),
            ]);

        // Name.
        $filters[] = (new filter(
            text::class,
            'name',
            new lang_string('name'),
            $this->get_entity_name(),
            "{$conversationalias}.name",
        ))
            ->add_joins($this->get_joins());

        // Enabled.
        $filters[] = (new filter(
            boolean_select::class,
            'enabled',
            new lang_string('enabled', 'core_message'),
            $this->get_entity_name(),
            "{$conversationalias}.enabled",
        ))
            ->add_joins($this->get_joins());

        // Time created.
        $filters[] = (new filter(
            date::class,
            'timecreated',
            new lang_string('timecreated', 'core_reportbuilder'),
            $this->get_entity_name(),
            "{$conversationalias}.timecreated",
        ))
            ->add_joins($this->get_joins());

        return $filters;
    }
}

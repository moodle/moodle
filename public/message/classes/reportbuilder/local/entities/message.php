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
use core_reportbuilder\local\entities\base;
use core_reportbuilder\local\filters\{date, text};
use core_reportbuilder\local\helpers\format;
use core_reportbuilder\local\report\{column, filter};
use stdClass;

/**
 * Message entity
 *
 * @package     core_message
 * @copyright   2025 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class message extends base {
    /**
     * Database tables that this entity uses
     *
     * @return string[]
     */
    protected function get_default_tables(): array {
        return [
            'messages',
        ];
    }

    /**
     * The default title for this entity
     *
     * @return lang_string
     */
    protected function get_default_entity_title(): lang_string {
        return new lang_string('message', 'core_message');
    }

    /**
     * Returns list of all available columns
     *
     * @return column[]
     */
    protected function get_available_columns(): array {
        $messagealias = $this->get_table_alias('messages');

        // Subject.
        $columns[] = (new column(
            'subject',
            new lang_string('subject'),
            $this->get_entity_name(),
        ))
            ->add_joins($this->get_joins())
            ->add_field("{$messagealias}.subject")
            ->set_is_sortable(true)
            ->add_callback(static function (?string $subject): string {
                if ($subject === null) {
                    return '';
                }
                return format_string($subject);
            });

        // Message.
        $columns[] = (new column(
            'message',
            new lang_string('message', 'core_message'),
            $this->get_entity_name(),
        ))
            ->add_joins($this->get_joins())
            ->add_fields("{$messagealias}.fullmessage, {$messagealias}.fullmessageformat, {$messagealias}.fullmessagetrust")
            ->set_is_sortable(true)
            ->add_callback(static function (?string $fullmessage, stdClass $message): string {
                if ($fullmessage === null) {
                    return '';
                }
                return format_text($fullmessage, $message->fullmessageformat, ['trusted' => $message->fullmessagetrust]);
            });

        // Time created.
        $columns[] = (new column(
            'timecreated',
            new lang_string('timecreated', 'core_reportbuilder'),
            $this->get_entity_name(),
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TIMESTAMP)
            ->add_field("{$messagealias}.timecreated")
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
        $messagealias = $this->get_table_alias('messages');

        // Subject.
        $filters[] = (new filter(
            text::class,
            'subject',
            new lang_string('subject'),
            $this->get_entity_name(),
            "{$messagealias}.subject",
        ))
            ->add_joins($this->get_joins());

        // Message.
        $filters[] = (new filter(
            text::class,
            'message',
            new lang_string('message', 'core_message'),
            $this->get_entity_name(),
            "{$messagealias}.fullmessage",
        ))
            ->add_joins($this->get_joins());

        // Time created.
        $filters[] = (new filter(
            date::class,
            'timecreated',
            new lang_string('timecreated', 'core_reportbuilder'),
            $this->get_entity_name(),
            "{$messagealias}.timecreated",
        ))
            ->add_joins($this->get_joins());

        return $filters;
    }
}

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

namespace core_message\reportbuilder\datasource;

use core\lang_string;
use core_message\reportbuilder\local\entities\{conversation, message};
use core_reportbuilder\datasource;
use core_reportbuilder\local\entities\user;
use core_reportbuilder\local\filters\text;
use core_reportbuilder\local\helpers\database;

/**
 * Messages datasource
 *
 * @package     core_message
 * @copyright   2025 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class messages extends datasource {
    /**
     * Return user friendly name of the datasource
     *
     * @return string
     */
    public static function get_name(): string {
        return get_string('messages', 'core_message');
    }

    /**
     * Initialise report
     */
    protected function initialise(): void {
        // Message.
        $messageentity = new message();
        $messagealias = $messageentity->get_table_alias('messages');

        $this->set_main_table('messages', $messagealias);
        $this->add_entity($messageentity);

        // Conversation.
        $conversationentity = new conversation();
        $conversationalias = $conversationentity->get_table_alias('message_conversations');
        $this->add_entity($conversationentity
            ->add_join("LEFT JOIN {message_conversations} {$conversationalias}
                               ON {$conversationalias}.id = {$messagealias}.conversationid"));

        // Author.
        $authorentity = new user();
        $authoralias = $authorentity->get_table_alias('user');
        $this->add_entity($authorentity
            ->add_join("LEFT JOIN {user} {$authoralias} ON {$authoralias}.id = {$messagealias}.useridfrom"));

        // Recipient.
        $recipiententity = new user();
        $recipientalias = $recipiententity->get_table_alias('user');
        $membersalias = database::generate_alias();
        $this->add_entity($recipiententity
            ->set_entity_name('recipient')
            ->set_entity_title(new lang_string('recipient', 'core_message'))
            ->add_joins($conversationentity->get_joins())
            ->add_joins([
                "LEFT JOIN {message_conversation_members} {$membersalias}
                        ON {$membersalias}.conversationid = {$conversationalias}.id
                       AND {$membersalias}.userid != {$messagealias}.useridfrom",
                "LEFT JOIN {user} {$recipientalias} ON {$recipientalias}.id = {$membersalias}.userid",
            ]));

        // Add all columns/filters/conditions from entities to be available in custom reports.
        $this->add_all_from_entities();
    }

    /**
     * Return the columns that will be added to the report as part of default setup
     *
     * @return string[]
     */
    public function get_default_columns(): array {
        return [
            'user:fullname',
            'recipient:fullname',
            'message:message',
            'message:timecreated',
        ];
    }

    /**
     * Return the default sorting that will be added to the report upon creation
     *
     * @return int[]
     */
    public function get_default_column_sorting(): array {
        return [
            'user:fullname' => SORT_ASC,
            'recipient:fullname' => SORT_ASC,
            'message:timecreated' => SORT_ASC,
        ];
    }

    /**
     * Return the filters that will be added to the report upon creation
     *
     * @return string[]
     */
    public function get_default_filters(): array {
        return [
            'user:fullname',
            'recipient:fullname',
            'message:message',
        ];
    }

    /**
     * Return the conditions that will be added to the report upon creation
     *
     * @return string[]
     */
    public function get_default_conditions(): array {
        return [
            'message:message',
        ];
    }

    /**
     * Return the condition values that will be set for the report upon creation
     *
     * @return array
     */
    public function get_default_condition_values(): array {
        return [
            'message:message_operator' => text::IS_NOT_EMPTY,
        ];
    }
}

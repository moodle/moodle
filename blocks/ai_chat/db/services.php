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

/**
 * External functions and service declaration for ai_chat
 *
 * Documentation: {@link https://moodledev.io/docs/apis/subsystems/external/description}
 *
 * @package    block_ai_chat
 * @category   webservice
 * @copyright  2024 Tobias Garske, ISB Bayern
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = [
    'block_ai_chat_get_all_conversations' => [
        'classname'     => 'block_ai_chat\external\get_all_conversations',
        'methodname'    => 'execute',
        'description'   => 'Get all conversations.',
        'type'          => 'read',
        'ajax'          => true,
        'capabilities'  => 'local/ai_manager:use',
    ],
    'block_ai_chat_get_new_conversation_id' => [
        'classname'     => 'block_ai_chat\external\get_new_conversation_id',
        'methodname'    => 'execute',
        'description'   => 'Save question and reply, get back the conversationid.',
        'type'          => 'write',
        'ajax'          => true,
        'capabilities'  => 'local/ai_manager:use',
    ],
    'block_ai_chat_delete_conversation' => [
        'classname'     => 'block_ai_chat\external\delete_conversation',
        'methodname'    => 'execute',
        'description'   => 'Delete/Hide conversation from history.',
        'type'          => 'write',
        'ajax'          => true,
        'capabilities'  => 'local/ai_manager:use',
    ],
    'block_ai_chat_get_conversationcontext_limit' => [
        'classname'     => 'block_ai_chat\external\get_conversationcontext_limit',
        'methodname'    => 'execute',
        'description'   => 'Get limit for messages to pass to query.',
        'type'          => 'read',
        'ajax'          => true,
        'capabilities'  => 'local/ai_manager:use',
    ],
];

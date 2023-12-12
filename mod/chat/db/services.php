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
 * Chat external functions and service definitions.
 *
 * @package    mod_chat
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */

defined('MOODLE_INTERNAL') || die;

$functions = array(

    'mod_chat_login_user' => array(
        'classname'     => 'mod_chat_external',
        'methodname'    => 'login_user',
        'description'   => 'Log a user into a chat room in the given chat.',
        'type'          => 'write',
        'capabilities'  => 'mod/chat:chat',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),

    'mod_chat_get_chat_users' => array(
        'classname'     => 'mod_chat_external',
        'methodname'    => 'get_chat_users',
        'description'   => 'Get the list of users in the given chat session.',
        'type'          => 'read',
        'capabilities'  => 'mod/chat:chat',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),

    'mod_chat_send_chat_message' => array(
        'classname'     => 'mod_chat_external',
        'methodname'    => 'send_chat_message',
        'description'   => 'Send a message on the given chat session.',
        'type'          => 'write',
        'capabilities'  => 'mod/chat:chat',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),

    'mod_chat_get_chat_latest_messages' => array(
        'classname'     => 'mod_chat_external',
        'methodname'    => 'get_chat_latest_messages',
        'description'   => 'Get the latest messages from the given chat session.',
        'type'          => 'read',
        'capabilities'  => 'mod/chat:chat',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),

    'mod_chat_view_chat' => array(
        'classname'     => 'mod_chat_external',
        'methodname'    => 'view_chat',
        'description'   => 'Trigger the course module viewed event and update the module completion status.',
        'type'          => 'write',
        'capabilities'  => 'mod/chat:chat',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),

    'mod_chat_get_chats_by_courses' => array(
        'classname'     => 'mod_chat_external',
        'methodname'    => 'get_chats_by_courses',
        'description'   => 'Returns a list of chat instances in a provided set of courses,
                            if no courses are provided then all the chat instances the user has access to will be returned.',
        'type'          => 'read',
        'capabilities'  => '',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),
    'mod_chat_get_sessions' => array(
        'classname'     => 'mod_chat_external',
        'methodname'    => 'get_sessions',
        'description'   => 'Retrieves chat sessions for a given chat.',
        'type'          => 'read',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),
    'mod_chat_get_session_messages' => array(
        'classname'     => 'mod_chat_external',
        'methodname'    => 'get_session_messages',
        'description'   => 'Retrieves messages of the given chat session.',
        'type'          => 'read',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),

    'mod_chat_view_sessions' => [
        'classname'     => 'mod_chat\external\view_sessions',
        'methodname'    => 'execute',
        'description'   => 'Trigger the chat session viewed event.',
        'type'          => 'write',
        'capabilities'  => 'mod/chat:readlog',
        'services'      => [MOODLE_OFFICIAL_MOBILE_SERVICE]
    ],
);

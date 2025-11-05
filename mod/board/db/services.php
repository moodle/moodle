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
 * The external services definitions.
 * @package     mod_board
 * @author      Karen Holland <karen@brickfieldlabs.ie>
 * @copyright   2021 Brickfield Education Labs <https://www.brickfield.ie/>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$functions = [
    'mod_board_get_board' => [
        'classname'     => \mod_board\external\get_board::class,
        'description'   => 'Get board column and post data',
        'type'          => 'read',
        'capabilities'  => '',
        'ajax'          => true,
        'loginrequired' => true,
    ],

    'mod_board_board_history' => [
        'classname'     => \mod_board\external\board_history::class,
        'description'   => 'Get board history',
        'type'          => 'read',
        'capabilities'  => '',
        'ajax'          => true,
        'loginrequired' => true,
    ],

    'mod_board_delete_column' => [
        'classname'     => \mod_board\external\delete_column::class,
        'description'   => 'Delete a column from the board',
        'type'          => 'write',
        'capabilities'  => '',
        'ajax'          => true,
        'loginrequired' => true,
    ],

    'mod_board_delete_note' => [
        'classname'     => \mod_board\external\delete_note::class,
        'description'   => 'Delete a note from the board',
        'type'          => 'write',
        'capabilities'  => '',
        'ajax'          => true,
        'loginrequired' => true,
    ],

    'mod_board_lock_column' => [
        'classname'     => \mod_board\external\lock_column::class,
        'description'   => 'Lock a column from the board',
        'type'          => 'write',
        'capabilities'  => '',
        'ajax'          => true,
        'loginrequired' => true,
    ],

    'mod_board_move_column' => [
        'classname'     => \mod_board\external\move_column::class,
        'description'   => 'Move a note on the board',
        'type'          => 'write',
        'capabilities'  => '',
        'ajax'          => true,
        'loginrequired' => true,
    ],

    'mod_board_move_note' => [
        'classname'     => \mod_board\external\move_note::class,
        'description'   => 'Move a note on the board',
        'type'          => 'write',
        'capabilities'  => '',
        'ajax'          => true,
        'loginrequired' => true,
    ],

    'mod_board_can_rate_note' => [
        'classname'     => \mod_board\external\can_rate_note::class,
        'description'   => 'Ask if rating is possible for note',
        'type'          => 'read',
        'capabilities'  => '',
        'ajax'          => true,
        'loginrequired' => true,
    ],

    'mod_board_rate_note' => [
        'classname'     => \mod_board\external\rate_note::class,
        'description'   => 'Rate a note on the board',
        'type'          => 'write',
        'capabilities'  => '',
        'ajax'          => true,
        'loginrequired' => true,
    ],

    'mod_board_get_comments' => [
        'classname'     => \mod_board\external\get_comments::class,
        'description'   => 'Get the list of comments for a note',
        'type'          => 'read',
        'capabilities'  => '',
        'ajax'          => true,
        'loginrequired' => true,
    ],

    'mod_board_add_comment' => [
        'classname'     => \mod_board\external\add_comment::class,
        'description'   => 'Add a comment to a note',
        'type'          => 'write',
        'capabilities'  => '',
        'ajax'          => true,
        'loginrequired' => true,
    ],

    'mod_board_delete_comment' => [
        'classname'     => \mod_board\external\delete_comment::class,
        'description'   => 'Delete a comment from a note',
        'type'          => 'write',
        'capabilities'  => '',
        'ajax'          => true,
        'loginrequired' => true,
    ],

    'mod_board_get_configuration' => [
        'classname'     => \mod_board\external\get_configuration::class,
        'description'   => 'Get the board configuration',
        'type'          => 'read',
        'capabilities'  => '',
        'ajax'          => true,
        'loginrequired' => true,
    ],
];

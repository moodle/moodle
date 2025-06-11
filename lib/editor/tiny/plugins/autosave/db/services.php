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
 * External service definitions for tiny_autosavse.
 *
 * @package tiny_autosave
 * @copyright 2022 Andrew Lyons <andrew@nicols.co.uk>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$functions = [
    'tiny_autosave_resume_session' => array(
        'classname' => 'tiny_autosave\external\resume_autosave_session',
        'methodname' => 'execute',
        'description' => 'Resume an autosave session',
        'type' => 'write',
        'loginrequired' => false,
        'ajax' => true,
    ),
    'tiny_autosave_reset_session' => array(
        'classname' => 'tiny_autosave\external\reset_autosave_session',
        'methodname' => 'execute',
        'description' => 'Reset an autosave session',
        'type' => 'write',
        'loginrequired' => false,
        'ajax' => true,
    ),
    'tiny_autosave_update_session' => array(
        'classname' => 'tiny_autosave\external\update_autosave_session_content',
        'methodname' => 'execute',
        'description' => 'Update an autosave session',
        'type' => 'write',
        'loginrequired' => false,
        'ajax' => true,
    ),
];

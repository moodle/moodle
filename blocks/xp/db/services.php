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
 * External services.
 *
 * @package    block_xp
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = [
    'block_xp_create_rule' => [
        'classname' => 'block_xp\external\create_rule',
        'methodname' => 'execute',
        'description' => 'Create a rule',
        'type' => 'write',
        'ajax' => true,
    ],
    'block_xp_delete_rule' => [
        'classname' => 'block_xp\external\delete_rule',
        'methodname' => 'execute',
        'description' => 'Delete a rule',
        'type' => 'write',
        'ajax' => true,
    ],
    'block_xp_get_rules' => [
        'classname' => 'block_xp\external\get_rules',
        'methodname' => 'execute',
        'description' => 'Get the rules',
        'type' => 'read',
        'ajax' => true,
    ],
    'block_xp_get_sections' => [
        'classname' => 'block_xp\external\get_sections',
        'methodname' => 'execute',
        'description' => 'Retrieves the sections present in a course',
        'type' => 'read',
        'ajax' => true,
    ],
    'block_xp_mark_popup_notification_seen' => [
        'classname' => 'block_xp\external\mark_popup_notification_seen',
        'methodname' => 'execute',
        'description' => 'Mark popup notification as seen',
        'type' => 'write',
        'ajax' => true,
    ],
    'block_xp_search_courses' => [
        'classname' => 'block_xp\external\search_courses',
        'methodname' => 'execute',
        'description' => 'Search for courses',
        'type' => 'read',
        'ajax' => true,
    ],
    'block_xp_search_modules' => [
        'classname' => 'block_xp\external\search_modules',
        'methodname' => 'execute',
        'description' => 'Search modules within a course',
        'type' => 'read',
        'ajax' => true,
    ],
    'block_xp_set_default_levels_info' => [
        'classname' => 'block_xp\external\set_default_levels_info',
        'methodname' => 'execute',
        'description' => 'Set the default levels info',
        'type' => 'write',
        'ajax' => true,
    ],
    'block_xp_set_levels_info' => [
        'classname' => 'block_xp\external\set_levels_info',
        'methodname' => 'execute',
        'description' => 'Set the levels info',
        'type' => 'write',
        'ajax' => true,
    ],
];

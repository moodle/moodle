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
 * External services definition.
 *
 * @package    local_coursematrix
 * @copyright  2024 Author Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = [
    'local_coursematrix_get_rules' => [
        'classname'   => 'local_coursematrix\external',
        'methodname'  => 'get_rules',
        'description' => 'Get all course matrix rules',
        'type'        => 'read',
        'ajax'        => true,
    ],
    'local_coursematrix_update_rule' => [
        'classname'   => 'local_coursematrix\external',
        'methodname'  => 'update_rule',
        'description' => 'Update or create course matrix rules',
        'type'        => 'write',
        'ajax'        => true,
    ],
];


$services = [
    'Course Matrix Service' => [
        'functions' => [
            'local_coursematrix_get_rules',
            'local_coursematrix_update_rule',
        ],
        'restrictedusers' => 0,
        'enabled' => 1,
    ],
];

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
 * Tool Block Timestat webservice definitions.
 *
 * @package    block_timestat
 * @copyright  2022 Jorge C.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
use block_timestat\external;

defined('MOODLE_INTERNAL') || die();

$functions = [
        'block_timestat_update_register' => [
                'classname' => external::class,
                'methodname' => 'update_register',
                'description' => '',
                'type' => 'write',
                'ajax' => true,
                'loginrequired' => true,
        ],
];

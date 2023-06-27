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
 * Forum external functions and service definitions.
 *
 * @package    block_use_stats
 * @copyright  2017 Valery Fremaux (valery.fremaux@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$functions = array(

    'block_use_stats_get_user_stats' => array(
        'classname' => 'block_use_stats_external',
        'methodname' => 'get_user_stats',
        'classpath' => 'blocks/use_stats/externallib.php',
        'description' => 'Get the stats for a user',
        'type' => 'read',
        'capabilities' => 'block/use_stats:export'
    ),

    'block_use_stats_get_users_stats' => array(
        'classname' => 'block_use_stats_external',
        'methodname' => 'get_users_stats',
        'classpath' => 'blocks/use_stats/externallib.php',
        'description' => 'Get the stats for a set of users',
        'type' => 'read',
        'capabilities' => 'block/use_stats:export'
    ),

);

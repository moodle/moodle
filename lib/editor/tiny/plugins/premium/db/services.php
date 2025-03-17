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
 * Tiny Premium external functions and service definitions.
 *
 * @package     tiny_premium
 * @copyright   2023 David Woloszyn <david.woloszyn@moodle.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = [
    'tiny_premium_get_api_key' => [
        'classname'       => 'tiny_premium\external\get_api_key',
        'methodname'      => 'execute',
        'description'     => 'Get the Tiny Premium API key from Moodle',
        'type'            => 'read',
        'capabilities'    => '',
        'ajax'            => true,
        'services'        => [MOODLE_OFFICIAL_MOBILE_SERVICE],
    ],
];

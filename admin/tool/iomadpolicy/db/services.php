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
 * Tool iomadpolicy external functions and service definitions.
 *
 * @package    tool_iomadpolicy
 * @category   external
 * @copyright  2018 Sara Arjona (sara@moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$functions = [
    'tool_iomadpolicy_get_iomadpolicy_version' => [
        'classname'     => 'tool_iomadpolicy\external',
        'methodname'    => 'get_iomadpolicy_version',
        'classpath'     => '',
        'description'   => 'Fetch the details of a iomadpolicy version',
        'type'          => 'read',
        'capabilities'  => '',
        'ajax'          => true,
        'loginrequired' => false,
    ],

    'tool_iomadpolicy_submit_accept_on_behalf' => [
        'classname'     => 'tool_iomadpolicy\external',
        'methodname' => 'submit_accept_on_behalf',
        'classpath' => '',
        'description' => 'Accept policies on behalf of other users',
        'ajax' => true,
        'type' => 'write',
    ],
];

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
 * Plugin Web Service API
 *
 * @package     format_softcourse
 * @copyright   Pimenko 2021 <contact@pimneko.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author      Jordan Kesraoui
 */

defined('MOODLE_INTERNAL') || die();

// We defined the web service functions to install.
$functions = [
    'format_softcourse_update_section_image' => [
        'classname' => 'format_softcourse_external',
        'methodname' => 'update_section_image',
        'classpath' => 'course/format/softcourse/externallib.php',
        'description' => 'Update the section image',
        'type' => 'write',
        'capabilities' => 'moodle/course:update',
        'ajax' => true,
    ],
    'format_softcourse_delete_section_image' => [
        'classname' => 'format_softcourse_external',
        'methodname' => 'delete_section_image',
        'classpath' => 'course/format/softcourse/externallib.php',
        'description' => 'Delete the section image',
        'type' => 'write',
        'capabilities' => 'moodle/course:update',
        'ajax' => true,
    ],
];

// We define the services to install as pre-build services. A pre-build service is not editable by administrator.
$services = [
    'Update the section image' => [
        'functions' => [ 'format_softcourse_update_section_image' ],
        'restrictedusers' => 0,
        'enabled' => 1,
    ],
    'Delete the section image' => [
        'functions' => [ 'format_softcourse_delete_section_image' ],
        'restrictedusers' => 0,
        'enabled' => 1,
    ],
];

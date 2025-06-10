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
 * Web service for mod customcert.
 *
 * @package    mod_customcert
 * @copyright  2016 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = [
    'mod_customcert_delete_issue' => [
        'classname'   => 'mod_customcert\external',
        'methodname'  => 'delete_issue',
        'classpath'   => '',
        'description' => 'Delete an issue for a certificate',
        'type'        => 'write',
        'ajax'        => true,
        'services'    => [MOODLE_OFFICIAL_MOBILE_SERVICE],
    ],
    'mod_customcert_save_element' => [
        'classname'   => 'mod_customcert\external',
        'methodname'  => 'save_element',
        'classpath'   => '',
        'description' => 'Saves data for an element',
        'type'        => 'write',
        'ajax'        => true,
    ],
    'mod_customcert_get_element_html' => [
        'classname'   => 'mod_customcert\external',
        'methodname'  => 'get_element_html',
        'classpath'   => '',
        'description' => 'Returns the HTML to display for an element',
        'type'        => 'read',
        'ajax'        => true,
    ],
];

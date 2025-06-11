<?php
// This file is part of the Choice group module for Moodle - http://moodle.org/
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
 * Structure to support subsection in versions of the app previous to 4.5.
 *
 * @package    mod_subsection
 * @copyright  2024 Dani Palou <dani@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$addons = [
    'mod_subsection' => [
        'handlers' => [
            'subsection' => [
                'delegate' => '', // Don't use a delegate, the JS code will register the handler.
                'init' => 'mobile_init',
                'styles' => [
                    'url' => $CFG->wwwroot . '/mod/subsection/mobileapp/styles.css',
                    'version' => '1',
                ],
            ],
        ],
    ],
];

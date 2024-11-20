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
 * Plugin administration pages are defined here.
 *
 * @package    theme_pimenko
 * @copyright  Pimenko 2019
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// We defined the web service functions to install.

defined('MOODLE_INTERNAL') || die;

$functions = [
    'theme_pimenko_search_courses' => [
        'classname' => 'theme_pimenko\external\search_courses',
        'description' => 'Pimenko : Search courses by (name, module, block, tag)',
        'type' => 'read',
        'ajax' => true,
        'loginrequired' => false,
        'services' => [MOODLE_OFFICIAL_MOBILE_SERVICE],
    ],
    'theme_pimenko_save_cover_file'      => [
        'classname'   => 'theme_pimenko\external\save_cover_file',
        'description' => 'Save the course cover file',
        'type'        => 'write',
        'ajax'        => true,
        'loginrequired' => true
    ],
];

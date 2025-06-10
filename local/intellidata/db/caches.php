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
 * This plugin provides access to Moodle data in form of analytics and reports.
 *
 * @package    local_intellidata
 * @copyright  2021 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */
defined('MOODLE_INTERNAL') || die();

$definitions = [
    'tracking' => [
        'mode' => cache_store::MODE_APPLICATION,
        'simplekeys' => true,
        'overrideclass' => 'local_intellidata\tools\cache_application',
        'overrideclassfile' => 'local/intellidata/classes/tools/cache_application.php',
    ],
    'events' => [
        'mode' => cache_store::MODE_APPLICATION,
        'simplekeys' => true,
        'overrideclass' => 'local_intellidata\tools\cache_application',
        'overrideclassfile' => 'local/intellidata/classes/tools/cache_application.php',
    ],
    'config' => [
        'mode' => cache_store::MODE_APPLICATION,
        'simplekeys' => true,
        'staticacceleration' => true,
    ],
    'datatypes' => [
        'mode' => cache_store::MODE_APPLICATION,
        'simplekeys' => true,
        'staticacceleration' => true,
    ],
];

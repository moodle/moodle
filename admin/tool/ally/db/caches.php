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
 * MUC cache defintion.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$definitions = [
    'annotationmaps' => [
        'mode'       => cache_store::MODE_APPLICATION,
        'simplekeys' => true,
        'ttl'        => 300, // 5 minutes.
    ],
    'request' => [
        'mode'       => cache_store::MODE_APPLICATION,
        'simplekeys' => true,
        'ttl'        => 43200, // 12 hours.
    ],
    'fileinusecache' => [
        'mode'       => cache_store::MODE_REQUEST,
        'simplekeys' => true
    ],
    'pluginfilesinhtml' => [
        'mode'       => cache_store::MODE_REQUEST,
        'simplekeys' => true
    ]
];

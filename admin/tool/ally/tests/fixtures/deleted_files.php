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
 * Fixture for deleted files.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2016 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

return [
    'tool_ally_deleted_files' => [
        [
            'id'           => '1',
            'courseid'     => '2',
            'pathnamehash' => 'xyz1',
            'contenthash'  => 'abc1',
            'mimetype'     => 'text/plain',
            'timedeleted'  => 1482186997
        ],
        [
            'id'           => '2',
            'courseid'     => '2',
            'pathnamehash' => 'xyz2',
            'contenthash'  => 'abc2',
            'mimetype'     => 'text/plain',
            'timedeleted'  => 1482186997
        ],
        [
            'id'           => '3',
            'courseid'     => '3',
            'pathnamehash' => 'xyz3',
            'contenthash'  => 'abc3',
            'mimetype'     => 'text/plain',
            'timedeleted'  => 1482186997
        ],
        [
            'id'           => '4',
            'courseid'     => '3',
            'pathnamehash' => 'xyz4',
            'contenthash'  => 'abc4',
            'mimetype'     => 'text/plain',
            'timedeleted'  => 1482186997
        ],
        [
            'id'           => '5',
            'courseid'     => '3',
            'pathnamehash' => 'xyz5',
            'contenthash'  => 'abc5',
            'mimetype'     => 'text/plain',
            'timedeleted'  => 1482186997
        ],
    ],
];

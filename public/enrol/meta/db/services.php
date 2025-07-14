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
 * Meta enrol external functions and service definitions.
 *
 * @package    enrol_meta
 * @copyright  2021 WKS KV Bildung
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$functions = [
    'enrol_meta_add_instances' => [
        'classname' => \enrol_meta\external\add_instances::class,
        'methodname' => 'execute',
        'description' => 'Add meta enrolment instances',
        'capabilities' => 'enrol/meta:config',
        'type' => 'write',
        'ajax' => true,
        'loginrequired' => true,
    ],
    'enrol_meta_delete_instances' => [
        'classname' => \enrol_meta\external\delete_instances::class,
        'methodname' => 'execute',
        'description' => 'Delete meta enrolment instances',
        'capabilities' => 'enrol/meta:config',
        'type' => 'write',
        'ajax' => true,
        'loginrequired' => true,
    ],
];

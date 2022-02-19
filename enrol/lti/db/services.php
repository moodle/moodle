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
 * LTI enrolment external functions.
 *
 * @package    enrol_lti
 * @category   external
 * @copyright  2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = [
    'enrol_lti_get_lti_advantage_registration_url' => [
        'classname'     => 'enrol_lti\local\ltiadvantage\external\get_registration_url',
        'classpath'     => '',
        'description'   => 'Get a secure, one-time-use registration URL',
        'type'          => 'write',
        'capabilities'  => 'moodle/site:config',
        'ajax'          => true,
    ],
    'enrol_lti_delete_lti_advantage_registration_url' => [
        'classname'     => 'enrol_lti\local\ltiadvantage\external\delete_registration_url',
        'classpath'     => '',
        'description'   => 'Delete the secure, one-time-use registration URL',
        'type'          => 'write',
        'capabilities'  => 'moodle/site:config',
        'ajax'          => true,
    ]
];

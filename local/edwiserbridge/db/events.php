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
 * Edwiser Bridge - WordPress and Moodle integration.
 * File responsible to register all the events which are used for 2 way synch.
 *
 * @package     local_edwiserbridge
 * @copyright   2021 WisdmLabs (https://wisdmlabs.com/) <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author      Wisdmlabs
 */

defined('MOODLE_INTERNAL') || die();

$observers = array(
    array(
        'eventname' => 'core\event\user_enrolment_created',
        'callback'  => 'local_edwiserbridge_observer::user_enrolment_created',
    ),
    array(
        'eventname' => 'core\event\user_enrolment_deleted',
        'callback'  => 'local_edwiserbridge_observer::user_enrolment_deleted',
    ),
    array(
        'eventname' => 'core\event\user_created',
        'callback'  => 'local_edwiserbridge_observer::user_created',
    ),
    array(
        'eventname' => 'core\event\user_deleted',
        'callback'  => 'local_edwiserbridge_observer::user_deleted',
    ),
    array(
        'eventname' => 'core\event\user_updated',
        'callback'  => 'local_edwiserbridge_observer::user_updated',
    ),
    array(
        'eventname' => 'core\event\user_password_updated',
        'callback'  => 'local_edwiserbridge_observer::user_password_updated',
    ),
    array(
        'eventname' => 'core\event\course_created',
        'callback'  => 'local_edwiserbridge_observer::course_created',
    ),
    array(
        'eventname' => 'core\event\course_deleted',
        'callback'  => 'local_edwiserbridge_observer::course_deleted',
    )
);

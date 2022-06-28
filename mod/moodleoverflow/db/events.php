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
 * Moodleoverflow event handler definition.
 *
 * @package   mod_moodleoverflow
 * @copyright 2017 Kennet Winter <k_wint10@uni-muenster.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$observers = array(

    // Delete read records and subscriptions if the user is not anymore enrolled.
    array(
        'eventname' => '\core\event\user_enrolment_deleted',
        'callback'  => 'mod_moodleoverflow_observer::user_enrolment_deleted',
    ),

    // Subscribe the user to moodleoverflows which force him to when enroling a user.
    array(
        'eventname' => '\core\event\role_assigned',
        'callback'  => 'mod_moodleoverflow_observer::role_assigned',
    ),

    // Subscribe the user to moodleoverflows which force him to when creating an instance.
    array(
        'eventname' => '\core\event\course_module_created',
        'callback'  => 'mod_moodleoverflow_observer::course_module_created',
    ),

);

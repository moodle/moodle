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
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 *
 * @package    local_intelliboard
 * @copyright  2017 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @website    http://intelliboard.net/
 */


$observers = [
    array(
        'eventname' => '\core\event\role_assigned',
        'callback' => 'local_intelliboard_observer::role_assigned',
    ),
    array(
        'eventname' => '\mod_forum\event\post_created',
        'callback' => 'local_intelliboard_observer::post_created',
    ),
    array(
        'eventname' => '\core\event\role_unassigned',
        'callback' => 'local_intelliboard_observer::role_unassigned',
    ),
    array(
        'eventname' => '\core\event\user_graded',
        'callback' => 'local_intelliboard_observer::user_graded',
    ),
    array(
        'eventname' => '\mod_quiz\event\attempt_submitted',
        'callback' => 'local_intelliboard_observer::quiz_attempt_submitted',
    ),
    array(
        'eventname' => '\mod_assign\event\assessable_submitted',
        'callback' => 'local_intelliboard_observer::assign_attempt_submitted',
    ),
    array(
        'eventname' => 'core\event\user_loggedin',
        'callback' => 'local_intelliboard_observer::user_loggedin',
    ),
    array(
        'eventname' => 'core\event\user_enrolment_created',
        'callback' => 'local_intelliboard_observer::user_enrolment_created',
    ),
    array(
        'eventname' => 'core\event\course_completed',
        'callback' => 'local_intelliboard_observer::course_completed',
    ),
    array(
        'eventname' => 'mod_resource\event\course_module_viewed',
        'callback' => 'local_intelliboard_observer::resource_viewed',
    ),

    // Transcripts events.
    array(
        'eventname' => '\core\event\user_enrolment_created',
        'callback'  => '\local_intelliboard\transcripts\observer::transcripts_user_enrolment_created',
    ),
    array(
        'eventname' => '\core\event\user_enrolment_deleted',
        'callback'  => '\local_intelliboard\transcripts\observer::transcripts_user_enrolment_deleted',
    ),
    array(
        'eventname' => '\core\event\course_completed',
        'callback'  => '\local_intelliboard\transcripts\observer::transcripts_course_completed',
    ),
    array(
        'eventname' => '\core\event\course_module_completion_updated',
        'callback'  => '\local_intelliboard\transcripts\observer::transcripts_course_module_completion_updated',
    ),
    array(
        'eventname' => '\core\event\course_module_viewed',
        'callback'  => '\local_intelliboard\transcripts\observer::transcripts_course_module_viewed',
    ),
    array(
        'eventname' => '\core\event\user_graded',
        'callback' => '\local_intelliboard\transcripts\observer::transcripts_user_graded',
    ),
    array(
        'eventname' => '\core\event\role_assigned',
        'callback' => '\local_intelliboard\transcripts\observer::transcripts_role_assigned',
    ),
    array(
        'eventname' => '\core\event\group_member_added',
        'callback' => '\local_intelliboard\transcripts\observer::transcripts_group_member_added',
    ),
];

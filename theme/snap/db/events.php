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
 * Snap event hooks.
 *
 * @package   theme_snap
 * @copyright Copyright (c) 2015 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$observers = [
    [
        'eventname' => '\core\event\course_updated',
        'callback'  => '\theme_snap\event_handlers::course_updated',
    ],
    [
        'eventname' => '\core\event\course_deleted',
        'callback'  => '\theme_snap\event_handlers::course_deleted',
    ],
    [
        'eventname' => '\core\event\user_deleted',
        'callback'  => '\theme_snap\event_handlers::user_deleted',
    ],

    // Calendar events.
    [
        'eventname' => '\core\event\calendar_event_created',
        'callback'  => '\theme_snap\event_handlers::calendar_change',
    ],
    [
        'eventname' => '\core\event\calendar_event_updated',
        'callback'  => '\theme_snap\event_handlers::calendar_change',
    ],
    [
        'eventname' => '\core\event\calendar_event_deleted',
        'callback'  => '\theme_snap\event_handlers::calendar_change',
    ],
    [
        'eventname' => '\mod_assign\event\extension_granted',
        'callback'  => '\theme_snap\event_handlers::calendar_change',
    ],

    // All events affecting course completion at course level.
    [
        'eventname' => '\core\event\course_completion_updated',
        'callback'  => '\theme_snap\event_handlers::course_completion_updated',
    ],
    [
        'eventname' => '\core\event\course_module_created',
        'callback'  => '\theme_snap\event_handlers::course_module_created',
    ],
    [
        'eventname' => '\core\event\course_module_updated',
        'callback'  => '\theme_snap\event_handlers::course_module_updated',
    ],
    [
        'eventname' => '\core\event\course_module_deleted',
        'callback'  => '\theme_snap\event_handlers::course_module_deleted',
    ],

    // User level course completion events.
    [
        'eventname' => '\core\event\course_module_completion_updated',
        'callback'  => '\theme_snap\event_handlers::course_module_completion_updated',
    ],

    // User updated event for Profile based branding.
    [
        'eventname' => '\core\event\user_updated',
        'callback'  => '\theme_snap\event_handlers::user_updated',
    ],

    // User enrolment handlers.
    [
        'eventname' => '\core\event\role_assigned',
        'callback'  => '\theme_snap\event_handlers::role_assigned',
    ],
    [
        'eventname' => '\core\event\role_unassigned',
        'callback'  => '\theme_snap\event_handlers::role_unassigned',
    ],
    [
        'eventname' => '\core\event\user_enrolment_deleted',
        'callback'  => '\theme_snap\event_handlers::user_enrolment_deleted',
    ],

    // Group member events, activity group overrides may make activity_deadlines cache invalid.
    [
        'eventname' => '\core\event\group_member_added',
        'callback'  => '\theme_snap\event_handlers::group_member_added',
    ],
    [
        'eventname' => '\core\event\group_member_removed',
        'callback'  => '\theme_snap\event_handlers::group_member_removed',
    ],
];

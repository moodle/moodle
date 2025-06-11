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
 * Cache definition for snap.
 *
 * @package   theme_snap
 * @author    Guy Thomas
 * @copyright Copyright (c) 2016 Open LMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$definitions = array(
    'webservicedefinitions' => [
        'mode'               => cache_store::MODE_APPLICATION,
        'simplekeys'         => false,
        'simpledata'         => false,
    ],
    // This is used so that we can invalidate session level caches if the course completion settings for a course
    // change.
    'course_completion_progress_ts' => [
        'mode'               => cache_store::MODE_APPLICATION,
        'simplekeys'         => true,
        'simpledata'         => true,
        'staticacceleration' => false,
    ],
    // This is used to cache completion data per course / user.
    'course_completion_progress' => [
        'mode'               => cache_store::MODE_SESSION,
        'simplekeys'         => true,
        'simpledata'         => false,
        'staticacceleration' => false,
    ],
    // This is used to cache deadlines per courses and groups.
    'activity_deadlines' => [
        'mode'               => cache_store::MODE_APPLICATION,
        'simplekeys'         => true,
        'simpledata'         => false,
        'staticacceleration' => false,
        'invalidationevents' => [
            'groupmemberschanged',
        ],
    ],
    'generalstaticappcache' => [
        'mode'               => cache_store::MODE_APPLICATION,
        'simplekeys'         => true,
        'simpledata'         => false,
        'staticacceleration' => true,
    ],
    'profile_based_branding' => [
        'mode'               => cache_store::MODE_SESSION,
        'simplekeys'         => true,
        'simpledata'         => false,
        'staticacceleration' => false,
    ],
    'course_card_bg_image' => [
        'mode'               => cache_store::MODE_APPLICATION,
        'simplekeys'         => true,
        'simpledata'         => true,
    ],
    'course_card_teacher_avatar' => [
        'mode'               => cache_store::MODE_APPLICATION,
        'simplekeys'         => true,
        'simpledata'         => false,
    ],
    'course_card_teacher_avatar_index' => [
        'mode'               => cache_store::MODE_APPLICATION,
        'simplekeys'         => true,
        'simpledata'         => false,
    ],
    'course_users_assign_ungraded' => [
        'mode'               => cache_store::MODE_APPLICATION,
        'simplekeys'         => true,
    ],
    'course_users_quiz_ungraded' => [
        'mode'               => cache_store::MODE_APPLICATION,
        'simplekeys'         => true,
    ],
);


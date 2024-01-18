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
 * Forum external functions and service definitions.
 *
 * @package    mod_forum
 * @copyright  2012 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$functions = array(

    'mod_forum_get_forums_by_courses' => array(
        'classname' => 'mod_forum_external',
        'methodname' => 'get_forums_by_courses',
        'classpath' => 'mod/forum/externallib.php',
        'description' => 'Returns a list of forum instances in a provided set of courses, if
            no courses are provided then all the forum instances the user has access to will be
            returned.',
        'type' => 'read',
        'capabilities' => 'mod/forum:viewdiscussion',
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),

    'mod_forum_get_discussion_posts' => array(
        'classname' => 'mod_forum_external',
        'methodname' => 'get_discussion_posts',
        'classpath' => 'mod/forum/externallib.php',
        'description' => 'Returns a list of forum posts for a discussion.',
        'type' => 'read',
        'ajax' => true,
        'capabilities' => 'mod/forum:viewdiscussion, mod/forum:viewqandawithoutposting',
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),

    'mod_forum_get_forum_discussions' => array(
        'classname' => 'mod_forum_external',
        'methodname' => 'get_forum_discussions',
        'classpath' => 'mod/forum/externallib.php',
        'description' => 'Returns a list of forum discussions optionally sorted and paginated.',
        'type' => 'read',
        'capabilities' => 'mod/forum:viewdiscussion, mod/forum:viewqandawithoutposting',
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),

    'mod_forum_view_forum' => array(
        'classname' => 'mod_forum_external',
        'methodname' => 'view_forum',
        'classpath' => 'mod/forum/externallib.php',
        'description' => 'Trigger the course module viewed event and update the module completion status.',
        'type' => 'write',
        'capabilities' => 'mod/forum:viewdiscussion',
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),

    'mod_forum_view_forum_discussion' => array(
        'classname' => 'mod_forum_external',
        'methodname' => 'view_forum_discussion',
        'classpath' => 'mod/forum/externallib.php',
        'description' => 'Trigger the forum discussion viewed event.',
        'type' => 'write',
        'capabilities' => 'mod/forum:viewdiscussion',
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),

    'mod_forum_add_discussion_post' => array(
        'classname' => 'mod_forum_external',
        'methodname' => 'add_discussion_post',
        'classpath' => 'mod/forum/externallib.php',
        'description' => 'Create new posts into an existing discussion.',
        'type' => 'write',
        'ajax' => true,
        'capabilities' => 'mod/forum:replypost',
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),

    'mod_forum_add_discussion' => array(
        'classname' => 'mod_forum_external',
        'methodname' => 'add_discussion',
        'classpath' => 'mod/forum/externallib.php',
        'description' => 'Add a new discussion into an existing forum.',
        'type' => 'write',
        'capabilities' => 'mod/forum:startdiscussion',
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),

    'mod_forum_can_add_discussion' => array(
        'classname' => 'mod_forum_external',
        'methodname' => 'can_add_discussion',
        'classpath' => 'mod/forum/externallib.php',
        'description' => 'Check if the current user can add discussions in the given forum (and optionally for the given group).',
        'type' => 'read',
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),

    'mod_forum_get_forum_access_information' => array(
        'classname'     => 'mod_forum_external',
        'methodname'    => 'get_forum_access_information',
        'description'   => 'Return capabilities information for a given forum.',
        'type'          => 'read',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
    ),

    'mod_forum_set_subscription_state' => array(
        'classname' => 'mod_forum_external',
        'methodname' => 'set_subscription_state',
        'classpath' => 'mod/forum/externallib.php',
        'description' => 'Set the subscription state',
        'type' => 'write',
        'ajax' => true,
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
    ),

    'mod_forum_set_lock_state' => array(
        'classname' => 'mod_forum_external',
        'methodname' => 'set_lock_state',
        'classpath' => 'mod/forum/externallib.php',
        'description' => 'Set the lock state for the discussion',
        'type' => 'write',
        'ajax' => true,
        'capabilities' => 'moodle/course:manageactivities',
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
    ),

    'mod_forum_toggle_favourite_state' => array(
        'classname' => 'mod_forum_external',
        'methodname' => 'toggle_favourite_state',
        'classpath' => 'mod/forum/externallib.php',
        'description' => 'Toggle the favourite state',
        'type' => 'write',
        'ajax' => true,
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
    ),
    'mod_forum_set_pin_state' => array(
        'classname' => 'mod_forum_external',
        'methodname' => 'set_pin_state',
        'classpath' => 'mod/forum/externallib.php',
        'description' => 'Set the pin state',
        'type' => 'write',
        'ajax' => true,
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
    ),

    'mod_forum_delete_post' => array(
        'classname' => 'mod_forum_external',
        'methodname' => 'delete_post',
        'classpath' => 'mod/forum/externallib.php',
        'description' => 'Deletes a post or a discussion completely when the post is the discussion topic.',
        'type' => 'write',
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
    ),

    'mod_forum_get_discussion_posts_by_userid' => array(
        'classname' => 'mod_forum_external',
        'methodname' => 'get_discussion_posts_by_userid',
        'classpath' => 'mod/forum/externallib.php',
        'description' => 'Returns a list of forum posts for a discussion for a user.',
        'type' => 'read',
        'ajax' => true,
        'capabilities' => 'mod/forum:viewdiscussion, mod/forum:viewqandawithoutposting',
    ),
    'mod_forum_get_discussion_post' => array(
        'classname' => 'mod_forum_external',
        'methodname' => 'get_discussion_post',
        'classpath' => 'mod/forum/externallib.php',
        'description' => 'Get a particular discussion post.',
        'type' => 'read',
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),
    'mod_forum_prepare_draft_area_for_post' => array(
        'classname' => 'mod_forum_external',
        'methodname' => 'prepare_draft_area_for_post',
        'classpath' => 'mod/forum/externallib.php',
        'description' => 'Prepares a draft area for editing a post.',
        'type' => 'write',
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),
    'mod_forum_update_discussion_post' => array(
        'classname' => 'mod_forum_external',
        'methodname' => 'update_discussion_post',
        'classpath' => 'mod/forum/externallib.php',
        'description' => 'Updates a post or a discussion topic post.',
        'type' => 'write',
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),
);

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
 * Defines message providers (types of messages being sent)
 *
 * The providers defined on this file are processed and registered into
 * the Moodle DB after any install or upgrade operation. All plugins
 * support this.
 *
 * For more information, take a look to the documentation available:
 *     - Message API: {@link http://docs.moodle.org/dev/Message_API}
 *     - Upgrade API: {@link http://docs.moodle.org/dev/Upgrade_API}
 *
 * @package   core
 * @category  message
 * @copyright 2008 onwards Martin Dougiamas  http://dougiamas.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$messageproviders = array (

    'newlogin' => array (
        'defaults' => array(
            'email' => MESSAGE_PERMITTED + MESSAGE_DEFAULT_ENABLED,
            'airnotifier' => MESSAGE_PERMITTED + MESSAGE_DEFAULT_ENABLED,
        ),
    ),

    // Notices that an admin might be interested in
    'notices' => array (
         'capability'  => 'moodle/site:config'
    ),

    // Important errors that an admin ought to know about
    'errors' => array (
         'capability'  => 'moodle/site:config'
    ),

    // cron-based notifications about available moodle and/or additional plugin updates
    'availableupdate' => array(
        'capability' => 'moodle/site:config',
        'defaults' => array(
            'email' => MESSAGE_PERMITTED + MESSAGE_DEFAULT_ENABLED
        ),

    ),

    'instantmessage' => array (
        'defaults' => array(
            'popup' => MESSAGE_PERMITTED + MESSAGE_DEFAULT_ENABLED,
            'email' => MESSAGE_PERMITTED + MESSAGE_DEFAULT_ENABLED,
        ),
    ),

    'backup' => array (
        'capability'  => 'moodle/site:config'
    ),

    // Course creation request notification
    'courserequested' => array (
        'capability'  => 'moodle/site:approvecourse'
    ),

    // Course request approval notification
    'courserequestapproved' => array (
         'capability'  => 'moodle/course:request',
         'defaults' => array(
            'airnotifier' => MESSAGE_PERMITTED + MESSAGE_DEFAULT_ENABLED,
        ),
    ),

    // Course request rejection notification
    'courserequestrejected' => array (
        'capability'  => 'moodle/course:request',
        'defaults' => array(
            'airnotifier' => MESSAGE_PERMITTED + MESSAGE_DEFAULT_ENABLED,
        ),
    ),

    // Course completed. Requires course completion configured at course level. It does not work with just activity progress.
    'coursecompleted' => [
        'defaults' => [
            'airnotifier' => MESSAGE_PERMITTED + MESSAGE_DEFAULT_ENABLED,
        ],
    ],

    // Course content updated. New content (activities or resources) has been created or existing content updated.
    'coursecontentupdated' => array (
        'defaults' => array(
            'popup' => MESSAGE_PERMITTED + MESSAGE_DEFAULT_ENABLED,
            'email' => MESSAGE_PERMITTED + MESSAGE_DEFAULT_ENABLED,
            'airnotifier' => MESSAGE_PERMITTED + MESSAGE_DEFAULT_ENABLED,
        ),
    ),

    // Badge award notification to a badge recipient.
    'badgerecipientnotice' => array (
        'defaults' => array(
            'popup' => MESSAGE_PERMITTED + MESSAGE_DEFAULT_ENABLED,
            'email' => MESSAGE_PERMITTED + MESSAGE_DEFAULT_ENABLED,
            'airnotifier' => MESSAGE_PERMITTED + MESSAGE_DEFAULT_ENABLED,
        ),
        'capability'  => 'moodle/badges:earnbadge'
    ),

    // Badge award notification to a badge creator (mostly cron-based).
    'badgecreatornotice' => array (
        'defaults' => array(
            'email' => MESSAGE_PERMITTED + MESSAGE_DEFAULT_ENABLED,
        )
    ),

    // A comment was left on a plan.
    'competencyplancomment' => array(),

    // A comment was left on a user competency.
    'competencyusercompcomment' => array(),

    // User insights.
    'insights' => array (
        'defaults' => [
            'popup' => MESSAGE_PERMITTED + MESSAGE_DEFAULT_ENABLED,
            'email' => MESSAGE_PERMITTED + MESSAGE_DEFAULT_ENABLED,
            'airnotifier' => MESSAGE_PERMITTED + MESSAGE_DEFAULT_ENABLED,
        ]
    ),

    // Message contact requests.
    'messagecontactrequests' => [
        'defaults' => [
            // We don't need to notify in the popup output here because the message drawer
            // already notifies users of contact requests.
            'email' => MESSAGE_PERMITTED + MESSAGE_DEFAULT_ENABLED,
            'airnotifier' => MESSAGE_PERMITTED + MESSAGE_DEFAULT_ENABLED,
        ]
    ],

    // Asyncronhous backup/restore notifications.
    'asyncbackupnotification' => array(
        'defaults' => array(
            'popup' => MESSAGE_PERMITTED + MESSAGE_DEFAULT_ENABLED,
            'email' => MESSAGE_PERMITTED + MESSAGE_DEFAULT_ENABLED,
        )
    ),

    'gradenotifications' => [
        'defaults' => array(
            'popup' => MESSAGE_PERMITTED + MESSAGE_DEFAULT_ENABLED,
            'email' => MESSAGE_PERMITTED + MESSAGE_DEFAULT_ENABLED,
        ),
    ],

    // Infected files.
    'infected' => array(
        'capability'  => 'moodle/site:config',
    ),

    // Report builder schedules.
    'reportbuilderschedule' => [
        'defaults' => [
            'email' => MESSAGE_FORCED,
        ],
    ],
);

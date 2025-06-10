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
 * Contains the different events Panopto can expect to handle.
 *
 * @package block_panopto
 * @copyright  Panopto 2009 - 2016 with contributions from Spenser Jones (sjones@ambrose.edu)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$observers = [
    [
        'eventname' => '\core\event\course_created',
        'callback' => 'block_panopto_rollingsync::coursecreated',
    ],
    // Event when a course is imported or backed up.
    [
        'eventname' => '\core\event\course_restored',
        'callback' => 'block_panopto_rollingsync::courserestored',
    ],
    [
        'eventname' => '\core\event\course_deleted',
        'callback' => 'block_panopto_rollingsync::coursedeleted',
    ],
    // User unenroled event.
    [
        'eventname' => '\core\event\user_enrolment_deleted',
        'callback' => 'block_panopto_rollingsync::userenrolmentdeleted',
    ],
    // User unenroled event.
    [
        'eventname' => '\core\event\user_enrolment_updated',
        'callback' => 'block_panopto_rollingsync::userenrolmentupdated',
    ],
    // User enroled event.
    [
        'eventname' => '\core\event\role_assigned',
        'callback' => 'block_panopto_rollingsync::roleassigned',
    ],
    // User Enrollment changed event.
    [
        'eventname' => '\core\event\role_unassigned',
        'callback' => 'block_panopto_rollingsync::roleunassigned',
    ],
    // User loggedin and loggedinas  events.
    [
        'eventname' => '\core\event\user_loggedin',
        'callback' => 'block_panopto_rollingsync::userloggedin',
    ],
    [
        'eventname' => '\core\event\user_loggedinas',
        'callback' => 'block_panopto_rollingsync::userloggedinas',
    ],
    [
        'eventname' => '\core\event\course_category_updated',
        'callback' => 'block_panopto_categorytasks::coursecategoryupdated',
    ],
    [
        'eventname' => '\core\event\course_category_created',
        'callback' => 'block_panopto_categorytasks::coursecategorycreated',
    ],
];

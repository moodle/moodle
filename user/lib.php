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
 * External user API
 *
 * @package    moodlecore
 * @subpackage user
 * @copyright  2009 Moodle Pty Ltd (http://moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * Creates a user
 * @param object $user user to create
 * @return int id of the newly created user
 */
function user_create_user($user) {
    global $DB;

/// set the timecreate field to the current time
    if (!is_object($user)) {
            $user = (object)$user;
    }

    /// hash the password
    $user->password = hash_internal_user_password($user->password);

    $user->timecreated = time();
    $user->timemodified = $user->timecreated;

/// insert the user into the database
    $newuserid = $DB->insert_record('user', $user);

/// create USER context for this user
    get_context_instance(CONTEXT_USER, $newuserid);

    return $newuserid;

}

/**
 * Update a user with a user object (will compare against the ID)
 * @param object $user - the user to update
 */
function user_update_user($user) {
    global $DB;

    /// set the timecreate field to the current time
    if (!is_object($user)) {
            $user = (object)$user;
    }

    /// hash the password
    $user->password = hash_internal_user_password($user->password);

    $user->timemodified = time();
    $DB->update_record('user', $user);
}


/**
 * Marks user deleted in internal user database and notifies the auth plugin.
 * Also unenrols user from all roles and does other cleanup.
 *
 * @todo Decide if this transaction is really needed (look for internal TODO:)
 * @param object $user Userobject before delete    (without system magic quotes)
 * @return boolean success
 */
function user_delete_user($user) {
    return delete_user($user);
}

/**
 * Get users by id
 * @param array $userids id of users to retrieve
 *
 */
function user_get_users_by_id($userids) {
    global $DB;
    return $DB->get_records_list('user', 'id', $userids);
}

/**
 * Return a list of page types
 * @param string $pagetype current page type
 * @param stdClass $parentcontext Block's parent context
 * @param stdClass $currentcontext Current context of block
 */
function user_pagetypelist($pagetype, $parentcontext, $currentcontext) {
    return array(
        'user-profile'=>get_string('page-user-profile', 'pagetype'),
        'my-index'=>get_string('page-my-index', 'pagetype')
    );
}

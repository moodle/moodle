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
    global $CFG, $DB;
    require_once($CFG->libdir.'/grouplib.php');
    require_once($CFG->libdir.'/gradelib.php');
    require_once($CFG->dirroot.'/message/lib.php');

    // delete all grades - backup is kept in grade_grades_history table
    if ($grades = grade_grade::fetch_all(array('userid'=>$user->id))) {
        foreach ($grades as $grade) {
            $grade->delete('userdelete');
        }
    }

    //move unread messages from this user to read
    message_move_userfrom_unread2read($user->id);

    // remove from all groups
    $DB->delete_records('groups_members', array('userid'=>$user->id));

    // unenrol from all roles in all contexts
    role_unassign(0, $user->id); // this might be slow but it is really needed - modules might do some extra cleanup!

    // now do a final accesslib cleanup - removes all role assingments in user context and context itself
    delete_context(CONTEXT_USER, $user->id);

    require_once($CFG->dirroot.'/tag/lib.php');
    tag_set('user', $user->id, array());

    // workaround for bulk deletes of users with the same email address
    $delname = "$user->email.".time();
    while ($DB->record_exists('user', array('username'=>$delname))) { // no need to use mnethostid here
        $delname++;
    }

    // mark internal user record as "deleted"
    $updateuser = new object();
    $updateuser->id           = $user->id;
    $updateuser->deleted      = 1;
    $updateuser->username     = $delname;            // Remember it just in case
    $updateuser->email        = md5($user->username);// Store hash of username, useful importing/restoring users
    $updateuser->idnumber     = '';                  // Clear this field to free it up
    $updateuser->timemodified = time();

    $DB->update_record('user', $updateuser);

    // notify auth plugin - do not block the delete even when plugin fails
    $authplugin = get_auth_plugin($user->auth);
    $authplugin->user_delete($user);

    events_trigger('user_deleted', $user);

    return true;
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

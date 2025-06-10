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
 * @package    block_link_logins
 * @copyright  2023 onwards Louisiana State University
 * @copyright  2023 onwards Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class link {

    /**
     * Returns the user object(s) for the email in question.
     *
     * @return @array of @objects
     */
    public static function get_user_from_email($email) {
        global $DB;

        // Use get_records because email might not be unique.
        $user = $DB->get_records('user', array('email' => $email));

        // Return the user array.
        return $user;
    }

    /**
     * Returns the user object for the username in question.
     *
     * @return @object
     */
    public static function get_user_from_username($username) {
        global $DB;

        // We can be confident username is unique.
        $user = $DB->get_record('user', array('username' => $username));

        // Return the user object.
        return $user;
    }

    /**
     * Returns the username based on some rules.
     *
     * @return @string
     */
    public static function generate_username_from_email($email) {
        global $CFG;

        // Get the configured home domain.
        $homedomain = $CFG->block_link_logins_homedomain;

        // Get the configured external domain.
        $extdomain = $CFG->block_link_logins_extdomain;

        // Isolate the domain.
        $inputdomain = substr(strrchr($email, "@"), 0);

        // perform the logic.
        if ($inputdomain == $homedomain) {
            // Set the username to email.
            $username = $email;
        } else {
            // Build the new username.
            $username = str_replace('@', '_', $email) . '#ext#' . $extdomain;
        }

        // Return the username.
        return $username;
    }

    public static function get_link($linkid) {
        global $DB;

        // Set the table.
        $table = 'auth_oauth2_linked_login';

        // Set the conditions.
        $conditions = array('id' => $linkid);

        // Get the data.
        $linkdata = $DB->get_record($table, $conditions, $fields='*', $strictness=IGNORE_MISSING);

        // return the data.
        return $linkdata;
    }

    public static function handle_creating_link($prospectiveemail, $prospectiveusername, $existinguserid) {
        global $CFG, $DB, $USER;

        // Get the issuerid or set it to 1 as a default.
        $issuerid = isset($CFG->block_link_logins_issuerid) ? $CFG->block_link_logins_issuerid : 1;

        // Set the table.
        $table = 'auth_oauth2_linked_login';

        // Build the data object.
        $dataobject = new stdClass();
        $dataobject->timecreated = time();
        $dataobject->timemodified = time();
        $dataobject->usermodified = $USER->id;
        $dataobject->userid = $existinguserid;
        $dataobject->issuerid = $issuerid;
        $dataobject->username = $prospectiveusername;
        $dataobject->email = $prospectiveemail;
        $dataobject->confirmtoken = '';
        $dataobject->confirmtokenexpires = 0;
   
        // Insert the record.
        $insert = $DB->insert_record($table, $dataobject, $returnid=true, $bulk=false);

        return $insert;
    }

    public static function check_dupes($prospectiveusername, $existinguser) {
        global $USER, $DB;

        // Set the table.
        $table = 'auth_oauth2_linked_login';

        $users = new stdClass();

        // We can be confident username is unique, get any matches.
        $users->existingusername = $DB->get_record($table, array('username' => $prospectiveusername));

        // If we have an existing user id.
        if (isset($existinguser->id)) {
            // Get all records of existing users.
            $users->mappedusers = $DB->get_records($table, array('userid' => $existinguser->id));
        }

        // If we have an existing usernamei dupe.
        if (isset($users->existingusername->usermodified)) {
            // Get the user who created the entry for future use.
            $users->creator = $DB->get_record('user', array('id' => $users->existingusername->usermodified));
        }

        // Return the data.
        return $users;
    }

    /**
     * Returns if a user can use the tool or not.
     *
     * @return @bool
     */
    public static function can_use() {
        global $CFG, $USER;

        // Define the array.
        $allowed_users = array();

        // Make if it's not configured, short circuit.
        if (!isset($CFG->block_link_logins_allowed)) {
            return false;
        }

        // Get the list of allowed users from the config.
        $allowed_users = array_map("trim",explode(',', $CFG->block_link_logins_allowed));

        // If we don't have any configured defined users, short circuit.
        if (count($allowed_users) == 0) {
            return false;
        }

        // Not only do they have to be admins, but they also have to be defined in config.
        $allowed = is_siteadmin() && in_array($USER->username, $allowed_users);

        // Return the value defined above.
        return $allowed;
    }
}

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
 * Contains class used to return information to display for the message area.
 *
 * @package    core_message
 * @copyright  2016 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_message;

require_once($CFG->dirroot . '/lib/messagelib.php');

defined('MOODLE_INTERNAL') || die();

/**
 * Class used to return information to display for the message area.
 *
 * @copyright  2016 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class api {

    /**
     * Handles searching for messages in the message area.
     *
     * @param int $userid The user id doing the searching
     * @param string $search The string the user is searching
     * @param int $limitfrom
     * @param int $limitnum
     * @return \core_message\output\messagearea\message_search_results
     */
    public static function search_messages($userid, $search, $limitfrom = 0, $limitnum = 0) {
        global $DB;

        // Get the user fields we want.
        $ufields = \user_picture::fields('u', array('lastaccess'), 'userfrom_id', 'userfrom_');
        $ufields2 = \user_picture::fields('u2', array('lastaccess'), 'userto_id', 'userto_');

        // Get all the messages for the user.
        $sql = "SELECT m.id, m.useridfrom, m.useridto, m.subject, m.fullmessage, m.fullmessagehtml, m.fullmessageformat,
                       m.smallmessage, m.notification, m.timecreated, 0 as isread, $ufields, $ufields2
                  FROM {message} m
                  JOIN {user} u
                    ON m.useridfrom = u.id
                  JOIN {user} u2
                    ON m.useridto = u2.id
                 WHERE ((useridto = ? AND timeusertodeleted = 0)
                    OR (useridfrom = ? AND timeuserfromdeleted = 0))
                   AND notification = 0
                   AND u.deleted = 0
                   AND u2.deleted = 0
                   AND " . $DB->sql_like('smallmessage', '?', false) . "
             UNION ALL
                SELECT mr.id, mr.useridfrom, mr.useridto, mr.subject, mr.fullmessage, mr.fullmessagehtml, mr.fullmessageformat,
                       mr.smallmessage, mr.notification, mr.timecreated, 1 as isread, $ufields, $ufields2
                  FROM {message_read} mr
                  JOIN {user} u
                    ON mr.useridfrom = u.id
                  JOIN {user} u2
                    ON mr.useridto = u2.id
                 WHERE ((useridto = ? AND timeusertodeleted = 0)
                    OR (useridfrom = ? AND timeuserfromdeleted = 0))
                   AND notification = 0
                   AND u.deleted = 0
                   AND u2.deleted = 0
                   AND " . $DB->sql_like('smallmessage', '?', false) . "
              ORDER BY timecreated DESC";
        $params = array($userid, $userid, '%' . $search . '%',
                        $userid, $userid, '%' . $search . '%');

        // Convert the messages into searchable contacts with their last message being the message that was searched.
        $contacts = array();
        if ($messages = $DB->get_records_sql($sql, $params, $limitfrom, $limitnum)) {
            foreach ($messages as $message) {
                $prefix = 'userfrom_';
                if ($userid == $message->useridfrom) {
                    $prefix = 'userto_';
                    // If it from the user, then mark it as read, even if it wasn't by the receiver.
                    $message->isread = true;
                }
                $message->messageid = $message->id;
                $contacts[] = \core_message\helper::create_contact($message, $prefix);
            }
        }

        return new \core_message\output\messagearea\message_search_results($userid, $contacts);
    }

    /**
     * Handles searching for people in a particular course in the message area.
     *
     * @param int $userid The user id doing the searching
     * @param int $courseid The id of the course we are searching in
     * @param string $search The string the user is searching
     * @param int $limitfrom
     * @param int $limitnum
     * @return \core_message\output\messagearea\people_search_results
     */
    public static function search_people_in_course($userid, $courseid, $search, $limitfrom = 0, $limitnum = 0) {
        global $DB;

        // Get all the users in the course.
        list($esql, $params) = get_enrolled_sql(\context_course::instance($courseid), '', 0, true);
        $sql = "SELECT u.*
                  FROM {user} u
                  JOIN ($esql) je ON je.id = u.id
                 WHERE u.deleted = 0";
        // Add more conditions.
        $fullname = $DB->sql_fullname();
        $sql .= " AND u.id != :userid
                  AND " . $DB->sql_like($fullname, ':search', false) . "
             ORDER BY " . $DB->sql_fullname();
        $params = array_merge(array('userid' => $userid, 'search' => '%' . $search . '%'), $params);


        // Convert all the user records into contacts.
        $contacts = array();
        if ($users = $DB->get_records_sql($sql, $params, $limitfrom, $limitnum)) {
            foreach ($users as $user) {
                $contacts[] = \core_message\helper::create_contact($user);
            }
        }

        return new \core_message\output\messagearea\people_search_results($contacts);
    }

    /**
     * Handles searching for people in the message area.
     *
     * @param int $userid The user id doing the searching
     * @param string $search The string the user is searching
     * @param int $limitnum
     * @return \core_message\output\messagearea\people_search_results
     */
    public static function search_people($userid, $search, $limitnum = 0) {
        global $CFG, $DB;

        require_once($CFG->dirroot . '/lib/coursecatlib.php');

        // Used to search for contacts.
        $fullname = $DB->sql_fullname();
        $ufields = \user_picture::fields('u', array('lastaccess'));

        // Users not to include.
        $excludeusers = array($userid, $CFG->siteguest);
        list($exclude, $excludeparams) = $DB->get_in_or_equal($excludeusers, SQL_PARAMS_NAMED, 'param', false);

        // Ok, let's search for contacts first.
        $contacts = array();
        $sql = "SELECT $ufields
                  FROM {user} u
                  JOIN {message_contacts} mc
                    ON u.id = mc.contactid
                 WHERE mc.userid = :userid
                   AND u.deleted = 0
                   AND u.confirmed = 1
                   AND " . $DB->sql_like($fullname, ':search', false) . "
                   AND u.id $exclude
              ORDER BY " . $DB->sql_fullname();
        if ($users = $DB->get_records_sql($sql, array('userid' => $userid, 'search' => '%' . $search . '%') +
            $excludeparams, 0, $limitnum)) {
            foreach ($users as $user) {
                $contacts[] = \core_message\helper::create_contact($user);
            }
        }

        // Now, let's get the courses.
        $courses = array();
        if ($arrcourses = \coursecat::search_courses(array('search' => $search), array('limit' => $limitnum))) {
            foreach ($arrcourses as $course) {
                $data = new \stdClass();
                $data->id = $course->id;
                $data->shortname = $course->shortname;
                $data->fullname = $course->fullname;
                $courses[] = $data;
            }
        }

        // Let's get those non-contacts. Toast them gears boi.
        $noncontacts = array();
        $sql = "SELECT $ufields
                  FROM {user} u
                 WHERE u.deleted = 0
                   AND u.confirmed = 1
                   AND " . $DB->sql_like($fullname, ':search', false) . "
                   AND u.id $exclude
                   AND u.id NOT IN (SELECT contactid 
                                      FROM {message_contacts} 
                                     WHERE userid = :userid)
              ORDER BY " . $DB->sql_fullname();
        if ($users = $DB->get_records_sql($sql,  array('userid' => $userid, 'search' => '%' . $search . '%') +
            $excludeparams, 0, $limitnum)) {
            foreach ($users as $user) {
                $noncontacts[] = \core_message\helper::create_contact($user);
            }
        }

        return new \core_message\output\messagearea\people_search_results($contacts, $courses, $noncontacts);
    }

    /**
     * Returns the contacts and their conversation to display in the contacts area.
     *
     * @param int $userid The user id
     * @param int $otheruserid The id of the user we have selected, 0 if none have been selected
     * @param int $limitfrom
     * @param int $limitnum
     * @return \core_message\output\messagearea\contacts
     */
    public static function get_conversations($userid, $otheruserid = 0, $limitfrom = 0, $limitnum = 0) {
        $arrcontacts = array();
        if ($conversations = message_get_recent_conversations($userid, $limitfrom, $limitnum)) {
            foreach ($conversations as $conversation) {
                $arrcontacts[] = \core_message\helper::create_contact($conversation);
            }
        }

        return new \core_message\output\messagearea\contacts($userid, $otheruserid, $arrcontacts);
    }

    /**
     * Returns the contacts to display in the contacts area.
     *
     * @param int $userid The user id
     * @param int $limitfrom
     * @param int $limitnum
     * @return \core_message\output\messagearea\contacts
     */
    public static function get_contacts($userid, $limitfrom = 0, $limitnum = 0) {
        global $DB;

        $arrcontacts = array();
        $sql = "SELECT u.*
                  FROM {message_contacts} mc
                  JOIN {user} u
                    ON mc.contactid = u.id
                 WHERE mc.userid = :userid
                   AND u.deleted = 0
              ORDER BY " . $DB->sql_fullname();
        if ($contacts = $DB->get_records_sql($sql, array('userid' => $userid), $limitfrom, $limitnum)) {
            foreach ($contacts as $contact) {
                $arrcontacts[] = \core_message\helper::create_contact($contact);
            }
        }

        return new \core_message\output\messagearea\contacts($userid, 0, $arrcontacts);
    }

    /**
     * Returns the messages to display in the message area.
     *
     * @param int $userid the current user
     * @param int $otheruserid the other user
     * @param int $limitfrom
     * @param int $limitnum
     * @param string $sort
     * @return \core_message\output\messagearea\messages
     */
    public static function get_messages($userid, $otheruserid, $limitfrom = 0, $limitnum = 0, $sort = 'timecreated ASC') {
        $arrmessages = array();
        if ($messages = \core_message\helper::get_messages($userid, $otheruserid, 0, $limitfrom, $limitnum, $sort)) {
            $arrmessages = \core_message\helper::create_messages($userid, $messages);
        }

        return new \core_message\output\messagearea\messages($userid, $otheruserid, $arrmessages);
    }

    /**
     * Returns the most recent message between two users.
     *
     * @param int $userid the current user
     * @param int $otheruserid the other user
     * @return \core_message\output\messagearea\message|null
     */
    public static function get_most_recent_message($userid, $otheruserid) {
        // We want two messages here so we get an accurate 'blocktime' value.
        if ($messages = \core_message\helper::get_messages($userid, $otheruserid, 0, 0, 2, 'timecreated DESC')) {
            // Swap the order so we now have them in historical order.
            $messages = array_reverse($messages);
            $arrmessages = \core_message\helper::create_messages($userid, $messages);
            return array_pop($arrmessages);
        }

        return null;
    }

    /**
     * Returns the profile information for a contact for a user.
     *
     * @param int $userid The user id
     * @param int $otheruserid The id of the user whose profile we want to view.
     * @return \core_message\output\messagearea\profile
     */
    public static function get_profile($userid, $otheruserid) {
        global $CFG, $DB;

        require_once($CFG->dirroot . '/user/lib.php');

        if ($user = \core_user::get_user($otheruserid)) {
            // Create the data we are going to pass to the renderable.
            $userfields = user_get_user_details($user, null, array('city', 'country', 'email',
                'profileimageurl', 'profileimageurlsmall', 'lastaccess'));
            if ($userfields) {
                $data = new \stdClass();
                $data->userid = $userfields['id'];
                $data->fullname = $userfields['fullname'];
                $data->city = isset($userfields['city']) ? $userfields['city'] : '';
                $data->country = isset($userfields['country']) ? $userfields['country'] : '';
                $data->email = isset($userfields['email']) ? $userfields['email'] : '';
                $data->profileimageurl = isset($userfields['profileimageurl']) ? $userfields['profileimageurl'] : '';
                $data->profileimageurlsmall = isset($userfields['profileimageurlsmall']) ?
                    $userfields['profileimageurlsmall'] : '';
                if (isset($userfields['lastaccess'])) {
                    $data->isonline = \core_message\helper::is_online($userfields['lastaccess']);
                } else {
                    $data->isonline = 0;
                }
            } else {
                // Technically the access checks in user_get_user_details are correct,
                // but messaging has never obeyed them. In order to keep messaging working
                // we at least need to return a minimal user record.
                $data = new \stdClass();
                $data->userid = $otheruserid;
                $data->fullname = fullname($user);
                $data->city = '';
                $data->country = '';
                $data->email = '';
                $data->profileimageurl = '';
                $data->profileimageurlsmall = '';
                $data->isonline = 0;
            }
            // Check if the contact has been blocked.
            $contact = $DB->get_record('message_contacts', array('userid' => $userid, 'contactid' => $otheruserid));
            if ($contact) {
                $data->isblocked = $contact->blocked;
                $data->iscontact = true;
            } else {
                $data->isblocked = false;
                $data->iscontact = false;
            }

            return new \core_message\output\messagearea\profile($userid, $data);
        }
    }

    /**
     * Checks if a user can delete messages they have either received or sent.
     *
     * @param int $userid The user id of who we want to delete the messages for (this may be done by the admin
     *  but will still seem as if it was by the user)
     * @return bool Returns true if a user can delete the message, false otherwise.
     */
    public static function can_delete_conversation($userid) {
        global $USER;

        $systemcontext = \context_system::instance();

        // Let's check if the user is allowed to delete this message.
        if (has_capability('moodle/site:deleteanymessage', $systemcontext) ||
            ((has_capability('moodle/site:deleteownmessage', $systemcontext) &&
                $USER->id == $userid))) {
            return true;
        }

        return false;
    }

    /**
     * Deletes a conversation.
     *
     * This function does not verify any permissions.
     *
     * @param int $userid The user id of who we want to delete the messages for (this may be done by the admin
     *  but will still seem as if it was by the user)
     * @param int $otheruserid The id of the other user in the conversation
     * @return bool
     */
    public static function delete_conversation($userid, $otheruserid) {
        global $DB, $USER;

        // We need to update the tables to mark all messages as deleted from and to the other user. This seems worse than it
        // is, that's because our DB structure splits messages into two tables (great idea, huh?) which causes code like this.
        // This won't be a particularly heavily used function (at least I hope not), so let's hope MDL-36941 gets worked on
        // soon for the sake of any developers' sanity when dealing with the messaging system.
        $now = time();
        $sql = "UPDATE {message}
                   SET timeuserfromdeleted = :time
                 WHERE useridfrom = :userid
                   AND useridto = :otheruserid
                   AND notification = 0";
        $DB->execute($sql, array('time' => $now, 'userid' => $userid, 'otheruserid' => $otheruserid));

        $sql = "UPDATE {message}
                   SET timeusertodeleted = :time
                 WHERE useridto = :userid
                   AND useridfrom = :otheruserid
                   AND notification = 0";
        $DB->execute($sql, array('time' => $now, 'userid' => $userid, 'otheruserid' => $otheruserid));

        $sql = "UPDATE {message_read}
                   SET timeuserfromdeleted = :time
                 WHERE useridfrom = :userid
                   AND useridto = :otheruserid
                   AND notification = 0";
        $DB->execute($sql, array('time' => $now, 'userid' => $userid, 'otheruserid' => $otheruserid));

        $sql = "UPDATE {message_read}
                   SET timeusertodeleted = :time
                 WHERE useridto = :userid
                   AND useridfrom = :otheruserid
                   AND notification = 0";
        $DB->execute($sql, array('time' => $now, 'userid' => $userid, 'otheruserid' => $otheruserid));

        // Now we need to trigger events for these.
        if ($messages = \core_message\helper::get_messages($userid, $otheruserid, $now)) {
            // Loop through and trigger a deleted event.
            foreach ($messages as $message) {
                $messagetable = 'message';
                if (!empty($message->timeread)) {
                    $messagetable = 'message_read';
                }

                // Trigger event for deleting the message.
                \core\event\message_deleted::create_from_ids($message->useridfrom, $message->useridto,
                    $USER->id, $messagetable, $message->id)->trigger();
            }
        }

        return true;
    }
}

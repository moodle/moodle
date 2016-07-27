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
     * Returns the contacts and their conversation to display in the contacts area.
     *
     * @param int $userid The user id
     * @param int $otheruserid The id of the user we have selected, 0 if none have been selected
     * @param int $limitfrom
     * @param int $limitnum
     * @return \core_message\output\contacts
     */
    public static function get_conversations($userid, $otheruserid = 0, $limitfrom = 0, $limitnum = 0) {
        $arrcontacts = array();
        if ($conversations = message_get_recent_conversations($userid, $limitfrom, $limitnum)) {
            foreach ($conversations as $conversation) {
                $arrcontacts[] = \core_message\helper::create_contact($conversation);
            }
        }

        return new \core_message\output\contacts($userid, $otheruserid, $arrcontacts);
    }

    /**
     * Returns the contacts to display in the contacts area.
     *
     * @param int $userid The user id
     * @param int $limitfrom
     * @param int $limitnum
     * @return \core_message\output\contacts
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

        return new \core_message\output\contacts($userid, 0, $arrcontacts, false);
    }

    /**
     * Returns the messages to display in the message area.
     *
     * @param int $userid the current user
     * @param int $otheruserid the other user
     * @param int $limitfrom
     * @param int $limitnum
     * @param string $sort
     * @return \core_message\output\messages
     */
    public static function get_messages($userid, $otheruserid, $limitfrom = 0, $limitnum = 0, $sort = 'timecreated ASC') {
        $arrmessages = array();
        if ($messages = \core_message\helper::get_messages($userid, $otheruserid, 0, $limitfrom, $limitnum, $sort)) {
            $arrmessages = \core_message\helper::create_messages($userid, $messages);
        }

        return new \core_message\output\messages($userid, $otheruserid, $arrmessages);
    }

    /**
     * Returns the most recent message between two users.
     *
     * @param int $userid the current user
     * @param int $otheruserid the other user
     * @return \core_message\output\message|null
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
     * @return \core_message\output\profile
     */
    public static function get_profile($userid, $otheruserid) {
        global $CFG, $DB;

        require_once($CFG->dirroot . '/user/lib.php');

        if ($user = \core_user::get_user($otheruserid)) {
            // Create the data we are going to pass to the renderable.
            $userfields = user_get_user_details($user, null, array('city', 'country', 'email',
                'profileimageurl', 'profileimageurlsmall'));
            $data = new \stdClass();
            $data->userid = $userfields['id'];
            $data->fullname = $userfields['fullname'];
            $data->city = isset($userfields['city']) ? $userfields['city'] : '';
            $data->country = isset($userfields['country']) ? $userfields['country'] : '';
            $data->email = isset($userfields['email']) ? $userfields['email'] : '';
            $data->profileimageurl = isset($userfields['profileimageurl']) ? $userfields['profileimageurl'] : '';
            $data->profileimageurlsmall = isset($userfields['profileimageurlsmall']) ?
                $userfields['profileimageurlsmall'] : '';
            // Check if the contact has been blocked.
            $contact = $DB->get_record('message_contacts', array('userid' => $userid, 'contactid' => $otheruserid));
            if ($contact) {
                $data->isblocked = $contact->blocked;
                $data->iscontact = true;
            } else {
                $data->isblocked = false;
                $data->iscontact = false;
            }

            return new \core_message\output\profile($userid, $data);
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

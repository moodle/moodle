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
     * @return \core_message\output\messages
     */
    public static function get_messages($userid, $otheruserid, $limitfrom = 0, $limitnum = 0) {
        $arrmessages = array();
        if ($messages = \core_message\helper::get_messages($userid, $otheruserid, $limitfrom, $limitnum)) {
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
        if ($messages = \core_message\helper::get_messages($userid, $otheruserid, 0, 2, 'timecreated DESC')) {
            // Swap the order so we now have them in historical order.
            $messages = array_reverse($messages);
            $arrmessages = \core_message\helper::create_messages($userid, $messages);
            return array_pop($arrmessages);
        }

        return null;
    }
}

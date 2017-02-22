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
 * Contains helper class for the message area.
 *
 * @package    core_message
 * @copyright  2016 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_message;

defined('MOODLE_INTERNAL') || die();

/**
 * Helper class for the message area.
 *
 * @copyright  2016 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper {

    /**
     * Helper function to retrieve the messages between two users
     *
     * @param int $userid the current user
     * @param int $otheruserid the other user
     * @param int $timedeleted the time the message was deleted
     * @param int $limitfrom
     * @param int $limitnum
     * @param string $sort
     * @param int $timefrom the time from the message being sent
     * @param int $timeto the time up until the message being sent
     * @return array of messages
     */
    public static function get_messages($userid, $otheruserid, $timedeleted = 0, $limitfrom = 0, $limitnum = 0,
                                        $sort = 'timecreated ASC', $timefrom = 0, $timeto = 0) {
        global $DB;

        $messageid = $DB->sql_concat("'message_'", 'id');
        $messagereadid = $DB->sql_concat("'messageread_'", 'id');

        $sql = "SELECT {$messageid} AS fakeid, id, useridfrom, useridto, subject, fullmessage, fullmessagehtml, fullmessageformat,
                       smallmessage, notification, timecreated, 0 as timeread
                  FROM {message} m
                 WHERE ((useridto = ? AND useridfrom = ? AND timeusertodeleted = ?)
                    OR (useridto = ? AND useridfrom = ? AND timeuserfromdeleted = ?))
                   AND notification = 0
                   %where%
             UNION ALL
                SELECT {$messagereadid} AS fakeid, id, useridfrom, useridto, subject, fullmessage, fullmessagehtml, fullmessageformat,
                       smallmessage, notification, timecreated, timeread
                  FROM {message_read} mr
                 WHERE ((useridto = ? AND useridfrom = ? AND timeusertodeleted = ?)
                    OR (useridto = ? AND useridfrom = ? AND timeuserfromdeleted = ?))
                   AND notification = 0
                   %where%
              ORDER BY $sort";
        $params1 = array($userid, $otheruserid, $timedeleted,
                         $otheruserid, $userid, $timedeleted);

        $params2 = array($userid, $otheruserid, $timedeleted,
                         $otheruserid, $userid, $timedeleted);
        $where = array();

        if (!empty($timefrom)) {
            $where[] = 'AND timecreated >= ?';
            $params1[] = $timefrom;
            $params2[] = $timefrom;
        }

        if (!empty($timeto)) {
            $where[] = 'AND timecreated <= ?';
            $params1[] = $timeto;
            $params2[] = $timeto;
        }

        $sql = str_replace('%where%', implode(' ', $where), $sql);
        $params = array_merge($params1, $params2);

        return $DB->get_records_sql($sql, $params, $limitfrom, $limitnum);
    }

    /**
     * Helper function to return an array of messages.
     *
     * @param int $userid
     * @param array $messages
     * @return array
     */
    public static function create_messages($userid, $messages) {
        // Store the messages.
        $arrmessages = array();

        // We always view messages from oldest to newest, ensure we have it in that order.
        $lastmessage = end($messages);
        $firstmessage = reset($messages);
        if ($lastmessage->timecreated < $firstmessage->timecreated) {
            $messages = array_reverse($messages);
        }

        // Keeps track of the last day, month and year combo we were viewing.
        $day = '';
        $month = '';
        $year = '';
        foreach ($messages as $message) {
            // Check if we are now viewing a different block period.
            $displayblocktime = false;
            $date = usergetdate($message->timecreated);
            if ($day != $date['mday'] || $month != $date['month'] || $year != $date['year']) {
                $day = $date['mday'];
                $month = $date['month'];
                $year = $date['year'];
                $displayblocktime = true;
            }
            // Store the message to pass to the renderable.
            $msg = new \stdClass();
            $msg->id = $message->id;
            $msg->text = message_format_message_text($message);
            $msg->currentuserid = $userid;
            $msg->useridfrom = $message->useridfrom;
            $msg->useridto = $message->useridto;
            $msg->displayblocktime = $displayblocktime;
            $msg->timecreated = $message->timecreated;
            $msg->timeread = $message->timeread;
            $arrmessages[] = $msg;
        }

        return $arrmessages;
    }

    /**
     * Helper function for creating a contact object.
     *
     * @param \stdClass $contact
     * @param string $prefix
     * @return \stdClass
     */
    public static function create_contact($contact, $prefix = '') {
        global $PAGE;

        // Create the data we are going to pass to the renderable.
        $userfields = \user_picture::unalias($contact, array('lastaccess'), $prefix . 'id', $prefix);
        $data = new \stdClass();
        $data->userid = $userfields->id;
        $data->useridfrom = null;
        $data->fullname = fullname($userfields);
        // Get the user picture data.
        $userpicture = new \user_picture($userfields);
        $userpicture->size = 1; // Size f1.
        $data->profileimageurl = $userpicture->get_url($PAGE)->out(false);
        $userpicture->size = 0; // Size f2.
        $data->profileimageurlsmall = $userpicture->get_url($PAGE)->out(false);
        // Store the message if we have it.
        $data->ismessaging = false;
        $data->lastmessage = null;
        $data->messageid = null;
        if (isset($contact->smallmessage)) {
            $data->ismessaging = true;
            // Strip the HTML tags from the message for displaying in the contact area.
            $data->lastmessage = clean_param($contact->smallmessage, PARAM_NOTAGS);
            $data->useridfrom = $contact->useridfrom;
            if (isset($contact->messageid)) {
                $data->messageid = $contact->messageid;
            }
        }
        $data->isonline = null;
        if (self::show_online_status($userfields)) {
            $data->isonline = self::is_online($userfields->lastaccess);
        }
        $data->isblocked = isset($contact->blocked) ? (bool) $contact->blocked : false;
        $data->isread = isset($contact->isread) ? (bool) $contact->isread : false;
        $data->unreadcount = isset($contact->unreadcount) ? $contact->unreadcount : null;

        return $data;
    }

    /**
     * Helper function for checking if we should show the user's online status.
     *
     * @param \stdClass $user
     * @return boolean
     */
    public static function show_online_status($user) {
        global $CFG;

        require_once($CFG->dirroot . '/user/lib.php');

        if ($lastaccess = user_get_user_details($user, null, array('lastaccess'))) {
            if (isset($lastaccess['lastaccess'])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Helper function for checking the time meets the 'online' condition.
     *
     * @param int $lastaccess
     * @return boolean
     */
    public static function is_online($lastaccess) {
        global $CFG;

        // Variable to check if we consider this user online or not.
        $timetoshowusers = 300; // Seconds default.
        if (isset($CFG->block_online_users_timetosee)) {
            $timetoshowusers = $CFG->block_online_users_timetosee * 60;
        }
        $time = time() - $timetoshowusers;

        return $lastaccess >= $time;
    }

    /**
     * Get providers preferences.
     *
     * @param array $providers
     * @param int $userid
     * @return \stdClass
     */
    public static function get_providers_preferences($providers, $userid) {
        $preferences = new \stdClass();

        // Get providers preferences.
        foreach ($providers as $provider) {
            foreach (array('loggedin', 'loggedoff') as $state) {
                $linepref = get_user_preferences('message_provider_' . $provider->component . '_' . $provider->name
                    . '_' . $state, '', $userid);
                if ($linepref == '') {
                    continue;
                }
                $lineprefarray = explode(',', $linepref);
                $preferences->{$provider->component.'_'.$provider->name.'_'.$state} = array();
                foreach ($lineprefarray as $pref) {
                    $preferences->{$provider->component.'_'.$provider->name.'_'.$state}[$pref] = 1;
                }
            }
        }

        return $preferences;
    }

    /**
     * Requires the JS libraries for the toggle contact button.
     *
     * @return void
     */
    public static function togglecontact_requirejs() {
        global $PAGE;

        static $done = false;
        if ($done) {
            return;
        }

        $PAGE->requires->js_call_amd('core_message/toggle_contact_button', 'enhance', array('#toggle-contact-button'));
        $done = true;
    }

    /**
     * Returns the attributes to place on a contact button.
     *
     * @param object $user User object.
     * @param bool $iscontact
     * @return array
     */
    public static function togglecontact_link_params($user, $iscontact = false) {
        $params = array(
            'data-userid' => $user->id,
            'data-is-contact' => $iscontact,
            'id' => 'toggle-contact-button',
            'role' => 'button',
            'class' => 'ajax-contact-button',
        );

        return $params;
    }

    /**
     * Returns the cache key for the time created value of the last message between two users.
     *
     * @param int $userid
     * @param int $user2id
     * @return string
     */
    public static function get_last_message_time_created_cache_key($userid, $user2id) {
        $ids = [$userid, $user2id];
        sort($ids);
        return implode('_', $ids);
    }
}

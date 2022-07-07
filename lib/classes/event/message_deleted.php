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
 * Message deleted event.
 *
 * @package    core
 * @copyright  2015 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\event;

defined('MOODLE_INTERNAL') || die();

/**
 * Message deleted event class.
 *
 * @property-read array $other {
 *      Extra information about event.
 *
 *      - int messageid: the id of the message.
 * }
 *
 * @package    core
 * @since      Moodle 3.0
 * @copyright  2015 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class message_deleted extends base {

    /**
     * Create event using ids.
     *
     * @param int $userid the user who the we are deleting the message for.
     * @param int $userdeleting the user who deleted it (it's possible that an admin may delete a message on someones behalf)
     * @param int $messageid the id of the message that was deleted.
     * @param int $muaid The id in the message_user_actions table.
     * @return message_deleted
     */
    public static function create_from_ids(int $userid, int $userdeleting, int $messageid, int $muaid) : message_deleted {
        // We set the userid to the user who deleted the message, nothing to do
        // with whether or not they sent or received the message.
        $event = self::create(array(
            'objectid' => $muaid,
            'userid' => $userdeleting,
            'context' => \context_system::instance(),
            'relateduserid' => $userid,
            'other' => array(
                'messageid' => $messageid,
            )
        ));

        return $event;
    }

    /**
     * Init method.
     */
    protected function init() {
        $this->data['objecttable'] = 'message_user_actions';
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventmessagedeleted', 'message');
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        // This is for BC when the event used to take this value into account before group conversations.
        // We still want the same message to display for older events.
        if (isset($this->other['useridto'])) {
            // Check if the person who deleted the message received or sent it.
            if ($this->userid == $this->other['useridto']) {
                $str = 'from';
            } else {
                $str = 'to';
            }

            return "The user with id '$this->userid' deleted a message sent $str the user with id '$this->relateduserid'.";
        }

        $messageid = $this->other['messageid'];

        // Check if the user deleting the message was not the actual user we are deleting for.
        $str = "The user with id '$this->userid' deleted a message with id '$messageid'";
        if ($this->userid != $this->relateduserid) {
            $str .= " for the user with id '$this->relateduserid'";
        }

        return $str;
    }

    /**
     * Custom validation.
     *
     * @throws \coding_exception
     * @return void
     */
    protected function validate_data() {
        parent::validate_data();

        if (!isset($this->relateduserid)) {
            throw new \coding_exception('The \'relateduserid\' must be set.');
        }

        if (!isset($this->other['messageid'])) {
            throw new \coding_exception('The \'messageid\' value must be set in other.');
        }
    }

    public static function get_objectid_mapping() {
        return array('db' => 'message_user_actions', 'restore' => base::NOT_MAPPED);
    }

    public static function get_other_mapping() {
        // Messages are not backed up, so no need to map them on restore.
        $othermapped = [];
        $othermapped['messageid'] = ['db' => 'messages', 'restore' => base::NOT_MAPPED];

        return $othermapped;
    }
}

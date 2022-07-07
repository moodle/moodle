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
 * Group message sent event.
 *
 * @package    core
 * @copyright  2018 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\event;

defined('MOODLE_INTERNAL') || die();

/**
 * Group message sent event class.
 *
 * @property-read array $other {
 *      Extra information about event.
 *
 *      - int courseid: the id of the related course.
 *      - int conversationid: the id of the conversation in which the message was sent.
 * }
 *
 * @package    core
 * @copyright  2018 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class group_message_sent extends base {
    /**
     * Create event using ids.
     * @param int $userfromid
     * @param int $conversationid
     * @param int $messageid
     * @param int $courseid course id the event is related with.
     * @return message_sent
     */
    public static function create_from_ids(int $userfromid, int $conversationid, int $messageid, int $courseid) {
        // We may be sending a message from the 'noreply' address, which means we are not actually sending a
        // message from a valid user. In this case, we will set the userid to 0.
        // Check if the userid is valid.
        if (!\core_user::is_real_user($userfromid)) {
            $userfromid = 0;
        }

        $event = self::create([
            'objectid' => $messageid,
            'userid' => $userfromid,
            'context' => \context_system::instance(),
            'other' => [
                'courseid' => $courseid,
                'conversationid' => $conversationid
            ]
        ]);

        return $event;
    }

    /**
     * Init method.
     */
    protected function init() {
        $this->data['objecttable'] = 'messages';
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventgroupmessagesent', 'message');
    }

    /**
     * Returns relevant URL.
     *
     * @return \moodle_url
     */
    public function get_url() {
        // There currently isn't a way to link back from a 'group message sent' event to a conversation.
        // So, just return the user to the index page.
        return new \moodle_url('/message/index.php');
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        $conversationid = $this->other['conversationid'];

        // Check if we are sending from a valid user.
        if (\core_user::is_real_user($this->userid)) {

            return "The user with id '$this->userid' sent a message with id '$this->objectid' to the conversation " .
                   "with id '$conversationid'.";
        }

        return "A message with id '$this->objectid' was sent by the system to the conversation with id '$conversationid'.";
    }

    /**
     * Custom validation.
     *
     * @throws \coding_exception
     * @return void
     */
    protected function validate_data() {
        parent::validate_data();

        if (!isset($this->other['courseid'])) {
            throw new \coding_exception('The \'courseid\' value must be set in other.');
        }
        if (!isset($this->other['conversationid'])) {
            throw new \coding_exception('The \'conversationid\' value must be set in other.');
        }
    }

    /**
     * Get the object this event maps to.
     *
     * @return array|string object id mapping.
     */
    public static function get_objectid_mapping() {
        return ['db' => 'messages', 'restore' => base::NOT_MAPPED];
    }

    /**
     * Get the item mappings for the 'other' fields for this event.
     *
     * @return array the array of other fields, mapped.
     */
    public static function get_other_mapping() {
        $othermapped = [];
        $othermapped['courseid'] = ['db' => 'course', 'restore' => base::NOT_MAPPED];
        $othermapped['conversationid'] = ['db' => 'message_conversations', 'restore' => base::NOT_MAPPED];
        return $othermapped;
    }
}

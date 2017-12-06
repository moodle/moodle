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
 * Message sent event.
 *
 * @package    core
 * @copyright  2014 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\event;

defined('MOODLE_INTERNAL') || die();

/**
 * Message sent event class.
 *
 * @property-read array $other {
 *      Extra information about event.
 *
 *      - int messageid: the id of the message.
 *      - int courseid: the id of the related course.
 * }
 *
 * @package    core
 * @since      Moodle 2.7
 * @copyright  2014 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class message_sent extends base {
    /**
     * Create event using ids.
     * @todo MDL-55449 Make $courseid mandatory in Moodle 3.6
     * @param int $userfromid
     * @param int $usertoid
     * @param int $messageid
     * @param int|null $courseid course id the event is related with. Use SITEID if no relation exists.
     * @return message_sent
     */
    public static function create_from_ids($userfromid, $usertoid, $messageid, $courseid = null) {
        // We may be sending a message from the 'noreply' address, which means we are not actually sending a
        // message from a valid user. In this case, we will set the userid to 0.
        // Check if the userid is valid.
        if (!\core_user::is_real_user($userfromid)) {
            $userfromid = 0;
        }

        // TODO: MDL-55449 Make $courseid mandatory in Moodle 3.6.
        if (is_null($courseid)) {
            // Arrived here with not defined $courseid to associate the event with.
            // Let's default to SITEID and perform debugging so devs are aware. MDL-47162.
            $courseid = SITEID;
            debugging('message_sent::create_from_ids() needs a $courseid to be passed, nothing was detected. Please, change ' .
                    'the call to include it, using SITEID if the message is unrelated to any real course.', DEBUG_DEVELOPER);
        }

        $event = self::create(array(
            'userid' => $userfromid,
            'context' => \context_system::instance(),
            'relateduserid' => $usertoid,
            'other' => array(
                // In earlier versions it can either be the id in the 'message_read' or 'message' table.
                // Now it is always the id from 'message' table. Please note that the record is still moved
                // to the 'message_read' table later when message marked as read.
                'messageid' => $messageid,
                'courseid' => $courseid
            )
        ));

        return $event;
    }

    /**
     * Init method.
     */
    protected function init() {
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventmessagesent', 'message');
    }

    /**
     * Returns relevant URL.
     *
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url('/message/index.php', array('user1' => $this->userid, 'user2' => $this->relateduserid));
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        // Check if we are sending from a valid user.
        if (\core_user::is_real_user($this->userid)) {
            return "The user with id '$this->userid' sent a message to the user with id '$this->relateduserid'.";
        }

        return "A message was sent by the system to the user with id '$this->relateduserid'.";
    }

    /**
     * Return legacy data for add_to_log().
     *
     * @return array
     */
    protected function get_legacy_logdata() {
        // The add_to_log function was only ever called when we sent a message from one user to another. We do not want
        // to return the legacy log data if we are sending a system message, so check that the userid is valid.
        if (\core_user::is_real_user($this->userid)) {
            return array(SITEID, 'message', 'write', 'index.php?user=' . $this->userid . '&id=' . $this->relateduserid .
                '&history=1#m' . $this->other['messageid'], $this->userid);
        }

        return null;
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

        if (!isset($this->other['courseid'])) {
            throw new \coding_exception('The \'courseid\' value must be set in other.');
        }
    }

    public static function get_objectid_mapping() {
        // Messages are not backed up, so no need to map them.
        return false;
    }

    public static function get_other_mapping() {
        // Messages are not backed up, so no need to map them on restore.
        $othermapped = array();
        // The messages table could vary for older events - so cannot be mapped.
        $othermapped['messageid'] = array('db' => base::NOT_MAPPED, 'restore' => base::NOT_MAPPED);
        $othermapped['courseid'] = array('db' => base::NOT_MAPPED, 'restore' => base::NOT_MAPPED);
        return $othermapped;
    }
}

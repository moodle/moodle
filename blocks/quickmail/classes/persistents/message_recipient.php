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
 * @package    block_quickmail
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_quickmail\persistents;

defined('MOODLE_INTERNAL') || die();

use block_quickmail\persistents\concerns\enhanced_persistent;
use block_quickmail\persistents\concerns\belongs_to_a_message;
use block_quickmail\persistents\concerns\belongs_to_a_user;
use block_quickmail\persistents\message;

class message_recipient extends \block_quickmail\persistents\persistent {

    use enhanced_persistent,
        belongs_to_a_message,
        belongs_to_a_user;

    /** Table name for the persistent. */
    const TABLE = 'block_quickmail_msg_recips';

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return [
            'message_id' => [
                'type' => PARAM_INT,
            ],
            'user_id' => [
                'type' => PARAM_INT,
            ],
            'moodle_message_id' => [
                'type' => PARAM_INT,
                'default' => 0
            ],
            'sent_at' => [
                'type' => PARAM_INT,
                'default' => 0
            ],
        ];
    }

    // Relationships.
    /**
     * Returns this message recipient's parent message
     *
     * @return message
     */
    public function get_message() {
        $messageid = $this->get('message_id');

        return new message($messageid);
    }

    // Getters.
    /**
     * Reports whether or not this recipient has been messaged
     *
     * @return bool
     */
    public function has_been_sent_to() {
        return (bool) $this->get('sent_at');
    }

    // Getters.
    /**
     * Checks if the account still exists as some users may get
     * "deleted" before a queue'd message is sent.
     *
     * @param  int        userid
     * @return bool
     */
    public function account_exists($userid = 0) {
        global $DB;
        // First check the user exists.

        $usertestrecord = array_values($DB->get_records_sql(
            'SELECT deleted
                FROM {user}
                WHERE id = ?',
            array($userid)
        ));

        return (bool) $usertestrecord[0]->deleted;
    }

    // Setters.
    /**
     * Update the recipient as having been sent to right now
     *
     * Optionally, save the moodle message id on the recipient
     *
     * @param  int        $moodlemessageid
     * @return void
     */
    public function mark_as_sent_to($moodlemessageid = 0) {
        $this->set('sent_at', time());

        if ($moodlemessageid) {
            $this->set('moodle_message_id', (int) $moodlemessageid);
        }

        $this->update();
    }
    // Maintenance.
    /**
     * Deletes a recipient for this message
     *
     * @param  int messageid
     * @param  int userid
     * @return void
     */
    public function remove_recipient_from_message($messageid = 0, $userid = 0) {
        global $DB;
        // Delete recipient belonging to this message.
        $DB->delete_records('block_quickmail_msg_recips', ['message_id' => $messageid, 'user_id' => $userid]);
    }

    // Custom Static Methods.
    /**
     * Deletes all recipients for this message
     *
     * @param  message $message
     * @return void
     */
    public static function clear_all_for_message(message $message) {
        global $DB;

        // Delete all recipients belonging to this message.
        $DB->delete_records('block_quickmail_msg_recips', ['message_id' => $message->get('id')]);
    }

    /**
     * Update the recipient belonging to the given message and user as have been sent to right now
     *
     * @param  message    $message
     * @param  core_user  $user
     * @param  int        $moodlemessageid
     * @return void
     */
    public static function mark_as_sent(message $message, $user, $moodlemessageid = 0) {
        $userid = 0;
        if (is_int($user)) {
            $userid = $user;
        } else {
            $userid = $user->id;
        }

        $recipient = self::get_record([
            'message_id' => $message->get('id'),
            'user_id' => $userid
        ]);

        $recipient->set('sent_at', time());
        $recipient->set('moodle_message_id', (int) $moodlemessageid);

        $recipient->update();
    }

}

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

namespace block_quickmail\messenger\factories\course_recipient_send;

defined('MOODLE_INTERNAL') || die();

use block_quickmail\messenger\factories\course_recipient_send\recipient_send_factory;
use block_quickmail\messenger\factories\course_recipient_send\recipient_send_factory_interface;
use core\message\message as moodle_message;
use block_quickmail_string;

class message_recipient_send_factory extends recipient_send_factory implements recipient_send_factory_interface {

    // TODO: Either use message_post_message directly or use the send_instant_message external function.
    public function set_factory_params() {
        $this->message_params->component = 'moodle'; // Must exist in the table message_providers.
        $this->message_params->name = 'instantmessage'; // Type of message from that module (as module defines it).
        $this->message_params->fullmessageformat = FORMAT_HTML;  // ... <------- check on this, should be hard-coded? FORMAT_PLAIN?
        $this->message_params->notification = false; // Just in case.
    }

    public function set_factory_computed_params() {
        $this->message_params->smallmessage = ''; // The small version of the message.
    }

    /**
     * Executes the sending of this message to this recipient
     *
     * Additionally, if successful, handle any post send actions (marking as sent, sending to mentors if appropriate)
     *
     * @return bool
     */
    public function send() {
        $result = $this->send_message_to_user();

        // If the message was sent successfully, handle post send tasks.
        if ($result) {
            $this->handle_recipient_post_send((int) $result);
        }

        return $result;
    }

    /**
     * Sends this formatted message content to the given user
     *
     * If no user is given, sends to this recipient user
     *
     * @param  object  $user
     * @param  array   $options
     * @return mixed  (either the int ID of the new message or false is unsuccessful)
     */
    private function send_message_to_user($user = null, $options = []) {
        // If no user was specified, use the recipient user.
        if (is_null($user)) {
            $user = $this->message_params->userto;
        }

        $moodlemessage = new moodle_message();

        $moodlemessage->component = $this->message_params->component;
        $moodlemessage->name = $this->message_params->name;
        $moodlemessage->userto = $user;
        $moodlemessage->userfrom = $this->message_params->userfrom;
        $moodlemessage->subject = $this->get_subject_prefix($options) . $this->message_params->subject;
        $moodlemessage->fullmessage = $this->get_message_prefix($options) . $this->message_params->fullmessage;
        $moodlemessage->fullmessageformat = $this->message_params->fullmessageformat;
        $moodlemessage->fullmessagehtml = $this->get_message_prefix($options) . $this->message_params->fullmessagehtml;
        $moodlemessage->smallmessage = $this->message_params->smallmessage;
        $moodlemessage->notification = $this->message_params->notification;

        // If moodle version is 3.2+, a courseid is required for sending messages.
        global $CFG;

        if ($CFG->version >= 2016120500) {
            $moodlemessage->courseid = $this->message->get('course_id');
        }

        // Returns mixed the integer ID of the new message or false if there was a problem with submitted data.
        $result = message_send($moodlemessage);

        return $result;
    }

    /**
     * Sends a "mentor-formatted" message to the given mentor user
     *
     * @param  object  $mentoruser
     * @param  object  $menteeuser
     * @return bool
     */
    private function send_message_to_mentor_user($mentoruser, $menteeuser) {
        return $this->send_message_to_user($mentoruser, [
            'subject_prefix' => block_quickmail_string::get('mentor_copy_subject_prefix'),
            'message_prefix' => block_quickmail_string::get('mentor_copy_message_prefix', fullname($menteeuser))
        ]);
    }

    /**
     * Sends this formatted message to any existing mentors of this recipient user
     * which are configured by context in moodle (see: docs.moodle.org/35/en/Parent_role)
     *
     * @return void
     */
    public function send_to_mentor_users() {
        $mentorusers = $this->get_recipient_mentors();

        foreach ($mentorusers as $mentoruser) {
            $this->send_message_to_mentor_user($mentoruser, $this->message_params->userto);
        }
    }

}

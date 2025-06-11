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

defined('MOODLE_INTERNAL') || die();

// Message record creation helpers.
trait creates_message_records {

    // Additional_data (recipient_users).
    public function create_compose_message($course, $sendinguser, array $additionaldata = [], array $overrideparams = []) {
        $params = $this->get_create_course_message_params($overrideparams);

        $data = new stdClass();
        $data->course_id = $course->id;
        $data->user_id = $sendinguser->id;
        $data->message_type = $params['message_type'];
        $data->alternate_email_id = $params['alternate_email_id'];
        $data->signature_id = $params['signature_id'];
        $data->subject = $params['subject'];
        $data->body = $params['body'];
        $data->editor_format = $params['editor_format'];
        $data->sent_at = $params['sent_at'];
        $data->to_send_at = $params['to_send_at'];
        $data->is_draft = $params['is_draft'];
        $data->send_receipt = $params['send_receipt'];
        $data->is_sending = $params['is_sending'];
        $data->no_reply = $params['no_reply'];

        $message = new block_quickmail\persistents\message(0, $data);
        $message->create();

        // Recipient creation.
        if (array_key_exists('recipient_users', $additionaldata)) {
            // Make each of these user a recipient.
            foreach ($additionaldata['recipient_users'] as $user) {
                $recipient = $this->create_message_recipient_from_user($message, $user);
            }
        }

        return $message;
    }

    public function get_create_course_message_params(array $overrideparams) {
        $params = [];

        $params['message_type'] = array_key_exists('message_type',
            $overrideparams) ? $overrideparams['message_type'] : 'email';
        $params['alternate_email_id'] = array_key_exists('alternate_email_id',
            $overrideparams) ? $overrideparams['alternate_email_id'] : '0';
        $params['signature_id'] = array_key_exists('signature_id',
            $overrideparams) ? $overrideparams['signature_id'] : '0';
        $params['subject'] = array_key_exists('subject',
            $overrideparams) ? $overrideparams['subject'] : 'this is the subject';
        $params['body'] = array_key_exists('body',
            $overrideparams) ? $overrideparams['body'] : 'this is a very important message body';
        $params['editor_format'] = array_key_exists('editor_format',
            $overrideparams) ? $overrideparams['editor_format'] : 1;
        $params['sent_at'] = array_key_exists('sent_at',
            $overrideparams) ? $overrideparams['sent_at'] : 0;
        $params['to_send_at'] = array_key_exists('to_send_at',
            $overrideparams) ? $overrideparams['to_send_at'] : 0;
        $params['is_draft'] = array_key_exists('is_draft',
            $overrideparams) ? $overrideparams['is_draft'] : false;
        $params['send_receipt'] = array_key_exists('send_receipt',
            $overrideparams) ? $overrideparams['send_receipt'] : '0';
        $params['is_sending'] = array_key_exists('is_sending',
            $overrideparams) ? $overrideparams['is_sending'] : false;
        $params['no_reply'] = array_key_exists('no_reply',
            $overrideparams) ? $overrideparams['no_reply'] : 0;

        return $params;
    }

    public function create_message_recipient_from_user($message, $user) {
        $recipient = block_quickmail\persistents\message_recipient::create_new([
            'message_id' => $message->get('id'),
            'user_id' => $user->id,
        ]);

        return $recipient;
    }

}

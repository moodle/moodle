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

namespace block_quickmail\components;

defined('MOODLE_INTERNAL') || die();

use block_quickmail\components\component;
use block_quickmail_string;
use block_quickmail_config;
use moodle_url;
use block_quickmail\persistents\signature;
use block_quickmail\persistents\alternate_email;

class view_message_component extends component implements \renderable {

    public $message;
    public $user;
    public $sent_recipient_users;
    public $unsent_recipient_users;
    public $additional_emails;
    public $attachmentcounts;
    public $attachments;

    public function __construct($params = []) {
        parent::__construct($params);
        $this->message = $this->get_param('message');
        $this->user = $this->get_param('user');
        $this->sent_recipient_users = $this->transform_recipient_users($this->get_param('sent_recipient_users'));
        $this->unsent_recipient_users = $this->transform_recipient_users($this->get_param('unsent_recipient_users'));
        $this->additional_emails = $this->get_param('additional_emails');
        $this->attachmentcounts = $this->get_param('attachments');
        $this->attachments = $this->get_param('attachmentlinks');
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @return stdClass
     */
    public function export_for_template($output) {
        $courseid = $this->message->get('course_id');

        // Get message status - queued|sending|sent.
        $status = $this->message->get_status();

        // Get message scope - compose|broadcast.
        $scope = $this->message->get_message_scope();

        $data = (object) [
            'messageId' => $this->message->get('id'),
            'status' => $status,
            'scope' => $scope,
            'fromEmail' => $this->get_message_from_email(),
            'courseName' => $this->message->get_course_property('shortname', ''),
            'messageType' => block_quickmail_string::get('message_type_' .
                $this->message->get('message_type')),
            'isQueued' => $this->message->is_queued_message(),
            'isSending' => $this->message->is_being_sent(),
            'wasSent' => ! $this->message->is_being_sent() &&
                !$this->message->is_queued_message(),
            'sendDate' => $this->message->is_queued_message()
                ? $this->message->get_readable_to_send_at()
                : $this->message->get_readable_sent_at(),
            'messageSubject' => $this->message->get('subject'),
            'messageBody' => $this->get_message_body(),
            'receiptReportRequested' => (bool) $this->message->get('send_receipt'),
            'mentorCopyRequested' => (bool) $this->message->get('send_to_mentors'),
            'attachmentCount' => count($this->attachmentcounts),
            'attachments' => $this->attachments,
            'sentRecipientCount' => count($this->sent_recipient_users),
            'sentRecipientUsers' => $this->sent_recipient_users,
            'unsentRecipientCount' => count($this->unsent_recipient_users),
            'unsentRecipientUsers' => $this->unsent_recipient_users,
            'additionalEmailCount' => count($this->additional_emails),
            'additionalEmails' => $this->additional_emails,
            'urlBack' => $courseid > 1
                ? new moodle_url('/course/view.php', ['id' => $courseid])
                : new moodle_url('/my'),
            'urlBackLabel' => $courseid > 1
                ? block_quickmail_string::get('back_to_course')
                : block_quickmail_string::get('back_to_mypage'),
            'urlDuplicate' => (
                new moodle_url(
                    '/blocks/quickmail/drafts.php',
                    ['action' => 'duplicate',
                    'id' => $this->message->get('id')])
                )->out(false),
            'urlSendNow' => (
                new moodle_url(
                    '/blocks/quickmail/queued.php',
                    ['action' => 'send',
                    'id' => $this->message->get('id')])
                )->out(false),
            'urlUnqueue' => (
                new moodle_url(
                    '/blocks/quickmail/queued.php',
                    ['action' => 'unqueue',
                    'id' => $this->message->get('id')])
                )->out(false),
        ];

        return $data;
    }

    /**
     * Returns this message's body with signature appended if necessary
     *
     * @return string
     */
    private function get_message_body() {
        $body = $this->message->get('body');

        if ($signatureid = $this->message->get('signature_id')) {
            if ($signature = signature::find_or_null($signatureid)) {
                $body .= $signature->get('signature');
            }
        }

        return $body;
    }

    /**
     * Returns this message's "sent from" email address, defaulting to system no reply address
     *
     * @return string
     */
    private function get_message_from_email() {
        // Get email address sent from.
        $fromemail = get_config('moodle', 'noreplyaddress');

        // If message was sent using alternate email, and alternate email exists.
        if (!empty($this->message->get('alternate_email_id'))) {
            if ($alternateemail = alternate_email::find_or_null($this->message->get('alternate_email_id'))) {
                $fromemail = $alternateemail->get('email');
            }
            // Otherwise, if message was not sent as no reply, and original sending user exists.
        } else if (empty($this->message->get('no_reply'))) {
            if ($user = $this->message->get_user()) {
                $fromemail = $user->email;
            }
        }

        return $fromemail;
    }

    /**
     * Returns the given users as an array of presentable strings
     *
     * @param  array  $users  an array of user objects which must contain "firstname,lastname,email" properties
     * @return array
     */
    private function transform_recipient_users($users) {
        return array_values(array_map(function($user) {
            return $user->firstname . ' ' . $user->lastname . ' (' . $user->email . ')';
        }, $users));
    }

    /**
     * Returns the given attachment persistents as an array of filename strings
     *
     * @param  array  $attachments  an array of message_attachment persistents
     * @return array
     */
    private function transform_attachments($attachments) {
        return array_values(array_map(function($attachment) {
            return $attachment->get('filename');
        }, $attachments));
    }

}

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

use block_quickmail\messenger\message\subject_prepender;
use block_quickmail\messenger\message\message_body_constructor;
use block_quickmail\messenger\message\signature_appender;
use block_quickmail\filemanager\attachment_appender;
use block_quickmail\repos\user_repo;
use block_quickmail_config;
use block_quickmail_plugin;
use block_quickmail_emailer;
use block_quickmail_string;

/**
 * This class is a base class to be extended by all types of "message types" (ex: email, message)
 * It accepts a message and message recipient, and then sends the message approriately
 */
abstract class recipient_send_factory {

    public $message;
    public $recipient;
    public $course;
    public $all_profile_fields;
    public $selected_profile_fields;
    public $message_params;
    public $alternate_email;

    public function __construct($message, $recipient, $allprofilefields, $selectedprofilefields) {
        $this->message = $message;
        $this->recipient = $recipient;
        $this->all_profile_fields = $allprofilefields;
        $this->selected_profile_fields = $selectedprofilefields;
        $this->message_params = (object) [];
        $this->alternate_email = null;
        $this->set_global_params();
        $this->set_global_computed_params();
        $this->set_factory_params();
        $this->set_factory_computed_params();
    }

    // Return email_recipient_send_factory OR message_recipient_send_factory.
    public static function make($message, $recipient, $allprofilefields, $selectedprofilefields) {
        // Get the factory class name to return (based on message message_type).
        $messagefactoryclass = self::get_message_factory_class_name($message);

        // Return the constructed factory.
        return new $messagefactoryclass($message, $recipient, $allprofilefields, $selectedprofilefields);
    }

    /**
     * Handles post successfully-sent tasks for a recipient
     *
     * @param  int  $moodlemessageid  optional, defaults to 0 (for emails)
     * @return void
     */
    public function handle_recipient_post_send($moodlemessageid = 0) {
        if ($this->message->get('send_to_mentors')) {
            $this->send_to_mentor_users();

            $this->send_to_mentor_profile_emails();
        }

        $this->recipient->mark_as_sent_to($moodlemessageid);
    }

    private static function get_message_factory_class_name($message) {
        $classname = $message->get('message_type') . '_recipient_send_factory';

        return 'block_quickmail\messenger\factories\course_recipient_send\\' . $classname;
    }

    private function set_global_params() {
        $this->message_params->userto = $this->recipient->get_user();
        $this->message_params->userfrom = $this->message->get_user();
        $this->course = $this->message->get_course();
    }

    private function set_global_computed_params() {
        // Optional message prepend + message subject.
        // Very short one-line subject.
        $this->message_params->subject = subject_prepender::format_course_subject(
            $this->course,
            $this->message->get('subject')
        );

        // Format the message body to include any injected user/course data.
        $formattedbody = message_body_constructor::get_formatted_body($this->message, $this->message_params->userto, $this->course);

        // Append a signature to the formatted body, if appropriate.
        $formattedbody = signature_appender::append_user_signature_to_body(
            $formattedbody,
            $this->message_params->userfrom->id,
            $this->message->get('signature_id')
        );

        // Append attachment download links to the formatted body, if any.
        $formattedbody = attachment_appender::add_download_links($this->message, $formattedbody);

        // Course/user formatted message (string format).
        // Raw text.
        $this->message_params->fullmessage = format_text_email($formattedbody, 1); // Hard coded for now, change?

        // Course/user formatted message (html format).
        // Full version (the message processor will choose with one to use).
        $this->message_params->fullmessagehtml = purify_html($formattedbody);
    }

    /**
     * Returns any existing mentors of this recipient
     *
     * @return array
     */
    public function get_recipient_mentors() {
        return user_repo::get_mentors_of_user($this->message_params->userto);
    }

    /**
     * Returns a subject prefix, if any, from the given options. Defaults to empty string.
     *
     * @param  array  $options
     * @return string
     */
    public function get_subject_prefix($options = []) {
        return array_key_exists('subject_prefix', $options)
            ? $options['subject_prefix'] . ' '
            : '';
    }

    /**
     * Returns a message prefix, if any, from the given options. Defaults to empty string.
     *
     * @param  array  $options
     * @return string
     */
    public function get_message_prefix($options = []) {
        return array_key_exists('message_prefix', $options)
            ? $options['message_prefix'] . ' '
            : '';
    }

    /**
     * Sends this formatted message to any existing mentor emails of this recipient user
     * which are configured by specific user profile field data
     *
     * @return void
     */
    private function send_to_mentor_profile_emails() {
        // If block is not configured to support mentor email profile fields, do nothing.
        if (!$this->selected_profile_fields) {
            return;
        }

        // Send each formatted email.
        foreach ($this->get_recipient_mentor_field_email_array() as $fieldshortname => $email) {
            $emailer = new block_quickmail_emailer(
                $this->message_params->userfrom,
                $this->message_params->subject,
                $this->get_profile_mentor_body_prefix($fieldshortname) . $this->message_params->fullmessagehtml
            );
            $emailer->to_email($email);
            $emailer->send();
        }
    }

    /**
     * Returns an array of all valid user profile field mentor emails
     *
     * @return array  [field shortname => email]
     */
    private function get_recipient_mentor_field_email_array() {
        // Set recipient user.
        $recipientuser = $this->message_params->userto;

        // Load this user's profile fields.
        profile_load_custom_fields($recipientuser);

        // Return all valid, assigned profile field emails.
        return array_filter(array_intersect_key($recipientuser->profile,
                                                array_flip($this->selected_profile_fields)),
                                                function ($value) {
                                                    return filter_var($value, FILTER_VALIDATE_EMAIL);
                                                });
    }

    /**
     * Returns a descriptive string to be prepended to outbound messages sent to profile field mentors
     *
     * @param  string  $profilefieldshortname
     * @return string
     */
    private function get_profile_mentor_body_prefix($profilefieldshortname) {
        return block_quickmail_string::get('profile_mentor_copy_message_prefix', $this->all_profile_fields[$profilefieldshortname]);
    }

}

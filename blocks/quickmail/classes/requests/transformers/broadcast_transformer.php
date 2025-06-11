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

namespace block_quickmail\requests\transformers;

defined('MOODLE_INTERNAL') || die();

use block_quickmail_config;

class broadcast_transformer extends transformer {

    public function transform_form_data() {
        $this->transformed_data->subject = (string) $this->form_data->subject;
        $this->transformed_data->message = (string) $this->form_data->message_editor['text'];
        $this->transformed_data->additional_emails = $this->get_transformed_additional_emails();
        $this->transformed_data->signature_id = $this->get_transformed_signature_id();
        $this->transformed_data->message_type = $this->get_transformed_message_type();
        $this->transformed_data->receipt = (bool) $this->form_data->receipt;
        $this->transformed_data->mentor_copy = $this->get_transformed_mentor_copy();
        $this->transformed_data->alternate_email_id = 0; // Assuming that alternate emails are not allowed for broadcast messages.
        $this->transformed_data->to_send_at = $this->get_transformed_to_send_at();
        $this->transformed_data->attachments_draftitem_id = $this->get_transformed_attachments_draftitem_id();
        $this->transformed_data->no_reply = $this->get_transformed_no_reply();
    }

    /**
     * Returns a sanitized array of additional emails from the form post data
     *
     * @return array
     */
    public function get_transformed_additional_emails() {
        $additionalemails = $this->form_data->additional_emails;

        $emails = !empty($additionalemails) ? array_unique(explode(',', $additionalemails)) : [];

        // Eliminate any white space.
        $emails = array_map(function($email) {
            return trim($email);
        }, $emails);

        // Return all valid emails.
        return array_filter($emails, function($email) {
            return strlen($email) > 0;
        });
    }

    /**
     * Returns a sanitized signature id from the form post data
     *
     * @return int
     */
    public function get_transformed_signature_id() {
        return ! $this->form_data->signature_id
            ? 0
            : (int) $this->form_data->signature_id;
    }

    /**
     * Returns a sanitized message type from the form post data
     *
     * @return string
     */
    public function get_transformed_message_type() {
        return ! empty($this->form_data->message_type)
            ? (string) $this->form_data->message_type
            : block_quickmail_config::get('default_message_type');
    }

    /**
     * Returns whether or not this composed message should be sent to mentors based on
     * input and system configuration
     *
     * @return bool
     */
    public function get_transformed_mentor_copy() {
        return block_quickmail_config::block('allow_mentor_copy') == 2
            ? true
            : (bool) $this->form_data->mentor_copy;
    }

    /**
     * Returns a sanitized to send at timestamp from the form post data
     *
     * @return int
     */
    public function get_transformed_to_send_at() {
        return ! $this->form_data->to_send_at
            ? 0
            : (int) $this->form_data->to_send_at;
    }

    /**
     * Returns ...
     *
     * @return int
     */
    public function get_transformed_attachments_draftitem_id() {
        return ! $this->form_data->attachments ? 0 : (int) $this->form_data->attachments;
    }

    /**
     * Returns a sanitized no_reply value from the form post data
     *
     * @return bool
     */
    public function get_transformed_no_reply() {
        return true;
    }

}

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

// Compose form submission helpers.
trait submits_compose_message_form {

    public function get_compose_message_form_submission(array $recipients = [],
            $messagetype = 'email',
            array $overrideparams = []) {
        $params = $this->get_compose_message_form_submission_params($overrideparams);

        list($includedentityids, $excludedentityids) = $this->get_recipients_array($recipients);

        $formdata = (object)[];
        // Default - '0' (user email), '-1' (system no reply), else alt id.
        $formdata->from_email_id = $params['from_email_id'];
        $formdata->included_entity_ids = $includedentityids;
        $formdata->excluded_entity_ids = $excludedentityids;
        // Default - 'this is the subject'.
        $formdata->subject = $params['subject'];
        // Default - ''.
        $formdata->additional_emails = $params['additional_emails'];
        $formdata->message_editor = [
            // Default - 'this is a very important message body'.
            'text' => $params['body'],
            'format' => '1',
            'itemid' => 881830772
        ];
        $formdata->attachments = 0;
        // Default - '0'.
        $formdata->signature_id = $params['signature_id'];
        $formdata->message_type = $messagetype;
        // Default - 0.
        $formdata->to_send_at = $params['to_send_at'];
        // Default - '0'.
        $formdata->receipt = $params['receipt'];
        // Default - '0'.
        $formdata->mentor_copy = $params['mentor_copy'];
        $formdata->send = 'Send Message';

        return $formdata;
    }

    // Recipients.
        // Included.
            // Roles.
            // Groups.
            // Users.
        // Excluded.
            // Roles.
            // Groups.
            // Users.

    private function get_recipients_array($recipients) {
        $includedentityids = [];
        $excludedentityids = [];

        foreach (['included', 'excluded'] as $inclusiontype) {
            if (array_key_exists($inclusiontype, $recipients)) {
                foreach (['role', 'group', 'user'] as $recipienttype) {
                    if (array_key_exists($recipienttype, $recipients[$inclusiontype])) {
                        foreach ($recipients[$inclusiontype][$recipienttype] as $id) {
                            // Segun Babalola, 2020-10-30.
                            // Not sure how this ever worked with undescores.
                            // Recipient IDs will never have been captured.
                            $containername = $inclusiontype . 'entityids';
                            $containername[] = $recipienttype . '_' . $id;
                        }
                    }
                }
            }
        }

        return [$includedentityids, $excludedentityids];
    }

    public function get_compose_message_form_submission_params(array $overrideparams) {
        $params = [];

        $params['from_email_id'] = array_key_exists('from_email_id',
                                       $overrideparams) ? $overrideparams['from_email_id'] : '0';
        $params['additional_emails'] = array_key_exists('additional_emails',
                                           $overrideparams) ? $overrideparams['additional_emails'] : '';
        $params['subject'] = array_key_exists('subject',
                                 $overrideparams) ? $overrideparams['subject'] : 'this is the subject';
        $params['body'] = array_key_exists('body',
                              $overrideparams) ? $overrideparams['body'] : 'this is a very important message body';
        $params['signature_id'] = array_key_exists('signature_id', $overrideparams) ? $overrideparams['signature_id'] : '0';
        $params['to_send_at'] = array_key_exists('to_send_at', $overrideparams) ? $overrideparams['to_send_at'] : 0;
        $params['receipt'] = array_key_exists('receipt', $overrideparams) ? $overrideparams['receipt'] : '0';
        $params['mentor_copy'] = array_key_exists('mentor_copy', $overrideparams) ? $overrideparams['mentor_copy'] : '0';

        return $params;
    }

}

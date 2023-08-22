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

namespace factor_sms;

use moodle_url;
use tool_mfa\local\factor\object_factor_base;

/**
 * SMS Factor class.
 *
 * @package     factor_sms
 * @subpackage  tool_mfa
 * @author      Peter Burnett <peterburnett@catalyst-au.net>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class factor extends object_factor_base {

    /**
     * SMS Factor implementation.
     *
     * @param \MoodleQuickForm $mform
     * @return object $mform
     */
    public function login_form_definition($mform) {
        $mform->addElement(new \tool_mfa\local\form\verification_field());
        $mform->setType('verificationcode', PARAM_ALPHANUM);
        return $mform;
    }

    /**
     * SMS Factor implementation.
     *
     * @param \MoodleQuickForm $mform Form to inject global elements into.
     * @return object $mform
     */
    public function login_form_definition_after_data($mform) {
        $instanceid = $this->generate_and_sms_code();
        $mform = $this->add_redacted_sent_message($mform, $instanceid);
        // Disable the form check prompt.
        $mform->disable_form_change_checker();
        return $mform;
    }

    /**
     * SMS Factor implementation.
     *
     * @param array $data
     * @return array
     */
    public function login_form_validation($data) {
        $return = [];

        if (!$this->check_verification_code($data['verificationcode'])) {
            $return['verificationcode'] = get_string('wrongcode', 'factor_sms');
        }

        return $return;
    }

    /**
     * Gets the string for setup button on preferences page.
     */
    public function get_setup_string() {
        return get_string('setupfactor', 'factor_sms');
    }

    /**
     * SMS Factor implementation.
     *
     * @param \MoodleQuickForm $mform
     * @return object $mform
     */
    public function setup_factor_form_definition($mform) {
        global $SESSION, $USER, $OUTPUT;

        $mform->addElement('html', $OUTPUT->heading(get_string('setupfactor', 'factor_sms'), 2));

        if (empty($USER->phone2) && empty($SESSION->tool_mfa_sms_number)) {
            $mform->addElement('hidden', 'verificationcode', 0);
            $mform->setType('verificationcode', PARAM_ALPHANUM);

            // Add field for phone number setup.
            $mform->addElement('text', 'phonenumber', get_string('addnumber', 'factor_sms'),
                [
                    'placeholder' => get_string('phoneplaceholder', 'factor_sms'),
                    'autocomplete' => 'tel',
                    'inputmode' => 'tel',
                ]);
            $mform->setType('phonenumber', PARAM_TEXT);
            $mform->addElement('html', \html_writer::tag('p', get_string('phonehelp', 'factor_sms')));
        }
    }

    /**
     * SMS Factor implementation.
     *
     * @param \MoodleQuickForm $mform
     * @return object $mform
     */
    public function setup_factor_form_definition_after_data($mform) {
        global $SESSION, $USER;

        // Nothing if they dont have a number added.
        if (empty($USER->phone2) && empty($SESSION->tool_mfa_sms_number)) {
            return $mform;
        }

        $mform->addElement(new \tool_mfa\local\form\verification_field());
        $mform->setType('verificationcode', PARAM_ALPHANUM);

        // Decide on number from session or profile.
        if (!empty($SESSION->tool_mfa_sms_number)) {
            $number = $SESSION->tool_mfa_sms_number;
        } else {
            $number = $USER->phone2;
        }

        $duration = get_config('factor_sms', 'duration');
        $code = $this->secretmanager->create_secret($duration, true);
        if (!empty($code)) {
            $this->sms_verification_code($code, $number);
        }

        // Tell users it was sent.
        $mform = $this->add_redacted_sent_message($mform, null, $number);

        // Disable the form check prompt.
        $mform->disable_form_change_checker();
    }

    /**
     * SMS Factor implementation.
     *
     * @param array $data
     * @return array
     */
    public function setup_factor_form_validation($data) {
        global $SESSION, $USER;

        // No validation on raw number.
        if (empty($USER->phone2) && empty($SESSION->tool_mfa_sms_number)) {
            return [];
        }

        $errors = [];
        $result = $this->secretmanager->validate_secret($data['verificationcode']);
        if ($result !== $this->secretmanager::VALID) {
            $errors['verificationcode'] = get_string('wrongcode', 'factor_sms');
        }

        return $errors;
    }

    /**
     * Adds an instance of the factor for a user, from form data.
     *
     * @param array $data
     * @return stdClass the factor record, or null.
     */
    public function setup_user_factor($data) {
        global $DB, $SESSION, $USER;

        // Handle phone number submission.
        if (empty($USER->phone2) && empty($SESSION->tool_mfa_sms_number)) {
            $SESSION->tool_mfa_sms_number = $data->phonenumber;

            $addurl = new \moodle_url('/admin/tool/mfa/action.php', [
                'action' => 'setup',
                'factor' => 'sms',
            ]);
            redirect($addurl);
        }

        // Decide on number to use for instance.
        if (empty($SESSION->tool_mfa_sms_number)) {
            // This came from the profile.
            $label = $USER->phone2;
        } else {
            $label = $SESSION->tool_mfa_sms_number;
        }

        // If the user somehow gets here through form resubmission.
        // We dont want two phones active.
        if ($DB->record_exists('tool_mfa', ['userid' => $USER->id, 'factor' => $this->name, 'revoked' => 0])) {
            return null;
        }

        $row = new \stdClass();
        $row->userid = $USER->id;
        $row->factor = $this->name;
        $row->secret = '';
        $row->label = $label;
        $row->timecreated = time();
        $row->createdfromip = $USER->lastip;
        $row->timemodified = time();
        $row->lastverified = time();
        $row->revoked = 0;

        $id = $DB->insert_record('tool_mfa', $row);
        $record = $DB->get_record('tool_mfa', ['id' => $id]);
        $this->create_event_after_factor_setup($USER);

        // Remove session phone number.
        unset($SESSION->tool_mfa_sms_number);

        return $record;
    }

    /**
     * Adds a redacted sent message to the mform with the users number.
     *
     * @param stdClass $mform the form to modify.
     * @param int|null $instanceid the instance to take the number from.
     * @param string|null $number the number to display if no instance given.
     */
    private function add_redacted_sent_message($mform, $instanceid = null, $number = null) {
        global $DB, $USER;

        if (!empty($instanceid)) {
            $phonenumber = $DB->get_field('tool_mfa', 'label', ['id' => $instanceid]);
        } else {
            $phonenumber = !empty($number) ? $number : $USER->phone2;
        }

        $redacted = helper::redact_phonenumber($phonenumber);

        $mform->addElement('html', \html_writer::tag('p', get_string('smssent', 'factor_sms', $redacted) . '<br>'));
        return $mform;
    }

    /**
     * SMS Factor implementation.
     *
     * @param stdClass $user the user to check against.
     * @return array
     */
    public function get_all_user_factors($user) {
        global $DB;

        $sql = 'SELECT *
                  FROM {tool_mfa}
                 WHERE userid = ?
                   AND factor = ?
                   AND label IS NOT NULL
                   AND revoked = 0';

        return $DB->get_records_sql($sql, [$user->id, $this->name]);
    }

    /**
     * SMS Factor implementation.
     *
     * {@inheritDoc}
     */
    public function is_enabled() {
        if (empty(get_config('factor_sms', 'gateway'))) {
            return false;
        }

        $class = '\factor_sms\local\smsgateway\\' . get_config('factor_sms', 'gateway');
        if (!call_user_func($class . '::is_gateway_enabled')) {
            return false;
        }
        return parent::is_enabled();
    }

    /**
     * SMS Factor implementation.
     *
     * {@inheritDoc}
     */
    public function has_input() {
        return true;
    }

    /**
     * SMS Factor implementation.
     *
     * {@inheritDoc}
     */
    public function has_setup() {
        return true;
    }

    /**
     * SMS Factor implementation
     *
     * {@inheritDoc}
     */
    public function show_setup_buttons() {
        global $DB, $USER;
        // If there is already a factor setup, don't allow multiple (for now).
        $sql = 'SELECT *
                  FROM {tool_mfa}
                 WHERE userid = ?
                   AND factor = ?
                   AND secret = ?
                   AND revoked = 0';

        $record = $DB->get_record_sql($sql, [$USER->id, $this->name, '']);
        return !empty($record) ? false : true;
    }

    /**
     * SMS Factor implementation.
     *
     * {@inheritDoc}
     */
    public function has_revoke() {
        return true;
    }

    /**
     * Generates and sms' the code for login to the user, stores codes in DB.
     *
     * @return int the instance ID being used.
     */
    private function generate_and_sms_code() {
        global $DB, $USER;

        $duration = get_config('factor_sms', 'duration');
        $secret = $this->secretmanager->create_secret($duration, false);
        $instance = $DB->get_record('tool_mfa', ['factor' => $this->name, 'userid' => $USER->id, 'revoked' => 0]);

        // There is a new code that needs to be sent.
        if (!empty($secret)) {
            // Grab the singleton SMS record.
            $this->sms_verification_code($secret, $instance->label);
        }
        return $instance->id;
    }

    /**
     * This function sends an SMS code to the user based on the phonenumber provided.
     *
     * @param int $secret the secret to send.
     * @param int|null $phonenumber the phonenumber to send the verification code to.
     * @return void
     */
    private function sms_verification_code($secret, $phonenumber) {
        global $CFG, $SITE;

        // Here we should get the information, then construct the message.
        $url = new moodle_url($CFG->wwwroot);
        $content = [
            'fullname' => $SITE->fullname,
            'shortname' => $SITE->shortname,
            'supportname' => $CFG->supportname,
            'url' => $url->get_host(),
            'code' => $secret,
        ];
        $message = get_string('smsstring', 'factor_sms', $content);

        $class = '\factor_sms\local\smsgateway\\' . get_config('factor_sms', 'gateway');
        $gateway = new $class();
        $gateway->send_sms_message($message, $phonenumber);
    }

    /**
     * Verifies entered code against stored DB record.
     *
     * @param string $enteredcode
     * @return bool
     */
    private function check_verification_code($enteredcode) {
        $state = $this->secretmanager->validate_secret($enteredcode);
        if ($state === \tool_mfa\local\secret_manager::VALID) {
            return true;
        }
        return false;
    }

    /**
     * SMS factor implementation.
     *
     * @param \stdClass $user
     */
    public function possible_states($user) {
        return [
            \tool_mfa\plugininfo\factor::STATE_PASS,
            \tool_mfa\plugininfo\factor::STATE_NEUTRAL,
            \tool_mfa\plugininfo\factor::STATE_FAIL,
            \tool_mfa\plugininfo\factor::STATE_UNKNOWN,
        ];
    }
}

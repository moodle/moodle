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
use stdClass;
use tool_mfa\local\factor\object_factor_base;
use tool_mfa\local\secret_manager;

/**
 * SMS Factor implementation.
 *
 * @package     factor_sms
 * @subpackage  tool_mfa
 * @author      Peter Burnett <peterburnett@catalyst-au.net>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class factor extends object_factor_base {

    /** @var string Factor icon */
    protected $icon = 'fa-commenting-o';

    /**
     * Defines login form definition page for SMS Factor.
     *
     * @param \MoodleQuickForm $mform
     * @return \MoodleQuickForm $mform
     */
    public function login_form_definition(\MoodleQuickForm $mform): \MoodleQuickForm {
        $mform->addElement(new \tool_mfa\local\form\verification_field());
        $mform->setType('verificationcode', PARAM_ALPHANUM);
        return $mform;
    }

    /**
     * Defines login form definition page after form data has been set.
     *
     * @param \MoodleQuickForm $mform Form to inject global elements into.
     * @return \MoodleQuickForm $mform
     */
    public function login_form_definition_after_data(\MoodleQuickForm $mform): \MoodleQuickForm {
        $this->generate_and_sms_code();

        // Disable the form check prompt.
        $mform->disable_form_change_checker();
        return $mform;
    }

    /**
     * Implements login form validation for SMS Factor.
     *
     * @param array $data
     * @return array
     */
    public function login_form_validation(array $data): array {
        $return = [];

        if (!$this->check_verification_code($data['verificationcode'])) {
            $return['verificationcode'] = get_string('error:wrongverification', 'factor_sms');
        }

        return $return;
    }

    /**
     * Gets the string for setup button on preferences page.
     *
     * @return string
     */
    public function get_setup_string(): string {
        return get_string('setupfactorbutton', 'factor_sms');
    }

    /**
     * Gets the string for manage button on preferences page.
     *
     * @return string
     */
    public function get_manage_string(): string {
        return get_string('managefactorbutton', 'factor_sms');
    }

    /**
     * Defines setup_factor form definition page for SMS Factor.
     *
     * @param \MoodleQuickForm $mform
     * @return \MoodleQuickForm $mform
     */
    public function setup_factor_form_definition(\MoodleQuickForm $mform): \MoodleQuickForm {
        global $OUTPUT, $USER, $DB;

        if (!empty(
            $phonenumber = $DB->get_field('tool_mfa', 'label', ['factor' => $this->name, 'userid' => $USER->id, 'revoked' => 0])
        )) {
            redirect(
                new \moodle_url('/admin/tool/mfa/user_preferences.php'),
                get_string('factorsetup', 'tool_mfa', $phonenumber),
                null,
                \core\output\notification::NOTIFY_SUCCESS);
        }

        $mform->addElement('html', $OUTPUT->heading(get_string('setupfactor', 'factor_sms'), 2));

        if (empty($this->get_phonenumber())) {
            $mform->addElement('hidden', 'verificationcode', 0);
            $mform->setType('verificationcode', PARAM_ALPHANUM);

            // Add field for phone number setup.
            $mform->addElement('text', 'phonenumber', get_string('addnumber', 'factor_sms'),
                [
                    'autocomplete' => 'tel',
                    'inputmode' => 'tel',
                ]);
            $mform->setType('phonenumber', PARAM_TEXT);

            // HTML to display a message about the phone number.
            $message = \html_writer::tag('div', '', ['class' => 'col-md-3']);
            $message .= \html_writer::tag(
                'div', \html_writer::tag('p', get_string('phonehelp', 'factor_sms')), ['class' => 'col-md-9']);
            $mform->addElement('html', \html_writer::tag('div', $message, ['class' => 'row']));
        }

        return $mform;
    }

    /**
     * Defines setup_factor form definition page after form data has been set.
     *
     * @param \MoodleQuickForm $mform
     * @return \MoodleQuickForm $mform
     */
    public function setup_factor_form_definition_after_data(\MoodleQuickForm $mform): \MoodleQuickForm {
        global $OUTPUT;

        $phonenumber = $this->get_phonenumber();
        if (empty($phonenumber)) {
            return $mform;
        }

        $duration = get_config('factor_sms', 'duration');
        $code = $this->secretmanager->create_secret($duration, true);
        if (!empty($code)) {
            $this->sms_verification_code($code, $phonenumber);
        }
        $message = get_string('logindesc', 'factor_sms', '<b>' . $phonenumber . '</b><br/>');
        $message .= get_string('editphonenumberinfo', 'factor_sms');
        $mform->addElement('html', \html_writer::tag('p', $OUTPUT->notification($message, 'success')));

        $mform->addElement(new \tool_mfa\local\form\verification_field());
        $mform->setType('verificationcode', PARAM_ALPHANUM);

        $editphonenumber = \html_writer::link(
            new \moodle_url('/admin/tool/mfa/factor/sms/editphonenumber.php', ['sesskey' => sesskey()]),
            get_string('editphonenumber', 'factor_sms'),
            ['class' => 'btn btn-secondary', 'type' => 'button']);

        $mform->addElement('html', \html_writer::tag('div', $editphonenumber, ['class' => 'float-sm-start col-md-4']));

        // Disable the form check prompt.
        $mform->disable_form_change_checker();

        return $mform;
    }

    /**
     * Returns the phone number from the current session or from the user profile data.
     * @return string|null
     */
    private function get_phonenumber(): ?string {
        global $SESSION, $USER, $DB;

        if (!empty($SESSION->tool_mfa_sms_number)) {
            return $SESSION->tool_mfa_sms_number;
        }
        $phonenumber = $DB->get_field('tool_mfa', 'label', ['factor' => $this->name, 'userid' => $USER->id, 'revoked' => 0]);
        if (!empty($phonenumber)) {
            return $phonenumber;
        }

        return null;
    }

    /**
     * Returns an array of errors, where array key = field id and array value = error text.
     *
     * @param array $data
     * @return array
     */
    public function setup_factor_form_validation(array $data): array {
        $errors = [];

        // Phone number validation.
        if (!empty($data["phonenumber"]) && empty(helper::is_valid_phonenumber($data["phonenumber"]))) {
            $errors['phonenumber'] = get_string('error:wrongphonenumber', 'factor_sms');

        } else if (!empty($this->get_phonenumber())) {
            // Code validation.
            if (empty($data["verificationcode"])) {
                $errors['verificationcode'] = get_string('error:emptyverification', 'factor_sms');
            } else if ($this->secretmanager->validate_secret($data['verificationcode']) !== $this->secretmanager::VALID) {
                $errors['verificationcode'] = get_string('error:wrongverification', 'factor_sms');
            }
        }

        return $errors;
    }

    /**
     * Reset values of the session data of the given factor.
     *
     * @param int $factorid
     * @return void
     */
    public function setup_factor_form_is_cancelled(int $factorid): void {
        global $SESSION;
        if (!empty($SESSION->tool_mfa_sms_number)) {
            unset($SESSION->tool_mfa_sms_number);
        }
        // Clean temp secrets code.
        $secretmanager = new secret_manager('sms');
        $secretmanager->cleanup_temp_secrets();
    }

    /**
     * Setup submit button string in given factor
     *
     * @return string|null
     */
    public function setup_factor_form_submit_button_string(): ?string {
        global $SESSION;
        if (!empty($SESSION->tool_mfa_sms_number)) {
            return get_string('setupsubmitcode', 'factor_sms');
        }
        return get_string('setupsubmitphone', 'factor_sms');
    }

    /**
     * Adds an instance of the factor for a user, from form data.
     *
     * @param stdClass $data
     * @return stdClass|null the factor record, or null.
     */
    public function setup_user_factor(stdClass $data): ?stdClass {
        global $DB, $SESSION, $USER;

        // Handle phone number submission.
        if (empty($SESSION->tool_mfa_sms_number)) {
            $SESSION->tool_mfa_sms_number = !empty($data->phonenumber) ? $data->phonenumber : '';

            $addurl = new \moodle_url('/admin/tool/mfa/action.php', [
                'action' => 'setup',
                'factor' => 'sms',
            ]);
            redirect($addurl);
        }

        // If the user somehow gets here through form resubmission.
        // We dont want two phones active.
        if ($DB->record_exists('tool_mfa', ['userid' => $USER->id, 'factor' => $this->name, 'revoked' => 0])) {
            return null;
        }

        $time = time();
        $label = $this->get_phonenumber();

        $row = new \stdClass();
        $row->userid = $USER->id;
        $row->factor = $this->name;
        $row->secret = '';
        $row->label = $label;
        $row->timecreated = $time;
        $row->createdfromip = $USER->lastip;
        $row->timemodified = $time;
        $row->lastverified = $time;
        $row->revoked = 0;

        $id = $DB->insert_record('tool_mfa', $row);
        $record = $DB->get_record('tool_mfa', ['id' => $id]);
        $this->create_event_after_factor_setup($USER);

        // Remove session phone number.
        unset($SESSION->tool_mfa_sms_number);

        return $record;
    }

    /**
     * Returns an array of all user factors of given type.
     *
     * @param stdClass $user the user to check against.
     * @return array
     */
    public function get_all_user_factors(stdClass $user): array {
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
     * Returns the information about factor availability.
     *
     * @return bool
     */
    public function is_enabled(): bool {
        return parent::is_enabled();
    }

    /**
     * Decides if a factor requires input from the user to verify.
     *
     * @return bool
     */
    public function has_input(): bool {
        return true;
    }

    /**
     * Decides if factor needs to be setup by user and has setup_form.
     *
     * @return bool
     */
    public function has_setup(): bool {
        return true;
    }

    /**
     * Decides if the setup buttons should be shown on the preferences page.
     *
     * @return bool
     */
    public function show_setup_buttons(): bool {
        return true;
    }

    /**
     * Returns true if factor class has factor records that might be revoked.
     * It means that user can revoke factor record from their profile.
     *
     * @return bool
     */
    public function has_revoke(): bool {
        return true;
    }

    /**
     * Generates and sms' the code for login to the user, stores codes in DB.
     *
     * @return int|null the instance ID being used.
     */
    private function generate_and_sms_code(): ?int {
        global $DB, $USER;

        $duration = get_config('factor_sms', 'duration');
        $instance = $DB->get_record('tool_mfa', ['factor' => $this->name, 'userid' => $USER->id, 'revoked' => 0]);
        if (empty($instance)) {
            return null;
        }
        $secret = $this->secretmanager->create_secret($duration, false);
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
     * @param string|null $phonenumber the phonenumber to send the verification code to.
     * @return void
     */
    private function sms_verification_code(int $secret, ?string $phonenumber): void {
        global $CFG, $SITE;

        // Here we should get the information, then construct the message.
        $url = new moodle_url($CFG->wwwroot);
        $content = [
            'fullname' => $SITE->fullname,
            'url' => $url->get_host(),
            'code' => $secret,
        ];
        $message = get_string('smsstring', 'factor_sms', $content);

        $manager = \core\di::get(\core_sms\manager::class);
        $manager->send(
            recipientnumber: $phonenumber,
            content: $message,
            component: 'factor_sms',
            messagetype: 'mfa',
            recipientuserid: null,
            issensitive: true,
            async: false,
            gatewayid: get_config('factor_sms', 'smsgateway'),
        );
    }

    /**
     * Verifies entered code against stored DB record.
     *
     * @param string $enteredcode
     * @return bool
     */
    private function check_verification_code(string $enteredcode): bool {
        return $this->secretmanager->validate_secret($enteredcode) === secret_manager::VALID;
    }

    /**
     * Returns all possible states for a user.
     *
     * @param \stdClass $user
     */
    public function possible_states(\stdClass $user): array {
        return [
            \tool_mfa\plugininfo\factor::STATE_PASS,
            \tool_mfa\plugininfo\factor::STATE_NEUTRAL,
            \tool_mfa\plugininfo\factor::STATE_FAIL,
            \tool_mfa\plugininfo\factor::STATE_UNKNOWN,
        ];
    }

    /**
     * Get the login description associated with this factor.
     * Override for factors that have a user input.
     *
     * @return string The login option.
     */
    public function get_login_desc(): string {

        $phonenumber = $this->get_phonenumber();

        if (empty($phonenumber)) {
            return get_string('errorsmssent', 'factor_sms');
        }

        return get_string('logindesc', 'factor_' . $this->name, $phonenumber);
    }
}

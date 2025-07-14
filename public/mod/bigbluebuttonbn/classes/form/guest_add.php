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
namespace mod_bigbluebuttonbn\form;

use context;
use core_form\dynamic_form;
use mod_bigbluebuttonbn\instance;
use mod_bigbluebuttonbn\local\exceptions\bigbluebutton_exception;
use mod_bigbluebuttonbn\task\send_guest_emails;
use moodle_exception;
use moodle_url;
use MoodleQuickForm;

/**
 * Popup form to add new guests to a meeting and show/copy credential to access the guest login page.
 *
 * @package    mod_bigbluebuttonbn
 * @copyright  2022 onwards, Blindside Networks Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Laurent David  (laurent [at] call-learning [dt] fr)
 */
class guest_add extends dynamic_form {

    /**
     * Max length for credential and url fields.
     */
    const MAX_INPUT_LENGTH = 35;

    /**
     * Process the form submission, used if form was submitted via AJAX.
     *
     * @return array
     */
    public function process_dynamic_submission(): array {
        global $USER;
        $data = $this->get_data();
        $allmails = [];
        if (!empty($data->emails)) {
            $emails = explode(',', $data->emails);
            foreach ($emails as $email) {
                $email = trim($email);
                if (validate_email($email)) {
                    $allmails[] = $email;
                }
            }
            $adhoctask = new send_guest_emails();
            $adhoctask->set_custom_data(
                [
                    'emails' => $allmails,
                    'useridfrom' => $USER->id
                ]
            );
            $adhoctask->set_instance_id($data->id);
            \core\task\manager::queue_adhoc_task($adhoctask);
        }
        return [
            'result' => true,
            'emails' => join(', ', $allmails),
            'emailcount' => count($allmails),
            'errors' => ''
        ];
    }

    /**
     * Perform some validation.
     *
     * @param array $formdata
     * @param array $files
     * @return array
     */
    public function validation($formdata, $files): array {
        $errors = [];
        $emailserrors = [];
        if (!empty($formdata['emails'])) {
            $emails = explode(',', $formdata['emails']);
            foreach ($emails as $email) {
                $email = trim($email);
                if (!validate_email($email)) {
                    $emailserrors[] .= get_string('guestaccess_emails_invalidemail', 'mod_bigbluebuttonbn', $email);
                }
            }
        }
        if (!empty($emailserrors)) {
            $errors['emails'] = \html_writer::alist($emailserrors);
        }
        return $errors;
    }

    /**
     * Load in existing data as form defaults (not applicable).
     *
     * @return void
     */
    public function set_data_for_dynamic_submission(): void {
        $instance = $this->get_instance_from_params();
        $data = [
            'id' => $instance->get_instance_id(),
            'groupid' => $instance->get_group_id(),
            'guestjoinurl' => $instance->get_guest_access_url(),
            'guestpassword' => $instance->get_guest_access_password(),
        ];
        $this->set_data($data);

    }

    /**
     * Get BigblueButton instance from context params
     *
     * @return instance
     * @throws moodle_exception
     */
    protected function get_instance_from_params(): instance {
        $bbid = $this->optional_param('id', null, PARAM_INT);
        $groupid = $this->optional_param('groupid', null, PARAM_INT);
        if (empty($bbid)) {
            throw new moodle_exception('guestaccess_add_no_id', 'mod_bigbluebuttonbn');
        }
        $instance = instance::get_from_instanceid($bbid);
        if ($groupid) {
            $instance->set_group_id($groupid);
        }
        return $instance;
    }

    /**
     * Form definition
     */
    protected function definition() {
        self::add_meeting_links_elements($this->_form);
        $mform = $this->_form;
        $mform->addElement('text', 'emails',
            get_string('guestaccess_emails', 'mod_bigbluebuttonbn'),
        );
        $mform->addHelpButton('emails', 'guestaccess_emails', 'mod_bigbluebuttonbn');
        $mform->setDefault('emails', '');
        $mform->setType('emails', PARAM_RAW);
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'groupid');
        $mform->setType('groupid', PARAM_INT);
    }

    /**
     * Add meeting links element. Helper for this form and the mod_form (module form)
     *
     * @param MoodleQuickForm $mform
     * @return void
     */
    public static function add_meeting_links_elements(MoodleQuickForm &$mform): void {
        global $CFG;
        MoodleQuickForm::registerElementType('text_with_copy',
            "$CFG->dirroot/mod/bigbluebuttonbn/classes/form/text_with_copy_element.php",
            text_with_copy_element::class);
        $mform->addElement('text_with_copy', 'guestjoinurl',
            get_string('guestaccess_meeting_link', 'mod_bigbluebuttonbn'),
            [
                'copylabel' => get_string('guestaccess_copy_link', 'mod_bigbluebuttonbn'),
                'size' => self::MAX_INPUT_LENGTH,
                'readonly' => 'readonly'
            ]
        );
        $mform->setType('guestjoinurl', PARAM_URL);
        $mform->addElement('text_with_copy', 'guestpassword',
            get_string('guestaccess_meeting_password', 'mod_bigbluebuttonbn'),
            [
                'copylabel' => get_string('guestaccess_copy_password', 'mod_bigbluebuttonbn'),
                'readonly' => 'readonly',
                'size' => self::MAX_INPUT_LENGTH,
            ]
        );
        $mform->setType('guestpassword', PARAM_RAW);
    }

    /**
     * Check if current user has access to this form, otherwise throw exception.
     *
     * @return void
     * @throws moodle_exception
     */
    protected function check_access_for_dynamic_submission(): void {
        $context = $this->get_context_for_dynamic_submission();
        $instance = instance::get_from_cmid($context->instanceid);
        if (!$instance->is_moderator()) {
            throw new \restricted_context_exception();
        }
    }

    /**
     * Return form context
     *
     * @return context
     */
    protected function get_context_for_dynamic_submission(): context {
        $instance = $this->get_instance_from_params();
        return $instance->get_context();
    }

    /**
     * Returns url to set in $PAGE->set_url() when form is being rendered or submitted via AJAX.
     *
     * @return moodle_url
     */
    protected function get_page_url_for_dynamic_submission(): moodle_url {
        $context = $this->get_context_for_dynamic_submission();
        return new moodle_url('/mod/bigbluebuttonbn/view.php', ['id' => $context->instanceid]);
    }
}

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

namespace core_admin\form\oauth2\server;

use core\oauth2\server\entity\client_entity;

/**
 * OAuth 2 Client creation form.
 *
 * @package    core_admin
 * @copyright  2026 Mihail Geshoski <mihailgesoski@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class create_client_form extends base_client_form {
    /**
     * Form definition.
     */
    public function definition(): void {
        global $PAGE, $OUTPUT;

        $mform = $this->_form;

        // Add the Name and Description fields.
        $this->add_client_details();

        // Client type radio buttons.
        $typeoptions = [
            client_entity::TYPE_CONFIDENTIAL => [
                'name' => get_string('oauth2server_clienttypeconfidential', 'admin'),
                'desc' => get_string('oauth2server_clienttypeconfidentialdesc', 'admin'),
            ],
            client_entity::TYPE_PUBLIC => [
                'name' => get_string('oauth2server_clienttypepublic', 'admin'),
                'desc' => get_string('oauth2server_clienttypepublicdesc', 'admin'),
            ],
        ];

        $clienttypes = [];

        foreach ($typeoptions as $val => $data) {
            $label = $this->create_label($data['name'], $data['desc']);
            $clienttypes[] = $mform->createElement('radio', 'clienttype', '', $label, $val);
        }

        $mform->addGroup(
            $clienttypes,
            'clienttypegroup',
            get_string('oauth2server_clienttype', 'admin'),
            \html_writer::div('', 'clienttypegroup-separator border-top w-100 my-2'),
            false,
        );

        $mform->setDefault('clienttype', client_entity::TYPE_CONFIDENTIAL);

        // Primary flow checkboxes.
        $flowoptions = [
            'auth_code' => [
                'name' => get_string('oauth2server_clientgranttypeauthcode', 'admin'),
                'desc' => get_string('oauth2server_clientgranttypeauthcodedesc', 'admin'),
            ],
            'client_credentials' => [
                'name' => get_string('oauth2server_clientgranttypeclientcreds', 'admin'),
                'desc' => get_string('oauth2server_clientgranttypeclientcredsdesc', 'admin'),
            ],
        ];

        $flowelements = [];

        foreach ($flowoptions as $key => $data) {
            $label = $this->create_label($data['name'], $data['desc']);
            $flowelements[] = $mform->createElement('checkbox', 'flow_' . $key, '', $label);
        }

        $mform->addGroup(
            $flowelements,
            'primaryflowsgroup',
            get_string('oauth2server_clientprimaryflows', 'admin'),
            \html_writer::div('', 'primaryflowsgroup-separator border-top w-100 my-2'),
            false,
        );

        $mform->setDefault('flow_auth_code', 1);
        $mform->setDefault('flow_client_credentials', 1);

        // Client Credentials is not available to Public clients.
        $mform->hideIf('flow_client_credentials', 'clienttype', 'eq', client_entity::TYPE_PUBLIC);

        // Public clients must use Authorization Code.
        $mform->disabledIf('flow_auth_code', 'clienttype', 'eq', client_entity::TYPE_PUBLIC);

        // Redirect URI fields.
        $this->add_redirect_uri_elements();

        // Hide redirect URI fields when Authorization Code is unchecked.
        $this->hide_redirect_uri_elements_when_auth_code_unchecked();

        // Enable PKCE checkbox.
        $label = $this->create_label(
            get_string('oauth2server_clientenablepkce', 'admin'),
            get_string('oauth2server_clientenablepkcedesc', 'admin'),
        );

        $mform->addElement('checkbox', 'enablepkce', get_string('oauth2server_clientpkce', 'admin'), $label);
        $mform->setDefault('enablepkce', 1);

        // PKCE only applies to Authorization Code.
        $mform->hideIf('enablepkce', 'flow_auth_code', 'notchecked');

        // Public clients cannot change the PKCE setting.
        $mform->disabledIf('enablepkce', 'clienttype', 'eq', client_entity::TYPE_PUBLIC);

        // Warning notice.
        $icon = \html_writer::tag('i', '', ['class' => 'fa fa-exclamation-triangle me-2', 'aria-hidden' => 'true']);

        $mform->addElement(
            'static',
            'warningnotice',
            '',
            $OUTPUT->notification(
                $icon . get_string(
                    'oauth2server_clientcreationwarning',
                    'admin'
                ),
                \core\output\notification::NOTIFY_WARNING,
                false,
            ),
        );

        // Action buttons.
        $this->add_action_buttons(true, get_string('oauth2server_clientcreate', 'admin'));

        // AMD module.
        $PAGE->requires->js_call_amd('core_admin/oauth2/server/client/create_client_form', 'init');
    }

    /**
     * Server-side validation.
     *
     * @param array $data Submitted form data.
     * @param array $files Submitted files.
     * @return array Array of errors indexed by field name.
     */
    public function validation($data, $files): array {
        $errors = parent::validation($data, $files);

        $clienttype = (int) $data['clienttype'];

        $hasauthcode = !empty($data['flow_auth_code']);
        $hasclientcreds = !empty($data['flow_client_credentials']);

        // Confidential clients must select at least one primary flow.
        if ($clienttype === client_entity::TYPE_CONFIDENTIAL && !$hasauthcode && !$hasclientcreds) {
            $errors['primaryflowsgroup'] = get_string(
                'oauth2server_clientmustselectprimaryflow',
                'admin'
            );
        }

        // Redirect URI is required for Public clients or Authorization Code.
        $requiresredirecturi = $clienttype === client_entity::TYPE_PUBLIC || $hasauthcode;

        $errors = array_merge($errors, $this->validate_redirect_uris($data, $requiresredirecturi));

        return $errors;
    }

    /**
     * Hide the redirect URI fields when Authorization Code is not selected.
     *
     * @return void
     */
    private function hide_redirect_uri_elements_when_auth_code_unchecked(): void {
        $mform = $this->_form;

        $i = 0;
        while ($mform->elementExists("redirecturigroup[$i]")) {
            $mform->hideIf("redirecturigroup[$i]", 'flow_auth_code', 'notchecked');
            $i++;
        }

        $mform->hideIf('add_redirecturi_fields', 'flow_auth_code', 'notchecked');

        $mform->hideIf('redirecturis_footer', 'flow_auth_code', 'notchecked');
    }

    /**
     * Create a custom label for a radio/checkbox element.
     *
     * @param string $name Label name.
     * @param string $description Label description.
     * @return string Generated HTML.
     */
    private function create_label(string $name, string $description): string {
        $namespan = \html_writer::span($name, 'fw-semibold');
        $descriptionspan = \html_writer::span($description, 'text-muted small');

        return \html_writer::div($namespan . $descriptionspan, 'd-inline-flex flex-column ms-1');
    }
}

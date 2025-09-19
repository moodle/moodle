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

namespace core_badges\form;

use core_badges\backpack_api;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

/**
 * Backpack form class.
 *
 * @package    core_badges
 * @copyright  2019 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class external_backpack extends \moodleform {

    /**
     * Create the form.
     *
     */
    public function definition() {
        global $CFG;

        $mform = $this->_form;
        $backpack = $this->_customdata['externalbackpack'] ?? null;

        $mform->addElement('hidden', 'action', 'edit');
        $mform->setType('action', PARAM_ALPHA);

        $apiversions = badges_get_badge_api_versions();
        $mform->addElement('select', 'apiversion', get_string('apiversion', 'core_badges'), $apiversions);
        $mform->setType('apiversion', PARAM_RAW);
        $mform->setDefault('apiversion', OPEN_BADGES_V2P1);
        $mform->addRule('apiversion', null, 'required', null, 'client');

        $this->add_provider_fields();

        $mform->addElement('text', 'backpackweburl', get_string('backpackweburl', 'core_badges'));
        $mform->setType('backpackweburl', PARAM_URL);
        $mform->addRule('backpackweburl', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->hideIf('backpackweburl', 'apiversion', 'ne', OPEN_BADGES_V2);

        $mform->addElement('text', 'backpackapiurl',  get_string('backpackapiurl', 'core_badges'));
        $mform->setType('backpackapiurl', PARAM_URL);
        $mform->addRule('backpackapiurl', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->hideIf('backpackapiurl', 'apiversion', 'ne', OPEN_BADGES_V2);

        $mform->addElement('text', 'backpackweburlv2p1', get_string('backpackweburl', 'core_badges'));
        $mform->setType('backpackweburlv2p1', PARAM_URL);
        $mform->addRule('backpackweburlv2p1', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->hideIf('backpackweburlv2p1', 'apiversion', 'ne', (string) OPEN_BADGES_V2P1);

        $mform->addElement('hidden', 'id', ($backpack->id ?? null));
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'badgebackpack', 0);
        $mform->setType('badgebackpack', PARAM_INT);
        $mform->addElement('hidden', 'userid', 0);
        $mform->setType('userid', PARAM_INT);
        $mform->addElement('hidden', 'backpackuid', 0);
        $mform->setType('backpackuid', PARAM_INT);

        // Add rules for backpack URL fields.
        if (backpack_api::display_canvas_credentials_fields()) {
            $mform->hideIf('backpackweburl', 'provider', 'ne', backpack_api::PROVIDER_OTHER);
            $mform->hideIf('backpackapiurl', 'provider', 'ne', backpack_api::PROVIDER_OTHER);
        }

        $issueremail = $CFG->badges_defaultissuercontact;
        // Connect to a Canvas Credentials provider.
        $this->add_connect_issuer_canvas_fields($issueremail);

        // Connect to another provider.
        $this->add_connect_issuer_fields($backpack, $issueremail);

        if ($backpack) {
            $this->set_data($backpack);
        }

        // Disable short forms.
        $mform->setDisableShortforms();

        $this->add_action_buttons();
    }

    #[\Override]
    public function definition_after_data(): void {
        parent::definition_after_data();
        $mform = $this->_form;

        if ($this->is_submitted()) {
            if (!$mform->elementExists('apiversion')) {
                return;
            }
            $apiversion = $mform->getElement('apiversion')->getValue();
            $apiversion = $apiversion ? array_pop($apiversion) : null;
            $provider = $mform->elementExists('provider') ? $mform->getElement('provider')->getValue() : null;
            $provider = $provider ? array_pop($provider) : null;
            $region = $mform->elementExists('region') ? $mform->getElement('region')->getValue() : null;
            $region = $region ? array_pop($region) : null;
            if ($apiversion == OPEN_BADGES_V2) {
                if (
                    $provider == backpack_api::PROVIDER_CANVAS_CREDENTIALS
                    && isset($region) && $region != backpack_api::REGION_EMPTY
                ) {
                    $mform->getElement('backpackweburl')->setValue(
                        backpack_api::get_region_url($region),
                    );
                    $mform->getElement('backpackapiurl')->setValue(
                        backpack_api::get_region_api_url($region),
                    );

                    if ($mform->getElement('includeauthdetailscanvas')->getValue()) {
                        $mform->getElement('backpackemail')->setValue(
                            $mform->getElement('backpackemailcanvas')->getValue(),
                        );
                        $mform->getElement('password')->setValue(
                            $mform->getElement('backpackpasswordcanvas')->getValue(),
                        );
                    }
                } else if (is_null($provider) || $provider == backpack_api::PROVIDER_OTHER) {
                    if ($mform->getElement('includeauthdetails')->getValue() == 0) {
                        // Clear backpack issuer fields when authentication details checkbox is not checked.
                        $mform->getElement('backpackemail')->setValue('');
                        $mform->getElement('password')->setValue('');
                    }
                }
            } else if ($apiversion == OPEN_BADGES_V2P1) {
                if (!empty($mform->getElement('backpackweburlv2p1')->getValue())) {
                    $mform->getElement('backpackweburl')->setValue(
                        $mform->getElement('backpackweburlv2p1')->getValue(),
                    );
                }
                // Clear backpack issuer fields when OBv2.1 is selected.
                $mform->getElement('includeauthdetails')->setValue(0);
                $mform->getElement('backpackemail')->setValue('');
                $mform->getElement('password')->setValue('');
            }
        }
    }

    #[\Override]
    public function set_data($backpack) {
        if ($backpack->apiversion == OPEN_BADGES_V2) {
            if (backpack_api::is_canvas_credentials_region($backpack->backpackweburl)) {
                // Calculate provider and region fields based on backpack URLs.
                $backpack->provider = backpack_api::PROVIDER_CANVAS_CREDENTIALS;
                $backpack->region = backpack_api::get_regionid_from_url($backpack->backpackweburl);
                $backpack->backpackweburl = '';
                $backpack->backpackapiurl = '';
                if (isset($backpack->backpackemail) && !empty($backpack->backpackemail)) {
                    // Update Canvas Credentials fields.
                    $backpack->includeauthdetailscanvas = 1;
                    $backpack->backpackemailcanvas = $backpack->backpackemail;
                    $backpack->backpackpasswordcanvas = $backpack->password;
                    // Clear email and password fields for another providers.
                    $backpack->includeauthdetails = 0;
                    $backpack->backpackemail = '';
                    $backpack->password = '';
                }
            } else {
                $backpack->provider = backpack_api::PROVIDER_OTHER;
            }
        } else if ($backpack->apiversion == OPEN_BADGES_V2P1) {
            $backpack->backpackweburlv2p1 = $backpack->backpackweburl;
            $backpack->backpackweburl = '';
            $backpack->backpackapiurl = '';
        }

        parent::set_data($backpack);
    }

    /**
     * Validate the data from the form.
     *
     * @param  array $data form data
     * @param  array $files form files
     * @return array An array of error messages.
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        // Ensure backpackapiurl and backpackweburl are valid URLs.
        $isobv20 = isset($data['apiversion']) && $data['apiversion'] == OPEN_BADGES_V2;
        $isobv2p1 = isset($data['apiversion']) && $data['apiversion'] == OPEN_BADGES_V2P1;
        if ($isobv20) {
            $errors = array_merge($errors, $this->validate_obv20($data));
        } else if ($isobv2p1) {
            $errors = array_merge($errors, $this->validate_obv2p1($data));
        }

        // Check email and password are not empty when including auth details.
        if (!empty($data['includeauthdetails']) && empty($data['backpackemail'])) {
            $errors['backpackemail'] = get_string('err_required', 'form');
        }
        if (!empty($data['includeauthdetails']) && empty($data['password'])) {
            $errors['password'] = get_string('err_required', 'form');
        }

        return $errors;
    }

    /**
     * Validate the data for Open Badges v2.0.
     *
     * @param array $data Form data.
     * @return string[] An array of error messages.
     */
    private function validate_obv20(array $data): array {
        $errors = [];

        $displaycanvasfields = backpack_api::display_canvas_credentials_fields();
        if (
            $displaycanvasfields
            && (!array_key_exists('provider', $data) || $data['provider'] == backpack_api::PROVIDER_EMPTY)
        ) {
            // Check provider is set.
            $errors['provider'] = get_string('err_required', 'form');
        } else if (
            $displaycanvasfields
            && ($data['provider'] == backpack_api::PROVIDER_CANVAS_CREDENTIALS)
        ) {
            // Check region is set.
            if (!array_key_exists('region', $data) || $data['region'] == backpack_api::REGION_EMPTY) {
                $errors['region'] = get_string('err_required', 'form');
            }
        } else {
            if (empty($data['backpackweburl'])) {
                $errors['backpackweburl'] = get_string('err_required', 'form');
            } else if (!preg_match('@^https?://.+@', $data['backpackweburl'])) {
                $errors['backpackweburl'] = get_string('invalidurl', 'badges');
            }
            if (empty($data['backpackapiurl'])) {
                $errors['backpackapiurl'] = get_string('err_required', 'form');
            } else if (!preg_match('@^https?://.+@', $data['backpackapiurl'])) {
                $errors['backpackapiurl'] = get_string('invalidurl', 'badges');
            }
        }

        if ($displaycanvasfields) {
            if (!empty($data['includeauthdetailscanvas']) && empty($data['backpackemailcanvas'])) {
                $errors['backpackemailcanvas'] = get_string('err_required', 'form');
            }
            if (!empty($data['includeauthdetailscanvas']) && empty($data['backpackpasswordcanvas'])) {
                $errors['backpackpasswordcanvas'] = get_string('err_required', 'form');
            }
        }

        return $errors;
    }

    /**
     * Validate the data for Open Badges v2.1.
     *
     * @param array $data Form data.
     * @return string[] An array of error messages.
     */
    private function validate_obv2p1(array $data): array {
        $errors = [];

        if (empty($data['backpackweburlv2p1'])) {
            $errors['backpackweburlv2p1'] = get_string('err_required', 'form');
        } else if (!preg_match('@^https?://.+@', $data['backpackweburlv2p1'])) {
            $errors['backpackweburlv2p1'] = get_string('invalidurl', 'badges');
        }

        return $errors;
    }

    /**
     * Add provider fields to the form.
     */
    protected function add_provider_fields(): void {
        $mform = $this->_form;

        if (!backpack_api::display_canvas_credentials_fields()) {
            // If canvas credentials fields are not to be displayed, return early.
            return;
        }

        // Add an empty option at the start.
        $providers = backpack_api::get_providers();
        $providers = [backpack_api::PROVIDER_EMPTY => ''] + $providers;
        $mform->addElement('select', 'provider', get_string('provider', 'core_badges'), $providers);
        $mform->setType('provider', PARAM_RAW);
        $mform->hideIf('provider', 'apiversion', 'ne', OPEN_BADGES_V2);

        // Add an empty option at the start.
        $regions = backpack_api::get_regions();
        $regions = [backpack_api::REGION_EMPTY => ''] + array_column($regions, 'name');
        $mform->addElement('select', 'region', get_string('region', 'core_badges'), $regions);
        $mform->setType('region', PARAM_RAW);
        $mform->hideIf('region', 'provider', 'ne', backpack_api::PROVIDER_CANVAS_CREDENTIALS);
        $mform->hideIf('region', 'apiversion', 'ne', OPEN_BADGES_V2);
    }

    /**
     * Add Canvas backpack specific issuer auth details.
     *
     * @param string|null $email The email addressed provided or null if it's new.
     */
    protected function add_connect_issuer_canvas_fields(?string $email): void {
        $mform = $this->_form;

        if (!backpack_api::display_canvas_credentials_fields()) {
            // If canvas credentials fields are not to be displayed, return early.
            return;
        }

        $providers = backpack_api::get_providers();
        $regions = backpack_api::get_regions();
        if (empty($providers) || empty($regions)) {
            // If no providers or regions are available, return early.
            return;
        }

        // Checkbox and information to enable/disable issuer account.
        $mform->addElement('static', '', null, '');
        $mform->addElement(
            'advcheckbox',
            'includeauthdetailscanvas',
            null,
            '<strong>' . get_string('includeauthdetailscanvas', 'core_badges') . '</strong> '
            . get_string('includeauthdetailscanvas_subtitle', 'core_badges'),
        );
        if (!empty($backpack->backpackemail) || !empty($backpack->password)) {
            $mform->setDefault('includeauthdetailscanvas', 1);
        }
        $mform->addHelpButton('includeauthdetailscanvas', 'includeauthdetailscanvas', 'core_badges');
        $mform->hideIf('includeauthdetailscanvas', 'apiversion', 'ne', OPEN_BADGES_V2);
        $mform->hideIf('includeauthdetailscanvas', 'region', 'eq', backpack_api::REGION_EMPTY);
        $mform->hideIf('includeauthdetailscanvas', 'provider', 'ne', backpack_api::PROVIDER_CANVAS_CREDENTIALS);

        $mform->addElement(
            'static',
            'includeauthdetailscanvasdesc',
            null,
            get_string('includeauthdetailscanvas_desc', 'core_badges'),
        );
        $mform->hideIf('includeauthdetailscanvasdesc', 'includeauthdetailscanvas');
        $mform->hideIf('includeauthdetailscanvasdesc', 'region', 'eq', backpack_api::REGION_EMPTY);
        $mform->hideIf('includeauthdetailscanvasdesc', 'provider', 'ne', backpack_api::PROVIDER_CANVAS_CREDENTIALS);
        $mform->hideIf('includeauthdetailscanvasdesc', 'apiversion', 'ne', OPEN_BADGES_V2);

        // Email.
        $mform->addElement('text', 'backpackemailcanvas', get_string('issueremail', 'core_badges'));
        $mform->setType('backpackemailcanvas', PARAM_EMAIL);
        $mform->setDefault('backpackemailcanvas', $email);
        $mform->hideIf('backpackemailcanvas', 'includeauthdetailscanvas');
        $mform->hideIf('backpackemailcanvas', 'apiversion', 'ne', OPEN_BADGES_V2);
        $mform->hideIf('backpackemailcanvas', 'provider', 'ne', backpack_api::PROVIDER_CANVAS_CREDENTIALS);

        // Password.
        $mform->addElement('passwordunmask', 'backpackpasswordcanvas', get_string('password'));
        $mform->setType('backpackpasswordcanvas', PARAM_RAW);
        $mform->hideIf('backpackpasswordcanvas', 'includeauthdetailscanvas');
        $mform->hideIf('backpackpasswordcanvas', 'apiversion', 'ne', OPEN_BADGES_V2);
        $mform->hideIf('backpackpasswordcanvas', 'provider', 'ne', backpack_api::PROVIDER_CANVAS_CREDENTIALS);
    }

    /**
     * Add generic backpack issuer auth details.
     *
     * @param \stdClass|null $backpack The backpack instance.
     * @param string|null $email The issuer email or null if it's new.
     */
    protected function add_connect_issuer_fields(?\stdClass $backpack, ?string $email): void {
        $mform = $this->_form;

        // Checkbox and information to enable/disable issuer account.
        $mform->addElement(
            'advcheckbox',
            'includeauthdetails',
            null,
            '<strong>' . get_string('includeauthdetails', 'core_badges') . '</strong>',
        );
        if ($backpack && (!empty($backpack->backpackemail) || !empty($backpack->password))) {
            $mform->setDefault('includeauthdetails', 1);
        }
        $mform->addHelpButton('includeauthdetails', 'includeauthdetails', 'core_badges');
        $mform->hideIf('includeauthdetails', 'provider', 'eq', backpack_api::PROVIDER_CANVAS_CREDENTIALS);
        $mform->hideIf('includeauthdetails', 'apiversion', 'ne', OPEN_BADGES_V2);

        $mform->addElement('static', 'includeauthdetailsdesc', null, get_string('includeauthdetails_desc', 'core_badges'));
        $mform->hideIf('includeauthdetailsdesc', 'includeauthdetails');
        $mform->hideIf('includeauthdetailsdesc', 'provider', 'eq', backpack_api::PROVIDER_CANVAS_CREDENTIALS);

        // Email and password fields.
        $this->add_auth_fields($email);
    }

    /**
     * Add backpack specific auth details.
     *
     * @param string|null $email The email addressed provided or null if it's new.
     * @param bool $includepassword Include the password field. Defaults to true
     * @throws \coding_exception
     */
    protected function add_auth_fields(?string $email, bool $includepassword = true) {
        $mform = $this->_form;

        // Email.
        $mform->addElement('text', 'backpackemail', get_string('issueremail', 'core_badges'));
        $mform->setType('backpackemail', PARAM_EMAIL);
        $mform->setDefault('backpackemail', $email);
        $mform->hideIf('backpackemail', 'includeauthdetails');
        $mform->hideIf('backpackemail', 'apiversion', 'ne', OPEN_BADGES_V2);
        $mform->hideIf('backpackemail', 'provider', 'eq', backpack_api::PROVIDER_CANVAS_CREDENTIALS);

        // Password.
        if ($includepassword) {
            $mform->addElement('passwordunmask', 'password', get_string('password'));
            $mform->setType('password', PARAM_RAW);
        }
        $mform->hideIf('password', 'includeauthdetails');
        $mform->hideIf('password', 'apiversion', 'ne', OPEN_BADGES_V2);
        $mform->hideIf('password', 'provider', 'eq', backpack_api::PROVIDER_CANVAS_CREDENTIALS);
    }
}

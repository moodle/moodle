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
 * External backpack form
 *
 * @package    core_badges
 * @copyright  2019 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_badges\form;
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
        $backpack = false;

        if (isset($this->_customdata['externalbackpack'])) {
            $backpack = $this->_customdata['externalbackpack'];
        }

        $mform->addElement('hidden', 'action', 'edit');
        $mform->setType('action', PARAM_ALPHA);

        if ($backpack) {
            $mform->addElement('hidden', 'id', $backpack->id);
            $mform->setType('id', PARAM_INTEGER);
        }

        $mform->addElement('text', 'backpackapiurl',  get_string('backpackapiurl', 'core_badges'));
        $mform->setType('backpackapiurl', PARAM_URL);
        $mform->addRule('backpackapiurl', null, 'required', null, 'client');
        $mform->addRule('backpackapiurl', get_string('maximumchars', '', 255), 'maxlength', 50, 'client');

        $mform->addElement('text', 'backpackweburl', get_string('backpackweburl', 'core_badges'));
        $mform->setType('backpackweburl', PARAM_URL);
        $mform->addRule('backpackweburl', null, 'required', null, 'client');
        $mform->addRule('backpackweburl', get_string('maximumchars', '', 255), 'maxlength', 50, 'client');

        $apiversions = badges_get_badge_api_versions();
        $mform->addElement('select', 'apiversion', get_string('apiversion', 'core_badges'), $apiversions);
        $mform->setType('apiversion', PARAM_RAW);
        $mform->setDefault('apiversion', OPEN_BADGES_V2P1);
        $mform->addRule('apiversion', null, 'required', null, 'client');

        $issuername = $CFG->badges_defaultissuername;
        $mform->addElement('static', 'issuerinfo', get_string('defaultissuername', 'core_badges'), $issuername);

        $issuercontact = $CFG->badges_defaultissuercontact;
        $mform->addElement('static', 'issuerinfo', get_string('defaultissuercontact', 'core_badges'), $issuercontact);

        if ($backpack && $backpack->apiversion != OPEN_BADGES_V2P1) {
            $mform->addElement('passwordunmask', 'password', get_string('defaultissuerpassword', 'core_badges'));
            $mform->setType('password', PARAM_RAW);
            $mform->addHelpButton('password', 'defaultissuerpassword', 'badges');
            $mform->hideIf('password', 'apiversion', 'eq', 1);
        } else {
            $oauth2options = badges_get_oauth2_service_options();
            $mform->addElement('select', 'oauth2_issuerid', get_string('oauth2issuer', 'core_badges'), $oauth2options);
            $mform->setType('oauth2_issuerid', PARAM_INT);
        }
        if ($backpack) {
            $this->set_data($backpack);
        }

        // Disable short forms.
        $mform->setDisableShortforms();

        $this->add_action_buttons();
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

        // Ensure backpackapiurl and  are valid URLs.
        if (!empty($data['backpackapiurl']) && !preg_match('@^https?://.+@', $data['backpackapiurl'])) {
            $errors['backpackapiurl'] = get_string('invalidurl', 'badges');
        }
        if (!empty($data['backpackweburl']) && !preg_match('@^https?://.+@', $data['backpackweburl'])) {
            $errors['backpackweburl'] = get_string('invalidurl', 'badges');
        }

        return $errors;
    }
}

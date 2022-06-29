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

namespace enrol_lti\local\ltiadvantage\form;
use enrol_lti\local\ltiadvantage\repository\deployment_repository;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * The deployment_form class, for registering a deployment for a registered platform.
 *
 * @package    enrol_lti
 * @copyright  2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class deployment_form extends \moodleform {

    /**
     * Define the form.
     */
    protected function definition() {
        $mform = $this->_form;
        $strrequired = get_string('required');

        // Registration id.
        $mform->addElement('hidden', 'registrationid');
        $mform->setType('registrationid', PARAM_INT);

        // Name.
        $mform->addElement('text', 'name', get_string('adddeployment:name', 'enrol_lti'));
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', $strrequired, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        // Deployment Id.
        $mform->addElement('text', 'deploymentid', get_string('adddeployment:deploymentid', 'enrol_lti'));
        $mform->setType('deploymentid', PARAM_TEXT);
        $mform->addRule('deploymentid', $strrequired, 'required', null, 'client');
        $mform->addRule('deploymentid', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        $mform->addHelpButton('deploymentid', 'adddeployment:deploymentid', 'enrol_lti');

        $buttonarray = [];
        $buttonarray[] = $mform->createElement('submit', 'submitbutton', get_string('savechanges'));
        $buttonarray[] = $mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '', ' ', false);
    }

    /**
     * Provides uniqueness validation of the deployment id.
     *
     * @param array $data any form data
     * @param array $files any submitted files
     * @return array array of errors.
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        // Validate the uniqueness of the deploymentid within the registration.
        $deploymentrepo = new deployment_repository();
        if ($deploymentrepo->find_by_registration($data['registrationid'], $data['deploymentid'])) {
            $errors['deploymentid'] = get_string('adddeployment:invaliddeploymentiderror', 'enrol_lti');
        }

        return $errors;
    }
}

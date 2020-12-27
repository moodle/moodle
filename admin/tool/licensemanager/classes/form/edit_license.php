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
 * Form for creating/updating a custom license.
 *
 * @package    tool_licensemanager
 * @copyright  2019 Tom Dickman <tom.dickman@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_licensemanager\form;

use moodleform;
use tool_licensemanager\helper;
use tool_licensemanager\manager;

defined('MOODLE_INTERNAL') || die('Direct access to this script is forbidden.');

global $CFG;
require_once($CFG->libdir . '/formslib.php');

/**
 * Form for creating/updating a custom license.
 *
 * @package    tool_licensemanager
 * @copyright  2019 Tom Dickman <tom.dickman@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class edit_license extends moodleform {

    /**
     * @var string the action form is taking.
     */
    private $action;

    /**
     * @var string license shortname if editing or empty string if creating license.
     */
    private $licenseshortname;

    /**
     * edit_license constructor.
     *
     * @param string $action the license_manager action to be taken by form.
     * @param string $licenseshortname the shortname of the license to edit.
     */
    public function __construct(string $action, string $licenseshortname) {
        $this->action = $action;
        $this->licenseshortname = $licenseshortname;

        if ($action == manager::ACTION_UPDATE && !empty($licenseshortname)) {
            parent::__construct(helper::get_update_license_url($licenseshortname));
        } else {
            parent::__construct(helper::get_create_license_url());
        }
    }

    /**
     * Form definition for creation and editing of licenses.
     */
    public function definition() {

        $mform = $this->_form;

        $mform->addElement('text', 'shortname', get_string('shortname', 'tool_licensemanager'));
        $mform->setType('shortname', PARAM_ALPHANUMEXT);
        // Shortname is only editable when user is creating a license.
        if ($this->action != manager::ACTION_CREATE) {
            $mform->freeze('shortname');
        } else {
            $mform->addRule('shortname', get_string('shortnamerequirederror', 'tool_licensemanager'), 'required');
        }

        $mform->addElement('text', 'fullname', get_string('fullname', 'tool_licensemanager'));
        $mform->setType('fullname', PARAM_TEXT);
        $mform->addRule('fullname', get_string('fullnamerequirederror', 'tool_licensemanager'), 'required');

        $mform->addElement('text', 'source', get_string('source', 'tool_licensemanager'));
        $mform->setType('source', PARAM_URL);
        $mform->addHelpButton('source', 'source', 'tool_licensemanager');
        $mform->addRule('source', get_string('sourcerequirederror', 'tool_licensemanager'), 'required');

        $mform->addElement('date_selector', 'version', get_string('version', 'tool_licensemanager'), get_string('from'));
        $mform->addHelpButton('version', 'version', 'tool_licensemanager');

        $this->add_action_buttons();
    }

    /**
     * Validate form data and return errors (if any).
     *
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @return array of "element_name"=>"error_description" if there are errors,
     *         or an empty array if everything is OK (true allowed for backwards compatibility too).
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if (array_key_exists('source', $data)  && !filter_var($data['source'], FILTER_VALIDATE_URL)) {
            $errors['source'] = get_string('invalidurl', 'tool_licensemanager');
        }

        if (array_key_exists('version', $data) && $data['version'] > time()) {
            $errors['version'] = get_string('versioncannotbefuture', 'tool_licensemanager');
        }

        return $errors;
    }
}

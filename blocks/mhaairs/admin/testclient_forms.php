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
 * Webservice client test form for MHAAIRS Gradebook Integration.
 *
 * @package     block_mhaairs
 * @copyright   2014 Itamar Tzadok <itamar@substantialmethods.com>
 * @copyright   2013-2014 Moodlerooms inc.
 * @author      Teresa Hardy <thardy@moodlerooms.com>
 * @author      Darko Miletic <dmiletic@moodlerooms.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') or die();

global $CFG;
require_once($CFG->libdir.'/formslib.php');

/**
 * Base form for mhaairs webservice test client.
 */
class block_mhaairs_service_form extends moodleform {

    /**
     * Definition.
     *
     * @return void
     */
    public function definition() {
        $mform = $this->_form;

        $this->add_action_buttons(true, get_string('execute', 'webservice'));

        // Protocol.
        $mform->addElement('header', 'mhtestclienthdr', get_string('testclient', 'webservice'));
        $this->definition_protocol();

        // Authentication.
        $mform->addElement('header', 'mhauthenticationhdr', 'Authentication');
        $this->set_expanded('mhauthenticationhdr');
        $this->definition_auth();

        // Service specific definition.
        $this->custom_definition();

        $this->add_action_buttons(true, get_string('execute', 'webservice'));
    }

    /**
     * Definition protocol.
     *
     * @return void
     */
    public function definition_protocol() {
        $mform = $this->_form;

        $protocols = array(
            'rest' => get_string('pluginname', 'webservice_rest')
        );
        $label = get_string('protocol', 'webservice');
        $mform->addElement('select', 'protocol', $label, $protocols);
        $mform->setType('protocol', PARAM_ALPHA);

        // Response format.
        $options = array(
            '' => get_string('xml', 'block_mhaairs'),
            'json' => get_string('json', 'block_mhaairs'),
        );
        $label = get_string('responseformat', 'block_mhaairs');
        $mform->addElement('select', 'moodlewsrestformat', $label, $options);
        $mform->setDefault('moodlewsrestformat', 'json');
    }

    /**
     * Definition authentication.
     *
     * @return void
     */
    public function definition_auth() {
        $mform = $this->_form;

        // Auth method.
        $authmethods = array(
            'simple' => get_string('simple', 'block_mhaairs'),
            'token' => get_string('token', 'block_mhaairs'),
        );
        $label = get_string('authmethod', 'webservice');
        $mform->addElement('select', 'authmethod', $label, $authmethods);
        $mform->setType('authmethod', PARAM_ALPHA);
        $mform->setDefault('authmethod', 'token');

        // Username password auth.
        $mform->addElement('text', 'wsusername', 'mhusername');
        $mform->setType('wsusername', PARAM_USERNAME);
        $mform->disabledIf('wsusername', 'authmethod', 'neq', 'simple');

        $mform->addElement('password', 'wspassword', 'mhpassword');
        $mform->setType('wspassword', PARAM_RAW);
        $mform->disabledIf('wspassword', 'authmethod', 'neq', 'simple');

        // Token auth.
        $mform->addElement('text', 'token', 'token', array('size' => '32'));
        $mform->setType('token', PARAM_BASE64);
        $mform->disabledIf('token', 'authmethod', 'neq', 'token');
    }

    /**
     * Offers posibility to add elements to the form.
     */
    protected function custom_definition() {

    }

    /**
     * Generate web service parameters.
     *
     * @param object $data
     * @return array
     */
    protected function format_params($data) {
        return (array) $data;
    }

    protected function set_expanded($element) {
        $mform = $this->_form;

        if (method_exists($mform, 'setExpanded')) {
            $mform->setExpanded($element);
        }
    }

    public function get_params() {
        if (!$data = $this->get_data()) {
            return null;
        }

        return $this->format_params($data);
    }
}


/**
 * Block mhaairs update grade test client form.
 */
class block_mhaairs_update_grade_form extends block_mhaairs_service_form {

    protected function custom_definition() {
        $mform = $this->_form;

        $mform->addElement('header', 'mhdefserviceparamshdr', 'Service params');
        $this->set_expanded('mhdefserviceparamshdr');
        $this->definition_service_params();

        $mform->addElement('header', 'mhdefitemdetailshdr', 'Item details');
        $this->set_expanded('mhdefitemdetailshdr');
        $this->definition_item_details();

        $mform->addElement('header', 'mhdefgradeshdr', 'Grades');
        $this->set_expanded('mhdefgradeshdr');
        $this->definition_grades();
    }

    protected function definition_service_params() {
        $mform = $this->_form;

        $dataset = 'serviceparams';

        $fields = array(
            'source' => array('text', 'Source of grade', 'mhaairs'),
            'courseid' => array('text', 'Course id', ''),
            'itemtype' => array('text', 'Item type', 'manual'),
            'itemmodule' => array('text', 'Item module', 'mhaairs'),
            'iteminstance' => array('text', 'Item instance', 0),
            'itemnumber' => array('text', 'Item number', 0),
        );

        foreach ($fields as $key => $def) {
            list($type, $label, $default, $options) = array_pad($def, 4, null);
            if ($type == 'select') {
                $mform->addElement($type, $dataset. "[$key]", $label, $options);
            } else {
                $mform->addElement($type, $dataset. "[$key]", $label);
            }
            if ($type == 'text') {
                $mform->setType($dataset. "[$key]", PARAM_TEXT);
            }
            $mform->setDefault($dataset. "[$key]", $default);
        }
    }

    protected function definition_item_details() {
        $mform = $this->_form;

        $dataset = 'itemdetails';

        $identitytypes = array(
            '' => get_string('choosedots'),
            'internal' => 'internal',
            'lti' => 'lti'
        );

        $fields = array(
            'categoryid' => array('text', 'Category id', ''),
            'courseid' => array('text', 'Course id', ''),
            'identity_type' => array('select', 'Identity type', '', $identitytypes),
            'itemname' => array('text', 'Item name', ''),
            'itemtype' => array('text', 'Item type', 'manual'),
            'idnumber' => array('text', 'Id number', 0),
            'gradetype' => array('text', 'Grade type', GRADE_TYPE_VALUE),
            'grademax' => array('text', 'Grade max', 100),
            'iteminfo' => array('text', 'Item info', ''),
            'deleted' => array('advcheckbox', 'Deleted', 0),
        );

        // Enable data set.
        $mform->addElement('advcheckbox', "enable$dataset", 'Enable');

        foreach ($fields as $key => $def) {
            list($type, $label, $default, $options) = array_pad($def, 4, null);
            if ($type == 'select') {
                $mform->addElement($type, $dataset. "[$key]", $label, $options);
            } else {
                $mform->addElement($type, $dataset. "[$key]", $label);
            }
            if ($type == 'text') {
                $mform->setType($dataset. "[$key]", PARAM_TEXT);
            }
            $mform->setDefault($dataset. "[$key]", $default);
            $mform->disabledIf($dataset. "[$key]", "enable$dataset", 'eq', 0);
        }
    }

    protected function definition_grades() {
        $mform = $this->_form;

        $dataset = 'grades';

        $fields = array(
            'userid' => array('text', 'User id', ''),
            'rawgrade' => array('text', 'Grade', ''),
        );

        // Enable data set.
        $mform->addElement('advcheckbox', "enable$dataset", 'Enable');

        foreach ($fields as $key => $def) {
            list($type, $label, $default, $options) = array_pad($def, 4, null);
            if ($type == 'select') {
                $mform->addElement($type, $dataset. "[$key]", $label, $options);
            } else {
                $mform->addElement($type, $dataset. "[$key]", $label);
            }
            if ($type == 'text') {
                $mform->setType($dataset. "[$key]", PARAM_TEXT);
            }
            $mform->setDefault($dataset. "[$key]", $default);
            $mform->disabledIf($dataset. "[$key]", "enable$dataset", 'eq', 0);
        }
    }

    public function get_params() {
        if (!$data = $this->get_data()) {
            return null;
        }

        $serviceparams = (object) $data->serviceparams;
        $serviceparams->itemdetails = null;
        $serviceparams->grades = null;

        // Add itemdetails.
        if (!empty($data->enableitemdetails)) {
            $serviceparams->itemdetails = urlencode(json_encode($data->itemdetails));
        }

        // Add grades.
        if (!empty($data->enablegrades)) {
            $serviceparams->grades = urlencode(json_encode($data->grades));
        }

        return $this->format_params($serviceparams);
    }
}

/**
 * Block mhaairs get grade test client form.
 */
class block_mhaairs_get_grade_form extends block_mhaairs_service_form {

    protected function custom_definition() {
        $mform = $this->_form;

        $mform->addElement('header', 'mhdefserviceparamshdr', 'Service params');
        $this->set_expanded('mhdefserviceparamshdr');
        $this->definition_service_params();

        $mform->addElement('header', 'mhdefitemdetailshdr', 'Item details');
        $this->set_expanded('mhdefitemdetailshdr');
        $this->definition_item_details();

        $mform->addElement('header', 'mhdefgradeshdr', 'Grades');
        $this->set_expanded('mhdefgradeshdr');
        $this->definition_grades();
    }

    protected function definition_service_params() {
        $mform = $this->_form;

        $dataset = 'serviceparams';

        $fields = array(
            'source' => array('text', 'Source of grade', 'mhaairs'),
            'courseid' => array('text', 'Course id', ''),
            'itemtype' => array('text', 'Item type', 'manual'),
            'itemmodule' => array('text', 'Item module', 'mhaairs'),
            'iteminstance' => array('text', 'Item instance', 0),
            'itemnumber' => array('text', 'Item number', 0),
        );

        foreach ($fields as $key => $def) {
            list($type, $label, $default, $options) = array_pad($def, 4, null);
            if ($type == 'select') {
                $mform->addElement($type, $dataset. "[$key]", $label, $options);
            } else {
                $mform->addElement($type, $dataset. "[$key]", $label);
            }
            if ($type == 'text') {
                $mform->setType($dataset. "[$key]", PARAM_TEXT);
            }
            $mform->setDefault($dataset. "[$key]", $default);
        }
    }

    protected function definition_item_details() {
        $mform = $this->_form;

        $dataset = 'itemdetails';

        $identitytypes = array(
            '' => get_string('choosedots'),
            'internal' => 'internal',
            'lti' => 'lti'
        );

        $fields = array(
            'categoryid' => array('text', 'Category id', ''),
            'courseid' => array('text', 'Course id', ''),
            'identity_type' => array('select', 'Identity type', '', $identitytypes),
            'itemname' => array('text', 'Item name', ''),
            'itemtype' => array('text', 'Item type', 'manual'),
            'idnumber' => array('text', 'Id number', 0),
            'gradetype' => array('text', 'Grade type', GRADE_TYPE_VALUE),
            'grademax' => array('text', 'Grade max', 100),
            'iteminfo' => array('text', 'Item info', ''),
        );

        // Enable data set.
        $mform->addElement('advcheckbox', "enable$dataset", 'Enable');

        foreach ($fields as $key => $def) {
            list($type, $label, $default, $options) = array_pad($def, 4, null);
            if ($type == 'select') {
                $mform->addElement($type, $dataset. "[$key]", $label, $options);
            } else {
                $mform->addElement($type, $dataset. "[$key]", $label);
            }
            if ($type == 'text') {
                $mform->setType($dataset. "[$key]", PARAM_TEXT);
            }
            $mform->setDefault($dataset. "[$key]", $default);
            $mform->disabledIf($dataset. "[$key]", "enable$dataset", 'eq', 0);
        }
    }

    protected function definition_grades() {
        $mform = $this->_form;

        $dataset = 'grades';

        $fields = array(
            'userid' => array('text', 'User id', ''),
        );

        // Enable data set.
        $mform->addElement('advcheckbox', "enable$dataset", 'Enable');

        foreach ($fields as $key => $def) {
            list($type, $label, $default, $options) = array_pad($def, 4, null);
            if ($type == 'select') {
                $mform->addElement($type, $dataset. "[$key]", $label, $options);
            } else {
                $mform->addElement($type, $dataset. "[$key]", $label);
            }
            if ($type == 'text') {
                $mform->setType($dataset. "[$key]", PARAM_TEXT);
            }
            $mform->setDefault($dataset. "[$key]", $default);
            $mform->disabledIf($dataset. "[$key]", "enable$dataset", 'eq', 0);
        }
    }

    public function get_params() {
        if (!$data = $this->get_data()) {
            return null;
        }

        $serviceparams = (object) $data->serviceparams;
        $serviceparams->itemdetails = null;
        $serviceparams->grades = null;

        // Add itemdetails.
        if (!empty($data->enableitemdetails)) {
            $serviceparams->itemdetails = urlencode(json_encode($data->itemdetails));
        }

        // Add grades.
        if (!empty($data->enablegrades)) {
            $serviceparams->grades = urlencode(json_encode($data->grades));
        }

        return $this->format_params($serviceparams);
    }
}

/**
 * Block mhaairs gradebookservice test client form.
 * Alias for block mhaairs update grade test client form.
 */
class block_mhaairs_gradebookservice_form extends block_mhaairs_update_grade_form {
}

/**
 * Block mhaairs get user info test client form.
 */
class block_mhaairs_get_user_info_form extends block_mhaairs_service_form {

    protected function custom_definition() {
        $mform = $this->_form;

        $mform->addElement('header', 'mhdefserviceparamshdr', 'Service params');
        $this->set_expanded('mhdefserviceparamshdr');
        $this->definition_service_params();
    }

    protected function definition_service_params() {
        $mform = $this->_form;

        // User id.
        $mform->addElement('text', 'serviceparams[userid]', 'User id');
        $mform->setType('serviceparams[userid]', PARAM_TEXT);

        // Identity type.
        $mform->addElement('advcheckbox', 'serviceparams[identitytype]', 'Identity type', 'internal', null, array('', 'internal'));
    }

    public function get_params() {
        if (!$data = $this->get_data()) {
            return null;
        }

        $serviceparams = (object) $data->serviceparams;
        $secret = get_config('core', 'block_mhaairs_shared_secret');

        // Create the token.
        $token = MHUtil::create_token($serviceparams->userid);
        $encodedtoken = MHUtil::encode_token2($token, $secret);
        $serviceparams->token = $encodedtoken;
        unset($serviceparams->userid);

        return $this->format_params($serviceparams);
    }
}

/**
 * Block mhaairs get environment info test client form.
 */
class block_mhaairs_get_environment_info_form extends block_mhaairs_service_form {
    public function get_params() {
        if (!$data = $this->get_data()) {
            return null;
        }

        return array();
    }
}

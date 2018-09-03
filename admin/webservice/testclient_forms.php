<?php

require_once($CFG->libdir.'/formslib.php');


class webservice_test_client_form extends moodleform {
    public function definition() {
        global $CFG;

        $mform = $this->_form;
        list($functions, $protocols) = $this->_customdata;

        $mform->addElement('header', 'wstestclienthdr', get_string('testclient', 'webservice'));

        $authmethod = array('simple' => 'simple', 'token' => 'token');
        $mform->addElement('select', 'authmethod', get_string('authmethod', 'webservice'), $authmethod);
        $mform->setType('simple', PARAM_ALPHA);

        $mform->addElement('select', 'protocol', get_string('protocol', 'webservice'), $protocols);
        $mform->setType('protocol', PARAM_ALPHA);

        $mform->addElement('select', 'function', get_string('function', 'webservice'), $functions);
        $mform->setType('function', PARAM_PLUGIN);

        $this->add_action_buttons(false, get_string('select'));
    }
}

// === Test client forms ===

/**
 * Base class for implementations of WS test client forms.
 *
 * @package   core_webservice
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright 2017 Marina Glancy
 */
abstract class webservice_test_client_base_form extends moodleform {

    /**
     * Definition of the parameters used by this WS function
     */
    protected abstract function test_client_definition();

    /**
     * The form definition.
     */
    public function definition() {
        $mform = $this->_form;

        $mform->addElement('header', 'wstestclienthdr', get_string('testclient', 'webservice'));

        // Note: these values are intentionally PARAM_RAW - we want users to test any rubbish as parameters.
        $data = $this->_customdata;
        if ($data['authmethod'] == 'simple') {
            $mform->addElement('text', 'wsusername', 'wsusername');
            $mform->setType('wsusername', core_user::get_property_type('username'));
            $mform->addElement('text', 'wspassword', 'wspassword');
            $mform->setType('wspassword', core_user::get_property_type('password'));
        } else if ($data['authmethod'] == 'token') {
            $mform->addElement('text', 'token', 'token');
            $mform->setType('token', PARAM_RAW_TRIMMED);
        }

        $mform->addElement('hidden', 'authmethod', $data['authmethod']);
        $mform->setType('authmethod', PARAM_ALPHA);

        $mform->addElement('hidden', 'function');
        $mform->setType('function', PARAM_PLUGIN);

        $mform->addElement('hidden', 'protocol');
        $mform->setType('protocol', PARAM_ALPHA);

        $this->test_client_definition();

        $this->add_action_buttons(true, get_string('execute', 'webservice'));
    }

    /**
     * Get the parameters that the user submitted using the form.
     * @return array|null
     */
    public function get_params() {
        if (!$data = $this->get_data()) {
            return null;
        }
        return array_diff_key((array)$data, ['submitbutton' => 1, 'protocol' => 1, 'function' => 1,
            'wsusername' => 1, 'wspassword' => 1, 'token' => 1, 'authmethod' => 1]);
    }
}

/**
 * Form class for create_categories() web service function test.
 *
 * @package   core_webservice
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright 2012 Fabio Souto
 */
class core_course_create_categories_testclient_form extends webservice_test_client_base_form {
    /**
     * The form definition.
     */
    protected function test_client_definition() {
        $mform = $this->_form;
        $mform->addElement('text', 'name[0]', 'name[0]');
        $mform->addElement('text', 'parent[0]', 'parent[0]');
        $mform->addElement('text', 'idnumber[0]', 'idnumber[0]');
        $mform->addElement('text', 'description[0]', 'description[0]');
        $mform->addElement('text', 'name[1]', 'name[1]');
        $mform->addElement('text', 'parent[1]', 'parent[1]');
        $mform->addElement('text', 'idnumber[1]', 'idnumber[1]');
        $mform->addElement('text', 'description[1]', 'description[1]');
        $mform->setType('name', PARAM_TEXT);
        $mform->setType('parent', PARAM_INT);
        $mform->setType('idnumber', PARAM_RAW);
        $mform->setType('description', PARAM_RAW);
    }

    /**
     * Get the parameters that the user submitted using the form.
     * @return array|null
     */
    public function get_params() {
        if (!$data = $this->get_data()) {
            return null;
        }

        $params = array();
        $params['categories'] = array();
        for ($i=0; $i<10; $i++) {
            if (empty($data->name[$i])) {
                continue;
            }
            $params['categories'][] = array('name'=>$data->name[$i], 'parent'=>$data->parent[$i],
                                            'idnumber'=>$data->idnumber[$i], 'description'=>$data->description[$i]);
        }
        return $params;
    }
}

/**
 * Form class for delete_categories() web service function test.
 *
 * @package   core_webservice
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright 2012 Fabio Souto
 */
class core_course_delete_categories_testclient_form extends webservice_test_client_base_form {
    /**
     * The form definition.
     */
    protected function test_client_definition() {
        $mform = $this->_form;
        $mform->addElement('text', 'id[0]', 'id[0]');
        $mform->addElement('text', 'newparent[0]', 'newparent[0]');
        $mform->addElement('text', 'recursive[0]', 'recursive[0]');
        $mform->addElement('text', 'id[1]', 'id[1]');
        $mform->addElement('text', 'newparent[1]', 'newparent[1]');
        $mform->addElement('text', 'recursive[1]', 'recursive[1]');
        $mform->setType('id', PARAM_INT);
        $mform->setType('newparent', PARAM_INT);
        $mform->setType('recursive', PARAM_BOOL);
    }

    /**
     * Get the parameters that the user submitted using the form.
     * @return array|null
     */
    public function get_params() {
        if (!$data = $this->get_data()) {
            return null;
        }
        $params = array();
        $params['categories'] = array();
        for ($i=0; $i<10; $i++) {
            if (empty($data->id[$i])) {
                continue;
            }
            $attrs = array();
            $attrs['id'] = $data->id[$i];
            if (!empty($data->newparent[$i])) {
                $attrs['newparent'] = $data->newparent[$i];
            }
            if (!empty($data->recursive[$i])) {
                $attrs['recursive'] = $data->recursive[$i];
            }
            $params['categories'][] = $attrs;
        }
        return $params;
    }
}

/**
 * Form class for create_categories() web service function test.
 *
 * @package   core_webservice
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright 2012 Fabio Souto
 */
class core_course_update_categories_testclient_form extends webservice_test_client_base_form {
    /**
     * The form definition.
     */
    protected function test_client_definition() {
        $mform = $this->_form;
        $mform->addElement('text', 'id[0]', 'id[0]');
        $mform->addElement('text', 'name[0]', 'name[0]');
        $mform->addElement('text', 'parent[0]', 'parent[0]');
        $mform->addElement('text', 'idnumber[0]', 'idnumber[0]');
        $mform->addElement('text', 'description[0]', 'description[0]');
        $mform->addElement('text', 'id[1]', 'id[1]');
        $mform->addElement('text', 'name[1]', 'name[1]');
        $mform->addElement('text', 'parent[1]', 'parent[1]');
        $mform->addElement('text', 'idnumber[1]', 'idnumber[1]');
        $mform->addElement('text', 'description[1]', 'description[1]');
        $mform->setType('id', PARAM_INT);
        $mform->setType('name', PARAM_TEXT);
        $mform->setType('parent', PARAM_INT);
        $mform->setType('idnumber', PARAM_RAW);
        $mform->setType('description', PARAM_RAW);
    }

    /**
     * Get the parameters that the user submitted using the form.
     * @return array|null
     */
    public function get_params() {
        if (!$data = $this->get_data()) {
            return null;
        }
        $params = array();
        $params['categories'] = array();
        for ($i=0; $i<10; $i++) {

            if (empty($data->id[$i])) {
                continue;
            }
            $attrs = array();
            $attrs['id'] = $data->id[$i];
            if (!empty($data->name[$i])) {
                $attrs['name'] = $data->name[$i];
            }
            if (!empty($data->parent[$i])) {
                $attrs['parent'] = $data->parent[$i];
            }
            if (!empty($data->idnumber[$i])) {
                $attrs['idnumber'] = $data->idnumber[$i];
            }
            if (!empty($data->description[$i])) {
                $attrs['description'] = $data->description[$i];
            }
            $params['categories'][] = $attrs;
        }
        return $params;
    }
}

/**
 * Test class for WS function core_fetch_notifications
 *
 * @package   core_webservice
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright 2017 Marina Glancy
 */
class core_fetch_notifications_testclient_form extends webservice_test_client_base_form {
    /**
     * The form definition.
     */
    protected function test_client_definition() {
        $mform = $this->_form;
        $mform->addElement('text', 'contextid', 'contextid');
        $mform->setType('contextid', PARAM_INT);
        $mform->setDefault('contextid', context_system::instance()->id);
    }
}

/**
 * Test class for WS function get_site_info
 *
 * @package   core_webservice
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright 2017 Marina Glancy
 */
class core_webservice_get_site_info_testclient_form extends webservice_test_client_base_form {
    /**
     * The form definition.
     */
    protected function test_client_definition() {
    }
}

/**
 * Test class for WS function core_get_string
 *
 * @package   core_webservice
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright 2017 Marina Glancy
 */
class core_get_string_testclient_form extends webservice_test_client_base_form {
    /**
     * The form definition.
     */
    protected function test_client_definition() {
        $mform = $this->_form;
        $mform->addElement('text', 'stringid', 'stringid');
        $mform->setType('stringid', PARAM_STRINGID);
        $mform->addElement('text', 'component', 'component');
        $mform->setType('component', PARAM_COMPONENT);
        $mform->addElement('text', 'lang', 'lang');
        $mform->setType('lang', PARAM_LANG);
        $mform->addElement('text', 'stringparams_name[1]', 'Parameter 1 name');
        $mform->setType('stringparams_name[1]', PARAM_ALPHANUMEXT);
        $mform->addElement('text', 'stringparams_value[1]', 'Parameter 1 value');
        $mform->setType('stringparams_value[1]', PARAM_RAW);
        $mform->addElement('text', 'stringparams_name[2]', 'Parameter 2 name');
        $mform->setType('stringparams_name[2]', PARAM_ALPHANUMEXT);
        $mform->addElement('text', 'stringparams_value[2]', 'Parameter 2 value');
        $mform->setType('stringparams_value[2]', PARAM_RAW);
        $mform->addElement('text', 'stringparams_name[3]', 'Parameter 3 name');
        $mform->setType('stringparams_name[3]', PARAM_ALPHANUMEXT);
        $mform->addElement('text', 'stringparams_value[3]', 'Parameter 3 value');
        $mform->setType('stringparams_value[3]', PARAM_RAW);
        $mform->addElement('static', 'paramnote', '', 'If a parameter is not an object, only specify "Parameter 1 value"');
    }

    /**
     * Get the parameters that the user submitted using the form.
     * @return array|null
     */
    public function get_params() {
        $params = parent::get_params();
        if ($params === null) {
            return null;
        }

        $params['stringparams'] = [];
        for ($idx = 1; $idx <= 3; $idx++) {
            $name = isset($params['stringparams_name'][$idx]) ? strval($params['stringparams_name'][$idx]) : '';
            $value = isset($params['stringparams_value'][$idx]) ? strval($params['stringparams_value'][$idx]) : '';
            if ($name !== '' || $value !== '') {
                if ($name === '') {
                    $params['stringparams'][] = ['value' => $value];
                } else {
                    $params['stringparams'][] = ['name' => $name, 'value' => $value];
                }
            }
        }
        unset($params['stringparams_name']);
        unset($params['stringparams_value']);
        return $params;
    }
}

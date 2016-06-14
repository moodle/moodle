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
 * Form class for create_categories() web service function test.
 *
 * @package   core_webservice
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright 2012 Fabio Souto
 */
class core_course_create_categories_form extends moodleform {
    /**
     * The form definition.
     */
    public function definition() {
        global $CFG;

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
        $mform->setType('authmethod', core_user::get_property_type('auth'));
        $mform->addElement('text', 'name[0]', 'name[0]');
        $mform->addElement('text', 'parent[0]', 'parent[0]');
        $mform->addElement('text', 'idnumber[0]', 'idnumber[0]');
        $mform->addElement('text', 'description[0]', 'description[0]');
        $mform->addElement('text', 'name[1]', 'name[1]');
        $mform->addElement('text', 'parent[1]', 'parent[1]');
        $mform->addElement('text', 'idnumber[1]', 'idnumber[1]');
        $mform->addElement('text', 'description[1]', 'description[1]');
        $mform->setType('name', core_user::get_property_type('firstname'));
        $mform->setType('parent', core_user::get_property_type('id'));
        $mform->setType('idnumber', core_user::get_property_type('idnumber'));
        $mform->setType('description', core_user::get_property_type('description'));

        $mform->addElement('hidden', 'function');
        $mform->setType('function', PARAM_PLUGIN);

        $mform->addElement('hidden', 'protocol');
        $mform->setType('protocol', PARAM_ALPHA);

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
        // Remove unused from form data.
        unset($data->submitbutton);
        unset($data->protocol);
        unset($data->function);
        unset($data->wsusername);
        unset($data->wspassword);
        unset($data->token);
        unset($data->authmethod);

        $params = array();
        $params['categories'] = array();
        for ($i=0; $i<10; $i++) {
            if (empty($data->name[$i]) or empty($data->parent[$i])) {
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
class core_course_delete_categories_form extends moodleform {
    /**
     * The form definition.
     */
    public function definition() {
        global $CFG;

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
        $mform->setType('authmethod', core_user::get_property_type('auth'));
        $mform->addElement('text', 'id[0]', 'id[0]');
        $mform->addElement('text', 'newparent[0]', 'newparent[0]');
        $mform->addElement('text', 'recursive[0]', 'recursive[0]');
        $mform->addElement('text', 'id[1]', 'id[1]');
        $mform->addElement('text', 'newparent[1]', 'newparent[1]');
        $mform->addElement('text', 'recursive[1]', 'recursive[1]');
        $mform->setType('id', core_user::get_property_type('id'));
        $mform->setType('newparent', PARAM_INT);
        $mform->setType('recursive', PARAM_BOOL);

        $mform->addElement('hidden', 'function');
        $mform->setType('function', PARAM_PLUGIN);

        $mform->addElement('hidden', 'protocol');
        $mform->setType('protocol', PARAM_ALPHA);

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
        // Remove unused from form data.
        unset($data->submitbutton);
        unset($data->protocol);
        unset($data->function);
        unset($data->wsusername);
        unset($data->wspassword);
        unset($data->token);
        unset($data->authmethod);

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
class core_course_update_categories_form extends moodleform {
    /**
     * The form definition.
     */
    public function definition() {
        global $CFG;

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
        $mform->setType('authmethod', core_user::get_property_type('auth'));
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
        $mform->setType('id', core_user::get_property_type('id'));
        $mform->setType('name', core_user::get_property_type('firstname'));
        $mform->setType('parent', PARAM_INT);
        $mform->setType('idnumber', core_user::get_property_type('idnumber'));
        $mform->setType('description', core_user::get_property_type('description'));

        $mform->addElement('hidden', 'function');
        $mform->setType('function', PARAM_PLUGIN);

        $mform->addElement('hidden', 'protocol');
        $mform->setType('protocol', PARAM_ALPHA);

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
        // Remove unused from form data.
        unset($data->submitbutton);
        unset($data->protocol);
        unset($data->function);
        unset($data->wsusername);
        unset($data->wspassword);
        unset($data->token);
        unset($data->authmethod);

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
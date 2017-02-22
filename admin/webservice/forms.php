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
 * Web services admin UI forms
 *
 * @package   webservice
 * @copyright 2009 Moodle Pty Ltd (http://moodle.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once $CFG->libdir . '/formslib.php';

/**
 * Display the authorised user settings form
 * Including IP Restriction, Valid until and (TODO) capability
 */
class external_service_authorised_user_settings_form extends moodleform {

    function definition() {
        $mform = $this->_form;
        $data = $this->_customdata;

        $mform->addElement('header', 'serviceusersettings',
                get_string('serviceusersettings', 'webservice'));

        $mform->addElement('text', 'iprestriction',
                get_string('iprestriction', 'webservice'));
        $mform->addHelpButton('iprestriction', 'iprestriction', 'webservice');
        $mform->setType('iprestriction', PARAM_RAW_TRIMMED);

        $mform->addElement('date_selector', 'validuntil',
                get_string('validuntil', 'webservice'), array('optional' => true));
        $mform->addHelpButton('validuntil', 'validuntil', 'webservice');
        $mform->setType('validuntil', PARAM_INT);

        $this->add_action_buttons(true, get_string('updateusersettings', 'webservice'));

        $this->set_data($data);
    }

}

class external_service_form extends moodleform {

    function definition() {
        $mform = $this->_form;
        $service = isset($this->_customdata) ? $this->_customdata : new stdClass();

        $mform->addElement('header', 'extservice',
                get_string('externalservice', 'webservice'));

        $mform->addElement('text', 'name', get_string('name'));
        $mform->addRule('name', get_string('required'), 'required', null, 'client');
        $mform->setType('name', PARAM_TEXT);

        $mform->addElement('text', 'shortname', get_string('shortname'), 'maxlength="255" size="20"');
        $mform->setType('shortname', PARAM_TEXT);
        if (!empty($service->id)) {
            $mform->hardFreeze('shortname');
            $mform->setConstants('shortname', $service->shortname);
        }

        $mform->addElement('advcheckbox', 'enabled', get_string('enabled', 'webservice'));
        $mform->setType('enabled', PARAM_BOOL);
        $mform->addElement('advcheckbox', 'restrictedusers',
                get_string('restrictedusers', 'webservice'));
        $mform->addHelpButton('restrictedusers', 'restrictedusers', 'webservice');
        $mform->setType('restrictedusers', PARAM_BOOL);

        // Can users download files?
        $mform->addElement('advcheckbox', 'downloadfiles', get_string('downloadfiles', 'webservice'));
        $mform->setAdvanced('downloadfiles');
        $mform->addHelpButton('downloadfiles', 'downloadfiles', 'webservice');
        $mform->setType('downloadfiles', PARAM_BOOL);

        // Can users upload files?
        $mform->addElement('advcheckbox', 'uploadfiles', get_string('uploadfiles', 'webservice'));
        $mform->setAdvanced('uploadfiles');
        $mform->addHelpButton('uploadfiles', 'uploadfiles', 'webservice');

        /// needed to select automatically the 'No required capability" option
        $currentcapabilityexist = false;
        if (empty($service->requiredcapability)) {
            $service->requiredcapability = "norequiredcapability";
            $currentcapabilityexist = true;
        }

        // Prepare the list of capabilities to choose from
        $systemcontext = context_system::instance();
        $allcapabilities = $systemcontext->get_capabilities();
        $capabilitychoices = array();
        $capabilitychoices['norequiredcapability'] = get_string('norequiredcapability',
                        'webservice');
        foreach ($allcapabilities as $cap) {
            $capabilitychoices[$cap->name] = $cap->name . ': '
                    . get_capability_string($cap->name);
            if (!empty($service->requiredcapability)
                    && $service->requiredcapability == $cap->name) {
                $currentcapabilityexist = true;
            }
        }

        $mform->addElement('searchableselector', 'requiredcapability',
                get_string('requiredcapability', 'webservice'), $capabilitychoices);
        $mform->addHelpButton('requiredcapability', 'requiredcapability', 'webservice');
        $mform->setAdvanced('requiredcapability');
        $mform->setType('requiredcapability', PARAM_RAW);
/// display notification error if the current requiredcapability doesn't exist anymore
        if (empty($currentcapabilityexist)) {
            global $OUTPUT;
            $mform->addElement('static', 'capabilityerror', '',
                    $OUTPUT->notification(get_string('selectedcapabilitydoesntexit',
                                    'webservice', $service->requiredcapability)));
            $service->requiredcapability = "norequiredcapability";
        }

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        if (!empty($service->id)) {
            $buttonlabel = get_string('savechanges');
        } else {
            $buttonlabel = get_string('addaservice', 'webservice');
        }

        $this->add_action_buttons(true, $buttonlabel);

        $this->set_data($service);
    }

    function definition_after_data() {
        $mform = $this->_form;
        $service = $this->_customdata;

        if (!empty($service->component)) {
            // built-in components must not be modified except the enabled flag!!
            $mform->hardFreeze('name,requiredcapability,restrictedusers');
        }
    }

    function validation($data, $files) {
        global $DB;

        $errors = parent::validation($data, $files);

        // Add field validation check for duplicate name.
        if ($webservice = $DB->get_record('external_services', array('name' => $data['name']))) {
            if (empty($data['id']) || $webservice->id != $data['id']) {
                $errors['name'] = get_string('nameexists', 'webservice');
            }
        }

        // Add field validation check for duplicate shortname.
        // Allow duplicated "empty" shortnames.
        if (!empty($data['shortname'])) {
            if ($service = $DB->get_record('external_services', array('shortname' => $data['shortname']), '*', IGNORE_MULTIPLE)) {
                if (empty($data['id']) || $service->id != $data['id']) {
                    $errors['shortname'] = get_string('shortnametaken', 'webservice', $service->name);
                }
            }
        }

        return $errors;
    }

}

class external_service_functions_form extends moodleform {

    function definition() {
        global $CFG;

        $mform = $this->_form;
        $data = $this->_customdata;

        $mform->addElement('header', 'addfunction', get_string('addfunctions', 'webservice'));

        require_once($CFG->dirroot . "/webservice/lib.php");
        $webservicemanager = new webservice();
        $functions = $webservicemanager->get_not_associated_external_functions($data['id']);

        //we add the descriptions to the functions
        foreach ($functions as $functionid => $functionname) {
            //retrieve full function information (including the description)
            $function = external_api::external_function_info($functionname);
            if (empty($function->deprecated)) {
                $functions[$functionid] = $function->name . ':' . $function->description;
            } else {
                // Exclude the deprecated ones.
                unset($functions[$functionid]);
            }
        }

        $mform->addElement('searchableselector', 'fids', get_string('name'),
                $functions, array('multiple'));
        $mform->addRule('fids', get_string('required'), 'required', null, 'client');

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'action');
        $mform->setType('action', PARAM_ALPHANUMEXT);

        $this->add_action_buttons(true, get_string('addfunctions', 'webservice'));

        $this->set_data($data);
    }

}

class web_service_token_form extends moodleform {

    function definition() {
        global $USER, $DB, $CFG;

        $mform = $this->_form;
        $data = $this->_customdata;

        $mform->addElement('header', 'token', get_string('token', 'webservice'));

        if (empty($data->nouserselection)) {

            //check if the number of user is reasonable to be displayed in a select box
            $usertotal = $DB->count_records('user',
                    array('deleted' => 0, 'suspended' => 0, 'confirmed' => 1));

            if ($usertotal < 500) {
                list($sort, $params) = users_order_by_sql('u');
                // User searchable selector - return users who are confirmed, not deleted, not suspended and not a guest.
                $sql = 'SELECT u.id, ' . get_all_user_name_fields(true, 'u') . '
                        FROM {user} u
                        WHERE u.deleted = 0
                        AND u.confirmed = 1
                        AND u.suspended = 0
                        AND u.id != :siteguestid
                        ORDER BY ' . $sort;
                $params['siteguestid'] = $CFG->siteguest;
                $users = $DB->get_records_sql($sql, $params);
                $options = array();
                foreach ($users as $userid => $user) {
                    $options[$userid] = fullname($user);
                }
                $mform->addElement('searchableselector', 'user', get_string('user'), $options);
                $mform->setType('user', PARAM_INT);
            } else {
                //simple text box for username or user id (if two username exists, a form error is displayed)
                $mform->addElement('text', 'user', get_string('usernameorid', 'webservice'));
                $mform->setType('user', PARAM_RAW_TRIMMED);
            }
            $mform->addRule('user', get_string('required'), 'required', null, 'client');
        }

        //service selector
        $services = $DB->get_records('external_services');
        $options = array();
        $systemcontext = context_system::instance();
        foreach ($services as $serviceid => $service) {
            //check that the user has the required capability
            //(only for generation by the profile page)
            if (empty($data->nouserselection)
                    || empty($service->requiredcapability)
                    || has_capability($service->requiredcapability, $systemcontext, $USER->id)) {
                $options[$serviceid] = $service->name;
            }
        }
        $mform->addElement('select', 'service', get_string('service', 'webservice'), $options);
        $mform->addRule('service', get_string('required'), 'required', null, 'client');
        $mform->setType('service', PARAM_INT);

        $mform->addElement('text', 'iprestriction', get_string('iprestriction', 'webservice'));
        $mform->setType('iprestriction', PARAM_RAW_TRIMMED);

        $mform->addElement('date_selector', 'validuntil',
                get_string('validuntil', 'webservice'), array('optional' => true));
        $mform->setType('validuntil', PARAM_INT);

        $mform->addElement('hidden', 'action');
        $mform->setType('action', PARAM_ALPHANUMEXT);

        $this->add_action_buttons(true);

        $this->set_data($data);
    }

    function get_data() {
        global $DB;
        $data = parent::get_data();

        if (!empty($data) && !is_numeric($data->user)) {
            //retrieve username
            $user = $DB->get_record('user', array('username' => $data->user), 'id');
            $data->user = $user->id;
        }
        return $data;
    }

    function validation($data, $files) {
        global $DB;

        $errors = parent::validation($data, $files);

        if (is_numeric($data['user'])) {
            $searchtype = 'id';
        } else {
            $searchtype = 'username';
            //check the username is valid
            if (clean_param($data['user'], PARAM_USERNAME) != $data['user']) {
                $errors['user'] = get_string('invalidusername');
            }
        }

        if (!isset($errors['user'])) {
            $users = $DB->get_records('user', array($searchtype => $data['user']), '', 'id');

            //check that the user exists in the database
            if (count($users) == 0) {
                $errors['user'] = get_string('usernameoridnousererror', 'webservice');
            } else if (count($users) > 1) { //can only be a username search as id are unique
                $errors['user'] = get_string('usernameoridoccurenceerror', 'webservice');
            }
        }

        return $errors;
    }

}

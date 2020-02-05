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
 * The contact form to the site's Data Protection Officer
 *
 * @copyright 2018 onwards Jun Pataleta
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package tool_dataprivacy
 */

use tool_dataprivacy\api;
use tool_dataprivacy\data_request;
use tool_dataprivacy\local\helper;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

/**
 * The contact form to the site's Data Protection Officer
 *
 * @copyright 2018 onwards Jun Pataleta
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package tool_dataprivacy
 */
class tool_dataprivacy_data_request_form extends \core\form\persistent {

    /** @var string Name of the persistent class. */
    protected static $persistentclass = data_request::class;

    /** @var bool Flag to indicate whether this form is being rendered for managing data requests or for regular requests. */
    protected $manage = false;

    /**
     * Form definition.
     *
     * @throws coding_exception
     * @throws dml_exception
     */
    public function definition() {
        global $USER;
        $mform =& $this->_form;

        $this->manage = $this->_customdata['manage'];
        if ($this->manage) {
            $options = [
                'ajax' => 'tool_dataprivacy/form-user-selector',
                'valuehtmlcallback' => function($value) {
                    global $OUTPUT;

                    $userfieldsapi = \core_user\fields::for_name();
                    $allusernames = $userfieldsapi->get_sql('', false, '', '', false)->selects;
                    $fields = 'id, email, ' . $allusernames;
                    $user = \core_user::get_user($value, $fields);
                    $useroptiondata = [
                        'fullname' => fullname($user),
                        'email' => $user->email
                    ];
                    return $OUTPUT->render_from_template('tool_dataprivacy/form-user-selector-suggestion', $useroptiondata);
                }
            ];
            $mform->addElement('autocomplete', 'userid', get_string('requestfor', 'tool_dataprivacy'), [], $options);
            $mform->addRule('userid', null, 'required', null, 'client');

        } else {
            // Get users whom you are being a guardian to if your role has the capability to make data requests for children.
            if ($children = helper::get_children_of_user($USER->id)) {
                $useroptions = [
                    $USER->id => fullname($USER)
                ];
                foreach ($children as $key => $child) {
                    $useroptions[$key] = fullname($child);
                }
                $mform->addElement('autocomplete', 'userid', get_string('requestfor', 'tool_dataprivacy'), $useroptions);
                $mform->addRule('userid', null, 'required', null, 'client');

            } else {
                // Requesting for self.
                $mform->addElement('hidden', 'userid', $USER->id);
            }
        }

        $mform->setType('userid', PARAM_INT);

        // Subject access request type.
        $options = [];
        if ($this->manage || api::can_create_data_download_request_for_self()) {
            $allowfiltering = get_config('tool_dataprivacy', 'allowfiltering');
            if ($allowfiltering) {
                $options[api::DATAREQUEST_TYPE_EXPORT] = get_string('requesttypeexportallowfiltering', 'tool_dataprivacy');
            } else {
                $options[api::DATAREQUEST_TYPE_EXPORT] = get_string('requesttypeexport', 'tool_dataprivacy');
            }
        }
        $options[api::DATAREQUEST_TYPE_DELETE] = get_string('requesttypedelete', 'tool_dataprivacy');

        $mform->addElement('select', 'type', get_string('requesttype', 'tool_dataprivacy'), $options);
        $mform->addHelpButton('type', 'requesttype', 'tool_dataprivacy');

        // Request comments text area.
        $textareaoptions = ['cols' => 60, 'rows' => 10];
        $mform->addElement('textarea', 'comments', get_string('requestcomments', 'tool_dataprivacy'), $textareaoptions);
        $mform->addHelpButton('comments', 'requestcomments', 'tool_dataprivacy');

        // Action buttons.
        $this->add_action_buttons();

        $shouldfreeze = false;
        if ($this->manage) {
            $shouldfreeze = !api::can_create_data_deletion_request_for_other();
        } else {
            $shouldfreeze = !api::can_create_data_deletion_request_for_self();
            if ($shouldfreeze && !empty($useroptions)) {
                foreach ($useroptions as $userid => $useroption) {
                    if (api::can_create_data_deletion_request_for_children($userid)) {
                        $shouldfreeze = false;
                        break;
                    }
                }
            }
        }

        if ($shouldfreeze) {
            $mform->freeze('type');
        }
    }

    /**
     * Get the default data. Unset the default userid if managing data requests
     *
     * @return stdClass
     */
    protected function get_default_data() {
        $data = parent::get_default_data();
        if ($this->manage) {
            unset($data->userid);
        }

        return $data;
    }

    /**
     * Form validation.
     *
     * @param stdClass $data
     * @param array $files
     * @param array $errors
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     */
    public function extra_validation($data, $files, array &$errors) {
        global $USER;

        $validrequesttypes = [
            api::DATAREQUEST_TYPE_EXPORT,
            api::DATAREQUEST_TYPE_DELETE
        ];
        if (!in_array($data->type, $validrequesttypes)) {
            $errors['type'] = get_string('errorinvalidrequesttype', 'tool_dataprivacy');
        }

        $userid = $data->userid;

        if (api::has_ongoing_request($userid, $data->type)) {
            $errors['type'] = get_string('errorrequestalreadyexists', 'tool_dataprivacy');
        }

        // Check if current user can create data requests.
        if ($data->type == api::DATAREQUEST_TYPE_DELETE) {
            if ($userid == $USER->id) {
                if (!api::can_create_data_deletion_request_for_self()) {
                    $errors['type'] = get_string('errorcannotrequestdeleteforself', 'tool_dataprivacy');
                }
            } else if (!api::can_create_data_deletion_request_for_other()
                && !api::can_create_data_deletion_request_for_children($userid)) {
                $errors['type'] = get_string('errorcannotrequestdeleteforother', 'tool_dataprivacy');
            }
        } else if ($data->type == api::DATAREQUEST_TYPE_EXPORT) {
            if ($userid == $USER->id && !api::can_create_data_download_request_for_self()) {
                $errors['type'] = get_string('errorcannotrequestexportforself', 'tool_dataprivacy');
            }
        }

        return $errors;
    }
}

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
class tool_dataprivacy_data_request_form extends moodleform {

    /** @var bool Flag to indicate whether this form is being rendered for managing data requests or for regular requests. */
    protected $manage = false;

    /**
     * Form definition.
     *
     * @throws coding_exception
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

                    $allusernames = get_all_user_name_fields(true);
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
        $options = [
            api::DATAREQUEST_TYPE_EXPORT => get_string('requesttypeexport', 'tool_dataprivacy'),
            api::DATAREQUEST_TYPE_DELETE => get_string('requesttypedelete', 'tool_dataprivacy')
        ];
        $mform->addElement('select', 'type', get_string('requesttype', 'tool_dataprivacy'), $options);
        $mform->setType('type', PARAM_INT);
        $mform->addHelpButton('type', 'requesttype', 'tool_dataprivacy');

        // Request comments text area.
        $textareaoptions = ['cols' => 60, 'rows' => 10];
        $mform->addElement('textarea', 'comments', get_string('requestcomments', 'tool_dataprivacy'), $textareaoptions);
        $mform->setType('type', PARAM_ALPHANUM);
        $mform->addHelpButton('comments', 'requestcomments', 'tool_dataprivacy');

        // Action buttons.
        $this->add_action_buttons();

    }

    /**
     * Form validation.
     *
     * @param array $data
     * @param array $files
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     */
    public function validation($data, $files) {
        $errors = [];

        $validrequesttypes = [
            api::DATAREQUEST_TYPE_EXPORT,
            api::DATAREQUEST_TYPE_DELETE
        ];
        if (!in_array($data['type'], $validrequesttypes)) {
            $errors['type'] = get_string('errorinvalidrequesttype', 'tool_dataprivacy');
        }

        if (api::has_ongoing_request($data['userid'], $data['type'])) {
            $errors['type'] = get_string('errorrequestalreadyexists', 'tool_dataprivacy');
        }

        return $errors;
    }
}

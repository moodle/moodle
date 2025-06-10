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
 * Local plugin "bulkenrol" - Enrolment form
 *
 * @package   local_bulkenrol
 * @copyright 2017 Soon Systems GmbH on behalf of Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_bulkenrol;

use moodleform;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->libdir.'/formslib.php');

/**
 * Class bulkenrol_form
 * @package local_bulkenrol
 * @copyright 2017 Soon Systems GmbH on behalf of Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class bulkenrol_form extends moodleform {

    /**
     * Form definition. Abstract method - always override!
     */
    protected function definition() {
        global $CFG, $SESSION;

        require_once($CFG->dirroot.'/local/bulkenrol/lib.php');

        $mform = $this->_form;

        // Infotext.
        $msg = get_string('bulkenrol_form_intro', 'local_bulkenrol');
        $mform->addElement('html', '<div id="intro">'.$msg.'</div>');

        // Textarea for Emails.
        $mform->addElement('textarea', 'usermails',
                get_string('usermails', 'local_bulkenrol'), 'wrap="virtual" rows="10" cols="80"');
        $mform->addRule('usermails', null, 'required');
        $mform->addHelpButton('usermails', 'usermails', 'local_bulkenrol');

        // Add form content if the user came back to check his input.
        $localbulkenroleditlist = optional_param('editlist', 0, PARAM_ALPHANUMEXT);
        if (!empty($localbulkenroleditlist)) {
            $localbulkenroldata = $localbulkenroleditlist.'_data';
            if (!empty($localbulkenroldata) && !empty($SESSION->local_bulkenrol_inputs) &&
                    array_key_exists($localbulkenroldata, $SESSION->local_bulkenrol_inputs)) {
                $formdatatmp = $SESSION->local_bulkenrol_inputs[$localbulkenroldata];
                $mform->setDefault('usermails', $formdatatmp);
            }
        }

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_RAW);
        $mform->setDefault('id', $this->_customdata['courseid']);

        $this->add_action_buttons(true, get_string('enrol_users', 'local_bulkenrol'));
    }

    /**
     * Get each of the rules to validate its own fields
     *
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @return array of "element_name"=>"error_description" if there are errors,
     *         or an empty array if everything is OK (true allowed for backwards compatibility too).
     */
    public function validation($data, $files) {
        $retval = [];

        if (empty($data['usermails'])) {
            $retval['usermails'] = get_string('error_usermails_empty', 'local_bulkenrol');
        }

        return $retval;
    }
}

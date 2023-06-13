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
 * Local plugin "QubitsSite"
 *
 * @package   local_qubitssite
 * @author    Qubits Dev Team
 * @copyright 2023 <https://www.yardstickedu.com/>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->libdir . '/formslib.php');

class sitesearch_form extends moodleform {

    /**
     * Form definition. Abstract method - always override!
     */
    protected function definition() {
        global $CFG, $DB;

        $search = $this->_customdata['search'];

        $mform = $this->_form;
        $this->_form->updateAttributes(array('id' => 'sitefilters_form'));

        $orgrow[] = $mform->createElement('text', "search", '', array("placeholder" => 'Search'));
        $mform->setDefault("search", $search);

        $orgrow[] = &$mform->createElement('submit', 'submitbutton', 'Search');
        $orgrow[] = &$mform->createElement('cancel', 'resetbutton', 'Reset');

        if(has_capability('local/qubitssite:createtenantsite', context_system::instance())){
            $courseurl = new moodle_url($CFG->wwwroot . '/local/qubitssite/edit.php', array('returnto' => 'sitelisting'));
            $linkcontent = '<a class="btn btn-primary ml-auto" href="' . $courseurl . '">' . get_string('createsite', 'local_qubitssite') . '</a>';
            $orgrow[] = &$mform->createElement('static', 'mylink', get_string('createsite', 'local_qubitssite'), $linkcontent);
        }
        $mform->addGroup($orgrow, 'orgrow', '', '', false);
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
        $retval = array();

        return $retval;
    }

}

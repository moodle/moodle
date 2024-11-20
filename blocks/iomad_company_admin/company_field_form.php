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
 * @package   block_iomad_company_admin
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    //  It must be included from a Moodle page.
}

require_once($CFG->dirroot.'/lib/formslib.php');

class field_form extends moodleform {

    public $field;
    public $companyid = 0;

    public function __construct($param1, $param2, $companyid=null) {
        $this->companyid = $companyid;
        parent::__construct($param1, $param2);
    }

    // Define the form.
    public function definition () {
        global $CFG;

        $mform =& $this->_form;

        // Everything else is dependant on the data type.
        $datatype = $this->_customdata;
        require_once($CFG->dirroot.'/user/profile/field/'.$datatype.'/define.class.php');
        $newfield = 'profile_define_'.$datatype;
        $this->field = new $newfield();

        $strrequired = get_string('required');

        // Add some extra hidden fields.
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->setType('categoryid', PARAM_INT);
        $mform->addElement('hidden', 'action', 'editfield');
        $mform->setType('action', PARAM_ACTION);
        $mform->addElement('hidden', 'datatype', $datatype);
        $mform->setType('datatype', PARAM_ALPHA);

        $this->field->define_form($mform, $this->companyid);

        $this->add_action_buttons(true);
    }


    // Alter definition based on existing or submitted data.
    public function definition_after_data () {
        $mform =& $this->_form;
        $this->field->define_after_data($mform);
    }


    // Perform some moodle validation.
    public function validation($data, $files) {
        return $this->field->define_validate($data, $files);
    }

    public function editors() {
        return $this->field->define_editors();
    }
}


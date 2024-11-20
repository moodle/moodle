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
 * @package   block_iomad_microlearning
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_iomad_microlearning\forms;

use \moodleform;
use \company;

class group_edit_form extends moodleform {
    protected $companyid = 0;
    protected $company = null;
    protected $deptid = 0;
    protected $output = null;

    public function __construct($actionurl, $companyid, $groupid, $output) {
        global $CFG, $DB;

        $this->companyid = $companyid;
        $this->groupid = $groupid;
        $this->output = $output;
        $this->availablethreads = $DB->get_records_menu('microlearning_thread', ['companyid' => $companyid],  'name', 'id,name');

        parent::__construct($actionurl);
    }

    public function definition() {
        global $CFG, $output;

        $mform =& $this->_form;

        $mform->addElement('hidden', 'id', $this->groupid);
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'companyid', $this->companyid);
        $mform->setType('companyid', PARAM_INT);


        // Display group select html (create only)
        $mform->addElement('select', 'threadid', get_string('threadname', 'block_iomad_microlearning'), $this->availablethreads);

        $mform->addElement('text', 'name',
                            get_string('name'),
                            'maxlength = "254" size = "50"');
        $mform->addHelpButton('name', 'namehelp', 'block_iomad_microlearning');
        $mform->setType('name', PARAM_MULTILANG);

        $this->add_action_buttons();
    }

    public function validation($data, $files) {
        global $DB;

        $errors = array();

        if ($groupbyname = $DB->get_record('microlearning_thread_group', ['companyid' => $this->companyid, 'name' => trim($data['name']), 'threadid' => $data['threadid']])) {
            if ($groupbyname->id != $this->groupid) {
                $errors['name'] = get_string('nameinuse', 'block_iomad_microlearning');
            }
        }
        return $errors;
    }
}

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

namespace block_iomad_company_admin\forms;

use \company_moodleform;
use \company;

class department_edit_form extends company_moodleform {
    protected $selectedcompany = 0;
    protected $company = null;
    protected $deptid = 0;
    protected $output = null;

    public function __construct($actionurl, $companyid, $departmentid, $output, $chosenid=0, $action=0) {
        global $CFG, $DB;

        $this->selectedcompany = $companyid;
        $this->departmentid = $departmentid;
        $this->output = $output;
        $this->chosenid = $chosenid;
        $this->action = $action;
        if (!empty($departmentid)) {
            $this->department = $DB->get_record('department', array('id' => $departmentid));
            $this->parentid = $this->department->parent;
        } else {
            $this->parentid = 0;
        }
        parent::__construct($actionurl);
    }

    public function definition() {
        global $CFG, $output;

        $mform =& $this->_form;
        $company = new company($this->selectedcompany);

        if (!empty($this->departmentid)) {
            $ignorecurrentbranch = $this->departmentid;
        } else {
            $ignorecurrentbranch = false;
        }

        // Then show the fields about where this block appears.
        if ($this->action == 0) {
            $mform->addElement('header', 'header',
                                get_string('createdepartment', 'block_iomad_company_admin'));
        } else {
            $mform->addElement('header', 'header',
                                get_string('editdepartments', 'block_iomad_company_admin'));
        }
        $mform->addElement('hidden', 'departmentid', $this->departmentid);
        $mform->setType('departmentid', PARAM_INT);
        $mform->addElement('hidden', 'action', $this->action);
        $mform->setType('action', PARAM_INT);

        // Display department select html (create only)
        $mform->addElement('html', '<p>' . get_string('parentdepartment', 'block_iomad_company_admin') . '</p>');
        $output->display_tree_selector_form($company, $mform, $this->parentid);

        $mform->addElement('text', 'fullname',
                            get_string('fullnamedepartment', 'block_iomad_company_admin'),
                            'maxlength = "254" size = "50"');
        $mform->addHelpButton('fullname', 'fullnamedepartment', 'block_iomad_company_admin');
        $mform->addRule('fullname',
                        get_string('missingfullnamedepartment', 'block_iomad_company_admin'),
                        'required', null, 'client');
        $mform->setType('fullname', PARAM_MULTILANG);

        $mform->addElement('text', 'shortname',
                            get_string('shortnamedepartment', 'block_iomad_company_admin'),
                            'maxlength = "100" size = "20"');
        $mform->addHelpButton('shortname', 'shortnamedepartment', 'block_iomad_company_admin');
        $mform->addRule('shortname',
                         get_string('missingshortnamedepartment', 'block_iomad_company_admin'),
                         'required', null, 'client');
        $mform->setType('shortname', PARAM_MULTILANG);

        if (!$this->departmentid) {
            $mform->addElement('hidden', 'chosenid', $this->chosenid);
        } else {
            $mform->addElement('hidden', 'chosenid', $this->departmentid);
        }
        $mform->setType('chosenid', PARAM_INT);

        $this->add_action_buttons();
    }

    public function validation($data, $files) {
        global $DB;

        $errors = array();

        if ($departmentbyname = $DB->get_record('department', array('company' => $this->selectedcompany, 'shortname' => trim($data['shortname'])))) {
            if ($departmentbyname->id != $this->departmentid) {
                $errors['shortname'] = get_string('departmentnameinuse', 'block_iomad_company_admin');
            }
        }
        return $errors;
    }
}
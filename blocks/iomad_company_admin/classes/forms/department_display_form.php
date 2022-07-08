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
use \iomad;
use \context_system;
use \context_coursecat;

class department_display_form extends company_moodleform {
    protected $selectedepartmentdcompany = 0;
    protected $context = null;
    protected $company = null;
    protected $parentlevel = 0;
    protected $notice = '';

    public function __construct($actionurl, $companyid, $departmentid, $output, $chosenid=0, $action=0, $notice='') {
        global $CFG, $USER;

        $this->selectedcompany = $companyid;
        $this->context = context_coursecat::instance($CFG->defaultrequestcategory);
        $syscontext = context_system::instance();

        $this->company = new company($this->selectedcompany);
        $parentlevel = company::get_company_parentnode($this->company->id);
        $this->companydepartment = $parentlevel->id;
        if (iomad::has_capability('block/iomad_company_admin:edit_all_departments', $syscontext)) {
            $userhierarchylevel = $parentlevel->id;
        } else {
            $userlevels = $this->company->get_userlevel($USER);
            $userhierarchylevel = key($userlevels);
        }

        $this->departmentid = $userhierarchylevel;
        $this->output = $output;
        $this->chosenid = $chosenid;
        $this->action = $action;
        $this->parentlevel = $parentlevel->id;
        $this->notice = $notice;
        $this->syscontext = $syscontext;

        parent::__construct($actionurl);
    }

    public function definition() {
        global $CFG, $output;

        $mform =& $this->_form;
        if (!$parentnode = company::get_company_parentnode($this->company->id)) {
            // Company has not been set up, possibly from before an upgrade.
            company::initialise_departments($this->company->id);
        }

        if (!empty($this->departmentid)) {
            $departmentslist = company::get_all_subdepartments($this->departmentid);
        } else {
            $departmentslist = company::get_all_departments($this->company->id);
        }

        if (!empty($this->departmentid)) {
            $department = company::get_departmentbyid($this->departmentid);
        } else {
            $department = company::get_company_parentnode($this->selectedcompany);
        }
        $subdepartmentslist = company::get_subdepartments_list($department);
        $subdepartments = company::get_subdepartments($department);

        // Create the sub department checkboxes html.
        $subdepartmenthtml = "";

        if (!empty($subdepartmentslist)) {
            $subdepartmenthtml = "";
            foreach ($subdepartmentslist as $key => $value) {

                $subdepartmenthtml .= '<input type = "checkbox" name = "departmentids[]" value="'.
                                       $key.'" /> '.$value.'</br>';
            }
        }

        if (count($departmentslist) == 1) {
            $mform->addElement('html', "<h3>" . get_string('nodepartments', 'block_iomad_company_admin') . "</h3></br>");
        }

        if (!empty($this->action)) {
            $mform->addElement('html', '<p>' . get_string('parentdepartment', 'block_iomad_company_admin') . '</p>');
        }

        if (!empty($this->notice)) {
            $mform->addElement('html', '<div class="alert alert-warning">');
            $mform->addElement('html', $this->notice . '</div>');
        }

        $output->display_tree_selector_form($this->company, $mform);

        $buttonarray = array();
        $buttonarray[] = $mform->createElement('submit', 'create',
                                get_string('createdepartment', 'block_iomad_company_admin'));
        if (!empty($subdepartmentslist)) {
            $buttonarray[] = $mform->createElement('submit', 'edit',
                                get_string('editdepartments', 'block_iomad_company_admin'));
            $buttonarray[] = $mform->createElement('submit', 'delete',
                                get_string('deletedepartment', 'block_iomad_company_admin'));
            if (iomad::has_capability('block/iomad_company_admin:export_departments', $this->syscontext)) {
                $buttonarray[] = $mform->createElement('submit', 'export',
                                        get_string('exportdepartment', 'block_iomad_company_admin'));
            }
        } else {
            if (iomad::has_capability('block/iomad_company_admin:import_departments', $this->syscontext)) {
                $buttonarray[] = $mform->createElement('submit', 'import',
                                        get_string('importdepartment', 'block_iomad_company_admin'));
            }
        }
        $mform->addGroup($buttonarray, 'buttonarray', '', array(' '), false);
    }

    public function get_data() {
        $data = parent::get_data();
        return $data;
    }
}
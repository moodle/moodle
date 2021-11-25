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

namespace block_iomad_microlearning\forms;

use \company_moodleform;
use \company;
use \iomad;
use \context_system;
use \context_coursecat;
use \moodleform;

class group_display_form extends moodleform {
    protected $selectedepartmentdcompany = 0;
    protected $context = null;
    protected $company = null;
    protected $parentlevel = 0;
    protected $notice = '';

    public function __construct($actionurl, $companyid, $groupid) {
        global $CFG, $USER;

        $this->selectedcompany = $companyid;
        $syscontext = context_system::instance();

        $this->company = new company($this->selectedcompany);
        $this->groupid = $groupid;
        $this->syscontext = $syscontext;

        parent::__construct($actionurl);
    }

    public function definition() {
        global $CFG, $DB;

        $mform =& $this->_form;

        $groups = $DB->get_records_sql("SELECT mtg.*, mt.name as threadname FROM {microlearning_thread_group} WHERE companyid = :companyid", ['companyid' => $this->companyid]);

        if (!empty($subdepartmentslist)) {
            $subdepartmenthtml = "<p>".get_string('subdepartments', 'block_iomad_company_admin').
                               "</p>";
            foreach ($subdepartmentslist as $key => $value) {

                $subdepartmenthtml .= '<input type = "checkbox" name = "departmentids[]" value="'.
                                       $key.'" /> '.$value.'</br>';
            }
        }

        // Then show the fields about where this block appears.
        $mform->addElement('header', 'header',
                            get_string('companydepartment', 'block_iomad_company_admin').
                           $this->company->get_name());

        if (count($departmentslist) == 1) {
            $mform->addElement('html', "<h3>" . get_string('nodepartments', 'block_iomad_company_admin') . "</h3></br>");
        }

        $mform->addElement('html', '<p>' . get_string('parentdepartment', 'block_iomad_company_admin') . '</p>');

        if (!empty($this->notice)) {
            $mform->addElement('html', '<div class="alert alert-warning">');
            $mform->addElement('html', $this->notice . '</div>');
        }

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

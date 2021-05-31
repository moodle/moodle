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
use \potential_company_group_user_selector;
use \current_company_group_user_selector;
use \context_coursecat;
use \context_system;

class course_group_user_display_form extends company_moodleform {
    protected $courseid = 0;
    protected $context = null;
    protected $company = null;

    public function __construct($actionurl, $companyid, $courseid, $output, $chosenid=0, $action=0) {
        global $CFG, $USER;

        $this->selectedcompany = $companyid;
        $this->context = context_coursecat::instance($CFG->defaultrequestcategory);
        $syscontext = context_system::instance();

        $this->company = new company($this->selectedcompany);
        $this->courseid = $courseid;
        $this->output = $output;
        parent::__construct($actionurl);
    }

    public function definition() {
        global $CFG,$DB;

        $mform =& $this->_form;
        $company = $this->company;
        if (!empty($this->courseid)) {
            $coursegroups = $company->get_course_groups_menu($this->courseid);
        } else {
            $coursegroups = array();
        }


        // Then show the fields about where this block appears.
        $mform->addElement('header', 'header',
                            get_string('companygroupsusers', 'block_iomad_company_admin').
                           $company->get_name());

        if (empty($coursegroups)) {
            $mform->addElement('html', "<h3>" . get_string('nogroups', 'block_iomad_company_admin') . "</h3></br>");
        } else {
            $autooptions = array('setmultiple' => false,
                                 'noselectionstring' => '',
                                 'onchange' => 'this.form.submit()');
            $mform->addElement('autocomplete', 'selectedgroup', get_string('selectgroup', 'block_iomad_company_admin'), $coursegroups, $autooptions);

        }

        $mform->addElement('hidden', 'selectedcourse', $this->courseid);
        $mform->setType('selectedcourse', PARAM_INT);

        // Disable the onchange popup.
        $mform->disable_form_change_checker();
    }

    public function create_user_selectors() {
        if (!empty ($this->course)) {
            $options = array('context' => $this->context,
                             'companyid' => $this->selectedcompany,
                             'courseid' => $this->course,
                             'departmentid' => $this->departmentid,
                             'subdepartments' => $this->subhierarchieslist,
                             'parentdepartmentid' => $this->parentlevel);
            if (empty($this->potentialusers)) {
                $this->potentialusers = new potential_company_group_user_selector('potentialgroupusers', $options);
            }
            if (empty($this->currentusers)) {
                $this->currentusers = new current_company_group_user_selector('currentgroupusers', $options);
            }
        } else {
            return;
        }

    }

    public function get_data() {
        $data = parent::get_data();
        return $data;
    }

}
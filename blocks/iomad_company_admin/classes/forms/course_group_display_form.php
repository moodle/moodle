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
use \context_coursecat;
use \context_system;

class course_group_display_form extends company_moodleform {
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

        // Create the course group checkboxes html.
        $coursegrouphtml = "";
        unset($coursegroups[0]);
        if (!empty($coursegroups)) {
            $coursegrouphtml = "<p>".get_string('group').
                               "</p>";
            foreach ($coursegroups as $key => $value) {

                $coursegrouphtml .= '<input type = "radio" name = "groupids[]" value="'.
                                       $key.'" /> '.$value.'</br>';
            }
        }
        // Then show the fields about where this block appears.
        $mform->addElement('header', 'header',
                            get_string('companygroups', 'block_iomad_company_admin').
                           $company->get_name());

        if (empty($coursegroups)) {
            $mform->addElement('html', "<h3>" . get_string('nogroups', 'block_iomad_company_admin') . "</h3></br>");
        }
        $mform->addElement('html', $coursegrouphtml);
        $mform->addElement('hidden', 'selectedcourse', $this->courseid);
        $mform->setType('selectedcourse', PARAM_INT);

        $buttonarray = array();
        $buttonarray[] = $mform->createElement('submit', 'create',
                                get_string('creategroup', 'block_iomad_company_admin'));
        if (!empty($coursegroups)) {
            $buttonarray[] = $mform->createElement('submit', 'edit',
                                get_string('editgroup', 'block_iomad_company_admin'));
            $buttonarray[] = $mform->createElement('submit', 'delete',
                                get_string('deletegroup', 'block_iomad_company_admin'));
        }
        $mform->addGroup($buttonarray, 'buttonarray', '', array(' '), false);

        // Disable the onchange popup.
        $mform->disable_form_change_checker();
    }

    public function get_data() {
        $data = parent::get_data();
        return $data;
    }

}
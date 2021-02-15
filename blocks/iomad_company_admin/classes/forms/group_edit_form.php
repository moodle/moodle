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

class group_edit_form extends company_moodleform {
    protected $selectedcompany = 0;
    protected $company = null;
    protected $courseid = 0;
    protected $groupid = 0;
    protected $output = null;

    public function __construct($actionurl, $companyid, $courseid, $groupid, $output, $action = 0) {
        global $CFG;

        $this->selectedcompany = $companyid;
        $this->courseid = $courseid;
        $this->output = $output;
        $this->groupid = $groupid;
        $this->action = $action;
        $this->company = new company($companyid);

        parent::__construct($actionurl);
    }

    public function definition() {
        global $CFG;
        $mform =& $this->_form;

        // Then show the fields about where this block appears.
        if ($this->action == 0) {
            $mform->addElement('header', 'header',
                                get_string('creategroup', 'block_iomad_company_admin'));
        } else {
            $mform->addElement('header', 'header',
                                get_string('editgroup', 'block_iomad_company_admin'));
        }
        $mform->addElement('hidden', 'courseid', $this->courseid);
        $mform->setType('courseid', PARAM_INT);
        $mform->addElement('hidden', 'groupid', $this->groupid);
        $mform->setType('groupid', PARAM_INT);
        $mform->addElement('hidden', 'action', $this->action);
        $mform->setType('action', PARAM_INT);
        $mform->addElement('hidden', 'name');
        $mform->setType('name', PARAM_CLEAN);

        $mform->addElement('hidden', 'selectedcourse', $this->courseid);
        $mform->setType('selectedcourse', PARAM_INT);

        $mform->addElement('text', 'description',
                            get_string('groupdescription', 'block_iomad_company_admin'),
                            'maxlength = "200" size = "50"');
        $mform->addHelpButton('description', 'fullnamegroup', 'block_iomad_company_admin');
        $mform->addRule('description',
                        get_string('missinggroupdescription', 'block_iomad_company_admin'),
                        'required', null, 'client');
        $mform->setType('description', PARAM_MULTILANG);

        $this->add_action_buttons();
    }

}
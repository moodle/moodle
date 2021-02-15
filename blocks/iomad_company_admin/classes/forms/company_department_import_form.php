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

use \moodleform;

/**
 * Script to let a user import departments to a particular company.
 */

class company_department_import_form extends moodleform {

    function definition() {
        global $CFG;

        // thing you have to do
        $mform =& $this->_form;

        // header for main bit
        $mform->addElement( 'header', 'general', get_string('departmentimport','block_iomad_company_admin'));

        // file picker
        $mform->addElement('filepicker', 'importfile', get_string('file'), null, array( 'accepted_types'=>'json'));
        $mform->addRule('importfile', null, 'required');

        // buttons
        $this->add_action_buttons();
    }
}
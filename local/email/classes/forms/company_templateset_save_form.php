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
 * @package   local_email
 * @copyright 2023 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Script to let a user create a course for a particular company.
 */

namespace local_email\forms;

use \moodleform;
use \context_system;
use \block_iomad_commerce\helper;

// Set up the save form.
class company_templateset_save_form extends moodleform {

    public function __construct($actionurl,
                                $companyid,
                                $templatesetid) {

        $this->companyid = $companyid;
        $this->templatesetid = $templatesetid;

        parent::__construct($actionurl);
    }

    public function definition() {
        $this->_form->addElement('hidden', 'companyid', $this->companyid);
        $this->_form->setType('companyid', PARAM_INT);
    }

    public function definition_after_data() {

        $mform =& $this->_form;

        $mform->addElement('hidden', 'templatesetid', $this->templatesetid);
        $mform->setType('templatesetid', PARAM_INT);

        $mform->addElement('text',  'templatesetname', get_string('templatesetname', 'local_email'),
                           'maxlength="254" size="50"');
        $mform->addHelpButton('templatesetname', 'templatesetname', 'local_email');
        $mform->addRule('templatesetname', get_string('missingtemplatesetname', 'local_email'), 'required', null, 'client');
        $mform->setType('templatesetname', PARAM_MULTILANG);

        $this->add_action_buttons(true, get_string('savetemplateset', 'local_email'));
    }

    public function validation($data, $files) {
        global $DB;

        $errors = [];

        if ($DB->get_record_sql("SELECT id FROM {email_templateset}
                                 where " . $DB->sql_compare_text('templatesetname') ." = :templatesetname",
                                 array('templatesetname' => $data['templatesetname']))) {
            $errors['templatesetname'] = get_string('templatesetnamealreadyinuse', 'local_email');
        }

        return $errors;
    }
}

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

// Set up the template edit control form.
class email_template_edit_form extends moodleform {

    public function __construct($actionurl, $companyid, $templatename, $templatesetid, $ismodified = false) {
        global $DB;

        $this->langs = get_string_manager()->get_list_of_translations(true);
        $this->templatesetid = $templatesetid;
        $this->ismodified = $ismodified;

        parent::__construct($actionurl);
    }

    public function definition() {
        global $DB,$CFG, $USER;

        $mform =& $this->_form;

        $mform->addElement('hidden', 'templateid');
        $mform->addElement('hidden', 'templatename');
        $mform->addElement('hidden', 'templatesetid', $this->templatesetid);
        $mform->setType('templatename', PARAM_CLEAN);
        $mform->setType('templatesetid', PARAM_INT);
        $mform->setType('templateid', PARAM_INT);
        $mform->addElement('select', 'lang', '', $this->langs);
        $mform->setDefault('lang', $USER->lang);
        $buttonarr = array();
        $buttonarr[] = &$mform->createElement('submit', 'edit', get_string('edit'));
        $buttonarr[] = &$mform->createElement('submit', 'view', get_string('view'));
        if (!empty($this->ismodified)) {
            $buttonarr[] = &$mform->createElement('submit', 'reset', get_string('reset'));
        }
        $mform->addGroup($buttonarr, 'buttonar', '', array(' '), false);

    }
}
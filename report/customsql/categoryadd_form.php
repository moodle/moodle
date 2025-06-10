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
 * Form for editing custom SQL reporting categories,
 * called from addcategory if you have the report/customsql:managecategories capability.
 *
 * @package report_customsql
 * @copyright 2013 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');
require_once(dirname(__FILE__) . '/locallib.php');

class report_customsql_addcategory_form extends moodleform {

    // Form definition.
    public function definition() {
        global $CFG, $DB;
        $mform =& $this->_form;
        $categoryid = $this->_customdata['categoryid'];

        $editoroptions = array();

        if ($categoryid) {
            $strsubmit = get_string('savechanges');
        } else {
            $strsubmit = get_string('addcategory', 'report_customsql');
        }

        $mform->addElement('text', 'name', get_string('categoryname'), array('size' => '30'));
        $mform->addRule('name', get_string('required'), 'required', null);
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->setDefault('name', '');
        $mform->setType('name', PARAM_TEXT);

        $mform->addElement('hidden', 'id', 0);
        $mform->setType('id', PARAM_INT);

        $this->add_action_buttons(true, $strsubmit);
    }

    public function validation($data, $files) {
        global $DB;
        $errors = parent::validation($data, $files);
        if (!empty($data['name'])) {
            // Check for duplicate names.
            if (!isset($data['id'])) {
                $data['id'] = 0;// Ensure id to check against.
            }
            if ($DB->get_record_select('report_customsql_categories',
                    'name = ? AND id != ?', array($data['name'], $data['id']))) {
                $errors['name'] = get_string('categoryexists', 'report_customsql');
            }
        }
        return $errors;
    }
}

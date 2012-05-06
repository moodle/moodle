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
 * Cohort related management functions, this file needs to be included manually.
 *
 * @package    core
 * @subpackage cohort
 * @copyright  2010 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->dirroot . '/lib/formslib.php');

class cohort_edit_form extends moodleform {

    /**
     * Define the cohort edit form
     */
    public function definition() {

        $mform = $this->_form;
        $editoroptions = $this->_customdata['editoroptions'];
        $cohort = $this->_customdata['data'];

        $mform->addElement('text', 'name', get_string('name', 'cohort'), 'maxlength="254" size="50"');
        $mform->addRule('name', get_string('required'), 'required', null, 'client');
        $mform->setType('name', PARAM_MULTILANG);

        $options = $this->get_category_options($cohort->contextid);
        $mform->addElement('select', 'contextid', get_string('context', 'role'), $options);

        $mform->addElement('text', 'idnumber', get_string('idnumber', 'cohort'), 'maxlength="254" size="50"');
        $mform->setType('name', PARAM_RAW); // idnumbers are plain text, must not be changed

        $mform->addElement('editor', 'description_editor', get_string('description', 'cohort'), null, $editoroptions);
        $mform->setType('description_editor', PARAM_RAW);

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $this->add_action_buttons();

        $this->set_data($cohort);
    }

    public function validation($data, $files) {
        global $DB;

        $errors = parent::validation($data, $files);

        $idnumber = trim($data['idnumber']);
        if ($idnumber === '') {
            // fine, empty is ok

        } else if ($data['id']) {
            $current = $DB->get_record('cohort', array('id'=>$data['id']), '*', MUST_EXIST);
            if ($current->idnumber !== $idnumber) {
                if ($DB->record_exists('cohort', array('idnumber'=>$idnumber))) {
                    $errors['idnumber'] = get_string('duplicateidnumber', 'cohort');
                }
            }

        } else {
            if ($DB->record_exists('cohort', array('idnumber'=>$idnumber))) {
                $errors['idnumber'] = get_string('duplicateidnumber', 'cohort');
            }
        }

        return $errors;
    }

    protected function get_category_options($currentcontextid) {
        $displaylist = array();
        $parentlist = array();
        make_categories_list($displaylist, $parentlist, 'moodle/cohort:manage');
        $options = array();
        $syscontext = get_context_instance(CONTEXT_SYSTEM);
        if (has_capability('moodle/cohort:manage', $syscontext)) {
            $options[$syscontext->id] = print_context_name($syscontext);
        }
        foreach ($displaylist as $cid=>$name) {
            $context = get_context_instance(CONTEXT_COURSECAT, $cid, MUST_EXIST);
            $options[$context->id] = $name;
        }
        // always add current - this is not likely, but if the logic gets changed it might be a problem
        if (!isset($options[$currentcontextid])) {
            $context = get_context_instance_by_id($currentcontextid, MUST_EXIST);
            $options[$context->id] = print_context_name($syscontext);
        }
        return $options;
    }
}


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
 * The form used at the rubric editor page is defined here
 *
 * @package    gradingform
 * @subpackage rubric
 * @copyright  2011 Marina Glancy <marina@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/lib/formslib.php');
require_once(dirname(__FILE__).'/rubriceditor.php');
MoodleQuickForm::registerElementType('rubriceditor', $CFG->dirroot.'/grade/grading/form/rubric/rubriceditor.php', 'MoodleQuickForm_rubriceditor');

/**
 * Defines the rubric edit form
 */
class gradingform_rubric_editrubric extends moodleform {

    /**
     * Form elements definition
     */
    public function definition() {
        $form = $this->_form;

        $form->addElement('hidden', 'areaid');
        $form->setType('areaid', PARAM_INT);

        $form->addElement('hidden', 'returnurl');

        // name
        $form->addElement('text', 'name', get_string('name', 'gradingform_rubric'), array('size'=>52));
        $form->addRule('name', get_string('required'), 'required');
        $form->setType('name', PARAM_TEXT);

        // description
        $options = gradingform_rubric_controller::description_form_field_options($this->_customdata['context']);
        $form->addElement('editor', 'description_editor', get_string('description', 'gradingform_rubric'), null, $options);
        $form->setType('description_editor', PARAM_RAW);

        // rubric editor
        $element = $form->addElement('rubriceditor', 'rubric_criteria', 'Rubric 1'); // todo label
        $form->setType('rubric_criteria', PARAM_RAW);
        $form->addRule('rubric_criteria', '', 'rubriceditorcompleted'); //TODO how to add this rule automatically?????
        if (array_key_exists('freezerubric', $this->_customdata) && $this->_customdata['freezerubric']) {
            $element->freeze();
        }

        $this->add_action_buttons(true);
    }
}

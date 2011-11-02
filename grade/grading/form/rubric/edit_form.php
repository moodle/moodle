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
     * Form element definition
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

        // rubric completion status
        $choices = array();
        $choices[gradingform_controller::DEFINITION_STATUS_WORKINPROGRESS]    = get_string('statusworkinprogress', 'gradingform_rubric');
        $choices[gradingform_controller::DEFINITION_STATUS_PRIVATE]    = get_string('statusprivate', 'gradingform_rubric');
        $choices[gradingform_controller::DEFINITION_STATUS_PUBLIC]    = get_string('statuspublic', 'gradingform_rubric');
        $form->addElement('select', 'status', 'Current rubric status', $choices)->freeze();

        // rubric editor
        $element = $form->addElement('rubriceditor', 'rubric', get_string('rubric', 'gradingform_rubric'));
        $form->setType('rubric', PARAM_RAW);
        //$element->freeze(); // TODO freeze rubric editor if needed

        $buttonarray = array();
        $buttonarray[] = &$form->createElement('submit', 'saverubric', get_string('saverubric', 'gradingform_rubric'));
        $buttonarray[] = &$form->createElement('submit', 'saverubricdraft', get_string('saverubricdraft', 'gradingform_rubric'));
        $buttonarray[] = &$form->createElement('cancel');
        $form->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $form->closeHeaderBefore('buttonar');
    }

    /**
     * Form vlidation.
     * If there are errors return array of errors ("fieldname"=>"error message"),
     * otherwise true if ok.
     *
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @return array of "element_name"=>"error_description" if there are errors,
     *               or an empty array if everything is OK (true allowed for backwards compatibility too).
     */
    function validation($data, $files) {
        $err = parent::validation($data, $files);
        $err = array();
        $form = $this->_form;
        $rubricel = $form->getElement('rubric');
        if ($rubricel->non_js_button_pressed($data['rubric'])) {
            // if JS is disabled and button such as 'Add criterion' is pressed - prevent from submit
            $err['rubricdummy'] = 1;
        } else if (isset($data['saverubric']) && $data['saverubric']) {
            // If user attempts to make rubric active - it needs to be validated
            if ($rubricel->validate($data['rubric']) !== false) {
                $err['rubricdummy'] = 1;
            }
        }
        return $err;
    }

    /**
     * Return submitted data if properly submitted or returns NULL if validation fails or
     * if there is no submitted data.
     *
     * @return object submitted data; NULL if not valid or not submitted or cancelled
     */
    function get_data() {
        $data = parent::get_data();
        if (!empty($data->saverubric)) {
            $data->status = gradingform_controller::DEFINITION_STATUS_PUBLIC; // TODO ???
        } else if (!empty($data->saverubricdraft)) {
            $data->status = gradingform_controller::DEFINITION_STATUS_WORKINPROGRESS;
        }
        return $data;
    }
}

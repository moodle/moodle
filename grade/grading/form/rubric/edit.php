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
 * @package    gradingform
 * @subpackage rubric
 * @copyright  2011 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../../../config.php");
require_once("../../lib.php");
require_once($CFG->dirroot.'/lib/formslib.php');
require_once($CFG->dirroot."/grade/grading/form/rubric/rubriceditor.php");
MoodleQuickForm::registerElementType('rubriceditor', $CFG->dirroot.'/grade/grading/form/rubric/rubriceditor.php', 'MoodleQuickForm_rubriceditor');

$areaid   = required_param('areaid', PARAM_INT);          // Area ID

$manager = get_grading_manager($areaid);
$controller = $manager->get_controller('rubric');

$cm = $manager->get_cm();
if (! $assignment = $DB->get_record("assignment", array("id"=>$cm->instance))) {
    print_error('invalidid', 'assignment');
}
if (! $course = $DB->get_record("course", array("id"=>$assignment->course))) {
    print_error('coursemisconf', 'assignment');
}

require_login($course, false, $cm);
//require_capability('TODO', $context); // TODO

$url = new moodle_url('/grade/grading/form/rubric/edit.php', array('areaid' => $areaid));
$PAGE->set_url($url);
$title = get_string('definerubric', 'gradingform_rubric');
$PAGE->set_title($title);
$PAGE->set_heading($title);

$PAGE->requires->js('/grade/grading/form/rubric/js/rubriceditor.js');



class gradingform_rubric_editrubric extends moodleform {
    function definition() {
        $form = $this->_form;

        $form->addElement('hidden', 'areaid');
        $form->setType('areaid', PARAM_INT);

        // name
        $form->addElement('text', 'name', get_string('name', 'gradingform_rubric'), array('size'=>52));
        $form->addRule('name', get_string('required'), 'required');
        $form->setType('name', PARAM_TEXT);

        // description
        $options = array();//gradingform_definition_base::description_form_field_options($this->_customdata['areaid']);
        $form->addElement('editor', 'description_editor', get_string('description', 'gradingform_rubric'), null, $options);
        $form->setType('description_editor', PARAM_RAW);

        // rubric editor

        $element = $form->addElement('rubriceditor', 'rubric', 'Rubric 1');
        $form->setType('rubric', PARAM_RAW);
        $form->addRule('rubric', '', 'rubriceditorcompleted'); //TODO how to add this rule automatically?????
        if (array_key_exists('freezerubric', $this->_customdata) && $this->_customdata['freezerubric']) {
            $element->freeze();
        }

        $this->add_action_buttons(true);
    }

    function set_data($obj) {
        if ($obj instanceof gradingform_rubric_controller) {
            $properties = $obj->get_data_for_edit();
        } else {
            $properties = $obj;
        }
        parent::set_data($properties);
    }
}

//TODO freeze rubric editor if needed
$mform = new gradingform_rubric_editrubric(null, array('areaid' => $areaid, 'freezerubric' => optional_param('freeze', 0, PARAM_INT)));
$mform->set_data($controller);
if ($mform->is_submitted() && $mform->is_validated()) {
    $data = $mform->get_data();
    $data = $controller->postupdate_data($data);
    $controller->update($data);
    redirect($url);
}

echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();
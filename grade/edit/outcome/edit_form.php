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

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once $CFG->libdir.'/formslib.php';

class edit_outcome_form extends moodleform {
    public function definition() {
        global $CFG, $COURSE;
        $mform =& $this->_form;

        // visible elements
        $mform->addElement('header', 'general', get_string('outcomes', 'grades'));

        $mform->addElement('text', 'fullname', get_string('outcomefullname', 'grades'), 'size="40"');
        $mform->addRule('fullname', get_string('required'), 'required');
        $mform->setType('fullname', PARAM_TEXT);

        $mform->addElement('text', 'shortname', get_string('outcomeshortname', 'grades'), 'size="20"');
        $mform->addRule('shortname', get_string('required'), 'required');
        $mform->setType('shortname', PARAM_NOTAGS);

        $mform->addElement('advcheckbox', 'standard', get_string('outcomestandard', 'grades'));
        $mform->addHelpButton('standard', 'outcomestandard', 'grades');

        $options = array();

        $mform->addElement('selectwithlink', 'scaleid', get_string('scale'), $options, null,
            array('link' => $CFG->wwwroot.'/grade/edit/scale/edit.php?courseid='.$COURSE->id, 'label' => get_string('scalescustomcreate')));
        $mform->addHelpButton('scaleid', 'typescale', 'grades');
        $mform->addRule('scaleid', get_string('required'), 'required');

        $mform->addElement('editor', 'description_editor', get_string('description'), null, $this->_customdata['editoroptions']);


        // hidden params
        $mform->addElement('hidden', 'id', 0);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'courseid', 0);
        $mform->setType('courseid', PARAM_INT);

/// add return tracking info
        $gpr = $this->_customdata['gpr'];
        $gpr->add_mform_elements($mform);

//-------------------------------------------------------------------------------
        // buttons
        $this->add_action_buttons();
    }


/// tweak the form - depending on existing data
    function definition_after_data() {
        global $CFG;

        $mform =& $this->_form;

        // first load proper scales
        if ($courseid = $mform->getElementValue('courseid')) {
            $options = array();
            if ($scales = grade_scale::fetch_all_local($courseid)) {
                $options[-1] = '--'.get_string('scalescustom');
                foreach($scales as $scale) {
                    $options[$scale->id] = $scale->get_name();
                }
            }
            if ($scales = grade_scale::fetch_all_global()) {
                $options[-2] = '--'.get_string('scalesstandard');
                foreach($scales as $scale) {
                    $options[$scale->id] = $scale->get_name();
                }
            }
            $scale_el =& $mform->getElement('scaleid');
            $scale_el->load($options);

        } else {
            $options = array();
            if ($scales = grade_scale::fetch_all_global()) {
                foreach($scales as $scale) {
                    $options[$scale->id] = $scale->get_name();
                }
            }
            $scale_el =& $mform->getElement('scaleid');
            $scale_el->load($options);
        }

        if ($id = $mform->getElementValue('id')) {
            $outcome = grade_outcome::fetch(array('id'=>$id));
            $itemcount   = $outcome->get_item_uses_count();
            $coursecount = $outcome->get_course_uses_count();

            if ($itemcount) {
                $mform->hardFreeze('scaleid');
            }

            if (empty($courseid)) {
                $mform->hardFreeze('standard');

            } else if (empty($outcome->courseid) and !has_capability('moodle/grade:manage', get_context_instance(CONTEXT_SYSTEM))) {
                $mform->hardFreeze('standard');

            } else if ($coursecount and empty($outcome->courseid)) {
                $mform->hardFreeze('standard');
            }


        } else {
            if (empty($courseid) or !has_capability('moodle/grade:manage', get_context_instance(CONTEXT_SYSTEM))) {
                $mform->hardFreeze('standard');
            }
        }
    }

/// perform extra validation before submission
    function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if ($data['scaleid'] < 1) {
            $errors['scaleid'] = get_string('required');
        }

        if (!empty($data['standard']) and $scale = grade_scale::fetch(array('id'=>$data['scaleid']))) {
            if (!empty($scale->courseid)) {
                //TODO: localize
                $errors['scaleid'] = 'Can not use custom scale in global outcome!';
            }
        }

        return $errors;
    }


}



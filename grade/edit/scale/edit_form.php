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


require_once $CFG->libdir.'/formslib.php';

class edit_scale_form extends moodleform {
    function definition() {
        global $CFG;
        $mform =& $this->_form;

        // visible elements
        $mform->addElement('header', 'general', get_string('scale'));

        $mform->addElement('text', 'name', get_string('name'), 'size="40"');
        $mform->addRule('name', get_string('required'), 'required', null, 'client');
        $mform->setType('name', PARAM_TEXT);

        $mform->addElement('advcheckbox', 'standard', get_string('scalestandard'));
        $mform->setHelpButton('standard', array('scalestandard', get_string('scalestandard'), 'grade'));

        $mform->addElement('static', 'used', get_string('used'));

        $mform->addElement('textarea', 'scale', get_string('scale'), array('cols'=>50, 'rows'=>2));
        $mform->setHelpButton('scale', array('scales', get_string('scale')));
        $mform->addRule('scale', get_string('required'), 'required', null, 'client');
        $mform->setType('scale', PARAM_TEXT);

        $mform->addElement('htmleditor', 'description', get_string('description'), array('cols'=>80, 'rows'=>20));


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

        $courseid = $mform->getElementValue('courseid');

        if ($id = $mform->getElementValue('id')) {
            $scale = grade_scale::fetch(array('id'=>$id));
            $used = $scale->is_used();

            if ($used) {
                $mform->hardFreeze('scale');
            }

            if (empty($courseid)) {
                $mform->hardFreeze('standard');

            } else if (empty($scale->courseid) and !has_capability('moodle/course:managescales', get_context_instance(CONTEXT_SYSTEM))) {
                $mform->hardFreeze('standard');

            } else if ($used and !empty($scale->courseid)) {
                $mform->hardFreeze('standard');
            }

            $usedstr = $scale->is_used() ? get_string('yes') : get_string('no');
            $used_el =& $mform->getElement('used');
            $used_el->setValue($usedstr);

        } else {
            $mform->removeElement('used');
            if (empty($courseid) or !has_capability('moodle/course:managescales', get_context_instance(CONTEXT_SYSTEM))) {
                $mform->hardFreeze('standard');
            }
        }
    }

/// perform extra validation before submission
    function validation($data, $files) {
        global $CFG, $COURSE;

        $errors = parent::validation($data, $files);

        // we can not allow 2 scales with the same exact scale as this creates
        // problems for backup/restore

        $old = grade_scale::fetch(array('id'=>$data['id']));

        if (array_key_exists('standard', $data)) {
            if (empty($data['standard'])) {
                $courseid = $COURSE->id;
            } else {
                $courseid = 0;
            }

        } else {
            $courseid = $old->courseid;
        }

        if (array_key_exists('scale', $data)) {
            $count = count_records('scale', 'courseid', $courseid, 'scale', $data['scale']);

            if (empty($old->id) or $old->courseid != $courseid) {
                if ($count) {
                    $errors['scale'] = get_string('duplicatescale', 'grades');
                }

            } else if ($old->scale != $data['scale']) {
                if ($count) {
                    $errors['scale'] = get_string('duplicatescale', 'grades');
                }
            }

            $options = explode(',', $data['scale']);
            if (count($options) < 2) {
                $errors['scale'] = get_string('badlyformattedscale', 'grades');
            }
        }

        return $errors;
    }
}

?>

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

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/wq/edit_wq_form.php');
require_once($CFG->dirroot . '/question/type/truefalse/edit_truefalse_form.php');

class qtype_truefalsewiris_edit_form extends qtype_wq_edit_form {

    protected $base;

    protected function definition_inner($mform) {
        global $DB, $CFG, $PAGE;
        $PAGE->requires->js('/question/type/truefalsewiris/js/tf.js');

        parent::definition_inner($mform);

        $wirishdr = $mform->createElement('header',
                                          'wirishdr',
                                          get_string('truefalsewiris_wiris_variables', 'qtype_truefalsewiris'));

        $hdr = $mform->createElement('header', 'hdr', '');

        $wirisclassarray = array('class' => 'wirisauthoringfield wirisstudio wirismultichoice' .
                                             ' wirisvariables wirisauxiliarcas wirisauxiliartextinput');
        $wiristruefalse = $mform->createElement('text',
                                                'wiristruefalse',
                                                get_string('truefalsewiris_algorithm', 'qtype_truefalsewiris'),
                                                $wirisclassarray);

        $wiriscorrectstring = get_string('truefalsewiris_correct_answer_variable', 'qtype_truefalsewiris');
        $wiriscorrect = $mform->createElement('text', 'wirisoverrideanswer', $wiriscorrectstring, array());

        if ($CFG->version >= 2013051400) { // 2.5+.
            $mform->_collapsibleElements['wirishdr'] = false;
            $mform->_collapsibleElements['hdr'] = false;
        }

        $mform->insertElementBefore($wirishdr, 'correctanswer');
        $mform->insertElementBefore($wiristruefalse, 'correctanswer');
        $mform->insertElementBefore($wiriscorrect, 'correctanswer');
        $mform->insertElementBefore($hdr, 'correctanswer');

        $mform->addHelpButton('wirisoverrideanswer', 'truefalsewirisoverrideanswer_identifier', 'qtype_truefalsewiris');

        if (!empty($this->question->id)) {
            $wiris = $DB->get_record('qtype_wq', array('question' => $this->question->id));
        }
        if (!empty($wiris)) {
            $wiriscorrectvariable = $wiris->options;
        } else {
            $wiriscorrectvariable = '';
        }

        $indexelem = $mform->_elementIndex['feedbackfalse'];
        $mform->_elements[$indexelem]->_label = get_string('truefalsewiris_feedback_wrong_response', 'qtype_truefalsewiris');

        $indexelem = $mform->_elementIndex['feedbacktrue'];
        $mform->_elements[$indexelem]->_label = get_string('truefalsewiris_feedback_right_response', 'qtype_truefalsewiris');

        $defaultvalues = array();
        $defaultvalues['wirisoverrideanswer'] = $wiriscorrectvariable;
        $mform->setDefaults($defaultvalues);
    }

    public function qtype() {
        return 'truefalsewiris';
    }
}

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
require_once($CFG->dirroot . '/question/type/shortanswer/edit_shortanswer_form.php');

class qtype_shortanswerwiris_edit_form extends qtype_wq_edit_form {

    protected function definition_inner($mform) {
        global $CFG;

        parent::definition_inner($mform);

        // Hide usecase field.
        $usecasevalue = $mform->_elementIndex['usecase'];
        $mform->_elements[$usecasevalue]->_label = '';
        $mform->_elements[$usecasevalue]->_attributes['style'] = 'display:none';
        // Change Correct Answers instructions.
        $mform->getElement('answersinstruct')->setValue(get_string('filloutoneanswer', 'qtype_shortanswerwiris'));

        if ($CFG->version >= 2013051400) { // 2.5+.
            global $PAGE;

            // Change page type because is needed by the css.
            $PAGE->set_pagetype('question-type-shortanswer');

            foreach ($mform->_elementIndex as $key => $value) {
                if (substr($key, 0, 14) == 'answeroptions[') {
                    $elem = $mform->_elements[$value];
                    foreach ($elem->_elements as $k => $subel) {
                        if ($subel->_type == 'text') {
                            // Add class info in order to be recognized by Wiris Quizzes.
                            $classattributes = 'wirisauthoringfield wirisstudio wirisopenanswer';
                            $classattributes .= ' ' . 'wirisvariables wirisauxiliarcas wirisgradingfunction';
                            $classattributes .= ' ' . 'wirisauxiliartextinput wirisgraphicsyntax';
                            $subel->_attributes['class'] = $classattributes;
                            $subel->_attributes['wirisslot'] = "0";
                        }
                    }
                }
            }
        } else {
            foreach ($mform->_elementIndex as $key => $value) {
                if (substr($key, 0, 7) == 'answer[') {
                    $classattributes = 'wirisauthoringfield wirisstudio wirisopenanswer';
                    $classattributes .= ' ' . 'wirisvariables wirisauxiliarcas wirisgradingfunction';
                    $mform->_elements[$value]->_attributes['class'] = $classattributes;
                }
            }
        }
    }

    public function qtype() {
        return 'shortanswerwiris';
    }
}

class qtype_shortanswerwiris_helper_edit_form extends qtype_shortanswer_edit_form {
    protected function get_more_choices_string() {
        return get_string('shortanswerwiris_addanswers', 'qtype_shortanswerwiris');
    }
    protected function add_per_answer_fields(&$mform, $label, $gradeoptions,
                                             $minoptions = QUESTION_NUMANS_START,
                                             $addoptions = QUESTION_NUMANS_ADD) {
        return parent::add_per_answer_fields($mform, $label, $gradeoptions, 1, 1);
    }
}

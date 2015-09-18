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
 * Defines the hooks necessary to make the gapselect question type combinable
 *
 * @package    qtype_gapselect
 * @copyright  2013 The Open University
 * @author     Jamie Pratt <me@jamiep.org>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class qtype_combined_combinable_type_gapselect extends qtype_combined_combinable_type_base {

    protected $identifier = 'selectmenu';

    protected function extra_question_properties() {
        return $this->combined_feedback_properties();
    }

    protected function extra_answer_properties() {
        return array();
    }

    public function subq_form_fragment_question_option_fields() {
        return array('shuffleanswers' => false);
    }

    protected function transform_subq_form_data_to_full($subqdata) {
        $data = parent::transform_subq_form_data_to_full($subqdata);
        $data->choices = array();
        foreach ($data->answer as $anskey => $answer) {
            $data->choices[$anskey] = array('answer' => $answer, 'choicegroup' => '1');
        }
        return $this->add_per_answer_properties($data);
    }

    protected function third_param_for_default_question_text() {
        return '1';
    }
}

class qtype_combined_combinable_gapselect extends qtype_combined_combinable_accepts_numerical_param {

    /**
     * @var array of the correct choices taken from the third param of embedded code
     */
    protected $correctchoices = array();

    /**
     * @param moodleform      $combinedform
     * @param MoodleQuickForm $mform
     * @param                 $repeatenabled
     */
    public function add_form_fragment(moodleform $combinedform, MoodleQuickForm $mform, $repeatenabled) {
        $mform->addElement('advcheckbox', $this->form_field_name('shuffleanswers'), get_string('shuffle', 'qtype_gapselect'));

        $answerel = array($mform->createElement('text',
                                                $this->form_field_name('answer'),
                                                get_string('choicex', 'qtype_gapselect'),
                                                array('size' => 57, 'class' => 'tweakcss')));

        if ($this->questionrec !== null) {
            $countanswers = count($this->questionrec->options->answers);
        } else {
            $countanswers = 0;
        }

        if ($repeatenabled) {
            $defaultstartnumbers = QUESTION_NUMANS_START * 2;
            $repeatsatstart = max($defaultstartnumbers, $countanswers + QUESTION_NUMANS_ADD);
        } else {
            $repeatsatstart = $countanswers;
        }

        $combinedform->repeat_elements($answerel,
                                        $repeatsatstart,
                                        array(),
                                        $this->form_field_name('noofchoices'),
                                        $this->form_field_name('morechoices'),
                                        QUESTION_NUMANS_ADD,
                                        get_string('addmorechoiceblanks', 'qtype_gapselect'),
                                        true);
        $mform->setType($this->form_field_name('answer'), PARAM_RAW_TRIMMED);

    }

    public function data_to_form($context, $fileoptions) {
        $gapselectoptions = array('answer' => array());
        if ($this->questionrec !== null) {
            $answers = array();
            foreach ($this->questionrec->options->answers as $answer) {
                $gapselectoptions['answer'][] = $answer->answer;
            }
        }
        return parent::data_to_form($context, $fileoptions) + $gapselectoptions;
    }

    public function can_be_more_than_one_of_same_instance() {
        return true;
    }

    public function validate_third_param($thirdparam) {
        if ($thirdparam === null) {
            $qtype = $this->type->get_identifier();
            return get_string('err_you_must_provide_third_param', 'qtype_combined', $qtype);
        } else {
            return parent::validate_third_param($thirdparam);
        }
    }



    public function validate() {
        $errors = array();
        $nonemptyanswerblanks = array();
        foreach ($this->formdata->answer as $anskey => $answer) {
            if ('' !== trim($answer)) {
                $nonemptyanswerblanks[] = $anskey;
            }
        }

        foreach ($this->correctchoices as $correctchoice) {
            $answerindex = $correctchoice - 1;
            if (!isset($this->formdata->answer[$answerindex])) {
                $errors['questiontext'] = get_string('errormissingchoice', 'qtype_gapselect', $correctchoice);
            } else if ('' === trim($this->formdata->answer[$answerindex])) {
                $errors[$this->form_field_name("answer[{$answerindex}]")] =
                                                                get_string('errorblankchoice', 'qtype_gapselect', $correctchoice);
                $errors['questiontext'] = get_string('errormissingchoice', 'qtype_gapselect', $correctchoice);
            }
        }

        if (count($nonemptyanswerblanks) < 2) {
            $errors[$this->form_field_name("answer[0]")] = get_string('err_youneedmorechoices', 'qtype_combined');
        }
        return $errors;
    }

    protected function code_construction_instructions() {
        return get_string('correct_choice_embed_code', 'qtype_combined', $this->get_string_hash());
    }

    public function save($contextid) {
        $this->formdata->questiontext = array();
        $this->formdata->questiontext['text'] = '';
        foreach ($this->correctchoices as $correctchoice) {
            $this->formdata->questiontext['text'] .= " [[$correctchoice]] ";
        }
        parent::save($contextid);
    }

    public function store_third_param($thirdparam) {
        $this->correctchoices[] = $thirdparam;
    }

    protected function get_third_params() {
        return $this->correctchoices;
    }

    public function has_submitted_data() {
        return $this->submitted_data_array_not_empty('answer') || parent::has_submitted_data();
    }
}

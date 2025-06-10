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
 * Defines the hooks necessary to make the oumultiresponse question type combinable
 *
 * @package   qtype_oumultiresponse
 * @copyright  2013 The Open University
 * @author     Jamie Pratt <me@jamiep.org>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class qtype_combined_combinable_type_oumultiresponse extends qtype_combined_combinable_type_base {

    protected $identifier = 'multiresponse';

    protected function extra_question_properties() {
        return $this->combined_feedback_properties();
    }

    protected function extra_answer_properties() {
        return array('feedback' => array('text' => '', 'format' => FORMAT_PLAIN));
    }

    public function subq_form_fragment_question_option_fields() {
        return [
            'shuffleanswers' => (bool) get_config('qtype_combined', 'shuffleanswers_multiresponse'),
            'answernumbering' => null,
        ];
    }

    protected function transform_subq_form_data_to_full($subqdata) {
        $data = parent::transform_subq_form_data_to_full($subqdata);
        foreach ($data->answer as $anskey => $answer) {
            $data->answer[$anskey] = array('text' => $answer['text'], 'format' => $answer['format']);
        }
        return $this->add_per_answer_properties($data);
    }

    protected function third_param_for_default_question_text() {
        return 'v';
    }
}

class qtype_combined_combinable_oumultiresponse extends qtype_combined_combinable_accepts_vertical_or_horizontal_layout_param {

    /**
     * @param moodleform      $combinedform
     * @param MoodleQuickForm $mform
     * @param                 $repeatenabled
     */
    public function add_form_fragment(moodleform $combinedform, MoodleQuickForm $mform, $repeatenabled) {
        $mform->addElement('advcheckbox', $this->form_field_name('shuffleanswers'),
            get_string('shuffle', 'qtype_combined'));
            $mform->setDefault($this->form_field_name('shuffleanswers'),
                get_config('qtype_combined', 'shuffleanswers_multiresponse'));

        $mform->addElement('select', $this->form_field_name('answernumbering'),
                get_string('answernumbering', 'qtype_multichoice'), qtype_multichoice::get_numbering_styles());
        $mform->setDefault($this->form_field_name('answernumbering'),
                get_config('qtype_combined', 'answernumbering_multiresponse'));

        $answerels = array();
        $answerels[] = $mform->createElement('editor', $this->form_field_name('answer'),
                get_string('choiceno', 'qtype_multichoice', '{no}'), ['rows' => 1]);
        $mform->setType($this->form_field_name('answer'), PARAM_RAW);
        $answerels[] = $mform->createElement('advcheckbox', $this->form_field_name('correctanswer'),
                get_string('correct', 'question'), get_string('correct', 'question'));

        $answergroupel = $mform->createElement('group',
                $this->form_field_name('answergroup'),
                get_string('choiceno', 'qtype_multichoice', '{no}'),
                $answerels, null, false);

        if (isset($this->questionrec->options)) {
            $repeatsatstart = count($this->questionrec->options->answers);
        } else {
            $repeatsatstart = max(5, QUESTION_NUMANS_START);
        }

        $combinedform->repeat_elements(array($answergroupel),
            $repeatsatstart,
            array(),
            $this->form_field_name('noofchoices'),
            $this->form_field_name('morechoices'),
            QUESTION_NUMANS_ADD,
            get_string('addmorechoiceblanks', 'qtype_gapselect'),
            true);
    }

    public function data_to_form($context, $fileoptions) {
        $mroptions = array('answer' => array(), 'correctanswer' => array());
        if ($this->questionrec !== null) {
            foreach ($this->questionrec->options->answers as $questionrecanswer) {
                $mroptions['answer'][] = [
                    'text' => $questionrecanswer->answer,
                    'format' => $questionrecanswer->answerformat,
                ];
                $mroptions['correctanswer'][] = $questionrecanswer->fraction > 0;
            }
        }
        return parent::data_to_form($context, $fileoptions) + $mroptions;
    }

    public function validate() {
        $errors = array();
        $nonemptyanswerblanks = array();
        foreach ($this->formdata->answer as $anskey => $answer) {
            $answer = $answer['text'];
            if ('' !== trim($answer)) {
                $nonemptyanswerblanks[] = $anskey;
            } else if ($this->formdata->correctanswer[$anskey]) {
                $errors[$this->form_field_name("answergroup[{$anskey}]")] = get_string('err_correctanswerblank',
                                                                                       'qtype_oumultiresponse');
            }
        }
        if (count($nonemptyanswerblanks) < 2) {
            $errors[$this->form_field_name("answergroup[0]")] = get_string('err_youneedmorechoices', 'qtype_oumultiresponse');
        }
        if (count(array_filter($this->formdata->correctanswer)) === 0) {
            $errors[$this->form_field_name("answergroup[0]")] = get_string('err_nonecorrect', 'qtype_oumultiresponse');
        }
        return $errors;
    }

    public function has_submitted_data() {
        return $this->submitted_data_array_not_empty('correctanswer') ||
                $this->html_field_has_submitted_data($this->form_field_name('answer')) ||
                parent::has_submitted_data();
    }
}

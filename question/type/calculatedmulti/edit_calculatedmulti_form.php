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
 * Defines the editing form for calculated multiple-choice questions.
 *
 * @package    qtype
 * @subpackage calculatedmulti
 * @copyright  2007 Jamie Pratt me@jamiep.org
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Calculated multiple-choice question editing form.
 *
 * @copyright  2007 Jamie Pratt me@jamiep.org
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_calculatedmulti_edit_form extends question_edit_form {
    /**
     * Handle to the question type for this question.
     *
     * @var question_calculatedmulti_qtype
     */
    public $qtypeobj;
    public $questiondisplay;
    public $initialname = '';
    public $reload = false;

    public function __construct($submiturl, $question, $category,
            $contexts, $formeditable = true) {
        $this->question = $question;
        $this->qtypeobj = question_bank::get_qtype('calculatedmulti');
        $this->reload = optional_param('reload', false, PARAM_BOOL);
        if (!$this->reload) {
            // Use database data as this is first pass.
            if (isset($this->question->id)) {
                // Remove prefix #{..}# if exists.
                $this->initialname = $question->name;
                $question->name = question_bank::get_qtype('calculated')
                        ->clean_technical_prefix_from_question_name($question->name);
            }
        }
        parent::__construct($submiturl, $question, $category, $contexts, $formeditable);
    }

    protected function can_preview() {
        return false; // Generally not possible for calculated multi-choice questions on this page.
    }

    public function get_per_answer_fields($mform, $label, $gradeoptions,
            &$repeatedoptions, &$answersoption) {
        $repeated = array();
        $answeroptions = array();
        $answeroptions[] = $mform->createElement('text', 'answer',
                $label, array('size' => 50));
        $answeroptions[] = $mform->createElement('select', 'fraction',
                get_string('gradenoun'), $gradeoptions);
        $repeated[] = $mform->createElement('group', 'answeroptions',
                 $label, $answeroptions, null, false);

        // Added answeroptions help button in definition_inner() after called to add_per_answer_fields.

        $repeatedoptions['answer']['type'] = PARAM_RAW;
        $repeatedoptions['fraction']['default'] = 0;
        $answersoption = 'answers';

        $mform->setType('answer', PARAM_NOTAGS);

        $repeated[] = $mform->createElement('hidden', 'tolerance');
        $repeated[] = $mform->createElement('hidden', 'tolerancetype', 1);
        $repeatedoptions['tolerance']['type'] = PARAM_FLOAT;
        $repeatedoptions['tolerance']['default'] = 0.01;
        $repeatedoptions['tolerancetype']['type'] = PARAM_INT;

        // Create display group.
        $answerdisplay = array();
        $answerdisplay[] =  $mform->createElement('select', 'correctanswerlength',
                get_string('answerdisplay', 'qtype_calculated'), range(0, 9));
        $repeatedoptions['correctanswerlength']['default'] = 2;

        $answerlengthformats = array(
            '1' => get_string('decimalformat', 'qtype_numerical'),
            '2' => get_string('significantfiguresformat', 'qtype_calculated')
        );
        $answerdisplay[] = $mform->createElement('select', 'correctanswerformat',
                get_string('correctanswershowsformat', 'qtype_calculated'), $answerlengthformats);
        $repeated[] = $mform->createElement('group', 'answerdisplay',
                 get_string('answerdisplay', 'qtype_calculated'), $answerdisplay, null, false);

        // Add feedback.
        $repeated[] = $mform->createElement('editor', 'feedback',
                get_string('feedback', 'question'), null, $this->editoroptions);

        return $repeated;
    }

    protected function definition_inner($mform) {

        $label = get_string('sharedwildcards', 'qtype_calculated');
        $mform->addElement('hidden', 'initialcategory', 1);
        $mform->addElement('hidden', 'reload', 1);
        $mform->setType('initialcategory', PARAM_INT);
        $mform->setType('reload', PARAM_BOOL);

        $html2 = '';
        $mform->insertElementBefore(
                $mform->createElement('static', 'listcategory', $label, $html2), 'name');
        if (isset($this->question->id)) {
            $mform->insertElementBefore($mform->createElement('static', 'initialname',
                    get_string('questionstoredname', 'qtype_calculated'),
                    format_string($this->initialname)), 'name');
        };
        $addfieldsname = 'updatecategory';
        $addstring = get_string('updatecategory', 'qtype_calculated');
        $mform->registerNoSubmitButton($addfieldsname);

        $mform->insertElementBefore(
                $mform->createElement('submit', $addfieldsname, $addstring), 'listcategory');
        $mform->registerNoSubmitButton('createoptionbutton');
        $mform->addElement('hidden', 'multichoice', 1);
        $mform->setType('multichoice', PARAM_INT);

        $menu = array(get_string('answersingleno', 'qtype_multichoice'),
                get_string('answersingleyes', 'qtype_multichoice'));
        $mform->addElement('select', 'single',
                get_string('answerhowmany', 'qtype_multichoice'), $menu);
        $mform->setDefault('single', 1);

        $mform->addElement('advcheckbox', 'shuffleanswers',
                get_string('shuffleanswers', 'qtype_multichoice'), null, null, array(0, 1));
        $mform->addHelpButton('shuffleanswers', 'shuffleanswers', 'qtype_multichoice');
        $mform->setDefault('shuffleanswers', 1);

        $numberingoptions = question_bank::get_qtype('multichoice')->get_numbering_styles();
        $mform->addElement('select', 'answernumbering',
                get_string('answernumbering', 'qtype_multichoice'), $numberingoptions);
        $mform->setDefault('answernumbering', 'abc');

        $this->add_per_answer_fields($mform, get_string('choiceno', 'qtype_multichoice', '{no}'),
                question_bank::fraction_options_full(), max(5, QUESTION_NUMANS_START));
        $mform->addHelpButton('answeroptions[0]', 'answeroptions', 'qtype_calculatedmulti');

        $repeated = array();
        $nounits = optional_param('nounits', 1, PARAM_INT);
        $mform->addElement('hidden', 'nounits', $nounits);
        $mform->setType('nounits', PARAM_INT);
        $mform->setConstants(array('nounits'=>$nounits));
        for ($i = 0; $i < $nounits; $i++) {
            $mform->addElement('hidden', 'unit'."[{$i}]",
                    optional_param("unit[{$i}]", '', PARAM_NOTAGS));
            $mform->setType('unit'."[{$i}]", PARAM_NOTAGS);
            $mform->addElement('hidden', 'multiplier'."[{$i}]",
                    optional_param("multiplier[{$i}]", '', PARAM_FLOAT));
            $mform->setType("multiplier[{$i}]", PARAM_FLOAT);
        }

        $this->add_combined_feedback_fields(true);
        $mform->disabledIf('shownumcorrect', 'single', 'eq', 1);

        $this->add_interactive_settings(true, true);

        // Hidden elements.
        $mform->addElement('hidden', 'synchronize', '');
        $mform->setType('synchronize', PARAM_INT);
        if (isset($this->question->options) && isset($this->question->options->synchronize)) {
            $mform->setDefault('synchronize', $this->question->options->synchronize);
        } else {
            $mform->setDefault('synchronize', 0);
        }
        $mform->addElement('hidden', 'wizard', 'datasetdefinitions');
        $mform->setType('wizard', PARAM_ALPHA);
    }

    public function data_preprocessing($question) {
        $question = parent::data_preprocessing($question);
        $question = $this->data_preprocessing_answers($question, false);
        $question = $this->data_preprocessing_combined_feedback($question, true);
        $question = $this->data_preprocessing_hints($question, true, true);

        if (isset($question->options)) {
            $question->synchronize     = $question->options->synchronize;
            $question->single          = $question->options->single;
            $question->answernumbering = $question->options->answernumbering;
            $question->shuffleanswers  = $question->options->shuffleanswers;
        }

        return $question;
    }

    protected function data_preprocessing_answers($question, $withanswerfiles = false) {
        $question = parent::data_preprocessing_answers($question, $withanswerfiles);
        if (empty($question->options->answers)) {
            return $question;
        }

        $key = 0;
        foreach ($question->options->answers as $answer) {
            // See comment in the parent method about this hack.
            unset($this->_form->_defaultValues["tolerance[{$key}]"]);
            unset($this->_form->_defaultValues["tolerancetype[{$key}]"]);
            unset($this->_form->_defaultValues["correctanswerlength[{$key}]"]);
            unset($this->_form->_defaultValues["correctanswerformat[{$key}]"]);

            $question->tolerance[$key]           = $answer->tolerance;
            $question->tolerancetype[$key]       = $answer->tolerancetype;
            $question->correctanswerlength[$key] = $answer->correctanswerlength;
            $question->correctanswerformat[$key] = $answer->correctanswerformat;
            $key++;
        }

        return $question;
    }

    /**
     * Validate the equations in the some question content.
     * @param array $errors where errors are being accumulated.
     * @param string $field the field being validated.
     * @param string $text the content of that field.
     * @return array the updated $errors array.
     */
    protected function validate_text($errors, $field, $text) {
        $problems = qtype_calculated_find_formula_errors_in_text($text);
        if ($problems) {
            $errors[$field] = $problems;
        }
        return $errors;
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        // Verifying for errors in {=...} in question text.
        $errors = $this->validate_text($errors, 'questiontext', $data['questiontext']['text']);
        $errors = $this->validate_text($errors, 'generalfeedback', $data['generalfeedback']['text']);
        $errors = $this->validate_text($errors, 'correctfeedback', $data['correctfeedback']['text']);
        $errors = $this->validate_text($errors, 'partiallycorrectfeedback', $data['partiallycorrectfeedback']['text']);
        $errors = $this->validate_text($errors, 'incorrectfeedback', $data['incorrectfeedback']['text']);

        $answers = $data['answer'];
        $answercount = 0;
        $maxgrade = false;
        $possibledatasets = $this->qtypeobj->find_dataset_names($data['questiontext']['text']);
        $mandatorydatasets = array();
        foreach ($answers as $key => $answer) {
            $mandatorydatasets += $this->qtypeobj->find_dataset_names($answer);
        }
        if (count($mandatorydatasets) == 0) {
            foreach ($answers as $key => $answer) {
                $errors['answeroptions['.$key.']'] =
                        get_string('atleastonewildcard', 'qtype_calculated');
            }
        }

        $totalfraction = 0;
        $maxfraction = -1;
        foreach ($answers as $key => $answer) {
            $trimmedanswer = trim($answer);
            $fraction = (float) $data['fraction'][$key];
            if (empty($trimmedanswer) && $trimmedanswer != '0' && empty($fraction)) {
                continue;
            }
            if (empty($trimmedanswer)) {
                $errors['answeroptions['.$key.']'] = get_string('errgradesetanswerblank', 'qtype_multichoice');
            }
            if ($trimmedanswer != '' || $answercount == 0) {
                // Verifying for errors in {=...} in answer text.
                $errors = $this->validate_text($errors, 'answeroptions[' . $key . ']', $answer);
                $errors = $this->validate_text($errors, 'feedback[' . $key . ']',
                        $data['feedback'][$key]['text']);
            }

            if ($trimmedanswer != '') {
                if ('2' == $data['correctanswerformat'][$key] &&
                        '0' == $data['correctanswerlength'][$key]) {
                    $errors['correctanswerlength['.$key.']'] =
                            get_string('zerosignificantfiguresnotallowed', 'qtype_calculated');
                }
                if (!is_numeric($data['tolerance'][$key])) {
                    $errors['tolerance['.$key.']'] =
                            get_string('xmustbenumeric', 'qtype_numerical',
                                get_string('acceptederror', 'qtype_numerical'));
                }
                if ($data['fraction'][$key] > 0) {
                    $totalfraction += $data['fraction'][$key];
                }
                if ($data['fraction'][$key] > $maxfraction) {
                    $maxfraction = $data['fraction'][$key];
                }

                $answercount++;
            }
        }
        if ($answercount == 0) {
            $errors['answeroptions[0]'] = get_string('notenoughanswers', 'qtype_multichoice', 2);
            $errors['answeroptions[1]'] = get_string('notenoughanswers', 'qtype_multichoice', 2);
        } else if ($answercount == 1) {
            $errors['answeroptions[1]'] = get_string('notenoughanswers', 'qtype_multichoice', 2);

        }
        // Perform sanity checks on fractional grades.
        if ($data['single']== 1 ) {
            if ($maxfraction != 1) {
                $errors['answeroptions[0]'] = get_string('errfractionsnomax', 'qtype_multichoice',
                        $maxfraction * 100);
            }
        } else {
            $totalfraction = round($totalfraction, 2);
            if ($totalfraction != 1) {
                $totalfraction = $totalfraction * 100;
                $errors['answeroptions[0]'] =
                        get_string('errfractionsaddwrong', 'qtype_multichoice', $totalfraction);
            }
        }
        return $errors;
    }

    public function qtype() {
        return 'calculatedmulti';
    }
}

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
        if (1 == optional_param('reload', '', PARAM_INT)) {
            $this->reload = true;
        } else {
            $this->reload = false;
        }
        if (!$this->reload) {
            // use database data as this is first pass
            if (isset($this->question->id)) {
                // remove prefix #{..}# if exists
                $this->initialname = $question->name;
                $regs= array();
                if (preg_match('~#\{([^[:space:]]*)#~', $question->name , $regs)) {
                    $question->name = str_replace($regs[0], '', $question->name);
                };
            }
        }
        parent::__construct($submiturl, $question, $category, $contexts, $formeditable);
    }

    public function get_per_answer_fields($mform, $label, $gradeoptions,
            &$repeatedoptions, &$answersoption) {
        $repeated = array();
        $repeated[] = $mform->createElement('header', 'answerhdr', $label);
        $repeated[] = $mform->createElement('text', 'answer',
                get_string('answer', 'question'), array('size' => 50));
        $repeated[] = $mform->createElement('select', 'fraction',
                get_string('grade'), $gradeoptions);
        $repeated[] = $mform->createElement('editor', 'feedback',
                get_string('feedback', 'question'), null, $this->editoroptions);
        $repeatedoptions['answer']['type'] = PARAM_RAW;
        $repeatedoptions['fraction']['default'] = 0;
        $answersoption = 'answers';

        $mform->setType('answer', PARAM_NOTAGS);

        $addrepeated = array();
        $addrepeated[] = $mform->createElement('hidden', 'tolerance');
        $addrepeated[] = $mform->createElement('hidden', 'tolerancetype', 1);
        $repeatedoptions['tolerance']['type'] = PARAM_NUMBER;
        $repeatedoptions['tolerance']['default'] = 0.01;

        $addrepeated[] =  $mform->createElement('select', 'correctanswerlength',
                get_string('correctanswershows', 'qtype_calculated'), range(0, 9));
        $repeatedoptions['correctanswerlength']['default'] = 2;

        $answerlengthformats = array(
            '1' => get_string('decimalformat', 'qtype_numerical'),
            '2' => get_string('significantfiguresformat', 'qtype_calculated')
        );
        $addrepeated[] = $mform->createElement('select', 'correctanswerformat',
                get_string('correctanswershowsformat', 'qtype_calculated'), $answerlengthformats);
        array_splice($repeated, 3, 0, $addrepeated);
        $repeated[1]->setLabel('...<strong>{={x}+..}</strong>...');

        return $repeated;
    }

    protected function definition_inner($mform) {

        $label = get_string('sharedwildcards', 'qtype_calculated');
        $mform->addElement('hidden', 'initialcategory', 1);
        $mform->addElement('hidden', 'reload', 1);
        $mform->setType('initialcategory', PARAM_INT);

        $html2 = '';
        $mform->insertElementBefore(
                $mform->createElement('static', 'listcategory', $label, $html2), 'name');
        if (isset($this->question->id)) {
            $mform->insertElementBefore($mform->createElement('static', 'initialname',
                    get_string('questionstoredname', 'qtype_calculated'),
                    $this->initialname), 'name');
        };
        $addfieldsname = 'updatecategory';
        $addstring = get_string('updatecategory', 'qtype_calculated');
        $mform->registerNoSubmitButton($addfieldsname);
        $this->editasmultichoice = 1;

        $mform->insertElementBefore(
                $mform->createElement('submit', $addfieldsname, $addstring), 'listcategory');
        $mform->registerNoSubmitButton('createoptionbutton');
        $mform->addElement('hidden', 'multichoice', $this->editasmultichoice);
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

        $repeated = array();
        //   if ($this->editasmultichoice == 1) {
        $nounits = optional_param('nounits', 1, PARAM_INT);
        $mform->addElement('hidden', 'nounits', $nounits);
        $mform->setType('nounits', PARAM_INT);
        $mform->setConstants(array('nounits'=>$nounits));
        for ($i = 0; $i < $nounits; $i++) {
            $mform->addElement('hidden', 'unit'."[$i]",
                    optional_param('unit'."[$i]", '', PARAM_NOTAGS));
            $mform->setType('unit'."[$i]", PARAM_NOTAGS);
            $mform->addElement('hidden', 'multiplier'."[$i]",
                    optional_param('multiplier'."[$i]", '', PARAM_NUMBER));
            $mform->setType('multiplier'."[$i]", PARAM_NUMBER);
        }

        $this->add_combined_feedback_fields(true);
        $mform->disabledIf('shownumcorrect', 'single', 'eq', 1);

        $this->add_interactive_settings(true, true);

        //hidden elements
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
        $question = $this->data_preprocessing_answers($question, true);
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

    protected function data_preprocessing_answers($question) {
        $question = parent::data_preprocessing_answers($question);
        if (empty($question->options->answers)) {
            return $question;
        }

        $key = 0;
        foreach ($question->options->answers as $answer) {
            // See comment in the parent method about this hack.
            unset($this->_form->_defaultValues["tolerance[$key]"]);
            unset($this->_form->_defaultValues["tolerancetype[$key]"]);
            unset($this->_form->_defaultValues["correctanswerlength[$key]"]);
            unset($this->_form->_defaultValues["correctanswerformat[$key]"]);

            $question->tolerance[$key]           = $answer->tolerance;
            $question->tolerancetype[$key]       = $answer->tolerancetype;
            $question->correctanswerlength[$key] = $answer->correctanswerlength;
            $question->correctanswerformat[$key] = $answer->correctanswerformat;
            $key++;
        }

        return $question;
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        //verifying for errors in {=...} in question text;
        $qtext = '';
        $qtextremaining = $data['questiontext']['text'];
        $possibledatasets = $this->qtypeobj->find_dataset_names($data['questiontext']['text']);
        foreach ($possibledatasets as $name => $value) {
            $qtextremaining = str_replace('{'.$name.'}', '1', $qtextremaining);
        }

        while (preg_match('~\{=([^[:space:]}]*)}~', $qtextremaining, $regs1)) {
            $qtextsplits = explode($regs1[0], $qtextremaining, 2);
            $qtext = $qtext.$qtextsplits[0];
            $qtextremaining = $qtextsplits[1];
            if (!empty($regs1[1]) && $formulaerrors =
                    qtype_calculated_find_formula_errors($regs1[1])) {
                if (!isset($errors['questiontext'])) {
                    $errors['questiontext'] = $formulaerrors.':'.$regs1[1];
                } else {
                    $errors['questiontext'] .= '<br/>'.$formulaerrors.':'.$regs1[1];
                }
            }
        }
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
                $errors['answer['.$key.']'] =
                        get_string('atleastonewildcard', 'qtype_calculated');
            }
        }
        if ($data['multichoice'] == 1) {
            foreach ($answers as $key => $answer) {
                $trimmedanswer = trim($answer);
                if ($trimmedanswer != '' || $answercount == 0) {
                    //verifying for errors in {=...} in answer text;
                    $qanswer = '';
                    $qanswerremaining =  $trimmedanswer;
                    $possibledatasets = $this->qtypeobj->find_dataset_names($trimmedanswer);
                    foreach ($possibledatasets as $name => $value) {
                        $qanswerremaining = str_replace('{'.$name.'}', '1', $qanswerremaining);
                    }

                    while (preg_match('~\{=([^[:space:]}]*)}~', $qanswerremaining, $regs1)) {
                        $qanswersplits = explode($regs1[0], $qanswerremaining, 2);
                        $qanswer = $qanswer . $qanswersplits[0];
                        $qanswerremaining = $qanswersplits[1];
                        if (!empty($regs1[1]) && $formulaerrors =
                                qtype_calculated_find_formula_errors($regs1[1])) {
                            if (!isset($errors['answer['.$key.']'])) {
                                $errors['answer['.$key.']'] = $formulaerrors.':'.$regs1[1];
                            } else {
                                $errors['answer['.$key.']'] .= '<br/>'.$formulaerrors.':'.$regs1[1];
                            }
                        }
                    }
                }
                if ($trimmedanswer != '') {
                    if ('2' == $data['correctanswerformat'][$key] &&
                            '0' == $data['correctanswerlength'][$key]) {
                        $errors['correctanswerlength['.$key.']'] =
                                get_string('zerosignificantfiguresnotallowed', 'qtype_calculated');
                    }
                    if (!is_numeric($data['tolerance'][$key])) {
                        $errors['tolerance['.$key.']'] =
                                get_string('mustbenumeric', 'qtype_calculated');
                    }
                    if ($data['fraction'][$key] == 1) {
                        $maxgrade = true;
                    }

                    $answercount++;
                }
                //check grades
                $totalfraction = 0;
                $maxfraction = 0;
                if ($answer != '') {
                    if ($data['fraction'][$key] > 0) {
                        $totalfraction += $data['fraction'][$key];
                    }
                    if ($data['fraction'][$key] > $maxfraction) {
                        $maxfraction = $data['fraction'][$key];
                    }
                }
            }
            if ($answercount == 0) {
                $errors['answer[0]'] = get_string('notenoughanswers', 'qtype_multichoice', 2);
                $errors['answer[1]'] = get_string('notenoughanswers', 'qtype_multichoice', 2);
            } else if ($answercount == 1) {
                $errors['answer[1]'] = get_string('notenoughanswers', 'qtype_multichoice', 2);

            }

            /// Perform sanity checks on fractional grades
            if ($data['single']) {
                if ($maxfraction > 0.999) {
                    $maxfraction = $maxfraction * 100;
                    $errors['fraction[0]'] =
                            get_string('errfractionsnomax', 'qtype_multichoice', $maxfraction);
                }
            } else {
                $totalfraction = round($totalfraction, 2);
                if ($totalfraction != 1) {
                    $totalfraction = $totalfraction * 100;
                    $errors['fraction[0]'] =
                            get_string('errfractionsaddwrong', 'qtype_multichoice', $totalfraction);
                }
            }

            if ($answercount == 0) {
                $errors['answer[0]'] = get_string('atleastoneanswer', 'qtype_calculated');
            }
            if ($maxgrade == false) {
                $errors['fraction[0]'] = get_string('fractionsnomax', 'question');
            }

        }
        return $errors;
    }

    public function qtype() {
        return 'calculatedmulti';
    }
}

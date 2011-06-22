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
 * Defines the editing form for the calculated question type.
 *
 * @package    qtype
 * @subpackage calculated
 * @copyright  2007 Jamie Pratt me@jamiep.org
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/numerical/edit_numerical_form.php');


/**
 * Calculated question type editing form definition.
 *
 * @copyright  2007 Jamie Pratt me@jamiep.org
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_calculated_edit_form extends qtype_numerical_edit_form {
    /**
     * Handle to the question type for this question.
     *
     * @var qtype_calculated
     */
    public $qtypeobj;
    public $questiondisplay;
    public $activecategory;
    public $categorychanged = false;
    public $initialname = '';
    public $reload = false;

    public function __construct($submiturl, $question, $category, $contexts,
            $formeditable = true) {
        global $CFG, $DB;
        $this->question = $question;
        if ('1' == optional_param('reload', '', PARAM_INT)) {
            $this->reload = true;
        } else {
            $this->reload = false;
        }

        if (!$this->reload) { // use database data as this is first pass
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
        $repeated = parent::get_per_answer_fields($mform, $label, $gradeoptions,
                $repeatedoptions, $answersoption);

        // 1 is the answer. 3 is tolerance.
        $repeated[1]->setLabel(get_string('correctanswerformula', 'qtype_calculated') . '=');
        $repeated[3]->setLabel(get_string('tolerance', 'qtype_calculated') . '=');
        $repeatedoptions['tolerance']['default'] = 0.01;

        $addrepeated = array();
        $addrepeated[] = $mform->createElement('select', 'tolerancetype',
                get_string('tolerancetype', 'qtype_numerical'), $this->qtypeobj->tolerance_types());

        $addrepeated[] = $mform->createElement('select', 'correctanswerlength',
                get_string('correctanswershows', 'qtype_calculated'), range(0, 9));
        $repeatedoptions['correctanswerlength']['default'] = 2;

        $answerlengthformats = array(
            '1' => get_string('decimalformat', 'qtype_numerical'),
            '2' => get_string('significantfiguresformat', 'qtype_calculated')
        );
        $addrepeated[] = $mform->createElement('select', 'correctanswerformat',
                get_string('correctanswershowsformat', 'qtype_calculated'), $answerlengthformats);

        array_splice($repeated, 4, 0, $addrepeated);

        return $repeated;
    }

    /**
     * Add question-type specific form fields.
     *
     * @param MoodleQuickForm $mform the form being built.
     */
    protected function definition_inner($mform) {
        $this->qtypeobj = question_bank::get_qtype($this->qtype());
        $label = get_string('sharedwildcards', 'qtype_calculated');
        $mform->addElement('hidden', 'initialcategory', 1);
        $mform->addElement('hidden', 'reload', 1);
        $mform->setType('initialcategory', PARAM_INT);
        $html2 = $this->qtypeobj->print_dataset_definitions_category($this->question);
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

        $mform->insertElementBefore(
                $mform->createElement('submit', $addfieldsname, $addstring), 'listcategory');
        $mform->registerNoSubmitButton('createoptionbutton');

        //editing as regular
        $mform->setType('single', PARAM_INT);

        $mform->addElement('hidden', 'shuffleanswers', '1');
        $mform->setType('shuffleanswers', PARAM_INT);
        $mform->addElement('hidden', 'answernumbering', 'abc');
        $mform->setType('answernumbering', PARAM_SAFEDIR);

        $this->add_per_answer_fields($mform, get_string('answerhdr', 'qtype_calculated', '{no}'),
                question_bank::fraction_options(), 1, 1);

        $repeated = array();

        $this->add_unit_options($mform, $this);
        $this->add_unit_fields($mform, $this);
        $this->add_interactive_settings();

        // Hidden elements
        $mform->addElement('hidden', 'synchronize', '');
        $mform->setType('synchronize', PARAM_INT);
        $mform->addElement('hidden', 'wizard', 'datasetdefinitions');
        $mform->setType('wizard', PARAM_ALPHA);
    }

    public function data_preprocessing($question) {
        $question = parent::data_preprocessing($question);
        $question = $this->data_preprocessing_answers($question);
        $question = $this->data_preprocessing_hints($question);
        $question = $this->data_preprocessing_units($question);
        $question = $this->data_preprocessing_unit_options($question);

        if (isset($question->options->synchronize)) {
            $question->synchronize = $question->options->synchronize;
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
            unset($this->_form->_defaultValues["tolerancetype[$key]"]);
            unset($this->_form->_defaultValues["correctanswerlength[$key]"]);
            unset($this->_form->_defaultValues["correctanswerformat[$key]"]);

            $question->tolerancetype[$key]       = $answer->tolerancetype;
            $question->correctanswerlength[$key] = $answer->correctanswerlength;
            $question->correctanswerformat[$key] = $answer->correctanswerformat;
            $key++;
        }

        return $question;
    }

    public function qtype() {
        return 'calculated';
    }

    public function validation($data, $files) {

        // verifying for errors in {=...} in question text;
        $qtext = "";
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

        $errors = parent::validation($data, $files);

        // Check that the answers use datasets.
        $answers = $data['answer'];
        $mandatorydatasets = array();
        foreach ($answers as $key => $answer) {
            $mandatorydatasets += $this->qtypeobj->find_dataset_names($answer);
        }
        if (empty($mandatorydatasets)) {
            foreach ($answers as $key => $answer) {
                $errors['answer['.$key.']'] =
                        get_string('atleastonewildcard', 'qtype_calculated');
            }
        }

        // Validate the answer format.
        foreach ($answers as $key => $answer) {
            $trimmedanswer = trim($answer);
            if (trim($answer)) {
                if ($data['correctanswerformat'][$key] == 2 &&
                        $data['correctanswerlength'][$key] == '0') {
                    $errors['correctanswerlength['.$key.']'] =
                            get_string('zerosignificantfiguresnotallowed', 'qtype_calculated');
                }
            }
        }

        return $errors;
    }

    protected function is_valid_answer($answer, $data) {
        return !qtype_calculated_find_formula_errors($answer);
    }

    protected function valid_answer_message($answer) {
        if (!$answer) {
            return get_string('mustenteraformulaorstar', 'qtype_numerical');
        } else {
            return qtype_calculated_find_formula_errors($answer);
        }
    }
}

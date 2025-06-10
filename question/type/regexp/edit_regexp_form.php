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
 * Defines the editing form for the regexp question type.
 * @package qtype_regexp
 * @copyright  2011 Joseph REZEAU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

 */

/**
 * Editing form for the regexp question type
 * @copyright  2011 Joseph REZEAU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_regexp_edit_form extends question_edit_form {
    /**
     * Add question-type specific form fields.
     *
     * @param MoodleQuickForm $mform the form being built.
     */

    /** Added public declarations to fix PHP 8.2 warning: Deprecated: Creation of dynamic property */
    /**
     * Description of the property.
     * @var int
     */
    public $showalternate;
    /**
     * Description of the property.
     * @var int
     */
    public $questionid;
    /**
     * Description of the property.
     * @var int
     */
    public $usecase;
    /**
     * Description of the property.
     * @var int
     */
    public $studentshowalternate;
    /**
     * Description of the property.
     * @var int
     */
    public $fraction;
    /**
     * Description of the property.
     * @var int
     */
    public $currentanswers;
    /**
     * Description of the property.
     * @var int
     */
    public $feedback;

    /**
     * Description of the property.
     * @param object $mform the form being built.
     */
    protected function definition_inner($mform) {
        global $CFG, $OUTPUT, $SESSION;

        require_once($CFG->dirroot.'/question/type/regexp/locallib.php');

        $this->showalternate = false;
        if ("" != optional_param('showalternate', '', PARAM_RAW)) {
            $this->showalternate = true;
            $this->questionid = optional_param('id', '', PARAM_NOTAGS);
            $this->usecase = optional_param('usecase', '', PARAM_NOTAGS);
            $this->studentshowalternate = optional_param('studentshowalternate', '', PARAM_NOTAGS);
            $this->fraction = optional_param_array('fraction', '', PARAM_RAW);
            $this->currentanswers = optional_param_array('answer', '', PARAM_NOTAGS);
            $feedback = data_submitted()->feedback;
            // We only need to get the feedback text, for validation purposes when showalternate is requested.
            foreach ($feedback as $key => $fb) {
                $this->feedback[$key]['text'] = clean_param($fb['text'], PARAM_NOTAGS);
            }
        }

        // Hint mode :: None / Letter / Word (including punctuation) / Word OR Punctuation.
        $menu = [get_string('none'), get_string('letter', 'qtype_regexp'),
            get_string('word', 'qtype_regexp'), get_string('wordorpunctuation', 'qtype_regexp')];
        $mform->addElement('select', 'usehint', get_string('usehint', 'qtype_regexp'), $menu);
        $mform->addHelpButton('usehint', 'usehint', 'qtype_regexp');

        // Use case :: yes / no.
        $menu = [get_string('caseno', 'qtype_regexp'), get_string('caseyes', 'qtype_regexp')];
        $mform->addElement('select', 'usecase', get_string('casesensitive', 'qtype_regexp'), $menu);

        // Display all correct alternate answers to student on review page :: yes / no.
        $menu = [get_string('no'), get_string('yes')];
        $menu = [get_string('no'), get_string('yes')];
        $mform->addElement('select', 'studentshowalternate', get_string('studentshowalternate', 'qtype_regexp'), $menu);
        $mform->addHelpButton('studentshowalternate', 'studentshowalternate', 'qtype_regexp');

        $mform->closeHeaderBefore('answersinstruct');
        $mform->addElement('static', 'answersinstruct', get_string('notice').'.-',
                get_string('filloutoneanswer', 'qtype_regexp'));

        $this->add_per_answer_fields($mform, get_string('answerno', 'qtype_shortanswer', '{no}'),
                question_bank::fraction_options());

        $this->add_interactive_settings();

        $mform->addElement('header', 'showhidealternate', get_string('showhidealternate', 'qtype_regexp'));
        $mform->addHelpButton('showhidealternate', 'showhidealternate', 'qtype_regexp');

        $buttonarray = [];
        $buttonarray[] = $mform->createElement('submit', 'showalternate', get_string('calculatealternate', 'qtype_regexp'));
        $mform->registerNoSubmitButton('showalternate');

        if ($this->showalternate) {
            $qu = new stdClass();
            $qu->id = $this->questionid;
            $qu->answers = [];
            $i = 0;
            $this->fraction[0] = 1;
            $data = [];

            // Add current question category to $data for validation!
            // Modified for moodle 3.6 compatibility.

            foreach ($this->currentanswers as $key => $answer) {
                $qu->answers[$i] = new stdClass();
                $qu->answers[$i]->answer = $answer;
                $qu->answers[$i]->fraction = $this->fraction[$i];
                // For sending $data to validation.
                $data['answer'][$i] = $answer;
                $data['fraction'][$i] = $this->fraction[$i];
                $data['feedback'][$i] = $this->feedback[$i];
                $i++;
            }

            $moodleval = $this->validation($data, '');
            if ((is_array($moodleval) && count($moodleval) !== 0)) {
                // Non-empty array means errors.
                foreach ($moodleval as $element => $msg) {
                    $mform->setElementError($element, $msg);
                }
            } else {
                // We need to unset SESSION in case Answers have been edited since last call to get_alternateanswers().
                if (isset($SESSION->qtype_regexp_question->alternateanswers[$this->questionid])) {
                    unset($SESSION->qtype_regexp_question->alternateanswers[$this->questionid]);
                }
                $alternateanswers = get_alternateanswers($qu);
                $mform->addElement('html', '<div class="alternateanswers">');
                $alternatelist = '';
                foreach ($alternateanswers as $key => $alternateanswer) {
                    $mform->addElement('static', 'alternateanswer', get_string('answer').' '.$key.
                        ' ('.$alternateanswer['fraction'].')',
                        '<span class="regexp">'.$alternateanswer['regexp'].'</span>' );
                    $list = '';
                    foreach ($alternateanswer['answers'] as $alternate) {
                        $list .= '<li>'.$alternate.'</li>';
                    }
                    $mform->addElement('static', 'alternateanswer', '', '<ul class="square">'.$list.'</ul>');
                }
                $mform->addElement('html', '</div>');
            }
        }

        $mform->addGroup($buttonarray, '', '', [' '], false);
    }

    /**
     * Add more blanks.
     * @var string
     */
    protected function get_more_choices_string() {
        return get_string('addmoreanswerblanks', 'qtype_shortanswer');
    }

    /**
     * Perform any preprocessing needed on the data passed in
     * before it is used to initialise the form.
     * @param object $question the data being passed to the form.
     * @return object $question the modified data.
     */
    protected function data_preprocessing($question) {
        global $CFG, $PAGE, $SESSION;

        $question = parent::data_preprocessing($question);
        $question = $this->data_preprocessing_answers($question);
        $question = $this->data_preprocessing_hints($question);

        if (!empty($question->options)) {
            $question->usecase = $question->options->usecase;
            $question->usehint = $question->options->usehint;
            $question->studentshowalternate = $question->options->studentshowalternate;
        } else {
            $key = 0;
            $defaultvalues['fraction['.$key.']'] = 1;
            $question = (object)((array)$question + $defaultvalues);
        }
        // Disable the score dropdown list for Answer 1 to make sure it remains at 100%.
        // Grade for Answer 1 will need to be automatically set to 1 in questiontype.php,  save_question_options($question).
        foreach ($this->_form->_elements as $element) {
            if (isset($element->_elements[0]->_attributes['name'])) {
                if ($element->_elements[0]->_attributes['name'] === "answer[0]") {
                    $element->_elements[1]->_attributes['disabled'] = 'disabled';
                    break;
                }
            }
        }
        return $question;
    }

    /**
     * Check the question text is valid.
     * @param array $data
     * @param array $files
     * @return boolean
     */
    public function validation($data, $files) {
        global $CFG;

        require_once($CFG->dirroot.'/question/type/regexp/locallib.php');
        // Starting with Moodle 4 if we are calculating alternate answers we cannot use parent:validation.
        if ($this->showalternate) {
            $errors = [];
        } else {
            $errors = parent::validation($data, $files);
        }
        $answers = $data['answer'];
        $data['fraction'][0] = 1;
        $grades = $data['fraction'];
        $answercount = 0;
        $illegalmetacharacters = ". ^ $ * + { } \\";

        foreach ($answers as $key => $answer) {
            $trimmedanswer = trim($answer);
            if ($trimmedanswer !== '') {
                $answercount++;
                $parenserror = '';
                $metacharserror = '';

                // We do not check parenthesis and square brackets in Answer 1 (correct answer).
                if ($key > 0) {
                    $parenserror = check_permutations($trimmedanswer);
                    if ($parenserror) {
                        $errors["answeroptions[$key]"] = $parenserror.'<br />';
                    }
                    $markedline = '';
                    for ($i = 0; $i < strlen($trimmedanswer); $i++) {
                        $markedline .= ' ';
                    }
                    $parenserror = check_my_parens($trimmedanswer, $markedline);
                    if ($parenserror) {
                        if (empty($errors["answeroptions[$key]"])) {
                            $errors["answeroptions[$key]"] = get_string("regexperrorparen", "qtype_regexp").'<br />';
                        } else {
                            $errors["answeroptions[$key]"] .= get_string("regexperrorparen", "qtype_regexp").'<br />';
                        }
                        $markedline = $parenserror;
                    }
                    // We do not test unescaped metacharacters in Answers expressions for incorrect responses (grade = None).
                    if ($data['fraction'][$key] > 0) {
                        $metacharserror = check_unescaped_metachars($trimmedanswer, $markedline);
                        if ($metacharserror) {
                            $errormessage = get_string("illegalcharacters", "qtype_regexp", $illegalmetacharacters);
                            if (empty($errors["answeroptions[$key]"])) {
                                $errors["answeroptions[$key]"] = $errormessage;
                            } else {
                                $errors["answeroptions[$key]"] .= $errormessage;
                            }
                        }
                    }
                    if ($metacharserror || $parenserror) {
                        $answerstringchunks = splitstring ($trimmedanswer);
                        $nbchunks = count($answerstringchunks);
                        $errors["answeroptions[$key]"] .= '<pre class="displayvalidationerrors">';
                        if ($metacharserror) {
                            $illegalcharschunks = splitstring ($metacharserror);
                            for ($i = 0; $i < $nbchunks; $i++) {
                                $errors["answeroptions[$key]"] .= '<br />'.$answerstringchunks[$i].'<br />'.$illegalcharschunks[$i];
                            }
                        } else if ($parenserror) {
                            $illegalcharschunks = splitstring ($parenserror);
                            for ($i = 0; $i < $nbchunks; $i++) {
                                $errors["answeroptions[$key]"] .= '<br />'.$answerstringchunks[$i].'<br />'.$illegalcharschunks[$i];
                            }
                        }
                        $errors["answeroptions[$key]"] .= '</pre>';
                    }
                }
            } else if ($data['fraction'][$key] != 0 || !html_is_blank($data['feedback'][$key]['text'])) {
                if ($key === 0) {
                    $errors['answeroptions[0]'] = get_string('answer1mustbegiven', 'qtype_regexp');
                } else {
                     $errors["answeroptions[$key]"] = get_string('answermustbegiven', 'qtype_regexp');
                }
                $answercount++;
            }
        }
        return $errors;
    }

    /**
     * Get the list of form elements to repeat, one for each answer.
     * Copied from edit_question_form.php just to extend size of answer text field from 40 to 80.
     * @param array $mform the form being built.
     * @param string $label the label to use for each option.
     * @param array $gradeoptions the possible grades for each answer.
     * @param array $repeatedoptions reference to array of repeated options to fill
     * @param array $answersoption reference to return the name of $question->options field holding an array of answers
     * @return array of form fields.
     */
    protected function get_per_answer_fields($mform, $label, $gradeoptions,
                    &$repeatedoptions, &$answersoption) {
        $repeated = [];
        $answeroptions = [];
        $answeroptions[] = $mform->createElement('text', 'answer',
                        $label, ['size' => 80]);
        $answeroptions[] = $mform->createElement('select', 'fraction',
                        get_string('gradenoun'), $gradeoptions);
        $repeated[] = $mform->createElement('group', 'answeroptions',
                        $label, $answeroptions, null, false);
        $repeated[] = $mform->createElement('editor', 'feedback',
                        get_string('feedback', 'question'), ['rows' => 5], $this->editoroptions);
        $repeatedoptions['answer']['type'] = PARAM_RAW;
        $repeatedoptions['fraction']['default'] = 0;
        $answersoption = 'answers';
        return $repeated;
    }


    /**
     * Copied from edit_question_form.php to change the strings and penalties values.
     * @param boolean $withclearwrong
     * @param boolean $withshownumpartscorrect
     */
    protected function add_interactive_settings($withclearwrong = false,
                    $withshownumpartscorrect = false) {
        $mform = $this->_form;

        $mform->addElement('header', 'multitriesheader',
                        get_string('settingsformultipletries', 'qtype_regexp'));

        $penalties = [
            1.0000000,
            0.5000000,
            0.3333333,
            0.2500000,
            0.2000000,
            0.1000000,
            0.0000000,
        ];

        if (!empty($this->question->penalty) && !in_array($this->question->penalty, $penalties)) {
            $penalties[] = $this->question->penalty;
            sort($penalties);
        }
        $penaltyoptions = [];
        foreach ($penalties as $penalty) {
            $penaltyoptions["$penalty"] = (100 * $penalty) . '%';
        }
        $mform->addElement('select', 'penalty',
                        get_string('penaltyforeachincorrecttry', 'qtype_regexp'), $penaltyoptions);
        $mform->addHelpButton('penalty', 'penaltyforeachincorrecttry', 'qtype_regexp');
        $mform->setDefault('penalty', 0.10);

        if (isset($this->question->hints)) {
            $counthints = count($this->question->hints);
        } else {
            $counthints = 0;
        }

        if ($this->question->formoptions->repeatelements) {
            $repeatsatstart = max(self::DEFAULT_NUM_HINTS, $counthints);
        } else {
            $repeatsatstart = $counthints;
        }

        list($repeated, $repeatedoptions) = $this->get_hint_fields(
                        $withclearwrong, $withshownumpartscorrect);
        $this->repeat_elements($repeated, $repeatsatstart, $repeatedoptions,
                        'numhints', 'addhint', 1, get_string('addanotherhint', 'question'), true);
    }

    /**
     * Name of this question type
     * @return string
     */
    public function qtype() {
        return 'regexp';
    }

}

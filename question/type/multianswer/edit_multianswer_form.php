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
 * Defines the editing form for the multi-answer question type.
 *
 * @package    qtype
 * @subpackage multianswer
 * @copyright  2007 Jamie Pratt me@jamiep.org
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Form for editing multi-answer questions.
 *
 * @copyright  2007 Jamie Pratt me@jamiep.org
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
class qtype_multianswer_edit_form extends question_edit_form {

    //  $questiondisplay will contain the qtype_multianswer_extract_question from
    // the questiontext
    public $questiondisplay;
    //  $savedquestiondisplay will contain the qtype_multianswer_extract_question
    // from the questiontext in database
    public $savedquestion;
    public $savedquestiondisplay;
    public $used_in_quiz = false;
    public $qtype_change = false;
    public $negative_diff = 0;
    public $nb_of_quiz = 0;
    public $nb_of_attempts = 0;
    public $confirm = 0;
    public $reload = false;

    public function __construct($submiturl, $question, $category, $contexts, $formeditable = true) {
        global $SESSION, $CFG, $DB;
        $this->regenerate = true;
        $this->reload = optional_param('reload', false, PARAM_BOOL);

        $this->used_in_quiz = false;

        if (isset($question->id) && $question->id != 0) {
            $this->savedquestiondisplay = fullclone($question);
            if ($list = $DB->get_records('quiz_question_instances',
                    array('question' => $question->id))) {
                foreach ($list as $key => $li) {
                    $this->nb_of_quiz ++;
                    if ($att = $DB->get_records('quiz_attempts',
                            array('quiz' => $li->quiz, 'preview' => '0'))) {
                        $this->nb_of_attempts += count($att);
                        $this->used_in_quiz = true;
                    }
                }
            }
        }

        parent::__construct($submiturl, $question, $category, $contexts, $formeditable);
    }

    protected function definition_inner($mform) {
        $mform->addElement('hidden', 'reload', 1);
        $mform->setType('reload', PARAM_INT);

        // Remove meaningless defaultmark field.
        $mform->removeElement('defaultmark');
        $this->confirm = optional_param('confirm', false, PARAM_BOOL);

        // Make questiontext a required field for this question type.
        $mform->addRule('questiontext', null, 'required', null, 'client');

        // display the questions from questiontext;
        if ($questiontext = optional_param_array('questiontext', false, PARAM_RAW)) {
            $this->questiondisplay = fullclone(qtype_multianswer_extract_question($questiontext));

        } else {
            if (!$this->reload && !empty($this->savedquestiondisplay->id)) {
                // use database data as this is first pass
                // question->id == 0 so no stored datasets
                $this->questiondisplay = fullclone($this->savedquestiondisplay);
                foreach ($this->questiondisplay->options->questions as $subquestion) {
                    if (!empty($subquestion)) {
                        $subquestion->answer = array('');
                        foreach ($subquestion->options->answers as $ans) {
                            $subquestion->answer[] = $ans->answer;
                        }
                    }
                }
            } else {
                $this->questiondisplay = "";
            }
        }

        if (isset($this->savedquestiondisplay->options->questions) &&
                is_array($this->savedquestiondisplay->options->questions)) {
            $countsavedsubquestions = 0;
            foreach ($this->savedquestiondisplay->options->questions as $subquestion) {
                if (!empty($subquestion)) {
                    $countsavedsubquestions++;
                }
            }
        } else {
            $countsavedsubquestions = 0;
        }
        if ($this->reload) {
            if (isset($this->questiondisplay->options->questions) &&
                    is_array($this->questiondisplay->options->questions)) {
                $countsubquestions = 0;
                foreach ($this->questiondisplay->options->questions as $subquestion) {
                    if (!empty($subquestion)) {
                        $countsubquestions++;
                    }
                }
            } else {
                $countsubquestions = 0;
            }
        } else {
            $countsubquestions = $countsavedsubquestions;
        }

        $mform->addElement('submit', 'analyzequestion',
                get_string('decodeverifyquestiontext', 'qtype_multianswer'));
        $mform->registerNoSubmitButton('analyzequestion');
        if ($this->reload) {
            for ($sub = 1; $sub <= $countsubquestions; $sub++) {

                if (isset($this->questiondisplay->options->questions[$sub]->qtype)) {
                    $this->editas[$sub] = $this->questiondisplay->options->questions[$sub]->qtype;
                } else {
                    $this->editas[$sub] = optional_param('sub_'.$sub.'_qtype', 'unknown type', PARAM_PLUGIN);
                }

                $storemess = '';
                if (isset($this->savedquestiondisplay->options->questions[$sub]->qtype) &&
                        $this->savedquestiondisplay->options->questions[$sub]->qtype !=
                                $this->questiondisplay->options->questions[$sub]->qtype) {
                    $this->qtype_change = true;
                    $storemess = ' ' . html_writer::tag('span', get_string(
                            'storedqtype', 'qtype_multianswer', question_bank::get_qtype_name(
                                    $this->savedquestiondisplay->options->questions[$sub]->qtype)),
                            array('class' => 'error'));
                }

                $mform->addElement('header', 'subhdr'.$sub, get_string('questionno', 'question',
                       '{#'.$sub.'}').'&nbsp;'.question_bank::get_qtype_name(
                        $this->questiondisplay->options->questions[$sub]->qtype).$storemess);

                $mform->addElement('static', 'sub_'.$sub.'_questiontext',
                        get_string('questiondefinition', 'qtype_multianswer'));

                if (isset ($this->questiondisplay->options->questions[$sub]->questiontext)) {
                    $mform->setDefault('sub_'.$sub.'_questiontext',
                            $this->questiondisplay->options->questions[$sub]->questiontext['text']);
                }

                $mform->addElement('static', 'sub_'.$sub.'_defaultmark',
                        get_string('defaultmark', 'question'));
                $mform->setDefault('sub_'.$sub.'_defaultmark',
                        $this->questiondisplay->options->questions[$sub]->defaultmark);

                if ($this->questiondisplay->options->questions[$sub]->qtype == 'shortanswer') {
                    $mform->addElement('static', 'sub_'.$sub.'_usecase',
                            get_string('casesensitive', 'qtype_shortanswer'));
                }

                if ($this->questiondisplay->options->questions[$sub]->qtype == 'multichoice') {
                    $mform->addElement('static', 'sub_'.$sub.'_layout',
                            get_string('layout', 'qtype_multianswer'));
                }

                foreach ($this->questiondisplay->options->questions[$sub]->answer as $key => $ans) {
                    $mform->addElement('static', 'sub_'.$sub.'_answer['.$key.']',
                            get_string('answer', 'question'));

                    if ($this->questiondisplay->options->questions[$sub]->qtype == 'numerical' &&
                            $key == 0) {
                        $mform->addElement('static', 'sub_'.$sub.'_tolerance['.$key.']',
                                get_string('acceptederror', 'qtype_numerical'));
                    }

                    $mform->addElement('static', 'sub_'.$sub.'_fraction['.$key.']',
                            get_string('grade'));

                    $mform->addElement('static', 'sub_'.$sub.'_feedback['.$key.']',
                            get_string('feedback', 'question'));
                }
            }

            $this->negative_diff = $countsavedsubquestions - $countsubquestions;
            if (($this->negative_diff > 0) ||$this->qtype_change ||
                    ($this->used_in_quiz && $this->negative_diff != 0)) {
                $mform->addElement('header', 'additemhdr',
                        get_string('warningquestionmodified', 'qtype_multianswer'));
            }
            if ($this->negative_diff > 0) {
                $mform->addElement('static', 'alert1', "<strong>".
                        get_string('questiondeleted', 'qtype_multianswer')."</strong>",
                        get_string('questionsless', 'qtype_multianswer', $this->negative_diff));
            }
            if ($this->qtype_change) {
                $mform->addElement('static', 'alert1', "<strong>".
                        get_string('questiontypechanged', 'qtype_multianswer')."</strong>",
                        get_string('questiontypechangedcomment', 'qtype_multianswer'));
            }
        }
        if ($this->used_in_quiz) {
            if ($this->negative_diff < 0) {
                $diff = $countsubquestions - $countsavedsubquestions;
                $mform->addElement('static', 'alert1', "<strong>".
                        get_string('questionsadded', 'qtype_multianswer')."</strong>",
                        "<strong>".get_string('questionsmore', 'qtype_multianswer', $diff).
                        "</strong>");
            }
            $a = new stdClass();
            $a->nb_of_quiz = $this->nb_of_quiz;
            $a->nb_of_attempts = $this->nb_of_attempts;
            $mform->addElement('header', 'additemhdr2',
                    get_string('questionusedinquiz', 'qtype_multianswer', $a));
            $mform->addElement('static', 'alertas',
                    get_string('youshouldnot', 'qtype_multianswer'));
        }
        if (($this->negative_diff > 0 || $this->used_in_quiz &&
                ($this->negative_diff > 0 || $this->negative_diff < 0 || $this->qtype_change)) &&
                        $this->reload) {
            $mform->addElement('header', 'additemhdr',
                    get_string('questionsaveasedited', 'qtype_multianswer'));
            $mform->addElement('checkbox', 'confirm', '',
                    get_string('confirmquestionsaveasedited', 'qtype_multianswer'));
            $mform->setDefault('confirm', 0);
        } else {
            $mform->addElement('hidden', 'confirm', 0);
        }

        $this->add_interactive_settings(true, true);
    }


    public function set_data($question) {
        global $DB;
        $default_values = array();
        if (isset($question->id) and $question->id and $question->qtype &&
                $question->questiontext) {

            foreach ($question->options->questions as $key => $wrapped) {
                if (!empty($wrapped)) {
                    // The old way of restoring the definitions is kept to gradually
                    // update all multianswer questions
                    if (empty($wrapped->questiontext)) {
                        $parsableanswerdef = '{' . $wrapped->defaultmark . ':';
                        switch ($wrapped->qtype) {
                            case 'multichoice':
                                $parsableanswerdef .= 'MULTICHOICE:';
                                break;
                            case 'shortanswer':
                                $parsableanswerdef .= 'SHORTANSWER:';
                                break;
                            case 'numerical':
                                $parsableanswerdef .= 'NUMERICAL:';
                                break;
                            default:
                                print_error('unknownquestiontype', 'question', '',
                                        $wrapped->qtype);
                        }
                        $separator = '';
                        foreach ($wrapped->options->answers as $subanswer) {
                            $parsableanswerdef .= $separator
                                . '%' . round(100*$subanswer->fraction) . '%';
                            if (is_array($subanswer->answer)) {
                                $parsableanswerdef .= $subanswer->answer['text'];
                            } else {
                                $parsableanswerdef .= $subanswer->answer;
                            }
                            if (!empty($wrapped->options->tolerance)) {
                                // Special for numerical answers:
                                $parsableanswerdef .= ":{$wrapped->options->tolerance}";
                                // We only want tolerance for the first alternative, it will
                                // be applied to all of the alternatives.
                                unset($wrapped->options->tolerance);
                            }
                            if ($subanswer->feedback) {
                                $parsableanswerdef .= "#$subanswer->feedback";
                            }
                            $separator = '~';
                        }
                        $parsableanswerdef .= '}';
                        // Fix the questiontext fields of old questions
                        $DB->set_field('question', 'questiontext', $parsableanswerdef,
                                array('id' => $wrapped->id));
                    } else {
                        $parsableanswerdef = str_replace('&#', '&\#', $wrapped->questiontext);
                    }
                    $question->questiontext = str_replace("{#$key}", $parsableanswerdef,
                            $question->questiontext);
                }
            }
        }

        // set default to $questiondisplay questions elements
        if ($this->reload) {
            if (isset($this->questiondisplay->options->questions)) {
                $subquestions = fullclone($this->questiondisplay->options->questions);
                if (count($subquestions)) {
                    $sub = 1;
                    foreach ($subquestions as $subquestion) {
                        $prefix = 'sub_'.$sub.'_';

                        // validate parameters
                        $answercount = 0;
                        $maxgrade = false;
                        $maxfraction = -1;
                        if ($subquestion->qtype == 'shortanswer') {
                            switch ($subquestion->usecase) {
                                case '1':
                                    $default_values[$prefix.'usecase'] =
                                            get_string('caseyes', 'qtype_shortanswer');
                                    break;
                                case '0':
                                default :
                                    $default_values[$prefix.'usecase'] =
                                            get_string('caseno', 'qtype_shortanswer');
                            }
                        }

                        if ($subquestion->qtype == 'multichoice') {
                            $default_values[$prefix.'layout'] = $subquestion->layout;
                            switch ($subquestion->layout) {
                                case '0':
                                    $default_values[$prefix.'layout'] =
                                            get_string('layoutselectinline', 'qtype_multianswer');
                                    break;
                                case '1':
                                    $default_values[$prefix.'layout'] =
                                            get_string('layoutvertical', 'qtype_multianswer');
                                    break;
                                case '2':
                                    $default_values[$prefix.'layout'] =
                                            get_string('layouthorizontal', 'qtype_multianswer');
                                    break;
                                default:
                                    $default_values[$prefix.'layout'] =
                                            get_string('layoutundefined', 'qtype_multianswer');
                            }
                        }
                        foreach ($subquestion->answer as $key => $answer) {
                            if ($subquestion->qtype == 'numerical' && $key == 0) {
                                $default_values[$prefix.'tolerance['.$key.']'] =
                                        $subquestion->tolerance[0];
                            }
                            if (is_array($answer)) {
                                $answer = $answer['text'];
                            }
                            $trimmedanswer = trim($answer);
                            if ($trimmedanswer !== '') {
                                $answercount++;
                                if ($subquestion->qtype == 'numerical' &&
                                        !(is_numeric($trimmedanswer) || $trimmedanswer == '*')) {
                                    $this->_form->setElementError($prefix.'answer['.$key.']',
                                            get_string('answermustbenumberorstar',
                                                    'qtype_numerical'));
                                }
                                if ($subquestion->fraction[$key] == 1) {
                                    $maxgrade = true;
                                }
                                if ($subquestion->fraction[$key] > $maxfraction) {
                                    $maxfraction = $subquestion->fraction[$key];
                                }
                            }

                            $default_values[$prefix.'answer['.$key.']'] =
                                    htmlspecialchars($answer);
                        }
                        if ($answercount == 0) {
                            if ($subquestion->qtype == 'multichoice') {
                                $this->_form->setElementError($prefix.'answer[0]',
                                        get_string('notenoughanswers', 'qtype_multichoice', 2));
                            } else {
                                $this->_form->setElementError($prefix.'answer[0]',
                                        get_string('notenoughanswers', 'question', 1));
                            }
                        }
                        if ($maxgrade == false) {
                            $this->_form->setElementError($prefix.'fraction[0]',
                                    get_string('fractionsnomax', 'question'));
                        }
                        foreach ($subquestion->feedback as $key => $answer) {

                            $default_values[$prefix.'feedback['.$key.']'] =
                                    htmlspecialchars ($answer['text']);
                        }
                        foreach ($subquestion->fraction as $key => $answer) {
                            $default_values[$prefix.'fraction['.$key.']'] = $answer;
                        }

                        $sub++;
                    }
                }
            }
        }
        $default_values['alertas']= "<strong>".get_string('questioninquiz', 'qtype_multianswer').
                "</strong>";

        if ($default_values != "") {
            $question = (object)((array)$question + $default_values);
        }
        $question = $this->data_preprocessing_hints($question, true, true);
        parent::set_data($question);
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        $questiondisplay = qtype_multianswer_extract_question($data['questiontext']);

        if (isset($questiondisplay->options->questions)) {
            $subquestions = fullclone($questiondisplay->options->questions);
            if (count($subquestions)) {
                $sub = 1;
                foreach ($subquestions as $subquestion) {
                    $prefix = 'sub_'.$sub.'_';
                    $answercount = 0;
                    $maxgrade = false;
                    $maxfraction = -1;
                    if (isset($this->savedquestiondisplay->options->questions[$sub]->qtype) &&
                            $this->savedquestiondisplay->options->questions[$sub]->qtype !=
                                    $questiondisplay->options->questions[$sub]->qtype) {
                        $storemess = " STORED QTYPE ".question_bank::get_qtype_name(
                                $this->savedquestiondisplay->options->questions[$sub]->qtype);
                    }
                    foreach ($subquestion->answer as $key => $answer) {
                        if (is_array($answer)) {
                            $answer = $answer['text'];
                        }
                        $trimmedanswer = trim($answer);
                        if ($trimmedanswer !== '') {
                            $answercount++;
                            if ($subquestion->qtype == 'numerical' &&
                                    !(is_numeric($trimmedanswer) || $trimmedanswer == '*')) {
                                $errors[$prefix.'answer['.$key.']'] =
                                        get_string('answermustbenumberorstar', 'qtype_numerical');
                            }
                            if ($subquestion->fraction[$key] == 1) {
                                $maxgrade = true;
                            }
                            if ($subquestion->fraction[$key] > $maxfraction) {
                                $maxfraction = $subquestion->fraction[$key];
                            }
                        }
                    }
                    if ($answercount == 0) {
                        if ($subquestion->qtype == 'multichoice') {
                            $errors[$prefix.'answer[0]'] =
                                    get_string('notenoughanswers', 'qtype_multichoice', 2);
                        } else {
                            $errors[$prefix.'answer[0]'] =
                                    get_string('notenoughanswers', 'question', 1);
                        }
                    }
                    if ($maxgrade == false) {
                        $errors[$prefix.'fraction[0]'] =
                                get_string('fractionsnomax', 'question');
                    }
                    $sub++;
                }
            } else {
                $errors['questiontext'] = get_string('questionsmissing', 'qtype_multianswer');
            }
        }

        if (($this->negative_diff > 0 || $this->used_in_quiz &&
                ($this->negative_diff > 0 || $this->negative_diff < 0 ||
                        $this->qtype_change)) && !$this->confirm) {
            $errors['confirm'] =
                    get_string('confirmsave', 'qtype_multianswer', $this->negative_diff);
        }

        return $errors;
    }

    public function qtype() {
        return 'multianswer';
    }
}

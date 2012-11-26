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
 * Defines the editing form for the numerical question type.
 *
 * @package    qtype
 * @subpackage numerical
 * @copyright  2007 Jamie Pratt me@jamiep.org
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/edit_question_form.php');
require_once($CFG->dirroot . '/question/type/numerical/questiontype.php');


/**
 * numerical editing form definition.
 *
 * @copyright  2007 Jamie Pratt me@jamiep.org
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_numerical_edit_form extends question_edit_form {
    protected $ap = null;

    protected function definition_inner($mform) {
        $this->add_per_answer_fields($mform, get_string('answerno', 'qtype_numerical', '{no}'),
                question_bank::fraction_options());

        $this->add_unit_options($mform);
        $this->add_unit_fields($mform);
        $this->add_interactive_settings();
    }

    protected function get_per_answer_fields($mform, $label, $gradeoptions,
            &$repeatedoptions, &$answersoption) {
        $repeated = parent::get_per_answer_fields($mform, $label, $gradeoptions,
                $repeatedoptions, $answersoption);

        $tolerance = $mform->createElement('text', 'tolerance',
                get_string('acceptederror', 'qtype_numerical'));
        $repeatedoptions['tolerance']['type'] = PARAM_FLOAT;
        $repeatedoptions['tolerance']['default'] = 0;
        array_splice($repeated, 3, 0, array($tolerance));
        $repeated[1]->setSize(10);

        return $repeated;
    }

    /**
     * Add the unit handling options to the form.
     * @param object $mform the form being built.
     */
    protected function add_unit_options($mform) {

        $mform->addElement('header', 'unithandling',
                get_string('unithandling', 'qtype_numerical'));

        $unitoptions = array(
            qtype_numerical::UNITNONE     => get_string('onlynumerical', 'qtype_numerical'),
            qtype_numerical::UNITOPTIONAL => get_string('manynumerical', 'qtype_numerical'),
            qtype_numerical::UNITGRADED   => get_string('unitgraded', 'qtype_numerical'),
        );
        $mform->addElement('select', 'unitrole',
                get_string('unithandling', 'qtype_numerical'), $unitoptions);

        $penaltygrp = array();
        $penaltygrp[] = $mform->createElement('text', 'unitpenalty',
                get_string('unitpenalty', 'qtype_numerical'), array('size' => 6));
        $mform->setType('unitpenalty', PARAM_FLOAT);
        $mform->setDefault('unitpenalty', 0.1000000);

        $unitgradingtypes = array(
            qtype_numerical::UNITGRADEDOUTOFMARK =>
                    get_string('decfractionofresponsegrade', 'qtype_numerical'),
            qtype_numerical::UNITGRADEDOUTOFMAX =>
                    get_string('decfractionofquestiongrade', 'qtype_numerical'),
        );
        $penaltygrp[] = $mform->createElement('select', 'unitgradingtypes', '', $unitgradingtypes);
        $mform->setDefault('unitgradingtypes', 1);

        $mform->addGroup($penaltygrp, 'penaltygrp',
                get_string('unitpenalty', 'qtype_numerical'), ' ', false);
        $mform->addHelpButton('penaltygrp', 'unitpenalty', 'qtype_numerical');

        $unitinputoptions = array(
            qtype_numerical::UNITINPUT => get_string('editableunittext', 'qtype_numerical'),
            qtype_numerical::UNITRADIO => get_string('unitchoice', 'qtype_numerical'),
            qtype_numerical::UNITSELECT => get_string('unitselect', 'qtype_numerical'),
        );
        $mform->addElement('select', 'multichoicedisplay',
                get_string('studentunitanswer', 'qtype_numerical'), $unitinputoptions);

        $unitsleftoptions = array(
            0 => get_string('rightexample', 'qtype_numerical'),
            1 => get_string('leftexample', 'qtype_numerical')
        );
        $mform->addElement('select', 'unitsleft',
                get_string('unitposition', 'qtype_numerical'), $unitsleftoptions);
        $mform->setDefault('unitsleft', 0);

        $mform->disabledIf('penaltygrp', 'unitrole', 'eq', qtype_numerical::UNITNONE);
        $mform->disabledIf('penaltygrp', 'unitrole', 'eq', qtype_numerical::UNITOPTIONAL);

        $mform->disabledIf('unitsleft', 'unitrole', 'eq', qtype_numerical::UNITNONE);

        $mform->disabledIf('multichoicedisplay', 'unitrole', 'eq', qtype_numerical::UNITNONE);
        $mform->disabledIf('multichoicedisplay', 'unitrole', 'eq', qtype_numerical::UNITOPTIONAL);
    }

    /**
     * Add the input areas for each unit.
     * @param object $mform the form being built.
     */
    protected function add_unit_fields($mform) {
        $repeated = array(
            $mform->createElement('header', 'unithdr',
                    get_string('unithdr', 'qtype_numerical', '{no}')),
            $mform->createElement('text', 'unit', get_string('unit', 'quiz')),
            $mform->createElement('text', 'multiplier', get_string('multiplier', 'quiz')),
        );

        $repeatedoptions['unit']['type'] = PARAM_NOTAGS;
        $repeatedoptions['multiplier']['type'] = PARAM_FLOAT;
        $repeatedoptions['unit']['disabledif'] =
                array('unitrole', 'eq', qtype_numerical::UNITNONE);
        $repeatedoptions['multiplier']['disabledif'] =
                array('unitrole', 'eq', qtype_numerical::UNITNONE);

        if (isset($this->question->options->units)) {
            $countunits = count($this->question->options->units);
        } else {
            $countunits = 0;
        }
        if ($this->question->formoptions->repeatelements) {
            $repeatsatstart = $countunits + 1;
        } else {
            $repeatsatstart = $countunits;
        }
        $this->repeat_elements($repeated, $repeatsatstart, $repeatedoptions, 'nounits',
                'addunits', 2, get_string('addmoreunitblanks', 'qtype_calculated', '{no}'));

        if ($mform->elementExists('multiplier[0]')) {
            $firstunit = $mform->getElement('multiplier[0]');
            $firstunit->freeze();
            $mform->setDefault('multiplier[0]', '1.0');
            $mform->addHelpButton('multiplier[0]', 'numericalmultiplier', 'qtype_numerical');
        }
    }

    protected function data_preprocessing($question) {
        $question = parent::data_preprocessing($question);
        $question = $this->data_preprocessing_answers($question);
        $question = $this->data_preprocessing_hints($question);
        $question = $this->data_preprocessing_units($question);
        $question = $this->data_preprocessing_unit_options($question);
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
            unset($this->_form->_defaultValues["tolerance[$key]"]);

            $question->tolerance[$key] = $answer->tolerance;
            $key++;
        }

        return $question;
    }

    /**
     * Perform the necessary preprocessing for the fields added by
     * {@link add_unit_fields()}.
     * @param object $question the data being passed to the form.
     * @return object $question the modified data.
     */
    protected function data_preprocessing_units($question) {
        if (empty($question->options->units)) {
            return $question;
        }

        foreach ($question->options->units as $key => $unit) {
            $question->unit[$key] = $unit->unit;
            $question->multiplier[$key] = $unit->multiplier;
        }

        return $question;
    }

    /**
     * Perform the necessary preprocessing for the fields added by
     * {@link add_unit_options()}.
     * @param object $question the data being passed to the form.
     * @return object $question the modified data.
     */
    protected function data_preprocessing_unit_options($question) {
        if (empty($question->options)) {
            return $question;
        }

        $question->unitpenalty = $question->options->unitpenalty;
        $question->unitsleft = $question->options->unitsleft;

        if ($question->options->unitgradingtype) {
            $question->unitgradingtypes = $question->options->unitgradingtype;
            $question->multichoicedisplay = $question->options->showunits;
            $question->unitrole = qtype_numerical::UNITGRADED;
        } else {
            $question->unitrole = $question->options->showunits;
        }

        return $question;
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        $errors = $this->validate_answers($data, $errors);
        $errors = $this->validate_numerical_options($data, $errors);
        return $errors;
    }

    /**
     * Validate the answers.
     * @param array $data the submitted data.
     * @param array $errors the errors array to add to.
     * @return array the updated errors array.
     */
    protected function validate_answers($data, $errors) {
        // Check the answers.
        $answercount = 0;
        $maxgrade = false;
        $answers = $data['answer'];
        foreach ($answers as $key => $answer) {
            $trimmedanswer = trim($answer);
            if ($trimmedanswer != '') {
                $answercount++;
                if (!$this->is_valid_answer($trimmedanswer, $data)) {
                    $errors['answer[' . $key . ']'] = $this->valid_answer_message($trimmedanswer);
                }
                if ($data['fraction'][$key] == 1) {
                    $maxgrade = true;
                }
                if ($answer !== '*' && !is_numeric($data['tolerance'][$key])) {
                    $errors['tolerance['.$key.']'] =
                            get_string('mustbenumeric', 'qtype_calculated');
                }
            } else if ($data['fraction'][$key] != 0 ||
                    !html_is_blank($data['feedback'][$key]['text'])) {
                $errors['answer[' . $key . ']'] = $this->valid_answer_message($trimmedanswer);
                $answercount++;
            }
        }
        if ($answercount == 0) {
            $errors['answer[0]'] = get_string('notenoughanswers', 'qtype_numerical');
        }
        if ($maxgrade == false) {
            $errors['fraction[0]'] = get_string('fractionsnomax', 'question');
        }

        return $errors;
    }

    /**
     * Validate a particular answer.
     * @param string $answer an answer to validate. Known to be non-blank and already trimmed.
     * @param array $data the submitted data.
     * @return bool whether this is a valid answer.
     */
    protected function is_valid_answer($answer, $data) {
        return $answer == '*' || $this->is_valid_number($answer);
    }

    /**
     * Validate that a string is a nubmer formatted correctly for the current locale.
     * @param string $x a string
     * @return bool whether $x is a number that the numerical question type can interpret.
     */
    protected function is_valid_number($x) {
        if (is_null($this->ap)) {
            $this->ap = new qtype_numerical_answer_processor(array());
        }

        list($value, $unit) = $this->ap->apply_units($x);

        return !is_null($value) && !$unit;
    }

    /**
     * @return string erre describing what an answer should be.
     */
    protected function valid_answer_message($answer) {
        return get_string('answermustbenumberorstar', 'qtype_numerical');
    }

    /**
     * Validate the answers.
     * @param array $data the submitted data.
     * @param array $errors the errors array to add to.
     * @return array the updated errors array.
     */
    protected function validate_numerical_options($data, $errors) {
        if ($data['unitrole'] != qtype_numerical::UNITNONE && trim($data['unit'][0]) == '') {
            $errors['unit[0]'] = get_string('unitonerequired', 'qtype_numerical');
        }

        if (empty($data['unit'])) {
            return $errors;
        }

        // Basic unit validation.
        foreach ($data['unit'] as $key => $unit) {
            if (is_numeric($unit)) {
                $errors['unit[' . $key . ']'] =
                        get_string('mustnotbenumeric', 'qtype_calculated');
            }

            $trimmedunit = trim($unit);
            if (empty($trimmedunit)) {
                continue;
            }

            $trimmedmultiplier = trim($data['multiplier'][$key]);
            if (empty($trimmedmultiplier)) {
                $errors['multiplier[' . $key . ']'] =
                        get_string('youmustenteramultiplierhere', 'qtype_calculated');
            } else if (!is_numeric($trimmedmultiplier)) {
                $errors['multiplier[' . $key . ']'] =
                        get_string('mustbenumeric', 'qtype_calculated');
            }
        }

        // Check for repeated units.
        $alreadyseenunits = array();
        foreach ($data['unit'] as $key => $unit) {
            $trimmedunit = trim($unit);
            if ($trimmedunit == '') {
                continue;
            }

            if (in_array($trimmedunit, $alreadyseenunits)) {
                $errors['unit[' . $key . ']'] =
                        get_string('errorrepeatedunit', 'qtype_numerical');
            } else {
                $alreadyseenunits[] = $trimmedunit;
            }
        }

        return $errors;
    }

    public function qtype() {
        return 'numerical';
    }
}

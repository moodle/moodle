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


/**
 * numerical editing form definition.
 *
 * @copyright  2007 Jamie Pratt me@jamiep.org
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_numerical_edit_form extends question_edit_form {

    protected function definition_inner($mform) {
        $creategrades = get_grade_options();
        $this->add_per_answer_fields($mform, get_string('answerno', 'qtype_numerical', '{no}'),
                $creategrades->gradeoptions);

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
        $repeatedoptions['tolerance']['type'] = PARAM_NUMBER;
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
            qtype_numerical::UNITDISPLAY  => get_string('oneunitshown', 'qtype_numerical'),
            qtype_numerical::UNITOPTIONAL => get_string('manynumerical', 'qtype_numerical'),
            qtype_numerical::UNITGRADED   => get_string('unitgraded', 'qtype_numerical'),
        );
        $mform->addElement('select', 'unitrole',
                get_string('unithandling', 'qtype_numerical'), $unitoptions);

        $penaltygrp = array();
        $penaltygrp[] = $mform->createElement('text', 'unitpenalty',
                get_string('unitpenalty', 'qtype_numerical'), array('size' => 6));
        $mform->setType('unitpenalty', PARAM_NUMBER);
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
            qtype_numerical::UNITSELECT => get_string('unitchoice', 'qtype_numerical'),
        );
        $mform->addElement('select', 'multichoicedisplay',
                get_string('studentunitanswer', 'qtype_numerical'), $unitinputoptions);

        $unitslefts = array(
            0 => get_string('rightexample', 'qtype_numerical'),
            1 => get_string('leftexample', 'qtype_numerical')
        );
        $mform->addElement('select', 'unitsleft',
                get_string('unitposition', 'qtype_numerical'), $unitslefts);
        $mform->setDefault('unitsleft', 0);

        $mform->addElement('editor', 'instructions',
                get_string('instructions', 'qtype_numerical'), null, $this->editoroptions);
        $mform->setType('instructions', PARAM_RAW);
        $mform->addHelpButton('instructions', 'numericalinstructions', 'qtype_numerical');

        $mform->disabledIf('penaltygrp', 'unitrole', 'eq', qtype_numerical::UNITNONE);
        $mform->disabledIf('penaltygrp', 'unitrole', 'eq', qtype_numerical::UNITDISPLAY);
        $mform->disabledIf('penaltygrp', 'unitrole', 'eq', qtype_numerical::UNITOPTIONAL);

        $mform->disabledIf('unitsleft', 'unitrole', 'eq', qtype_numerical::UNITNONE);

        $mform->disabledIf('multichoicedisplay', 'unitrole', 'eq', qtype_numerical::UNITNONE);
        $mform->disabledIf('multichoicedisplay', 'unitrole', 'eq', qtype_numerical::UNITDISPLAY);
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
        $repeatedoptions['multiplier']['type'] = PARAM_NUMBER;
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
            $firstunit->setValue('1.0');
            $firstunit->setPersistantFreeze(true);
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

    protected function data_preprocessing_answers($question) {
        $question = parent::data_preprocessing_answers($question);
        if (empty($question->options->answers)) {
            return $question;
        }

        $key = 0;
        foreach ($question->options->answers as $answer) {
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

        // Instructions field.
        $draftitemid = file_get_submitted_draft_itemid('instruction');
        $question->instructions['text'] = file_prepare_draft_area(
            $draftitemid,                    // draftid
            $this->context->id,              // context
            'qtype_' . $this->qtype(),       // component
            'instruction',                   // filarea
            !empty($question->id) ? (int) $question->id : null, // itemid
            $this->fileoptions,              // options
            $question->options->instructions // text
        );
        $question->instructions['itemid'] = $draftitemid ;
        $question->instructions['format'] = $question->options->instructionsformat;

        return $question;
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        // Check the answers.
        $answercount = 0;
        $maxgrade = false;
        $answers = $data['answer'];
        foreach ($answers as $key => $answer) {
            $trimmedanswer = trim($answer);
            if ($trimmedanswer != '') {
                $answercount++;
                if (!(is_numeric($trimmedanswer) || $trimmedanswer == '*')) {
                    $errors['answer[' . $key . ']'] =
                            get_string('answermustbenumberorstar', 'qtype_numerical');
                }
                if ($data['fraction'][$key] == 1) {
                    $maxgrade = true;
                }
            } else if ($data['fraction'][$key] != 0 ||
                    !html_is_blank($data['feedback'][$key]['text'])) {
                $errors['answer[' . $key . ']'] =
                        get_string('answermustbenumberorstar', 'qtype_numerical');
                $answercount++;
            }
        }
        if ($answercount == 0) {
            $errors['answer[0]'] = get_string('notenoughanswers', 'qtype_numerical');
        }
        if ($maxgrade == false) {
            $errors['fraction[0]'] = get_string('fractionsnomax', 'question');
        }

        $errors = $this->validate_numerical_options($data, $errors);

        return $errors;
    }

    /**
      * Validate the unit options.
      */
    function validate_numerical_options($data, $errors) {
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

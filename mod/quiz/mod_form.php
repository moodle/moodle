<?php

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 1999 onwards Martin Dougiamas  http://dougiamas.com     //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->dirroot . '/course/moodleform_mod.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');

/**
 * Settings form for the quiz module.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package quiz
 */
class mod_quiz_mod_form extends moodleform_mod {
    var $_feedbacks;

    function definition() {

        global $COURSE, $CFG, $DB, $PAGE;
        $quizconfig = get_config('quiz');
        $mform    =& $this->_form;

//-------------------------------------------------------------------------------
        $mform->addElement('header', 'general', get_string('general', 'form'));

    /// Name.
        $mform->addElement('text', 'name', get_string('name'), array('size'=>'64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');

    /// Introduction.
        $this->add_intro_editor(false, get_string('introduction', 'quiz'));

    /// Open and close dates.
        $mform->addElement('date_time_selector', 'timeopen', get_string('quizopen', 'quiz'), array('optional' => true));
        $mform->addElement('date_time_selector', 'timeclose', get_string('quizclose', 'quiz'), array('optional' => true));

    /// Time limit.
        $mform->addElement('duration', 'timelimit', get_string('timelimit', 'quiz'), array('optional' => true));
        $mform->addHelpButton('timelimit', 'timelimit', 'quiz');
        $mform->setAdvanced('timelimit', $quizconfig->timelimit_adv);
        $mform->setDefault('timelimit', $quizconfig->timelimit);

    /// Number of attempts.
        $attemptoptions = array('0' => get_string('unlimited'));
        for ($i = 1; $i <= QUIZ_MAX_ATTEMPT_OPTION; $i++) {
            $attemptoptions[$i] = $i;
        }
        $mform->addElement('select', 'attempts', get_string('attemptsallowed', 'quiz'), $attemptoptions);
        $mform->setAdvanced('attempts', $quizconfig->attempts_adv);
        $mform->setDefault('attempts', $quizconfig->attempts);

    /// Grading method.
        $mform->addElement('select', 'grademethod', get_string('grademethod', 'quiz'), quiz_get_grading_options());
        $mform->addHelpButton('grademethod', 'grademethod', 'quiz');
        $mform->setAdvanced('grademethod', $quizconfig->grademethod_adv);
        $mform->setDefault('grademethod', $quizconfig->grademethod);
        $mform->disabledIf('grademethod', 'attempts', 'eq', 1);

//-------------------------------------------------------------------------------
        $mform->addElement('header', 'layouthdr', get_string('layout', 'quiz'));

    /// Shuffle questions.
        $shuffleoptions = array(0 => get_string('asshownoneditscreen', 'quiz'), 1 => get_string('shuffledrandomly', 'quiz'));
        $mform->addElement('select', 'shufflequestions', get_string('questionorder', 'quiz'), $shuffleoptions, array('id' => 'id_shufflequestions'));
        $mform->setAdvanced('shufflequestions', $quizconfig->shufflequestions_adv);
        $mform->setDefault('shufflequestions', $quizconfig->shufflequestions);

    /// Questions per page.
        $pageoptions = array();
        $pageoptions[0] = get_string('neverallononepage', 'quiz');
        $pageoptions[1] = get_string('everyquestion', 'quiz');
        for ($i = 2; $i <= QUIZ_MAX_QPP_OPTION; ++$i) {
            $pageoptions[$i] = get_string('everynquestions', 'quiz', $i);
        }

        $pagegroup = array();
        $pagegroup[] = &$mform->createElement('select', 'questionsperpage', get_string('newpage', 'quiz'), $pageoptions, array('id' => 'id_questionsperpage'));
        $mform->setDefault('questionsperpage', $quizconfig->questionsperpage);

        if (!empty($this->_cm)) {
            $pagegroup[] = &$mform->createElement('checkbox', 'repaginatenow', '', get_string('repaginatenow', 'quiz'), array('id' => 'id_repaginatenow'));
            $mform->disabledIf('repaginatenow', 'shufflequestions', 'eq', 1);
            $PAGE->requires->yui2_lib('event');
            $PAGE->requires->js('/mod/quiz/edit.js');
        }

        $mform->addGroup($pagegroup, 'questionsperpagegrp', get_string('newpage', 'quiz'), null, false);
        $mform->addHelpButton('questionsperpagegrp', 'newpage', 'quiz');
        $mform->setAdvanced('questionsperpagegrp', $quizconfig->questionsperpage_adv);

//-------------------------------------------------------------------------------
        $mform->addElement('header', 'interactionhdr', get_string('questionbehaviour', 'quiz'));

    /// Shuffle within questions.
        $mform->addElement('selectyesno', 'shuffleanswers', get_string('shufflewithin', 'quiz'));
        $mform->addHelpButton('shuffleanswers', 'shufflewithin', 'quiz');
        $mform->setAdvanced('shuffleanswers', $quizconfig->shuffleanswers_adv);
        $mform->setDefault('shuffleanswers', $quizconfig->shuffleanswers);

    /// Adaptive mode.
        $mform->addElement('selectyesno', 'adaptive', get_string('adaptive', 'quiz'));
        $mform->addHelpButton('adaptive', 'adaptive', 'quiz');
        $mform->setAdvanced('adaptive', $quizconfig->optionflags_adv);
        $mform->setDefault('adaptive', $quizconfig->optionflags & QUESTION_ADAPTIVE);

    /// Apply penalties.
        $mform->addElement('selectyesno', 'penaltyscheme', get_string('penaltyscheme', 'quiz'));
        $mform->addHelpButton('penaltyscheme', 'penaltyscheme', 'quiz');
        $mform->setAdvanced('penaltyscheme', $quizconfig->penaltyscheme_adv);
        $mform->setDefault('penaltyscheme', $quizconfig->penaltyscheme);
        $mform->disabledIf('penaltyscheme', 'adaptive', 'neq', 1);

    /// Each attempt builds on last.
        $mform->addElement('selectyesno', 'attemptonlast', get_string('eachattemptbuildsonthelast', 'quiz'));
        $mform->addHelpButton('attemptonlast', 'eachattemptbuildsonthelast', 'quiz');
        $mform->setAdvanced('attemptonlast', $quizconfig->attemptonlast_adv);
        $mform->setDefault('attemptonlast', $quizconfig->attemptonlast);
        $mform->disabledIf('attemptonlast', 'attempts', 'eq', 1);

//-------------------------------------------------------------------------------
        $mform->addElement('header', 'reviewoptionshdr', get_string('reviewoptionsheading', 'quiz'));
        $mform->addHelpButton('reviewoptionshdr', 'reviewoptionsheading', 'quiz');
        $mform->setAdvanced('reviewoptionshdr', $quizconfig->review_adv);

    /// Review options.
        $immediatelyoptionsgrp=array();
        $immediatelyoptionsgrp[] = &$mform->createElement('checkbox', 'responsesimmediately', '', get_string('responses', 'quiz'));
        $immediatelyoptionsgrp[] = &$mform->createElement('checkbox', 'answersimmediately', '', get_string('answers', 'quiz'));
        $immediatelyoptionsgrp[] = &$mform->createElement('checkbox', 'feedbackimmediately', '', get_string('feedback', 'quiz'));
        $immediatelyoptionsgrp[] = &$mform->createElement('checkbox', 'generalfeedbackimmediately', '', get_string('generalfeedback', 'quiz'));
        $immediatelyoptionsgrp[] = &$mform->createElement('checkbox', 'scoreimmediately', '', get_string('scores', 'quiz'));
        $immediatelyoptionsgrp[] = &$mform->createElement('checkbox', 'overallfeedbackimmediately', '', get_string('overallfeedback', 'quiz'));
        $mform->addGroup($immediatelyoptionsgrp, 'immediatelyoptionsgrp', get_string('reviewimmediately', 'quiz'), null, false);
        $mform->setDefault('responsesimmediately', $quizconfig->review & QUIZ_REVIEW_RESPONSES & QUIZ_REVIEW_IMMEDIATELY);
        $mform->setDefault('answersimmediately', $quizconfig->review & QUIZ_REVIEW_ANSWERS & QUIZ_REVIEW_IMMEDIATELY);
        $mform->setDefault('feedbackimmediately', $quizconfig->review & QUIZ_REVIEW_FEEDBACK & QUIZ_REVIEW_IMMEDIATELY);
        $mform->setDefault('generalfeedbackimmediately', $quizconfig->review & QUIZ_REVIEW_GENERALFEEDBACK & QUIZ_REVIEW_IMMEDIATELY);
        $mform->setDefault('scoreimmediately', $quizconfig->review & QUIZ_REVIEW_SCORES & QUIZ_REVIEW_IMMEDIATELY);
        $mform->setDefault('overallfeedbackimmediately', $quizconfig->review & QUIZ_REVIEW_OVERALLFEEDBACK & QUIZ_REVIEW_IMMEDIATELY);

        $openoptionsgrp=array();
        $openoptionsgrp[] = &$mform->createElement('checkbox', 'responsesopen', '', get_string('responses', 'quiz'));
        $openoptionsgrp[] = &$mform->createElement('checkbox', 'answersopen', '', get_string('answers', 'quiz'));
        $openoptionsgrp[] = &$mform->createElement('checkbox', 'feedbackopen', '', get_string('feedback', 'quiz'));
        $openoptionsgrp[] = &$mform->createElement('checkbox', 'generalfeedbackopen', '', get_string('generalfeedback', 'quiz'));
        $openoptionsgrp[] = &$mform->createElement('checkbox', 'scoreopen', '', get_string('scores', 'quiz'));
        $openoptionsgrp[] = &$mform->createElement('checkbox', 'overallfeedbackopen', '', get_string('overallfeedback', 'quiz'));
        $mform->addGroup($openoptionsgrp, 'openoptionsgrp', get_string('reviewopen', 'quiz'), array(' '), false);
        $mform->setDefault('responsesopen', $quizconfig->review & QUIZ_REVIEW_RESPONSES & QUIZ_REVIEW_OPEN);
        $mform->setDefault('answersopen', $quizconfig->review & QUIZ_REVIEW_ANSWERS & QUIZ_REVIEW_OPEN);
        $mform->setDefault('feedbackopen', $quizconfig->review & QUIZ_REVIEW_FEEDBACK & QUIZ_REVIEW_OPEN);
        $mform->setDefault('generalfeedbackopen', $quizconfig->review & QUIZ_REVIEW_GENERALFEEDBACK & QUIZ_REVIEW_OPEN);
        $mform->setDefault('scoreopen', $quizconfig->review & QUIZ_REVIEW_SCORES & QUIZ_REVIEW_OPEN);
        $mform->setDefault('overallfeedbackopen', $quizconfig->review & QUIZ_REVIEW_OVERALLFEEDBACK & QUIZ_REVIEW_OPEN);

        $closedoptionsgrp=array();
        $closedoptionsgrp[] = &$mform->createElement('checkbox', 'responsesclosed', '', get_string('responses', 'quiz'));
        $closedoptionsgrp[] = &$mform->createElement('checkbox', 'answersclosed', '', get_string('answers', 'quiz'));
        $closedoptionsgrp[] = &$mform->createElement('checkbox', 'feedbackclosed', '', get_string('feedback', 'quiz'));
        $closedoptionsgrp[] = &$mform->createElement('checkbox', 'generalfeedbackclosed', '', get_string('generalfeedback', 'quiz'));
        $closedoptionsgrp[] = &$mform->createElement('checkbox', 'scoreclosed', '', get_string('scores', 'quiz'));
        $closedoptionsgrp[] = &$mform->createElement('checkbox', 'overallfeedbackclosed', '', get_string('overallfeedback', 'quiz'));
        $mform->addGroup($closedoptionsgrp, 'closedoptionsgrp', get_string('reviewclosed', 'quiz'), array(' '), false);
        $mform->setDefault('responsesclosed', $quizconfig->review & QUIZ_REVIEW_RESPONSES & QUIZ_REVIEW_CLOSED);
        $mform->setDefault('answersclosed', $quizconfig->review & QUIZ_REVIEW_ANSWERS & QUIZ_REVIEW_CLOSED);
        $mform->setDefault('feedbackclosed', $quizconfig->review & QUIZ_REVIEW_FEEDBACK & QUIZ_REVIEW_CLOSED);
        $mform->setDefault('generalfeedbackclosed', $quizconfig->review & QUIZ_REVIEW_GENERALFEEDBACK & QUIZ_REVIEW_CLOSED);
        $mform->setDefault('scoreclosed', $quizconfig->review & QUIZ_REVIEW_SCORES & QUIZ_REVIEW_CLOSED);
        $mform->setDefault('overallfeedbackclosed', $quizconfig->review & QUIZ_REVIEW_OVERALLFEEDBACK & QUIZ_REVIEW_CLOSED);
        $mform->disabledIf('closedoptionsgrp', 'timeclose[enabled]');

//-------------------------------------------------------------------------------
        $mform->addElement('header', 'display', get_string('display', 'form'));

    /// Show user picture.
        $mform->addElement('selectyesno', 'showuserpicture', get_string('showuserpicture', 'quiz'));
        $mform->addHelpButton('showuserpicture', 'showuserpicture', 'quiz');
        $mform->setAdvanced('showuserpicture', $quizconfig->showuserpicture_adv);
        $mform->setDefault('showuserpicture', $quizconfig->showuserpicture);

    /// Overall decimal points.
        $options = array();
        for ($i = 0; $i <= QUIZ_MAX_DECIMAL_OPTION; $i++) {
            $options[$i] = $i;
        }
        $mform->addElement('select', 'decimalpoints', get_string('decimalplaces', 'quiz'), $options);
        $mform->addHelpButton('decimalpoints', 'decimalplaces', 'quiz');
        $mform->setAdvanced('decimalpoints', $quizconfig->decimalpoints_adv);
        $mform->setDefault('decimalpoints', $quizconfig->decimalpoints);

    /// Question decimal points.
        $options = array(-1 => get_string('sameasoverall', 'quiz'));
        for ($i = 0; $i <= QUIZ_MAX_Q_DECIMAL_OPTION; $i++) {
            $options[$i] = $i;
        }
        $mform->addElement('select', 'questiondecimalpoints', get_string('decimalplacesquestion', 'quiz'), $options);
        $mform->addHelpButton('questiondecimalpoints', 'decimalplacesquestion','quiz');
        $mform->setAdvanced('questiondecimalpoints', $quizconfig->questiondecimalpoints_adv);
        $mform->setDefault('questiondecimalpoints', $quizconfig->questiondecimalpoints);

        // Show blocks during quiz attempt
        $mform->addElement('selectyesno', 'showblocks', get_string('showblocks', 'quiz'));
        $mform->addHelpButton('showblocks', 'showblocks', 'quiz');
        $mform->setAdvanced('showblocks', $quizconfig->showblocks_adv);
        $mform->setDefault('showblocks', $quizconfig->showblocks);

//-------------------------------------------------------------------------------
        $mform->addElement('header', 'security', get_string('extraattemptrestrictions', 'quiz'));

    /// Enforced time delay between quiz attempts.
        $mform->addElement('passwordunmask', 'quizpassword', get_string('requirepassword', 'quiz'));
        $mform->setType('quizpassword', PARAM_TEXT);
        $mform->addHelpButton('quizpassword', 'requirepassword', 'quiz');
        $mform->setAdvanced('quizpassword', $quizconfig->password_adv);
        $mform->setDefault('quizpassword', $quizconfig->password);

    /// IP address.
        $mform->addElement('text', 'subnet', get_string('requiresubnet', 'quiz'));
        $mform->setType('subnet', PARAM_TEXT);
        $mform->addHelpButton('subnet', 'requiresubnet', 'quiz');
        $mform->setAdvanced('subnet', $quizconfig->subnet_adv);
        $mform->setDefault('subnet', $quizconfig->subnet);

    /// Enforced time delay between quiz attempts.
        $mform->addElement('duration', 'delay1', get_string('delay1st2nd', 'quiz'), array('optional' => true));
        $mform->addHelpButton('delay1', 'delay1st2nd', 'quiz');
        $mform->setAdvanced('delay1', $quizconfig->delay1_adv);
        $mform->setDefault('delay1', $quizconfig->delay1);
        $mform->disabledIf('delay1', 'attempts', 'eq', 1);

        $mform->addElement('duration', 'delay2', get_string('delaylater', 'quiz'), array('optional' => true));
        $mform->addHelpButton('delay2', 'delaylater', 'quiz');
        $mform->setAdvanced('delay2', $quizconfig->delay2_adv);
        $mform->setDefault('delay2', $quizconfig->delay2);
        $mform->disabledIf('delay2', 'attempts', 'eq', 1);
        $mform->disabledIf('delay2', 'attempts', 'eq', 2);

    /// 'Secure' window.
        $options = array(
                    0 => get_string('none', 'quiz'),
                    1 => get_string('popupwithjavascriptsupport', 'quiz'));
        if (!empty($CFG->enablesafebrowserintegration)) {
            $options[2] = get_string('requiresafeexambrowser', 'quiz');
        }
        $mform->addElement('select', 'popup', get_string('browsersecurity', 'quiz'), $options);
        $mform->addHelpButton('popup', 'browsersecurity', 'quiz');
        $mform->setAdvanced('popup', $quizconfig->popup_adv);
        $mform->setDefault('popup', $quizconfig->popup);

//-------------------------------------------------------------------------------
        $mform->addElement('header', 'overallfeedbackhdr', get_string('overallfeedback', 'quiz'));
        $mform->addHelpButton('overallfeedbackhdr', 'overallfeedback', 'quiz');

        $mform->addElement('hidden', 'grade', $quizconfig->maximumgrade);
        $mform->setType('grade', PARAM_RAW);
        if (empty($this->_cm)) {
            $needwarning = $quizconfig->maximumgrade == 0;
        } else {
            $quizgrade = $DB->get_field('quiz', 'grade', array('id' => $this->_instance));
            $needwarning = $quizgrade == 0;
        }
        if ($needwarning) {
            $mform->addElement('static', 'nogradewarning', '', get_string('nogradewarning', 'quiz'));
        }

        $mform->addElement('static', 'gradeboundarystatic1', get_string('gradeboundary', 'quiz'), '100%');

        $repeatarray = array();
        $repeatarray[] = &MoodleQuickForm::createElement('editor', 'feedbacktext', get_string('feedback', 'quiz'), null, array('maxfiles'=>EDITOR_UNLIMITED_FILES, 'noclean'=>true, 'context'=>$this->context));
        $mform->setType('feedbacktext', PARAM_RAW);
        $repeatarray[] = &MoodleQuickForm::createElement('text', 'feedbackboundaries', get_string('gradeboundary', 'quiz'), array('size' => 10));
        $mform->setType('feedbackboundaries', PARAM_NOTAGS);

        if (!empty($this->_instance)) {
            $this->_feedbacks = $DB->get_records('quiz_feedback', array('quizid'=>$this->_instance), 'mingrade DESC');
        } else {
            $this->_feedbacks = array();
        }
        $numfeedbacks = max(count($this->_feedbacks) * 1.5, 5);

        $nextel=$this->repeat_elements($repeatarray, $numfeedbacks - 1,
                array(), 'boundary_repeats', 'boundary_add_fields', 3,
                get_string('addmoreoverallfeedbacks', 'quiz'), true);

        // Put some extra elements in before the button
        $insertEl = &MoodleQuickForm::createElement('editor', "feedbacktext[$nextel]", get_string('feedback', 'quiz'), null, array('maxfiles'=>EDITOR_UNLIMITED_FILES, 'noclean'=>true, 'context'=>$this->context));
        $mform->insertElementBefore($insertEl, 'boundary_add_fields');

        $insertEl = &MoodleQuickForm::createElement('static', 'gradeboundarystatic2', get_string('gradeboundary', 'quiz'), '0%');
        $mform->insertElementBefore($insertEl, 'boundary_add_fields');

        // Add the disabledif rules. We cannot do this using the $repeatoptions parameter to
        // repeat_elements becuase we don't want to dissable the first feedbacktext.
        for ($i = 0; $i < $nextel; $i++) {
            $mform->disabledIf('feedbackboundaries[' . $i . ']', 'grade', 'eq', 0);
            $mform->disabledIf('feedbacktext[' . ($i + 1) . ']', 'grade', 'eq', 0);
        }

//-------------------------------------------------------------------------------
        $this->standard_coursemodule_elements();

//-------------------------------------------------------------------------------
        // buttons
        $this->add_action_buttons();
    }

    function data_preprocessing(&$default_values){
        if (isset($default_values['grade'])) {
            $default_values['grade'] = $default_values['grade'] + 0; // Convert to a real number, so we don't get 0.0000.
        }

        if (count($this->_feedbacks)) {
            $key = 0;
            foreach ($this->_feedbacks as $feedback){
                $draftid = file_get_submitted_draft_itemid('feedbacktext['.$key.']');
                $default_values['feedbacktext['.$key.']']['text'] = file_prepare_draft_area(
                    $draftid,               // draftid
                    $this->context->id,     // context
                    'mod_quiz',             // component
                    'feedback',             // filarea
                    !empty($feedback->id) ? (int) $feedback->id : null, // itemid
                    null,
                    $feedback->feedbacktext // text
                );
                $default_values['feedbacktext['.$key.']']['format'] = $feedback->feedbacktextformat;
                $default_values['feedbacktext['.$key.']']['itemid'] = $draftid;

                if ($default_values['grade'] == 0) {
                    // When a quiz is un-graded, there can only be one lot of
                    // feedback. If the quiz previously had a maximum grade and
                    // several lots of feedback, we must now avoid putting text
                    // into input boxes that are disabled, but which the
                    // validation will insist are blank.
                    break;
                }

                if ($feedback->mingrade > 0) {
                    $default_values['feedbackboundaries['.$key.']'] = (100.0 * $feedback->mingrade / $default_values['grade']) . '%';
                }
                $key++;
            }
        }

        if (isset($default_values['timelimit'])) {
            $default_values['timelimitenable'] = $default_values['timelimit'] > 0;
        }

        if (isset($default_values['review'])){
            $review = (int)$default_values['review'];
            unset($default_values['review']);

            $default_values['responsesimmediately'] = $review & QUIZ_REVIEW_RESPONSES & QUIZ_REVIEW_IMMEDIATELY;
            $default_values['answersimmediately'] = $review & QUIZ_REVIEW_ANSWERS & QUIZ_REVIEW_IMMEDIATELY;
            $default_values['feedbackimmediately'] = $review & QUIZ_REVIEW_FEEDBACK & QUIZ_REVIEW_IMMEDIATELY;
            $default_values['generalfeedbackimmediately'] = $review & QUIZ_REVIEW_GENERALFEEDBACK & QUIZ_REVIEW_IMMEDIATELY;
            $default_values['scoreimmediately'] = $review & QUIZ_REVIEW_SCORES & QUIZ_REVIEW_IMMEDIATELY;
            $default_values['overallfeedbackimmediately'] = $review & QUIZ_REVIEW_OVERALLFEEDBACK & QUIZ_REVIEW_IMMEDIATELY;

            $default_values['responsesopen'] = $review & QUIZ_REVIEW_RESPONSES & QUIZ_REVIEW_OPEN;
            $default_values['answersopen'] = $review & QUIZ_REVIEW_ANSWERS & QUIZ_REVIEW_OPEN;
            $default_values['feedbackopen'] = $review & QUIZ_REVIEW_FEEDBACK & QUIZ_REVIEW_OPEN;
            $default_values['generalfeedbackopen'] = $review & QUIZ_REVIEW_GENERALFEEDBACK & QUIZ_REVIEW_OPEN;
            $default_values['scoreopen'] = $review & QUIZ_REVIEW_SCORES & QUIZ_REVIEW_OPEN;
            $default_values['overallfeedbackopen'] = $review & QUIZ_REVIEW_OVERALLFEEDBACK & QUIZ_REVIEW_OPEN;

            $default_values['responsesclosed'] = $review & QUIZ_REVIEW_RESPONSES & QUIZ_REVIEW_CLOSED;
            $default_values['answersclosed'] = $review & QUIZ_REVIEW_ANSWERS & QUIZ_REVIEW_CLOSED;
            $default_values['feedbackclosed'] = $review & QUIZ_REVIEW_FEEDBACK & QUIZ_REVIEW_CLOSED;
            $default_values['generalfeedbackclosed'] = $review & QUIZ_REVIEW_GENERALFEEDBACK & QUIZ_REVIEW_CLOSED;
            $default_values['scoreclosed'] = $review & QUIZ_REVIEW_SCORES & QUIZ_REVIEW_CLOSED;
            $default_values['overallfeedbackclosed'] = $review & QUIZ_REVIEW_OVERALLFEEDBACK & QUIZ_REVIEW_CLOSED;
        }

        if (isset($default_values['optionflags'])){
            $default_values['adaptive'] = $default_values['optionflags'] & QUESTION_ADAPTIVE;
            unset($default_values['optionflags']);
        }

        // Password field - different in form to stop browsers that remember passwords
        // getting confused.
        if (isset($default_values['password'])) {
            $default_values['quizpassword'] = $default_values['password'];
            unset($default_values['password']);
        }
    }

    function validation($data, $files) {
        $errors = parent::validation($data, $files);

        // Check open and close times are consistent.
        if ($data['timeopen'] != 0 && $data['timeclose'] != 0 && $data['timeclose'] < $data['timeopen']) {
            $errors['timeclose'] = get_string('closebeforeopen', 'quiz');
        }

        // Check the boundary value is a number or a percentage, and in range.
        $i = 0;
        while (!empty($data['feedbackboundaries'][$i] )) {
            $boundary = trim($data['feedbackboundaries'][$i]);
            if (strlen($boundary) > 0 && $boundary[strlen($boundary) - 1] == '%') {
                $boundary = trim(substr($boundary, 0, -1));
                if (is_numeric($boundary)) {
                    $boundary = $boundary * $data['grade'] / 100.0;
                } else {
                    $errors["feedbackboundaries[$i]"] = get_string('feedbackerrorboundaryformat', 'quiz', $i + 1);
                }
            }
            if (is_numeric($boundary) && $boundary <= 0 || $boundary >= $data['grade'] ) {
                $errors["feedbackboundaries[$i]"] = get_string('feedbackerrorboundaryoutofrange', 'quiz', $i + 1);
            }
            if (is_numeric($boundary) && $i > 0 && $boundary >= $data['feedbackboundaries'][$i - 1]) {
                $errors["feedbackboundaries[$i]"] = get_string('feedbackerrororder', 'quiz', $i + 1);
            }
            $data['feedbackboundaries'][$i] = $boundary;
            $i += 1;
        }
        $numboundaries = $i;

        // Check there is nothing in the remaining unused fields.
        if (!empty($data['feedbackboundaries'])) {
            for ($i = $numboundaries; $i < count($data['feedbackboundaries']); $i += 1) {
                if (!empty($data['feedbackboundaries'][$i] ) && trim($data['feedbackboundaries'][$i] ) != '') {
                    $errors["feedbackboundaries[$i]"] = get_string('feedbackerrorjunkinboundary', 'quiz', $i + 1);
                }
            }
        }
        for ($i = $numboundaries + 1; $i < count($data['feedbacktext']); $i += 1) {
            if (!empty($data['feedbacktext'][$i]['text']) && trim($data['feedbacktext'][$i]['text'] ) != '') {
                $errors["feedbacktext[$i]"] = get_string('feedbackerrorjunkinfeedback', 'quiz', $i + 1);
            }
        }

        return $errors;
    }

}


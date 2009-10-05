<?php // $Id$
require_once ($CFG->dirroot.'/course/moodleform_mod.php');

require_once("$CFG->dirroot/mod/quiz/locallib.php");

class mod_quiz_mod_form extends moodleform_mod {
    var $_feedbacks;

    function definition() {

        global $COURSE, $CFG;
        $mform    =& $this->_form;

//-------------------------------------------------------------------------------
        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('text', 'name', get_string('name'), array('size'=>'64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEAN);
        }
        $mform->addRule('name', null, 'required', null, 'client');

        $mform->addElement('htmleditor', 'intro', get_string("introduction", "quiz"));
        $mform->setType('intro', PARAM_RAW);
        $mform->setHelpButton('intro', array('richtext', get_string('helprichtext')));

//-------------------------------------------------------------------------------
        $mform->addElement('header', 'timinghdr', get_string('timing', 'form'));
        $mform->addElement('date_time_selector', 'timeopen', get_string('quizopen', 'quiz'), array('optional'=>true));
        $mform->setHelpButton('timeopen', array('timeopen', get_string('quizopen', 'quiz'), 'quiz'));

        $mform->addElement('date_time_selector', 'timeclose', get_string('quizclose', 'quiz'), array('optional'=>true));
        $mform->setHelpButton('timeclose', array('timeopen', get_string('quizclose', 'quiz'), 'quiz'));


        $timelimitgrp=array();
        $timelimitgrp[] = &$mform->createElement('text', 'timelimit');
        $timelimitgrp[] = &$mform->createElement('checkbox', 'timelimitenable', '', get_string('enable'));
        $mform->addGroup($timelimitgrp, 'timelimitgrp', get_string('timelimitmin', 'quiz'), array(' '), false);
        $mform->setType('timelimit', PARAM_TEXT);
        $timelimitgrprules = array();
        $timelimitgrprules['timelimit'][] = array(null, 'numeric', null, 'client');
        $mform->addGroupRule('timelimitgrp', $timelimitgrprules);
        $mform->disabledIf('timelimitgrp', 'timelimitenable');
        $mform->setAdvanced('timelimitgrp', $CFG->quiz_fix_timelimit);
        $mform->setHelpButton('timelimitgrp', array("timelimit", get_string("quiztimer","quiz"), "quiz"));
        $mform->setDefault('timelimit', $CFG->quiz_timelimit);
        $mform->setDefault('timelimitenable', !empty($CFG->quiz_timelimit));


        //enforced time delay between quiz attempts add-on
        $timedelayoptions = array();
        $timedelayoptions[0] = get_string('none');
        $timedelayoptions[1800] = get_string('numminutes', '', 30);
        $timedelayoptions[3600] = get_string('numminutes', '', 60);
        for($i=2; $i<=23; $i++) {
             $seconds  = $i*3600;
             $timedelayoptions[$seconds] = get_string('numhours', '', $i);
        }
        $timedelayoptions[86400] = get_string('numhours', '', 24);
        for($i=2; $i<=7; $i++) {
             $seconds = $i*86400;
             $timedelayoptions[$seconds] = get_string('numdays', '', $i);
        }
        $mform->addElement('select', 'delay1', get_string("delay1", "quiz"), $timedelayoptions);
        $mform->setHelpButton('delay1', array("timedelay1", get_string("delay1", "quiz"), "quiz"));
        $mform->setAdvanced('delay1', $CFG->quiz_fix_delay1);
        $mform->setDefault('delay1', $CFG->quiz_delay1);

        $mform->addElement('select', 'delay2', get_string("delay2", "quiz"), $timedelayoptions);
        $mform->setHelpButton('delay2', array("timedelay2", get_string("delay2", "quiz"), "quiz"));
        $mform->setAdvanced('delay2', $CFG->quiz_fix_delay2);
        $mform->setDefault('delay2', $CFG->quiz_delay2);

//-------------------------------------------------------------------------------
        $mform->addElement('header', 'displayhdr', get_string('display', 'form'));
        $perpage = array();
        for ($i = 0; $i <= 50; ++$i) {
            $perpage[$i] = $i;
        }
        $perpage[0] = get_string('allinone', 'quiz');
        $mform->addElement('select', 'questionsperpage', get_string('questionsperpage', 'quiz'), $perpage);
        $mform->setHelpButton('questionsperpage', array('questionsperpage', get_string('questionsperpage', 'quiz'), 'quiz'));
        $mform->setAdvanced('questionsperpage', $CFG->quiz_fix_questionsperpage);
        $mform->setDefault('questionsperpage', $CFG->quiz_questionsperpage);

        $mform->addElement('selectyesno', 'shufflequestions', get_string("shufflequestions", "quiz"));
        $mform->setHelpButton('shufflequestions', array("shufflequestions", get_string("shufflequestions","quiz"), "quiz"));
        $mform->setAdvanced('shufflequestions', $CFG->quiz_fix_shufflequestions);
        $mform->setDefault('shufflequestions', $CFG->quiz_shufflequestions);

        $mform->addElement('selectyesno', 'shuffleanswers', get_string("shufflewithin", "quiz"));
        $mform->setHelpButton('shuffleanswers', array("shufflewithin", get_string("shufflewithin","quiz"), "quiz"));
        $mform->setAdvanced('shuffleanswers', $CFG->quiz_fix_shuffleanswers);
        $mform->setDefault('shuffleanswers', $CFG->quiz_shuffleanswers);

//-------------------------------------------------------------------------------
        $mform->addElement('header', 'attemptshdr', get_string('attempts', 'quiz'));
        $attemptoptions = array('0' => get_string('unlimited'));
        for ($i = 1; $i <= 10; $i++) {
            $attemptoptions[$i] = $i;
        }
        $mform->addElement('select', 'attempts', get_string("attemptsallowed", "quiz"), $attemptoptions);
        $mform->setHelpButton('attempts', array("attempts", get_string("attemptsallowed","quiz"), "quiz"));
        $mform->setAdvanced('attempts', $CFG->quiz_fix_attempts);
        $mform->setDefault('attempts', $CFG->quiz_attempts);

        $mform->addElement('selectyesno', 'attemptonlast', get_string("eachattemptbuildsonthelast", "quiz"));
        $mform->setHelpButton('attemptonlast', array("repeatattempts", get_string("eachattemptbuildsonthelast", "quiz"), "quiz"));
        $mform->setAdvanced('attemptonlast', $CFG->quiz_fix_attemptonlast);
        $mform->setDefault('attemptonlast', $CFG->quiz_attemptonlast);

        $mform->addElement('selectyesno', 'adaptive', get_string("adaptive", "quiz"));
        $mform->setHelpButton('adaptive', array("adaptive", get_string("adaptive","quiz"), "quiz"));
        $mform->setAdvanced('adaptive', $CFG->quiz_fix_adaptive);
        $mform->setDefault('adaptive', $CFG->quiz_optionflags & QUESTION_ADAPTIVE);


//-------------------------------------------------------------------------------
        $mform->addElement('header', 'gradeshdr', get_string('grades', 'grades'));
        $mform->addElement('select', 'grademethod', get_string("grademethod", "quiz"), quiz_get_grading_options());
        $mform->setHelpButton('grademethod', array("grademethod", get_string("grademethod","quiz"), "quiz"));
        $mform->setAdvanced('grademethod', $CFG->quiz_fix_grademethod);
        $mform->setDefault('grademethod', $CFG->quiz_grademethod);

        $mform->addElement('selectyesno', 'penaltyscheme', get_string("penaltyscheme", "quiz"));
        $mform->setHelpButton('penaltyscheme', array("penaltyscheme", get_string("penaltyscheme","quiz"), "quiz"));
        $mform->setAdvanced('penaltyscheme', $CFG->quiz_fix_penaltyscheme);
        $mform->setDefault('penaltyscheme', $CFG->quiz_penaltyscheme);

        $options = array(
                    0 => '0',
                    1 => '1',
                    2 => '2',
                    3 => '3');
        $mform->addElement('select', 'decimalpoints', get_string("decimaldigits", "quiz"), $options);
        $mform->setHelpButton('decimalpoints', array("decimalpoints", get_string("decimaldigits","quiz"), "quiz"));
        $mform->setAdvanced('decimalpoints', $CFG->quiz_fix_decimalpoints);
        $mform->setDefault('decimalpoints', $CFG->quiz_decimalpoints);

//-------------------------------------------------------------------------------
        $mform->addElement('header', 'reviewoptionshdr', get_string('reviewoptionsheading', 'quiz'));
        $mform->setHelpButton('reviewoptionshdr', array('reviewoptions', get_string('reviewoptionsheading','quiz'), 'quiz'));
        $mform->setAdvanced('reviewoptionshdr', $CFG->quiz_fix_review);

        $immediatelyoptionsgrp=array();
        $immediatelyoptionsgrp[] = &$mform->createElement('checkbox', 'responsesimmediately', '', get_string('responses', 'quiz'));
        $immediatelyoptionsgrp[] = &$mform->createElement('checkbox', 'answersimmediately', '', get_string('answers', 'quiz'));
        $immediatelyoptionsgrp[] = &$mform->createElement('checkbox', 'feedbackimmediately', '', get_string('feedback', 'quiz'));
        $immediatelyoptionsgrp[] = &$mform->createElement('checkbox', 'generalfeedbackimmediately', '', get_string('generalfeedback', 'quiz'));
        $immediatelyoptionsgrp[] = &$mform->createElement('checkbox', 'scoreimmediately', '', get_string('scores', 'quiz'));
        $immediatelyoptionsgrp[] = &$mform->createElement('checkbox', 'overallfeedbackimmediately', '', get_string('overallfeedback', 'quiz'));
        $mform->addGroup($immediatelyoptionsgrp, 'immediatelyoptionsgrp', get_string("reviewimmediately", "quiz"), null, false);
        $mform->setDefault('responsesimmediately', $CFG->quiz_review & QUIZ_REVIEW_RESPONSES & QUIZ_REVIEW_IMMEDIATELY);
        $mform->setDefault('answersimmediately', $CFG->quiz_review & QUIZ_REVIEW_ANSWERS & QUIZ_REVIEW_IMMEDIATELY);
        $mform->setDefault('feedbackimmediately', $CFG->quiz_review & QUIZ_REVIEW_FEEDBACK & QUIZ_REVIEW_IMMEDIATELY);
        $mform->setDefault('generalfeedbackimmediately', $CFG->quiz_review & QUIZ_REVIEW_GENERALFEEDBACK & QUIZ_REVIEW_IMMEDIATELY);
        $mform->setDefault('scoreimmediately', $CFG->quiz_review & QUIZ_REVIEW_SCORES & QUIZ_REVIEW_IMMEDIATELY);
        $mform->setDefault('overallfeedbackimmediately', $CFG->quiz_review & QUIZ_REVIEW_OVERALLFEEDBACK & QUIZ_REVIEW_IMMEDIATELY);

        $openoptionsgrp=array();
        $openoptionsgrp[] = &$mform->createElement('checkbox', 'responsesopen', '', get_string('responses', 'quiz'));
        $openoptionsgrp[] = &$mform->createElement('checkbox', 'answersopen', '', get_string('answers', 'quiz'));
        $openoptionsgrp[] = &$mform->createElement('checkbox', 'feedbackopen', '', get_string('feedback', 'quiz'));
        $openoptionsgrp[] = &$mform->createElement('checkbox', 'generalfeedbackopen', '', get_string('generalfeedback', 'quiz'));
        $openoptionsgrp[] = &$mform->createElement('checkbox', 'scoreopen', '', get_string('scores', 'quiz'));
        $openoptionsgrp[] = &$mform->createElement('checkbox', 'overallfeedbackopen', '', get_string('overallfeedback', 'quiz'));
        $mform->addGroup($openoptionsgrp, 'openoptionsgrp', get_string("reviewopen", "quiz"), array(' '), false);
        $mform->setDefault('responsesopen', $CFG->quiz_review & QUIZ_REVIEW_RESPONSES & QUIZ_REVIEW_OPEN);
        $mform->setDefault('answersopen', $CFG->quiz_review & QUIZ_REVIEW_ANSWERS & QUIZ_REVIEW_OPEN);
        $mform->setDefault('feedbackopen', $CFG->quiz_review & QUIZ_REVIEW_FEEDBACK & QUIZ_REVIEW_OPEN);
        $mform->setDefault('generalfeedbackopen', $CFG->quiz_review & QUIZ_REVIEW_GENERALFEEDBACK & QUIZ_REVIEW_OPEN);
        $mform->setDefault('scoreopen', $CFG->quiz_review & QUIZ_REVIEW_SCORES & QUIZ_REVIEW_OPEN);
        $mform->setDefault('overallfeedbackopen', $CFG->quiz_review & QUIZ_REVIEW_OVERALLFEEDBACK & QUIZ_REVIEW_OPEN);


        $closedoptionsgrp=array();
        $closedoptionsgrp[] = &$mform->createElement('checkbox', 'responsesclosed', '', get_string('responses', 'quiz'));
        $closedoptionsgrp[] = &$mform->createElement('checkbox', 'answersclosed', '', get_string('answers', 'quiz'));
        $closedoptionsgrp[] = &$mform->createElement('checkbox', 'feedbackclosed', '', get_string('feedback', 'quiz'));
        $closedoptionsgrp[] = &$mform->createElement('checkbox', 'generalfeedbackclosed', '', get_string('generalfeedback', 'quiz'));
        $closedoptionsgrp[] = &$mform->createElement('checkbox', 'scoreclosed', '', get_string('scores', 'quiz'));
        $closedoptionsgrp[] = &$mform->createElement('checkbox', 'overallfeedbackclosed', '', get_string('overallfeedback', 'quiz'));
        $mform->addGroup($closedoptionsgrp, 'closedoptionsgrp', get_string("reviewclosed", "quiz"), array(' '), false);
        $mform->setDefault('responsesclosed', $CFG->quiz_review & QUIZ_REVIEW_RESPONSES & QUIZ_REVIEW_CLOSED);
        $mform->setDefault('answersclosed', $CFG->quiz_review & QUIZ_REVIEW_ANSWERS & QUIZ_REVIEW_CLOSED);
        $mform->setDefault('feedbackclosed', $CFG->quiz_review & QUIZ_REVIEW_FEEDBACK & QUIZ_REVIEW_CLOSED);
        $mform->setDefault('generalfeedbackclosed', $CFG->quiz_review & QUIZ_REVIEW_GENERALFEEDBACK & QUIZ_REVIEW_CLOSED);
        $mform->setDefault('scoreclosed', $CFG->quiz_review & QUIZ_REVIEW_SCORES & QUIZ_REVIEW_CLOSED);
        $mform->setDefault('overallfeedbackclosed', $CFG->quiz_review & QUIZ_REVIEW_OVERALLFEEDBACK & QUIZ_REVIEW_CLOSED);

//-------------------------------------------------------------------------------
        $mform->addElement('header', 'security', get_string('security', 'form'));

        $options = array(
                    0 => get_string('none', 'quiz'),
                    1 => get_string('popupwithjavascriptsupport', 'quiz'));
        if (!empty($CFG->enablesafebrowserintegration)) {
            $options[2] = get_string('requiresafeexambrowser', 'quiz');
        }
        $mform->addElement('select', 'popup', get_string('browsersecurity', 'quiz'), $options);
        $mform->setHelpButton('popup', array('browsersecurity', get_string('browsersecurity', 'quiz'), 'quiz'));
        $mform->setAdvanced('popup', $CFG->quiz_fix_popup);
        $mform->setDefault('popup', $CFG->quiz_popup);

        $mform->addElement('passwordunmask', 'quizpassword', get_string("requirepassword", "quiz"));
        $mform->setType('quizpassword', PARAM_TEXT);
        $mform->setHelpButton('quizpassword', array("requirepassword", get_string("requirepassword", "quiz"), "quiz"));
        $mform->setAdvanced('quizpassword', $CFG->quiz_fix_password);
        $mform->setDefault('quizpassword', $CFG->quiz_password);

        $mform->addElement('text', 'subnet', get_string("requiresubnet", "quiz"));
        $mform->setType('subnet', PARAM_TEXT);
        $mform->setHelpButton('subnet', array("requiresubnet", get_string("requiresubnet", "quiz"), "quiz"));
        $mform->setAdvanced('subnet', $CFG->quiz_fix_subnet);
        $mform->setDefault('subnet', $CFG->quiz_subnet);

//-------------------------------------------------------------------------------
        $features = new stdClass;
        $features->groups = true;
        $features->groupings = true;
        $features->groupmembersonly = true;
        $this->standard_coursemodule_elements($features);
//-------------------------------------------------------------------------------
        $mform->addElement('header', 'overallfeedbackhdr', get_string('overallfeedback', 'quiz'));
        $mform->setHelpButton('overallfeedbackhdr', array('overallfeedback', get_string('overallfeedback', 'quiz'), 'quiz'));

        $mform->addElement('hidden', 'grade', $CFG->quiz_maximumgrade);
        $mform->setType('grade', PARAM_RAW);
        if (empty($this->_cm)) {
            $needwarning = $CFG->quiz_maximumgrade == 0;
        } else {
            $quizgrade = get_field('quiz', 'grade', 'id', $this->_instance);
            $needwarning = $quizgrade == 0;
        }
        if ($needwarning) {
            $mform->addElement('static', 'nogradewarning', '', get_string('nogradewarning', 'quiz'));
        }

        $mform->addElement('static', 'gradeboundarystatic1', get_string('gradeboundary', 'quiz'), '100%');

        $repeatarray = array();
        $repeatarray[] = &MoodleQuickForm::createElement('text', 'feedbacktext', get_string('feedback', 'quiz'), array('size' => 50));
        $mform->setType('feedbacktext', PARAM_RAW);
        $repeatarray[] = &MoodleQuickForm::createElement('text', 'feedbackboundaries', get_string('gradeboundary', 'quiz'), array('size' => 10));
        $mform->setType('feedbackboundaries', PARAM_NOTAGS);

        if (!empty($this->_instance)) {
            $this->_feedbacks = get_records('quiz_feedback', 'quizid', $this->_instance, 'mingrade DESC');
        } else {
            $this->_feedbacks = array();
        }
        $numfeedbacks = max(count($this->_feedbacks) * 1.5, 5);

        $nextel = $this->repeat_elements($repeatarray, $numfeedbacks - 1,
                array(), 'boundary_repeats', 'boundary_add_fields', 3,
                get_string('addmoreoverallfeedbacks', 'quiz'), true);

        // Put some extra elements in before the button
        $insertEl = &MoodleQuickForm::createElement('text', "feedbacktext[$nextel]", get_string('feedback', 'quiz'), array('size' => 50));
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
        // buttons
        $this->add_action_buttons();
    }

    function data_preprocessing(&$default_values){
        if (count($this->_feedbacks)) {
            $key = 0;
            foreach ($this->_feedbacks as $feedback){
                $default_values['feedbacktext['.$key.']'] = $feedback->feedbacktext;
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
            if (!empty($data['feedbacktext'][$i] ) && trim($data['feedbacktext'][$i] ) != '') {
                $errors["feedbacktext[$i]"] = get_string('feedbackerrorjunkinfeedback', 'quiz', $i + 1);
            }
        }

        if (count($errors) == 0) {
            return true;
        } else {
            return $errors;
        }
    }

}
?>

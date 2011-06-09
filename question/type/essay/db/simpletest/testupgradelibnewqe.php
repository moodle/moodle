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
 * Tests of the upgrade to the new Moodle question engine for attempts at
 * essay questions.
 *
 * @package    qtype
 * @subpackage essay
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/engine/upgrade/simpletest/helper.php');


/**
 * Testing the upgrade of essay question attempts.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_essay_attempt_upgrader_test extends question_attempt_upgrader_test_base {

    public function test_essay_deferredfeedback_history98220() {
        $quiz = (object) array(
            'id' => '4140',
            'course' => '5012',
            'name' => 'M887 Online Examination: 19th April 2010, 10am - 1pm',
            'intro' => '<h2>M887 Examination Paper 19th April 2010</h2>

        <h2>Postgraduate Computing<br />Web Systems Integration<br /></h2>

        <h2>Begin by pressing Start Attempt (below)</h2>',
            'introformat' => FORMAT_HTML,
            'questiondecimalpoints' => '-1',
            'showuserpicture' => '1',
            'showblocks' => '1',
            'timeopen' => '1271665800',
            'timeclose' => '1271682000',
            'preferredbehaviour' => 'deferredfeedback',
            'attempts' => '1',
            'attemptonlast' => '0',
            'grademethod' => '1',
            'decimalpoints' => '2',
            'review' => '71727591',
            'questionsperpage' => '1',
            'shufflequestions' => '0',
            'shuffleanswers' => '1',
            'questions' => '90042,0,90043,0,90045,0,90052,0,90053,0,90054,0,90055,0,90056,0,90057,0,90058,0,90059,0,90046,0,90044,0,90047,0,90048,0,90049,0',
            'sumgrades' => '100',
            'grade' => '100',
            'timecreated' => '0',
            'timemodified' => '1272274569',
            'password' => '',
            'subnet' => '',
            'popup' => '0',
            'delay1' => '0',
            'delay2' => '0',
            'timelimit' => '0',
        );
        $attempt = (object) array(
            'id' => '388325',
            'uniqueid' => '388326',
            'quiz' => '4140',
            'userid' => '118065',
            'attempt' => '1',
            'sumgrades' => '51',
            'timestart' => '1271667586',
            'timefinish' => '1271678351',
            'timemodified' => '1273069013',
            'layout' => '90042,0,90043,0,90045,0,90052,0,90053,0,90054,0,90055,0,90056,0,90057,0,90058,0,90059,0,90046,0,90044,0,90047,0,90048,0,90049,0',
            'preview' => '0',
            'needsupgradetonewqe' => '1',
        );
        $question = (object) array(
            'id' => '90056',
            'category' => '8619',
            'parent' => '0',
            'name' => 'Question 6',
            'questiontext' => '<p>

        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"></meta><meta name="ProgId" content="Word.Document"></meta><meta name="Generator" content="Microsoft Word 11"></meta><meta name="Originator" content="Microsoft Word 11"></meta><link rel="File-List" href="file:///C:\\DOCUME~1\\pgt2\\LOCALS~1\\Temp\\msohtml1\\01\\clip_filelist.xml"></link><style></style>Give two examples of facilities within XML schemas that cannot be found in Document Type Definitions (DTDs).<br /><i>(2 marks)</i></p>',
            'questiontextformat' => '1',
            'defaultmark' => '2',
            'penalty' => '0',
            'qtype' => 'essay',
            'length' => '1',
            'stamp' => 'learn.open.ac.uk+100205101651+5eB30s',
            'version' => 'learn.open.ac.uk+100209161823+oZCX9n',
            'hidden' => '0',
            'generalfeedback' => '',
            'generalfeedbackformat' => '1',
            'timecreated' => '1265365011',
            'timemodified' => '1265732303',
            'createdby' => '219095',
            'modifiedby' => '25483',
            'unlimited' => '0',
            'maxmark' => '2',
            'options' => (object) array(
                'answers' => array(
                    303772 => (object) array(
                        'question' => '90056',
                        'answer' => '',
                        'fraction' => '0',
                        'feedback' => '',
                        'id' => 303772,
                    ),
                ),
            ),
            'hints' => false,
        );
        $qsession = (object) array(
            'id' => '3962570',
            'attemptid' => '388326',
            'questionid' => '90056',
            'newest' => '10517712',
            'newgraded' => '10517712',
            'sumpenalty' => '0',
            'manualcomment' => '',
            'manualcommentformat' => '1',
            'flagged' => '1',
        );
        $qstates = array(
            10094242 => (object) array(
                'attempt' => '388326',
                'question' => '90056',
                'originalquestion' => '0',
                'seq_number' => '0',
                'answer' => '',
                'timestamp' => '1271667586',
                'event' => '0',
                'grade' => '0',
                'raw_grade' => '0',
                'penalty' => '0',
                'id' => 10094242,
            ),
            10096161 => (object) array(
                'attempt' => '388326',
                'question' => '90056',
                'originalquestion' => '0',
                'seq_number' => '1',
                'answer' => '<p>Variable typeing</p>
        <p>Namespaces</p>',
                'timestamp' => '1271670445',
                'event' => '2',
                'grade' => '0',
                'raw_grade' => '0',
                'penalty' => '0',
                'id' => 10096161,
            ),
            10097144 => (object) array(
                'attempt' => '388326',
                'question' => '90056',
                'originalquestion' => '0',
                'seq_number' => '2',
                'answer' => '<p>Variable can be typed</p>
        <p>xml Schemas fully support Namespaces</p>',
                'timestamp' => '1271671440',
                'event' => '2',
                'grade' => '0',
                'raw_grade' => '0',
                'penalty' => '0',
                'id' => 10097144,
            ),
            10103710 => (object) array(
                'attempt' => '388326',
                'question' => '90056',
                'originalquestion' => '0',
                'seq_number' => '3',
                'answer' => '<p>Variable can be typed</p>
        <p>xml Schemas fully support Namespaces</p>',
                'timestamp' => '1271671440',
                'event' => '8',
                'grade' => '0',
                'raw_grade' => '0',
                'penalty' => '0',
                'id' => 10103710,
            ),
            10517712 => (object) array(
                'attempt' => '388326',
                'question' => '90056',
                'originalquestion' => '0',
                'seq_number' => '4',
                'answer' => '<p>Variable can be typed</p>
        <p>xml Schemas fully support Namespaces</p>',
                'timestamp' => '1273068477',
                'event' => '9',
                'grade' => '2',
                'raw_grade' => '2',
                'penalty' => '0',
                'id' => 10517712,
            ),
        );

        $qa = $this->updater->convert_question_attempt($quiz, $attempt, $question, $qsession, $qstates);

        $expectedqa = (object) array(
            'behaviour' => 'manualgraded',
            'questionid' => 90056,
            'variant' => 1,
            'maxmark' => 2,
            'minfraction' => 0,
            'flagged' => 0,
            'questionsummary' => "* Give two examples of facilities within XML schemas that cannot be found in Document Type Definitions (DTDs).\n_(2 marks)_",
            'rightanswer' => '',
            'responsesummary' => "Variable can be typed \n\nxml Schemas fully support Namespaces",
            'timemodified' => 1273068477,
            'steps' => array(
                0 => (object) array(
                    'sequencenumber' => 0,
                    'state' => 'todo',
                    'fraction' => null,
                    'timecreated' => 1271667586,
                    'userid' => 118065,
                    'data' => array(),
                ),
                1 => (object) array(
                    'sequencenumber' => 1,
                    'state' => 'complete',
                    'fraction' => null,
                    'timecreated' => 1271670445,
                    'userid' => 118065,
                    'data' => array('answer' => '<p>Variable typeing</p>
        <p>Namespaces</p>'),
                ),
                2 => (object) array(
                    'sequencenumber' => 2,
                    'state' => 'complete',
                    'fraction' => null,
                    'timecreated' => 1271671440,
                    'userid' => 118065,
                    'data' => array('answer' => '<p>Variable can be typed</p>
        <p>xml Schemas fully support Namespaces</p>'),
                ),
                3 => (object) array(
                    'sequencenumber' => 3,
                    'state' => 'needsgrading',
                    'fraction' => null,
                    'timecreated' => 1271671440,
                    'userid' => 118065,
                    'data' => array('answer' => '<p>Variable can be typed</p>
        <p>xml Schemas fully support Namespaces</p>', '-finish' => 1),
                ),
                4 => (object) array(
                    'sequencenumber' => 4,
                    'state' => 'mangrright',
                    'fraction' => 1,
                    'timecreated' => 1273068477,
                    'userid' => null,
                    'data' => array('-comment' => '', '-mark' => 2, '-maxmark' => 2),
                ),
            ),
        );

        $this->compare_qas($expectedqa, $qa);
    }

    public function test_essay_deferredfeedback_history820() {
        $quiz = (object) array(
            'id' => '142',
            'course' => '187',
            'name' => 'Questionnaire',
            'intro' => '<p>B680 is pioneering the use of the eAssessment module in the OU VLE (Virtual Learning Environment). We believe that the module is fit for purpose but we need users\' (students and ALs) experience to confirm this. Your answers to this short questionnaire therefore are of wide importance to the OU VLE Development Programme.  If you could complete this short questionnaire after attempting Practice CTMA04 <b></b>it would be greatly appreciated.</p>
        <p>The questionnaire has 15 questions and we would like you to answer as many of these as possible.  When you have completed your answers you will see a End test button, similar to the one in Practice CTMA 04, which you will need to click.  This will move you to a Summary page.  Please click the \'Submit all and finish\' button when you are happy to submit your final answers.  <strong>Please complete the questionnaire only once.</strong>  At a later stage the B680 Course Team will analyse the students\' answers to the questions. <br /></p>',
            'introformat' => FORMAT_HTML,
            'questiondecimalpoints' => '-1',
            'showuserpicture' => '1',
            'showblocks' => '1',
            'timeopen' => '0',
            'timeclose' => '0',
            'preferredbehaviour' => 'deferredfeedback',
            'attempts' => '0',
            'attemptonlast' => '0',
            'grademethod' => '1',
            'decimalpoints' => '2',
            'review' => '71727591',
            'questionsperpage' => '1',
            'shufflequestions' => '0',
            'shuffleanswers' => '0',
            'questions' => '3664,3716,0,3663,3717,0,3718,3719,0,3720,0,3733,3727,0,3728,3730,0,3731,3732,0,3726,3729,0',
            'sumgrades' => '0',
            'grade' => '0',
            'timecreated' => '0',
            'timemodified' => '1178202609',
            'password' => '',
            'subnet' => '',
            'popup' => '0',
            'delay1' => '0',
            'delay2' => '0',
            'timelimit' => '0',
        );
        $attempt = (object) array(
            'id' => '4246',
            'uniqueid' => '4246',
            'quiz' => '142',
            'userid' => '96864',
            'attempt' => '1',
            'sumgrades' => '0',
            'timestart' => '1179134211',
            'timefinish' => '1179134998',
            'timemodified' => '1179134869',
            'layout' => '3664,3716,0,3663,3717,0,3718,3719,0,3720,0,3733,3727,0,3728,3730,0,3731,3732,0,3726,3729,0',
            'preview' => '0',
            'needsupgradetonewqe' => '1',
        );
        $question = (object) array(
            'id' => '3729',
            'category' => '163',
            'parent' => '0',
            'name' => 'Question 98',
            'questiontext' => 'If you answered ‘No’ to the previous question please expand on your problem here.<br /><b></b><br />',
            'questiontextformat' => '1',
            'defaultmark' => '0',
            'penalty' => '0',
            'qtype' => 'essay',
            'length' => '1',
            'stamp' => 'learn.open.ac.uk+070312094434+k2HaUF',
            'version' => 'learn.open.ac.uk+070501173219+spx2IM',
            'hidden' => '0',
            'generalfeedback' => '',
            'generalfeedbackformat' => '1',
            'timecreated' => '0',
            'timemodified' => '0',
            'createdby' => null,
            'modifiedby' => null,
            'unlimited' => null,
            'maxmark' => '0',
            'options' => (object) array(
                'answers' => array(
                    11264 => (object) array(
                        'question' => '3729',
                        'answer' => '',
                        'fraction' => '0',
                        'feedback' => '',
                        'id' => 11264,
                    ),
                ),
            ),
            'hints' => false,
        );
        $qsession = (object) array(
            'id' => '47133',
            'attemptid' => '4246',
            'questionid' => '3729',
            'newest' => '107502',
            'newgraded' => '107407',
            'sumpenalty' => '0',
            'manualcomment' => '',
            'manualcommentformat' => '1',
            'flagged' => '1',
        );
        $qstates = array(
            107407 => (object) array(
                'attempt' => '4246',
                'question' => '3729',
                'originalquestion' => '0',
                'seq_number' => '0',
                'answer' => '',
                'timestamp' => '1179134211',
                'event' => '0',
                'grade' => '0',
                'raw_grade' => '0',
                'penalty' => '0',
                'id' => 107407,
            ),
            107484 => (object) array(
                'attempt' => '4246',
                'question' => '3729',
                'originalquestion' => '0',
                'seq_number' => '1',
                'answer' => 'it would be better to point our a \'red colour\' on the number which indicates the questions that we have done wrong. similar to previously, from question 1 to 10, green colour shows the right answer and red colour shows the wrong answer, so that we do not need to click on each answer to find out if it is right or wrong.',
                'timestamp' => '1179134869',
                'event' => '2',
                'grade' => '0',
                'raw_grade' => '0',
                'penalty' => '0',
                'id' => 107484,
            ),
            107502 => (object) array(
                'attempt' => '4246',
                'question' => '3729',
                'originalquestion' => '0',
                'seq_number' => '2',
                'answer' => 'it would be better to point our a \'red colour\' on the number which indicates the questions that we have done wrong. similar to previously, from question 1 to 10, green colour shows the right answer and red colour shows the wrong answer, so that we do not need to click on each answer to find out if it is right or wrong.',
                'timestamp' => '1179134869',
                'event' => '8',
                'grade' => '0',
                'raw_grade' => '0',
                'penalty' => '0',
                'id' => 107502,
            ),
        );

        $qa = $this->updater->convert_question_attempt($quiz, $attempt, $question, $qsession, $qstates);

        $expectedqa = (object) array(
            'behaviour' => 'manualgraded',
            'questionid' => 3729,
            'variant' => 1,
            'maxmark' => 0,
            'minfraction' => 0,
            'flagged' => 0,
            'questionsummary' => "If you answered ‘No’ to the previous question please expand on your problem here.",
            'rightanswer' => '',
            'responsesummary' => 'it would be better to point our a \'red colour\' on the number which indicates the questions that we have done wrong. similar to previously, from question 1 to 10, green colour shows the right answer and red colour shows the wrong answer, so that we do not need to click on each answer to find out if it is right or wrong.',
            'timemodified' => 1179134869,
            'steps' => array(
                0 => (object) array(
                    'sequencenumber' => 0,
                    'state' => 'todo',
                    'fraction' => null,
                    'timecreated' => 1179134211,
                    'userid' => 96864,
                    'data' => array(),
                ),
                1 => (object) array(
                    'sequencenumber' => 1,
                    'state' => 'complete',
                    'fraction' => null,
                    'timecreated' => 1179134869,
                    'userid' => 96864,
                    'data' => array('answer' => 'it would be better to point our a \'red colour\' on the number which indicates the questions that we have done wrong. similar to previously, from question 1 to 10, green colour shows the right answer and red colour shows the wrong answer, so that we do not need to click on each answer to find out if it is right or wrong.'),
                ),
                2 => (object) array(
                    'sequencenumber' => 2,
                    'state' => 'needsgrading',
                    'fraction' => null,
                    'timecreated' => 1179134869,
                    'userid' => 96864,
                    'data' => array('-finish' => 1, 'answer' => 'it would be better to point our a \'red colour\' on the number which indicates the questions that we have done wrong. similar to previously, from question 1 to 10, green colour shows the right answer and red colour shows the wrong answer, so that we do not need to click on each answer to find out if it is right or wrong.'),
                ),
            ),
        );

        $this->compare_qas($expectedqa, $qa);
    }

    public function test_essay_deferredfeedback_missing() {
        $quiz = (object) array(
            'id' => '142',
            'course' => '187',
            'name' => 'Questionnaire',
            'intro' => '<p>B680 is pioneering the use of the eAssessment module in the OU VLE (Virtual Learning Environment). We believe that the module is fit for purpose but we need users\' (students and ALs) experience to confirm this. Your answers to this short questionnaire therefore are of wide importance to the OU VLE Development Programme.  If you could complete this short questionnaire after attempting Practice CTMA04 <b></b>it would be greatly appreciated.</p>
        <p>The questionnaire has 15 questions and we would like you to answer as many of these as possible.  When you have completed your answers you will see a End test button, similar to the one in Practice CTMA 04, which you will need to click.  This will move you to a Summary page.  Please click the \'Submit all and finish\' button when you are happy to submit your final answers.  <strong>Please complete the questionnaire only once.</strong>  At a later stage the B680 Course Team will analyse the students\' answers to the questions. <br /></p>',
            'introformat' => FORMAT_HTML,
            'questiondecimalpoints' => '-1',
            'showuserpicture' => '1',
            'showblocks' => '1',
            'timeopen' => '0',
            'timeclose' => '0',
            'preferredbehaviour' => 'deferredfeedback',
            'attempts' => '0',
            'attemptonlast' => '0',
            'grademethod' => '1',
            'decimalpoints' => '2',
            'review' => '71727591',
            'questionsperpage' => '1',
            'shufflequestions' => '0',
            'shuffleanswers' => '0',
            'questions' => '3664,3716,0,3663,3717,0,3718,3719,0,3720,0,3733,3727,0,3728,3730,0,3731,3732,0,3726,3729,0',
            'sumgrades' => '0',
            'grade' => '0',
            'timecreated' => '0',
            'timemodified' => '1178202609',
            'password' => '',
            'subnet' => '',
            'popup' => '0',
            'delay1' => '0',
            'delay2' => '0',
            'timelimit' => '0',
        );
        $attempt = (object) array(
            'id' => '4246',
            'uniqueid' => '4246',
            'quiz' => '142',
            'userid' => '96864',
            'attempt' => '1',
            'sumgrades' => '0',
            'timestart' => '1179134211',
            'timefinish' => '1179134998',
            'timemodified' => '1179134869',
            'layout' => '3664,3716,0,3663,3717,0,3718,3719,0,3720,0,3733,3727,0,3728,3730,0,3731,3732,0,3726,3729,0',
            'preview' => '0',
            'needsupgradetonewqe' => '1',
        );
        $question = (object) array(
            'id' => '3729',
            'category' => '163',
            'parent' => '0',
            'name' => 'Question 98',
            'questiontext' => 'If you answered ‘No’ to the previous question please expand on your problem here.<br /><b></b><br />',
            'questiontextformat' => '1',
            'defaultmark' => '0',
            'penalty' => '0',
            'qtype' => 'essay',
            'length' => '1',
            'stamp' => 'learn.open.ac.uk+070312094434+k2HaUF',
            'version' => 'learn.open.ac.uk+070501173219+spx2IM',
            'hidden' => '0',
            'generalfeedback' => '',
            'generalfeedbackformat' => '1',
            'timecreated' => '0',
            'timemodified' => '0',
            'createdby' => null,
            'modifiedby' => null,
            'unlimited' => null,
            'maxmark' => '0',
            'options' => (object) array(
                'answers' => array(
                    11264 => (object) array(
                        'question' => '3729',
                        'answer' => '',
                        'fraction' => '0',
                        'feedback' => '',
                        'id' => 11264,
                    ),
                ),
            ),
            'hints' => false,
        );

        $qa = $this->updater->supply_missing_question_attempt( $quiz, $attempt, $question);

        $expectedqa = (object) array(
            'behaviour' => 'manualgraded',
            'questionid' => 3729,
            'variant' => 1,
            'maxmark' => 0,
            'minfraction' => 0,
            'flagged' => 0,
            'questionsummary' => "If you answered ‘No’ to the previous question please expand on your problem here.",
            'rightanswer' => '',
            'responsesummary' => '',
            'timemodified' => 1179134211,
            'steps' => array(
                0 => (object) array(
                    'sequencenumber' => 0,
                    'state' => 'todo',
                    'fraction' => null,
                    'timecreated' => 1179134211,
                    'userid' => 96864,
                    'data' => array(),
                ),
            ),
        );

        $this->compare_qas($expectedqa, $qa);
    }
}

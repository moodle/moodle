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
 * description questions.
 *
 * @package    qtype
 * @subpackage description
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/upgrade/tests/helper.php');


/**
 * Testing the upgrade of description question attempts.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_description_attempt_upgrader_test extends question_attempt_upgrader_test_base {

    public function test_description_deferredfeedback_history80() {
        $quiz = (object) array(
            'id' => '278',
            'course' => '2950',
            'name' => 'test quiz 1',
            'intro' => 'my demonstration quiz ',
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
            'shuffleanswers' => '1',
            'sumgrades' => '5',
            'grade' => '10',
            'timecreated' => '0',
            'timemodified' => '1178101987',
            'password' => '',
            'subnet' => '',
            'popup' => '0',
            'delay1' => '0',
            'delay2' => '0',
            'timelimit' => '0',
        );
        $attempt = (object) array(
            'id' => '6802',
            'uniqueid' => '6802',
            'quiz' => '278',
            'userid' => '13',
            'attempt' => '1',
            'sumgrades' => '2.33333',
            'timestart' => '1185289572',
            'timefinish' => '1185289637',
            'timemodified' => '1185289590',
            'layout' => '4940,0,5043,0,4945,0,4942,0,5566,0',
            'preview' => '1',
        );
        $question = (object) array(
            'id' => '4940',
            'category' => '247',
            'parent' => '0',
            'name' => 'Northampton Gallery Case Study',
            'questiontext' => 'The following questions are based on the Northampton Art Gallery case study and associated web links.  The questions cover artists and works that were available during the study weeks for Block 3.  Some items may no longer be available on websites but will be available in the readings for Block 3.',
            'questiontextformat' => '1',
            'defaultmark' => '0',
            'penalty' => '0',
            'qtype' => 'description',
            'length' => '1',
            'stamp' => 'learn.open.ac.uk+070501114616+rtsfKk',
            'version' => 'learn.open.ac.uk+070501114616+ZY94d5',
            'hidden' => '0',
            'generalfeedback' => '',
            'generalfeedbackformat' => '1',
            'timecreated' => '0',
            'timemodified' => '0',
            'createdby' => null,
            'modifiedby' => null,
            'unlimited' => null,
            'maxmark' => '1',
        );
        $qsession = (object) array(
            'id' => '130459',
            'attemptid' => '6802',
            'questionid' => '4940',
            'newest' => '297740',
            'newgraded' => '297730',
            'sumpenalty' => '0',
            'manualcomment' => '',
            'manualcommentformat' => '1',
            'flagged' => '1',
        );
        $qstates = array(
            297730 => (object) array(
                'attempt' => '6802',
                'question' => '4940',
                'originalquestion' => '0',
                'seq_number' => '0',
                'answer' => '',
                'timestamp' => '1185289572',
                'event' => '0',
                'grade' => '0',
                'raw_grade' => '0',
                'penalty' => '0',
                'id' => 297730,
            ),
            297740 => (object) array(
                'attempt' => '6802',
                'question' => '4940',
                'originalquestion' => '0',
                'seq_number' => '1',
                'answer' => '',
                'timestamp' => '1185289572',
                'event' => '8',
                'grade' => '0',
                'raw_grade' => '0',
                'penalty' => '0',
                'id' => 297740,
            ),
        );

        $qa = $this->updater->convert_question_attempt($quiz, $attempt, $question, $qsession, $qstates);

        $expectedqa = (object) array(
            'behaviour' => 'informationitem',
            'questionid' => 4940,
            'variant' => 1,
            'maxmark' => 1,
            'minfraction' => 0,
            'maxfraction' => 1,
            'flagged' => 0,
            'questionsummary' => 'The following questions are based on the Northampton Art Gallery case study and associated web links. The questions cover artists and works that were available during the study weeks for Block 3. Some items may no longer be available on websites but will be available in the readings for Block 3.',
            'rightanswer' => '',
            'responsesummary' => '',
            'timemodified' => 1185289572,
            'steps' => array(
                0 => (object) array(
                    'sequencenumber' => 0,
                    'state' => 'todo',
                    'fraction' => null,
                    'timecreated' => 1185289572,
                    'userid' => 13,
                    'data' => array(),
                ),
                1 => (object) array(
                    'sequencenumber' => 1,
                    'state' => 'finished',
                    'fraction' => null,
                    'timecreated' => 1185289572,
                    'userid' => 13,
                    'data' => array('-finish' => 1),
                ),
            ),
        );

        $this->compare_qas($expectedqa, $qa);
    }

    public function test_description_deferredfeedback_history70() {
        $quiz = (object) array(
            'id' => '442',
            'course' => '2591',
            'name' => 'Needs Analysis (online version)',
            'intro' => '<h3>Information </h3>The purpose of the Needs Analysis is to help you identify your professional development needs and those programme modules which will best address them. <br /><br />This Needs Analysis will form the basis of a discussion with your mentor from which will emerge your Professional Development Plan (PDP). Identifying professional development needs is an integral part of the process of professional development. The PDP and the subsequent professional development opportunities it identifies will be effective only if the Needs Analysis is completed fully and accurately. You will get much more out of the programme if you give plenty of time at this early stage to the Needs Analysis. <br /><br /><h3>1. Personal and Professional Information</h3>In addition to personal information you will be asked to outline any professional qualifications and experience to date. <br /><br /><h3>2. Professional and Musical Skills</h3>In this section you will be asked to record your skills, knowledge and experience against a range of professional activities which closely relate to the focus of the music CPD programme. You will be asked to provide brief examples of evidence of those areas in which you have significant experience. <br /><br /><h3>3. Issues in Music Teaching and Learning</h3>This section is organised under three areas: Learning Musically, Teaching Musically and Making Music. <br /><br />In this section we ask you to audit your knowledge, skills and understanding against the key issues covered by the online and face-to-face module units. You will be invited to identify your strengths and provide evidence of your experience and understanding. <br /><br />',
            'introformat' => FORMAT_HTML,
            'questiondecimalpoints' => '-1',
            'showuserpicture' => '1',
            'showblocks' => '1',
            'timeopen' => '0',
            'timeclose' => '0',
            'preferredbehaviour' => 'deferredfeedback',
            'attempts' => '1',
            'attemptonlast' => '0',
            'grademethod' => '1',
            'decimalpoints' => '2',
            'review' => '71719269',
            'questionsperpage' => '1',
            'shufflequestions' => '0',
            'shuffleanswers' => '0',
            'sumgrades' => '0',
            'grade' => '10',
            'timecreated' => '0',
            'timemodified' => '1202212400',
            'password' => '',
            'subnet' => '',
            'popup' => '0',
            'delay1' => '0',
            'delay2' => '0',
            'timelimit' => '0',
        );
        $attempt = (object) array(
            'id' => '11230',
            'uniqueid' => '11230',
            'quiz' => '442',
            'userid' => '193184',
            'attempt' => '1',
            'sumgrades' => '0',
            'timestart' => '1192648793',
            'timefinish' => '1192742609',
            'timemodified' => '1192742300',
            'layout' => '8492,0,8487,8488,8489,8490,0,8441,0,8443,0,8486,0,8444,0,8445,0,8494,0,8446,8429,0,8447,8430,0,8448,8431,0,8449,8432,0,8450,8433,0,8451,8434,0,8452,8435,0,8453,8436,0,8454,8437,0,8493,0,8455,8456,8457,0,8458,8459,8460,0,8461,8462,8463,8438,0,8464,8465,8466,0,8467,8468,8469,0,8470,8471,8472,8439,0,8473,8440,0,8474,8475,8476,0,8477,8478,8479,0,8480,8481,8482,0,8483,8484,8485,8442,0',
            'preview' => '0',
        );
        $question = (object) array(
            'id' => '8492',
            'category' => '131',
            'parent' => '0',
            'name' => 'Personal and Professional Information',
            'questiontext' => '<h3>Personal and Professional Information</h3><br />Here we want you to enter personal information and outline your professional qualifications and experience to date.',
            'questiontextformat' => '1',
            'defaultmark' => '0',
            'penalty' => '0',
            'qtype' => 'description',
            'length' => '0',
            'stamp' => 'learn.open.ac.uk+070808083925+qTmPpB',
            'version' => 'learn.open.ac.uk+070907143809+3ltY7I',
            'hidden' => '0',
            'generalfeedback' => '',
            'generalfeedbackformat' => '1',
            'timecreated' => '0',
            'timemodified' => '0',
            'createdby' => null,
            'modifiedby' => null,
            'unlimited' => null,
            'maxmark' => '0',
        );
        $qsession = (object) array(
            'id' => '206424',
            'attemptid' => '11230',
            'questionid' => '8492',
            'newest' => '480877',
            'newgraded' => '476039',
            'sumpenalty' => '0',
            'manualcomment' => '',
            'manualcommentformat' => '1',
            'flagged' => '1',
        );
        $qstates = array(
            476039 => (object) array(
                'attempt' => '11230',
                'question' => '8492',
                'originalquestion' => '0',
                'seq_number' => '0',
                'answer' => '',
                'timestamp' => '1192648793',
                'event' => '0',
                'grade' => '0',
                'raw_grade' => '0',
                'penalty' => '0',
                'id' => 476039,
            ),
            480877 => (object) array(
                'attempt' => '11230',
                'question' => '8492',
                'originalquestion' => '0',
                'seq_number' => '1',
                'answer' => '',
                'timestamp' => '1192648793',
                'event' => '7',
                'grade' => '0',
                'raw_grade' => '0',
                'penalty' => '0',
                'id' => 480877,
            ),
        );

        $qa = $this->updater->convert_question_attempt($quiz, $attempt, $question, $qsession, $qstates);

        $expectedqa = (object) array(
            'behaviour' => 'informationitem',
            'questionid' => 8492,
            'variant' => 1,
            'maxmark' => 0,
            'minfraction' => 0,
            'maxfraction' => 1,
            'flagged' => 0,
            'questionsummary' => "PERSONAL AND PROFESSIONAL INFORMATION\n\nHere we want you to enter personal information and outline your professional qualifications and experience to date.",
            'rightanswer' => '',
            'responsesummary' => '',
            'timemodified' => 1192648793,
            'steps' => array(
                0 => (object) array(
                    'sequencenumber' => 0,
                    'state' => 'todo',
                    'fraction' => null,
                    'timecreated' => 1192648793,
                    'userid' => 193184,
                    'data' => array(),
                ),
                1 => (object) array(
                    'sequencenumber' => 1,
                    'state' => 'finished',
                    'fraction' => null,
                    'timecreated' => 1192648793,
                    'userid' => 193184,
                    'data' => array('-finish' => 1),
                ),
            ),
        );

        $this->compare_qas($expectedqa, $qa);
    }

    public function test_description_deferredfeedback_history0() {
        $quiz = (object) array(
            'id' => '466',
            'course' => '3464',
            'name' => 'Music CPD Needs Analysis',
            'intro' => '<h3>Information </h3>The purpose of the Needs Analysis is to help you identify your professional development needs and those programme modules which will best address them. <br /><br />This Needs Analysis will form the basis of a discussion with your mentor from which will emerge your Professional Development Plan (PDP). Identifying professional development needs is an integral part of the process of professional development. The PDP and the subsequent professional development opportunities it identifies will be effective only if the Needs Analysis is completed fully and accurately. You will get much more out of the programme if you give plenty of time at this early stage to the Needs Analysis. <br /><br />
        <h3>1. Personal and Professional Information</h3>In addition to personal information you will be asked to outline any professional qualifications and experience to date. <br /><br />
        <h3>2. Professional and Musical Skills</h3>In this section you will be asked to record your skills, knowledge and experience against a range of professional activities which closely relate to the focus of the music CPD programme. You will be asked to provide brief examples of evidence of those areas in which you have significant experience. <br /><br />
        <h3>3. Issues in Music Teaching and Learning</h3>This section is organised under three areas: Learning Musically, Teaching Musically and Making Music. <br /><br />In this section we ask you to audit your knowledge, skills and understanding against the key issues covered by the online and face-to-face module units. You will be invited to identify your strengths and provide evidence of your experience and understanding. <br /> <br />',
            'introformat' => FORMAT_HTML,
            'questiondecimalpoints' => '-1',
            'showuserpicture' => '1',
            'showblocks' => '1',
            'timeopen' => '0',
            'timeclose' => '0',
            'preferredbehaviour' => 'deferredfeedback',
            'attempts' => '1',
            'attemptonlast' => '0',
            'grademethod' => '1',
            'decimalpoints' => '2',
            'review' => '71719269',
            'questionsperpage' => '1',
            'shufflequestions' => '0',
            'shuffleanswers' => '0',
            'sumgrades' => '0',
            'grade' => '10',
            'timecreated' => '0',
            'timemodified' => '1184685800',
            'password' => '',
            'subnet' => '',
            'popup' => '0',
            'delay1' => '0',
            'delay2' => '0',
            'timelimit' => '0',
        );
        $attempt = (object) array(
            'id' => '7401',
            'uniqueid' => '7401',
            'quiz' => '466',
            'userid' => '24474',
            'attempt' => '1',
            'sumgrades' => '0',
            'timestart' => '1187168654',
            'timefinish' => '0',
            'timemodified' => '1187168769',
            'layout' => '8691,0,8690,8692,8693,8694,0,8695,0,8696,0,8697,0,8698,0,8699,0,8700,0,8701,8702,0,8703,8704,0,8705,8706,0,8707,8708,0,8709,8710,0,8711,8712,0,8713,8714,0,8715,8716,0,8717,8718,0,8719,0,8720,8721,8722,0,8723,8724,8725,0,8726,8727,8728,8729,0,8730,8731,8732,0,8733,8734,8735,0,8736,8737,8738,8739,0,8740,8741,0,8742,8743,8744,0,8745,8746,8747,0,8748,8749,8750,0,8752,8751,8753,8754,0',
            'preview' => '1',
        );
        $question = (object) array(
            'id' => '8719',
            'category' => '427',
            'parent' => '0',
            'name' => 'Music Teaching and Learning',
            'questiontext' => '<h3>Music Teaching and Learning </h3><br />In this section we ask you to audit your knowledge, skills and understanding against the key issues covered by the non-core online and face-to-face module units. You will be invited to identify your strengths and provide evidence of your experience and understanding.<br /><br />In this set of questions, you should select from the drop-down menu ‘None’, ‘Some’, ‘Good’ or ‘Strong’.<br /><br />',
            'questiontextformat' => '1',
            'defaultmark' => '0',
            'penalty' => '0',
            'qtype' => 'description',
            'length' => '0',
            'stamp' => 'learn.open.ac.uk+070430145701+r8LVld',
            'version' => 'learn.open.ac.uk+070430145834+FxIAjw',
            'hidden' => '0',
            'generalfeedback' => '',
            'generalfeedbackformat' => '1',
            'timecreated' => '0',
            'timemodified' => '0',
            'createdby' => null,
            'modifiedby' => null,
            'unlimited' => null,
            'maxmark' => '0',
        );
        $qsession = (object) array(
            'id' => '157658',
            'attemptid' => '7401',
            'questionid' => '8719',
            'newest' => '361166',
            'newgraded' => '361166',
            'sumpenalty' => '0',
            'manualcomment' => '',
            'manualcommentformat' => '1',
            'flagged' => '1',
        );
        $qstates = array(
            361166 => (object) array(
                'attempt' => '7401',
                'question' => '8719',
                'originalquestion' => '0',
                'seq_number' => '0',
                'answer' => '',
                'timestamp' => '1187168654',
                'event' => '0',
                'grade' => '0',
                'raw_grade' => '0',
                'penalty' => '0',
                'id' => 361166,
            ),
        );

        $qa = $this->updater->convert_question_attempt($quiz, $attempt, $question, $qsession, $qstates);

        $expectedqa = (object) array(
            'behaviour' => 'informationitem',
            'questionid' => 8719,
            'variant' => 1,
            'maxmark' => 0,
            'minfraction' => 0,
            'maxfraction' => 1,
            'flagged' => 0,
            'questionsummary' => "MUSIC TEACHING AND LEARNING \n\nIn this section we ask you to audit your knowledge, skills and understanding against the key issues covered by the non-core online and face-to-face module units. You will be invited to identify your strengths and provide evidence of your experience and understanding.\n\nIn this set of questions, you should select from the drop-down menu ‘None’, ‘Some’, ‘Good’ or ‘Strong’.",
            'rightanswer' => '',
            'responsesummary' => '',
            'timemodified' => 1187168654,
            'steps' => array(
                0 => (object) array(
                    'sequencenumber' => 0,
                    'state' => 'todo',
                    'fraction' => null,
                    'timecreated' => 1187168654,
                    'userid' => 24474,
                    'data' => array(),
                ),
            ),
        );

        $this->compare_qas($expectedqa, $qa);
    }
}

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

namespace qtype_numerical;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/upgrade/tests/helper.php');


/**
 * Testing the upgrade of numerical question attempts.
 *
 * @package    qtype_numerical
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class upgrade_old_attempt_data_test extends \question_attempt_upgrader_test_base {

    public function test_numerical_deferredfeedback_history620(): void {
        $quiz = (object) array(
            'id' => '221',
            'course' => '187',
            'name' => 'Practice CTMA04',
            'intro' => 'This is the Practice CTMA04. Your mark for this CTMA <span style="font-style: italic">does not </span>contribute to your continuous assessment mark for B680.<br /><br />This CTMA covers material primarily in Books 10 and 11, however some of the questions may return to material you covered in Books 0 to 9. There are 100 questions in total.<br /><br />It is a function of this testing software that it cannot provide for every possible spelling error that you make. The onus is therefore on you, the student, to ensure that your spelling is correct. ',
            'introformat' => FORMAT_HTML,
            'questiondecimalpoints' => '-1',
            'showuserpicture' => '1',
            'showblocks' => '1',
            'timeopen' => '1178492400',
            'timeclose' => '1193875200',
            'preferredbehaviour' => 'deferredfeedback',
            'attempts' => '0',
            'attemptonlast' => '0',
            'grademethod' => '1',
            'decimalpoints' => '2',
            'review' => '71727591',
            'questionsperpage' => '1',
            'shufflequestions' => '0',
            'shuffleanswers' => '0',
            'sumgrades' => '100',
            'grade' => '100',
            'timecreated' => '0',
            'timemodified' => '1195232889',
            'password' => '',
            'subnet' => '',
            'popup' => '0',
            'delay1' => '0',
            'delay2' => '0',
            'timelimit' => '0',
        );
        $attempt = (object) array(
            'id' => '4034',
            'uniqueid' => '4034',
            'quiz' => '221',
            'userid' => '405',
            'attempt' => '1',
            'sumgrades' => '59',
            'timestart' => '1178564323',
            'timefinish' => '1178575685',
            'timemodified' => '1178571037',
            'layout' => '4184,0,4185,0,4154,0,4186,0,4187,0,4188,0,4189,0,4190,0,4162,0,4191,0,4192,0,4193,0,4254,0,4195,0,4196,0,4163,0,4197,0,4198,0,4199,0,4164,0,4200,0,4165,0,4201,0,4202,0,4166,0,4203,0,4204,0,4205,0,4167,0,4155,0,4168,0,4206,0,4207,0,4208,0,4209,0,4210,0,4211,0,4212,0,4213,0,4214,0,4156,0,4215,0,4216,0,4217,0,4169,0,4170,0,4157,0,4218,0,4219,0,4220,0,4171,0,4221,0,4172,0,4222,0,4223,0,4224,0,4225,0,4226,0,4227,0,4228,0,4173,0,4229,0,4230,0,4231,0,4232,0,4174,0,4233,0,4234,0,4235,0,4236,0,4237,0,4238,0,4239,0,4240,0,4241,0,4242,0,4158,0,4243,0,4244,0,4245,0,4246,0,4159,0,4175,0,4247,0,4176,0,4248,0,4177,0,4160,0,4249,0,4178,0,4250,0,4161,0,4251,0,4179,0,4252,0,4180,0,4181,0,4182,0,4253,0,4183,0',
            'preview' => '0',
        );
        $question = (object) array(
            'id' => '4165',
            'category' => '204',
            'parent' => '0',
            'name' => '4ccP22 Book 11 Electronic Activity 6.3 Screen F2 NPV calcn(7)',
            'questiontext' => 'Calculate your answer to the nearest WHOLE £ and enter it in the blank space provided.
        <p>[Note: Please <b><i>DO NOT</i></b> enter commas, spaces or £ signs within the number you enter.] </p>
        <p>The cash flows of a project are set out below: </p>
        <p>
            <table width="500" border="1"><tbody>
                <tr>
                    <th valign="top" width="50%" halign="CENTER">year
                    </th>
                    <th valign="top" width="50%">£
                    </th>
                </tr>
                <tr>
                    <td>Year 0
                    </td>
                    <td>(28,000)
                    </td>
                </tr>
                <tr>
                    <td>Year 1
                    </td>
                    <td>18,000
                    </td>
                </tr>
                <tr>
                    <td>Year 2
                    </td>
                    <td>16,000
                    </td>
                </tr></tbody>
            </table></p>
        <p>Using the Table in Book 11, what is the project\'s net present value using a discount rate of 10%? </p>
        <p>If it is negative, put a minus sign before the number you enter. </p>
        <p>£ </p>',
            'questiontextformat' => '1',
            'defaultmark' => '1',
            'penalty' => '1',
            'qtype' => 'numerical',
            'length' => '1',
            'stamp' => 'learn.open.ac.uk+070417143727+5hOqJ8',
            'version' => 'learn.open.ac.uk+070417154843+o0iHPi',
            'hidden' => '0',
            'generalfeedback' => '',
            'generalfeedbackformat' => '1',
            'timecreated' => '0',
            'timemodified' => '0',
            'createdby' => null,
            'modifiedby' => null,
            'unlimited' => null,
            'maxmark' => '1',
            'options' => (object) array(
                'answers' => array(
                    12729 => (object) array(
                        'question' => '4165',
                        'answer' => '1586',
                        'fraction' => '1',
                        'feedback' => 'Yes, the correct answer is £1,586. Well done!
        <p>Note to the answer:<br />£(18,000 x 0.9091) + (£16,000 x 0.8264) - £28,000 = £1,586.2 (the factors are found in the Table 1 of the Appendix to Book 11) </p>
        <p>Book 11 Electronic Activity 6.3 Screen F2 </p>',
                        'tolerance' => '0',
                        'id' => 12729,
                    ),
                    12730 => (object) array(
                        'question' => '4165',
                        'answer' => '*',
                        'fraction' => '0',
                        'feedback' => 'The correct answer is £1,586.
        <p>Note to the answer:<br />£(18,000 x 0.9091) + (£16,000 x 0.8264) - £28,000 = £1,586.2 (the factors are found in the Table 1 of the Appendix to Book 11) </p>
        <p>Book 11 Electronic Activity 6.3 Screen F2 </p>',
                        'tolerance' => '0',
                        'id' => 12730,
                    ),
                ),
                'units' => array(
                ),
            ),
            'hints' => false,
        );
        $qsession = (object) array(
            'id' => '39882',
            'attemptid' => '4034',
            'questionid' => '4165',
            'newest' => '92971',
            'newgraded' => '92971',
            'sumpenalty' => '1',
            'manualcomment' => '',
            'manualcommentformat' => '1',
            'flagged' => '1',
        );
        $qstates = array(
            92694 => (object) array(
                'attempt' => '4034',
                'question' => '4165',
                'originalquestion' => '0',
                'seq_number' => '0',
                'answer' => '',
                'timestamp' => '1178564323',
                'event' => '0',
                'grade' => '0',
                'raw_grade' => '0',
                'penalty' => '0',
                'id' => 92694,
            ),
            92764 => (object) array(
                'attempt' => '4034',
                'question' => '4165',
                'originalquestion' => '0',
                'seq_number' => '1',
                'answer' => '17520',
                'timestamp' => '1178567143',
                'event' => '2',
                'grade' => '0',
                'raw_grade' => '0',
                'penalty' => '1',
                'id' => 92764,
            ),
            92971 => (object) array(
                'attempt' => '4034',
                'question' => '4165',
                'originalquestion' => '0',
                'seq_number' => '2',
                'answer' => '17520',
                'timestamp' => '1178567143',
                'event' => '6',
                'grade' => '0',
                'raw_grade' => '0',
                'penalty' => '1',
                'id' => 92971,
            ),
        );

        $qa = $this->updater->convert_question_attempt($quiz, $attempt, $question, $qsession, $qstates);

        $expectedqa = (object) array(
            'behaviour' => 'deferredfeedback',
            'questionid' => 4165,
            'variant' => 1,
            'maxmark' => 1,
            'minfraction' => 0,
            'maxfraction' => 1,
            'flagged' => 0,
            'questionsummary' => "Calculate your answer to the nearest WHOLE £ and enter it in the blank space provided. \n\n[Note: Please _DO NOT_ enter commas, spaces or £ signs within the number you enter.]  \n\nThe cash flows of a project are set out below: \n\n \t\tYEAR \n \t\t£ \n\n \t\tYear 0 \n \t\t(28,000) \n\n \t\tYear 1 \n \t\t18,000 \n\n \t\tYear 2 \n \t\t16,000 \n\nUsing the Table in Book 11, what is the project's net present value using a discount rate of 10%?  \n\nIf it is negative, put a minus sign before the number you enter.  \n\n£",
            'rightanswer' => '1586',
            'responsesummary' => '17520',
            'timemodified' => 1178567143,
            'steps' => array(
                0 => (object) array(
                    'sequencenumber' => 0,
                    'state' => 'todo',
                    'fraction' => null,
                    'timecreated' => 1178564323,
                    'userid' => 405,
                    'data' => array('_separators' => '.$,'),
                ),
                1 => (object) array(
                    'sequencenumber' => 1,
                    'state' => 'complete',
                    'fraction' => null,
                    'timecreated' => 1178567143,
                    'userid' => 405,
                    'data' => array('answer' => 17520),
                ),
                2 => (object) array(
                    'sequencenumber' => 2,
                    'state' => 'gradedwrong',
                    'fraction' => 0,
                    'timecreated' => 1178567143,
                    'userid' => 405,
                    'data' => array('answer' => 17520, '-finish' => 1),
                ),
            ),
        );

        $this->compare_qas($expectedqa, $qa);
    }

    public function test_numerical_deferredfeedback_required_units(): void {
        $quiz = (object) array(
            'id' => '2',
            'course' => '2',
            'name' => 'Numerical quiz',
            'intro' => '',
            'introformat' => '1',
            'timeopen' => '0',
            'timeclose' => '0',
            'attempts' => '0',
            'attemptonlast' => '0',
            'grademethod' => '1',
            'decimalpoints' => '2',
            'questiondecimalpoints' => '-1',
            'review' => '4459503',
            'questionsperpage' => '1',
            'shufflequestions' => '0',
            'shuffleanswers' => '1',
            'sumgrades' => '5.00000',
            'grade' => '10.00000',
            'timecreated' => '0',
            'timemodified' => '1305273177',
            'timelimit' => '0',
            'password' => '',
            'subnet' => '',
            'popup' => '0',
            'delay1' => '0',
            'delay2' => '0',
            'showuserpicture' => '0',
            'showblocks' => '0',
            'preferredbehaviour' => 'deferredfeedback',
        );
        $attempt = (object) array(
            'id' => '6',
            'uniqueid' => '6',
            'quiz' => '2',
            'userid' => '2',
            'attempt' => '1',
            'sumgrades' => '4.00000',
            'timestart' => '1305273566',
            'timefinish' => '1305273600',
            'timemodified' => '1305273600',
            'layout' => '6,12,13,15,14,0',
            'preview' => '1',
        );
        $question = (object) array(
            'id' => '15',
            'category' => '2',
            'parent' => '0',
            'name' => 'Required units',
            'questiontext' => '<p>What is twice 1.5 m?</p>',
            'questiontextformat' => '1',
            'generalfeedback' => '',
            'generalfeedbackformat' => '1',
            'penalty' => '0.1000000',
            'qtype' => 'numerical',
            'length' => '1',
            'stamp' => 'tjh238.vledev2.open.ac.uk+110513075857+rrvakr',
            'version' => 'tjh238.vledev2.open.ac.uk+110513075857+PH0loi',
            'hidden' => '0',
            'timecreated' => '1305273537',
            'timemodified' => '1305273537',
            'createdby' => '2',
            'modifiedby' => '2',
            'maxmark' => '1.0000000',
            'options' => (object) array(
                'answers' => array(
                    22 => (object) array(
                        'id' => '22',
                        'question' => '15',
                        'answer' => '3',
                        'answerformat' => '0',
                        'fraction' => '1.0000000',
                        'feedback' => '',
                        'feedbackformat' => '1',
                        'tolerance' => '',
                    ),
                ),
                'units' => array(
                    0 => (object) array(
                        'id' => '7',
                        'question' => '15',
                        'multiplier' => 1,
                        'unit' => 'm',
                    ),
                    1 => (object) array(
                        'id' => '8',
                        'question' => '15',
                        'multiplier' => 100,
                        'unit' => 'cm',
                    ),
                ),
                'unitgradingtype' => '2',
                'unitpenalty' => '0.5000000',
                'showunits' => '0',
                'unitsleft' => '0',
                'instructions' => '<p>Write an answer like 3 m.</p>',
                'instructionsformat' => '1',
            ),
            'defaultmark' => '1.0000000',
        );
        $qsession = (object) array(
            'id' => '49',
            'attemptid' => '6',
            'questionid' => '15',
            'newest' => '139',
            'newgraded' => '139',
            'sumpenalty' => '0.1000000',
            'manualcomment' => '',
            'manualcommentformat' => '1',
            'flagged' => '0',
        );
        $qstates = array(
            129 => (object) array(
                'id' => '129',
                'attempt' => '6',
                'question' => '15',
                'seq_number' => '0',
                'answer' => '|||||',
                'timestamp' => '1305273566',
                'event' => '0',
                'grade' => '0.0000000',
                'raw_grade' => '0.0000000',
                'penalty' => '0.0000000',
            ),
            134 => (object) array(
                'id' => '134',
                'attempt' => '6',
                'question' => '15',
                'seq_number' => '1',
                'answer' => '3|||||m',
                'timestamp' => '1305273595',
                'event' => '2',
                'grade' => '0.0000000',
                'raw_grade' => '1.0000000',
                'penalty' => '0.1000000',
            ),
            139 => (object) array(
                'id' => '139',
                'attempt' => '6',
                'question' => '15',
                'seq_number' => '2',
                'answer' => '3|||||m',
                'timestamp' => '1305273595',
                'event' => '6',
                'grade' => '1.0000000',
                'raw_grade' => '1.0000000',
                'penalty' => '0.1000000',
            ),
        );

        $qa = $this->updater->convert_question_attempt($quiz, $attempt, $question, $qsession, $qstates);

        $expectedqa = (object) array(
            'behaviour' => 'deferredfeedback',
            'questionid' => 15,
            'variant' => 1,
            'maxmark' => 1.0000000,
            'minfraction' => 0,
            'maxfraction' => 1,
            'flagged' => 0,
            'questionsummary' => 'What is twice 1.5 m?',
            'rightanswer' => '3 m',
            'responsesummary' => '3 m',
            'timemodified' => 1305273595,
            'steps' => array(
                0 => (object) array(
                    'sequencenumber' => 0,
                    'state' => 'todo',
                    'fraction' => null,
                    'timecreated' => 1305273566,
                    'userid' => 2,
                    'data' => array('_separators' => '.$,'),
                ),
                1 => (object) array(
                    'sequencenumber' => 1,
                    'state' => 'complete',
                    'fraction' => null,
                    'timecreated' => 1305273595,
                    'userid' => 2,
                    'data' => array('answer' => '3 m'),
                ),
                2 => (object) array(
                    'sequencenumber' => 2,
                    'state' => 'gradedright',
                    'fraction' => 1,
                    'timecreated' => 1305273595,
                    'userid' => 2,
                    'data' => array('answer' => '3 m', '-finish' => '1'),
                ),
            ),
        );

        $this->compare_qas($expectedqa, $qa);
    }
}

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
 * truefalse questions.
 *
 * @package    qtype
 * @subpackage shortanswer
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/engine/upgrade/simpletest/helper.php');


/**
 * Testing the upgrade of shortanswer question attempts.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_shortanswer_attempt_upgrader_test extends question_attempt_upgrader_test_base {

    public function test_shortanswer_deferredfeedback_history620() {
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
            'questions' => '4184,0,4185,0,4154,0,4186,0,4187,0,4188,0,4189,0,4190,0,4162,0,4191,0,4192,0,4193,0,4254,0,4195,0,4196,0,4163,0,4197,0,4198,0,4199,0,4164,0,4200,0,4165,0,4201,0,4202,0,4166,0,4203,0,4204,0,4205,0,4167,0,4155,0,4168,0,4206,0,4207,0,4208,0,4209,0,4210,0,4211,0,4212,0,4213,0,4214,0,4156,0,4215,0,4216,0,4217,0,4169,0,4170,0,4157,0,4218,0,4219,0,4220,0,4171,0,4221,0,4172,0,4222,0,4223,0,4224,0,4225,0,4226,0,4227,0,4228,0,4173,0,4229,0,4230,0,4231,0,4232,0,4174,0,4233,0,4234,0,4235,0,4236,0,4237,0,4238,0,4239,0,4240,0,4241,0,4242,0,4158,0,4243,0,4244,0,4245,0,4246,0,4159,0,4175,0,4247,0,4176,0,4248,0,4177,0,4160,0,4249,0,4178,0,4250,0,4161,0,4251,0,4179,0,4252,0,4180,0,4181,0,4182,0,4253,0,4183,0',
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
            'id' => '4025',
            'uniqueid' => '4025',
            'quiz' => '221',
            'userid' => '55568',
            'attempt' => '1',
            'sumgrades' => '30',
            'timestart' => '1178549306',
            'timefinish' => '1178641326',
            'timemodified' => '1178549306',
            'layout' => '4184,0,4185,0,4154,0,4186,0,4187,0,4188,0,4189,0,4190,0,4162,0,4191,0,4192,0,4193,0,4254,0,4195,0,4196,0,4163,0,4197,0,4198,0,4199,0,4164,0,4200,0,4165,0,4201,0,4202,0,4166,0,4203,0,4204,0,4205,0,4167,0,4155,0,4168,0,4206,0,4207,0,4208,0,4209,0,4210,0,4211,0,4212,0,4213,0,4214,0,4156,0,4215,0,4216,0,4217,0,4169,0,4170,0,4157,0,4218,0,4219,0,4220,0,4171,0,4221,0,4172,0,4222,0,4223,0,4224,0,4225,0,4226,0,4227,0,4228,0,4173,0,4229,0,4230,0,4231,0,4232,0,4174,0,4233,0,4234,0,4235,0,4236,0,4237,0,4238,0,4239,0,4240,0,4241,0,4242,0,4158,0,4243,0,4244,0,4245,0,4246,0,4159,0,4175,0,4247,0,4176,0,4248,0,4177,0,4160,0,4249,0,4178,0,4250,0,4161,0,4251,0,4179,0,4252,0,4180,0,4181,0,4182,0,4253,0,4183,0',
            'preview' => '0',
            'needsupgradetonewqe' => '1',
        );
        $question = (object) array(
            'id' => '4239',
            'category' => '204',
            'parent' => '0',
            'name' => '4hdP73 Book 11 Section 2.1 ignore unavoidable costs when analysing data',
            'questiontext' => 'Complete the following sentence.
 <p>In general, the procedures for analysing cost data for decision making are: </p>
 <p>Ignore all sunk costs, ignore all ______ costs, use remaining costs for decision making purposes. </p>',
            'questiontextformat' => '1',
            'defaultmark' => '1',
            'penalty' => '1',
            'qtype' => 'shortanswer',
            'length' => '1',
            'stamp' => 'learn.open.ac.uk+070417143728+6z2qbB',
            'version' => 'learn.open.ac.uk+070417143728+BJ8YOd',
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
                    12944 => (object) array(
                        'question' => '4239',
                        'answer' => 'unavoidable*',
                        'fraction' => '1',
                        'feedback' => 'Yes, the correct answer is unavoidable costs. Well done! <p> Book 11 Section 2.1',
                        'id' => 12944,
                    ),
                    12945 => (object) array(
                        'question' => '4239',
                        'answer' => 'irrelevant*',
                        'fraction' => '1',
                        'feedback' => 'Yes, the correct answer is unavoidable (or \'irrelevant\') costs. Well done! <p> Book 11 Section 2.1',
                        'id' => 12945,
                    ),
                    12946 => (object) array(
                        'question' => '4239',
                        'answer' => 'comitte*',
                        'fraction' => '1',
                        'feedback' => 'Yes, the correct answer is unavoidable costs. Well done! <i> <p> Book 11 Section 2.1',
                        'id' => 12946,
                    ),
                    12947 => (object) array(
                        'question' => '4239',
                        'answer' => 'commite*',
                        'fraction' => '1',
                        'feedback' => 'Yes, the correct answer is unavoidable costs. Well done! <i> <p> Book 11 Section 2.1',
                        'id' => 12947,
                    ),
                    12948 => (object) array(
                        'question' => '4239',
                        'answer' => '*',
                        'fraction' => '0',
                        'feedback' => 'The correct answer is unavoidable costs. <p> Book 11 Section 2.1',
                        'id' => 12948,
                    ),
                ),
                'usecase' => '0',
            ),
            'hints' => false,
        );
        $qsession = (object) array(
            'id' => '39422',
            'attemptid' => '4025',
            'questionid' => '4239',
            'newest' => '94517',
            'newgraded' => '94517',
            'sumpenalty' => '1',
            'manualcomment' => '',
            'manualcommentformat' => '1',
            'flagged' => '1',
        );
        $qstates = array(
            92129 => (object) array(
                'attempt' => '4025',
                'question' => '4239',
                'originalquestion' => '0',
                'seq_number' => '0',
                'answer' => '',
                'timestamp' => '1178549306',
                'event' => '0',
                'grade' => '0',
                'raw_grade' => '0',
                'penalty' => '0',
                'id' => 92129,
            ),
            94433 => (object) array(
                'attempt' => '4025',
                'question' => '4239',
                'originalquestion' => '0',
                'seq_number' => '1',
                'answer' => 'irrelevant',
                'timestamp' => '1178639607',
                'event' => '2',
                'grade' => '0',
                'raw_grade' => '1',
                'penalty' => '1',
                'id' => 94433,
            ),
            94517 => (object) array(
                'attempt' => '4025',
                'question' => '4239',
                'originalquestion' => '0',
                'seq_number' => '2',
                'answer' => 'irrelevant',
                'timestamp' => '1178639607',
                'event' => '6',
                'grade' => '1',
                'raw_grade' => '1',
                'penalty' => '1',
                'id' => 94517,
            ),
        );

        $qa = $this->updater->convert_question_attempt($quiz, $attempt, $question, $qsession, $qstates);

        $expectedqa = (object) array(
            'behaviour' => 'deferredfeedback',
            'questionid' => 4239,
            'variant' => 1,
            'maxmark' => 1,
            'minfraction' => 0,
            'flagged' => 0,
            'questionsummary' => "Complete the following sentence. \n\nIn general, the procedures for analysing cost data for decision making are:  \n\nIgnore all sunk costs, ignore all ______ costs, use remaining costs for decision making purposes.",
            'rightanswer' => 'unavoidable*',
            'responsesummary' => 'irrelevant',
            'timemodified' => 1178639607,
            'steps' => array(
                0 => (object) array(
                    'sequencenumber' => 0,
                    'state' => 'todo',
                    'fraction' => null,
                    'timecreated' => 1178549306,
                    'userid' => 55568,
                    'data' => array(),
                ),
                1 => (object) array(
                    'sequencenumber' => 1,
                    'state' => 'complete',
                    'fraction' => null,
                    'timecreated' => 1178639607,
                    'userid' => 55568,
                    'data' => array('answer' => 'irrelevant'),
                ),
                2 => (object) array(
                    'sequencenumber' => 2,
                    'state' => 'gradedright',
                    'fraction' => 1,
                    'timecreated' => 1178639607,
                    'userid' => 55568,
                    'data' => array('answer' => 'irrelevant', '-finish' => 1),
                ),
            ),
        );

        $this->compare_qas($expectedqa, $qa);
    }

    public function test_shortanswer_deferredfeedback_history60() {
        $quiz = (object) array(
            'id' => '789',
            'course' => '3500',
            'name' => 'Modes of integration quiz',
            'intro' => '<p><font size="2">Use this quiz to test your knowledge of when you have completed <span style="font-style: italic">Modes of Integration</span>.</font> </p>
        <p><font size="2">This quiz is for your information only, the results will not be used as part of the work based activity. </font></p>',
            'introformat' => FORMAT_HTML,
            'questiondecimalpoints' => '-1',
            'showuserpicture' => '1',
            'showblocks' => '1',
            'timeopen' => '1147960800',
            'timeclose' => '1233414000',
            'preferredbehaviour' => 'deferredfeedback',
            'attempts' => '0',
            'attemptonlast' => '0',
            'grademethod' => '1',
            'decimalpoints' => '2',
            'review' => '71760879',
            'questionsperpage' => '0',
            'shufflequestions' => '1',
            'shuffleanswers' => '1',
            'questions' => '10218,10216,10219,10220,10217,0',
            'sumgrades' => '5',
            'grade' => '5',
            'timecreated' => '0',
            'timemodified' => '1191939532',
            'password' => '',
            'subnet' => '',
            'popup' => '0',
            'delay1' => '0',
            'delay2' => '0',
            'timelimit' => '0',
        );
        $attempt = (object) array(
            'id' => '17778',
            'uniqueid' => '17778',
            'quiz' => '789',
            'userid' => '111471',
            'attempt' => '1',
            'sumgrades' => '4.6',
            'timestart' => '1161205933',
            'timefinish' => '1161206024',
            'timemodified' => '1161206024',
            'layout' => '10219,10220,10216,10217,10218,0',
            'preview' => '0',
            'needsupgradetonewqe' => '1',
        );
        $question = (object) array(
            'id' => '10216',
            'category' => '528',
            'parent' => '0',
            'name' => 'Q2',
            'questiontext' => '<font size="2">In information-oriented integration what is the document which describes all the data structures in a potential integrated system and their relationships, for example, the fact that one database contained on a server will consist of a subset of another database contained in another server?</font>',
            'questiontextformat' => '1',
            'defaultmark' => '1',
            'penalty' => '0.1',
            'qtype' => 'shortanswer',
            'length' => '1',
            'stamp' => 'vledemo.open.ac.uk+060512111049+aApRic',
            'version' => 'vledemo.open.ac.uk+060719152101+aydsEU',
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
                    31846 => (object) array(
                        'question' => '10216',
                        'answer' => '*Enterprise data model*',
                        'fraction' => '1',
                        'feedback' => 'It is the \'Enterprise data model\'.',
                        'id' => 31846,
                    ),
                    31847 => (object) array(
                        'question' => '10216',
                        'answer' => '*Enterprise*',
                        'fraction' => '0.8',
                        'feedback' => 'It is the \'Enterprise data model\'.',
                        'id' => 31847,
                    ),
                    31848 => (object) array(
                        'question' => '10216',
                        'answer' => '*',
                        'fraction' => '0',
                        'feedback' => 'It is the \'Enterprise data model\'.',
                        'id' => 31848,
                    ),
                ),
                'usecase' => '0',
            ),
            'hints' => false,
        );
        $qsession = (object) array(
            'id' => '283784',
            'attemptid' => '17778',
            'questionid' => '10216',
            'newest' => '677550',
            'newgraded' => '677550',
            'sumpenalty' => '0',
            'manualcomment' => '',
            'manualcommentformat' => '1',
            'flagged' => '1',
        );
        $qstates = array(
            677543 => (object) array(
                'attempt' => '17778',
                'question' => '10216',
                'originalquestion' => '0',
                'seq_number' => '0',
                'answer' => '',
                'timestamp' => '1161205933',
                'event' => '0',
                'grade' => '0',
                'raw_grade' => '0',
                'penalty' => '0',
                'id' => 677543,
            ),
            677550 => (object) array(
                'attempt' => '17778',
                'question' => '10216',
                'originalquestion' => '0',
                'seq_number' => '1',
                'answer' => 'enterprise data dictionary',
                'timestamp' => '1161206024',
                'event' => '6',
                'grade' => '0.8',
                'raw_grade' => '0.8',
                'penalty' => '0.1',
                'id' => 677550,
            ),
        );

        $qa = $this->updater->convert_question_attempt($quiz, $attempt, $question, $qsession, $qstates);

        $expectedqa = (object) array(
            'behaviour' => 'deferredfeedback',
            'questionid' => 10216,
            'variant' => 1,
            'maxmark' => 1,
            'minfraction' => 0,
            'flagged' => 0,
            'questionsummary' => 'In information-oriented integration what is the document which describes all the data structures in a potential integrated system and their relationships, for example, the fact that one database contained on a server will consist of a subset of another database contained in another server?',
            'rightanswer' => '*Enterprise data model*',
            'responsesummary' => 'enterprise data dictionary',
            'timemodified' => 1161206024,
            'steps' => array(
                0 => (object) array(
                    'sequencenumber' => 0,
                    'state' => 'todo',
                    'fraction' => null,
                    'timecreated' => 1161205933,
                    'userid' => 111471,
                    'data' => array(),
                ),
                1 => (object) array(
                    'sequencenumber' => 1,
                    'state' => 'gradedpartial',
                    'fraction' => 0.8,
                    'timecreated' => 1161206024,
                    'userid' => 111471,
                    'data' => array('answer' => 'enterprise data dictionary', '-finish' => 1),
                ),
            ),
        );

        $this->compare_qas($expectedqa, $qa);
    }

    public function test_shortanswer_deferredfeedback_history3220() {
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
            'questions' => '4184,0,4185,0,4154,0,4186,0,4187,0,4188,0,4189,0,4190,0,4162,0,4191,0,4192,0,4193,0,4254,0,4195,0,4196,0,4163,0,4197,0,4198,0,4199,0,4164,0,4200,0,4165,0,4201,0,4202,0,4166,0,4203,0,4204,0,4205,0,4167,0,4155,0,4168,0,4206,0,4207,0,4208,0,4209,0,4210,0,4211,0,4212,0,4213,0,4214,0,4156,0,4215,0,4216,0,4217,0,4169,0,4170,0,4157,0,4218,0,4219,0,4220,0,4171,0,4221,0,4172,0,4222,0,4223,0,4224,0,4225,0,4226,0,4227,0,4228,0,4173,0,4229,0,4230,0,4231,0,4232,0,4174,0,4233,0,4234,0,4235,0,4236,0,4237,0,4238,0,4239,0,4240,0,4241,0,4242,0,4158,0,4243,0,4244,0,4245,0,4246,0,4159,0,4175,0,4247,0,4176,0,4248,0,4177,0,4160,0,4249,0,4178,0,4250,0,4161,0,4251,0,4179,0,4252,0,4180,0,4181,0,4182,0,4253,0,4183,0',
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
            'id' => '4058',
            'uniqueid' => '4058',
            'quiz' => '221',
            'userid' => '83485',
            'attempt' => '1',
            'sumgrades' => '19',
            'timestart' => '1178636138',
            'timefinish' => '1178874880',
            'timemodified' => '1178636138',
            'layout' => '4184,0,4185,0,4154,0,4186,0,4187,0,4188,0,4189,0,4190,0,4162,0,4191,0,4192,0,4193,0,4254,0,4195,0,4196,0,4163,0,4197,0,4198,0,4199,0,4164,0,4200,0,4165,0,4201,0,4202,0,4166,0,4203,0,4204,0,4205,0,4167,0,4155,0,4168,0,4206,0,4207,0,4208,0,4209,0,4210,0,4211,0,4212,0,4213,0,4214,0,4156,0,4215,0,4216,0,4217,0,4169,0,4170,0,4157,0,4218,0,4219,0,4220,0,4171,0,4221,0,4172,0,4222,0,4223,0,4224,0,4225,0,4226,0,4227,0,4228,0,4173,0,4229,0,4230,0,4231,0,4232,0,4174,0,4233,0,4234,0,4235,0,4236,0,4237,0,4238,0,4239,0,4240,0,4241,0,4242,0,4158,0,4243,0,4244,0,4245,0,4246,0,4159,0,4175,0,4247,0,4176,0,4248,0,4177,0,4160,0,4249,0,4178,0,4250,0,4161,0,4251,0,4179,0,4252,0,4180,0,4181,0,4182,0,4253,0,4183,0',
            'preview' => '0',
            'needsupgradetonewqe' => '1',
        );
        $question = (object) array(
            'id' => '4184',
            'category' => '204',
            'parent' => '0',
            'name' => '4abP1 Book 11 Section 3.1 capit invest appraisal defn',
            'questiontext' => 'Complete the following sentence by providing an appropriate word for the indicated gap.
 <p>Capital investment appraisal involves both quantitative and ______ issues. </p>',
            'questiontextformat' => '1',
            'defaultmark' => '1',
            'penalty' => '1',
            'qtype' => 'shortanswer',
            'length' => '1',
            'stamp' => 'learn.open.ac.uk+070417143727+tO4yGs',
            'version' => 'learn.open.ac.uk+070417143727+fieanX',
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
                    12768 => (object) array(
                        'question' => '4184',
                        'answer' => 'qualitative*',
                        'fraction' => '1',
                        'feedback' => 'Yes, the correct answer is qualitative issues. Well done! <p> Book 11 Section 3.1',
                        'id' => 12768,
                    ),
                    12769 => (object) array(
                        'question' => '4184',
                        'answer' => 'qual*tat*ve*',
                        'fraction' => '1',
                        'feedback' => 'Yes, the correct answer is qualitative issues. Well done! <i>(but watch your spelling!)</i> <p> Book 11 Section 3.1',
                        'id' => 12769,
                    ),
                    12770 => (object) array(
                        'question' => '4184',
                        'answer' => '*',
                        'fraction' => '0',
                        'feedback' => 'The correct answer is qualitative issues. <p> Book 11 Section 3.1',
                        'id' => 12770,
                    ),
                ),
                'usecase' => '0',
            ),
            'hints' => false,
        );
        $qsession = (object) array(
            'id' => '40854',
            'attemptid' => '4058',
            'questionid' => '4184',
            'newest' => '100733',
            'newgraded' => '100733',
            'sumpenalty' => '1',
            'manualcomment' => '',
            'manualcommentformat' => '1',
            'flagged' => '1',
        );
        $qstates = array(
            94330 => (object) array(
                'attempt' => '4058',
                'question' => '4184',
                'originalquestion' => '0',
                'seq_number' => '0',
                'answer' => '',
                'timestamp' => '1178636138',
                'event' => '0',
                'grade' => '0',
                'raw_grade' => '0',
                'penalty' => '0',
                'id' => 94330,
            ),
            94415 => (object) array(
                'attempt' => '4058',
                'question' => '4184',
                'originalquestion' => '0',
                'seq_number' => '1',
                'answer' => 'qualitative',
                'timestamp' => '1178636171',
                'event' => '2',
                'grade' => '0',
                'raw_grade' => '1',
                'penalty' => '1',
                'id' => 94415,
            ),
            100142 => (object) array(
                'attempt' => '4058',
                'question' => '4184',
                'originalquestion' => '0',
                'seq_number' => '2',
                'answer' => 'subjective',
                'timestamp' => '1178825180',
                'event' => '2',
                'grade' => '0',
                'raw_grade' => '0',
                'penalty' => '1',
                'id' => 100142,
            ),
            100733 => (object) array(
                'attempt' => '4058',
                'question' => '4184',
                'originalquestion' => '0',
                'seq_number' => '3',
                'answer' => 'subjective',
                'timestamp' => '1178825180',
                'event' => '3',
                'grade' => '0',
                'raw_grade' => '0',
                'penalty' => '1',
                'id' => 100733,
            ),
        );

        $qa = $this->updater->convert_question_attempt($quiz, $attempt, $question, $qsession, $qstates);

        $expectedqa = (object) array(
            'behaviour' => 'deferredfeedback',
            'questionid' => 4184,
            'variant' => 1,
            'maxmark' => 1,
            'minfraction' => 0,
            'flagged' => 0,
            'questionsummary' => "Complete the following sentence by providing an appropriate word for the indicated gap. \n\nCapital investment appraisal involves both quantitative and ______ issues.",
            'rightanswer' => 'qualitative*',
            'responsesummary' => 'subjective',
            'timemodified' => 1178825180,
            'steps' => array(
                0 => (object) array(
                    'sequencenumber' => 0,
                    'state' => 'todo',
                    'fraction' => null,
                    'timecreated' => 1178636138,
                    'userid' => 83485,
                    'data' => array(),
                ),
                1 => (object) array(
                    'sequencenumber' => 1,
                    'state' => 'complete',
                    'fraction' => null,
                    'timecreated' => 1178636171,
                    'userid' => 83485,
                    'data' => array('answer' => 'qualitative'),
                ),
                2 => (object) array(
                    'sequencenumber' => 2,
                    'state' => 'complete',
                    'fraction' => null,
                    'timecreated' => 1178825180,
                    'userid' => 83485,
                    'data' => array('answer' => 'subjective'),
                ),
                3 => (object) array(
                    'sequencenumber' => 3,
                    'state' => 'gradedwrong',
                    'fraction' => 0,
                    'timecreated' => 1178825180,
                    'userid' => 83485,
                    'data' => array('answer' => 'subjective', '-finish' => 1),
                ),
            ),
        );

        $this->compare_qas($expectedqa, $qa);
    }
}

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
 * multianswer questions.
 *
 * @package    qtype
 * @subpackage multianswer
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/engine/upgrade/simpletest/helper.php');


/**
 * Testing the upgrade of multianswer question attempts.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_multianswer_attempt_upgrader_test extends question_attempt_upgrader_test_base {
    public function test_multianswer_adaptivenopenalty_qsession104() {
        $quiz = (object) array(
            'id' => '5',
            'course' => '2',
            'name' => 'Multianswer quiz',
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
            'questions' => '28,19,0',
            'sumgrades' => '14.00000',
            'grade' => '10.00000',
            'timecreated' => '0',
            'timemodified' => '1306424728',
            'timelimit' => '0',
            'password' => '',
            'subnet' => '',
            'popup' => '0',
            'delay1' => '0',
            'delay2' => '0',
            'showuserpicture' => '0',
            'showblocks' => '0',
            'preferredbehaviour' => 'adaptivenopenalty',
        );
        $attempt = (object) array(
            'id' => '16',
            'uniqueid' => '16',
            'quiz' => '5',
            'userid' => '4',
            'attempt' => '1',
            'sumgrades' => '6.00000',
            'timestart' => '1306425691',
            'timefinish' => '1306425746',
            'timemodified' => '1306425746',
            'layout' => '28,19,0',
            'preview' => '0',
            'needsupgradetonewqe' => 1,
        );
        $question = (object) array(
            'id' => '28',
            'category' => '2',
            'parent' => '0',
            'name' => 'Very simple cloze',
            'questiontext' => '<p>An answer {#1}.</p>',
            'questiontextformat' => '1',
            'generalfeedback' => '',
            'generalfeedbackformat' => '1',
            'penalty' => '0.1000000',
            'qtype' => 'multianswer',
            'length' => '1',
            'stamp' => 'tjh238.vledev2.open.ac.uk+110526154631+XQLcXi',
            'version' => 'tjh238.vledev2.open.ac.uk+110526154631+T8hPiI',
            'hidden' => '0',
            'timecreated' => '1306424791',
            'timemodified' => '1306424791',
            'createdby' => '2',
            'modifiedby' => '2',
            'maxmark' => '1.0000000',
            'options' => (object) array(
                'questions' => array(
                    1 => (object) array(
                        'id' => '29',
                        'category' => '2',
                        'parent' => '28',
                        'name' => 'Very simple cloze',
                        'questiontext' => '{1:SHORTANSWER:=frog#Yay!}',
                        'questiontextformat' => '0',
                        'generalfeedback' => '',
                        'generalfeedbackformat' => '1',
                        'defaultgrade' => '1.0000000',
                        'penalty' => '0.0000000',
                        'qtype' => 'shortanswer',
                        'length' => '1',
                        'stamp' => 'tjh238.vledev2.open.ac.uk+110526154631+j3BYTL',
                        'version' => 'tjh238.vledev2.open.ac.uk+110526154631+lxNwQv',
                        'hidden' => '0',
                        'timecreated' => '1306424791',
                        'timemodified' => '1306424791',
                        'createdby' => '2',
                        'modifiedby' => '2',
                        'options' => (object) array(
                            'answers' => array(
                                52 => (object) array(
                                    'id' => '52',
                                    'question' => '29',
                                    'answer' => 'frog',
                                    'answerformat' => '0',
                                    'fraction' => '1.0000000',
                                    'feedback' => 'Yay!',
                                    'feedbackformat' => '1',
                                ),
                            ),
                            'usecase' => '0',
                        ),
                        'maxgrade' => '1.0000000',
                    ),
                ),
            ),
            'defaultmark' => '1.0000000',
        );
        $qsession = (object) array(
            'id' => '104',
            'attemptid' => '16',
            'questionid' => '28',
            'newest' => '285',
            'newgraded' => '285',
            'sumpenalty' => '0.1000000',
            'manualcomment' => '',
            'manualcommentformat' => '1',
            'flagged' => '0',
        );
        $qstates = array(
            281 => (object) array(
                'id' => '281',
                'attempt' => '16',
                'question' => '28',
                'seq_number' => '0',
                'answer' => '1-',
                'timestamp' => '1306425691',
                'event' => '0',
                'grade' => '0.0000000',
                'raw_grade' => '0.0000000',
                'penalty' => '0.0000000',
            ),
            283 => (object) array(
                'id' => '283',
                'attempt' => '16',
                'question' => '28',
                'seq_number' => '1',
                'answer' => '1-frog',
                'timestamp' => '1306425739',
                'event' => '2',
                'grade' => '0.0000000',
                'raw_grade' => '1.0000000',
                'penalty' => '0.1000000',
            ),
            285 => (object) array(
                'id' => '285',
                'attempt' => '16',
                'question' => '28',
                'seq_number' => '2',
                'answer' => '1-frog',
                'timestamp' => '1306425739',
                'event' => '6',
                'grade' => '1.0000000',
                'raw_grade' => '1.0000000',
                'penalty' => '0.1000000',
            ),
        );

        $qa = $this->updater->convert_question_attempt($quiz, $attempt, $question, $qsession, $qstates);

        $expectedqa = (object) array(
            'behaviour' => 'adaptivenopenalty',
            'questionid' => 28,
            'maxmark' => 1.0000000,
            'minfraction' => 0,
            'flagged' => 0,
            'questionsummary' => 'An answer _____.',
            'rightanswer' => 'part 1: frog',
            'responsesummary' => 'part 1: frog',
            'timemodified' => 1306425739,
            'steps' => array(
                0 => (object) array(
                    'sequencenumber' => 0,
                    'state' => 'todo',
                    'fraction' => null,
                    'timecreated' => 1306425691,
                    'userid' => 4,
                    'data' => array('-_try' => 1),
                ),
                1 => (object) array(
                    'sequencenumber' => 1,
                    'state' => 'complete',
                    'fraction' => null,
                    'timecreated' => 1306425739,
                    'userid' => 4,
                    'data' => array('sub1_answer' => 'frog'),
                ),
                2 => (object) array(
                    'sequencenumber' => 2,
                    'state' => 'gradedright',
                    'fraction' => 1.0,
                    'timecreated' => 1306425739,
                    'userid' => 4,
                    'data' => array('sub1_answer' => 'frog', '-finish' => '1',
                            '-_try' => '1', '-_rawfraction' => 1.0),
                ),
            ),
        );

        $this->assertEqual($expectedqa, $qa);
    }

    public function test_multianswer_adaptivenopenalty_qsession106() {
        $quiz = (object) array(
            'id' => '5',
            'course' => '2',
            'name' => 'Multianswer quiz',
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
            'questions' => '28,19,0',
            'sumgrades' => '14.00000',
            'grade' => '10.00000',
            'timecreated' => '0',
            'timemodified' => '1306424728',
            'timelimit' => '0',
            'password' => '',
            'subnet' => '',
            'popup' => '0',
            'delay1' => '0',
            'delay2' => '0',
            'showuserpicture' => '0',
            'showblocks' => '0',
            'preferredbehaviour' => 'adaptivenopenalty',
        );
        $attempt = (object) array(
            'id' => '17',
            'uniqueid' => '17',
            'quiz' => '5',
            'userid' => '4',
            'attempt' => '2',
            'sumgrades' => '0.00000',
            'timestart' => '1306425757',
            'timefinish' => '1306425762',
            'timemodified' => '1306425762',
            'layout' => '28,19,0',
            'preview' => '0',
            'needsupgradetonewqe' => 1,
        );
        $question = (object) array(
            'id' => '28',
            'category' => '2',
            'parent' => '0',
            'name' => 'Very simple cloze',
            'questiontext' => '<p>An answer {#1}.</p>',
            'questiontextformat' => '1',
            'generalfeedback' => '',
            'generalfeedbackformat' => '1',
            'penalty' => '0.1000000',
            'qtype' => 'multianswer',
            'length' => '1',
            'stamp' => 'tjh238.vledev2.open.ac.uk+110526154631+XQLcXi',
            'version' => 'tjh238.vledev2.open.ac.uk+110526154631+T8hPiI',
            'hidden' => '0',
            'timecreated' => '1306424791',
            'timemodified' => '1306424791',
            'createdby' => '2',
            'modifiedby' => '2',
            'maxmark' => '1.0000000',
            'options' => (object) array(
                'questions' => array(
                    1 => (object) array(
                        'id' => '29',
                        'category' => '2',
                        'parent' => '28',
                        'name' => 'Very simple cloze',
                        'questiontext' => '{1:SHORTANSWER:=frog#Yay!}',
                        'questiontextformat' => '0',
                        'generalfeedback' => '',
                        'generalfeedbackformat' => '1',
                        'defaultgrade' => '1.0000000',
                        'penalty' => '0.0000000',
                        'qtype' => 'shortanswer',
                        'length' => '1',
                        'stamp' => 'tjh238.vledev2.open.ac.uk+110526154631+j3BYTL',
                        'version' => 'tjh238.vledev2.open.ac.uk+110526154631+lxNwQv',
                        'hidden' => '0',
                        'timecreated' => '1306424791',
                        'timemodified' => '1306424791',
                        'createdby' => '2',
                        'modifiedby' => '2',
                        'options' => (object) array(
                            'answers' => array(
                                52 => (object) array(
                                    'id' => '52',
                                    'question' => '29',
                                    'answer' => 'frog',
                                    'answerformat' => '0',
                                    'fraction' => '1.0000000',
                                    'feedback' => 'Yay!',
                                    'feedbackformat' => '1',
                                ),
                            ),
                            'usecase' => '0',
                        ),
                        'maxgrade' => '1.0000000',
                    ),
                ),
            ),
            'defaultmark' => '1.0000000',
        );
        $qsession = (object) array(
            'id' => '106',
            'attemptid' => '17',
            'questionid' => '28',
            'newest' => '289',
            'newgraded' => '289',
            'sumpenalty' => '0.1000000',
            'manualcomment' => '',
            'manualcommentformat' => '1',
            'flagged' => '0',
        );
        $qstates = array(
            287 => (object) array(
                'id' => '287',
                'attempt' => '17',
                'question' => '28',
                'seq_number' => '0',
                'answer' => '1-',
                'timestamp' => '1306425757',
                'event' => '0',
                'grade' => '0.0000000',
                'raw_grade' => '0.0000000',
                'penalty' => '0.0000000',
            ),
            289 => (object) array(
                'id' => '289',
                'attempt' => '17',
                'question' => '28',
                'seq_number' => '1',
                'answer' => '1-',
                'timestamp' => '1306425757',
                'event' => '6',
                'grade' => '0.0000000',
                'raw_grade' => '0.0000000',
                'penalty' => '0.1000000',
            ),
        );

        $qa = $this->updater->convert_question_attempt($quiz, $attempt, $question, $qsession, $qstates);

        $expectedqa = (object) array(
            'behaviour' => 'adaptivenopenalty',
            'questionid' => 28,
            'maxmark' => 1.0000000,
            'minfraction' => 0,
            'flagged' => 0,
            'questionsummary' => 'An answer _____.',
            'rightanswer' => 'part 1: frog',
            'responsesummary' => 'part 1: ',
            'timemodified' => 1306425757,
            'steps' => array(
                0 => (object) array(
                    'sequencenumber' => 0,
                    'state' => 'todo',
                    'fraction' => null,
                    'timecreated' => 1306425757,
                    'userid' => 4,
                    'data' => array('-_try' => '1'),
                ),
                1 => (object) array(
                    'sequencenumber' => 1,
                    'state' => 'gradedwrong',
                    'fraction' => 0.0,
                    'timecreated' => 1306425757,
                    'userid' => 4,
                    'data' => array('-finish' => '1', '-_try' => '1', '-_rawfraction' => 0.0),
                ),
            ),
        );

        $this->assertEqual($expectedqa, $qa);
    }

    public function test_multianswer_adaptivenopenalty_qsession108() {
        $quiz = (object) array(
            'id' => '5',
            'course' => '2',
            'name' => 'Multianswer quiz',
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
            'questions' => '28,19,0',
            'sumgrades' => '14.00000',
            'grade' => '10.00000',
            'timecreated' => '0',
            'timemodified' => '1306424728',
            'timelimit' => '0',
            'password' => '',
            'subnet' => '',
            'popup' => '0',
            'delay1' => '0',
            'delay2' => '0',
            'showuserpicture' => '0',
            'showblocks' => '0',
            'preferredbehaviour' => 'adaptivenopenalty',
        );
        $attempt = (object) array(
            'id' => '18',
            'uniqueid' => '18',
            'quiz' => '5',
            'userid' => '3',
            'attempt' => '1',
            'sumgrades' => '10.40000',
            'timestart' => '1306425784',
            'timefinish' => '1306425931',
            'timemodified' => '1306425931',
            'layout' => '28,19,0',
            'preview' => '0',
            'needsupgradetonewqe' => 1,
        );
        $question = (object) array(
            'id' => '28',
            'category' => '2',
            'parent' => '0',
            'name' => 'Very simple cloze',
            'questiontext' => '<p>An answer {#1}.</p>',
            'questiontextformat' => '1',
            'generalfeedback' => '',
            'generalfeedbackformat' => '1',
            'penalty' => '0.1000000',
            'qtype' => 'multianswer',
            'length' => '1',
            'stamp' => 'tjh238.vledev2.open.ac.uk+110526154631+XQLcXi',
            'version' => 'tjh238.vledev2.open.ac.uk+110526154631+T8hPiI',
            'hidden' => '0',
            'timecreated' => '1306424791',
            'timemodified' => '1306424791',
            'createdby' => '2',
            'modifiedby' => '2',
            'maxmark' => '1.0000000',
            'options' => (object) array(
                'questions' => array(
                    1 => (object) array(
                        'id' => '29',
                        'category' => '2',
                        'parent' => '28',
                        'name' => 'Very simple cloze',
                        'questiontext' => '{1:SHORTANSWER:=frog#Yay!}',
                        'questiontextformat' => '0',
                        'generalfeedback' => '',
                        'generalfeedbackformat' => '1',
                        'defaultgrade' => '1.0000000',
                        'penalty' => '0.0000000',
                        'qtype' => 'shortanswer',
                        'length' => '1',
                        'stamp' => 'tjh238.vledev2.open.ac.uk+110526154631+j3BYTL',
                        'version' => 'tjh238.vledev2.open.ac.uk+110526154631+lxNwQv',
                        'hidden' => '0',
                        'timecreated' => '1306424791',
                        'timemodified' => '1306424791',
                        'createdby' => '2',
                        'modifiedby' => '2',
                        'options' => (object) array(
                            'answers' => array(
                                52 => (object) array(
                                    'id' => '52',
                                    'question' => '29',
                                    'answer' => 'frog',
                                    'answerformat' => '0',
                                    'fraction' => '1.0000000',
                                    'feedback' => 'Yay!',
                                    'feedbackformat' => '1',
                                ),
                            ),
                            'usecase' => '0',
                        ),
                        'maxgrade' => '1.0000000',
                    ),
                ),
            ),
            'defaultmark' => '1.0000000',
        );
        $qsession = (object) array(
            'id' => '108',
            'attemptid' => '18',
            'questionid' => '28',
            'newest' => '298',
            'newgraded' => '298',
            'sumpenalty' => '0.2000000',
            'manualcomment' => '',
            'manualcommentformat' => '1',
            'flagged' => '0',
        );
        $qstates = array(
            291 => (object) array(
                'id' => '291',
                'attempt' => '18',
                'question' => '28',
                'seq_number' => '0',
                'answer' => '1-',
                'timestamp' => '1306425784',
                'event' => '0',
                'grade' => '0.0000000',
                'raw_grade' => '0.0000000',
                'penalty' => '0.0000000',
            ),
            293 => (object) array(
                'id' => '293',
                'attempt' => '18',
                'question' => '28',
                'seq_number' => '1',
                'answer' => '1-ds&#0044;&#0045;afg',
                'timestamp' => '1306425801',
                'event' => '2',
                'grade' => '0.0000000',
                'raw_grade' => '0.0000000',
                'penalty' => '0.1000000',
            ),
            297 => (object) array(
                'id' => '297',
                'attempt' => '18',
                'question' => '28',
                'seq_number' => '2',
                'answer' => '1-frog',
                'timestamp' => '1306425917',
                'event' => '3',
                'grade' => '1.0000000',
                'raw_grade' => '1.0000000',
                'penalty' => '0.1000000',
            ),
            298 => (object) array(
                'id' => '298',
                'attempt' => '18',
                'question' => '28',
                'seq_number' => '3',
                'answer' => '1-frog',
                'timestamp' => '1306425917',
                'event' => '6',
                'grade' => '1.0000000',
                'raw_grade' => '1.0000000',
                'penalty' => '0.1000000',
            ),
        );

        $qa = $this->updater->convert_question_attempt($quiz, $attempt, $question, $qsession, $qstates);

        $expectedqa = (object) array(
            'behaviour' => 'adaptivenopenalty',
            'questionid' => 28,
            'maxmark' => 1.0000000,
            'minfraction' => 0,
            'flagged' => 0,
            'questionsummary' => 'An answer _____.',
            'rightanswer' => 'part 1: frog',
            'responsesummary' => 'part 1: frog',
            'timemodified' => 1306425917,
            'steps' => array(
                0 => (object) array(
                    'sequencenumber' => 0,
                    'state' => 'todo',
                    'fraction' => null,
                    'timecreated' => 1306425784,
                    'userid' => 3,
                    'data' => array('-_try' => '1'),
                ),
                1 => (object) array(
                    'sequencenumber' => 1,
                    'state' => 'complete',
                    'fraction' => null,
                    'timecreated' => 1306425801,
                    'userid' => 3,
                    'data' => array('sub1_answer' => 'ds,-afg'),
                ),
                2 => (object) array(
                    'sequencenumber' => 2,
                    'state' => 'complete',
                    'fraction' => 1,
                    'timecreated' => 1306425917,
                    'userid' => 3,
                    'data' => array('sub1_answer' => 'frog', '-_try' => 2,
                            '-_rawfraction' => 1.0, '-submit' => 1),
                ),
                3 => (object) array(
                    'sequencenumber' => 3,
                    'state' => 'gradedright',
                    'fraction' => 1,
                    'timecreated' => 1306425917,
                    'userid' => 3,
                    'data' => array('sub1_answer' => 'frog', '-_try' => 2,
                            '-_rawfraction' => 1.0, '-finish' => 1),
                ),
            ),
        );

        $this->assertEqual($expectedqa, $qa);
    }
}

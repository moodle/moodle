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
 * calculated questions.
 *
 * @package    qtype
 * @subpackage calculated
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/upgrade/tests/helper.php');


/**
 * Testing the upgrade of calculated question attempts.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_calculated_attempt_upgrader_test extends question_attempt_upgrader_test_base {
    public function test_calculated_adaptive_qsession97() {
        $quiz = (object) array(
            'id' => '4',
            'course' => '2',
            'name' => 'Calculated quiz',
            'intro' => '',
            'introformat' => '1',
            'timeopen' => '0',
            'timeclose' => '0',
            'attempts' => '0',
            'attemptonlast' => '0',
            'grademethod' => '1',
            'decimalpoints' => '2',
            'questiondecimalpoints' => '-1',
            'questionsperpage' => '1',
            'shufflequestions' => '0',
            'shuffleanswers' => '1',
            'sumgrades' => '3.00000',
            'grade' => '10.00000',
            'timecreated' => '0',
            'timemodified' => '1305648351',
            'timelimit' => '0',
            'password' => '',
            'subnet' => '',
            'popup' => '0',
            'delay1' => '0',
            'delay2' => '0',
            'showuserpicture' => '0',
            'showblocks' => '0',
            'preferredbehaviour' => 'adaptive',
            'reviewattempt' => '69888',
            'reviewcorrectness' => '69888',
            'reviewmarks' => '69888',
            'reviewspecificfeedback' => '69888',
            'reviewgeneralfeedback' => '69888',
            'reviewrightanswer' => '69888',
            'reviewoverallfeedback' => '4352',
        );
        $attempt = (object) array(
            'id' => '13',
            'uniqueid' => '13',
            'quiz' => '4',
            'userid' => '4',
            'attempt' => '1',
            'sumgrades' => '0.00000',
            'timestart' => '1305830650',
            'timefinish' => '1305830656',
            'timemodified' => '1305830656',
            'layout' => '16,0,17,0,18,0',
            'preview' => '0',
        );
        $question = (object) array(
            'id' => '18',
            'category' => '2',
            'parent' => '0',
            'name' => 'Calculated',
            'questiontext' => '<p>What is {a} m + {b} m?</p><p>_______________</p><p>Remember to type a unit.</p>',
            'questiontextformat' => '1',
            'generalfeedback' => '',
            'generalfeedbackformat' => '1',
            'defaultmark' => '1.0000000',
            'penalty' => '0.1',
            'qtype' => 'calculated',
            'length' => '1',
            'stamp' => 'tjh238.vledev2.open.ac.uk+110519184316+ELvZeg',
            'version' => 'tjh238.vledev2.open.ac.uk+110519184317+exx1Bm',
            'hidden' => '0',
            'timecreated' => '1305830596',
            'timemodified' => '1305830596',
            'createdby' => '2',
            'modifiedby' => '2',
            'maxmark' => '1.0000000',
            'options' => (object) array(
                'id' => '2',
                'question' => '18',
                'synchronize' => '0',
                'single' => '0',
                'shuffleanswers' => '1',
                'correctfeedback' => '',
                'correctfeedbackformat' => '0',
                'partiallycorrectfeedback' => '',
                'partiallycorrectfeedbackformat' => '0',
                'incorrectfeedback' => '',
                'incorrectfeedbackformat' => '0',
                'answernumbering' => 'abc',
                'shownumcorrect' => '0',
                'answers' => array(
                    28 => (object) array(
                        'id' => '28',
                        'question' => '18',
                        'answer' => '{a} + {b}',
                        'answerformat' => '0',
                        'fraction' => '1.0000000',
                        'feedback' => '',
                        'feedbackformat' => '1',
                        'tolerance' => '0.01',
                        'tolerancetype' => '1',
                        'correctanswerlength' => '2',
                        'correctanswerformat' => '1',
                    ),
                ),
                'units' => array(
                    0 => (object) array(
                        'id' => '9',
                        'question' => '18',
                        'multiplier' => 1,
                        'unit' => 'm',
                    ),
                ),
                'unitgradingtype' => '1',
                'unitpenalty' => '0.5000000',
                'showunits' => '0',
                'unitsleft' => '0',
            ),
            'hints' => array(
            ),
        );
        $qsession = (object) array(
            'id' => '97',
            'attemptid' => '13',
            'questionid' => '18',
            'newest' => '258',
            'newgraded' => '258',
            'sumpenalty' => '0.1000000',
            'manualcomment' => '',
            'manualcommentformat' => '1',
            'flagged' => '0',
        );
        $qstates = array(
            255 => (object) array(
                'id' => '255',
                'attempt' => '13',
                'question' => '18',
                'seq_number' => '0',
                'answer' => 'dataset10-|||||',
                'timestamp' => '1305830650',
                'event' => '0',
                'grade' => '0.0000000',
                'raw_grade' => '0.0000000',
                'penalty' => '0.0000000',
            ),
            258 => (object) array(
                'id' => '258',
                'attempt' => '13',
                'question' => '18',
                'seq_number' => '1',
                'answer' => 'dataset10-|||||',
                'timestamp' => '1305830650',
                'event' => '6',
                'grade' => '0.0000000',
                'raw_grade' => '0.0000000',
                'penalty' => '0.1000000',
            ),
        );
        $this->loader->put_dataset_in_cache($question->id, 10, array('a' => '7.5', 'b' => '4.9'));

        $qa = $this->updater->convert_question_attempt($quiz, $attempt, $question, $qsession, $qstates);

        $expectedqa = (object) array(
            'behaviour' => 'adaptive',
            'questionid' => 18,
            'variant' => 10,
            'maxmark' => 1.0000000,
            'minfraction' => 0,
            'maxfraction' => 1,
            'flagged' => 0,
            'questionsummary' => 'What is 7.5 m + 4.9 m?

_______________

Remember to type a unit.',
            'rightanswer' => '12.4 m',
            'responsesummary' => '',
            'timemodified' => 1305830650,
            'steps' => array(
                0 => (object) array(
                    'sequencenumber' => 0,
                    'state' => 'todo',
                    'fraction' => null,
                    'timecreated' => 1305830650,
                    'userid' => 4,
                    'data' => array('_separators' => '.$,',
                            '_var_a' => '7.5', '_var_b' => '4.9'),
                ),
                1 => (object) array(
                    'sequencenumber' => 1,
                    'state' => 'gradedwrong',
                    'fraction' => null,
                    'timecreated' => 1305830650,
                    'userid' => 4,
                    'data' => array('answer' => '', '-finish' => 1, '-_try' => 1, '-_rawfraction' => 0),
                ),
            ),
        );

        $this->compare_qas($expectedqa, $qa);
    }

    public function test_calculated_adaptive_qsession100() {
        $quiz = (object) array(
            'id' => '4',
            'course' => '2',
            'name' => 'Calculated quiz',
            'intro' => '',
            'introformat' => '1',
            'timeopen' => '0',
            'timeclose' => '0',
            'attempts' => '0',
            'attemptonlast' => '0',
            'grademethod' => '1',
            'decimalpoints' => '2',
            'questiondecimalpoints' => '-1',
            'questionsperpage' => '1',
            'shufflequestions' => '0',
            'shuffleanswers' => '1',
            'sumgrades' => '3.00000',
            'grade' => '10.00000',
            'timecreated' => '0',
            'timemodified' => '1305648351',
            'timelimit' => '0',
            'password' => '',
            'subnet' => '',
            'popup' => '0',
            'delay1' => '0',
            'delay2' => '0',
            'showuserpicture' => '0',
            'showblocks' => '0',
            'preferredbehaviour' => 'adaptive',
            'reviewattempt' => '69888',
            'reviewcorrectness' => '69888',
            'reviewmarks' => '69888',
            'reviewspecificfeedback' => '69888',
            'reviewgeneralfeedback' => '69888',
            'reviewrightanswer' => '69888',
            'reviewoverallfeedback' => '4352',
        );
        $attempt = (object) array(
            'id' => '14',
            'uniqueid' => '14',
            'quiz' => '4',
            'userid' => '4',
            'attempt' => '2',
            'sumgrades' => '2.80000',
            'timestart' => '1305830661',
            'timefinish' => '1305830729',
            'timemodified' => '1305830729',
            'layout' => '16,0,17,0,18,0',
            'preview' => '0',
        );
        $question = (object) array(
            'id' => '18',
            'category' => '2',
            'parent' => '0',
            'name' => 'Calculated',
            'questiontext' => '<p>What is {a} m + {b} m?</p><p>_______________</p><p>Remember to type a unit.</p>',
            'questiontextformat' => '1',
            'generalfeedback' => '',
            'generalfeedbackformat' => '1',
            'defaultmark' => '1.0000000',
            'penalty' => '0.1',
            'qtype' => 'calculated',
            'length' => '1',
            'stamp' => 'tjh238.vledev2.open.ac.uk+110519184316+ELvZeg',
            'version' => 'tjh238.vledev2.open.ac.uk+110519184317+exx1Bm',
            'hidden' => '0',
            'timecreated' => '1305830596',
            'timemodified' => '1305830596',
            'createdby' => '2',
            'modifiedby' => '2',
            'maxmark' => '1.0000000',
            'options' => (object) array(
                'id' => '2',
                'question' => '18',
                'synchronize' => '0',
                'single' => '0',
                'shuffleanswers' => '1',
                'correctfeedback' => '',
                'correctfeedbackformat' => '0',
                'partiallycorrectfeedback' => '',
                'partiallycorrectfeedbackformat' => '0',
                'incorrectfeedback' => '',
                'incorrectfeedbackformat' => '0',
                'answernumbering' => 'abc',
                'shownumcorrect' => '0',
                'answers' => array(
                    28 => (object) array(
                        'id' => '28',
                        'question' => '18',
                        'answer' => '{a} + {b}',
                        'answerformat' => '0',
                        'fraction' => '1.0000000',
                        'feedback' => '',
                        'feedbackformat' => '1',
                        'tolerance' => '0.01',
                        'tolerancetype' => '1',
                        'correctanswerlength' => '2',
                        'correctanswerformat' => '1',
                    ),
                ),
                'units' => array(
                    0 => (object) array(
                        'id' => '9',
                        'question' => '18',
                        'multiplier' => 1,
                        'unit' => 'm',
                    ),
                ),
                'unitgradingtype' => '1',
                'unitpenalty' => '0.5000000',
                'showunits' => '0',
                'unitsleft' => '0',
            ),
            'hints' => array(
            ),
        );
        $qsession = (object) array(
            'id' => '100',
            'attemptid' => '14',
            'questionid' => '18',
            'newest' => '269',
            'newgraded' => '269',
            'sumpenalty' => '0.3000000',
            'manualcomment' => '',
            'manualcommentformat' => '1',
            'flagged' => '0',
        );
        $qstates = array(
            261 => (object) array(
                'id' => '261',
                'attempt' => '14',
                'question' => '18',
                'seq_number' => '0',
                'answer' => 'dataset11-|||||',
                'timestamp' => '1305830661',
                'event' => '0',
                'grade' => '0.0000000',
                'raw_grade' => '0.0000000',
                'penalty' => '0.0000000',
            ),
            265 => (object) array(
                'id' => '265',
                'attempt' => '14',
                'question' => '18',
                'seq_number' => '1',
                'answer' => 'dataset11-9.6|||||',
                'timestamp' => '1305830714',
                'event' => '3',
                'grade' => '0.5000000',
                'raw_grade' => '0.5000000',
                'penalty' => '0.1000000',
            ),
            266 => (object) array(
                'id' => '266',
                'attempt' => '14',
                'question' => '18',
                'seq_number' => '2',
                'answer' => 'dataset11-9.6|||||m',
                'timestamp' => '1305830722',
                'event' => '3',
                'grade' => '0.9000000',
                'raw_grade' => '1.0000000',
                'penalty' => '0.1000000',
            ),
            269 => (object) array(
                'id' => '269',
                'attempt' => '14',
                'question' => '18',
                'seq_number' => '3',
                'answer' => 'dataset11-9.6|||||m',
                'timestamp' => '1305830722',
                'event' => '6',
                'grade' => '0.9000000',
                'raw_grade' => '1.0000000',
                'penalty' => '0.1000000',
            ),
        );
        $this->loader->put_dataset_in_cache($question->id, 11, array('a' => '5.1', 'b' => '4.5'));

        $qa = $this->updater->convert_question_attempt($quiz, $attempt, $question, $qsession, $qstates);

        $expectedqa = (object) array(
            'behaviour' => 'adaptive',
            'questionid' => 18,
            'variant' => 11,
            'maxmark' => 1.0000000,
            'minfraction' => 0,
            'maxfraction' => 1,
            'flagged' => 0,
            'questionsummary' => 'What is 5.1 m + 4.5 m?

_______________

Remember to type a unit.',
            'rightanswer' => '9.6 m',
            'responsesummary' => '9.6 m',
            'timemodified' => 1305830722,
            'steps' => array(
                0 => (object) array(
                    'sequencenumber' => 0,
                    'state' => 'todo',
                    'fraction' => null,
                    'timecreated' => 1305830661,
                    'userid' => 4,
                    'data' => array('_separators' => '.$,',
                            '_var_a' => '5.1', '_var_b' => '4.5'),
                ),
                1 => (object) array(
                    'sequencenumber' => 1,
                    'state' => 'todo',
                    'fraction' => 0.5,
                    'timecreated' => 1305830714,
                    'userid' => 4,
                    'data' => array('answer' => 9.6, '-_try' => 1,
                            '-_rawfraction' => 0.5, '-submit' => 1),
                ),
                2 => (object) array(
                    'sequencenumber' => 2,
                    'state' => 'todo',
                    'fraction' => 0.9,
                    'timecreated' => 1305830722,
                    'userid' => 4,
                    'data' => array('answer' => '9.6 m', '-_try' => 2,
                            '-_rawfraction' => 1, '-submit' => 1),
                ),
                3 => (object) array(
                    'sequencenumber' => 3,
                    'state' => 'gradedright',
                    'fraction' => 0.9,
                    'timecreated' => 1305830722,
                    'userid' => 4,
                    'data' => array('answer' => '9.6 m', '-_try' => 2,
                            '-_rawfraction' => 1, '-finish' => 1),
                ),
            ),
        );

        $this->compare_qas($expectedqa, $qa);
    }

    public function test_calculated_adaptive_qsession103() {
        $quiz = (object) array(
            'id' => '4',
            'course' => '2',
            'name' => 'Calculated quiz',
            'intro' => '',
            'introformat' => '1',
            'timeopen' => '0',
            'timeclose' => '0',
            'attempts' => '0',
            'attemptonlast' => '0',
            'grademethod' => '1',
            'decimalpoints' => '2',
            'questiondecimalpoints' => '-1',
            'questionsperpage' => '1',
            'shufflequestions' => '0',
            'shuffleanswers' => '1',
            'sumgrades' => '3.00000',
            'grade' => '10.00000',
            'timecreated' => '0',
            'timemodified' => '1305648351',
            'timelimit' => '0',
            'password' => '',
            'subnet' => '',
            'popup' => '0',
            'delay1' => '0',
            'delay2' => '0',
            'showuserpicture' => '0',
            'showblocks' => '0',
            'preferredbehaviour' => 'adaptive',
            'reviewattempt' => '69888',
            'reviewcorrectness' => '69888',
            'reviewmarks' => '69888',
            'reviewspecificfeedback' => '69888',
            'reviewgeneralfeedback' => '69888',
            'reviewrightanswer' => '69888',
            'reviewoverallfeedback' => '4352',
        );
        $attempt = (object) array(
            'id' => '15',
            'uniqueid' => '15',
            'quiz' => '4',
            'userid' => '3',
            'attempt' => '1',
            'sumgrades' => '0.70000',
            'timestart' => '1305830744',
            'timefinish' => '0',
            'timemodified' => '1305830792',
            'layout' => '16,0,17,0,18,0',
            'preview' => '0',
        );
        $question = (object) array(
            'id' => '18',
            'category' => '2',
            'parent' => '0',
            'name' => 'Calculated',
            'questiontext' => '<p>What is {a} m + {b} m?</p><p>_______________</p><p>Remember to type a unit.</p>',
            'questiontextformat' => '1',
            'generalfeedback' => '',
            'generalfeedbackformat' => '1',
            'defaultmark' => '1.0000000',
            'penalty' => '0.1',
            'qtype' => 'calculated',
            'length' => '1',
            'stamp' => 'tjh238.vledev2.open.ac.uk+110519184316+ELvZeg',
            'version' => 'tjh238.vledev2.open.ac.uk+110519184317+exx1Bm',
            'hidden' => '0',
            'timecreated' => '1305830596',
            'timemodified' => '1305830596',
            'createdby' => '2',
            'modifiedby' => '2',
            'maxmark' => '1.0000000',
            'options' => (object) array(
                'id' => '2',
                'question' => '18',
                'synchronize' => '0',
                'single' => '0',
                'shuffleanswers' => '1',
                'correctfeedback' => '',
                'correctfeedbackformat' => '0',
                'partiallycorrectfeedback' => '',
                'partiallycorrectfeedbackformat' => '0',
                'incorrectfeedback' => '',
                'incorrectfeedbackformat' => '0',
                'answernumbering' => 'abc',
                'shownumcorrect' => '0',
                'answers' => array(
                    28 => (object) array(
                        'id' => '28',
                        'question' => '18',
                        'answer' => '{a} + {b}',
                        'answerformat' => '0',
                        'fraction' => '1.0000000',
                        'feedback' => '',
                        'feedbackformat' => '1',
                        'tolerance' => '0.01',
                        'tolerancetype' => '1',
                        'correctanswerlength' => '2',
                        'correctanswerformat' => '1',
                    ),
                ),
                'units' => array(
                    0 => (object) array(
                        'id' => '9',
                        'question' => '18',
                        'multiplier' => 1,
                        'unit' => 'm',
                    ),
                ),
                'unitgradingtype' => '1',
                'unitpenalty' => '0.5000000',
                'showunits' => '0',
                'unitsleft' => '0',
            ),
            'hints' => array(
            ),
        );
        $qsession = (object) array(
            'id' => '103',
            'attemptid' => '15',
            'questionid' => '18',
            'newest' => '280',
            'newgraded' => '279',
            'sumpenalty' => '0.1000000',
            'manualcomment' => '',
            'manualcommentformat' => '1',
            'flagged' => '0',
        );
        $qstates = array(
            272 => (object) array(
                'id' => '272',
                'attempt' => '15',
                'question' => '18',
                'seq_number' => '0',
                'answer' => 'dataset1-|||||',
                'timestamp' => '1305830744',
                'event' => '0',
                'grade' => '0.0000000',
                'raw_grade' => '0.0000000',
                'penalty' => '0.0000000',
            ),
            279 => (object) array(
                'id' => '279',
                'attempt' => '15',
                'question' => '18',
                'seq_number' => '1',
                'answer' => 'dataset1-123|||||cm',
                'timestamp' => '1305830775',
                'event' => '3',
                'grade' => '0.0000000',
                'raw_grade' => '0.0000000',
                'penalty' => '0.1000000',
            ),
            280 => (object) array(
                'id' => '280',
                'attempt' => '15',
                'question' => '18',
                'seq_number' => '2',
                'answer' => 'dataset1-12.4|||||',
                'timestamp' => '1305830787',
                'event' => '2',
                'grade' => '0.0000000',
                'raw_grade' => '0.5000000',
                'penalty' => '0.1000000',
            ),
        );
        $this->loader->put_dataset_in_cache($question->id, 1, array('a' => '9.9', 'b' => '2.5'));

        $qa = $this->updater->convert_question_attempt($quiz, $attempt, $question, $qsession, $qstates);

        $expectedqa = (object) array(
            'behaviour' => 'adaptive',
            'questionid' => 18,
            'variant' => 1,
            'maxmark' => 1.0000000,
            'minfraction' => 0,
            'maxfraction' => 1,
            'flagged' => 0,
            'questionsummary' => 'What is 9.9 m + 2.5 m?

_______________

Remember to type a unit.',
            'rightanswer' => '12.4 m',
            'responsesummary' => '12.4',
            'timemodified' => 1305830787,
            'steps' => array(
                0 => (object) array(
                    'sequencenumber' => 0,
                    'state' => 'todo',
                    'fraction' => null,
                    'timecreated' => 1305830744,
                    'userid' => 3,
                    'data' => array('_separators' => '.$,',
                            '_var_a' => '9.9', '_var_b' => '2.5'),
                ),
                1 => (object) array(
                    'sequencenumber' => 1,
                    'state' => 'todo',
                    'fraction' => 0,
                    'timecreated' => 1305830775,
                    'userid' => 3,
                    'data' => array('answer' => '123 cm', '-_try' => 1,
                            '-_rawfraction' => 0, '-submit' => 1),
                ),
                2 => (object) array(
                    'sequencenumber' => 2,
                    'state' => 'complete',
                    'fraction' => 0,
                    'timecreated' => 1305830787,
                    'userid' => 3,
                    'data' => array('answer' => '12.4'),
                ),
            ),
        );

        $this->compare_qas($expectedqa, $qa);
    }
}

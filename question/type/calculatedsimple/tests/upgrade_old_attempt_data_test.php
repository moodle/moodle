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

namespace qtype_calculatedsimple;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/upgrade/tests/helper.php');


/**
 * Testing the upgrade of simple calculated question attempts.
 *
 * @package    qtype_calculatedsimple
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class upgrade_old_attempt_data_test extends \question_attempt_upgrader_test_base {
    public function test_calculatedsimple_adaptive_qsession95(): void {
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
            'id' => '16',
            'category' => '2',
            'parent' => '0',
            'name' => 'Calculated simple',
            'questiontext' => '<p>What is {={a}} + {={b}} ?</p>',
            'questiontextformat' => '1',
            'generalfeedback' => '',
            'generalfeedbackformat' => '1',
            'defaultmark' => '1.0000000',
            'penalty' => '0.1',
            'qtype' => 'calculatedsimple',
            'length' => '1',
            'stamp' => 'tjh238.vledev2.open.ac.uk+110517161007+2Barhu',
            'version' => 'tjh238.vledev2.open.ac.uk+110517161008+Mu6OQu',
            'hidden' => '0',
            'timecreated' => '1305648607',
            'timemodified' => '1305648607',
            'createdby' => '2',
            'modifiedby' => '2',
            'maxmark' => '1.0000000',
            'options' => (object) array(
                'synchronize' => 0,
                'single' => 0,
                'answernumbering' => 'abc',
                'shuffleanswers' => 0,
                'correctfeedback' => '',
                'partiallycorrectfeedback' => '',
                'incorrectfeedback' => '',
                'correctfeedbackformat' => 0,
                'partiallycorrectfeedbackformat' => 0,
                'incorrectfeedbackformat' => 0,
                'answers' => array(
                    23 => (object) array(
                        'id' => '23',
                        'question' => '16',
                        'answer' => '{a} + {b}',
                        'answerformat' => '0',
                        'fraction' => '1.0000000',
                        'feedback' => '<p>Well done!</p>',
                        'feedbackformat' => '1',
                        'tolerance' => '0.01',
                        'tolerancetype' => '1',
                        'correctanswerlength' => '2',
                        'correctanswerformat' => '1',
                    ),
                ),
                'units' => array(
                ),
                'unitgradingtype' => '0',
                'unitpenalty' => '0.1000000',
                'showunits' => '3',
                'unitsleft' => '0',
            ),
            'hints' => array(
            ),
        );
        $qsession = (object) array(
            'id' => '95',
            'attemptid' => '13',
            'questionid' => '16',
            'newest' => '256',
            'newgraded' => '256',
            'sumpenalty' => '0.1000000',
            'manualcomment' => '',
            'manualcommentformat' => '1',
            'flagged' => '0',
        );
        $qstates = array(
            253 => (object) array(
                'id' => '253',
                'attempt' => '13',
                'question' => '16',
                'seq_number' => '0',
                'answer' => 'dataset7-|||||',
                'timestamp' => '1305830650',
                'event' => '0',
                'grade' => '0.0000000',
                'raw_grade' => '0.0000000',
                'penalty' => '0.0000000',
            ),
            256 => (object) array(
                'id' => '256',
                'attempt' => '13',
                'question' => '16',
                'seq_number' => '1',
                'answer' => 'dataset7-|||||',
                'timestamp' => '1305830650',
                'event' => '6',
                'grade' => '0.0000000',
                'raw_grade' => '0.0000000',
                'penalty' => '0.1000000',
            ),
        );
        $this->loader->put_dataset_in_cache($question->id, 7, array('a' => '3', 'b' => '6'));

        $qa = $this->updater->convert_question_attempt($quiz, $attempt, $question, $qsession, $qstates);

        $expectedqa = (object) array(
            'behaviour' => 'adaptive',
            'questionid' => 16,
            'variant' => 7,
            'maxmark' => 1.0000000,
            'minfraction' => 0,
            'maxfraction' => 1,
            'flagged' => 0,
            'questionsummary' => 'What is 3 + 6 ?',
            'rightanswer' => '9',
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
                            '_var_a' => '3', '_var_b' => '6'),
                ),
                1 => (object) array(
                    'sequencenumber' => 1,
                    'state' => 'gradedwrong',
                    'fraction' => 0,
                    'timecreated' => 1305830650,
                    'userid' => 4,
                    'data' => array('answer' => '', '-finish' => 1, '-_try' => 1, '-_rawfraction' => 0),
                ),
            ),
        );

        $this->compare_qas($expectedqa, $qa);
    }

    public function test_calculatedsimple_adaptive_qsession98(): void {
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
            'id' => '16',
            'category' => '2',
            'parent' => '0',
            'name' => 'Calculated simple',
            'questiontext' => '<p>What is {={a}} + {={b}} ?</p>',
            'questiontextformat' => '1',
            'generalfeedback' => '',
            'generalfeedbackformat' => '1',
            'defaultmark' => '1.0000000',
            'penalty' => '0.1',
            'qtype' => 'calculatedsimple',
            'length' => '1',
            'stamp' => 'tjh238.vledev2.open.ac.uk+110517161007+2Barhu',
            'version' => 'tjh238.vledev2.open.ac.uk+110517161008+Mu6OQu',
            'hidden' => '0',
            'timecreated' => '1305648607',
            'timemodified' => '1305648607',
            'createdby' => '2',
            'modifiedby' => '2',
            'maxmark' => '1.0000000',
            'options' => (object) array(
                'synchronize' => 0,
                'single' => 0,
                'answernumbering' => 'abc',
                'shuffleanswers' => 0,
                'correctfeedback' => '',
                'partiallycorrectfeedback' => '',
                'incorrectfeedback' => '',
                'correctfeedbackformat' => 0,
                'partiallycorrectfeedbackformat' => 0,
                'incorrectfeedbackformat' => 0,
                'answers' => array(
                    23 => (object) array(
                        'id' => '23',
                        'question' => '16',
                        'answer' => '{a} + {b}',
                        'answerformat' => '0',
                        'fraction' => '1.0000000',
                        'feedback' => '<p>Well done!</p>',
                        'feedbackformat' => '1',
                        'tolerance' => '0.01',
                        'tolerancetype' => '1',
                        'correctanswerlength' => '2',
                        'correctanswerformat' => '1',
                    ),
                ),
                'units' => array(
                ),
                'unitgradingtype' => '0',
                'unitpenalty' => '0.1000000',
                'showunits' => '3',
                'unitsleft' => '0',
            ),
            'hints' => array(
            ),
        );
        $qsession = (object) array(
            'id' => '98',
            'attemptid' => '14',
            'questionid' => '16',
            'newest' => '267',
            'newgraded' => '267',
            'sumpenalty' => '0.3000000',
            'manualcomment' => '',
            'manualcommentformat' => '1',
            'flagged' => '0',
        );
        $qstates = array(
            259 => (object) array(
                'id' => '259',
                'attempt' => '14',
                'question' => '16',
                'seq_number' => '0',
                'answer' => 'dataset4-|||||',
                'timestamp' => '1305830661',
                'event' => '0',
                'grade' => '0.0000000',
                'raw_grade' => '0.0000000',
                'penalty' => '0.0000000',
            ),
            262 => (object) array(
                'id' => '262',
                'attempt' => '14',
                'question' => '16',
                'seq_number' => '1',
                'answer' => 'dataset4-9.00|||||',
                'timestamp' => '1305830668',
                'event' => '3',
                'grade' => '0.0000000',
                'raw_grade' => '0.0000000',
                'penalty' => '0.1000000',
            ),
            263 => (object) array(
                'id' => '263',
                'attempt' => '14',
                'question' => '16',
                'seq_number' => '2',
                'answer' => 'dataset4-15.40|||||',
                'timestamp' => '1305830679',
                'event' => '3',
                'grade' => '0.9000000',
                'raw_grade' => '1.0000000',
                'penalty' => '0.1000000',
            ),
            267 => (object) array(
                'id' => '267',
                'attempt' => '14',
                'question' => '16',
                'seq_number' => '3',
                'answer' => 'dataset4-15.40|||||',
                'timestamp' => '1305830679',
                'event' => '6',
                'grade' => '0.9000000',
                'raw_grade' => '1.0000000',
                'penalty' => '0.1000000',
            ),
        );
        $this->loader->put_dataset_in_cache($question->id, 4, array('a' => '6.4', 'b' => '9'));

        $qa = $this->updater->convert_question_attempt($quiz, $attempt, $question, $qsession, $qstates);

        $expectedqa = (object) array(
            'behaviour' => 'adaptive',
            'questionid' => 16,
            'variant' => 4,
            'maxmark' => 1.0000000,
            'minfraction' => 0,
            'maxfraction' => 1,
            'flagged' => 0,
            'questionsummary' => 'What is 6.4 + 9 ?',
            'rightanswer' => '15.4',
            'responsesummary' => '15.40',
            'timemodified' => 1305830679,
            'steps' => array(
                0 => (object) array(
                    'sequencenumber' => 0,
                    'state' => 'todo',
                    'fraction' => null,
                    'timecreated' => 1305830661,
                    'userid' => 4,
                    'data' => array('_separators' => '.$,',
                            '_var_a' => '6.4', '_var_b' => '9'),
                ),
                1 => (object) array(
                    'sequencenumber' => 1,
                    'state' => 'todo',
                    'fraction' => 0,
                    'timecreated' => 1305830668,
                    'userid' => 4,
                    'data' => array('answer' => '9.00', '-submit' => 1, '-_try' => 1, '-_rawfraction' => 0),
                ),
                2 => (object) array(
                    'sequencenumber' => 2,
                    'state' => 'todo',
                    'fraction' => 0.9,
                    'timecreated' => 1305830679,
                    'userid' => 4,
                    'data' => array('answer' => '15.40', '-submit' => 1, '-_try' => 2, '-_rawfraction' => 1),
                ),
                3 => (object) array(
                    'sequencenumber' => 3,
                    'state' => 'gradedright',
                    'fraction' => 0.9,
                    'timecreated' => 1305830679,
                    'userid' => 4,
                    'data' => array('answer' => '15.40', '-finish' => 1, '-_try' => 2, '-_rawfraction' => 1),
                ),
            ),
        );

        $this->compare_qas($expectedqa, $qa);
    }

    public function test_calculatedsimple_adaptive_qsession101(): void {
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
            'id' => '16',
            'category' => '2',
            'parent' => '0',
            'name' => 'Calculated simple',
            'questiontext' => '<p>What is {={a}} + {={b}} ?</p>',
            'questiontextformat' => '1',
            'generalfeedback' => '',
            'generalfeedbackformat' => '1',
            'defaultmark' => '1.0000000',
            'penalty' => '0.1',
            'qtype' => 'calculatedsimple',
            'length' => '1',
            'stamp' => 'tjh238.vledev2.open.ac.uk+110517161007+2Barhu',
            'version' => 'tjh238.vledev2.open.ac.uk+110517161008+Mu6OQu',
            'hidden' => '0',
            'timecreated' => '1305648607',
            'timemodified' => '1305648607',
            'createdby' => '2',
            'modifiedby' => '2',
            'maxmark' => '1.0000000',
            'options' => (object) array(
                'synchronize' => 0,
                'single' => 0,
                'answernumbering' => 'abc',
                'shuffleanswers' => 0,
                'correctfeedback' => '',
                'partiallycorrectfeedback' => '',
                'incorrectfeedback' => '',
                'correctfeedbackformat' => 0,
                'partiallycorrectfeedbackformat' => 0,
                'incorrectfeedbackformat' => 0,
                'answers' => array(
                    23 => (object) array(
                        'id' => '23',
                        'question' => '16',
                        'answer' => '{a} + {b}',
                        'answerformat' => '0',
                        'fraction' => '1.0000000',
                        'feedback' => '<p>Well done!</p>',
                        'feedbackformat' => '1',
                        'tolerance' => '0.01',
                        'tolerancetype' => '1',
                        'correctanswerlength' => '2',
                        'correctanswerformat' => '1',
                    ),
                ),
                'units' => array(
                ),
                'unitgradingtype' => '0',
                'unitpenalty' => '0.1000000',
                'showunits' => '3',
                'unitsleft' => '0',
            ),
            'hints' => array(
            ),
        );
        $qsession = (object) array(
            'id' => '101',
            'attemptid' => '15',
            'questionid' => '16',
            'newest' => '273',
            'newgraded' => '270',
            'sumpenalty' => '0.0000000',
            'manualcomment' => '',
            'manualcommentformat' => '1',
            'flagged' => '0',
        );
        $qstates = array(
            270 => (object) array(
                'id' => '270',
                'attempt' => '15',
                'question' => '16',
                'seq_number' => '0',
                'answer' => 'dataset6-|||||',
                'timestamp' => '1305830744',
                'event' => '0',
                'grade' => '0.0000000',
                'raw_grade' => '0.0000000',
                'penalty' => '0.0000000',
            ),
            273 => (object) array(
                'id' => '273',
                'attempt' => '15',
                'question' => '16',
                'seq_number' => '1',
                'answer' => 'dataset6-13.1|||||',
                'timestamp' => '1305830755',
                'event' => '2',
                'grade' => '0.0000000',
                'raw_grade' => '1.0000000',
                'penalty' => '0.1000000',
            ),
        );
        $this->loader->put_dataset_in_cache($question->id, 6, array('a' => '6.1', 'b' => '7'));

        $qa = $this->updater->convert_question_attempt($quiz, $attempt, $question, $qsession, $qstates);

        $expectedqa = (object) array(
            'behaviour' => 'adaptive',
            'questionid' => 16,
            'variant' => 6,
            'maxmark' => 1.0000000,
            'minfraction' => 0,
            'maxfraction' => 1,
            'flagged' => 0,
            'questionsummary' => 'What is 6.1 + 7 ?',
            'rightanswer' => '13.1',
            'responsesummary' => '13.1',
            'timemodified' => 1305830755,
            'steps' => array(
                0 => (object) array(
                    'sequencenumber' => 0,
                    'state' => 'todo',
                    'fraction' => null,
                    'timecreated' => 1305830744,
                    'userid' => 3,
                    'data' => array('_separators' => '.$,',
                            '_var_a' => '6.1', '_var_b' => '7'),
                ),
                1 => (object) array(
                    'sequencenumber' => 1,
                    'state' => 'complete',
                    'fraction' => null,
                    'timecreated' => 1305830755,
                    'userid' => 3,
                    'data' => array('answer' => 13.1),
                ),
            ),
        );

        $this->compare_qas($expectedqa, $qa);
    }
}

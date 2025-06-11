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

namespace qtype_randomsamatch;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/upgrade/tests/helper.php');


/**
 * Testing the upgrade of randomsamatch question attempts.
 *
 * @package    qtype_randomsamatch
 * @copyright  2013 Jean-Michel Vedrine
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class upgrade_old_attempt_data_test extends \question_attempt_upgrader_test_base {
    public function test_randomsamatch_deferredfeedback_qsession1(): void {
        $quiz = (object) array(
            'id' => '1',
            'course' => '2',
            'name' => 'random short answer matching deferred quiz',
            'intro' => '<p>To test random shortanswer matching questions.</p>',
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
            'questions' => '5,0',
            'sumgrades' => '1.00000',
            'grade' => '100.00000',
            'timecreated' => '0',
            'timemodified' => '1368446711',
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
            'id' => '1',
            'uniqueid' => '1',
            'quiz' => '1',
            'userid' => '3',
            'attempt' => '1',
            'sumgrades' => '0.66667',
            'timestart' => '1368446755',
            'timefinish' => '1368446789',
            'timemodified' => '1368446789',
            'layout' => '5,0',
            'preview' => '0',
            'needsupgradetonewqe' => 1,
        );
        $question = (object) array(
            'id' => '5',
            'category' => '1',
            'parent' => '0',
            'name' => 'Random shortanswer matching question animals',
            'questiontext' => 'For each of the following questions, select the matching answer from the menu.',
            'questiontextformat' => '1',
            'generalfeedback' => '',
            'generalfeedbackformat' => '1',
            'penalty' => '0.1000000',
            'qtype' => 'randomsamatch',
            'length' => '1',
            'stamp' => 'localhost+130513115611+72Efbk',
            'version' => 'localhost+130513115611+0REXHW',
            'hidden' => '0',
            'timecreated' => '1368446171',
            'timemodified' => '1368446171',
            'createdby' => '2',
            'modifiedby' => '2',
            'maxmark' => '1.0000000',
            'options' => (object) array(
                'id' => '1',
                'question' => '5',
                'choose' => '3',
                'subcats' => 1,
            ),
            'defaultmark' => '1.0000000',
        );
        $qsession = (object) array(
            'id' => '1',
            'attemptid' => '1',
            'questionid' => '5',
            'newest' => '3',
            'newgraded' => '3',
            'sumpenalty' => '0.1000000',
            'manualcomment' => '',
            'manualcommentformat' => '1',
            'flagged' => '0',
        );
        $qstates = array(
            1 => (object) array(
                'id' => '1',
                'attempt' => '1',
                'question' => '5',
                'seq_number' => '0',
                'answer' => '2-0,3-0,6-0',
                'timestamp' => '1368446755',
                'event' => '0',
                'grade' => '0.0000000',
                'raw_grade' => '0.0000000',
                'penalty' => '0.0000000',
            ),
            2 => (object) array(
                'id' => '2',
                'attempt' => '1',
                'question' => '5',
                'seq_number' => '1',
                'answer' => '2-3,3-5,6-3',
                'timestamp' => '1368446783',
                'event' => '2',
                'grade' => '0.0000000',
                'raw_grade' => '0.6666667',
                'penalty' => '0.1000000',
            ),
            3 => (object) array(
                'id' => '3',
                'attempt' => '1',
                'question' => '5',
                'seq_number' => '2',
                'answer' => '2-3,3-5,6-3',
                'timestamp' => '1368446783',
                'event' => '6',
                'grade' => '0.6666667',
                'raw_grade' => '0.6666667',
                'penalty' => '0.1000000',
            ),
        );
        $sa1 = (object) array(
            'id' => '2',
            'category' => '1',
            'parent' => '0',
            'name' => 'animal 1',
            'questiontext' => 'Dog',
            'questiontextformat' => '1',
            'defaultmark' => '1',
            'penalty' => '0.1',
            'qtype' => 'shortanswer',
            'length' => '1',
            'stamp' => 'localhost+090227173002+mbdE0X',
            'version' => 'localhost+090304190917+xAB5Nf',
            'hidden' => '0',
            'generalfeedback' => '',
            'generalfeedbackformat' => '1',
            'timecreated' => '1235755802',
            'timemodified' => '1236193757',
            'createdby' => '25299',
            'modifiedby' => '25299',
            'unlimited' => '0',
            'options' => (object) array(
                'id' => '15211',
                'question' => '2',
                'layout' => '0',
                'answers' => array(
                    7 => (object) array(
                        'question' => '2',
                        'answer' => 'Amphibian',
                        'fraction' => '0',
                        'feedback' => '',
                        'id' => 7,
                    ),
                    3 => (object) array(
                        'question' => '2',
                        'answer' => 'Mammal',
                        'fraction' => '1',
                        'feedback' => '',
                        'id' => 3,
                    ),
                    22 => (object) array(
                        'question' => '2',
                        'answer' => '*',
                        'fraction' => '0',
                        'feedback' => '',
                        'id' => 22,
                    ),
                ),
                'single' => '1',
                'shuffleanswers' => '1',
                'correctfeedback' => 'Your answer is correct. Well done.',
                'partiallycorrectfeedback' => '',
                'incorrectfeedback' => 'Your answer is incorrect. The correct answer is: Mammal.',
                'answernumbering' => 'abc',
            ),
        );

        $sa2 = (object) array(
            'id' => '3',
            'category' => '1',
            'parent' => '0',
            'name' => 'animal 2',
            'questiontext' => 'Frog',
            'questiontextformat' => '1',
            'defaultmark' => '1',
            'penalty' => '0.1',
            'qtype' => 'shortanswer',
            'length' => '1',
            'stamp' => 'localhost+090227173002+mbdE0X',
            'version' => 'localhost+090304190917+xAB5Nf',
            'hidden' => '0',
            'generalfeedback' => '',
            'generalfeedbackformat' => '1',
            'timecreated' => '1235755802',
            'timemodified' => '1236193757',
            'createdby' => '25299',
            'modifiedby' => '25299',
            'unlimited' => '0',
            'options' => (object) array(
                'id' => '15214',
                'question' => '3',
                'layout' => '0',
                'answers' => array(
                    5 => (object) array(
                        'question' => '3',
                        'answer' => 'Amphibian',
                        'fraction' => '1',
                        'feedback' => '',
                        'id' => 5,
                    ),
                    11 => (object) array(
                        'question' => '3',
                        'answer' => 'Mammal',
                        'fraction' => '0',
                        'feedback' => '',
                        'id' => 11,
                    ),
                    27 => (object) array(
                        'question' => '3',
                        'answer' => '*',
                        'fraction' => '0',
                        'feedback' => '',
                        'id' => 27,
                    ),
                ),
                'single' => '1',
                'shuffleanswers' => '1',
                'correctfeedback' => 'Your answer is correct. Well done.',
                'partiallycorrectfeedback' => '',
                'incorrectfeedback' => 'Your answer is incorrect. The correct answer is: Mammal.',
                'answernumbering' => 'abc',
            ),
        );

        $sa3 = (object) array(
            'id' => '6',
            'category' => '1',
            'parent' => '0',
            'name' => 'animal 3',
            'questiontext' => 'Toad',
            'questiontextformat' => '1',
            'defaultmark' => '1',
            'penalty' => '0.1',
            'qtype' => 'shortanswer',
            'length' => '1',
            'stamp' => 'localhost+090227173002+mbdE0X',
            'version' => 'localhost+090304190917+xAB5Nf',
            'hidden' => '0',
            'generalfeedback' => '',
            'generalfeedbackformat' => '1',
            'timecreated' => '1235755802',
            'timemodified' => '1236193757',
            'createdby' => '25299',
            'modifiedby' => '25299',
            'unlimited' => '0',
            'options' => (object) array(
                'id' => '4578',
                'question' => '6',
                'layout' => '0',
                'answers' => array(
                    9 => (object) array(
                        'question' => '6',
                        'answer' => 'Amphibian',
                        'fraction' => '1',
                        'feedback' => '',
                        'id' => 9,
                    ),
                    18 => (object) array(
                        'question' => '6',
                        'answer' => 'Mammal',
                        'fraction' => '0',
                        'feedback' => '',
                        'id' => 18,
                    ),
                    32 => (object) array(
                        'question' => '6',
                        'answer' => '*',
                        'fraction' => '0',
                        'feedback' => '',
                        'id' => 32,
                    ),
                ),
                'single' => '1',
                'shuffleanswers' => '1',
                'correctfeedback' => 'Your answer is correct. Well done.',
                'partiallycorrectfeedback' => '',
                'incorrectfeedback' => 'Your answer is incorrect. The correct answer is: Mammal.',
                'answernumbering' => 'abc',
            ),
        );

        $this->loader->put_question_in_cache($sa2);
        $this->loader->put_question_in_cache($sa1);
        $this->loader->put_question_in_cache($sa3);
        $qa = $this->updater->convert_question_attempt($quiz, $attempt, $question, $qsession, $qstates);

        $expectedqa = (object) array(
            'behaviour' => 'deferredfeedback',
            'questionid' => 5,
            'variant' => 1,
            'maxmark' => 1.0000000,
            'minfraction' => 0,
            'maxfraction' => 1,
            'flagged' => 0,
            'questionsummary' => 'For each of the following questions, select the matching answer from the menu.{Dog;Frog;Toad}->{Mammal;Amphibian}',
            'rightanswer' => 'Dog -> Mammal; Frog -> Amphibian; Toad -> Amphibian',
            'responsesummary' => 'Dog->Mammal;Frog->Amphibian;Toad->Mammal',
            'timemodified' => 1368446783,
            'steps' => array(
                0 => (object) array(
                    'sequencenumber' => 0,
                    'state' => 'todo',
                    'fraction' => null,
                    'timecreated' => 1368446755,
                    'userid' => 3,
                    'data' => array(
                        '_choice_3' => 'Mammal',
                        '_stem_2' => 'Dog',
                        '_stemformat_2' => '1',
                        '_right_2' => 3,
                        '_choice_5' => 'Amphibian',
                        '_stem_3' => 'Frog',
                        '_stemformat_3' => '1',
                        '_right_3' => 5,
                        '_stem_6' => 'Toad',
                        '_stemformat_6' => '1',
                        '_right_6' => 5,
                        '_stemorder' => '2,3,6',
                        '_choiceorder' => '3,5',
                    ),
                ),
                1 => (object) array(
                    'sequencenumber' => 1,
                    'state' => 'complete',
                    'fraction' => null,
                    'timecreated' => 1368446783,
                    'userid' => 3,
                    'data' => array(
                        'sub0' => 1,
                        'sub1' => 2,
                        'sub2' => 1,
                    ),
                ),
                2 => (object) array(
                    'sequencenumber' => 2,
                    'state' => 'gradedpartial',
                    'fraction' => 0.6666667,
                    'timecreated' => 1368446783,
                    'userid' => 3,
                    'data' => array(
                        'sub0' => 1,
                        'sub1' => 2,
                        'sub2' => 1,
                        '-finish' => 1,

                    ),
                ),
            ),
        );

        $this->compare_qas($expectedqa, $qa);
    }
}

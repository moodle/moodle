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
 * @subpackage truefalse
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/engine/upgrade/simpletest/helper.php');


/**
 * Testing the upgrade of truefalse question attempts.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_truefalse_attempt_upgrader_test extends question_attempt_upgrader_test_base {

    public function test_truefalse_deferredfeedback_history620() {
        $quiz = (object) array(
            'id' => '203',
            'course' => '2359',
            'name' => 'Quiz 1',
            'intro' => '',
            'introformat' => FORMAT_HTML,
            'timeopen' => '0',
            'timeclose' => '0',
            'preferredbehaviour' => 'deferredfeedback',
            'attempts' => '0',
            'attemptonlast' => '1',
            'grademethod' => '1',
            'decimalpoints' => '2',
            'review' => '71760879',
            'questionsperpage' => '2',
            'questiondecimalpoints' => '-1',
            'showuserpicture' => '1',
            'showblocks' => '1',
            'shufflequestions' => '0',
            'shuffleanswers' => '0',
            'questions' => '3859,3860,0,3861,3862,0,3863,3864,0,3865,3866,0,3867,3868,0',
            'sumgrades' => '50',
            'grade' => '50',
            'timecreated' => '0',
            'timemodified' => '1176461532',
            'password' => '',
            'subnet' => '',
            'popup' => '0',
            'delay1' => '0',
            'delay2' => '0',
            'timelimit' => '0',
        );
        $attempt = (object) array(
            'id' => '3795',
            'uniqueid' => '3795',
            'quiz' => '203',
            'userid' => '1888',
            'attempt' => '1',
            'sumgrades' => '40',
            'timestart' => '1177841172',
            'timefinish' => '1177841409',
            'timemodified' => '1177841394',
            'layout' => '3859,3860,0,3861,3862,0,3863,3864,0,3865,3866,0,3867,3868,0',
            'preview' => '0',
            'needsupgradetonewqe' => '1',
        );
        $question = (object) array(
            'id' => '3865',
            'category' => '187',
            'parent' => '0',
            'name' => 'Question 7',
            'questiontext' => '<p>The term ‘integration server’ is another name for an application server, true or false?</p>',
            'questiontextformat' => '1',
            'defaultmark' => '1',
            'penalty' => '0',
            'qtype' => 'truefalse',
            'length' => '1',
            'stamp' => 'learn.open.ac.uk+070404143040+oLimmG',
            'version' => 'learn.open.ac.uk+070405112705+DLhORU',
            'hidden' => '0',
            'generalfeedback' => '<p></p>',
            'generalfeedbackformat' => '1',
            'timecreated' => '0',
            'timemodified' => '0',
            'createdby' => null,
            'modifiedby' => null,
            'unlimited' => null,
            'maxmark' => '5',
            'options' => (object) array(
                'id' => '98',
                'question' => '3865',
                'trueanswer' => '11693',
                'falseanswer' => '11694',
                'answers' => array(
                    11693 => (object) array(
                        'question' => '3865',
                        'answer' => 'True',
                        'fraction' => '0',
                        'feedback' => '',
                        'id' => 11693,
                    ),
                    11694 => (object) array(
                        'question' => '3865',
                        'answer' => 'False',
                        'fraction' => '1',
                        'feedback' => '',
                        'id' => 11694,
                    ),
                ),
            ),
            'hints' => false,
        );
        $qsession = (object) array(
            'id' => '35137',
            'attemptid' => '3795',
            'questionid' => '3865',
            'newest' => '84791',
            'newgraded' => '84791',
            'sumpenalty' => '0',
            'manualcomment' => '',
            'manualcommentformat' => '1',
            'flagged' => '1',
        );
        $qstates = array(
            84771 => (object) array(
                'attempt' => '3795',
                'question' => '3865',
                'originalquestion' => '0',
                'seq_number' => '0',
                'answer' => '',
                'timestamp' => '1177841172',
                'event' => '0',
                'grade' => '0',
                'raw_grade' => '0',
                'penalty' => '0',
                'id' => 84771,
            ),
            84785 => (object) array(
                'attempt' => '3795',
                'question' => '3865',
                'originalquestion' => '0',
                'seq_number' => '1',
                'answer' => '11694',
                'timestamp' => '1177841361',
                'event' => '2',
                'grade' => '0',
                'raw_grade' => '5',
                'penalty' => '5',
                'id' => 84785,
            ),
            84791 => (object) array(
                'attempt' => '3795',
                'question' => '3865',
                'originalquestion' => '0',
                'seq_number' => '2',
                'answer' => '11694',
                'timestamp' => '1177841361',
                'event' => '6',
                'grade' => '5',
                'raw_grade' => '5',
                'penalty' => '5',
                'id' => 84791,
            ),
        );

        $qa = $this->updater->convert_question_attempt($quiz, $attempt, $question, $qsession, $qstates);

        $expectedqa = (object) array(
            'behaviour' => 'deferredfeedback',
            'questionid' => 3865,
            'maxmark' => 5,
            'minfraction' => 0,
            'flagged' => 0,
            'questionsummary' => 'The term ‘integration server’ is another name for an application server, true or false?',
            'rightanswer' => 'False',
            'responsesummary' => 'False',
            'timemodified' => 1177841361,
            'steps' => array(
                0 => (object) array(
                    'sequencenumber' => 0,
                    'state' => 'todo',
                    'fraction' => null,
                    'timecreated' => 1177841172,
                    'userid' => 1888,
                    'data' => array(),
                ),
                1 => (object) array(
                    'sequencenumber' => 1,
                    'state' => 'complete',
                    'fraction' => null,
                    'timecreated' => 1177841361,
                    'userid' => 1888,
                    'data' => array('answer' => 0),
                ),
                2 => (object) array(
                    'sequencenumber' => 2,
                    'state' => 'gradedright',
                    'fraction' => 1,
                    'timecreated' => 1177841361,
                    'userid' => 1888,
                    'data' => array('answer' => 0, '-finish' => 1),
                ),
            ),
        );

        $this->assertEqual($expectedqa, $qa);
    }

    public function test_truefalse_deferredfeedback_history20() {
        $quiz = (object) array(
            'id' => '551',
            'course' => '2828',
            'name' => 'Unit 4 Quiz',
            'intro' => '',
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
            'review' => '71760879',
            'questionsperpage' => '1',
            'shufflequestions' => '0',
            'shuffleanswers' => '1',
            'questions' => '9043,0,9057,0,9062,0,9241,0',
            'sumgrades' => '4',
            'grade' => '4',
            'timecreated' => '0',
            'timemodified' => '1190277883',
            'password' => '',
            'subnet' => '',
            'popup' => '0',
            'delay1' => '0',
            'delay2' => '0',
            'timelimit' => '0',
        );
        $attempt = (object) array(
            'id' => '23226',
            'uniqueid' => '23226',
            'quiz' => '551',
            'userid' => '80300',
            'attempt' => '2',
            'sumgrades' => '0',
            'timestart' => '1200326384',
            'timefinish' => '0',
            'timemodified' => '1200326384',
            'layout' => '9043,0,9057,0,9062,0,9241,0',
            'preview' => '0',
            'needsupgradetonewqe' => '1',
        );
        $question = (object) array(
            'id' => '9062',
            'category' => '481',
            'parent' => '0',
            'name' => 'U04_NA_In viaggio_Q3',
            'questiontext' => '<p><img title="my market" height="336" alt="my market" hspace="0" src="http://learnacct.open.ac.uk/file.php/2828/Naples_My_market.jpg" /></p>
        <p>What can you buy in this shop? Is this list accurate?</p>
        <p><br />Mark true or false (for the list as a whole).</p> 
 
 
        <p><i>single tickets</i></p> 
        <p><i>weekly season tickets</i></p> 
        <p><i>monthly season tickets</i></p> 
        <p><i>wine and grappa</i></p> 
        <p><i>fruit and vegetables</i></p> 
        <p><i>tobacco </i></p>',
            'questiontextformat' => '1',
            'defaultmark' => '1',
            'penalty' => '0',
            'qtype' => 'truefalse',
            'length' => '1',
            'stamp' => 'learn.open.ac.uk+070820163735+PqUlDM',
            'version' => 'learn.open.ac.uk+080304160318+owhQUb',
            'hidden' => '0',
            'generalfeedback' => '',
            'generalfeedbackformat' => '1',
            'timecreated' => '0',
            'timemodified' => '1204646598',
            'createdby' => null,
            'modifiedby' => '97230',
            'unlimited' => '0',
            'maxmark' => '1',
            'options' => (object) array(
                'id' => '199',
                'question' => '9062',
                'trueanswer' => '28221',
                'falseanswer' => '28222',
                'answers' => array(
                    28221 => (object) array(
                        'question' => '9062',
                        'answer' => 'True',
                        'fraction' => '0',
                        'feedback' => '<p>The correct answer is \'false\'.  The only items on the list not sold at My Market are tobacco and weekly season tickets. It sells everything else! <br /><em>Biglietto unico</em> is a single ticket for bus, funicular railway or metro (underground) while <em>abbonamento mensile</em> is a monthly season ticket.<br />The shop also sells wine and grappa, as you can see them in the window, and fruit and vegetables as it says in the sign. Small shops in Naples often sell a variety of things, not always connected! </p>',
                        'id' => 28221,
                    ),
                    28222 => (object) array(
                        'question' => '9062',
                        'answer' => 'False',
                        'fraction' => '1',
                        'feedback' => 'The correct anstwer is \'false\'. The only items on the list not sold at My Market are tobacco and weekly season tickets. It sells everything else! <em>Biglietto</em> <em>unico</em> is a single ticket for bus, funicular railway or metro (underground) while \'abbonamento mensile\' is a monthly season ticket. It also sells wine and grappa, as you can see them in the window, and fruit and vegetables as it says in the sign! Small shops in Naples often sell a variety of things, not always connected! ',
                        'id' => 28222,
                    ),
                ),
            ),
            'hints' => false,
        );
        $qsession = (object) array(
            'id' => '351032',
            'attemptid' => '23226',
            'questionid' => '9062',
            'newest' => '848428',
            'newgraded' => '848426',
            'sumpenalty' => '0',
            'manualcomment' => '',
            'manualcommentformat' => '1',
            'flagged' => '1',
        );
        $qstates = array(
            848426 => (object) array(
                'attempt' => '23226',
                'question' => '9062',
                'originalquestion' => '0',
                'seq_number' => '0',
                'answer' => '',
                'timestamp' => '1200326384',
                'event' => '0',
                'grade' => '0',
                'raw_grade' => '0',
                'penalty' => '0',
                'id' => 848426,
            ),
            848428 => (object) array(
                'attempt' => '23226',
                'question' => '9062',
                'originalquestion' => '0',
                'seq_number' => '1',
                'answer' => '28221',
                'timestamp' => '1200326384',
                'event' => '2',
                'grade' => '0',
                'raw_grade' => '0',
                'penalty' => '1',
                'id' => 848428,
            ),
        );

        $qa = $this->updater->convert_question_attempt($quiz, $attempt, $question, $qsession, $qstates);

        $expectedqa = (object) array(
            'behaviour' => 'deferredfeedback',
            'questionid' => 9062,
            'maxmark' => 1,
            'minfraction' => 0,
            'flagged' => 0,
            'questionsummary' => "[my market] \n\nWhat can you buy in this shop? Is this list accurate? \n\nMark true or false (for the list as a whole). \n\n_single tickets_ \n\n_weekly season tickets_ \n\n_monthly season tickets_ \n\n_wine and grappa_ \n\n_fruit and vegetables_ \n\n_tobacco _",
            'rightanswer' => 'False',
            'responsesummary' => 'True',
            'timemodified' => 1200326384,
            'steps' => array(
                0 => (object) array(
                    'sequencenumber' => 0,
                    'state' => 'todo',
                    'fraction' => null,
                    'timecreated' => 1200326384,
                    'userid' => 80300,
                    'data' => array(),
                ),
                1 => (object) array(
                    'sequencenumber' => 1,
                    'state' => 'complete',
                    'fraction' => null,
                    'timecreated' => 1200326384,
                    'userid' => 80300,
                    'data' => array('answer' => 1),
                ),
            ),
        );

        $this->assertEqual($expectedqa, $qa);
    }

    public function test_truefalse_deferredfeedback_history90() {
        $quiz = (object) array(
            'id' => '3',
            'course' => '1095',
            'name' => 'Introduction quiz',
            'intro' => 'Use this self-assessment quiz after you have read the course introduction. ',
            'introformat' => FORMAT_HTML,
            'questiondecimalpoints' => '-1',
            'showuserpicture' => '1',
            'showblocks' => '1',
            'timeopen' => '1150107000',
            'timeclose' => '0',
            'preferredbehaviour' => 'deferredfeedback',
            'attempts' => '0',
            'attemptonlast' => '0',
            'grademethod' => '1',
            'decimalpoints' => '2',
            'review' => '71760879',
            'questionsperpage' => '0',
            'shufflequestions' => '1',
            'shuffleanswers' => '1',
            'questions' => '96,98,104,106,101,108,111,102,113,0',
            'sumgrades' => '9',
            'grade' => '9',
            'timecreated' => '0',
            'timemodified' => '1150127779',
            'password' => '',
            'subnet' => '',
            'popup' => '0',
            'delay1' => '0',
            'delay2' => '0',
            'timelimit' => '0',
        );
        $attempt = (object) array(
            'id' => '19',
            'uniqueid' => '19',
            'quiz' => '3',
            'userid' => '49542',
            'attempt' => '1',
            'sumgrades' => '9',
            'timestart' => '1150301292',
            'timefinish' => '1150301347',
            'timemodified' => '1150454872',
            'layout' => '96,108,102,101,106,113,104,98,111,0',
            'preview' => '0',
            'needsupgradetonewqe' => '1',
        );
        $question = (object) array(
            'id' => '111',
            'category' => '5',
            'parent' => '0',
            'name' => 'Q7',
            'questiontext' => 'Web services, integration servers, XML, application servers, message-oriented middleware and remote procedure call can be used to enable integrated systems?',
            'questiontextformat' => '1',
            'defaultgrade' => '1',
            'penalty' => '0',
            'qtype' => 'truefalse',
            'length' => '1',
            'stamp' => 'learn.open.ac.uk+060612113518+uuFWow',
            'version' => 'learn.open.ac.uk+060612154736+HeFOV0',
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
                'id' => '24',
                'question' => '111',
                'trueanswer' => '312',
                'falseanswer' => '313',
                'answers' => array(
                    312 => (object) array(
                        'question' => '111',
                        'answer' => 'True',
                        'fraction' => '1',
                        'feedback' => 'They are all used in storing data or connecting together components of an integrated system.',
                        'id' => 312,
                    ),
                    313 => (object) array(
                        'question' => '111',
                        'answer' => 'False',
                        'fraction' => '0',
                        'feedback' => 'They are all used in storing data or connecting together components of an integrated system.',
                        'id' => 313,
                    ),
                ),
            ),
            'hints' => false,
        );
        $qsession = (object) array(
            'id' => '169',
            'attemptid' => '19',
            'questionid' => '111',
            'newest' => '252',
            'newgraded' => '252',
            'sumpenalty' => '0',
            'manualcomment' => '',
            'manualcommentformat' => '1',
            'flagged' => '1',
        );
        $qstates = array(
            242 => (object) array(
                'attempt' => '19',
                'question' => '111',
                'originalquestion' => '0',
                'seq_number' => '0',
                'answer' => '',
                'timestamp' => '1150301292',
                'event' => '0',
                'grade' => '0',
                'raw_grade' => '0',
                'penalty' => '0',
                'id' => 242,
            ),
            252 => (object) array(
                'attempt' => '19',
                'question' => '111',
                'originalquestion' => '0',
                'seq_number' => '1',
                'answer' => '',
                'timestamp' => '1150454872',
                'event' => '9',
                'grade' => '1',
                'raw_grade' => '1',
                'penalty' => '0',
                'id' => 252,
            ),
        );

        $qa = $this->updater->convert_question_attempt($quiz, $attempt, $question, $qsession, $qstates);

        $expectedqa = (object) array(
            'behaviour' => 'deferredfeedback',
            'questionid' => 111,
            'maxmark' => 1,
            'minfraction' => 0,
            'flagged' => 0,
            'questionsummary' => 'Web services, integration servers, XML, application servers, message-oriented middleware and remote procedure call can be used to enable integrated systems?',
            'rightanswer' => 'True',
            'responsesummary' => '',
            'timemodified' => 1150454872,
            'steps' => array(
                0 => (object) array(
                    'sequencenumber' => 0,
                    'state' => 'todo',
                    'fraction' => null,
                    'timecreated' => 1150301292,
                    'userid' => 49542,
                    'data' => array(),
                ),
                1 => (object) array(
                    'sequencenumber' => 1,
                    'state' => 'gradedwrong',
                    'fraction' => null,
                    'timecreated' => 1150454872,
                    'userid' => 49542,
                    'data' => array('-finish' => 1),
                ),
                2 => (object) array(
                    'sequencenumber' => 2,
                    'state' => 'mangrright',
                    'fraction' => 1,
                    'timecreated' => 1150454872,
                    'userid' => null,
                    'data' => array('-comment' => '', '-maxmark' => 1, '-mark' => 1),
                ),
            ),
        );

        $this->assertEqual($expectedqa, $qa);
    }
}

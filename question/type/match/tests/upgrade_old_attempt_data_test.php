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

namespace qtype_match;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/upgrade/tests/helper.php');


/**
 * Testing the upgrade of match question attempts.
 *
 * @package   qtype_match
 * @copyright 2009 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class upgrade_old_attempt_data_test extends \question_attempt_upgrader_test_base {

    public function test_match_deferredfeedback_history6220(): void {
        $quiz = (object) array(
            'id' => '72',
            'course' => '1181',
            'name' => 'Study Guide 4 Quiz',
            'intro' => '',
            'introformat' => FORMAT_HTML,
            'questiondecimalpoints' => '-1',
            'showuserpicture' => '1',
            'showblocks' => '1',
            'timeopen' => '0',
            'timeclose' => '0',
            'preferredbehaviour' => 'deferredfeedback',
            'attempts' => '0',
            'attemptonlast' => '1',
            'grademethod' => '1',
            'decimalpoints' => '2',
            'review' => '71760879',
            'questionsperpage' => '2',
            'shufflequestions' => '0',
            'shuffleanswers' => '0',
            'sumgrades' => '48',
            'grade' => '48',
            'timecreated' => '0',
            'timemodified' => '1170427370',
            'password' => '',
            'subnet' => '',
            'popup' => '0',
            'delay1' => '0',
            'delay2' => '0',
            'timelimit' => '0',
        );
        $attempt = (object) array(
            'id' => '3562',
            'uniqueid' => '3562',
            'quiz' => '72',
            'userid' => '91483',
            'attempt' => '6',
            'sumgrades' => '43',
            'timestart' => '1177419915',
            'timefinish' => '1177419962',
            'timemodified' => '1168015476',
            'layout' => '689,690,0,691,692,0,693,694,0,695,696,0,697,698,0',
            'preview' => '0',
        );
        $question = (object) array(
            'id' => '695',
            'category' => '65',
            'parent' => '0',
            'name' => 'Question 7',
            'questiontext' => '<p>Associate the appropriate definition with each term.</p>',
            'questiontextformat' => '1',
            'defaultmark' => '1',
            'penalty' => '0',
            'qtype' => 'match',
            'length' => '1',
            'stamp' => 'learn.open.ac.uk+061123110024+a0RsuG',
            'version' => 'learn.open.ac.uk+061123163015+Oe63zC',
            'hidden' => '0',
            'generalfeedback' => 'For further information about this question see Study Guide 4 SAQ 3.1',
            'generalfeedbackformat' => '1',
            'timecreated' => '0',
            'timemodified' => '0',
            'createdby' => null,
            'modifiedby' => null,
            'unlimited' => null,
            'maxmark' => '3',
            'options' => (object) array(
                'id' => '27',
                'questionid' => '695',
                'subquestions' => array(
                    148 => (object) array(
                        'questionid' => '695',
                        'questiontext' => 'Active adjacent system',
                        'answertext' => 'A system that interacts with or participates in the work.',
                        'id' => 148,
                    ),
                    149 => (object) array(
                        'questionid' => '695',
                        'questiontext' => 'Autonomous adjacent system',
                        'answertext' => 'An external entity that acts independently of the work under study.',
                        'id' => 149,
                    ),
                    150 => (object) array(
                        'questionid' => '695',
                        'questiontext' => 'Cooperative adjacent system',
                        'answertext' => 'A system that is involved in the response to a business event.',
                        'id' => 150,
                    ),
                    151 => (object) array(
                        'questionid' => '695',
                        'questiontext' => '',
                        'answertext' => 'A system which does not supply or receive data from the work.',
                        'id' => 151,
                    ),
                    152 => (object) array(
                        'questionid' => '695',
                        'questiontext' => '',
                        'answertext' => 'An external entity that performs part of the work under study.',
                        'id' => 152,
                    ),
                ),
                'shuffleanswers' => '1',
                'correctfeedback' => '',
                'partiallycorrectfeedback' => '',
                'incorrectfeedback' => '',
                'correctresponsesfeedback' => '0',
            ),
            'hints' => false,
        );
        $qsession = (object) array(
            'id' => '33092',
            'attemptid' => '3562',
            'questionid' => '695',
            'newest' => '79626',
            'newgraded' => '79626',
            'sumpenalty' => '0',
            'manualcomment' => '',
            'manualcommentformat' => '1',
            'flagged' => '1',
        );
        $qstates = array(
            79604 => (object) array(
                'attempt' => '3562',
                'question' => '695',
                'originalquestion' => '0',
                'seq_number' => '0',
                'answer' => '148-0,149-0,150-0,151-0,152-0',
                'timestamp' => '1177419915',
                'event' => '0',
                'grade' => '0',
                'raw_grade' => '0',
                'penalty' => '0',
                'id' => 79604,
            ),
            79614 => (object) array(
                'attempt' => '3562',
                'question' => '695',
                'originalquestion' => '0',
                'seq_number' => '1',
                'answer' => '148-148,149-151,150-152,151-0,152-0',
                'timestamp' => '1177419855',
                'event' => '2',
                'grade' => '0',
                'raw_grade' => '1',
                'penalty' => '0',
                'id' => 79614,
            ),
            79619 => (object) array(
                'attempt' => '3562',
                'question' => '695',
                'originalquestion' => '0',
                'seq_number' => '2',
                'answer' => '148-148,149-149,150-150,151-0,152-0',
                'timestamp' => '1177419956',
                'event' => '2',
                'grade' => '0',
                'raw_grade' => '3',
                'penalty' => '0',
                'id' => 79619,
            ),
            79626 => (object) array(
                'attempt' => '3562',
                'question' => '695',
                'originalquestion' => '0',
                'seq_number' => '3',
                'answer' => '148-148,149-149,150-150,151-0,152-0',
                'timestamp' => '1177419956',
                'event' => '6',
                'grade' => '3',
                'raw_grade' => '3',
                'penalty' => '0',
                'id' => 79626,
            ),
        );

        $qa = $this->updater->convert_question_attempt($quiz, $attempt, $question, $qsession, $qstates);

        $expectedqa = (object) array(
            'behaviour' => 'deferredfeedback',
            'questionid' => 695,
            'variant' => 1,
            'maxmark' => 3,
            'minfraction' => 0,
            'maxfraction' => 1,
            'flagged' => 0,
            'questionsummary' => 'Associate the appropriate definition with each term. ' .
                    '{Active adjacent system; Autonomous adjacent system; ' .
                    'Cooperative adjacent system} -> {A system that interacts with ' .
                    'or participates in the work.; An external entity that acts ' .
                    'independently of the work under study.; A system that is involved ' .
                    'in the response to a business event.; A system which does not supply ' .
                    'or receive data from the work.; An external entity that performs part ' .
                    'of the work under study.}',
            'rightanswer' => 'Active adjacent system -> A system that interacts with ' .
                    'or participates in the work.; Autonomous adjacent system -> ' .
                    'An external entity that acts independently of the work under study.; ' .
                    'Cooperative adjacent system -> A system that is involved in the response ' .
                    'to a business event.',
            'responsesummary' => 'Active adjacent system -> A system that interacts with ' .
                    'or participates in the work.; Autonomous adjacent system -> ' .
                    'An external entity that acts independently of the work under study.; ' .
                    'Cooperative adjacent system -> A system that is involved in the response ' .
                    'to a business event.',
            'timemodified' => 1177419956,
            'steps' => array(
                0 => (object) array(
                    'sequencenumber' => 0,
                    'state' => 'todo',
                    'fraction' => null,
                    'timecreated' => 1177419915,
                    'userid' => 91483,
                    'data' => array('_stemorder' => '148,149,150',
                            '_choiceorder' => 'todo - see below'),
                ),
                1 => (object) array(
                    'sequencenumber' => 1,
                    'state' => 'complete',
                    'fraction' => null,
                    'timecreated' => 1177419855,
                    'userid' => 91483,
                    'data' => array('sub0' => 148, 'sub1' => 151, 'sub2' => 152),
                ),
                2 => (object) array(
                    'sequencenumber' => 2,
                    'state' => 'complete',
                    'fraction' => null,
                    'timecreated' => 1177419956,
                    'userid' => 91483,
                    'data' => array('sub0' => 148, 'sub1' => 149, 'sub2' => 150),
                ),
                3 => (object) array(
                    'sequencenumber' => 3,
                    'state' => 'gradedright',
                    'fraction' => 1,
                    'timecreated' => 1177419956,
                    'userid' => 91483,
                    'data' => array('sub0' => 148, 'sub1' => 149, 'sub2' => 150, '-finish' => 1),
                ),
            ),
        );

        // This is a random thing, so just set expected to actual.
        $expectedqa->steps[0]->data['_choiceorder'] = $qa->steps[0]->data['_choiceorder'];
        $order = explode(',', $qa->steps[0]->data['_choiceorder']);
        $order = array_combine(array_values($order), array_keys($order));
        for ($i = 1; $i <= 3; $i++) {
            for ($sub = 0; $sub < 3; $sub++) {
                $expectedqa->steps[$i]->data['sub' . $sub] =
                        $order[$expectedqa->steps[$i]->data['sub' . $sub]] + 1;
            }
        }

        $this->compare_qas($expectedqa, $qa);
    }

    public function test_match_deferredfeedback_history60(): void {
        $quiz = (object) array(
            'id' => '60',
            'course' => '2304',
            'name' => 'Types of resources available quiz',
            'intro' => 'This quiz covers the different types of information resources available ' .
                    'and how to select which is most appropriate. ',
            'introformat' => FORMAT_HTML,
            'questiondecimalpoints' => '-1',
            'showuserpicture' => '1',
            'showblocks' => '1',
            'timeopen' => '1164153600',
            'timeclose' => '1606003200',
            'preferredbehaviour' => 'deferredfeedback',
            'attempts' => '0',
            'attemptonlast' => '0',
            'grademethod' => '1',
            'decimalpoints' => '3',
            'review' => '71752557',
            'questionsperpage' => '1',
            'shufflequestions' => '0',
            'shuffleanswers' => '0',
            'sumgrades' => '5',
            'grade' => '10',
            'timecreated' => '0',
            'timemodified' => '1170245956',
            'password' => '',
            'subnet' => '',
            'popup' => '0',
            'delay1' => '0',
            'delay2' => '0',
            'timelimit' => '0',
        );
        $attempt = (object) array(
            'id' => '1065',
            'uniqueid' => '1065',
            'quiz' => '60',
            'userid' => '182682',
            'attempt' => '1',
            'sumgrades' => '3.99998',
            'timestart' => '1168267317',
            'timefinish' => '1168267508',
            'timemodified' => '1168267508',
            'layout' => '509,510,511,738,514,0',
            'preview' => '0',
        );
        $question = (object) array(
            'id' => '738',
            'category' => '60',
            'parent' => '0',
            'name' => 'TR004',
            'questiontext' => '<p>Which of the following statements about subject gateways are true, and which are false? </p>',
            'questiontextformat' => '1',
            'defaultmark' => '1',
            'penalty' => '0.1',
            'qtype' => 'match',
            'length' => '1',
            'stamp' => 'learn.open.ac.uk+061128151507+CLevuJ',
            'version' => 'learn.open.ac.uk+070108115531+VvJurj',
            'hidden' => '0',
            'generalfeedback' => '<UL><LI>Subject gateways provide links to sites that have been quality checked = True</LI>
        <LI>Subject gateways offer more variety than search engines = False</LI>
        <LI>Subject gateways index websites automatically = False</LI>
        <LI>Subject gateways can provide a more direct route to websites containing academic content = True</LI>
        </UL>',
            'generalfeedbackformat' => '1',
            'timecreated' => '0',
            'timemodified' => '0',
            'createdby' => null,
            'modifiedby' => null,
            'unlimited' => null,
            'maxmark' => '1',
            'options' => (object) array(
                'id' => '35',
                'questionid' => '738',
                'subquestions' => array(
                    213 => (object) array(
                        'questionid' => '738',
                        'questiontext' => 'Subject gateways provide links to sites that have been quality checked ',
                        'answertext' => 'True',
                        'id' => 213,
                    ),
                    214 => (object) array(
                        'questionid' => '738',
                        'questiontext' => 'Subject gateways offer more variety than search engines ',
                        'answertext' => 'False',
                        'id' => 214,
                    ),
                    215 => (object) array(
                        'questionid' => '738',
                        'questiontext' => 'Subject gateways index websites automatically',
                        'answertext' => 'False',
                        'id' => 215,
                    ),
                    216 => (object) array(
                        'questionid' => '738',
                        'questiontext' => 'Subject gateways can provide a more direct route ' .
                                'to websites containing academic content ',
                        'answertext' => 'True',
                        'id' => 216,
                    ),
                ),
                'shuffleanswers' => '1',
                'correctfeedback' => '',
                'partiallycorrectfeedback' => '',
                'incorrectfeedback' => '',
                'correctresponsesfeedback' => '0',
            ),
            'hints' => false,
        );
        $qsession = (object) array(
            'id' => '9258',
            'attemptid' => '1065',
            'questionid' => '738',
            'newest' => '24966',
            'newgraded' => '24966',
            'sumpenalty' => '0',
            'manualcomment' => '',
            'manualcommentformat' => '1',
            'flagged' => '1',
        );
        $qstates = array(
            24961 => (object) array(
                'attempt' => '1065',
                'question' => '738',
                'originalquestion' => '0',
                'seq_number' => '0',
                'answer' => '213-0,214-0,215-0,216-0',
                'timestamp' => '1168267317',
                'event' => '0',
                'grade' => '0',
                'raw_grade' => '0',
                'penalty' => '0',
                'id' => 24961,
            ),
            24966 => (object) array(
                'attempt' => '1065',
                'question' => '738',
                'originalquestion' => '0',
                'seq_number' => '1',
                'answer' => '213-213,214-214,215-215,216-216',
                'timestamp' => '1168267508',
                'event' => '6',
                'grade' => '1',
                'raw_grade' => '1',
                'penalty' => '0.1',
                'id' => 24966,
            ),
        );

        $qa = $this->updater->convert_question_attempt($quiz, $attempt, $question, $qsession, $qstates);

        $expectedqa = (object) array(
            'behaviour' => 'deferredfeedback',
            'questionid' => 738,
            'variant' => 1,
            'maxmark' => 1,
            'minfraction' => 0,
            'maxfraction' => 1,
            'flagged' => 0,
            'questionsummary' => 'Which of the following statements about subject ' .
                    'gateways are true, and which are false? {Subject gateways ' .
                    'provide links to sites that have been quality checked; ' .
                    'Subject gateways offer more variety than search engines; ' .
                    'Subject gateways index websites automatically; ' .
                    'Subject gateways can provide a more direct route to websites containing academic content} -> ' .
                    '{True; False}',
            'rightanswer' => 'Subject gateways provide links to sites that have been quality checked -> True; ' .
                    'Subject gateways offer more variety than search engines -> False; ' .
                    'Subject gateways index websites automatically -> False; ' .
                    'Subject gateways can provide a more direct route to websites containing academic content -> True',
            'responsesummary' => 'Subject gateways provide links to sites that have been quality checked -> True; ' .
                    'Subject gateways offer more variety than search engines -> False; ' .
                    'Subject gateways index websites automatically -> False; ' .
                    'Subject gateways can provide a more direct route to websites containing academic content -> True',
            'timemodified' => 1168267508,
            'steps' => array(
                0 => (object) array(
                    'sequencenumber' => 0,
                    'state' => 'todo',
                    'fraction' => null,
                    'timecreated' => 1168267317,
                    'userid' => 182682,
                    'data' => array('_stemorder' => '213,214,215,216', '_choiceorder' => 'todo - see below'),
                ),
                1 => (object) array(
                    'sequencenumber' => 1,
                    'state' => 'gradedright',
                    'fraction' => 1,
                    'timecreated' => 1168267508,
                    'userid' => 182682,
                    'data' => array('sub0' => 213, 'sub1' => 214, 'sub2' => 214, 'sub3' => 213, '-finish' => 1),
                ),
            ),
        );

        // This is a random thing, so just set expected to actual.
        $expectedqa->steps[0]->data['_choiceorder'] = $qa->steps[0]->data['_choiceorder'];
        $order = explode(',', $qa->steps[0]->data['_choiceorder']);
        $order = array_combine(array_values($order), array_keys($order));
        for ($i = 1; $i <= 1; $i++) {
            for ($sub = 0; $sub < 4; $sub++) {
                $expectedqa->steps[$i]->data['sub' . $sub] =
                        $order[$expectedqa->steps[$i]->data['sub' . $sub]] + 1;
            }
        }

        $this->compare_qas($expectedqa, $qa);
    }

    public function test_match_deferredfeedback_history622220(): void {
        $quiz = (object) array(
            'id' => '719',
            'course' => '3541',
            'name' => 'Types of resources quiz',
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
            'sumgrades' => '5',
            'grade' => '10',
            'timecreated' => '0',
            'timemodified' => '1193678199',
            'password' => '',
            'subnet' => '',
            'popup' => '0',
            'delay1' => '0',
            'delay2' => '0',
            'timelimit' => '0',
        );
        $attempt = (object) array(
            'id' => '23777',
            'uniqueid' => '23777',
            'quiz' => '719',
            'userid' => '6584',
            'attempt' => '1',
            'sumgrades' => '3.91664',
            'timestart' => '1200506648',
            'timefinish' => '1200507571',
            'timemodified' => '1200506959',
            'layout' => '11163,0,11164,0,11165,0,11135,0,11166,0',
            'preview' => '0',
        );
        $question = (object) array(
            'id' => '11135',
            'category' => '675',
            'parent' => '0',
            'name' => 'TR004',
            'questiontext' => '<p>Which of the following statements about subject gateways are true, and which are false? </p>',
            'questiontextformat' => '0',
            'defaultmark' => '1',
            'penalty' => '0.1',
            'qtype' => 'match',
            'length' => '1',
            'stamp' => 'learn.open.ac.uk+071023110917+tqaM6z',
            'version' => 'learn.open.ac.uk+071023110917+Ia7Hpz',
            'hidden' => '0',
            'generalfeedback' => '<ul>
            <li>Subject gateways provide links to sites that have been quality checked = True </li>
 </ul>
 <p>All links in a subject gateway have been added by a knowledgeable subject specialist and ' .
                                'so have to be of a certain quality to be added to the collection.</p>
 <ul>
            <li>Subject gateways offer more variety than search engines = False </li>
 </ul>
 <p>Subject gateways will most likely provide fewer links than a search engine, but this is because ' .
                                'they are selected with a particular subject area in mind </p>
 <ul>
            <li>Subject gateways index websites automatically = False </li>
 </ul>
 <p>Subject gateways links are indexed by knowledgeable subject specialists rather than a machine. </p>
 <ul>
            <li>Subject gateways can provide a more direct route to websites containing academic content = True </li>
 </ul>
 <p>All links in a subject gateway have been added by a knowledgeable subject specialist ' .
                                'and so you can find academic content easier than using a web search engine.</p>',
            'generalfeedbackformat' => '1',
            'timecreated' => '0',
            'timemodified' => '0',
            'createdby' => null,
            'modifiedby' => null,
            'unlimited' => null,
            'maxmark' => '1',
            'options' => (object) array(
                'id' => '279',
                'questionid' => '11135',
                'subquestions' => array(
                    1632 => (object) array(
                        'questionid' => '11135',
                        'questiontext' => 'Subject gateways provide links to sites that have been quality checked',
                        'answertext' => 'True',
                        'id' => 1632,
                    ),
                    1633 => (object) array(
                        'questionid' => '11135',
                        'questiontext' => 'Subject gateways offer more variety than search engines',
                        'answertext' => 'False',
                        'id' => 1633,
                    ),
                    1634 => (object) array(
                        'questionid' => '11135',
                        'questiontext' => 'Subject gateways index websites automatically',
                        'answertext' => 'False',
                        'id' => 1634,
                    ),
                    1635 => (object) array(
                        'questionid' => '11135',
                        'questiontext' => 'Subject gateways can provide a more direct route to websites ' .
                                'containing academic content',
                        'answertext' => 'True',
                        'id' => 1635,
                    ),
                ),
                'shuffleanswers' => '1',
                'correctfeedback' => '',
                'partiallycorrectfeedback' => '',
                'incorrectfeedback' => '',
                'correctresponsesfeedback' => '0',
            ),
            'hints' => false,
        );
        $qsession = (object) array(
            'id' => '356418',
            'attemptid' => '23777',
            'questionid' => '11135',
            'newest' => '862740',
            'newgraded' => '862740',
            'sumpenalty' => '0.1',
            'manualcomment' => '',
            'manualcommentformat' => '1',
            'flagged' => '1',
        );
        $qstates = array(
            862587 => (object) array(
                'attempt' => '23777',
                'question' => '11135',
                'originalquestion' => '0',
                'seq_number' => '0',
                'answer' => '1633-0,1635-0,1634-0,1632-0',
                'timestamp' => '1200506648',
                'event' => '0',
                'grade' => '0',
                'raw_grade' => '0',
                'penalty' => '0',
                'id' => 862587,
            ),
            862638 => (object) array(
                'attempt' => '23777',
                'question' => '11135',
                'originalquestion' => '0',
                'seq_number' => '1',
                'answer' => '1633-1633,1635-1635,1634-0,1632-1632',
                'timestamp' => '1200507025',
                'event' => '2',
                'grade' => '0',
                'raw_grade' => '0.75',
                'penalty' => '0.1',
                'id' => 862638,
            ),
            862668 => (object) array(
                'attempt' => '23777',
                'question' => '11135',
                'originalquestion' => '0',
                'seq_number' => '2',
                'answer' => '1633-1633,1635-1635,1634-0,1632-1632',
                'timestamp' => '1200507125',
                'event' => '2',
                'grade' => '0',
                'raw_grade' => '0.75',
                'penalty' => '0.1',
                'id' => 862668,
            ),
            862673 => (object) array(
                'attempt' => '23777',
                'question' => '11135',
                'originalquestion' => '0',
                'seq_number' => '3',
                'answer' => '1633-1633,1635-1635,1634-0,1632-1632',
                'timestamp' => '1200507172',
                'event' => '2',
                'grade' => '0',
                'raw_grade' => '0.75',
                'penalty' => '0.1',
                'id' => 862673,
            ),
            862716 => (object) array(
                'attempt' => '23777',
                'question' => '11135',
                'originalquestion' => '0',
                'seq_number' => '4',
                'answer' => '1633-1633,1635-1635,1634-1635,1632-1632',
                'timestamp' => '1200507467',
                'event' => '2',
                'grade' => '0',
                'raw_grade' => '0.75',
                'penalty' => '0.1',
                'id' => 862716,
            ),
            862740 => (object) array(
                'attempt' => '23777',
                'question' => '11135',
                'originalquestion' => '0',
                'seq_number' => '5',
                'answer' => '1633-1633,1635-1635,1634-1635,1632-1632',
                'timestamp' => '1200507467',
                'event' => '6',
                'grade' => '0.75',
                'raw_grade' => '0.75',
                'penalty' => '0.1',
                'id' => 862740,
            ),
        );

        $qa = $this->updater->convert_question_attempt($quiz, $attempt, $question, $qsession, $qstates);

        $expectedqa = (object) array(
            'behaviour' => 'deferredfeedback',
            'questionid' => 11135,
            'variant' => 1,
            'maxmark' => 1,
            'minfraction' => 0,
            'maxfraction' => 1,
            'flagged' => 0,
            'questionsummary' => 'Which of the following statements about subject gateways are true, and which are false? ' .
                    '{Subject gateways provide links to sites that have been quality checked; ' .
                    'Subject gateways offer more variety than search engines; ' .
                    'Subject gateways index websites automatically; ' .
                    'Subject gateways can provide a more direct route to websites containing academic content} ' .
                    '-> {True; False}',
            'rightanswer' => 'Subject gateways provide links to sites that have been quality checked -> True; ' .
                    'Subject gateways offer more variety than search engines -> False; ' .
                    'Subject gateways index websites automatically -> False; ' .
                    'Subject gateways can provide a more direct route to websites containing academic content -> True',
            'responsesummary' => 'Subject gateways offer more variety than search engines -> False; ' .
                    'Subject gateways can provide a more direct route to websites containing academic content -> True; ' .
                    'Subject gateways index websites automatically -> True; ' .
                    'Subject gateways provide links to sites that have been quality checked -> True',
            'timemodified' => 1200507467,
            'steps' => array(
                0 => (object) array(
                    'sequencenumber' => 0,
                    'state' => 'todo',
                    'fraction' => null,
                    'timecreated' => 1200506648,
                    'userid' => 6584,
                    'data' => array('_stemorder' => '1633,1635,1634,1632',
                            '_choiceorder' => 'todo - see below'),
                ),
                1 => (object) array(
                    'sequencenumber' => 1,
                    'state' => 'complete',
                    'fraction' => null,
                    'timecreated' => 1200507025,
                    'userid' => 6584,
                    'data' => array('sub0' => 1633, 'sub1' => 1632, 'sub2' => 0, 'sub3' => 1632),
                ),
                2 => (object) array(
                    'sequencenumber' => 2,
                    'state' => 'complete',
                    'fraction' => null,
                    'timecreated' => 1200507125,
                    'userid' => 6584,
                    'data' => array('sub0' => 1633, 'sub1' => 1632, 'sub2' => 0, 'sub3' => 1632),
                ),
                3 => (object) array(
                    'sequencenumber' => 3,
                    'state' => 'complete',
                    'fraction' => null,
                    'timecreated' => 1200507172,
                    'userid' => 6584,
                    'data' => array('sub0' => 1633, 'sub1' => 1632, 'sub2' => 0, 'sub3' => 1632),
                ),
                4 => (object) array(
                    'sequencenumber' => 4,
                    'state' => 'complete',
                    'fraction' => null,
                    'timecreated' => 1200507467,
                    'userid' => 6584,
                    'data' => array('sub0' => 1633, 'sub1' => 1632, 'sub2' => 1632, 'sub3' => 1632),
                ),
                5 => (object) array(
                    'sequencenumber' => 5,
                    'state' => 'gradedpartial',
                    'fraction' => 0.75,
                    'timecreated' => 1200507467,
                    'userid' => 6584,
                    'data' => array('sub0' => 1633, 'sub1' => 1632, 'sub2' => 1632, 'sub3' => 1632, '-finish' => 1),
                ),
            ),
        );

        // This is a random thing, so just set expected to actual.
        $expectedqa->steps[0]->data['_choiceorder'] = $qa->steps[0]->data['_choiceorder'];
        $order = explode(',', $qa->steps[0]->data['_choiceorder']);
        $order = array_combine(array_values($order), array_keys($order));
        for ($i = 1; $i <= 5; $i++) {
            for ($sub = 0; $sub < 5; $sub++) {
                if (!array_key_exists('sub' . $sub, $expectedqa->steps[$i]->data) ||
                        $expectedqa->steps[$i]->data['sub' . $sub] == 0) {
                    continue;
                }
                $expectedqa->steps[$i]->data['sub' . $sub] =
                        $order[$expectedqa->steps[$i]->data['sub' . $sub]] + 1;
            }
        }

        $this->compare_qas($expectedqa, $qa);
    }
}

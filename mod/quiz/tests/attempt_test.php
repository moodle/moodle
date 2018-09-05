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
 * Tests for the quiz_attempt class.
 *
 * @package   mod_quiz
 * @category  test
 * @copyright 2014 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/quiz/locallib.php');


/**
 * Subclass of quiz_attempt to allow faking of the page layout.
 *
 * @copyright 2014 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_quiz_attempt_testable extends quiz_attempt {
    /** @var array list of slots to treat as if they contain descriptions in the fake layout. */
    protected $infos = array();

    /**
     * Set a fake page layout. Used when we test URL generation.
     * @param int $id assumed attempt id.
     * @param string $layout layout to set. Like quiz attempt.layout. E.g. '1,2,0,3,4,0,'.
     * @param array $infos slot numbers which contain 'descriptions', or other non-questions.
     * @return quiz_attempt attempt object for use in unit tests.
     */
    public static function setup_fake_attempt_layout($id, $layout, $infos = array()) {
        $attempt = new stdClass();
        $attempt->id = $id;
        $attempt->layout = $layout;

        $course = new stdClass();
        $quiz = new stdClass();
        $cm = new stdClass();
        $cm->id = 0;

        $attemptobj = new self($attempt, $quiz, $cm, $course, false);

        $attemptobj->slots = array();
        foreach (explode(',', $layout) as $slot) {
            if ($slot == 0) {
                continue;
            }
            $attemptobj->slots[$slot] = new stdClass();
            $attemptobj->slots[$slot]->slot = $slot;
            $attemptobj->slots[$slot]->requireprevious = 0;
            $attemptobj->slots[$slot]->questionid = 0;
        }

        $attemptobj->sections = array();
        $attemptobj->sections[0] = new stdClass();
        $attemptobj->sections[0]->heading = '';
        $attemptobj->sections[0]->firstslot = 1;
        $attemptobj->sections[0]->shufflequestions = 0;

        $attemptobj->infos = $infos;
        $attemptobj->link_sections_and_slots();
        $attemptobj->determine_layout();
        $attemptobj->number_questions();

        return $attemptobj;
    }

    public function is_real_question($slot) {
        return !in_array($slot, $this->infos);
    }
}


/**
 * Tests for the quiz_attempt class.
 *
 * @copyright 2014 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_quiz_attempt_testcase extends basic_testcase {
    /**
     * Test the functions quiz_update_open_attempts() and get_list_of_overdue_attempts()
     */
    public function test_attempt_url() {
        $attempt = mod_quiz_attempt_testable::setup_fake_attempt_layout(
                123, '1,2,0,3,4,0,5,6,0');

        // Attempt pages.
        $this->assertEquals(new moodle_url(
                '/mod/quiz/attempt.php?attempt=123&cmid=0'),
                $attempt->attempt_url());

        $this->assertEquals(new moodle_url(
                '/mod/quiz/attempt.php?attempt=123&page=2&cmid=0'),
                $attempt->attempt_url(null, 2));

        $this->assertEquals(new moodle_url(
                '/mod/quiz/attempt.php?attempt=123&page=1&cmid=0#'),
                $attempt->attempt_url(3));

        $this->assertEquals(new moodle_url(
                '/mod/quiz/attempt.php?attempt=123&page=1&cmid=0#q4'),
                $attempt->attempt_url(4));

        $this->assertEquals(new moodle_url(
                '#'),
                $attempt->attempt_url(null, 2, 2));

        $this->assertEquals(new moodle_url(
                '#'),
                $attempt->attempt_url(3, -1, 1));

        $this->assertEquals(new moodle_url(
                '#q4'),
                $attempt->attempt_url(4, -1, 1));

        // Summary page.
        $this->assertEquals(new moodle_url(
                '/mod/quiz/summary.php?attempt=123&cmid=0'),
                $attempt->summary_url());

        // Review page.
        $this->assertEquals(new moodle_url(
                '/mod/quiz/review.php?attempt=123&cmid=0'),
                $attempt->review_url());

        $this->assertEquals(new moodle_url(
                '/mod/quiz/review.php?attempt=123&page=2&cmid=0'),
                $attempt->review_url(null, 2));

        $this->assertEquals(new moodle_url(
                '/mod/quiz/review.php?attempt=123&page=1&cmid=0'),
                $attempt->review_url(3, -1, false));

        $this->assertEquals(new moodle_url(
                '/mod/quiz/review.php?attempt=123&page=1&cmid=0#q4'),
                $attempt->review_url(4, -1, false));

        $this->assertEquals(new moodle_url(
                '/mod/quiz/review.php?attempt=123&cmid=0'),
                $attempt->review_url(null, 2, true));

        $this->assertEquals(new moodle_url(
                '/mod/quiz/review.php?attempt=123&cmid=0'),
                $attempt->review_url(1, -1, true));

        $this->assertEquals(new moodle_url(
                '/mod/quiz/review.php?attempt=123&page=2&cmid=0'),
                $attempt->review_url(null, 2, false));

        $this->assertEquals(new moodle_url(
                '/mod/quiz/review.php?attempt=123&showall=0&cmid=0'),
                $attempt->review_url(null, 0, false));

        $this->assertEquals(new moodle_url(
                '/mod/quiz/review.php?attempt=123&showall=0&cmid=0'),
                $attempt->review_url(1, -1, false));

        $this->assertEquals(new moodle_url(
                '/mod/quiz/review.php?attempt=123&page=1&cmid=0'),
                $attempt->review_url(3, -1, false));

        $this->assertEquals(new moodle_url(
                '/mod/quiz/review.php?attempt=123&page=2&cmid=0'),
                $attempt->review_url(null, 2));

        $this->assertEquals(new moodle_url(
                '#'),
                $attempt->review_url(null, -1, null, 0));

        $this->assertEquals(new moodle_url(
                '#q3'),
                $attempt->review_url(3, -1, null, 0));

        $this->assertEquals(new moodle_url(
                '#q4'),
                $attempt->review_url(4, -1, null, 0));

        $this->assertEquals(new moodle_url(
                '#'),
                $attempt->review_url(null, 2, true, 0));

        $this->assertEquals(new moodle_url(
                '#'),
                $attempt->review_url(1, -1, true, 0));

        $this->assertEquals(new moodle_url(
                '/mod/quiz/review.php?attempt=123&page=2&cmid=0'),
                $attempt->review_url(null, 2, false, 0));

        $this->assertEquals(new moodle_url(
                '#'),
                $attempt->review_url(null, 0, false, 0));

        $this->assertEquals(new moodle_url(
                '#'),
                $attempt->review_url(1, -1, false, 0));

        $this->assertEquals(new moodle_url(
                '/mod/quiz/review.php?attempt=123&page=1&cmid=0#'),
                $attempt->review_url(3, -1, false, 0));

        // Review with more than 50 questions in the quiz.
        $attempt = mod_quiz_attempt_testable::setup_fake_attempt_layout(
                124, '1,2,3,4,5,6,7,8,9,10,0,11,12,13,14,15,16,17,18,19,20,0,' .
                '21,22,23,24,25,26,27,28,29,30,0,31,32,33,34,35,36,37,38,39,40,0,' .
                '41,42,43,44,45,46,47,48,49,50,0,51,52,53,54,55,56,57,58,59,60,0');

        $this->assertEquals(new moodle_url(
                '/mod/quiz/review.php?attempt=124&cmid=0'),
                $attempt->review_url());

        $this->assertEquals(new moodle_url(
                '/mod/quiz/review.php?attempt=124&page=2&cmid=0'),
                $attempt->review_url(null, 2));

        $this->assertEquals(new moodle_url(
                '/mod/quiz/review.php?attempt=124&page=1&cmid=0'),
                $attempt->review_url(11, -1, false));

        $this->assertEquals(new moodle_url(
                '/mod/quiz/review.php?attempt=124&page=1&cmid=0#q12'),
                $attempt->review_url(12, -1, false));

        $this->assertEquals(new moodle_url(
                '/mod/quiz/review.php?attempt=124&showall=1&cmid=0'),
                $attempt->review_url(null, 2, true));

        $this->assertEquals(new moodle_url(
                '/mod/quiz/review.php?attempt=124&showall=1&cmid=0'),
                $attempt->review_url(1, -1, true));

        $this->assertEquals(new moodle_url(
                '/mod/quiz/review.php?attempt=124&page=2&cmid=0'),
                $attempt->review_url(null, 2, false));

        $this->assertEquals(new moodle_url(
                '/mod/quiz/review.php?attempt=124&cmid=0'),
                $attempt->review_url(null, 0, false));

        $this->assertEquals(new moodle_url(
                '/mod/quiz/review.php?attempt=124&page=1&cmid=0'),
                $attempt->review_url(11, -1, false));

        $this->assertEquals(new moodle_url(
                '/mod/quiz/review.php?attempt=124&page=1&cmid=0#q12'),
                $attempt->review_url(12, -1, false));

        $this->assertEquals(new moodle_url(
                '/mod/quiz/review.php?attempt=124&page=2&cmid=0'),
                $attempt->review_url(null, 2));

        $this->assertEquals(new moodle_url(
                '#'),
                $attempt->review_url(null, -1, null, 0));

        $this->assertEquals(new moodle_url(
                '#q3'),
                $attempt->review_url(3, -1, null, 0));

        $this->assertEquals(new moodle_url(
                '#q4'),
                $attempt->review_url(4, -1, null, 0));

        $this->assertEquals(new moodle_url(
                '#'),
                $attempt->review_url(null, 2, true, 0));

        $this->assertEquals(new moodle_url(
                '#'),
                $attempt->review_url(1, -1, true, 0));

        $this->assertEquals(new moodle_url(
                '/mod/quiz/review.php?attempt=124&page=2&cmid=0'),
                $attempt->review_url(null, 2, false, 0));

        $this->assertEquals(new moodle_url(
                '#'),
                $attempt->review_url(null, 0, false, 0));

        $this->assertEquals(new moodle_url(
                '#'),
                $attempt->review_url(1, -1, false, 0));

        $this->assertEquals(new moodle_url(
                '/mod/quiz/review.php?attempt=124&page=1&cmid=0#'),
                $attempt->review_url(11, -1, false, 0));
    }
}

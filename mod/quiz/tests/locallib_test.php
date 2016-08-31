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
 * Unit tests for (some of) mod/quiz/locallib.php.
 *
 * @package    mod_quiz
 * @category   test
 * @copyright  2008 Tim Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/quiz/locallib.php');


/**
 * Unit tests for (some of) mod/quiz/locallib.php.
 *
 * @copyright  2008 Tim Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_quiz_locallib_testcase extends advanced_testcase {

    public function test_quiz_rescale_grade() {
        $quiz = new stdClass();
        $quiz->decimalpoints = 2;
        $quiz->questiondecimalpoints = 3;
        $quiz->grade = 10;
        $quiz->sumgrades = 10;
        $this->assertEquals(quiz_rescale_grade(0.12345678, $quiz, false), 0.12345678);
        $this->assertEquals(quiz_rescale_grade(0.12345678, $quiz, true), format_float(0.12, 2));
        $this->assertEquals(quiz_rescale_grade(0.12345678, $quiz, 'question'),
            format_float(0.123, 3));
        $quiz->sumgrades = 5;
        $this->assertEquals(quiz_rescale_grade(0.12345678, $quiz, false), 0.24691356);
        $this->assertEquals(quiz_rescale_grade(0.12345678, $quiz, true), format_float(0.25, 2));
        $this->assertEquals(quiz_rescale_grade(0.12345678, $quiz, 'question'),
            format_float(0.247, 3));
    }

    public function quiz_attempt_state_data_provider() {
        return [
            [quiz_attempt::IN_PROGRESS, null, null, mod_quiz_display_options::DURING],
            [quiz_attempt::FINISHED, -90, null, mod_quiz_display_options::IMMEDIATELY_AFTER],
            [quiz_attempt::FINISHED, -7200, null, mod_quiz_display_options::LATER_WHILE_OPEN],
            [quiz_attempt::FINISHED, -7200, 3600, mod_quiz_display_options::LATER_WHILE_OPEN],
            [quiz_attempt::FINISHED, -30, 30, mod_quiz_display_options::IMMEDIATELY_AFTER],
            [quiz_attempt::FINISHED, -90, -30, mod_quiz_display_options::AFTER_CLOSE],
            [quiz_attempt::FINISHED, -7200, -3600, mod_quiz_display_options::AFTER_CLOSE],
            [quiz_attempt::FINISHED, -90, -3600, mod_quiz_display_options::AFTER_CLOSE],
            [quiz_attempt::ABANDONED, -10000000, null, mod_quiz_display_options::LATER_WHILE_OPEN],
            [quiz_attempt::ABANDONED, -7200, 3600, mod_quiz_display_options::LATER_WHILE_OPEN],
            [quiz_attempt::ABANDONED, -7200, -3600, mod_quiz_display_options::AFTER_CLOSE],
        ];
    }

    /**
     * @dataProvider quiz_attempt_state_data_provider
     *
     * @param unknown $attemptstate as in the quiz_attempts.state DB column.
     * @param unknown $relativetimefinish time relative to now when the attempt finished, or null for 0.
     * @param unknown $relativetimeclose time relative to now when the quiz closes, or null for 0.
     * @param unknown $expectedstate expected result. One of the mod_quiz_display_options constants/
     */
    public function test_quiz_attempt_state($attemptstate,
            $relativetimefinish, $relativetimeclose, $expectedstate) {

        $attempt = new stdClass();
        $attempt->state = $attemptstate;
        if ($relativetimefinish === null) {
            $attempt->timefinish = 0;
        } else {
            $attempt->timefinish = time() + $relativetimefinish;
        }

        $quiz = new stdClass();
        if ($relativetimeclose === null) {
            $quiz->timeclose = 0;
        } else {
            $quiz->timeclose = time() + $relativetimeclose;
        }

        $this->assertEquals($expectedstate, quiz_attempt_state($quiz, $attempt));
    }

    public function test_quiz_question_tostring() {
        $question = new stdClass();
        $question->qtype = 'multichoice';
        $question->name = 'The question name';
        $question->questiontext = '<p>What sort of <b>inequality</b> is x &lt; y<img alt="?" src="..."></p>';
        $question->questiontextformat = FORMAT_HTML;

        $summary = quiz_question_tostring($question);
        $this->assertEquals('<span class="questionname">The question name</span> ' .
                '<span class="questiontext">What sort of INEQUALITY is x &lt; y[?]' . "\n" . '</span>', $summary);
    }

    /**
     * Test quiz_view
     * @return void
     */
    public function test_quiz_view() {
        global $CFG;

        $CFG->enablecompletion = 1;
        $this->resetAfterTest();

        $this->setAdminUser();
        // Setup test data.
        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));
        $quiz = $this->getDataGenerator()->create_module('quiz', array('course' => $course->id),
                                                            array('completion' => 2, 'completionview' => 1));
        $context = context_module::instance($quiz->cmid);
        $cm = get_coursemodule_from_instance('quiz', $quiz->id);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();

        quiz_view($quiz, $course, $cm, $context);

        $events = $sink->get_events();
        // 2 additional events thanks to completion.
        $this->assertCount(3, $events);
        $event = array_shift($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_quiz\event\course_module_viewed', $event);
        $this->assertEquals($context, $event->get_context());
        $moodleurl = new \moodle_url('/mod/quiz/view.php', array('id' => $cm->id));
        $this->assertEquals($moodleurl, $event->get_url());
        $this->assertEventContextNotUsed($event);
        $this->assertNotEmpty($event->get_name());
        // Check completion status.
        $completion = new completion_info($course);
        $completiondata = $completion->get_data($cm);
        $this->assertEquals(1, $completiondata->completionstate);
    }
}

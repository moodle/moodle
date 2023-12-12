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

namespace mod_quiz\question;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/quiz/locallib.php');

/**
 * Unit tests for {@see display_options}.
 *
 * @package    mod_quiz
 * @category   test
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \mod_quiz\question\display_options
 */
class display_options_test extends \basic_testcase {
    public function test_num_attempts_access_rule() {
        $quiz = new \stdClass();
        $quiz->decimalpoints = 2;
        $quiz->questiondecimalpoints = -1;
        $quiz->reviewattempt          = 0x11110;
        $quiz->reviewcorrectness      = 0x10000;
        $quiz->reviewmaxmarks         = 0x10000; // Max marks is set.
        $quiz->reviewmarks            = 0x00000; // Marks is not set.
        $quiz->reviewspecificfeedback = 0x10000;
        $quiz->reviewgeneralfeedback  = 0x01000;
        $quiz->reviewrightanswer      = 0x00100;
        $quiz->reviewoverallfeedback  = 0x00010;

        $options = display_options::make_from_quiz($quiz,
            display_options::DURING);

        $this->assertEquals(true, $options->attempt);
        $this->assertEquals(display_options::VISIBLE, $options->correctness);
        $this->assertEquals(display_options::MAX_ONLY, $options->marks);
        $this->assertEquals(display_options::VISIBLE, $options->feedback);
        // The next two should be controlled by the same settings as ->feedback.
        $this->assertEquals(display_options::VISIBLE, $options->numpartscorrect);
        $this->assertEquals(display_options::VISIBLE, $options->manualcomment);
        $this->assertEquals(2, $options->markdp);

        $quiz->questiondecimalpoints = 5;
        $quiz->reviewmaxmarks         = 0x11000; // Max marks is set.
        $quiz->reviewmarks            = 0x11000; // Marks is also set.
        $options = display_options::make_from_quiz($quiz,
            display_options::IMMEDIATELY_AFTER);

        $this->assertEquals(display_options::MARK_AND_MAX, $options->marks);
        $this->assertEquals(display_options::VISIBLE, $options->generalfeedback);
        $this->assertEquals(display_options::HIDDEN, $options->feedback);
        // The next two should be controlled by the same settings as ->feedback.
        $this->assertEquals(display_options::HIDDEN, $options->numpartscorrect);
        $this->assertEquals(display_options::HIDDEN, $options->manualcomment);
        $this->assertEquals(5, $options->markdp);

        $quiz->reviewmaxmarks         = 0x00000; // Max marks is NOT set.
        $quiz->reviewmarks            = 0x00000; // Marks is also NOT set.
        $options = display_options::make_from_quiz($quiz,
            display_options::LATER_WHILE_OPEN);

        $this->assertEquals(display_options::HIDDEN, $options->marks);
        $this->assertEquals(display_options::VISIBLE, $options->rightanswer);
        $this->assertEquals(display_options::HIDDEN, $options->generalfeedback);

        $options = display_options::make_from_quiz($quiz,
            display_options::AFTER_CLOSE);

        $this->assertEquals(display_options::VISIBLE, $options->overallfeedback);
        $this->assertEquals(display_options::HIDDEN, $options->rightanswer);
    }
}

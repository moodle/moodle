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

namespace mod_quiz;

use mod_quiz_display_options;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/quiz/locallib.php');

/**
 * Unit tests for {@link mod_quiz_display_options}.
 *
 * @package    mod_quiz
 * @category   test
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quizdisplayoptions_test extends \basic_testcase {
    public function test_num_attempts_access_rule() {
        $quiz = new \stdClass();
        $quiz->decimalpoints = 2;
        $quiz->questiondecimalpoints = -1;
        $quiz->reviewattempt          = 0x11110;
        $quiz->reviewcorrectness      = 0x10000;
        $quiz->reviewmarks            = 0x01110;
        $quiz->reviewspecificfeedback = 0x10000;
        $quiz->reviewgeneralfeedback  = 0x01000;
        $quiz->reviewrightanswer      = 0x00100;
        $quiz->reviewoverallfeedback  = 0x00010;

        $options = mod_quiz_display_options::make_from_quiz($quiz,
            mod_quiz_display_options::DURING);

        $this->assertEquals(true, $options->attempt);
        $this->assertEquals(mod_quiz_display_options::VISIBLE, $options->correctness);
        $this->assertEquals(mod_quiz_display_options::MAX_ONLY, $options->marks);
        $this->assertEquals(mod_quiz_display_options::VISIBLE, $options->feedback);
        // The next two should be controlled by the same settings as ->feedback.
        $this->assertEquals(mod_quiz_display_options::VISIBLE, $options->numpartscorrect);
        $this->assertEquals(mod_quiz_display_options::VISIBLE, $options->manualcomment);
        $this->assertEquals(2, $options->markdp);

        $quiz->questiondecimalpoints = 5;
        $options = mod_quiz_display_options::make_from_quiz($quiz,
            mod_quiz_display_options::IMMEDIATELY_AFTER);

        $this->assertEquals(mod_quiz_display_options::MARK_AND_MAX, $options->marks);
        $this->assertEquals(mod_quiz_display_options::VISIBLE, $options->generalfeedback);
        $this->assertEquals(mod_quiz_display_options::HIDDEN, $options->feedback);
        // The next two should be controlled by the same settings as ->feedback.
        $this->assertEquals(mod_quiz_display_options::HIDDEN, $options->numpartscorrect);
        $this->assertEquals(mod_quiz_display_options::HIDDEN, $options->manualcomment);
        $this->assertEquals(5, $options->markdp);

        $options = mod_quiz_display_options::make_from_quiz($quiz,
            mod_quiz_display_options::LATER_WHILE_OPEN);

        $this->assertEquals(mod_quiz_display_options::VISIBLE, $options->rightanswer);
        $this->assertEquals(mod_quiz_display_options::HIDDEN, $options->generalfeedback);

        $options = mod_quiz_display_options::make_from_quiz($quiz,
            mod_quiz_display_options::AFTER_CLOSE);

        $this->assertEquals(mod_quiz_display_options::VISIBLE, $options->overallfeedback);
        $this->assertEquals(mod_quiz_display_options::HIDDEN, $options->rightanswer);
    }
}

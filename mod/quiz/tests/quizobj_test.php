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

use basic_testcase;
use mod_quiz\question\display_options;
use mod_quiz\quiz_settings;
use stdClass;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/quiz/locallib.php');

/**
 * Unit tests for the quiz class
 *
 * @package    mod_quiz
 * @copyright  2008 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \mod_quiz\quiz_settings
 */
class quizobj_test extends basic_testcase {
    /**
     * Test cases for {@see test_cannot_review_message()}.
     *
     * @return array[]
     */
    public static function cannot_review_message_testcases(): array {
        return [
            // Review       Time close
            // Later close  quiz attempt    When                Expected
            // Quiz with no close date.
            [false, false, null, null, display_options::DURING, ''],
            [false, false, null, -60, display_options::IMMEDIATELY_AFTER, 'noreview'],
            [false, false, null, -180, display_options::LATER_WHILE_OPEN, 'noreview'],
            [false, false, null, -180, display_options::AFTER_CLOSE, 'noreview'],
            [false, true, null, null, display_options::DURING, ''],
            [false, true, null, -60, display_options::IMMEDIATELY_AFTER, 'noreview'],
            [false, true, null, -180, display_options::LATER_WHILE_OPEN, 'noreview'],
            [false, true, null, -180, display_options::AFTER_CLOSE, 'noreview'],
            // Quiz with a close in the future date, review only after close.
            [false, true, 300, null, display_options::DURING, ''],
            [false, true, 300, -60, display_options::IMMEDIATELY_AFTER, 300],
            [false, true, 300, -180, display_options::LATER_WHILE_OPEN, 300],
            // Quiz with a close in the future date, review later while open, or after close.
            [true, true, 300, null, display_options::DURING, ''],
            [true, true, 300, -60, display_options::IMMEDIATELY_AFTER, 60],
            [true, false, 300, -60, display_options::IMMEDIATELY_AFTER, 60],
            // Quiz with no closer date, review later while open.
            [true, false, 300, null, display_options::DURING, ''],
            [true, false, 300, -60, display_options::IMMEDIATELY_AFTER, 60],
        ];
    }

    /**
     * Unit test for {@see quiz_settings::cannot_review_message()}.
     *
     * @dataProvider cannot_review_message_testcases
     * @param bool $reviewlater whether the quiz allows reivew 'later while the quiz is still open'.
     * @param bool $reviewafterclose whether the quiz allows rievew 'after the quiz is closed'.
     * @param int|null $quizcloseoffset quiz close date, relative to now. Null means not set.
     * @param int|null $attemptsubmitoffset quiz attempt sumbite time relative to now. Null means not submitted yet.
     * @param int $attemptstate current state of the attempt, one of the display_options constants.
     * @param string|int $expectation expected result: '' means '', 'noreview' means noreview lang string,
     *      int means noreviewuntil with that time relative to now.
     */
    public function test_cannot_review_message(
        bool $reviewlater,
        bool $reviewafterclose,
        ?int $quizcloseoffset,
        ?int $attemptsubmitoffset,
        int $attemptstate,
        string|int $expectation
    ): void {
        $quiz = new stdClass();
        $now = time();

        $cm = new stdClass();
        $cm->id = 123;

        // Prepare quiz settings.
        $quiz->reviewattempt = display_options::DURING;
        if ($reviewlater) {
            $quiz->reviewattempt |= display_options::LATER_WHILE_OPEN;
        }
        if ($reviewafterclose) {
            $quiz->reviewattempt |= display_options::AFTER_CLOSE;
        }
        $quiz->attempts = 0;

        if ($quizcloseoffset === null) {
            $quiz->timeclose = 0;
        } else {
            $quiz->timeclose = $now + $quizcloseoffset;
        }
        if ($attemptsubmitoffset === null) {
            $submittime = 0;
        } else {
            $submittime = $now + $attemptsubmitoffset;
        }

        $quizobj = new quiz_settings($quiz, $cm, new stdClass(), false);

        // Prepare expected message.
        if ($expectation === 'noreview') {
            $expectation = get_string('noreview', 'quiz');
        } else if (is_int($expectation)) {
            $expectation = get_string('noreviewuntil', 'quiz', userdate($now + $expectation));
        }

        // Test.
        $this->assertEquals($expectation,
            $quizobj->cannot_review_message($attemptstate, false, $submittime));
    }
}

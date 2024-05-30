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

namespace quizaccess_offlineattempts;

use mod_quiz\quiz_settings;
use quizaccess_offlineattempts;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/quiz/accessrule/offlineattempts/rule.php');


/**
 * Unit tests for the quizaccess_offlineattempts plugin.
 *
 * @package    quizaccess_offlineattempts
 * @copyright  2016 Juan Leyva
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class rule_test extends \basic_testcase {
    public function test_offlineattempts_access_rule(): void {
        $quiz = new \stdClass();
        $quiz->allowofflineattempts = 1;
        $cm = new \stdClass();
        $cm->id = 0;
        $quizobj = new quiz_settings($quiz, $cm, null);
        $rule = new quizaccess_offlineattempts($quizobj, 0);
        $attempt = new \stdClass();

        $this->assertFalse($rule->prevent_access());
        $this->assertFalse($rule->prevent_new_attempt(0, $attempt));
        $this->assertFalse($rule->is_finished(0, $attempt));
        $this->assertFalse($rule->end_time($attempt));
        $this->assertFalse($rule->time_left_display($attempt, 0));
    }
}

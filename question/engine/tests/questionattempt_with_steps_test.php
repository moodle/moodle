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

namespace core_question;

use question_attempt;
use question_attempt_step;
use question_state;
use testable_question_attempt;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once(__DIR__ . '/../lib.php');
require_once(__DIR__ . '/helpers.php');

/**
 * These tests use a standard fixture of a {@link question_attempt} with three steps.
 *
 * Action methods like start, process_action and finish are assumed to be
 * tested by walkthrough tests in the various behaviours.
 *
 * @package    core_question
 * @category   test
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class questionattempt_with_steps_test extends \advanced_testcase {
    private $question;
    private $qa;

    protected function setUp(): void {
        parent::setUp();
        $this->question = \test_question_maker::make_question('description');
        $this->qa = new testable_question_attempt($this->question, 0, null, 2);
        for ($i = 0; $i < 3; $i++) {
            $step = new question_attempt_step(array('i' => $i));
            $this->qa->add_step($step);
        }
    }

    protected function tearDown(): void {
        $this->qa = null;
        parent::tearDown();
    }

    public function test_get_step_before_start(): void {
        $this->expectException(\moodle_exception::class);
        $step = $this->qa->get_step(-1);
    }

    public function test_get_step_at_start(): void {
        $step = $this->qa->get_step(0);
        $this->assertEquals(0, $step->get_qt_var('i'));
    }

    public function test_get_step_at_end(): void {
        $step = $this->qa->get_step(2);
        $this->assertEquals(2, $step->get_qt_var('i'));
    }

    public function test_get_step_past_end(): void {
        $this->expectException(\moodle_exception::class);
        $step = $this->qa->get_step(3);
    }

    public function test_get_num_steps(): void {
        $this->assertEquals(3, $this->qa->get_num_steps());
    }

    public function test_get_last_step(): void {
        $step = $this->qa->get_last_step();
        $this->assertEquals(2, $step->get_qt_var('i'));
    }

    public function test_get_last_qt_var_there1(): void {
        $this->assertEquals(2, $this->qa->get_last_qt_var('i'));
    }

    public function test_get_last_qt_var_there2(): void {
        $this->qa->get_step(0)->set_qt_var('_x', 'a value');
        $this->assertEquals('a value', $this->qa->get_last_qt_var('_x'));
    }

    public function test_get_last_qt_var_missing(): void {
        $this->assertNull($this->qa->get_last_qt_var('notthere'));
    }

    public function test_get_last_qt_var_missing_default(): void {
        $this->assertEquals('default', $this->qa->get_last_qt_var('notthere', 'default'));
    }

    public function test_get_last_behaviour_var_missing(): void {
        $this->assertNull($this->qa->get_last_qt_var('notthere'));
    }

    public function test_get_last_behaviour_var_there(): void {
        $this->qa->get_step(1)->set_behaviour_var('_x', 'a value');
        $this->assertEquals('a value', '' . $this->qa->get_last_behaviour_var('_x'));
    }

    public function test_get_state_gets_state_of_last(): void {
        $this->qa->get_step(2)->set_state(question_state::$gradedright);
        $this->qa->get_step(1)->set_state(question_state::$gradedwrong);
        $this->assertEquals(question_state::$gradedright, $this->qa->get_state());
    }

    public function test_get_mark_gets_mark_of_last(): void {
        $this->assertEquals(2, $this->qa->get_max_mark());
        $this->qa->get_step(2)->set_fraction(0.5);
        $this->qa->get_step(1)->set_fraction(0.1);
        $this->assertEquals(1, $this->qa->get_mark());
    }

    public function test_get_fraction_gets_fraction_of_last(): void {
        $this->qa->get_step(2)->set_fraction(0.5);
        $this->qa->get_step(1)->set_fraction(0.1);
        $this->assertEquals(0.5, $this->qa->get_fraction());
    }

    public function test_get_fraction_returns_null_if_none(): void {
        $this->assertNull($this->qa->get_fraction());
    }

    public function test_format_mark(): void {
        $this->qa->get_step(2)->set_fraction(0.5);
        $this->assertEquals('1.00', $this->qa->format_mark(2));
    }

    public function test_format_max_mark(): void {
        $this->assertEquals('2.0000000', $this->qa->format_max_mark(7));
    }

    public function test_get_min_fraction(): void {
        $this->qa->set_min_fraction(-1);
        $this->assertEquals(-1, $this->qa->get_min_fraction());
    }

    public function test_cannot_get_min_fraction_before_start(): void {
        $qa = new question_attempt($this->question, 0);
        $this->expectException('moodle_exception');
        $qa->get_min_fraction();
    }

    public function test_get_max_fraction(): void {
        $this->qa->set_max_fraction(2);
        $this->assertEquals(2, $this->qa->get_max_fraction());
    }

    public function test_cannot_get_max_fraction_before_start(): void {
        $qa = new question_attempt($this->question, 0);
        $this->expectException('moodle_exception');
        $qa->get_max_fraction();
    }

    /**
     * Test cases for {@see test_validate_manual_mark()}.
     *
     * @return array test cases
     */
    public static function validate_manual_mark_cases(): array {
        // Recall, the DB schema stores question grade information to 7 decimal places.
        return [
            [0, 1, 2, null, ''],
            [0, 1, 2, '', ''],
            [0, 1, 2, '0', ''],
            [0, 1, 2, '0.0', ''],
            [0, 1, 2, '2,0', ''],
            [0, 1, 2, 'frog', get_string('manualgradeinvalidformat', 'question')],
            [0, 1, 2, '2.1', get_string('manualgradeoutofrange', 'question')],
            [0, 1, 2, '-0,01', get_string('manualgradeoutofrange', 'question')],
            [-0.3333333, 1, 0.75, '0.75', ''],
            [-0.3333333, 1, 0.75, '0.7500001', get_string('manualgradeoutofrange', 'question')],
            [-0.3333333, 1, 0.75, '-0.25', ''],
            [-0.3333333, 1, 0.75, '-0.2500001', get_string('manualgradeoutofrange', 'question')],
        ];
    }

    /**
     * Test validate_manual_mark.
     *
     * @dataProvider validate_manual_mark_cases
     *
     * @param float $minfraction minimum fraction for the question being attempted.
     * @param float $maxfraction maximum fraction for the question being attempted.
     * @param float $maxmark marks for the question attempt.
     * @param string|null $currentmark submitted mark.
     * @param string $expectederror expected error, if any.
     */
    public function test_validate_manual_mark(float $minfraction, float $maxfraction,
            float $maxmark, ?string $currentmark, string $expectederror): void {
        $this->qa->set_min_fraction($minfraction);
        $this->qa->set_max_fraction($maxfraction);
        $this->qa->set_max_mark($maxmark);
        $this->assertSame($expectederror, $this->qa->validate_manual_mark($currentmark));
    }
}

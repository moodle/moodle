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

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once(__DIR__ . '/../lib.php');
require_once(__DIR__ . '/helpers.php');

/**
 * Unit tests for the {@link question_attempt} class.
 *
 * Action methods like start, process_action and finish are assumed to be
 * tested by walkthrough tests in the various behaviours.
 *
 * These are the tests that don't require any steps.
 *
 * @package    core_question
 * @category   test
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class questionattempt_test extends \advanced_testcase {
    /** @var question_definition a question that can be used in the tests. */
    private $question;
    /** @var int fake question_usage id used in some tests. */
    private $usageid;
    /** @var question_attempt a question attempt that can be used in the tests. */
    private $qa;

    protected function setUp(): void {
        $this->question = \test_question_maker::make_question('description');
        $this->question->defaultmark = 3;
        $this->usageid = 13;
        $this->qa = new question_attempt($this->question, $this->usageid);
    }

    protected function tearDown(): void {
        $this->question = null;
        $this->useageid = null;
        $this->qa = null;
    }

    public function test_constructor_sets_maxmark() {
        $qa = new question_attempt($this->question, $this->usageid);
        $this->assertSame($this->question, $qa->get_question(false));
        $this->assertEquals(3, $qa->get_max_mark());
    }

    public function test_maxmark_beats_default_mark() {
        $qa = new question_attempt($this->question, $this->usageid, null, 2);
        $this->assertEquals(2, $qa->get_max_mark());
    }

    public function test_get_set_slot() {
        $this->qa->set_slot(7);
        $this->assertEquals(7, $this->qa->get_slot());
    }

    public function test_fagged_initially_false() {
        $this->assertEquals(false, $this->qa->is_flagged());
    }

    public function test_set_is_flagged() {
        $this->qa->set_flagged(true);
        $this->assertEquals(true, $this->qa->is_flagged());
    }

    public function test_get_qt_field_name() {
        $name = $this->qa->get_qt_field_name('test');
        $this->assertMatchesRegularExpression('/^' . preg_quote($this->qa->get_field_prefix(), '/') . '/', $name);
        $this->assertMatchesRegularExpression('/_test$/', $name);
    }

    public function test_get_behaviour_field_name() {
        $name = $this->qa->get_behaviour_field_name('test');
        $this->assertMatchesRegularExpression('/^' . preg_quote($this->qa->get_field_prefix(), '/') . '/', $name);
        $this->assertMatchesRegularExpression('/_-test$/', $name);
    }

    public function test_get_field_prefix() {
        $this->qa->set_slot(7);
        $name = $this->qa->get_field_prefix();
        $this->assertMatchesRegularExpression('/' . preg_quote($this->usageid, '/') . '/', $name);
        $this->assertMatchesRegularExpression('/' . preg_quote($this->qa->get_slot(), '/') . '/', $name);
    }

    public function test_get_submitted_var_not_present_var_returns_null() {
        $this->assertNull($this->qa->get_submitted_var(
                'reallyunlikelyvariablename', PARAM_BOOL));
    }

    public function test_get_all_submitted_qt_vars() {
        $this->qa->set_usage_id('MDOgzdhS4W');
        $this->qa->set_slot(1);
        $this->assertEquals(array('omval_response1' => 1, 'omval_response2' => 666, 'omact_gen_14' => 'Check'),
                $this->qa->get_all_submitted_qt_vars(array(
                    'qMDOgzdhS4W:1_omval_response1' => 1,
                    'qMDOgzdhS4W:1_omval_response2' => 666,
                    'qMDOgzdhS4W:1_omact_gen_14' => 'Check',
                )));
    }
}

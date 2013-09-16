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
 * Unit tests for the progress classes.
 *
 * @package core_backup
 * @category phpunit
 * @copyright 2013 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Include all the needed stuff.
global $CFG;
require_once($CFG->dirroot . '/backup/util/progress/core_backup_progress.class.php');

/**
 * Progress tests.
 */
class backup_progress_testcase extends basic_testcase {

    /**
     * Tests for basic use with simple numeric progress.
     */
    public function test_basic() {
        $progress = new core_backup_mock_progress();

        // Check values of empty progress things.
        $this->assertFalse($progress->is_in_progress_section());

        // Start progress counting, check basic values and check that update
        // gets called.
        $progress->start_progress('hello', 10);
        $this->assertTrue($progress->was_update_called());
        $this->assertTrue($progress->is_in_progress_section());
        $this->assertEquals('hello', $progress->get_current_description());

        // Check numeric position and indeterminate count.
        $this->assert_min_max(0.0, 0.0, $progress);
        $this->assertEquals(0, $progress->get_progress_count());

        // Make some progress and check that the time limit gets added.
        $progress->step_time();
        $progress->progress(2);
        $this->assertTrue($progress->was_update_called());
        $this->assertEquals(120, ini_get('max_execution_time'));

        // Check the new value.
        $this->assert_min_max(0.2, 0.2, $progress);

        // Do another progress run at same time, it should be ignored.
        $progress->progress(3);
        $this->assertFalse($progress->was_update_called());
        $this->assert_min_max(0.2, 0.2, $progress);

        // End the section. This should cause an update.
        $progress->end_progress();
        $this->assertTrue($progress->was_update_called());

        // Because there are no sections left open, it thinks we finished.
        $this->assert_min_max(1.0, 1.0, $progress);

        // There was 1 progress call.
        $this->assertEquals(1, $progress->get_progress_count());

        // Clear the time limit, otherwise phpunit complains.
        set_time_limit(0);
    }

    /**
     * Tests progress that is nested and/or indeterminate.
     */
    public function test_nested() {
        // Outer progress goes from 0 to 10.
        $progress = new core_backup_mock_progress();
        $progress->start_progress('hello', 10);

        // Get up to 4, check position.
        $progress->step_time();
        $progress->progress(4);
        $this->assert_min_max(0.4, 0.4, $progress);
        $this->assertEquals('hello', $progress->get_current_description());

        // Now start indeterminate progress.
        $progress->start_progress('world');
        $this->assert_min_max(0.4, 0.5, $progress);
        $this->assertEquals('world', $progress->get_current_description());

        // Do some indeterminate progress and count it (once per second).
        $progress->step_time();
        $progress->progress();
        $this->assertEquals(2, $progress->get_progress_count());
        $progress->progress();
        $this->assertEquals(2, $progress->get_progress_count());
        $progress->step_time();
        $progress->progress();
        $this->assertEquals(3, $progress->get_progress_count());
        $this->assert_min_max(0.4, 0.5, $progress);

        // Exit the indeterminate section.
        $progress->end_progress();
        $this->assert_min_max(0.5, 0.5, $progress);

        $progress->step_time();
        $progress->progress(7);
        $this->assert_min_max(0.7, 0.7, $progress);

        // Enter a numbered section (this time with a range of 5).
        $progress->start_progress('frogs', 5);
        $this->assert_min_max(0.7, 0.7, $progress);
        $progress->step_time();
        $progress->progress(1);
        $this->assert_min_max(0.72, 0.72, $progress);
        $progress->step_time();
        $progress->progress(3);
        $this->assert_min_max(0.76, 0.76, $progress);

        // Now enter another indeterminate section.
        $progress->start_progress('and');
        $this->assert_min_max(0.76, 0.78, $progress);

        // Make some progress, should increment indeterminate count.
        $progress->step_time();
        $progress->progress();
        $this->assertEquals(7, $progress->get_progress_count());

        // Enter numbered section, won't make any difference to values.
        $progress->start_progress('zombies', 2);
        $progress->step_time();
        $progress->progress(1);
        $this->assert_min_max(0.76, 0.78, $progress);
        $this->assertEquals(8, $progress->get_progress_count());

        // Leaving it will make no difference too.
        $progress->end_progress();

        // Leaving the indeterminate section will though.
        $progress->end_progress();
        $this->assert_min_max(0.78, 0.78, $progress);

        // Leave the two numbered sections.
        $progress->end_progress();
        $this->assert_min_max(0.8, 0.8, $progress);
        $progress->end_progress();
        $this->assertFalse($progress->is_in_progress_section());

        set_time_limit(0);
    }

    /**
     * Tests the feature for 'weighting' nested progress.
     */
    public function test_nested_weighted() {
        $progress = new core_backup_mock_progress();
        $progress->start_progress('', 10);

        // First nested child has 2 units of its own and is worth 1 unit.
        $progress->start_progress('', 2);
        $progress->step_time();
        $progress->progress(1);
        $this->assert_min_max(0.05, 0.05, $progress);
        $progress->end_progress();
        $this->assert_min_max(0.1, 0.1, $progress);

        // Next child has 2 units of its own but is worth 3 units.
        $progress->start_progress('weighted', 2, 3);
        $progress->step_time();
        $progress->progress(1);
        $this->assert_min_max(0.25, 0.25, $progress);
        $progress->end_progress();
        $this->assert_min_max(0.4, 0.4, $progress);

        // Next indeterminate child is worth 6 units.
        $progress->start_progress('', core_backup_progress::INDETERMINATE, 6);
        $progress->step_time();
        $progress->progress();
        $this->assert_min_max(0.4, 1.0, $progress);
        $progress->end_progress();
        $this->assert_min_max(1.0, 1.0, $progress);

        set_time_limit(0);
    }

    /**
     * I had some issues with real use in backup/restore, this test is intended
     * to be similar.
     */
    public function test_realistic() {
        $progress = new core_backup_mock_progress();
        $progress->start_progress('parent', 100);
        $progress->start_progress('child', 1);
        $progress->progress(1);
        $this->assert_min_max(0.01, 0.01, $progress);
        $progress->end_progress();
        $this->assert_min_max(0.01, 0.01, $progress);

        // Clear the time limit, otherwise phpunit complains.
        set_time_limit(0);
    }

    /**
     * To avoid causing problems, progress needs to work for sections that have
     * zero entries.
     */
    public function test_zero() {
        $progress = new core_backup_mock_progress();
        $progress->start_progress('parent', 100);
        $progress->progress(1);
        $this->assert_min_max(0.01, 0.01, $progress);
        $progress->start_progress('child', 0);

        // For 'zero' progress, the progress section as immediately complete
        // within the parent count, so it moves up to 2%.
        $this->assert_min_max(0.02, 0.02, $progress);
        $progress->progress(0);
        $this->assert_min_max(0.02, 0.02, $progress);
        $progress->end_progress();
        $this->assert_min_max(0.02, 0.02, $progress);

        set_time_limit(0);
    }

    /**
     * Tests for any exceptions due to invalid calls.
     */
    public function test_exceptions() {
        $progress = new core_backup_mock_progress();

        // Check errors when empty.
        try {
            $progress->progress();
            $this->fail();
        } catch (coding_exception $e) {
            $this->assertEquals(1, preg_match('~without start_progress~', $e->getMessage()));
        }
        try {
            $progress->end_progress();
            $this->fail();
        } catch (coding_exception $e) {
            $this->assertEquals(1, preg_match('~without start_progress~', $e->getMessage()));
        }
        try {
            $progress->get_current_description();
            $this->fail();
        } catch (coding_exception $e) {
            $this->assertEquals(1, preg_match('~Not inside progress~', $e->getMessage()));
        }
        try {
            $progress->start_progress('', 1, 7);
            $this->fail();
        } catch (coding_exception $e) {
            $this->assertEquals(1, preg_match('~must be 1~', $e->getMessage()));
        }

        // Check invalid start (-2).
        try {
            $progress->start_progress('hello', -2);
            $this->fail();
        } catch (coding_exception $e) {
            $this->assertEquals(1, preg_match('~cannot be negative~', $e->getMessage()));
        }

        // Indeterminate when value expected.
        $progress->start_progress('hello', 10);
        try {
            $progress->progress(core_backup_progress::INDETERMINATE);
            $this->fail();
        } catch (coding_exception $e) {
            $this->assertEquals(1, preg_match('~expecting value~', $e->getMessage()));
        }

        // Value when indeterminate expected.
        $progress->start_progress('hello');
        try {
            $progress->progress(4);
            $this->fail();
        } catch (coding_exception $e) {
            $this->assertEquals(1, preg_match('~expecting INDETERMINATE~', $e->getMessage()));
        }

        // Illegal values.
        $progress->start_progress('hello', 10);
        try {
            $progress->progress(-2);
            $this->fail();
        } catch (coding_exception $e) {
            $this->assertEquals(1, preg_match('~out of range~', $e->getMessage()));
        }
        try {
            $progress->progress(11);
            $this->fail();
        } catch (coding_exception $e) {
            $this->assertEquals(1, preg_match('~out of range~', $e->getMessage()));
        }

        // You are allowed two with the same value...
        $progress->progress(4);
        $progress->step_time();
        $progress->progress(4);
        $progress->step_time();

        // ...but not to go backwards.
        try {
            $progress->progress(3);
            $this->fail();
        } catch (coding_exception $e) {
            $this->assertEquals(1, preg_match('~backwards~', $e->getMessage()));
        }

        // When you go forward, you can't go further than there is room.
        try {
            $progress->start_progress('', 1, 7);
            $this->fail();
        } catch (coding_exception $e) {
            $this->assertEquals(1, preg_match('~would exceed max~', $e->getMessage()));
        }

        // Clear the time limit, otherwise phpunit complains.
        set_time_limit(0);
    }

    /**
     * Checks the current progress values are as expected.
     *
     * @param number $min Expected min progress
     * @param number $max Expected max progress
     * @param core_backup_mock_progress $progress
     */
    private function assert_min_max($min, $max, core_backup_mock_progress $progress) {
        $this->assertEquals(array($min, $max),
                $progress->get_progress_proportion_range());
    }
}

/**
 * Helper class that records when update_progress is called and allows time
 * stepping.
 */
class core_backup_mock_progress extends core_backup_progress {
    private $updatecalled = false;
    private $time = 1;

    /**
     * Checks if update was called since the last call to this function.
     *
     * @return boolean True if update was called
     */
    public function was_update_called() {
        if ($this->updatecalled) {
            $this->updatecalled = false;
            return true;
        }
        return false;
    }

    /**
     * Steps the current time by 1 second.
     */
    public function step_time() {
        $this->time++;
    }

    protected function update_progress() {
        $this->updatecalled = true;
    }

    protected function get_time() {
        return $this->time;
    }
}

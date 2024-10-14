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

namespace core\lock;

/**
 * Unit tests for the lock factory.
 *
 * @covers \core\lock\timing_wrapper_lock_factory
 * @package core
 * @copyright 2022 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class timing_wrapper_lock_factory_test extends \advanced_testcase {

    /**
     * Tests lock timing wrapper class.
     */
    public function test_lock_timing(): void {
        global $PERF;

        $this->resetAfterTest();

        // Reset the storage in case previous tests have added anything.
        unset($PERF->locks);

        // Set up a lock factory using the db record lock type which is always available.
        $lockfactory = new timing_wrapper_lock_factory('phpunit',
                new db_record_lock_factory('phpunit'));

        // Get 2 locks and release them.
        $before = microtime(true);
        $lock1 = $lockfactory->get_lock('frog', 2);
        $lock2 = $lockfactory->get_lock('toad', 2);
        $lock2->release();
        $lock1->release();
        $duration = microtime(true) - $before;

        // Confirm that perf info is now logged and appears plausible.
        $this->assertObjectHasProperty('locks', $PERF);
        $this->assertEquals('phpunit', $PERF->locks[0]->type);
        $this->assertEquals('frog', $PERF->locks[0]->resource);
        $this->assertTrue($PERF->locks[0]->wait <= $duration);
        $this->assertTrue($PERF->locks[0]->success);
        $this->assertTrue($PERF->locks[0]->held <= $duration);
        $this->assertEquals('phpunit', $PERF->locks[1]->type);
        $this->assertEquals('toad', $PERF->locks[1]->resource);
        $this->assertTrue($PERF->locks[1]->wait <= $duration);
        $this->assertTrue($PERF->locks[1]->success);
        $this->assertTrue($PERF->locks[1]->held <= $duration);
    }
}

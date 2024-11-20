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
 * Unit tests for the postgres lock factory.
 *
 * @covers \core\lock\postgres_lock_factory
 * @package core
 * @copyright 2024 Martin Gauk <martin.gauk@tu-berlin.de>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class postgres_lock_factory_test extends \advanced_testcase {
    /**
     * Set up.
     */
    public function setUp(): void {
        global $DB;
        parent::setUp();
        // Skip tests if not using Postgres.
        if (!($DB instanceof \pgsql_native_moodle_database)) {
            $this->markTestSkipped('Postgres-only test');
        }
    }

    /**
     * Test locking resources that lead to the same token computed by postgres_lock_factory::get_index_from_key.
     *
     * It is known that get_index_from_key computes the same token for the strings 'cron_core_cron' and
     * 'cron_adhoc_82795762'.
     */
    public function test_resources_with_same_hash(): void {
        $cronlockfactory = new postgres_lock_factory('cron');
        $lock1 = $cronlockfactory->get_lock('core_cron', 0);
        $this->assertNotFalse($lock1);
        $lock2 = $cronlockfactory->get_lock('adhoc_82795762', 0);
        $this->assertNotFalse($lock2);

        $lock2->release();
        $lock1->release();
    }

    /**
     * Test auto_release() method.
     *
     * Check that all locks were released after calling auto_release().
     */
    public function test_auto_release(): void {
        $cronlockfactory = new postgres_lock_factory('test');
        $lock1 = $cronlockfactory->get_lock('1', 0);
        $this->assertNotFalse($lock1);
        $lock2 = $cronlockfactory->get_lock('2', 0);
        $this->assertNotFalse($lock2);

        // Trying to get the lock again should fail as the lock is already held.
        $this->assertFalse($cronlockfactory->get_lock('1', 0));

        $cronlockfactory->auto_release();

        // We can now lock the resources again.
        $lock1again = $cronlockfactory->get_lock('1', 0);
        $this->assertNotFalse($lock1again);
        $lock2again = $cronlockfactory->get_lock('2', 0);
        $this->assertNotFalse($lock2again);

        $lock1again->release();
        $lock2again->release();

        // Need to explicitly release the locks (although they were already released) to avoid the debug message.
        $lock1->release();
        $lock2->release();
    }
}

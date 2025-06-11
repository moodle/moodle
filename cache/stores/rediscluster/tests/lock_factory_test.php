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
 * lock unit tests for rediscluster
 *
 * @package    cachestore_rediscluster
 * @copyright  Copyright (c) 2021 Open LMS (https://www.openlms.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../../../tests/fixtures/stores.php');
require_once(__DIR__.'/../lib.php');


/**
 * Unit tests for our rediscluster locking implementation.
 *
 * @package    cachestore_rediscluster
 * @copyright  Copyright (c) 2021 Open LMS (https://www.openlms.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cachestore_rediscluster_lock_factory_testcase extends advanced_testcase {

    /**
     * Some lock types will store data in the database.
     */
    protected function setUp(): void {
        $this->resetAfterTest(true);
    }

    /**
     * Run a suite of tests on the lock factory.
     */
    public function test_lock_factory() {
        if (!PHPUNIT_LONGTEST) {
            return;
        }
        $instance = new cachestore_rediscluster('RedisCluster Test', cachestore_rediscluster::unit_test_configuration());

        if (!$instance->is_ready()) {
            // We're not configured to use RedisCluster. Skip.
            $this->markTestSkipped();
        }

        $lockfactory = new \cachestore_rediscluster\lock_factory('test');

        if ($lockfactory->is_available()) {
            // This should work.
            $lock1 = $lockfactory->get_lock('abc', 2);
            $this->assertNotEmpty($lock1, 'Get a lock');

            // This should timeout.
            $lock2 = $lockfactory->get_lock('abc', 2);
            $this->assertFalse($lock2, 'Cannot get a stacked lock');

            // Release the lock.
            $this->assertTrue($lock1->release(), 'Release a lock');
            // Get it again.
            $lock3 = $lockfactory->get_lock('abc', 2);

            $this->assertNotEmpty($lock3, 'Get a lock again');
            // Release the lock again.
            $this->assertTrue($lock3->release(), 'Release a lock again');
            // Release the lock again (shouldn't hurt).
            $this->assertFalse($lock3->release(), 'Release a lock that is not held');
            if (!$lockfactory->supports_auto_release()) {
                // Test that a lock can be claimed after the timeout period.
                $lock4 = $lockfactory->get_lock('abc', 2, 2);
                $this->assertNotEmpty($lock4, 'Get a lock');
                sleep(3);

                $lock5 = $lockfactory->get_lock('abc', 2, 2);
                $this->assertNotEmpty($lock5, 'Get another lock after a timeout');
                $this->assertTrue($lock5->release(), 'Release the lock');
                $this->assertTrue($lock4->release(), 'Release the lock');
            }
        }
    }

}

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
 * Ratelimiter unit tests for rediscluster
 *
 * @package    cachestore_rediscluster
 * @copyright  Copyright (c) 2021 Open LMS (https://www.openlms.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../../../tests/fixtures/stores.php');
require_once(__DIR__.'/../lib.php');
require_once(__DIR__.'/fixtures/testable_ratelimiter.php');


/**
 * Unit tests for our rediscluster rate limiting implementation.
 *
 * @package    cachestore_rediscluster
 * @copyright  Copyright (c) 2021 Open LMS (https://www.openlms.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cachestore_rediscluster_ratelimiter_testcase extends advanced_testcase {

    /**
     * Some lock types will store data in the database.
     */
    protected function setUp(): void {
        $this->resetAfterTest(true);

        $instance = new cachestore_rediscluster('RedisCluster Test', cachestore_rediscluster::unit_test_configuration());

        if (!$instance->is_ready()) {
            // We're not configured to use RedisCluster. Skip.
            $this->markTestSkipped();
        }
    }

    protected function tearDown(): void {
        $keys = ['abc', 'def', 'ghi'];
        testable_ratelimiter::get_instance()->testing_cleanup($keys);
    }

    /**
     * Test the average response limiting feature.
     */
    public function test_limited_average() {
        $window = 120;
        $limit = 10;
        $minimum = 2;

        $limiter = testable_ratelimiter::get_instance();

        $limiter->track('abc', 11); // Avg = 11.
        // Only one sample, a minimum of 2 samples means this should not yet be limited.
        $this->assertFalse($limiter->limited_average('abc', $window, $limit, $minimum));
        $limiter->track('abc', 11); // Avg = 11.
        // We now have the minimum samples and an average over the limit.
        $this->assertTrue($limiter->limited_average('abc', $window, $limit, $minimum));
        $limiter->track('abc', 5); // Avg = 9.
        // Sample is still over the minimum, but our average is now under the limit.
        $this->assertFalse($limiter->limited_average('abc', $window, $limit, $minimum));
    }

    /**
     * Test the total rate response limiting feature.
     */
    public function test_limited_count() {
        $window = 120;
        $limit = 3;

        $limiter = testable_ratelimiter::get_instance();

        $limiter->track('def', 1); // Count = 1.
        $limiter->track('def', 1);// Count = 2.
        // We need 3 to trigger the rate limit, so 2 should be fine.
        $this->assertFalse($limiter->limited_count('def', $window, $limit));
        $limiter->track('def', 1);// Count = 3.
        // We've now hit the limit.
        $this->assertTrue($limiter->limited_count('def', $window, $limit));
        // And without tracking more, lets ask about a higher limit.
        $this->assertFalse($limiter->limited_count('def', $window, $limit + 1));
    }

    public function test_limited_timeouts() {
        if (!PHPUNIT_LONGTEST) {
            $this->markTestSkipped();
        }

        $window = 1;
        $limit = 3;

        $limiter = testable_ratelimiter::get_instance();

        $limiter->track('ghi', 1);
        $limiter->track('ghi', 1);
        $limiter->track('ghi', 1);
        // At t=0, c=3. We've hit the limit in the window.
        $this->assertTrue($limiter->limited_count('ghi', $window, $limit));
        usleep(500000);
        $limiter->track('ghi', 1);
        // At t=0.50, c=4.
        $this->assertTrue($limiter->limited_count('ghi', $window, $limit));
        usleep(250000);
        $limiter->track('ghi', 1);
        // At t=0.75, c=5.
        $this->assertTrue($limiter->limited_count('ghi', $window, $limit));
        usleep(500000);
        // At t=1.25, c=2 (the 3 from before t - 1 will drop off).
        $this->assertFalse($limiter->limited_count('ghi', $window, $limit));
        $limiter->track('ghi', 1);
        // At t=1.25, c=3, limited again.
        $this->assertTrue($limiter->limited_count('ghi', $window, $limit));
    }
}

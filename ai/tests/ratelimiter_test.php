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

namespace core_ai;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/testing/classes/incrementing_clock.php');

/**
 * Test ai subsystem rate limiter methods.
 *
 * @package    core_ai
 * @copyright  2024 Matt Porritt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core_ai\ratelimiter
 */
final class ratelimiter_test extends \advanced_testcase {

    /** @var \cache_application Cache instance for rate limiter. */
    private \cache_application $cache;

    /**
     * Set up before tests.
     */
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest(true);
        ratelimiter::reset_instance(); // Reset the singleton instance.
    }

    /**
     * Test global rate limit is enforced correctly.
     */
    public function test_global_rate_limit(): void {
        $ratelimiter = ratelimiter::get_instance();
        $component = 'testcomponent';
        $ratelimit = 5;

        // Make 5 requests, all should be allowed.
        for ($i = 0; $i < 5; $i++) {
            $this->assertTrue($ratelimiter->check_global_rate_limit($component, $ratelimit));
        }

        // The 6th request should be denied.
        $this->assertFalse($ratelimiter->check_global_rate_limit($component, $ratelimit));
    }

    /**
     * Test user rate limit is enforced correctly.
     */
    public function test_user_rate_limit(): void {
        $ratelimiter = ratelimiter::get_instance();
        $component = 'testcomponent';
        $ratelimit = 3;
        $userid = 12345;

        // Make 3 requests for the user, all should be allowed.
        for ($i = 0; $i < 3; $i++) {
            $this->assertTrue($ratelimiter->check_user_rate_limit($component, $ratelimit, $userid));
        }

        // The 4th request should be denied.
        $this->assertFalse($ratelimiter->check_user_rate_limit($component, $ratelimit, $userid));
    }

    /**
     * Test global rate limit resets after time window.
     */
    public function test_global_rate_limit_reset(): void {
        $clock = new \incrementing_clock(0);
        $ratelimiter = ratelimiter::get_instance($clock);
        $component = 'testcomponent';
        $ratelimit = 3;

        // Make 3 requests, all should be allowed.
        for ($i = 0; $i < 3; $i++) {
            $this->assertTrue($ratelimiter->check_global_rate_limit($component, $ratelimit));
        }

        // Simulate moving time forward by TIME_WINDOW to reset the rate limit.
        $clock->set_to(ratelimiter::TIME_WINDOW + 1);

        // The next request should be allowed again.
        $this->assertTrue($ratelimiter->check_global_rate_limit($component, $ratelimit));
    }

    /**
     * Test user rate limit resets after time window.
     */
    public function test_user_rate_limit_reset(): void {
        $clock = new \incrementing_clock(0);
        $ratelimiter = ratelimiter::get_instance($clock);
        $component = 'testcomponent';
        $ratelimit = 3;
        $userid = 12345;

        // Make 3 requests for the user, all should be allowed.
        for ($i = 0; $i < 3; $i++) {
            $this->assertTrue($ratelimiter->check_user_rate_limit($component, $ratelimit, $userid));
        }

        // Simulate moving time forward by TIME_WINDOW to reset the rate limit.
        $clock->set_to(ratelimiter::TIME_WINDOW + 1);

        // The next user request should be allowed again.
        $this->assertTrue($ratelimiter->check_user_rate_limit($component, $ratelimit, $userid));
    }
}

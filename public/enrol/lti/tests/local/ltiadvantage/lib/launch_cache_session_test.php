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

namespace enrol_lti\local\ltiadvantage\lib;

/**
 * Tests for the launch_cache_session class.
 *
 * @package enrol_lti
 * @copyright 2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \enrol_lti\local\ltiadvantage\lib\launch_cache_session
 */
final class launch_cache_session_test extends \advanced_testcase {

    /**
     * Test that the session cache, and in particular distinct object instances, can cache and retrieve launch data.
     *
     * Using different objects simulates the kind of usage we expect: uses across different requests.
     *
     * @covers ::cacheLaunchData
     */
    public function test_cache_launch_data(): void {
        $lcs = new launch_cache_session();
        $lcs->cacheLaunchData('TestKey', ['JWT body' => 'xxx']);

        $lcs2 = new launch_cache_session();
        $this->assertEquals(['JWT body' => 'xxx'], $lcs2->getLaunchData('TestKey'));
        $this->assertNull($lcs2->getLaunchData('TestKey123'));
    }

    /**
     * Test that the session cache, and in particular distinct object instances, can cache and check the nonce data.
     *
     * Using different objects simulates the kind of usage we expect: uses across different requests.
     *
     * @covers ::cacheNonce
     */
    public function test_cache_and_check_nonce(): void {
        $lcs = new launch_cache_session();
        $lcs->cacheNonce('my_nonce_123', 'my_state_234');

        $lcs2 = new launch_cache_session();
        $this->assertTrue($lcs2->checkNonceIsValid('my_nonce_123', 'my_state_234'));
        $this->assertFalse($lcs2->checkNonceIsValid('different_nonce', 'my_state_234'));
        $this->assertFalse($lcs2->checkNonceIsValid('my_nonce_123', 'different_state'));
    }

    /**
     * Test that the session cache, and in particular distinct object instances, can purge cached launch data.
     *
     * Using different objects simulates the kind of usage we expect: uses across different requests.
     *
     * @covers ::purge
     */
    public function test_purge(): void {
        $lcs = new launch_cache_session();
        $lcs->cacheLaunchData('TestKey', ['JWT body' => 'xxx']);

        $lcs2 = new launch_cache_session();
        $this->assertEquals(['JWT body' => 'xxx'], $lcs2->getLaunchData('TestKey'));
        $lcs2->purge();
        $this->assertNull($lcs2->getLaunchData('TestKey'));
    }
}

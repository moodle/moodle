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

namespace factor_token;

/**
 * Tests for MFA manager class.
 *
 * @package     factor_token
 * @author      Peter Burnett <peterburnett@catalyst-au.net>
 * @author      Kevin Pham <kevinpham@catalyst-au.net>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class factor_test extends \advanced_testcase {

    /**
     * Holds specific requested factor, which is token factor.
     *
     * @var \factor_token\factor $factor
     */
    public \factor_token\factor $factor;

    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
        $this->factor = new \factor_token\factor('token');
    }

    /**
     * Test calculating expiry time in general
     *
     * @covers ::calculate_expiry_time
     * @return void
     */
    public function test_calculate_expiry_time_in_general(): void {
        $timestamp = 1642213800; // 1230 UTC.

        set_config('expireovernight', 0, 'factor_token');
        $method = new \ReflectionMethod($this->factor, 'calculate_expiry_time');

        // Test that non-overnight timestamps are just exactly as configured.
        // We don't need to care about 0 or negative ints, they will just make the cookie expire immediately.
        $expiry = $method->invoke($this->factor, $timestamp);
        $this->assertEquals(DAYSECS, $expiry[1]);

        set_config('expiry', HOURSECS, 'factor_token');
        $expiry = $method->invoke($this->factor, $timestamp);
        $this->assertGreaterThan(HOURSECS - 30, $expiry[1]);
        $this->assertLessThan(HOURSECS + 30, $expiry[1]);

        set_config('expireovernight', 1, 'factor_token');
        // Manually calculate the next reset time.
        $reset = strtotime('tomorrow 0200', $timestamp);
        $resetdelta = $reset - $timestamp;
        // Confirm that a timestamp that doesnt reach reset time.
        if ($timestamp + HOURSECS < $reset) {
            $expiry = $method->invoke($this->factor, $timestamp);
            $this->assertGreaterThan(HOURSECS - 30, $expiry[1]);
            $this->assertLessThan(HOURSECS + 30, $expiry[1]);
        }

        set_config('expiry', 2 * DAYSECS, 'factor_token');
        // Now confirm that the returned expiry is less than the absolute amount.
        $expiry = $method->invoke($this->factor, $timestamp);
        $this->assertGreaterThan(DAYSECS, $expiry[1]);
        $this->assertLessThan(2 * DAYSECS, $expiry[1]);
        $this->assertGreaterThan($resetdelta + DAYSECS - 30, $expiry[1]);
        $this->assertLessThan($resetdelta + DAYSECS + 30, $expiry[1]);
    }

    /**
     * Everything should end at 2am unless adding the hours lands it between
     * 0 <= x < 2am, which in that case it should just expire using the raw
     * value, provided it never goes past raw value expiry time, and when it
     * needs to be 2am, it's 2am on the following morning.
     *
     * @covers ::calculate_expiry_time
     * @param int $timestamp
     * @dataProvider timestamp_provider
     */
    public function test_calculate_expiry_time_for_overnight_expiry_with_one_day_expiry($timestamp): void {
        // Setup configuration.
        $method = new \ReflectionMethod($this->factor, 'calculate_expiry_time');
        set_config('expireovernight', 1, 'factor_token');
        set_config('expiry', DAYSECS, 'factor_token');

        // All the results here, should be for 2am the following morning from the timestamp provided.
        $expiry = $method->invoke($this->factor, $timestamp);
        list($expiresat, $secondstillexpiry) = $expiry;

        // Calculate the expected raw expiry if not considering 'overnight'.
        $timezone = \core_date::get_user_timezone_object();
        $datetime = new \DateTime();
        $datetime->setTimezone($timezone);

        $rawexpiry = $timestamp + DAYSECS;
        $datetime->setTimestamp($rawexpiry);
        $rawhour = $datetime->format('H');
        $rawminute = $datetime->format('m');

        // Sanity check, that the $secondstillexpiry is in the appropriate ranges.
        $this->assertGreaterThan(0, $secondstillexpiry);
        $this->assertLessThan(DAYSECS + 1, $secondstillexpiry);

        if ($rawhour >= 0 && $rawhour < 2 || $rawhour == 2 && $rawminute == 0) {
            // Should just use expiry time, if the hours will land between 0 and 2am.
            $this->assertEquals($datetime->getTimestamp(), $expiresat);
            // Ensure the $secondstillexpiry is calculated correctly.
            $this->assertEquals($expiresat - $timestamp, $secondstillexpiry);
        } else {
            // Otherwise it should fall on 2am the following day.
            $followingdayattwoam = strtotime('tomorrow 0200', $timestamp);
            $this->assertEquals($followingdayattwoam, $expiresat);
            // Ensure the $secondstillexpiry is calculated correctly.
            $this->assertEquals($followingdayattwoam - $timestamp, $secondstillexpiry);
        }
    }

    /**
     * Everything should end at 2am unless adding the hours lands it between
     * 0 <= x < 2am, which in that case it should just expire using the raw
     * value, provided it never goes past raw value expiry time, and when it
     * needs to be 2am, it's 2am on the morning after tomorrow.
     *
     * @covers ::calculate_expiry_time
     * @param int $timestamp
     * @dataProvider timestamp_provider
     */
    public function test_calculate_expiry_time_for_overnight_expiry_with_two_day_expiry($timestamp): void {
        // Setup configuration.
        $method = new \ReflectionMethod($this->factor, 'calculate_expiry_time');
        set_config('expireovernight', 1, 'factor_token');
        set_config('expiry', 2 * DAYSECS, 'factor_token');

        // All the results here, should be for 2am the following morning from the timestamp provided.
        $expiry = $method->invoke($this->factor, $timestamp);
        list($expiresat, $secondstillexpiry) = $expiry;

        // Calculate the expected raw expiry if not considering 'overnight'.
        $timezone = \core_date::get_user_timezone_object();
        $datetime = new \DateTime();
        $datetime->setTimezone($timezone);

        $rawexpiry = $timestamp + (2 * DAYSECS);
        $datetime->setTimestamp($rawexpiry);
        $rawhour = $datetime->format('H');
        $rawminute = $datetime->format('m');

        // Sanity check, that the $secondstillexpiry is in the appropriate ranges.
        $this->assertGreaterThan(0, $secondstillexpiry);
        $this->assertLessThan((2 * DAYSECS) + 1, $secondstillexpiry);

        if ($rawhour >= 0 && $rawhour < 2 || $rawhour == 2 && $rawminute == 0) {
            // Should just use expiry time, if the hours will land between 0 and 2am.
            $this->assertEquals($datetime->getTimestamp(), $expiresat);
            // Ensure the $secondstillexpiry is calculated correctly.
            $this->assertEquals($expiresat - $timestamp, $secondstillexpiry);
        } else {
            // Otherwise it should fall on 2am the following day after tomorrow.
            $followingdayattwoam = strtotime('tomorrow 0200', $timestamp) + DAYSECS;
            $this->assertEquals($followingdayattwoam, $expiresat);
            // Ensure the $secondstillexpiry is calculated correctly.
            $this->assertEquals($followingdayattwoam - $timestamp, $secondstillexpiry);
        }

        // Expiry should always be more than one day for an expiry duration of
        // more than 1 day, but the overnight check should apply for the
        // duration of the final night.
        $this->assertGreaterThan(DAYSECS, $secondstillexpiry);
    }

    /**
     * This should check if the 3am expiry is pushed back to 2am as expected, but everything else appears as expected
     *
     * @covers ::calculate_expiry_time
     * @param int $timestamp
     * @dataProvider timestamp_provider
     */
    public function test_calculate_expiry_time_for_overnight_expiry_with_three_hour_expiry($timestamp): void {
        // Setup configuration.
        $method = new \ReflectionMethod($this->factor, 'calculate_expiry_time');
        set_config('expireovernight', 1, 'factor_token');
        set_config('expiry', 3 * HOURSECS, 'factor_token');

        // All the results here, should be for 2am the following morning from the timestamp provided.
        $expiry = $method->invoke($this->factor, $timestamp);
        list($expiresat, $secondstillexpiry) = $expiry;

        // Calculate the expected raw expiry if not considering 'overnight'.
        $timezone = \core_date::get_user_timezone_object();
        $datetime = new \DateTime();
        $datetime->setTimezone($timezone);

        $rawexpiry = $timestamp + (3 * HOURSECS);
        $datetime->setTimestamp($rawexpiry);

        // Sanity check, that the $secondstillexpiry is in the appropriate ranges.
        $this->assertGreaterThan(0, $secondstillexpiry);
        $this->assertLessThan((3 * HOURSECS) + 1, $secondstillexpiry);

        // If the raw timestamp of the expiry, is less than tomorrow at 2am,
        // then use the raw expiry time.
        $followingdayattwoam = strtotime('tomorrow 0200', $timestamp);
        if ($datetime->getTimestamp() < $followingdayattwoam) {
            $this->assertEquals($datetime->getTimestamp(), $expiresat);
            // Ensure the $secondstillexpiry is calculated correctly.
            $this->assertEquals($expiresat - $timestamp, $secondstillexpiry);
        } else {
            // Otherwsie it should be pushed back to 2am.
            $this->assertEquals($followingdayattwoam, $expiresat);
            // Ensure the $secondstillexpiry is calculated correctly.
            $this->assertEquals($followingdayattwoam - $timestamp, $secondstillexpiry);
        }
    }

    /**
     * Only relevant based on the hour padding used, which is currently set to 2 hours (2am).
     *
     * @covers ::calculate_expiry_time
     * @param int $timestamp
     * @dataProvider timestamp_provider
     */
    public function test_calculate_expiry_time_for_overnight_expiry_with_an_hour_expiry($timestamp): void {
        // Setup configuration.
        $method = new \ReflectionMethod($this->factor, 'calculate_expiry_time');
        set_config('expireovernight', 1, 'factor_token');
        set_config('expiry', HOURSECS, 'factor_token');

        // All the results here, should be for 2am the following morning from the timestamp provided.
        $expiry = $method->invoke($this->factor, $timestamp);
        list($expiresat, $secondstillexpiry) = $expiry;

        // Calculate the expected raw expiry if not considering 'overnight'.
        $timezone = \core_date::get_user_timezone_object();
        $datetime = new \DateTime();
        $datetime->setTimezone($timezone);

        $rawexpiry = $timestamp + HOURSECS;
        $datetime->setTimestamp($rawexpiry);

        // Sanity check, that the $secondstillexpiry is in the appropriate ranges.
        $this->assertGreaterThan(0, $secondstillexpiry);
        $this->assertLessThan(HOURSECS + 1, $secondstillexpiry);

        // If the raw timestamp of the expiry, is less than tomorrow at 2am,
        // then use the raw expiry time.
        $followingdayattwoam = strtotime('tomorrow 0200', $timestamp);
        if ($datetime->getTimestamp() < $followingdayattwoam) {
            $this->assertEquals($datetime->getTimestamp(), $expiresat);
            // Ensure the $secondstillexpiry is calculated correctly.
            $this->assertEquals($expiresat - $timestamp, $secondstillexpiry);
        } else {
            // Otherwsie it should be pushed back to 2am.
            $this->assertEquals($followingdayattwoam, $expiresat);
            // Ensure the $secondstillexpiry is calculated correctly.
            $this->assertEquals($followingdayattwoam - $timestamp, $secondstillexpiry);
        }
    }

    /**
     * Timestamps for a 24 hour period starting from a fixed time.
     * Increments by 30 minutes to cover half hour and hour cases.
     * Starting timestamp: 2022-01-15 07:30:00 Australia/Melbourne time.
     */
    public static function timestamp_provider(): array {
        $starttimestamp = 1642192200;
        foreach (range(0, 23) as $i) {
            $timestamps[] = [$starttimestamp + ($i * HOURSECS)];
            $timestamps[] = [$starttimestamp + ($i * HOURSECS) + (30 * MINSECS)];
        }
        return $timestamps;
    }
}

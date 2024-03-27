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

declare(strict_types=1);

namespace core_reportbuilder\local\filters;

use advanced_testcase;
use lang_string;
use core_reportbuilder\local\report\filter;

/**
 * Unit tests for date report filter
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\local\filters\base
 * @covers      \core_reportbuilder\local\filters\date
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class date_test extends advanced_testcase {

    /**
     * Data provider for {@see test_get_sql_filter_simple}
     *
     * @return array
     */
    public function get_sql_filter_simple_provider(): array {
        return [
            [date::DATE_ANY, true],
            [date::DATE_NOT_EMPTY, true],
            [date::DATE_EMPTY, false],
        ];
    }

    /**
     * Test getting filter SQL
     *
     * @param int $operator
     * @param bool $expectuser
     *
     * @dataProvider get_sql_filter_simple_provider
     */
    public function test_get_sql_filter_simple(int $operator, bool $expectuser): void {
        global $DB;

        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user([
            'timecreated' => 12345,
        ]);

        $filter = new filter(
            date::class,
            'test',
            new lang_string('yes'),
            'testentity',
            'timecreated'
        );

        // Create instance of our filter, passing given operator.
        [$select, $params] = date::create($filter)->get_sql_filter([
            $filter->get_unique_identifier() . '_operator' => $operator,
        ]);

        $usernames = $DB->get_fieldset_select('user', 'username', $select, $params);
        if ($expectuser) {
            $this->assertContains($user->username, $usernames);
        } else {
            $this->assertNotContains($user->username, $usernames);
        }
    }

    /**
     * Test getting filter SQL while specifying a date range
     */
    public function test_get_sql_filter_date_range(): void {
        global $DB;

        $this->resetAfterTest();

        $userone = $this->getDataGenerator()->create_user(['timecreated' => 50]);
        $usertwo = $this->getDataGenerator()->create_user(['timecreated' => 100]);

        $filter = new filter(
            date::class,
            'test',
            new lang_string('yes'),
            'testentity',
            'timecreated'
        );

        // Create instance of our date range filter.
        [$select, $params] = date::create($filter)->get_sql_filter([
            $filter->get_unique_identifier() . '_operator' => date::DATE_RANGE,
            $filter->get_unique_identifier() . '_from' => 80,
            $filter->get_unique_identifier() . '_to' => 120,
        ]);

        // The only matching user should be our first test user.
        $usernames = $DB->get_fieldset_select('user', 'username', $select, $params);
        $this->assertEquals([$usertwo->username], $usernames);
    }

    /**
     * Data provider for {@see test_get_sql_filter_current_week}
     *
     * @return array
     */
    public function get_sql_filter_current_week_provider(): array {
        return array_map(static function(int $day): array {
            return [$day];
        }, range(0, 6));
    }

    /**
     * Test getting filter SQL for the current week. Note that relative dates are hard to test, here we are asserting that
     * the current time is always within the current week regardless of calendar configuration/preferences
     *
     * @param int $startweekday
     *
     * @dataProvider get_sql_filter_current_week_provider
     */
    public function test_get_sql_filter_current_week(int $startweekday): void {
        global $DB;

        $this->resetAfterTest();

        set_config('calendar_startwday', $startweekday);

        $user = $this->getDataGenerator()->create_user(['timecreated' => time()]);

        $filter = new filter(
            date::class,
            'test',
            new lang_string('yes'),
            'testentity',
            'timecreated'
        );

        [$select, $params] = date::create($filter)->get_sql_filter([
            $filter->get_unique_identifier() . '_operator' => date::DATE_CURRENT,
            $filter->get_unique_identifier() . '_unit' => date::DATE_UNIT_WEEK,
        ]);

        $matchingusers = $DB->get_fieldset_select('user', 'username', $select, $params);
        $this->assertContains($user->username, $matchingusers);
    }

    /**
     * Data provider for {@see test_get_sql_filter_current_week_no_match}
     *
     * @return array
     */
    public function get_sql_filter_current_week_no_match_provider(): array {
        $data = [];

        // For each day, create provider data for -/+ 8 days.
        foreach (range(0, 6) as $day) {
            $data = array_merge($data, [
                [$day, '-8 day'],
                [$day, '+8 day'],
            ]);
        }

        return $data;
    }

    /**
     * Test getting filter SQL for the current week excludes dates that don't match (outside week time range)
     *
     * @param int $startweekday
     * @param string $timecreated Relative time suitable for passing to {@see strtotime}
     *
     * @dataProvider get_sql_filter_current_week_no_match_provider
     */
    public function test_get_sql_filter_current_week_no_match(int $startweekday, string $timecreated): void {
        global $DB;

        $this->resetAfterTest();

        set_config('calendar_startwday', $startweekday);

        $usertimecreated = strtotime($timecreated);
        $user = $this->getDataGenerator()->create_user(['timecreated' => $usertimecreated]);

        $filter = new filter(
            date::class,
            'test',
            new lang_string('yes'),
            'testentity',
            'timecreated'
        );

        [$select, $params] = date::create($filter)->get_sql_filter([
            $filter->get_unique_identifier() . '_operator' => date::DATE_CURRENT,
            $filter->get_unique_identifier() . '_unit' => date::DATE_UNIT_WEEK,
        ]);

        $matchingusers = $DB->get_fieldset_select('user', 'username', $select, $params);
        $this->assertNotContains($user->username, $matchingusers);
    }

    /**
     * Data provider for {@see test_get_sql_filter_relative}
     *
     * @return array
     */
    public function get_sql_filter_relative_provider(): array {
        return [
            'Before hour' => [date::DATE_BEFORE, 1, date::DATE_UNIT_HOUR, '-90 minute'],
            'Before day' => [date::DATE_BEFORE, 1, date::DATE_UNIT_DAY, '-25 hour'],
            'Before week' => [date::DATE_BEFORE, 1, date::DATE_UNIT_WEEK, '-10 day'],
            'Before month' => [date::DATE_BEFORE, 1, date::DATE_UNIT_MONTH, '-7 week'],
            'Before year' => [date::DATE_BEFORE, 1, date::DATE_UNIT_YEAR, '-15 month'],
            'Before two hours' => [date::DATE_BEFORE, 2, date::DATE_UNIT_HOUR, '-150 minute'],
            'Before two days' => [date::DATE_BEFORE, 2, date::DATE_UNIT_DAY, '-50 hour'],
            'Before two weeks' => [date::DATE_BEFORE, 2, date::DATE_UNIT_WEEK, '-20 day'],
            'Before two months' => [date::DATE_BEFORE, 2, date::DATE_UNIT_MONTH, '-15 week'],
            'Before two years' => [date::DATE_BEFORE, 2, date::DATE_UNIT_YEAR, '-30 month'],

            'After hour' => [date::DATE_AFTER, 1, date::DATE_UNIT_HOUR, '+90 minute'],
            'After day' => [date::DATE_AFTER, 1, date::DATE_UNIT_DAY, '+25 hour'],
            'After week' => [date::DATE_AFTER, 1, date::DATE_UNIT_WEEK, '+10 day'],
            'After month' => [date::DATE_AFTER, 1, date::DATE_UNIT_MONTH, '+7 week'],
            'After year' => [date::DATE_AFTER, 1, date::DATE_UNIT_YEAR, '+15 month'],
            'After two hours' => [date::DATE_AFTER, 2, date::DATE_UNIT_HOUR, '+150 minute'],
            'After two days' => [date::DATE_AFTER, 2, date::DATE_UNIT_DAY, '+50 hour'],
            'After two weeks' => [date::DATE_AFTER, 2, date::DATE_UNIT_WEEK, '+20 day'],
            'After two months' => [date::DATE_AFTER, 2, date::DATE_UNIT_MONTH, '+15 week'],
            'After two years' => [date::DATE_AFTER, 2, date::DATE_UNIT_YEAR, '+30 month'],

            'Last hour' => [date::DATE_LAST, 1, date::DATE_UNIT_HOUR, '-30 minute'],
            'Last day' => [date::DATE_LAST, 1, date::DATE_UNIT_DAY, '-6 hour'],
            'Last week' => [date::DATE_LAST, 1, date::DATE_UNIT_WEEK, '-3 day'],
            'Last month' => [date::DATE_LAST, 1, date::DATE_UNIT_MONTH, '-3 week'],
            'Last year' => [date::DATE_LAST, 1, date::DATE_UNIT_YEAR, '-6 month'],
            'Last two hours' => [date::DATE_LAST, 2, date::DATE_UNIT_HOUR, '-90 minute'],
            'Last two days' => [date::DATE_LAST, 2, date::DATE_UNIT_DAY, '-25 hour'],
            'Last two weeks' => [date::DATE_LAST, 2, date::DATE_UNIT_WEEK, '-10 day'],
            'Last two months' => [date::DATE_LAST, 2, date::DATE_UNIT_MONTH, '-7 week'],
            'Last two years' => [date::DATE_LAST, 2, date::DATE_UNIT_YEAR, '-15 month'],

            // Current week is tested separately.
            'Current hour' => [date::DATE_CURRENT, null, date::DATE_UNIT_HOUR],
            'Current day' => [date::DATE_CURRENT, null, date::DATE_UNIT_DAY],
            'Current month' => [date::DATE_CURRENT, null, date::DATE_UNIT_MONTH],
            'Current year' => [date::DATE_CURRENT, null, date::DATE_UNIT_YEAR],

            'Next hour' => [date::DATE_NEXT, 1, date::DATE_UNIT_HOUR, '+30 minute'],
            'Next day' => [date::DATE_NEXT, 1, date::DATE_UNIT_DAY, '+6 hour'],
            'Next week' => [date::DATE_NEXT, 1, date::DATE_UNIT_WEEK, '+3 day'],
            'Next month' => [date::DATE_NEXT, 1, date::DATE_UNIT_MONTH, '+3 week'],
            'Next year' => [date::DATE_NEXT, 1, date::DATE_UNIT_YEAR, '+6 month'],
            'Next two hours' => [date::DATE_NEXT, 2, date::DATE_UNIT_HOUR, '+90 minute'],
            'Next two days' => [date::DATE_NEXT, 2, date::DATE_UNIT_DAY, '+25 hour'],
            'Next two weeks' => [date::DATE_NEXT, 2, date::DATE_UNIT_WEEK, '+10 day'],
            'Next two months' => [date::DATE_NEXT, 2, date::DATE_UNIT_MONTH, '+7 week'],
            'Next two years' => [date::DATE_NEXT, 2, date::DATE_UNIT_YEAR, '+15 month'],

            'In the past' => [date::DATE_PAST, null, null, '-3 hour'],
            'In the future' => [date::DATE_FUTURE, null, null, '+3 hour'],
        ];
    }

    /**
     * Unit tests for filtering relative dates
     *
     * @param int $operator
     * @param int|null $unitvalue
     * @param int|null $unit
     * @param string|null $timecreated Relative time suitable for passing to {@see strtotime} (or null for current time)
     *
     * @dataProvider get_sql_filter_relative_provider
     */
    public function test_get_sql_filter_relative(int $operator, ?int $unitvalue, ?int $unit, ?string $timecreated = null): void {
        global $DB;

        $this->resetAfterTest();

        $usertimecreated = ($timecreated !== null ? strtotime($timecreated) : time());
        $user = $this->getDataGenerator()->create_user(['timecreated' => $usertimecreated]);

        $filter = new filter(
            date::class,
            'test',
            new lang_string('yes'),
            'testentity',
            'timecreated'
        );

        [$select, $params] = date::create($filter)->get_sql_filter([
            $filter->get_unique_identifier() . '_operator' => $operator,
            $filter->get_unique_identifier() . '_value' => $unitvalue,
            $filter->get_unique_identifier() . '_unit' => $unit,
        ]);

        $matchingusers = $DB->get_fieldset_select('user', 'username', $select, $params);
        $this->assertContains($user->username, $matchingusers);
    }
}

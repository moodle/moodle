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

namespace tool_usertours;

use tool_usertours\local\filter\accessdate;

/**
 * Tests for time filter.
 *
 * @package    tool_usertours
 * @copyright  2019 Tom Dickman <tomdickman@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers    \tool_usertours\local\filter\accessdate
 */
final class accessdate_filter_test extends \advanced_testcase {
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest(true);
    }

    /**
     * Data Provider for filter_matches method.
     *
     * @return array
     */
    public static function filter_matches_provider(): array {
        return [
            'No config set; Matches' => [
                [],
                [],
                true,
            ],
            'Filter is not enabled; Match' => [
                ['filter_accessdate' => accessdate::FILTER_ACCOUNT_CREATION, 'filter_accessdate_range' => 90 * DAYSECS,
                    'filter_accessdate_enabled' => 0, ],
                ['timecreated' => time() - (89 * DAYSECS)],
                true,
            ],
            'Filter is not enabled (tour would not be displayed if it was); Match' => [
                ['filter_accessdate' => accessdate::FILTER_ACCOUNT_CREATION, 'filter_accessdate_range' => 90 * DAYSECS,
                    'filter_accessdate_enabled' => 0, ],
                ['timecreated' => time() - (91 * DAYSECS)],
                true,
            ],
            'Inside range of account creation date; Match' => [
                ['filter_accessdate' => accessdate::FILTER_ACCOUNT_CREATION, 'filter_accessdate_range' => 90 * DAYSECS,
                    'filter_accessdate_enabled' => 1, ],
                ['timecreated' => time() - (89 * DAYSECS)],
                true,
            ],
            'Outside range of account creation date; No match' => [
                ['filter_accessdate' => accessdate::FILTER_ACCOUNT_CREATION, 'filter_accessdate_range' => 90 * DAYSECS,
                    'filter_accessdate_enabled' => 1, ],
                ['timecreated' => time() - (91 * DAYSECS)],
                false,
            ],
            'Inside range of first login date; Match' => [
                ['filter_accessdate' => accessdate::FILTER_FIRST_LOGIN, 'filter_accessdate_range' => 90 * DAYSECS,
                    'filter_accessdate_enabled' => 1, ],
                ['firstaccess' => time() - (89 * DAYSECS)],
                true,
            ],
            'Outside range of first login date; No match' => [
                ['filter_accessdate' => accessdate::FILTER_FIRST_LOGIN, 'filter_accessdate_range' => 90 * DAYSECS,
                    'filter_accessdate_enabled' => 1, ],
                ['firstaccess' => time() - (91 * DAYSECS)],
                false,
            ],
            'Inside range of last login date; Match' => [
                ['filter_accessdate' => accessdate::FILTER_LAST_LOGIN, 'filter_accessdate_range' => 90 * DAYSECS,
                    'filter_accessdate_enabled' => 1, ],
                ['lastlogin' => time() - (89 * DAYSECS)],
                true,
            ],
            'Outside range of last login date; No match' => [
                ['filter_accessdate' => accessdate::FILTER_LAST_LOGIN, 'filter_accessdate_range' => 90 * DAYSECS,
                    'filter_accessdate_enabled' => 1, ],
                ['lastlogin' => time() - (91 * DAYSECS)],
                false,
            ],
            'User has never logged in, but tour should be visible; Match' => [
                ['filter_accessdate' => accessdate::FILTER_LAST_LOGIN, 'filter_accessdate_range' => 90 * DAYSECS,
                    'filter_accessdate_enabled' => 1, ],
                ['lastlogin' => 0, 'timecreated' => time() - (89 * DAYSECS)],
                true,
            ],
            'User has never logged in, and tour should not be visible; No match' => [
                ['filter_accessdate' => accessdate::FILTER_LAST_LOGIN, 'filter_accessdate_range' => 90 * DAYSECS,
                    'filter_accessdate_enabled' => 1, ],
                ['lastlogin' => 0, 'timecreated' => time() - (91 * DAYSECS)],
                false,
            ],
        ];
    }

    /**
     * Test filter matches.
     *
     * @dataProvider    filter_matches_provider
     *
     * @param array $filtervalues the filter values set.
     * @param array $userstate any user state required for test.
     * @param bool $expected result expected.
     */
    public function test_filter_matches($filtervalues, $userstate, $expected): void {
        $course = $this->getDataGenerator()->create_course();
        $context = \context_course::instance($course->id);

        $user = $this->getDataGenerator()->create_user($userstate);
        $this->setUser($user);

        $tour = new tour();
        $tour->set_filter_values('accessdate', $filtervalues);

        $this->assertEquals($expected, accessdate::filter_matches($tour, $context));
    }
}

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

namespace core_calendar\output;

use DateTime;

/**
 * Tests for humandate class.
 *
 * @covers     \core_calendar\output\humandate
 * @package    core_calendar
 * @category   test
 * @copyright  2025 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class humandate_test extends \advanced_testcase {

    /**
     * Initialize.
     */
    protected function setUp(): void {
        parent::setUp();

        // Mock the clock.
        $this->setTimezone('Australia/Perth');
    }

    /**
     * Test export_for_template() method.
     *
     * @dataProvider provider_export_for_template
     * @param int $addseconds The number of seconds to add to the current time.
     * @param bool $userelatives Whether to use relative dates.
     * @param string|null $date For relative dates, the expected string (Tomorrow, Today, Yesterday).
     * @param bool $ispast Whether the date is in the past.
     * @param bool $needtitle Whether the date needs a title.
     * @param bool $isnear Whether the date is near.
     * @param string $userdateformat The user date expected format.
     */
    public function test_export_for_template(
        int $addseconds,
        bool $userelatives,
        ?string $date,
        bool $ispast,
        bool $needtitle,
        bool $isnear,
        string $userdateformat,
    ): void {
        global $PAGE;

        $this->resetAfterTest();

        // 26 February 2025 15:59:59 (GMT).
        $clock = $this->mock_clock_with_frozen(1740585599);
        $renderer = $PAGE->get_renderer('core');

        $timestamp = $clock->time() + $addseconds;
        $expected = [
            'timestamp' => $timestamp,
            'date' => $date,
            'userdate' => userdate($timestamp, get_string($userdateformat)),
            'ispast' => $ispast,
            'needtitle' => $needtitle,
            'isnear' => $isnear,
        ];
        $humandate = humandate::create_from_timestamp($timestamp);
        $humandate->set_use_relatives($userelatives);
        $result = $humandate->export_for_template($renderer);
        $this->compare_output($expected, $result, $userelatives);
    }

    /**
     * Data provider.
     *
     * @return array
     */
    public static function provider_export_for_template(): array {
        return [
            'Now with relatives' => [
                'addseconds' => 0,
                'userelatives' => true,
                'date' => 'Today',
                'ispast' => false,
                'needtitle' => true,
                'isnear' => false,
                'userdateformat' => 'strftimedayshort',
            ],
            'Tomorrow with relatives' => [
                'addseconds' => DAYSECS,
                'userelatives' => true,
                'date' => 'Tomorrow',
                'ispast' => false,
                'needtitle' => true,
                'isnear' => false,
                'userdateformat' => 'strftimedayshort',
            ],
            'Yesterday with relatives' => [
                'addseconds' => -DAYSECS,
                'userelatives' => true,
                'date' => 'Yesterday',
                'ispast' => true,
                'needtitle' => true,
                'isnear' => false,
                'userdateformat' => 'strftimedayshort',
            ],
            'One hour future with relatives' => [
                'addseconds' => HOURSECS,
                'userelatives' => true,
                'date' => 'Tomorrow',
                'ispast' => false,
                'needtitle' => true,
                'isnear' => true,
                'userdateformat' => 'strftimedayshort',
            ],
            'One hour past with relatives' => [
                'addseconds' => -HOURSECS,
                'userelatives' => true,
                'date' => 'Today',
                'ispast' => true,
                'needtitle' => true,
                'isnear' => false,
                'userdateformat' => 'strftimedayshort',
            ],
            'Now without relatives' => [
                'addseconds' => 0,
                'userelatives' => false,
                'date' => 'Today',
                'ispast' => false,
                'needtitle' => false,
                'isnear' => false,
                'userdateformat' => 'strftimedayshort',
            ],
            'Tomorrow without relatives' => [
                'addseconds' => DAYSECS,
                'userelatives' => false,
                'date' => 'Tomorrow',
                'ispast' => false,
                'needtitle' => false,
                'isnear' => false,
                'userdateformat' => 'strftimedayshort',
            ],
            'Yesterday without relatives' => [
                'addseconds' => -DAYSECS,
                'userelatives' => false,
                'date' => 'Yesterday',
                'ispast' => true,
                'needtitle' => false,
                'isnear' => false,
                'userdateformat' => 'strftimedayshort',
            ],
            'One hour future without relatives' => [
                'addseconds' => HOURSECS,
                'userelatives' => false,
                'date' => null,
                'ispast' => false,
                'needtitle' => false,
                'isnear' => true,
                'userdateformat' => 'strftimedayshort',
            ],
            'One hour past without relatives' => [
                'addseconds' => -HOURSECS,
                'userelatives' => false,
                'date' => null,
                'ispast' => true,
                'needtitle' => false,
                'isnear' => false,
                'userdateformat' => 'strftimedayshort',
            ],
            'one year from now' => [
                'addseconds' => YEARSECS,
                'userelatives' => false,
                'date' => null,
                'ispast' => false,
                'needtitle' => false,
                'isnear' => false,
                'userdateformat' => 'strftimedaydate',
            ],
            'one year in the past' => [
                'addseconds' => -YEARSECS,
                'userelatives' => false,
                'date' => null,
                'ispast' => true,
                'needtitle' => false,
                'isnear' => false,
                'userdateformat' => 'strftimedaydate',
            ],
        ];
    }

    public function test_create_from_timestamp(): void {
        $this->resetAfterTest();

        $clock = $this->mock_clock_with_frozen();
        $timestamp = $clock->time();
        $humandate = humandate::create_from_timestamp($timestamp);
        $this->assertInstanceOf(humandate::class, $humandate);
    }

    public function test_create_from_datetime(): void {
        $this->resetAfterTest();

        $humandate = humandate::create_from_datetime(new DateTime());
        $this->assertInstanceOf(humandate::class, $humandate);
    }

    /**
     * Compare humandate output.
     *
     * @param array $expected The expected output.
     * @param array $actual The actual output.
     * @param bool $userelatives Whether to use relative dates.
     */
    protected function compare_output(
        array $expected,
        array $actual,
        bool $userelatives,
    ): void {
        $fields = ['timestamp', 'userdate', 'ispast', 'needtitle'];
        foreach ($fields as $field) {
            $this->assertEquals($expected[$field], $actual[$field], "Field $field does not match");
        }

        if ($expected['isnear']) {
            $this->assertEquals($expected[$field], $actual[$field], "Field isnear does not match");
        } else {
            $this->assertArrayNotHasKey('isnear', $actual);
        }

        if (!is_null($expected['date'])) {
            if ($userelatives) {
                $this->assertStringContainsString($expected['date'], $actual['date']);
            } else {
                $this->assertStringNotContainsString($expected['date'], $actual['date']);
            }
        } else {
            $this->assertStringNotContainsString('Today', $actual['date']);
            $this->assertStringNotContainsString('Yesterday', $actual['date']);
            $this->assertStringNotContainsString('Tomorrow', $actual['date']);
        }
    }
}

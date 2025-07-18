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

namespace core_calendar\external;

use core_calendar\output\humandate;

/**
 * Tests for calendar
 *
 * @covers     \core_calendar\external\humandate_exporter
 * @package    core_calendar
 * @category   test
 * @copyright  2025 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class humandate_exporter_test extends \advanced_testcase {
    /**
     * Test exporting human-readable dates for external use.
     *
     * @dataProvider provider_export
     * @param int $addseconds The number of seconds to add to the current time.
     * @param bool $userelatives Whether to use relative dates.
     * @param string|null $date For relative dates, the expected string (Tomorrow, Today, Yesterday).
     * @param bool $ispast Whether the date is in the past.
     * @param bool $needtitle Whether the date needs a title.
     * @param bool $isnear Whether the date is near.
     * @param string $userdateformat The user date expected format.
     */
    public function test_export(
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

        $humandate = humandate::create_from_timestamp($timestamp);
        $humandate->set_use_relatives($userelatives);

        $icon = $humandate->get_near_icon();
        if ($icon) {
            $icondata = $icon->get_exporter()->export($renderer);
        } else {
            $icondata = null;
        }

        $exporter = new humandate_exporter($humandate, ['context' => \context_system::instance()]);
        $data = $exporter->export($renderer);

        $this->assertObjectHasProperty('timestamp', $data);
        $this->assertObjectHasProperty('userdate', $data);
        $this->assertObjectHasProperty('date', $data);
        $this->assertObjectHasProperty('time', $data);
        $this->assertObjectHasProperty('needtitle', $data);
        $this->assertObjectHasProperty('link', $data);
        $this->assertObjectHasProperty('ispast', $data);
        $this->assertObjectHasProperty('isnear', $data);
        $this->assertObjectHasProperty('nearicon', $data);
        $this->assertCount(9, get_object_vars($data));

        $formatmethod = new \ReflectionMethod(humandate::class, 'format_time');
        $formatmethod->setAccessible(true);

        $expected = [
            'timestamp' => $timestamp,
            'userdate' => userdate($timestamp, get_string($userdateformat)),
            'time' => $formatmethod->invoke($humandate),
            'needtitle' => $needtitle,
            'link' => '',
            'ispast' => $ispast,
            'isnear' => $isnear,
            'nearicon' => $icondata,
        ];
        foreach ($expected as $key => $value) {
            $this->assertEquals($value, $data->$key);
        }

        if ($userelatives) {
            $this->assertStringContainsString($date, $data->date);
        } else {
            $this->assertStringContainsString($expected['userdate'], $data->date);
        }
    }

    /**
     * Data provider.
     *
     * @return array
     */
    public static function provider_export(): array {
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
                'date' => null,
                'ispast' => false,
                'needtitle' => false,
                'isnear' => false,
                'userdateformat' => 'strftimedayshort',
            ],
            'Tomorrow without relatives' => [
                'addseconds' => DAYSECS,
                'userelatives' => false,
                'date' => null,
                'ispast' => false,
                'needtitle' => false,
                'isnear' => false,
                'userdateformat' => 'strftimedayshort',
            ],
            'Yesterday without relatives' => [
                'addseconds' => -DAYSECS,
                'userelatives' => false,
                'date' => null,
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
}

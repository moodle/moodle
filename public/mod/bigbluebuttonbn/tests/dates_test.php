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

namespace mod_bigbluebuttonbn;

use advanced_testcase;
use core\activity_dates;

/**
 * Class for unit testing mod_bigbluebutton\dates.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2025 Laurent David <laurent.david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \mod_bigbluebuttonbn\dates
 */
final class dates_test extends advanced_testcase {
    use \mod_bigbluebuttonbn\test\testcase_helper_trait;

    /**
     * Data provider for get_dates_for_module().
     *
     * @return array[]
     */
    public static function get_dates_for_module_provider(): array {
        $clock = \core\di::get(\core\clock::class);
        $now = $clock->time();
        $open = $now - DAYSECS;
        $close = $now + DAYSECS;

        return [
            'Without any dates' => [
                null, null, [],
            ],
            'Only with opening time' => [
                $open,
                null,
                [
                    [
                        'label' => get_string('activitydate:opened', 'course'),
                        'timestamp' => $open,
                        'dataid' => 'timeopen',
                    ],
                ],
            ],
            'Only with closing time' => [
                null,
                $close,
                [
                    [
                        'label' => get_string('activitydate:closes', 'course'),
                        'timestamp' => $close,
                        'dataid' => 'timeclose',
                    ],
                ],
            ],
            'With both times' => [
                $open,
                $close,
                [
                    [
                        'label' => get_string('activitydate:opened', 'course'),
                        'timestamp' => $open,
                        'dataid' => 'timeopen',
                    ],
                    [
                        'label' => get_string('activitydate:closes', 'course'),
                        'timestamp' => $close,
                        'dataid' => 'timeclose',
                    ],
                ],
            ],
            'With both times in the future' => [
                $now + DAYSECS,
                $now + (2 * DAYSECS),
                [
                    [
                        'label' => get_string('activitydate:opens', 'course'),
                        'timestamp' => $now + DAYSECS,
                        'dataid' => 'timeopen',
                    ],
                    [
                        'label' => get_string('activitydate:closes', 'course'),
                        'timestamp' => $now + (2 * DAYSECS),
                        'dataid' => 'timeclose',
                    ],
                ],
            ],
            'With both times in the past' => [
                $now - (2 * DAYSECS),
                $now - DAYSECS,
                [
                    [
                        'label' => get_string('activitydate:opened', 'course'),
                        'timestamp' => $now - (2 * DAYSECS),
                        'dataid' => 'timeopen',
                    ],
                    [
                        'label' => get_string('activitydate:closed', 'course'),
                        'timestamp' => $now - DAYSECS,
                        'dataid' => 'timeclose',
                    ],
                ],
            ],
        ];
    }

    /**
     * Test for get_dates_for_module().
     *
     * @param int|null $open Opening time in the BigBlueButton.
     * @param int|null $close Closing time in the BigBlueButton.
     * @param array $expected The expected value of calling get_dates_for_module()
     * @covers ::get_dates_for_module
     * @dataProvider get_dates_for_module_provider
     */
    public function test_get_dates_for_module(
        ?int $open,
        ?int $close,
        array $expected
    ): void {
        $this->resetAfterTest();
        ['user' => $user, 'cm' => $cm] = $this->setup_instance($open, $close);
        $this->setUser($user);
        $dates = activity_dates::get_dates_for_module($cm, (int) $user->id);

        $this->assertEquals($expected, $dates);
    }


    /**
     * Test for get_open_date().
     *
     * @param int|null $open Opening time in the BigBlueButton.
     * @param int|null $close Closing time in the BigBlueButton.
     * @covers ::get_open_date
     * @dataProvider get_dates_for_module_provider
     */
    public function test_get_open_date(
        ?int $open,
        ?int $close,
    ): void {

        $this->resetAfterTest();
        ['user' => $user, 'cm' => $cm] = $this->setup_instance($open, $close);
        $this->setUser($user);
        $dates = new \mod_bigbluebuttonbn\dates($cm, (int) $user->id);

        $this->assertEquals($open, $dates->get_open_date());
    }

    /**
     * Test for get_close_date().
     *
     * @param int|null $open Opening time in the BigBlueButton.
     * @param int|null $close Closing time in the BigBlueButton.
     * @covers ::get_close_date
     * @dataProvider get_dates_for_module_provider
     */
    public function test_get_close_date(
        ?int $open,
        ?int $close,
    ): void {

        $this->resetAfterTest();
        ['user' => $user, 'cm' => $cm] = $this->setup_instance($open, $close);
        $dates = new \mod_bigbluebuttonbn\dates($cm, (int) $user->id);

        $this->assertEquals($close, $dates->get_close_date());
    }

    /**
     * Setup a BigBlueButton activity instance.
     *
     * @param int|null $open Opening time in the BigBlueButton.
     * @param int|null $close Closing time in the BigBlueButton.
     * @return array with keys 'user' and 'cm'.
     */
    private function setup_instance(
        ?int $open,
        ?int $close,
    ): array {
        $generator = $this->getDataGenerator();

        $course = $generator->create_course();
        $user = $generator->create_user();
        $generator->enrol_user($user->id, $course->id);
        $data = [];
        if ($open !== null) {
            $data['openingtime'] = $open;
        }
        if ($close !== null) {
            $data['closingtime'] = $close;
        }
        $this->setAdminUser();
        [$bbactivitycontext, $bbactivitycm, $bbactivity] = $this->create_instance(
            $course,
            $data
        );
        return ['user' => $user, 'cm' => $bbactivitycm];
    }
}

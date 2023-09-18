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
 * This file contains unit test related to xAPI library.
 *
 * @package    core_xapi
 * @copyright  2020 Ferran Recio
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_xapi\local\statement;

use advanced_testcase;
use core_xapi\xapi_exception;

/**
 * Contains test cases for testing statement result class.
 *
 * @package    core_xapi
 * @since      Moodle 3.9
 * @copyright  2020 Ferran Recio
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class item_result_test extends advanced_testcase {

    /**
     * Test item creation.
     */
    public function test_creation(): void {

        $data = $this->get_generic_data();
        $item = item_result::create_from_data($data);

        $this->assertEquals(json_encode($item), json_encode($data));
        $this->assertNull($item->get_duration());

        $score = $item->get_score();
        $this->assertEquals(json_encode($score), json_encode($data->score));

    }

    /**
     * Return a generic data to create a valid item.
     *
     * @return \stdClass the creation data
     */
    private function get_generic_data(): \stdClass {
        return (object) [
            'score' => (object)[
                'min' => 0,
                'max' => 100,
                'raw' => 50,
                'scaled' => 0.5,
            ],
            'completion' => true,
            'success' => true,
        ];
    }

    /**
     * Test for duration values.
     *
     * @dataProvider duration_values_data
     * @param string|null $duration specified duration
     * @param int|null $seconds calculated seconds
     * @param bool $exception if exception is expected
     */
    public function test_duration_values(?string $duration, ?int $seconds, bool $exception): void {

        if ($exception) {
            $this->expectException(xapi_exception::class);
        }

        $data = $this->get_generic_data();
        if ($duration !== null) {
            $data->duration = $duration;
        }
        $item = item_result::create_from_data($data);
        $this->assertEquals($seconds, $item->get_duration());
    }

    /**
     * Data provider for the test_duration_values tests.
     *
     * @return array
     */
    public function duration_values_data() : array {
        return [
            'No duration' => [
                null, null, false
            ],
            'Empty duration' => [
                '', null, false
            ],
            '1 minute duration' => [
                'PT1M', 60, false
            ],
            '1 hour duration' => [
                'PT1H', 3600, false
            ],
            '1 second duration' => [
                'PT1S', 1, false
            ],
            '1.11 second duration (dot variant)' => [
                'PT1.11S', 1, false
            ],
            '1,11 second duration (comma variant)' => [
                'PT1.11S', 1, false
            ],
            '90 minutes 5 seconds duration' => [
                'PT1H30M5S', 5405, false
            ],
            '90 minutes 05 seconds duration' => [
                'PT1H30M05S', 5405, false
            ],
            'Half year duration' => [
                'P0.5Y', null, true
            ],
            'Incorrect format' => [
                'INVALID', null, true
            ],
        ];
    }
}

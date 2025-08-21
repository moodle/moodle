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

namespace mod_bigbluebuttonbn;

/**
 * Genarator tests class for mod_bigbluebuttonbn.
 *
 * @package    mod_bigbluebuttonbn
 * @category   test
 * @copyright  2025 Laurent David <laurent.david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class generator_test extends \advanced_testcase {
    /**
     * Test the creation of a bigbluebuttonbn instance.
     * @covers \mod_bigbluebuttonbn_generator::create_instance
     */
    public function test_create_instance(): void {
        $db = \core\di::get(\moodle_database::class);
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $bigbluebuttonbn = $this->getDataGenerator()->create_module('bigbluebuttonbn', ['course' => $course]);
        $records = $db->get_records('bigbluebuttonbn', ['course' => $course->id], 'id');
        $this->assertEquals(1, count($records));
        $this->assertTrue(array_key_exists($bigbluebuttonbn->id, $records));

        $params = ['course' => $course->id, 'name' => 'Another bigbluebuttonbn'];
        $bigbluebuttonbn = $this->getDataGenerator()->create_module('bigbluebuttonbn', $params);
        $records = $db->get_records('bigbluebuttonbn', ['course' => $course->id], 'id');
        $this->assertEquals(2, count($records));
        $this->assertEquals('Another bigbluebuttonbn', $records[$bigbluebuttonbn->id]->name);
    }


    /**
     * Test the creation of a bigbluebuttonbn instance with a custom name.
     *
     * @param string|int $opening The opening time as a timestamp or human-readable date.
     * @param string|int $closing The closing time as a timestamp or human-readable date
     * @param int $expectedopening The expected opening time as a timestamp.
     * @param int $expectedclosing The expected closing time as a timestamp.
     * @covers \mod_bigbluebuttonbn_generator::create_instance
     * @dataProvider provider_create_instance_with_name
     */
    public function test_create_instance_with_dates(
        string|int $opening,
        string|int $closing,
        int $expectedopening,
        int $expectedclosing
    ): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $params = ['course' => $course->id, 'openingtime' => $opening, 'closingtime' => $closing];
        $bigbluebuttonbn = $this->getDataGenerator()->create_module('bigbluebuttonbn', $params);
        $instance = \mod_bigbluebuttonbn\instance::get_from_instanceid($bigbluebuttonbn->id);
        $this->assertEquals($expectedopening, $instance->get_instance_var('openingtime'));
        $this->assertEquals($expectedclosing, $instance->get_instance_var('closingtime'));
    }

    /**
     * Data provider for test_create_instance_with_dates.
     *
     * @return array[]
     */
    public static function provider_create_instance_with_name(): array {
        global $CFG;
        require_once($CFG->libdir . '/testing/classes/frozen_clock.php');
        $clock = new \frozen_clock();
        \core\di::set(\core\clock::class, $clock);
        $opening = $clock->time();
        $closing = $opening + DAYSECS;
        return [
            'Timestamp' => [
                $opening,
                $closing,
                $opening,
                $closing,
            ],
            'Human date' => [
                userdate($opening, get_string('strftimedatetimeaccurate', 'langconfig')),
                userdate($closing, get_string('strftimedatetimeaccurate', 'langconfig')),
                $opening,
                $closing,
            ],
        ];
    }
}

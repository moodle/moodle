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
 * Unit tests for the block_timeline implementation of the privacy API.
 *
 * @package    block_timeline
 * @category   test
 * @copyright  2018 Peter Dias <peter@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace block_timeline\privacy;

defined('MOODLE_INTERNAL') || die();

use core_privacy\local\request\writer;
use block_timeline\privacy\provider;

/**
 * Unit tests for the block_timeline implementation of the privacy API.
 *
 * @copyright  2018 Peter Dias <peter@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider_test extends \core_privacy\tests\provider_testcase {

    /**
     * Ensure that export_user_preferences returns no data if the user has not visited the myoverview block.
     */
    public function test_export_user_preferences_no_pref() {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        provider::export_user_preferences($user->id);
        $writer = writer::with_context(\context_system::instance());
        $this->assertFalse($writer->has_any_data());
    }

    /**
     * Test the export_user_preferences given different inputs
     *
     * @param string $type The name of the user preference to get/set
     * @param string $value The value you are storing
     * @param string $expected The expected value override
     *
     * @dataProvider user_preference_provider
     */
    public function test_export_user_preferences($type, $value, $expected) {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        set_user_preference($type, $value, $user);
        provider::export_user_preferences($user->id);
        $writer = writer::with_context(\context_system::instance());
        $blockpreferences = $writer->get_user_preferences('block_timeline');
        if (!$expected) {
            $expected = get_string($value, 'block_timeline');
        }
        $this->assertEquals($expected, $blockpreferences->{$type}->value);
    }

    /**
     * Create an array of valid user preferences for the timeline block.
     *
     * @return array Array of valid user preferences.
     */
    public function user_preference_provider() {
        return array(
            array('block_timeline_user_sort_preference', 'sortbydates', ''),
            array('block_timeline_user_sort_preference', 'sortbycourses', ''),
            array('block_timeline_user_sort_preference', 'next7days', ''),
            array('block_timeline_user_sort_preference', 'all', ''),
            array('block_timeline_user_limit_preference', 5, 5),
        );
    }
}

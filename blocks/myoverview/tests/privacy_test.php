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
 * Unit tests for the block_myoverview implementation of the privacy API.
 *
 * @package    block_myoverview
 * @category   test
 * @copyright  2018 Peter Dias <peter@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
use \core_privacy\local\request\writer;
use \block_myoverview\privacy\provider;
/**
 * Unit tests for the block_myoverview implementation of the privacy API.
 *
 * @copyright  2018 Peter Dias <peter@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_myoverview_privacy_testcase extends \core_privacy\tests\provider_testcase {
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
     *
     * @dataProvider user_preference_provider
     */
    public function test_export_user_preferences($type, $value, $expected) {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        set_user_preference($type, $value, $user);
        provider::export_user_preferences($user->id);
        $writer = writer::with_context(\context_system::instance());
        $blockpreferences = $writer->get_user_preferences('block_myoverview');
        if (!$expected) {
            $expected = get_string($value, 'block_myoverview');
        }
        $this->assertEquals($expected, $blockpreferences->{$type}->value);
    }

    /**
     * Create an array of valid user preferences for the myoverview block.
     *
     * @return array Array of valid user preferences.
     */
    public function user_preference_provider() {
        return array(
            array('block_myoverview_user_sort_preference', 'lastaccessed', ''),
            array('block_myoverview_user_sort_preference', 'title', ''),
            array('block_myoverview_user_grouping_preference', 'all', ''),
            array('block_myoverview_user_grouping_preference', 'inprogress', ''),
            array('block_myoverview_user_grouping_preference', 'future', ''),
            array('block_myoverview_user_grouping_preference', 'past', ''),
            array('block_myoverview_user_view_preference', 'card', ''),
            array('block_myoverview_user_view_preference', 'list', ''),
            array('block_myoverview_user_view_preference', 'summary', ''),
            array('block_myoverview_user_paging_preference', 12, 12)
        );
    }

    public function test_export_user_preferences_with_hidden_courses() {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $name = "block_myoverview_hidden_course_1";

        set_user_preference($name, 1, $user);
        provider::export_user_preferences($user->id);
        $writer = writer::with_context(\context_system::instance());
        $blockpreferences = $writer->get_user_preferences('block_myoverview');

        $this->assertEquals(
            get_string("privacy:request:preference:set", 'block_myoverview', (object) [
                'name' => $name,
                'value' => 1,
            ]),
            $blockpreferences->{$name}->description
        );
    }
}
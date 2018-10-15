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

defined('MOODLE_INTERNAL') || die();

use \core_privacy\local\request\writer;
use \block_timeline\privacy\provider;

/**
 * Unit tests for the block_timeline implementation of the privacy API.
 *
 * @copyright  2018 Peter Dias <peter@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_timeline_privacy_testcase extends \core_privacy\tests\provider_testcase {

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
     * Test that the preference courses is exported properly.
     */
    public function test_export_user_preferences_date_sort_preference() {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        set_user_preference('block_timeline_user_sort_preference', 'sortbydates', $user);

        provider::export_user_preferences($user->id);
        $writer = writer::with_context(\context_system::instance());
        $blockpreferences = $writer->get_user_preferences('block_timeline');
        $this->assertEquals('Sort by dates', $blockpreferences->block_timeline_user_sort_preference->value);
    }

    /**
     * Test that the preference timeline is exported properly.
     */
    public function test_export_user_preferences_course_sort_preference() {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        set_user_preference('block_timeline_user_sort_preference', 'sortbycourses', $user);

        provider::export_user_preferences($user->id);
        $writer = writer::with_context(\context_system::instance());
        $blockpreferences = $writer->get_user_preferences('block_timeline');
        $this->assertEquals('Sort by courses', $blockpreferences->block_timeline_user_sort_preference->value);
    }

    /**
     * Test that the preference timeline is exported properly.
     */
    public function test_export_user_preferences_7day_filter_preference() {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        set_user_preference('block_timeline_user_filter_preference', 'next7days', $user);

        provider::export_user_preferences($user->id);
        $writer = writer::with_context(\context_system::instance());
        $blockpreferences = $writer->get_user_preferences('block_timeline');
        $this->assertEquals('Next 7 days', $blockpreferences->block_timeline_user_filter_preference->value);
    }

    /**
     * Test that the preference timeline is exported properly.
     */
    public function test_export_user_preferences_all_filter_preference() {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        set_user_preference('block_timeline_user_filter_preference', 'all', $user);

        provider::export_user_preferences($user->id);
        $writer = writer::with_context(\context_system::instance());
        $blockpreferences = $writer->get_user_preferences('block_timeline');
        $this->assertEquals('All', $blockpreferences->block_timeline_user_filter_preference->value);
    }
}

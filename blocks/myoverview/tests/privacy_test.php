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
 * @copyright  2018 Adrian Greeve <adriangreeve.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use \core_privacy\local\request\writer;
use \block_myoverview\privacy\provider;

/**
 * Unit tests for the block_myoverview implementation of the privacy API.
 *
 * @copyright  2018 Adrian Greeve <adriangreeve.com>
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
     * Test that the preference courses is exported properly.
     */
    public function test_export_user_preferences_course_preference() {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        set_user_preference('block_myoverview_last_tab', 'courses', $user);

        provider::export_user_preferences($user->id);
        $writer = writer::with_context(\context_system::instance());
        $blockpreferences = $writer->get_user_preferences('block_myoverview');
        $this->assertEquals('courses', $blockpreferences->block_myoverview_last_tab->value);
    }

    /**
     * Test that the preference timeline is exported properly.
     */
    public function test_export_user_preferences_timeline_preference() {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        set_user_preference('block_myoverview_last_tab', 'timeline', $user);

        provider::export_user_preferences($user->id);
        $writer = writer::with_context(\context_system::instance());
        $blockpreferences = $writer->get_user_preferences('block_myoverview');
        $this->assertEquals('timeline', $blockpreferences->block_myoverview_last_tab->value);
    }
}

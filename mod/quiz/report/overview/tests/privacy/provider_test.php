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
 * Privacy provider tests.
 *
 * @package    quiz_overview
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace quiz_overview\privacy;

use core_privacy\local\metadata\collection;
use quiz_overview\privacy\provider;
use core_privacy\local\request\writer;
use core_privacy\local\request\transform;

defined('MOODLE_INTERNAL') || die();

/**
 * Privacy provider tests class.
 *
 * @package    quiz_overview
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider_test extends \core_privacy\tests\provider_testcase {
    /**
     * When no preference exists, there should be no export.
     */
    public function test_preference_unset() {
        global $USER;

        $this->resetAfterTest();
        $this->setAdminUser();

        provider::export_user_preferences($USER->id);

        $this->assertFalse(writer::with_context(\context_system::instance())->has_any_data());
    }

    /**
     * Preference does exist.
     */
    public function test_preference_yes() {
        $this->resetAfterTest();

        // Create test user, add some preferences.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        set_user_preference('quiz_overview_slotmarks', 1, $user);

        // Switch to admin user (so we can validate preferences of the correct user are being exported).
        $this->setAdminUser();

        // Export test users preferences.
        provider::export_user_preferences($user->id);

        $writer = writer::with_context(\context_system::instance());
        $this->assertTrue($writer->has_any_data());

        $preferences = $writer->get_user_preferences('quiz_overview');
        $this->assertNotEmpty($preferences->slotmarks);
        $this->assertEquals(transform::yesno(1), $preferences->slotmarks->value);
        $description = get_string('privacy:preference:slotmarks:yes', 'quiz_overview');
        $this->assertEquals($description, $preferences->slotmarks->description);
    }

    /**
     * Preference does exist and is no.
     */
    public function test_preference_no() {
        $this->resetAfterTest();

        // Create test user, add some preferences.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        set_user_preference('quiz_overview_slotmarks', 0);

        // Switch to admin user (so we can validate preferences of the correct user are being exported).
        $this->setAdminUser();

        // Export test users preferences.
        provider::export_user_preferences($user->id);

        $writer = writer::with_context(\context_system::instance());
        $this->assertTrue($writer->has_any_data());

        $preferences = $writer->get_user_preferences('quiz_overview');
        $this->assertNotEmpty($preferences->slotmarks);
        $this->assertEquals(transform::yesno(0), $preferences->slotmarks->value);
        $description = get_string('privacy:preference:slotmarks:no', 'quiz_overview');
        $this->assertEquals($description, $preferences->slotmarks->description);
    }
}

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
 * Unit tests for the tool_usertours implementation of the privacy API.
 *
 * @package    tool_usertours
 * @category   test
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use \core_privacy\local\metadata\collection;
use \core_privacy\local\request\writer;
use \tool_usertours\tour;
use \tool_usertours\privacy\provider;

/**
 * Unit tests for the tool_usertours implementation of the privacy API.
 *
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_usertours_privacy_provider_testcase extends \core_privacy\tests\provider_testcase {

    /**
     * Ensure that get_metadata exports valid content.
     */
    public function test_get_metadata() {
        $items = new collection('tool_usertours');
        $result = provider::get_metadata($items);
        $this->assertSame($items, $result);
        $this->assertInstanceOf(collection::class, $result);
    }

    /**
     * Ensure that export_user_preferences returns no data if the user has completed no tours.
     */
    public function test_export_user_preferences_no_pref() {
        $user = \core_user::get_user_by_username('admin');
        provider::export_user_preferences($user->id);

        $writer = writer::with_context(\context_system::instance());

        $this->assertFalse($writer->has_any_data());
    }

    /**
     * Ensure that export_user_preferences returns request completion data.
     */
    public function test_export_user_preferences_completed() {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        $alltours = $DB->get_records('tool_usertours_tours');
        $tourdata = reset($alltours);

        $user = \core_user::get_user_by_username('admin');
        $tour = tour::instance($tourdata->id);
        $tour->mark_user_completed();
        provider::export_user_preferences($user->id);

        $writer = writer::with_context(\context_system::instance());

        $this->assertTrue($writer->has_any_data());
        $prefs = $writer->get_user_preferences('tool_usertours');

        $this->assertCount(1, (array) $prefs);
    }

    /**
     * Ensure that export_user_preferences returns request completion data.
     */
    public function test_export_user_preferences_requested() {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        $alltours = $DB->get_records('tool_usertours_tours');
        $tourdata = reset($alltours);

        $user = \core_user::get_user_by_username('admin');
        $tour = tour::instance($tourdata->id);
        $tour->mark_user_completed();
        $tour->request_user_reset();
        provider::export_user_preferences($user->id);

        $writer = writer::with_context(\context_system::instance());

        $this->assertTrue($writer->has_any_data());
        $prefs = $writer->get_user_preferences('tool_usertours');

        $this->assertCount(2, (array) $prefs);
    }

    /**
     * Make sure we are exporting preferences for the correct user
     */
    public function test_export_user_preferences_correct_user(): void {
        global $DB;

        $this->resetAfterTest();

        $alltours = $DB->get_records('tool_usertours_tours');
        $tour = tour::instance(reset($alltours)->id);

        // Create test user, mark them as having completed the tour.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);
        $tour->mark_user_completed();

        // Switch to admin user, mark them as having reset the tour.
        $this->setAdminUser();
        $tour->request_user_reset();

        // Export test users preferences.
        provider::export_user_preferences($user->id);

        $writer = writer::with_context(\context_system::instance());
        $this->assertTrue($writer->has_any_data());

        $prefs = $writer->get_user_preferences('tool_usertours');
        $this->assertCount(1, (array) $prefs);

        // We should have received back the "completed tour" preference of the test user.
        $this->assertStringStartsWith('You last marked the "' . $tour->get_name() . '" user tour as completed on',
            reset($prefs)->description);
    }

    /**
     * Ensure that export_user_preferences excludes deleted tours.
     */
    public function test_export_user_preferences_deleted_tour() {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        $user = \core_user::get_user_by_username('admin');

        $alltours = $DB->get_records('tool_usertours_tours');

        $tour1 = tour::instance(array_shift($alltours)->id);
        $tour1->mark_user_completed();

        $tour2 = tour::instance(array_shift($alltours)->id);
        $tour2->mark_user_completed();
        $tour2->remove();

        $writer = writer::with_context(\context_system::instance());

        provider::export_user_preferences($user->id);
        $this->assertTrue($writer->has_any_data());

        // We should have one preference.
        $prefs = $writer->get_user_preferences('tool_usertours');
        $this->assertCount(1, (array) $prefs);

        // The preference should be related to the first tour.
        $this->assertContains($tour1->get_name(), reset($prefs)->description);
    }
}

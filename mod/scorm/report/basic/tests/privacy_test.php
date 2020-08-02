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
 * Unit tests for the scormreport_basic implementation of the privacy API.
 *
 * @package    scormreport_basic
 * @category   test
 * @copyright  2018 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use core_privacy\local\request\writer;
use scormreport_basic\privacy\provider;

/**
 * Unit tests for the scormreport_basic implementation of the privacy API.
 *
 * @copyright  2018 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class scormreport_basic_privacy_testcase extends \core_privacy\tests\provider_testcase {

    /**
     * Basic setup for these tests.
     */
    public function setUp(): void {
        $this->resetAfterTest(true);
    }

    /**
     * Ensure that export_user_preferences returns no data if the user has no data.
     */
    public function test_export_user_preferences_not_defined() {
        $user = \core_user::get_user_by_username('admin');
        provider::export_user_preferences($user->id);

        $writer = writer::with_context(\context_system::instance());
        $this->assertFalse($writer->has_any_data());
    }

    /**
     * Ensure that export_user_preferences returns single preferences.
     */
    public function test_export_user_preferences_single() {
        // Define a user preference.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);
        set_user_preference('scorm_report_detailed', 1);
        set_user_preference('scorm_report_pagesize', 50);

        // Validate exported data.
        provider::export_user_preferences($user->id);
        $context = \context_user::instance($user->id);
        $writer = writer::with_context($context);
        $this->assertTrue($writer->has_any_data());
        $prefs = $writer->get_user_preferences('scormreport_basic');
        $this->assertCount(2, (array) $prefs);
        $this->assertEquals(
            get_string('privacy:metadata:preference:scorm_report_detailed', 'scormreport_basic'),
            $prefs->scorm_report_detailed->description
        );
        $this->assertEquals(get_string('yes'), $prefs->scorm_report_detailed->value);
        $this->assertEquals(
            get_string('privacy:metadata:preference:scorm_report_pagesize', 'scormreport_basic'),
            $prefs->scorm_report_pagesize->description
        );
        $this->assertEquals(50, $prefs->scorm_report_pagesize->value);
    }
}

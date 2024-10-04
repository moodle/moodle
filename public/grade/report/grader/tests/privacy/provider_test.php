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
 * Unit tests for the gradereport_grader implementation of the privacy API.
 *
 * @package    gradereport_grader
 * @category   test
 * @copyright  2018 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace gradereport_grader\privacy;

defined('MOODLE_INTERNAL') || die();

use core_privacy\local\metadata\collection;
use core_privacy\local\request\writer;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\deletion_criteria;
use gradereport_grader\privacy\provider;

/**
 * Unit tests for the gradereport_grader implementation of the privacy API.
 *
 * @copyright  2018 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class provider_test extends \core_privacy\tests\provider_testcase {

    /**
     * Basic setup for these tests.
     */
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest(true);
    }


    /**
     * Ensure that export_user_preferences returns no data if the user has no data.
     */
    public function test_export_user_preferences_not_defined(): void {
        $user = \core_user::get_user_by_username('admin');
        provider::export_user_preferences($user->id);

        $writer = writer::with_context(\context_system::instance());
        $this->assertFalse($writer->has_any_data());
    }

    /**
     * Ensure that export_user_preferences returns single preferences.
     * These preferences can be set on each course, but the value is shared in the whole site.
     */
    public function test_export_user_preferences_single(): void {
        // Create test user, add some preferences.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        set_user_preference('grade_report_meanselection', GRADE_REPORT_MEAN_GRADED, $user);
        set_user_preference('grade_report_studentsperpage', 50, $user);

        // Switch to admin user (so we can validate preferences of our test user are still exported).
        $this->setAdminUser();

        // Validate exported data for our test user.
        provider::export_user_preferences($user->id);
        $context = \context_user::instance($user->id);
        /** @var \core_privacy\tests\request\content_writer $writer */
        $writer = writer::with_context($context);
        $this->assertTrue($writer->has_any_data());
        $prefs = $writer->get_user_preferences('gradereport_grader');
        $this->assertCount(2, (array) $prefs);
        $this->assertEquals(get_string('meangraded', 'grades'), $prefs->grade_report_meanselection->value);
        $this->assertEquals(50, $prefs->grade_report_studentsperpage->value);
    }

    /**
     * Ensure that export_user_preferences returns preferences.
     */
    public function test_export_user_preferences_multiple(): void {
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        // Set preference for an invalid course.
        set_user_preference('grade_report_grader_collapsed_categories42', serialize(['gradesonly' => []]));

        // Set preferences for a couple of valid courses.
        $courseone = $this->getDataGenerator()->create_course();
        $courseonepreferencename = "grade_report_grader_collapsed_categories{$courseone->id}";
        $courseonepreferencevalue = serialize(['aggregatesonly' => [], 'gradesonly' => []]);
        set_user_preference($courseonepreferencename, $courseonepreferencevalue);

        $coursetwo = $this->getDataGenerator()->create_course();
        $coursetwopreferencename = "grade_report_grader_collapsed_categories{$coursetwo->id}";
        $coursetwopreferencevalue = serialize(['gradesonly' => [], 'aggregatesonly' => []]);
        set_user_preference($coursetwopreferencename, $coursetwopreferencevalue);

        // Validate exported data.
        provider::export_user_preferences($user->id);
        $context = \context_user::instance($user->id);
        /** @var \core_privacy\tests\request\content_writer $writer */
        $writer = writer::with_context($context);
        $this->assertTrue($writer->has_any_data());
        $prefs = $writer->get_user_preferences('gradereport_grader');
        $this->assertCount(2, (array) $prefs);

        $this->assertEquals($courseonepreferencevalue, $prefs->{$courseonepreferencename}->value);
        $this->assertEquals(
            get_string('privacy:request:preference:grade_report_grader_collapsed_categories', 'gradereport_grader', [
                'name' => $courseone->fullname,
            ]),
            $prefs->{$courseonepreferencename}->description,
        );

        $this->assertEquals($coursetwopreferencevalue, $prefs->{$coursetwopreferencename}->value);
        $this->assertEquals(
            get_string('privacy:request:preference:grade_report_grader_collapsed_categories', 'gradereport_grader', [
                'name' => $coursetwo->fullname,
            ]),
            $prefs->{$coursetwopreferencename}->description,
        );
    }
}

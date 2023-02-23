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

namespace core_courseformat\privacy;

use context_course;
use core_privacy\local\request\writer;

/**
 * Privacy tests for core_courseformat.
 *
 * @package    core_courseformat
 * @category   test
 * @copyright  2021 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider_test extends \core_privacy\tests\provider_testcase {

    /**
     * Test for provider::test_export_user_preferences().
     */
    public function test_export_user_preferences() {
        $this->resetAfterTest();

        // Test setup.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        course_create_sections_if_missing($course, [0, 1, 2]);
        $user = $generator->create_and_enrol($course, 'student');

        $prefix = provider::SECTION_PREFERENCES_PREFIX;
        $preference = "{$prefix}_{$course->id}";
        $value = "Something";
        $preferencestring = get_string("preference:$prefix", 'courseformat', $course->fullname);

        // Add a user home page preference for the User.
        set_user_preference($preference , $value, $user);

        // Test the user preferences export contains 1 user preference record for the User.
        provider::export_user_preferences($user->id);
        $coursecontext = context_course::instance($course->id);
        $writer = writer::with_context($coursecontext);
        $this->assertTrue($writer->has_any_data());

        $exportedpreferences = $writer->get_user_preferences('core_courseformat');
        $this->assertCount(1, (array) $exportedpreferences);
        $this->assertEquals(
            $value,
            $exportedpreferences->$preference->value
        );
        $this->assertEquals(
            $preferencestring,
            $exportedpreferences->{$preference}->description
        );
    }
}

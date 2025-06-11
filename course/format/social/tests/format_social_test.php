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

namespace format_social;

/**
 * Social course format related unit tests.
 *
 * @package    format_social
 * @copyright  2023 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \format_social
 */
final class format_social_test extends \advanced_testcase {

    /**
     * Test for get_view_url().
     *
     * @covers ::get_view_url
     */
    public function test_get_view_url(): void {
        global $CFG;
        $this->resetAfterTest();

        // Generate a course with two sections (0 and 1) and two modules.
        $generator = $this->getDataGenerator();
        $course1 = $generator->create_course(['format' => 'social']);
        course_create_sections_if_missing($course1, [0, 1]);

        $data = (object)['id' => $course1->id];
        $format = course_get_format($course1);
        $format->update_course_format_options($data);

        // In page.
        $this->assertNotEmpty($format->get_view_url(null));
        $this->assertNotEmpty($format->get_view_url(0));
        $this->assertNotEmpty($format->get_view_url(1));

        // Navigation.
        $this->assertStringContainsString('course/view.php', $format->get_view_url(0));
        $this->assertStringContainsString('course/view.php', $format->get_view_url(1));
        $this->assertStringContainsString('course/view.php', $format->get_view_url(0, ['navigation' => 1]));
        $this->assertStringContainsString('course/view.php', $format->get_view_url(1, ['navigation' => 1]));
        $this->assertStringContainsString('course/view.php', $format->get_view_url(0, ['sr' => 1]));
        $this->assertStringContainsString('course/view.php', $format->get_view_url(1, ['sr' => 1]));
        $this->assertStringContainsString('course/view.php', $format->get_view_url(0, ['sr' => 0]));
        $this->assertStringContainsString('course/view.php', $format->get_view_url(1, ['sr' => 0]));
    }

    /**
     * Test get_required_jsfiles().
     *
     * @covers ::get_required_jsfiles
     */
    public function test_get_required_jsfiles(): void {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator();

        $course = $generator->create_course(['format' => 'social']);
        $format = course_get_format($course);
        $this->assertEmpty($format->get_required_jsfiles());
    }
}

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

namespace core;

/**
 * Unit tests for lib/outputrenderers.
 *
 * @package   core
 * @category  test
 * @copyright 2023 Rodrigo Mady
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core_renderer
 */
class outputrenderers_test extends \advanced_testcase {
    /**
     * Test generated url from course image.
     *
     * @covers ::get_generated_url_for_course
     */
    public function test_get_generated_url_for_course_image() {
        global $OUTPUT;

        $this->resetAfterTest();

        $course = self::getDataGenerator()->create_course();
        $context = \context_course::instance($course->id, IGNORE_MISSING);

        // Get the image with correct course context.
        $courseimage = $OUTPUT->get_generated_url_for_course($context);
        $url = "https://www.example.com/moodle/pluginfile.php/{$context->id}/course/generated/course.svg";
        $this->assertEquals($url, $courseimage);
    }
}

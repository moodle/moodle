<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace core\hook;

/**
 * Test hook for external routing
 *
 * @coversDefaultClass \core_course\hook\before_course_viewed
 *
 * @package    core
 * @copyright  2024 Jacob Viertel
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class before_course_viewed_test extends \advanced_testcase {
    /**
     * Test hook description.
     * @covers ::get_hook_description
     */
    public function test_hook_description(): void {
        $description = \core_course\hook\before_course_viewed::get_hook_description();
        $this->assertIsString($description);
        $this->assertSame('Hook dispatched just before viewing a course in course/view.php.', $description);
    }

    /**
     * Test hook tags.
     * @covers ::get_hook_tags
     */
    public function test_hook_tags(): void {
        $tags = \core_course\hook\before_course_viewed::get_hook_tags();
        $this->assertIsArray($tags);
        $this->assertContains('course', $tags);
        $this->assertContains('view', $tags);
        $this->assertContains('routing', $tags);
        $this->assertContains('navigation', $tags);
    }

    /**
     * Test hook initialization with course data.
     * @covers ::__construct
     */
    public function test_hook_initialization(): void {
        $course = new \stdClass();
        $course->id = 1;
        $course->fullname = 'Test Course';

        $hook = new \core_course\hook\before_course_viewed($course);

        $this->assertInstanceOf(\core_course\hook\before_course_viewed::class, $hook);
        $this->assertSame($course, $hook->course);
    }

    /**
     * Test hook dispatch and propagation.
     * @covers \core\hook\manager::dispatch
     */
    public function test_hook_dispatch(): void {
        $course = new \stdClass();
        $course->id = 1;
        $course->fullname = 'Test Course';

        $hook = new \core_course\hook\before_course_viewed($course);

        $count = 0;
        $receivedhook = null;
        $testcallback = function (\core_course\hook\before_course_viewed $hook) use (&$receivedhook, &$count): void {
            $count++;
            $receivedhook = $hook;
        };
        $this->redirectHook(\core_course\hook\before_course_viewed::class, $testcallback);

        \core\hook\manager::get_instance()->dispatch($hook);

        $this->assertSame(1, $count);
        $this->assertSame($hook, $receivedhook);
    }
}

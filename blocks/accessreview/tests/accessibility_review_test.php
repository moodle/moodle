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

namespace block_accessreview;

use ReflectionClass;
use advanced_testcase;
use block_accessreview;
use context_course;

/**
 * PHPUnit block_accessibility_review tests
 *
 * @package   block_accessreview
 * @copyright  2020 onward: Learning Technology Services, www.lts.ie
 * @author     Jay Churchward (jay.churchward@poetopensource.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \block_accessreview
 */
class accessibility_review_test extends advanced_testcase {
    public static function setUpBeforeClass(): void {
        require_once(__DIR__ . '/../../moodleblock.class.php');
        require_once(__DIR__ . '/../block_accessreview.php');
    }

    public function test_get_toggle_link() {
        $rc = new ReflectionClass(block_accessreview::class);
        $rm = $rc->getMethod('get_toggle_link');

        $block = new block_accessreview();
        $output = $rm->invoke($block);
        $this->assertNotEmpty($output);
    }

    public function test_get_download_link() {
        $this->resetAfterTest();

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        $course = $this->getDataGenerator()->create_course();

        // Enrol users in the course.
        $this->getDataGenerator()->enrol_user($user1->id, $course->id, 'teacher');
        $this->getDataGenerator()->enrol_user($user2->id, $course->id, 'student');

        $rc = new ReflectionClass(block_accessreview::class);
        $rm = $rc->getMethod('get_download_link');
        $block = new block_accessreview();

        $this->setUser($user1);
        $result = $rm->invoke($block, context_course::instance($course->id));
        $this->assertNotEmpty($result);

        $this->setUser($user2);
        $result = $rm->invoke($block, context_course::instance($course->id));
        $this->assertEmpty($result);
    }

    public function test_get_report_link() {
        $this->resetAfterTest();

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        $course = $this->getDataGenerator()->create_course();

        // Enrol users in the course.
        $this->getDataGenerator()->enrol_user($user1->id, $course->id, 'teacher');
        $this->getDataGenerator()->enrol_user($user2->id, $course->id, 'student');

        $rc = new ReflectionClass(block_accessreview::class);
        $rm = $rc->getMethod('get_report_link');
        $block = new block_accessreview();

        $this->setUser($user1);
        $result = $rm->invoke($block, context_course::instance($course->id));
        $this->assertNotEmpty($result);

        $this->setUser($user2);
        $result = $rm->invoke($block, context_course::instance($course->id));
        $this->assertEmpty($result);
    }

    /**
     * Test the behaviour of can_block_be_added() method.
     *
     * @covers ::can_block_be_added
     */
    public function test_can_block_be_added(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a course and prepare the page where the block will be added.
        $course = $this->getDataGenerator()->create_course();
        $page = new \moodle_page();
        $page->set_context(context_course::instance($course->id));
        $page->set_pagelayout('course');

        $block = new block_accessreview();

        // If the accessibility tools is enabled, the method should return true.
        set_config('enableaccessibilitytools', true);
        $this->assertTrue($block->can_block_be_added($page));

        // However, if the accessibility tools is disabled, the method should return false.
        set_config('enableaccessibilitytools', false);
        $this->assertFalse($block->can_block_be_added($page));
    }
}

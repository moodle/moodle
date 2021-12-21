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

namespace block_accessreview\tests;

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
 */
class accessibility_review_test extends advanced_testcase {
    public static function setUpBeforeClass(): void {
        require_once(__DIR__ . '/../../moodleblock.class.php');
        require_once(__DIR__ . '/../block_accessreview.php');
    }

    public function test_get_toggle_link() {
        $rc = new ReflectionClass(block_accessreview::class);
        $rm = $rc->getMethod('get_toggle_link');
        $rm->setAccessible(true);

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
        $rm->setAccessible(true);
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
        $rm->setAccessible(true);
        $block = new block_accessreview();

        $this->setUser($user1);
        $result = $rm->invoke($block, context_course::instance($course->id));
        $this->assertNotEmpty($result);

        $this->setUser($user2);
        $result = $rm->invoke($block, context_course::instance($course->id));
        $this->assertEmpty($result);
    }
}

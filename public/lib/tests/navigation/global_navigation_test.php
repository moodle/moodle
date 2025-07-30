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

namespace core\navigation;

use core\tests\navigation\exposed_global_navigation;
use core\tests\navigation\navigation_testcase;

/**
 * Tests for global_navigation.
 *
 * @package    core
 * @category   test
 * @copyright  2025 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(global_navigation::class)]
final class global_navigation_test extends navigation_testcase {
    public function test_module_extends_navigation(): void {
        $node = new exposed_global_navigation();
        // Create an initial tree structure to work with.
        $cat1 = $node->add('category 1', null, navigation_node::TYPE_CATEGORY, null, 'cat1');
        $cat2 = $node->add('category 2', null, navigation_node::TYPE_CATEGORY, null, 'cat2');
        $cat3 = $node->add('category 3', null, navigation_node::TYPE_CATEGORY, null, 'cat3');
        $sub1 = $cat2->add('sub category 1', null, navigation_node::TYPE_CATEGORY, null, 'sub1');
        $sub2 = $cat2->add('sub category 2', null, navigation_node::TYPE_CATEGORY, null, 'sub2');
        $sub3 = $cat2->add('sub category 3', null, navigation_node::TYPE_CATEGORY, null, 'sub3');
        $course1 = $sub2->add('course 1', null, navigation_node::TYPE_COURSE, null, 'course1');
        $course2 = $sub2->add('course 2', null, navigation_node::TYPE_COURSE, null, 'course2');
        $course3 = $sub2->add('course 3', null, navigation_node::TYPE_COURSE, null, 'course3');
        $section1 = $course2->add('section 1', null, navigation_node::TYPE_SECTION, null, 'sec1');
        $section2 = $course2->add('section 2', null, navigation_node::TYPE_SECTION, null, 'sec2');
        $section3 = $course2->add('section 3', null, navigation_node::TYPE_SECTION, null, 'sec3');
        $act1 = $section2->add('activity 1', null, navigation_node::TYPE_ACTIVITY, null, 'act1');
        $act2 = $section2->add('activity 2', null, navigation_node::TYPE_ACTIVITY, null, 'act2');
        $act3 = $section2->add('activity 3', null, navigation_node::TYPE_ACTIVITY, null, 'act3');
        $res1 = $section2->add('resource 1', null, navigation_node::TYPE_RESOURCE, null, 'res1');
        $res2 = $section2->add('resource 2', null, navigation_node::TYPE_RESOURCE, null, 'res2');
        $res3 = $section2->add('resource 3', null, navigation_node::TYPE_RESOURCE, null, 'res3');

        $this->assertTrue($node->exposed_module_extends_navigation('data'));
        $this->assertFalse($node->exposed_module_extends_navigation('test1'));
    }
}

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

    /**
     * Test that subsections with hidden restrictions (eye closed) are not shown in the navigation
     * block, and that subsections with visible restrictions (eye open) still appear.
     */
    public function test_load_section_activities_navigation_hidden_subsection_visibility(): void {
        global $PAGE, $CFG;
        require_once($CFG->dirroot . '/course/lib.php');

        $this->resetAfterTest();
        $this->setAdminUser();
        set_config('enableavailability', 1);

        $generator = $this->getDataGenerator();
        $course = $generator->create_course(['numsections' => 1]);

        $student = $generator->create_user();
        $generator->enrol_user($student->id, $course->id, 'student');

        // Profile condition that can never be met: no test user is assigned this reserved address.
        $nevermatchemail = '{"type":"profile","sf":"email","op":"isequalto","v":"nomail@moodle.invalid"}';
        // Flag showc:[false] = eye closed (restriction hidden from students).
        $eyeclosed = '{"op":"&","c":[' . $nevermatchemail . '],"showc":[false]}';
        // Flag showc:[true] = eye open (restriction visible to students, MDL-87671 scenario).
        $eyeopen = '{"op":"&","c":[' . $nevermatchemail . '],"showc":[true]}';

        $hiddensubsection = $generator->create_module('subsection', [
            'course' => $course->id,
            'section' => 1,
            'availability' => $eyeclosed,
        ]);
        $visiblesubsection = $generator->create_module('subsection', [
            'course' => $course->id,
            'section' => 1,
            'availability' => $eyeopen,
        ]);

        rebuild_course_cache($course->id, true);
        $this->setUser($student);
        $PAGE->set_url('/course/view.php', ['id' => $course->id]);
        $PAGE->set_course($course);
        $PAGE->set_context(\core\context\course::instance($course->id));

        $modinfo = get_fast_modinfo($course);
        $section1 = $modinfo->get_section_info(1);
        $hiddeninfo = $modinfo->get_section_info_by_component('mod_subsection', $hiddensubsection->id);
        $visibleinfo = $modinfo->get_section_info_by_component('mod_subsection', $visiblesubsection->id);

        $nav = new exposed_global_navigation($PAGE);
        $nav->set_initialised();

        [, $activities] = $nav->exposed_generate_sections_and_activities($course);

        $sectionnode = $nav->add('Section 1', null, navigation_node::TYPE_SECTION, null, $section1->id);
        $nav->exposed_load_section_activities_navigation($sectionnode, $section1, $activities);

        // Eye-closed restricted subsection must NOT appear in the navigation block.
        $this->assertFalse($sectionnode->find($hiddeninfo->id, navigation_node::TYPE_SECTION));
        // Eye-open restricted subsection MUST appear in navigation (MDL-87671 behaviour).
        $this->assertNotFalse($sectionnode->find($visibleinfo->id, navigation_node::TYPE_SECTION));
    }
}

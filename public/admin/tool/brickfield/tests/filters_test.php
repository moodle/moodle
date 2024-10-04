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

namespace tool_brickfield;

use tool_brickfield\local\tool\filter;

/**
 * Unit tests for {@filter tool_brickfield\local\tool\filter}.
 *
 * @package   tool_brickfield
 * @copyright  2020 onward: Brickfield Education Labs, www.brickfield.ie
 * @author     Jay Churchward (jay.churchward@poetopensource.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class filters_test extends \advanced_testcase {
    public function test_constructor(): void {
        $this->resetAfterTest();

        // Variables.
        $courseid = 1;
        $categoryid = 2;
        $tab = 'tab';
        $page = 3;
        $perpage = 4;
        $url = 'url';

        // Test responses.
        $object = new filter();
        $this->assertEquals($object->courseid, 0);
        $this->assertEquals($object->categoryid, 0);
        $this->assertEquals($object->tab, '');
        $this->assertEquals($object->page, 0);
        $this->assertEquals($object->perpage, 0);
        $this->assertEquals($object->url, '');

        $object = new filter($courseid, $categoryid, $tab, $page, $perpage, $url);
        $this->assertEquals($object->courseid, $courseid);
        $this->assertEquals($object->categoryid, $categoryid);
        $this->assertEquals($object->tab, $tab);
        $this->assertEquals($object->page, $page);
        $this->assertEquals($object->perpage, $perpage);
        $this->assertEquals($object->url, $url);
    }

    public function test_get_course_sql(): void {
        $this->resetAfterTest();
        $object = new filter();

        $output = $object->get_course_sql();

        $this->assertIsArray($output);
        $this->assertEquals($output[0], '');

        $object = $this->create_object_with_params();
        $output = $object->get_course_sql();

        $this->assertEquals($output[0], ' AND (courseid = ?)');
        $this->assertEquals($output[1][0], $object->courseid);

    }

    public function test_validate_filters(): void {
        $this->resetAfterTest();
        // Variables.
        $courseid = 0;
        $categoryid = 2;
        $tab = 'tab';
        $page = 3;
        $perpage = 4;
        $url = 'url';
        $object = new filter();

        $output = $object->validate_filters();
        $this->assertTrue($output);

        $object = $this->create_object();
        $output = $object->validate_filters();
        $this->assertTrue($output);

        $object = new filter($courseid, $categoryid, $tab, $page, $perpage);
        $output = $object->validate_filters();
        $this->assertFalse($output);

        $category = $this->getDataGenerator()->create_category();

        $object = new filter($courseid, $category->id, $tab, $page, $perpage);
        $output = $object->validate_filters();
        $this->assertFalse($output);
    }

    public function test_has_course_filters(): void {
        $this->resetAfterTest();

        $object = new filter();
        $output = $object->has_course_filters();
        $this->assertFalse($output);

        $object = $this->create_object();
        $output = $object->has_course_filters();
        $this->assertTrue($output);
    }

    public function test_has_capability_in_context(): void {
        global $DB;

        $this->resetAfterTest();

        $object = $this->create_object_with_params();
        $capability = accessibility::get_capability_name('viewcoursetools');
        $output = $object->has_capability_in_context($capability, \context_system::instance());
        $this->assertFalse($output);

        $output = $object->has_capability_in_context($capability, \context_coursecat::instance($object->categoryid));
        $this->assertFalse($output);

        $output = $object->has_capability_in_context($capability, \context_course::instance($object->courseid));
        $this->assertFalse($output);

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $this->setUser($user);

        $output = $object->has_capability_in_context($capability, \context_system::instance());
        $this->assertFalse($output);

        $output = $object->has_capability_in_context($capability, \context_coursecat::instance($object->categoryid));
        $this->assertFalse($output);

        $output = $object->has_capability_in_context($capability, \context_course::instance($course->id));
        $this->assertTrue($output);

        $teacherrole = $DB->get_record('role', ['shortname' => 'teacher']);
        $categorycontext = \context_coursecat::instance($object->categoryid);
        $this->getDataGenerator()->role_assign($teacherrole->id, $user->id, $categorycontext->id);

        $output = $object->has_capability_in_context($capability, $categorycontext);
        $this->assertTrue($output);
    }

    public function test_get_errormessage(): void {
        $this->resetAfterTest();
        // Variables.
        $courseid = 0;
        $categoryid = 2;
        $tab = 'tab';
        $page = 3;
        $perpage = 4;
        $url = 'url';

        $object = new filter();
        $output = $object->get_errormessage();
        $this->assertNull($output);

        $object = new filter($courseid, $categoryid, $tab, $page, $perpage);
        $object->validate_filters();
        $output = $object->get_errormessage();
        $this->assertEquals($output, 'Invalid category, please check your input');

        $category = $this->getDataGenerator()->create_category();
        $object = new filter($courseid, $category->id, $tab, $page, $perpage);
        $object->validate_filters();
        $output = $object->get_errormessage();
        $this->assertEquals($output, 'No courses found for category ' . $category->id);
    }

    /**
     * Create a filter object and return it.
     * @return filter
     */
    private function create_object() {
        // Variables.
        $courseid = 1;
        $categoryid = 2;
        $tab = 'tab';
        $page = 3;
        $perpage = 4;
        $url = 'url';

        $object = new filter($courseid, $categoryid, $tab, $page, $perpage);

        return $object;
    }

    /**
     * Create a filter object with some parameters and return it.
     * @return filter
     */
    private function create_object_with_params() {
        // Variables.
        $tab = 'tab';
        $page = 3;
        $perpage = 4;
        $url = 'url';

        $category = $this->getDataGenerator()->create_category();
        $course = $this->getDataGenerator()->create_course((object)['category' => $category->id]);

        $object = new filter($course->id, $category->id, $tab, $page, $perpage);

        return $object;
    }
}

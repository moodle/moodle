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
 * Contains the tests for the content_item_service class.
 *
 * @package    core
 * @subpackage course
 * @copyright  2020 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tests\course;

defined('MOODLE_INTERNAL') || die();

use \core_course\local\service\content_item_service;
use \core_course\local\repository\content_item_readonly_repository;

/**
 * The tests for the content_item_service class.
 *
 * @copyright  2020 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class services_content_item_service_testcase extends \advanced_testcase {

    /**
     * Test confirming that content items are returned by the service.
     */
    public function test_get_content_items_for_user_in_course_basic() {
        $this->resetAfterTest();

        // Create a user in a course.
        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');

        $cis = new content_item_service(new content_item_readonly_repository());
        $contentitems = $cis->get_content_items_for_user_in_course($user, $course);

        foreach ($contentitems as $key => $contentitem) {
            $this->assertObjectHasAttribute('id', $contentitem);
            $this->assertObjectHasAttribute('name', $contentitem);
            $this->assertObjectHasAttribute('title', $contentitem);
            $this->assertObjectHasAttribute('link', $contentitem);
            $this->assertObjectHasAttribute('icon', $contentitem);
            $this->assertObjectHasAttribute('help', $contentitem);
            $this->assertObjectHasAttribute('archetype', $contentitem);
            $this->assertObjectHasAttribute('componentname', $contentitem);
        }
    }

    /**
     * Test confirming that access control is performed when asking the service to return content items for a user in a course.
     */
    public function test_get_content_items_for_user_in_course_permissions() {
        $this->resetAfterTest();
        global $DB;

        // Create a user in a course.
        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');

        // No cap override, so assign should be returned.
        $cis = new content_item_service(new content_item_readonly_repository());
        $contentitems = $cis->get_content_items_for_user_in_course($user, $course);
        $this->assertContains('assign', array_column($contentitems, 'name'));

        // Override the capability 'mod/assign:addinstance' for the 'editing teacher' role.
        $teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));
        assign_capability('mod/assign:addinstance', CAP_PROHIBIT, $teacherrole->id, \context_course::instance($course->id));

        $contentitems = $cis->get_content_items_for_user_in_course($user, $course);
        $this->assertArrayNotHasKey('assign', $contentitems);
    }

    /**
     * Test confirming that params can be added to the content item's link.
     */
    public function test_get_content_item_for_user_in_course_link_params() {
        $this->resetAfterTest();

        // Create a user in a course.
        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');

        $cis = new content_item_service(new content_item_readonly_repository());
        $contentitems = $cis->get_content_items_for_user_in_course($user, $course, ['sr' => 7]);

        foreach ($contentitems as $item) {
            $this->assertStringContainsString('sr=7', $item->link);
        }
    }

    /**
     * Test confirming that all content items can be fetched irrespective of permissions.
     */
    public function test_get_all_content_items() {
        $this->resetAfterTest();
        global $DB;

        // Create a user in a course.
        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');

        $cis = new content_item_service(new content_item_readonly_repository());
        $allcontentitems = $cis->get_all_content_items();
        $coursecontentitems = $cis->get_content_items_for_user_in_course($user, $course);

        // The call to get_all_content_items() should return the same items as for the course,
        // given the user in an editing teacher and can add manual lti instances.
        $this->assertEquals(array_column($allcontentitems, 'name'), array_column($coursecontentitems, 'name'));

        // Now removing the cap 'mod/lti:addinstance'. This will restrict those items returned by the course-specific method.
        $teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));
        assign_capability('mod/lti:addinstance', CAP_PROHIBIT, $teacherrole->id, \context_course::instance($course->id));

        // Verify that all items, including lti, are still returned by the get_all_content_items() call.
        $allcontentitems = $cis->get_all_content_items();
        $coursecontentitems = $cis->get_content_items_for_user_in_course($user, $course);
        $this->assertNotContains('lti', array_column($coursecontentitems, 'name'));
        $this->assertContains('lti', array_column($allcontentitems, 'name'));
    }
}

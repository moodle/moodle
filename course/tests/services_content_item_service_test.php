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
        $allcontentitems = $cis->get_all_content_items($user);
        $coursecontentitems = $cis->get_content_items_for_user_in_course($user, $course);

        // The call to get_all_content_items() should return the same items as for the course,
        // given the user in an editing teacher and can add manual lti instances.
        $this->assertContains('lti', array_column($coursecontentitems, 'name'));
        $this->assertContains('lti', array_column($allcontentitems, 'name'));

        // Now removing the cap 'mod/lti:addinstance'. This will restrict those items returned by the course-specific method.
        $teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));
        assign_capability('mod/lti:addinstance', CAP_PROHIBIT, $teacherrole->id, \context_course::instance($course->id));

        // Verify that all items, including lti, are still returned by the get_all_content_items() call.
        $allcontentitems = $cis->get_all_content_items($user);
        $coursecontentitems = $cis->get_content_items_for_user_in_course($user, $course);
        $this->assertNotContains('lti', array_column($coursecontentitems, 'name'));
        $this->assertContains('lti', array_column($allcontentitems, 'name'));
    }

    /**
     * Test confirming that content items which title match a certain pattern can be fetched irrespective of permissions.
     */
    public function test_get_content_items_by_name_pattern() {
        $this->resetAfterTest();

        // Create a user in a course.
        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');

        // Pattern that does exist.
        $pattern1 = "assign";
        // Pattern that does not exist.
        $pattern2 = "random string";

        $cis = new content_item_service(new content_item_readonly_repository());
        $matchingcontentitems1 = $cis->get_content_items_by_name_pattern($user, $pattern1);
        $matchingcontentitems2 = $cis->get_content_items_by_name_pattern($user, $pattern2);

        // The pattern "assign" should return at least 1 content item (ex. "Assignment").
        $this->assertGreaterThanOrEqual(1, count($matchingcontentitems1));
        // Verify the pattern "assign" can be found in the title of each returned content item.
        foreach ($matchingcontentitems1 as $contentitem) {
            $this->assertEquals(1, preg_match("/$pattern1/i", $contentitem->title));
        }
        // The pattern "random string" should not return any content items.
        $this->assertEmpty($matchingcontentitems2);
    }

    /**
     * Test confirming that a content item can be added to a user's favourites.
     */
    public function test_add_to_user_favourites() {
        $this->resetAfterTest();

        // Create a user in a course.
        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $cis = new content_item_service(new content_item_readonly_repository());

        // Grab a the assign content item, which we'll favourite for the user.
        $items = $cis->get_all_content_items($user);
        $assign = $items[array_search('assign', array_column($items, 'name'))];
        $contentitem = $cis->add_to_user_favourites($user, 'mod_assign', $assign->id);

        // Verify the exported result is marked as a favourite.
        $this->assertEquals('assign', $contentitem->name);
        $this->assertTrue($contentitem->favourite);

        // Verify the item is marked as a favourite when returned from the other service methods.
        $allitems = $cis->get_all_content_items($user);
        $allitemsassign = $allitems[array_search('assign', array_column($allitems, 'name'))];

        $courseitems = $cis->get_content_items_for_user_in_course($user, $course);
        $courseitemsassign = $courseitems[array_search('assign', array_column($courseitems, 'name'))];
        $this->assertTrue($allitemsassign->favourite);
        $this->assertTrue($courseitemsassign->favourite);
    }

    /**
     * Test verifying that content items can be removed from a user's favourites.
     */
    public function test_remove_from_user_favourites() {
        $this->resetAfterTest();

        // Create a user in a course.
        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $cis = new content_item_service(new content_item_readonly_repository());

        // Grab a the assign content item, which we'll favourite for the user.
        $items = $cis->get_all_content_items($user);
        $assign = $items[array_search('assign', array_column($items, 'name'))];
        $cis->add_to_user_favourites($user, 'mod_assign', $assign->id);

        // Now, remove the favourite, and verify it.
        $contentitem = $cis->remove_from_user_favourites($user, 'mod_assign', $assign->id);

        // Verify the exported result is not marked as a favourite.
        $this->assertEquals('assign', $contentitem->name);
        $this->assertFalse($contentitem->favourite);

        // Verify the item is not marked as a favourite when returned from the other service methods.
        $allitems = $cis->get_all_content_items($user);
        $allitemsassign = $allitems[array_search('assign', array_column($allitems, 'name'))];
        $courseitems = $cis->get_content_items_for_user_in_course($user, $course);
        $courseitemsassign = $courseitems[array_search('assign', array_column($courseitems, 'name'))];
        $this->assertFalse($allitemsassign->favourite);
        $this->assertFalse($courseitemsassign->favourite);
    }

    /**
     * Test that toggling a recommendation works as anticipated.
     */
    public function test_toggle_recommendation() {
        $this->resetAfterTest();

        // Create a user in a course.
        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $cis = new content_item_service(new content_item_readonly_repository());

        // Grab a the assign content item, which we'll recommend for the user.
        $items = $cis->get_all_content_items($user);
        $assign = $items[array_search('assign', array_column($items, 'name'))];
        $result = $cis->toggle_recommendation($assign->componentname, $assign->id);
        $this->assertTrue($result);

        $courseitems = $cis->get_all_content_items($user);
        $courseitemsassign = $courseitems[array_search('assign', array_column($courseitems, 'name'))];
        $this->assertTrue($courseitemsassign->recommended);

        // Let's toggle the recommendation off.
        $result = $cis->toggle_recommendation($assign->componentname, $assign->id);
        $this->assertFalse($result);

        $courseitems = $cis->get_all_content_items($user);
        $courseitemsassign = $courseitems[array_search('assign', array_column($courseitems, 'name'))];
        $this->assertFalse($courseitemsassign->recommended);
    }
}

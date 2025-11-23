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
 * Contains the test class for the caching_content_item_readonly_repository class.
 *
 * @package    core
 * @subpackage course
 * @copyright  2020 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_course;

defined('MOODLE_INTERNAL') || die();

use core_course\local\repository\content_item_readonly_repository;
use core_course\local\repository\caching_content_item_readonly_repository;

/**
 * The test class for the caching_content_item_readonly_repository class.
 *
 * @copyright  2020 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(caching_content_item_readonly_repository::class)]
final class caching_content_item_readonly_repository_test extends \advanced_testcase {
    /**
     * Test verifying that content items are cached and returned from the cache in subsequent same-request calls.
     */
    public function test_find_all_for_course(): void {
        $this->resetAfterTest();
        global $DB;

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $cir = new content_item_readonly_repository();
        $ccir = new caching_content_item_readonly_repository(\cache::make('core', 'user_course_content_items'), $cir);

        // Get the content items using both the live and the caching repos.
        $items = $cir->find_all_for_course($course, $user);
        $cacheditems = $ccir->find_all_for_course($course, $user);
        $itemsfiltered = array_values(array_filter($items, function($item) {
            return $item->get_component_name() == 'mod_book';
        }));
        $cacheditemsfiltered = array_values(array_filter($cacheditems, function($item) {
            return $item->get_component_name() == 'mod_book';
        }));

        // Verify the book module is in both result sets.
        $module = $DB->get_record('modules', ['name' => 'book']);
        $this->assertEquals($module->name, $itemsfiltered[0]->get_name());
        $this->assertEquals($module->name, $cacheditemsfiltered[0]->get_name());

        // Hide a module and get the content items again.
        $DB->set_field("modules", "visible", "0", ["id" => $module->id]);
        $items = $cir->find_all_for_course($course, $user);
        $cacheditems = $ccir->find_all_for_course($course, $user);
        $itemsfiltered = array_values(array_filter($items, function($item) {
            return $item->get_component_name() == 'mod_book';
        }));
        $cacheditemsfiltered = array_values(array_filter($cacheditems, function($item) {
            return $item->get_component_name() == 'mod_book';
        }));

        // The caching repo should return the same list, while the live repo will return the updated list.
        $this->assertEquals($module->name, $cacheditemsfiltered[0]->get_name());
        $this->assertEmpty($itemsfiltered);
    }

    /**
     * Test verifying that cached content items are returned from the cache as the correct user.
     */
    public function test_find_all_for_course_user_cache(): void {
        global $DB, $CFG;
        require_once($CFG->dirroot . '/mod/lti/locallib.php');

        $this->resetAfterTest();
        $admin = get_admin();

        $course = $this->getDataGenerator()->create_course();
        $editingteacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $cir = new content_item_readonly_repository();
        $ccir = new caching_content_item_readonly_repository(\cache::make('core', 'user_course_content_items'), $cir);

        // Create lti that is only available to editingteacher.
        $type = new \stdClass();
        $type->course = SITEID;
        $type->name = 'Editing Teacher Only LTI Tool';
        $type->baseurl = 'https://example.com/lti/launch';
        $type->tooldomain = 'example.com';
        $type->state = LTI_TOOL_STATE_CONFIGURED;
        $type->coursevisible = LTI_COURSEVISIBLE_ACTIVITYCHOOSER;
        $type->createdby = $admin->id;
        $type->timecreated = time();
        $type->timemodified = time();

        $typeid = $DB->insert_record('lti_types', $type);

        // Ensure we are working as editingteacher.
        $this->setUser($editingteacher);

        // Get cached content items for each user.
        // We run this twice, first to build the cache, second to query cache.
        $temp = $ccir->find_all_for_course($course, $editingteacher);
        $cachededitingteacheritems = $ccir->find_all_for_course($course, $editingteacher);

        $temp = $ccir->find_all_for_course($course, $teacher);
        $cachedteacheritems = $ccir->find_all_for_course($course, $teacher);

        // The lti will only appear for editingteacher.
        $cachededitingteacheritemsfiltered = array_values(array_filter($cachededitingteacheritems, function ($item) {
            return $item->get_title()->get_value() == 'Editing Teacher Only LTI Tool';
        }));
        $this->assertCount(1, $cachededitingteacheritemsfiltered);

        // The lti will not appear for teacher.
        $cachedteacheritemsfiltered = array_values(array_filter($cachedteacheritems, function ($item) {
            return $item->get_title()->get_value() == 'Editing Teacher Only LTI Tool';
        }));
        $this->assertEmpty($cachedteacheritemsfiltered);
    }
}

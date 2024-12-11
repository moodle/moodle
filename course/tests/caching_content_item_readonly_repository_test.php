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
}

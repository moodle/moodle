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
 * Contains the test class for the content_item_readonly_repository class.
 *
 * @package    core
 * @subpackage course
 * @copyright  2020 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tests\core_course;

defined('MOODLE_INTERNAL') || die();

use core_course\local\entity\content_item;
use core_course\local\repository\content_item_readonly_repository;

/**
 * The test class for the content_item_readonly_repository class.
 *
 * @copyright  2020 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class content_item_readonly_repository_testcase extends \advanced_testcase {
    /**
     * Test the repository method, find_all_for_course().
     */
    public function test_find_all_for_course() {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $cir = new content_item_readonly_repository();

        $items = $cir->find_all_for_course($course, $user);
        foreach ($items as $key => $item) {
            $this->assertInstanceOf(content_item::class, $item);
            $this->assertEquals($course->id, $item->get_link()->param('id'));
            $this->assertNotNull($item->get_link()->param('add'));
        }
    }

    /**
     * Test verifying that content items for hidden modules are not returned.
     */
    public function test_find_all_for_course_hidden_module() {
        $this->resetAfterTest();
        global $DB;

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $cir = new content_item_readonly_repository();

        // Hide a module.
        $module = $DB->get_record('modules', ['id' => 1]);
        $DB->set_field("modules", "visible", "0", ["id" => $module->id]);

        $items = $cir->find_all_for_course($course, $user);
        $this->assertArrayNotHasKey($module->name, $items);
    }
}

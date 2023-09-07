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
namespace core_course;

defined('MOODLE_INTERNAL') || die();

use core_course\local\entity\content_item;
use core_course\local\repository\content_item_readonly_repository;

/**
 * The test class for the content_item_readonly_repository class.
 *
 * @copyright  2020 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class content_item_readonly_repository_test extends \advanced_testcase {
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

    /**
     * Test confirming that all content items can be fetched, even those which require certain caps when in a course.
     */
    public function test_find_all() {
        $this->resetAfterTest();

        global $DB, $CFG;
        require_once($CFG->dirroot . '/mod/lti/tests/generator/lib.php');
        require_once($CFG->dirroot . '/mod/lti/locallib.php');

        // We'll compare our results to those which are course-specific, using mod_lti as an example.
        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        /** @var \mod_lti_generator $ltigenerator */
        $ltigenerator = $this->getDataGenerator()->get_plugin_generator('mod_lti');
        $ltigenerator->create_tool_types([
            'name' => 'site tool',
            'baseurl' => 'http://example.com',
            'coursevisible' => LTI_COURSEVISIBLE_ACTIVITYCHOOSER,
            'state' => LTI_TOOL_STATE_CONFIGURED
        ]);
        $teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));
        assign_capability('mod/lti:addpreconfiguredinstance', CAP_PROHIBIT, $teacherrole->id,
            \core\context\course::instance($course->id));
        $cir = new content_item_readonly_repository();
        $this->setUser($user); // This is needed since the underlying lti code needs the global user despite the api accepting user.

        // Course specific - the tool won't be returned as the user doesn't have the capability required to use preconfigured tools.
        $forcourse = $cir->find_all_for_course($course, $user);
        $forcourse = array_filter($forcourse, function($contentitem) {
            return str_contains($contentitem->get_name(), 'lti_type');
        });
        $this->assertEmpty($forcourse);

        // All - all items are returned, including the lti site tool.
        $all = $cir->find_all();
        $all = array_filter($all, function($contentitem) {
            return str_contains($contentitem->get_name(), 'lti_type');
        });
        $this->assertCount(1, $all);
    }
}

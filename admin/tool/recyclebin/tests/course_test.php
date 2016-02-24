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
 * Recycle bin tests.
 *
 * @package    local_recyclebin
 * @copyright  2015 University of Kent
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Recycle bin course tests.
 *
 * @package    local_recyclebin
 * @copyright  2015 University of Kent
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_recyclebin_course_tests extends \advanced_testcase
{
    /**
     * Setup for each test.
     */
    protected function setUp() {
        global $DB;

        $this->resetAfterTest(true);
        $this->setAdminUser();

        $this->course = $this->getDataGenerator()->create_course();
        $this->before = $DB->count_records('course_modules');
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');
        $this->instance = $generator->create_instance(array(
            'course' => $this->course->id
        ));
    }

    /**
     * Run a bunch of tests to make sure we capture mods.
     */
    public function test_observer() {
        global $DB;

        $this->assertEquals($this->before + 1, $DB->count_records('course_modules'));
        $this->assertEquals(0, $DB->count_records('local_recyclebin_course'));

        // Delete the CM.
        course_delete_module($this->instance->cmid);

        $this->assertEquals($this->before, $DB->count_records('course_modules'));
        $this->assertEquals(1, $DB->count_records('local_recyclebin_course'));

        // Try with the API.
        $recyclebin = new \local_recyclebin\course($this->course->id);
        $this->assertEquals(1, count($recyclebin->get_items()));
    }

    /**
     * Run a bunch of tests to make sure we can restore mods.
     */
    public function test_restore() {
        global $DB;

        // Delete the CM.
        course_delete_module($this->instance->cmid);

        // Try restoring.
        $recyclebin = new \local_recyclebin\course($this->course->id);
        foreach ($recyclebin->get_items() as $item) {
            $recyclebin->restore_item($item);
        }

        $this->assertEquals($this->before + 1, $DB->count_records('course_modules'));
        $this->assertEquals(0, $DB->count_records('local_recyclebin_course'));
        $this->assertEquals(0, count($recyclebin->get_items()));
    }

    /**
     * Run a bunch of tests to make sure we can purge mods.
     */
    public function test_purge() {
        global $DB;

        // Delete the CM.
        course_delete_module($this->instance->cmid);

        // Try purging.
        $recyclebin = new \local_recyclebin\course($this->course->id);
        foreach ($recyclebin->get_items() as $item) {
            $recyclebin->delete_item($item);
        }

        $this->assertEquals($this->before, $DB->count_records('course_modules'));
        $this->assertEquals(0, $DB->count_records('local_recyclebin_course'));
        $this->assertEquals(0, count($recyclebin->get_items()));
    }

    /**
     * Test the cleanup/purge task.
     */
    public function test_purge_task() {
        global $DB;

        set_config('expiry', 1, 'local_recyclebin');

        // Delete the CM.
        course_delete_module($this->instance->cmid);

        // Set deleted date to the distant past.
        $recyclebin = new \local_recyclebin\course($this->course->id);
        foreach ($recyclebin->get_items() as $item) {
            $item->deleted = 1;
            $DB->update_record('local_recyclebin_course', $item);
        }
        // Execute cleanup task.
        $task = new local_recyclebin\task\cleanup_activities();
        $task->execute();

        $this->assertEquals($this->before, $DB->count_records('course_modules'));
        $this->assertEquals(0, $DB->count_records('local_recyclebin_course'));
        $this->assertEquals(0, count($recyclebin->get_items()));
    }
}

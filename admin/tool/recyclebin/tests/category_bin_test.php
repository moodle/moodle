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
 * @package    tool_recyclebin
 * @copyright  2015 University of Kent
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Recycle bin category tests.
 *
 * @package    tool_recyclebin
 * @copyright  2015 University of Kent
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_recyclebin_category_bin_tests extends advanced_testcase {

    /**
     * @var stdClass $course
     */
    protected $course;

    /**
     * @var stdClass $coursebeingrestored
     */
    protected $coursebeingrestored;

    /**
     * Setup for each test.
     */
    protected function setUp() {
        $this->resetAfterTest();
        $this->setAdminUser();

        // We want the category bin to be enabled.
        set_config('categorybinenable', 1, 'tool_recyclebin');

        $this->course = $this->getDataGenerator()->create_course();
    }

    /**
     * Check that our hook is called when a course is deleted.
     */
    public function test_pre_course_delete_hook() {
        global $DB;

        // This simulates a temporary course being cleaned up by a course restore.
        $this->coursebeingrestored = $this->getDataGenerator()->create_course();
        $this->coursebeingrestored->deletesource = 'restore';

        // Should have nothing in the recycle bin.
        $this->assertEquals(0, $DB->count_records('tool_recyclebin_category'));

        delete_course($this->course, false);
        // This should not be added to the recycle bin.
        delete_course($this->coursebeingrestored, false);

        // Check the course is now in the recycle bin.
        $this->assertEquals(1, $DB->count_records('tool_recyclebin_category'));

        // Try with the API.
        $recyclebin = new \tool_recyclebin\category_bin($this->course->category);
        $this->assertEquals(1, count($recyclebin->get_items()));
    }

    /**
     * Check that our hook is called when a course is deleted.
     */
    public function test_pre_course_category_delete_hook() {
        global $DB;

        // Should have nothing in the recycle bin.
        $this->assertEquals(0, $DB->count_records('tool_recyclebin_category'));

        delete_course($this->course, false);

        // Check the course is now in the recycle bin.
        $this->assertEquals(1, $DB->count_records('tool_recyclebin_category'));

        // Now let's delete the course category.
        $category = core_course_category::get($this->course->category);
        $category->delete_full(false);

        // Check that the course was deleted from the category recycle bin.
        $this->assertEquals(0, $DB->count_records('tool_recyclebin_category'));
    }

    /**
     * Test that we can restore recycle bin items.
     */
    public function test_restore() {
        global $DB;

        delete_course($this->course, false);

        $recyclebin = new \tool_recyclebin\category_bin($this->course->category);
        foreach ($recyclebin->get_items() as $item) {
            $recyclebin->restore_item($item);
        }

        // Check that it was restored and removed from the recycle bin.
        $this->assertEquals(2, $DB->count_records('course')); // Site course and the course we restored.
        $this->assertEquals(0, count($recyclebin->get_items()));
    }

    /**
     * Test that we can delete recycle bin items.
     */
    public function test_delete() {
        global $DB;

        delete_course($this->course, false);

        $recyclebin = new \tool_recyclebin\category_bin($this->course->category);
        foreach ($recyclebin->get_items() as $item) {
            $recyclebin->delete_item($item);
        }

        // Item was deleted, so no course was restored.
        $this->assertEquals(1, $DB->count_records('course')); // Just the site course.
        $this->assertEquals(0, count($recyclebin->get_items()));
    }

    /**
     * Test the cleanup task.
     */
    public function test_cleanup_task() {
        global $DB;

        // Set the expiry to 1 week.
        set_config('categorybinexpiry', WEEKSECS, 'tool_recyclebin');

        delete_course($this->course, false);

        $recyclebin = new \tool_recyclebin\category_bin($this->course->category);

        // Set deleted date to the distant past.
        foreach ($recyclebin->get_items() as $item) {
            $item->timecreated = time() - WEEKSECS;
            $DB->update_record('tool_recyclebin_category', $item);
        }

        // Create another course to delete.
        $course = $this->getDataGenerator()->create_course();
        delete_course($course, false);

        // Should now be two courses in the recycle bin.
        $this->assertEquals(2, count($recyclebin->get_items()));

        // Execute cleanup task.
        $this->expectOutputRegex("/\[tool_recyclebin\] Deleting item '\d+' from the category recycle bin/");
        $task = new \tool_recyclebin\task\cleanup_category_bin();
        $task->execute();

        // Task should only have deleted the course where we updated the time.
        $courses = $recyclebin->get_items();
        $this->assertEquals(1, count($courses));
        $course = reset($courses);
        $this->assertEquals('Test course 2', $course->fullname);
    }

    /**
     * Provider for test_course_restore_with_userdata() and test_course_restore_without_userdata()
     *
     * Used to verify that recycle bin is immune to various settings. Provides plugin, name, value for
     * direct usage with set_config()
     */
    public function recycle_bin_settings_provider() {
        return [
            'backup/backup_auto_storage moodle' => [[
                (object)['plugin' => 'backup', 'name' => 'backup_auto_storage', 'value' => 0],
            ]],

            'backup/backup_auto_storage external' => [[
                (object)['plugin' => 'backup', 'name' => 'backup_auto_storage', 'value' => 1],
                (object)['plugin' => 'backup', 'name' => 'backup_auto_destination', 'value' => true],
            ]],

            'backup/backup_auto_storage mixed' => [[
                (object)['plugin' => 'backup', 'name' => 'backup_auto_storage', 'value' => 2],
                (object)['plugin' => 'backup', 'name' => 'backup_auto_destination', 'value' => true],
            ]],
        ];
    }

    /**
     * Tests that user data is restored when course is restored.
     *
     * @dataProvider recycle_bin_settings_provider
     * @param array $settings array of plugin, name, value stdClass().
     */
    public function test_course_restore_with_userdata($settings) {
        global $DB;

        // Force configuration changes from provider.
        foreach ($settings as $setting) {
            // Need to create a directory for backup_auto_destination.
            if ($setting->plugin === 'backup' && $setting->name === 'backup_auto_destination' && $setting->value === true) {
                $setting->value = make_request_directory();
            }
            set_config($setting->name, $setting->value, $setting->plugin);
        }

        // We want user data to be included for this test.
        set_config('backup_auto_users', true, 'backup');

        $student = $this->getDataGenerator()->create_and_enrol($this->course, 'student');

        // Delete course.
        delete_course($this->course, false);
        $this->assertFalse($DB->record_exists('course', ['id' => $this->course->id]));

        // Verify there is now a backup @ cat recycle bin file area.
        $recyclebin = new \tool_recyclebin\category_bin($this->course->category);
        $this->assertEquals(1, count($recyclebin->get_items()));

        // Restore the recycle bin item.
        $recyclebin->restore_item(current($recyclebin->get_items()));

        // Get the new course.
        $newcourse = $DB->get_record('course', ['shortname' => $this->course->shortname], '*', MUST_EXIST);

        // Check that it was removed from the recycle bin.
        $this->assertEquals(0, count($recyclebin->get_items()));

        // Verify that student DOES continue enrolled.
        $this->assertTrue(is_enrolled(context_course::instance($newcourse->id), $student->id));
    }

    /**
     * Tests that user data is not restored when course is restored.
     *
     * @dataProvider recycle_bin_settings_provider
     * @param array $settings array of plugin, name, value stdClass().
     */
    public function test_course_restore_without_userdata($settings) {
        global $DB;

        // Force configuration changes from provider.
        foreach ($settings as $setting) {
            // Need to create a directory for backup_auto_destination.
            if ($setting->plugin === 'backup' && $setting->name === 'backup_auto_destination' && $setting->value === true) {
                $setting->value = make_request_directory();
            }
            set_config($setting->name, $setting->value, $setting->plugin);
        }

        // We want user data to be included for this test.
        set_config('backup_auto_users', false, 'backup');

        $student = $this->getDataGenerator()->create_and_enrol($this->course, 'student');

        // Delete course.
        delete_course($this->course, false);
        $this->assertFalse($DB->record_exists('course', ['id' => $this->course->id]));

        // Verify there is now a backup @ cat recycle bin file area.
        $recyclebin = new \tool_recyclebin\category_bin($this->course->category);
        $this->assertEquals(1, count($recyclebin->get_items()));

        // Restore the recycle bin item.
        $recyclebin->restore_item(current($recyclebin->get_items()));

        // Get the new course.
        $newcourse = $DB->get_record('course', ['shortname' => $this->course->shortname], '*', MUST_EXIST);

        // Check that it was removed from the recycle bin.
        $this->assertEquals(0, count($recyclebin->get_items()));

        // Verify that student DOES NOT continue enrolled.
        $this->assertFalse(is_enrolled(context_course::instance($newcourse->id), $student->id));
    }
}

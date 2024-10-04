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

namespace core\moodlenet;

use backup_activity_task;

/**
 * Unit tests for {@see \core\moodlenet\course_partial_packager}.
 *
 * @coversDefaultClass \core\moodlenet\course_partial_packager
 * @package    core
 * @copyright  2023 Huong Nguyen <huongnv13@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class course_partial_packager_test extends \advanced_testcase {

    /**
     * Test fetching task settings.
     *
     * @covers ::remove_unselected_activities
     * @covers ::get_all_activity_tasks
     * @covers ::get_backup_controller
     */
    public function test_remove_unselected_activities(): void {
        global $USER;
        $this->resetAfterTest();
        $this->setAdminUser();

        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $page1 = $generator->create_module('page', ['course' => $course->id]);
        $page2 = $generator->create_module('page', ['course' => $course->id]);

        // Load the course packager.
        $packager = new course_partial_packager($course, [$page1->cmid], $USER->id);

        // Fetch all backup task settings.
        $rc = new \ReflectionClass(course_partial_packager::class);
        $rcmgetbackup = $rc->getMethod('get_backup_controller');
        $controller = $rcmgetbackup->invoke($packager);

        $rcmremove = $rc->getMethod('remove_unselected_activities');
        $rcmremove->invoke($packager, $controller);

        // Fetch all backup task settings for asserting them.
        $finalsetting = [];
        foreach ($controller->get_plan()->get_tasks() as $task) {
            if (! $task instanceof backup_activity_task) { // Only for activity tasks.
                continue;
            }
            $tasksettings = $task->get_settings();
            foreach ($tasksettings as $setting) {
                if (in_array($task->get_moduleid(), [$page1->cmid, $page2->cmid]) &&
                    strpos($setting->get_name(), '_included') !== false) {
                    $finalsetting[$task->get_moduleid()] = [
                        'name' => $setting->get_name(),
                        'value' => $setting->get_value(),
                    ];
                }
            }
        }

        // Check the number of partial sharing tasks.
        // Expected 2, Page 1 and Page 2.
        $this->assertCount(2, $finalsetting);

        // Check the value of the task of Page 1. 1 mean enabled, the backup will include the Page 1 activity.
        $this->assertEquals('page_' . $page1->cmid . '_included', $finalsetting[$page1->cmid]['name']);
        $this->assertEquals(1, $finalsetting[$page1->cmid]['value']);

        // Check the value of the task of Page 2. 0 mean disabled, the backup will not include the Page 2 activity.
        $this->assertEquals('page_' . $page2->cmid . '_included', $finalsetting[$page2->cmid]['name']);
        $this->assertEquals(0, $finalsetting[$page2->cmid]['value']);

        // We have finished with the backup controller, so destroy it.
        $controller->destroy();
    }
}

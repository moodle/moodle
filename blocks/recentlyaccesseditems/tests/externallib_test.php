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
 * External block functions unit tests
 *
 * @package    block_recentlyaccesseditems
 * @category   external
 * @copyright  2018 Victor Deniz <victor@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.6
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');

/**
 * Test Recently accessed items block external functions
 *
 * @package    block_recentlyaccesseditems
 * @category   external
 * @copyright  2018 Victor Deniz <victor@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.6
 */
class block_recentlyaccesseditems_externallib_testcase extends externallib_advanced_testcase {

    /**
     * Test the get_recent_items function.
     */
    public function test_get_recent_items() {

        $this->resetAfterTest();
        $generator = $this->getDataGenerator();

        // Add courses.
        $courses = array();
        for ($i = 1; $i < 4; $i++) {
            $courses[] = $generator->create_course();
        };

        // Add users.
        $student = $generator->create_user();
        $teacher = $generator->create_user();

        // Enrol users and add items to courses.
        foreach ($courses as $course) {
            $generator->enrol_user($student->id, $course->id, 'student');
            $forum[] = $this->getDataGenerator()->create_module('forum', array('course' => $course));
            $glossary[] = $this->getDataGenerator()->create_module('glossary', array('course' => $course));
            $chat[] = $this->getDataGenerator()->create_module('chat', array('course' => $course));
        }
        $generator->enrol_user($teacher->id, $courses[0]->id, 'teacher');

        $this->setUser($student);

        // No recent items.
        $result = \block_recentlyaccesseditems\external::get_recent_items();
        $this->assertCount(0, $result);

        // Student access all forums.
        foreach ($forum as $module) {
            $event = \mod_forum\event\course_module_viewed::create(array('context' => context_module::instance($module->cmid),
                    'objectid' => $module->id));
            $event->trigger();
            $this->waitForSecond();
        }

        // Test that only access to forums are returned.
        $result = \block_recentlyaccesseditems\external::get_recent_items();
        $this->assertCount(count($forum), $result);

        // Student access all assignments.
        foreach ($chat as $module) {
            $event = \mod_chat\event\course_module_viewed::create(array('context' => context_module::instance($module->cmid),
                    'objectid' => $module->id));
            $event->trigger();
            $this->waitForSecond();
        }

        // Test that results are sorted by timeaccess DESC (default).
        $result = \block_recentlyaccesseditems\external::get_recent_items();
        $this->assertCount((count($forum) + count($chat)), $result);
        foreach ($result as $key => $record) {
            if ($key == 0) {
                continue;
            }
            $this->assertTrue($record->timeaccess < $result[$key - 1]->timeaccess);
        }
    }
}
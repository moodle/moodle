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
 * Block recentlyaccesseditems observer tests.
 *
 * @package    block_recentlyaccesseditems
 * @copyright  2018 Victor Deniz <victor@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.6
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/assign/tests/generator.php');

/**
 * Block Recently accessed items observer tests.
 *
 * @package    block_recentlyaccesseditems
 * @copyright  2018 Victor Deniz <victor@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.6
 */
class block_recentlyaccesseditems_observer_testcase extends advanced_testcase {
    use mod_assign_test_generator;

    /**
     * Set up for every test
     */
    public function setUp() {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Block table name.
        $this->table = "block_recentlyaccesseditems";

        // Setup test data.
        $this->course = $this->getDataGenerator()->create_course();

        // Create users.
        $this->student = self::getDataGenerator()->create_user();
        $this->teacher = self::getDataGenerator()->create_user();

        // Users enrolments.
        $this->studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));
        $this->getDataGenerator()->enrol_user($this->student->id, $this->course->id, $this->studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($this->teacher->id, $this->course->id, $this->teacherrole->id, 'manual');

        // Create items.
        $this->forum = $this->getDataGenerator()->create_module('forum', array('course' => $this->course));
        $this->glossary = $this->getDataGenerator()->create_module('glossary', array('course' => $this->course));
        $this->chat = $this->getDataGenerator()->create_module('chat', array('course' => $this->course));
    }

    /**
     * Test items views are recorded
     *
     * When items events are triggered they are stored in the block_recentlyaccesseditems table.
     */
    public function test_item_view_recorded_testcase() {
        global $DB;

        // Empty table at the beggining.
        $records = $DB->count_records($this->table, array());
        $this->assertEquals(0, $records);

        // Teacher access forum activity.
        $this->setUser($this->teacher);
        $event = \mod_forum\event\course_module_viewed::create(array('context' => context_module::instance($this->forum->cmid),
                'objectid' => $this->forum->id));
        $event->trigger();

        // Student access chat activity.
        $this->setUser($this->student);
        $event1 = \mod_chat\event\course_module_viewed::create(array('context' => context_module::instance($this->chat->cmid),
                'objectid' => $this->chat->id));
        $event1->trigger();

        $records = $DB->count_records($this->table, array('userid' => $this->teacher->id, 'courseid' => $this->course->id,
                'cmid' => $this->forum->cmid));
        $this->assertEquals(1, $records);

        $records = $DB->count_records($this->table, array('userid' => $this->student->id, 'courseid' => $this->course->id, 'cmid' =>
                $this->chat->cmid));
        $this->assertEquals(1, $records);

        $this->waitForSecond();
        // Student access chat activity again after 1 second (no new record created, timeaccess updated).
        $event2 = \mod_chat\event\course_module_viewed::create(array('context' => context_module::instance($this->chat->cmid),
                'objectid' => $this->chat->id));
        $event2->trigger();

        $records = $DB->get_records($this->table, array('userid' => $this->student->id, 'courseid' => $this->course->id, 'cmid' =>
                $this->chat->cmid));
        $this->assertCount(1, $records);
        $this->assertEquals($event2->timecreated, array_shift($records)->timeaccess);

    }

    /**
     * Test removed items records are deleted.
     *
     * When a course module is removed, the records associated in the block_recentlyaccesseditems table are deleted.
     */
    public function test_item_delete_record_testcase() {
        global $DB;

        // Empty table at the beggining.
        $records = $DB->count_records($this->table, array());
        $this->assertEquals(0, $records);

        // Teacher access forum activity.
        $this->setUser($this->teacher);
        $event = \mod_forum\event\course_module_viewed::create(array('context' => context_module::instance($this->forum->cmid),
                'objectid' => $this->forum->id));
        $event->trigger();

        // Teacher access chat activity.
        $event = \mod_chat\event\course_module_viewed::create(array('context' => context_module::instance($this->chat->cmid),
                'objectid' => $this->chat->id));
        $event->trigger();

        // Student access chat activity.
        $this->setUser($this->student);
        $event = \mod_chat\event\course_module_viewed::create(array('context' => context_module::instance($this->chat->cmid),
                'objectid' => $this->chat->id));
        $event->trigger();

        // Student access forum activity.
        $event = \mod_forum\event\course_module_viewed::create(array('context' => context_module::instance($this->forum->cmid),
                'objectid' => $this->forum->id));
        $event->trigger();

        $records = $DB->count_records($this->table, array('cmid' => $this->forum->cmid));
        $this->assertEquals(2, $records);
        course_delete_module($this->forum->cmid);
        $records = $DB->count_records($this->table, array('cmid' => $this->forum->cmid));
        $this->assertEquals(0, $records);
        $records = $DB->count_records($this->table, array('cmid' => $this->chat->cmid));
        $this->assertEquals(2, $records);
    }
}
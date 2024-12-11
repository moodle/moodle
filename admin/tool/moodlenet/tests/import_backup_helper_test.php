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

namespace tool_moodlenet;

/**
 * Unit tests for the import_backup_helper
 *
 * @package    tool_moodlenet
 * @category   test
 * @copyright  2020 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class import_backup_helper_test extends \advanced_testcase {

    /**
     * Test that the first available context with the capability to upload backup files is returned.
     */
    public function test_get_context_for_user(): void {
        global $DB;

        $this->resetAfterTest();

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        $user4 = $this->getDataGenerator()->create_user();
        $user5 = $this->getDataGenerator()->create_user();

        $course = $this->getDataGenerator()->create_course();
        $coursecontext = \context_course::instance($course->id);

        $this->getDataGenerator()->enrol_user($user1->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($user2->id, $course->id, 'editingteacher');
        $this->getDataGenerator()->enrol_user($user5->id, $course->id, 'student');

        $category = $this->getDataGenerator()->create_category();
        $rolerecord = $DB->get_record('role', ['shortname' => 'manager']);
        $categorycontext = \context_coursecat::instance($category->id);
        $this->getDataGenerator()->role_assign($rolerecord->id, $user3->id, $categorycontext->id);
        $this->getDataGenerator()->role_assign($rolerecord->id, $user5->id, $categorycontext->id);

        $roleid = $this->getDataGenerator()->create_role();
        $sitecontext = \context_system::instance();
        assign_capability('moodle/restore:uploadfile', CAP_ALLOW, $roleid, $sitecontext->id, true);
        accesslib_clear_all_caches_for_unit_testing();
        $this->getDataGenerator()->role_assign($roleid, $user4->id, $sitecontext->id);

        $result = \tool_moodlenet\local\import_backup_helper::get_context_for_user($user1->id);
        $this->assertNull($result);
        $result = \tool_moodlenet\local\import_backup_helper::get_context_for_user($user2->id);
        $this->assertEquals($result, $coursecontext);
        $this->assertEquals(CONTEXT_COURSE, $result->contextlevel);
        $result = \tool_moodlenet\local\import_backup_helper::get_context_for_user($user3->id);
        $this->assertEquals($result, $categorycontext);
        $this->assertEquals(CONTEXT_COURSECAT, $result->contextlevel);
        $result = \tool_moodlenet\local\import_backup_helper::get_context_for_user($user4->id);
        $this->assertEquals($result, $sitecontext);
        $this->assertEquals(CONTEXT_SYSTEM, $result->contextlevel);
        $result = \tool_moodlenet\local\import_backup_helper::get_context_for_user($user5->id);
        $this->assertEquals($result, $categorycontext);
        $this->assertEquals(CONTEXT_COURSECAT, $result->contextlevel);
    }

}

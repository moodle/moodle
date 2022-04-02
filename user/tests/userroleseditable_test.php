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

namespace core_user;

/**
 * Unit tests for user roles editable class.
 *
 * @package    core_user
 * @copyright  2017 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class userroleseditable_test extends \advanced_testcase {
    /**
     * Test user roles editable.
     */
    public function test_update() {
        global $DB;

        $this->resetAfterTest();

        // Create user and modify user profile.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        $course1 = $this->getDataGenerator()->create_course();
        $coursecontext = \context_course::instance($course1->id);
        $teacherrole = $DB->get_record('role', array('shortname' => 'teacher'));
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($user1->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course1->id);
        role_assign($teacherrole->id, $user1->id, $coursecontext->id);
        role_assign($teacherrole->id, $user2->id, $coursecontext->id);

        $this->setAdminUser();
        accesslib_clear_all_caches_for_unit_testing();

        // Use the userroleseditable api to remove all roles from user1 and give user2 student and teacher.
        $itemid = $course1->id . ':' . $user1->id;
        $newvalue = json_encode([]);

        $result = \core_user\output\user_roles_editable::update($itemid, $newvalue);
        $this->assertTrue($result instanceof \core_user\output\user_roles_editable);

        $currentroles = get_user_roles_in_course($user1->id, $course1->id);

        $this->assertEmpty($currentroles);

        $this->setAdminUser();
        accesslib_clear_all_caches_for_unit_testing();

        $itemid = $course1->id . ':' . $user2->id;
        $newvalue = json_encode([$teacherrole->id, $studentrole->id]);

        $result = \core_user\output\user_roles_editable::update($itemid, $newvalue);
        $this->assertTrue($result instanceof \core_user\output\user_roles_editable);
        $currentroles = get_user_roles_in_course($user2->id, $course1->id);

        $this->assertStringContainsString('Non-editing teacher', $currentroles);
        $this->assertStringContainsString('Student', $currentroles);

    }

}

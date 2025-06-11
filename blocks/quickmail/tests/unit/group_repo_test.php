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
 * @package    block_quickmail
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/traits/unit_testcase_traits.php');

use block_quickmail\repos\group_repo;

class block_quickmail_group_repo_testcase extends advanced_testcase {

    use has_general_helpers,
        sets_up_courses;

    public function test_get_course_user_selectable_groups() {
        $this->resetAfterTest(true);

        list($course, $coursecontext, $enrolledusers, $coursegroups) = $this->create_course_with_users_and_groups();

        $redgroup = $coursegroups['red'];
        $yellowgroup = $coursegroups['yellow'];
        $bluegroup = $coursegroups['blue'];

        // Should have access to all three groups.
        $editingteacher = $enrolledusers['editingteacher'][0];

        $groups = group_repo::get_course_user_selectable_groups($course, $editingteacher);

        $firstgroup = current($groups);

        $this->assertIsArray($groups);
        $this->assertCount(3, $groups);
        $this->assertArrayHasKey($redgroup->id, $groups);
        $this->assertArrayHasKey($yellowgroup->id, $groups);
        $this->assertArrayHasKey($bluegroup->id, $groups);
        $this->assertIsObject($firstgroup);
        $this->assertObjectHasAttribute('id', $firstgroup);
        $this->assertObjectHasAttribute('name', $firstgroup);

        $student = $enrolledusers['student'][0];

        // Should have access to only two groups.
        $groups = group_repo::get_course_user_selectable_groups($course, $student);

        $this->assertIsArray($groups);
        $this->assertCount(2, $groups);
        $this->assertArrayHasKey($redgroup->id, $groups);
        $this->assertArrayHasKey($yellowgroup->id, $groups);
        $this->assertArrayNotHasKey($bluegroup->id, $groups);
    }

    public function test_get_course_user_groups() {
        $this->resetAfterTest(true);

        list($course, $coursecontext, $enrolledusers, $coursegroups) = $this->create_course_with_users_and_groups();

        $redgroup = $coursegroups['red'];
        $yellowgroup = $coursegroups['yellow'];
        $bluegroup = $coursegroups['blue'];

        // Should not be in any groups.
        $editingteacher = $enrolledusers['editingteacher'][0];

        $groups = group_repo::get_course_user_groups($course, $editingteacher, $coursecontext);

        $this->assertIsArray($groups);
        $this->assertCount(0, $groups);

        $student = $enrolledusers['student'][0];

        // Should have access to only two groups (red and yellow).
        $groups = group_repo::get_course_user_groups($course, $student, $coursecontext);

        $firstgroup = current($groups);

        $this->assertIsArray($groups);
        $this->assertCount(2, $groups);
        $this->assertArrayHasKey($redgroup->id, $groups);
        $this->assertArrayHasKey($yellowgroup->id, $groups);
        $this->assertArrayNotHasKey($bluegroup->id, $groups);
        $this->assertIsObject($firstgroup);
        $this->assertObjectHasAttribute('id', $firstgroup);
        $this->assertObjectHasAttribute('name', $firstgroup);

        $student = $enrolledusers['student'][38];

        // Should not be in any groups.
        $groups = group_repo::get_course_user_groups($course, $student, $coursecontext);

        $firstgroup = current($groups);

        $this->assertIsArray($groups);
        $this->assertCount(0, $groups);
    }

}

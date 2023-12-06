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

namespace report_log;

/**
 * Class report_log\renderable_test to cover functions in \report_log_renderable.
 *
 * @package    report_log
 * @copyright  2023 Stephan Robotta <stephan.robotta@bfh.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
class renderable_test extends \advanced_testcase {

    /**
     * @var [stdClass] The students.
     */
    private $student = [];

    /**
     * @var [stdClass] The teachers.
     */
    private $teacher = [];

    /**
     * @var [stdClass] The groups.
     */
    private $group = [];

    /**
     * @var stdClass The course.
     */
    private $course;

    /**
     * Set up a course with two groups, three students being each in one of the groups,
     * two teachers each in either group while the second teacher is also member of the other group.
     * @return void
     * @throws \coding_exception
     */
    public function setUp(): void {
        global $PAGE;
        $this->course = $this->getDataGenerator()->create_course(['groupmode' => 1]);
        $this->group[] = $this->getDataGenerator()->create_group(['courseid' => $this->course->id]);
        $this->group[] = $this->getDataGenerator()->create_group(['courseid' => $this->course->id]);

        for ($i = 0; $i < 3; $i++) {
            $this->student[$i] = $this->getDataGenerator()->create_user();
            $this->getDataGenerator()->enrol_user($this->student[$i]->id, $this->course->id, 'student');
            $this->getDataGenerator()->create_group_member([
                'groupid' => $this->group[$i % 2]->id,
                'userid' => $this->student[$i]->id,
            ]);
        }
        for ($i = 0; $i < 2; $i++) {
            $this->teacher[$i] = $this->getDataGenerator()->create_user();
            $this->getDataGenerator()->enrol_user($this->teacher[$i]->id, $this->course->id, 'editingteacher');
            $this->getDataGenerator()->create_group_member([
                'groupid' => $this->group[$i]->id,
                'userid' => $this->teacher[$i]->id,
            ]);
        }
        // Make teacher2 also member of group1.
        $this->getDataGenerator()->create_group_member([
            'groupid' => $this->group[0]->id,
            'userid' => $this->teacher[1]->id,
        ]);

        $PAGE->set_url('/report/log/index.php?id=' . $this->course->id);
        $this->resetAfterTest();
    }

    /**
     * Test report_log_renderable::get_user_list().
     * @covers \report_log_renderable::get_user_list
     * @return void
     */
    public function test_get_user_list() {
        // Fetch all users of group 1 and the guest user.
        $userids = $this->fetch_users_from_renderable((int)$this->student[0]->id);
        $this->assertCount(5, $userids);
        $this->assertContains((int)$this->student[0]->id, $userids); // His own group (group 1).
        $this->assertNotContains((int)$this->student[1]->id, $userids); // He is in group 2.
        $this->assertContains((int)$this->teacher[0]->id, $userids); // He is in group 1.
        $this->assertContains((int)$this->teacher[1]->id, $userids); // He is in both groups.

        // Fetch users of all groups and the guest user. The teacher has the capability moodle/site:accessallgroups.
        $this->setUser($this->teacher[1]->id);
        $renderable = new \report_log_renderable("", (int)$this->course->id, $this->teacher[1]->id);
        $users = $renderable->get_user_list();
        $this->assertCount(6, $users);

        // Fetch users of group 2 and the guest user.
        $userids = $this->fetch_users_from_renderable((int)$this->student[1]->id);
        $this->assertCount( 3, $userids);
        $this->assertNotContains((int)$this->student[0]->id, $userids);
        $this->assertContains((int)$this->student[1]->id, $userids);
        $this->assertNotContains((int)$this->teacher[0]->id, $userids);
        $this->assertContains((int)$this->teacher[1]->id, $userids);

        // Fetch users of group 2 and test user as teacher2 but limited to his group.
        $userids = $this->fetch_users_from_renderable((int)$this->teacher[1]->id, (int)$this->group[1]->id);
        $this->assertCount( 3, $userids);
        $this->assertNotContains((int)$this->student[0]->id, $userids);
        $this->assertContains((int)$this->student[1]->id, $userids);
        $this->assertNotContains((int)$this->teacher[0]->id, $userids);
        $this->assertContains((int)$this->teacher[1]->id, $userids);

    }

    /**
     * Helper function to return a list of user ids from the renderable object.
     * @param int $userid
     * @param ?int $groupid
     * @return array
     */
    protected function fetch_users_from_renderable(int $userid, ?int $groupid = 0): array {
        $this->setUser($userid);
        $renderable = new \report_log_renderable(
            "", (int)$this->course->id, $userid, 0, '', $groupid);
        $users = $renderable->get_user_list();
        return \array_keys($users);
    }

    /**
     * Test report_log_renderable::get_group_list().
     * @covers \report_log_renderable::get_group_list
     * @return void
     */
    public function test_get_group_list() {

        // The student sees his own group only.
        $this->setUser($this->student[0]->id);
        $renderable = new \report_log_renderable("", (int)$this->course->id, $this->student[0]->id);
        $groups = $renderable->get_group_list();
        $this->assertCount(1, $groups);

        // While the teacher is allowed to see all groups.
        $this->setUser($this->teacher[0]->id);
        $renderable = new \report_log_renderable("", (int)$this->course->id, $this->teacher[0]->id);
        $groups = $renderable->get_group_list();
        $this->assertCount(2, $groups);

    }
}

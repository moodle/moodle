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

namespace core;

/**
 * Tests the report_helper class.
 *
 * @covers \core\report_helper
 * @package core
 * @category test
 * @copyright 2024 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class report_helper_test extends \advanced_testcase {
    /** @var int[] Array of created user ids */
    protected array $userids;

    /**
     * Tests {@see report_helper::get_group_filter()}.
     */
    public function test_get_group_filter(): void {
        $this->resetAfterTest();

        // Create some test course, groups, and users.
        $generator = self::getDataGenerator();

        $vgcourse = $generator->create_course(['groupmode' => VISIBLEGROUPS]);
        $sgcourse = $generator->create_course(['groupmode' => SEPARATEGROUPS]);

        $vg1 = $generator->create_group(['courseid' => $vgcourse->id]);
        $vg2 = $generator->create_group(['courseid' => $vgcourse->id]);
        $sg1 = $generator->create_group(['courseid' => $sgcourse->id]);
        $sg2 = $generator->create_group(['courseid' => $sgcourse->id]);

        $this->userids = [];
        for ($i = 0; $i < 10; $i++) {
            $this->userids[$i] = $generator->create_user()->id;
            $generator->enrol_user($this->userids[$i], ($i < 5) ? $vgcourse->id : $sgcourse->id, 'student');
        }

        groups_add_member($vg1, $this->userids[0]);
        groups_add_member($vg1, $this->userids[1]);
        groups_add_member($vg2, $this->userids[0]);
        groups_add_member($vg2, $this->userids[2]);
        groups_add_member($vg2, $this->userids[3]);

        groups_add_member($sg1, $this->userids[5]);
        groups_add_member($sg1, $this->userids[6]);
        groups_add_member($sg2, $this->userids[5]);
        groups_add_member($sg2, $this->userids[7]);
        groups_add_member($sg2, $this->userids[8]);

        // Teacher user has access all groups.
        $teacher = $generator->create_user();
        $generator->enrol_user($teacher->id, $vgcourse->id, 'editingteacher');
        $generator->enrol_user($teacher->id, $sgcourse->id, 'editingteacher');

        // With specified groups on either course (does not matter who user is).
        $this->assert_group_filter([0, 1], ['courseid' => $vgcourse->id, 'groupid' => $vg1->id]);
        $this->assert_group_filter([0, 2, 3], ['courseid' => $vgcourse->id, 'groupid' => $vg2->id]);
        $this->assert_group_filter([5, 6], ['courseid' => $sgcourse->id, 'groupid' => $sg1->id]);
        $this->assert_group_filter([5, 7, 8], ['courseid' => $sgcourse->id, 'groupid' => $sg2->id]);

        // With specified group and user.
        $this->assert_group_filter([2], [
            'courseid' => $vgcourse->id,
            'groupid' => $vg2->id,
            'userid' => $this->userids[2],
        ]);
        $this->assert_group_filter([6], [
            'courseid' => $sgcourse->id,
            'groupid' => $sg2->id,
            'userid' => $this->userids[6],
        ]);

        // No restrictions, user belongs to a group or to both groups on VG course.
        $this->setUser($this->userids[1]);
        $all = array_keys($this->userids);
        $this->assert_group_filter($all, ['courseid' => $vgcourse->id]);
        $this->setUser($this->userids[0]);
        $this->assert_group_filter($all, ['courseid' => $vgcourse->id]);

        // No restrictions, user belongs to a group or to both groups on SG course.
        $this->setUser($this->userids[6]);
        $this->assert_group_filter([5, 6], ['courseid' => $sgcourse->id]);
        $this->setUser($this->userids[5]);
        $this->assert_group_filter([5, 6, 7, 8], ['courseid' => $sgcourse->id]);

        // No restrictions, user has access all groups on either course.
        $this->setUser($teacher);
        $this->assert_group_filter($all, ['courseid' => $vgcourse->id]);
        $this->assert_group_filter($all, ['courseid' => $sgcourse->id]);

        // There was a performance issue for users with access all groups where it listed all users
        // in the system in the 'filter' list, now it doesn't.
        $this->assertNull(report_helper::get_group_filter(
            (object)['courseid' => $sgcourse->id],
        )['useridfilter']);

        // Specified group even if you have AAG.
        $this->assert_group_filter([0, 1], ['courseid' => $vgcourse->id, 'groupid' => $vg1->id]);
        $this->assert_group_filter([5, 6], ['courseid' => $sgcourse->id, 'groupid' => $sg1->id]);

        // No restrictions, user does not belong to a group on course. Makes no difference in VG.
        $this->setUser($this->userids[5]);
        $this->assert_group_filter($all, ['courseid' => $vgcourse->id]);

        // In SG user can now view all users across system who are not in a group on course.
        // Strange but true.
        $this->setUser($this->userids[0]);
        $this->assert_group_filter([0, 1, 2, 3, 4, 9], ['courseid' => $sgcourse->id]);
    }

    /**
     * Calls {@see report_helper::get_group_filter()} and checks which of the users created by this
     * unit test are returned.
     *
     * @param int[] $expecteduserindexes Expected user indexes
     * @param array $filterparams Array of filter parameters to pass to get_group_filter
     */
    protected function assert_group_filter(array $expecteduserindexes, array $filterparams): void {
        global $DB;

        $result = report_helper::get_group_filter((object)$filterparams);

        // Combine the joins (if any). 'TRUE' is not allowed in SQL Server, you must use '1 = 1'.
        $where = '1 = 1';
        foreach ($result['joins'] as $join) {
            $where .= ' AND ' . $join;
        }

        // The joins use field 'userid' so we make a subselect table with that field name.
        $userids = $DB->get_fieldset_sql("
            SELECT userid
              FROM (SELECT id AS userid FROM {user}) userdata
             WHERE $where", $result['params']);

        if ($result['useridfilter'] !== null) {
            $userids = array_filter($userids, fn($userid) => array_key_exists($userid, $result['useridfilter']));
        }

        // Convert user ids to expected indexes, exclude any results not in our test user list, and sort.
        $indexes = array_map(fn($userid) => array_search($userid, $this->userids), $userids);
        $indexes = array_filter($indexes, fn($index) => $index !== false);
        sort($indexes);
        $this->assertEquals($expecteduserindexes, $indexes);
    }

}

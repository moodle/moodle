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

namespace tool_cohortroles;

/**
 * API tests.
 *
 * @package    tool_cohortroles
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class api_test extends \advanced_testcase {
    /** @var \stdClass $cohort */
    protected $cohort = null;

    /** @var \stdClass $userassignto */
    protected $userassignto = null;

    /** @var \stdClass $userassignover */
    protected $userassignover = null;

    /** @var \stdClass $role */
    protected $role = null;

    /** @var int $roleid */
    protected $roleid;

    /**
     * Setup function- we will create a course and add an assign instance to it.
     */
    protected function setUp(): void {
        $this->resetAfterTest(true);

        // Create some users.
        $this->cohort = $this->getDataGenerator()->create_cohort();
        $this->userassignto = $this->getDataGenerator()->create_user();
        $this->userassignover = $this->getDataGenerator()->create_user();
        $this->roleid = create_role('Sausage Roll', 'sausageroll', 'mmmm');
        cohort_add_member($this->cohort->id, $this->userassignover->id);
    }

    public function test_create_cohort_role_assignment_without_permission() {
        $this->setUser($this->userassignto);
        $params = (object) array(
            'userid' => $this->userassignto->id,
            'roleid' => $this->roleid,
            'cohortid' => $this->cohort->id
        );
        $this->expectException(\required_capability_exception::class);
        api::create_cohort_role_assignment($params);
    }

    public function test_create_cohort_role_assignment_with_invalid_data() {
        $this->setAdminUser();
        $params = (object) array(
            'userid' => $this->userassignto->id,
            'roleid' => -8,
            'cohortid' => $this->cohort->id
        );
        $this->expectException(\core_competency\invalid_persistent_exception::class);
        api::create_cohort_role_assignment($params);
    }

    public function test_create_cohort_role_assignment() {
        $this->setAdminUser();
        $params = (object) array(
            'userid' => $this->userassignto->id,
            'roleid' => $this->roleid,
            'cohortid' => $this->cohort->id
        );
        $result = api::create_cohort_role_assignment($params);
        $this->assertNotEmpty($result->get('id'));
        $this->assertEquals($result->get('userid'), $this->userassignto->id);
        $this->assertEquals($result->get('roleid'), $this->roleid);
        $this->assertEquals($result->get('cohortid'), $this->cohort->id);
    }

    public function test_delete_cohort_role_assignment_without_permission() {
        $this->setAdminUser();
        $params = (object) array(
            'userid' => $this->userassignto->id,
            'roleid' => $this->roleid,
            'cohortid' => $this->cohort->id
        );
        $result = api::create_cohort_role_assignment($params);
        $this->setUser($this->userassignto);
        $this->expectException(\required_capability_exception::class);
        api::delete_cohort_role_assignment($result->get('id'));
    }

    public function test_delete_cohort_role_assignment_with_invalid_data() {
        $this->setAdminUser();
        $params = (object) array(
            'userid' => $this->userassignto->id,
            'roleid' => $this->roleid,
            'cohortid' => $this->cohort->id
        );
        $result = api::create_cohort_role_assignment($params);
        $this->expectException(\dml_missing_record_exception::class);
        api::delete_cohort_role_assignment($result->get('id') + 1);
    }

    public function test_delete_cohort_role_assignment() {
        $this->setAdminUser();
        // Create a cohort role assigment.
        $params = (object) [
            'userid' => $this->userassignto->id,
            'roleid' => $this->roleid,
            'cohortid' => $this->cohort->id
        ];
        $cohortroleassignment = api::create_cohort_role_assignment($params);
        $sync = api::sync_all_cohort_roles();
        $rolesadded = [
            [
                'useridassignedto' => $this->userassignto->id,
                'useridassignedover' => $this->userassignover->id,
                'roleid' => $this->roleid
            ]
        ];
        $expected = [
            'rolesadded' => $rolesadded,
            'rolesremoved' => []
        ];
        $this->assertEquals($sync, $expected);

        // Delete the cohort role assigment and confirm the roles are removed.
        $result = api::delete_cohort_role_assignment($cohortroleassignment->get('id'));
        $this->assertTrue($result);
        $sync = api::sync_all_cohort_roles();
        $expected = [
            'rolesadded' => [],
            'rolesremoved' => $rolesadded
        ];
        $this->assertEquals($expected, $sync);
    }

    /**
     * Test case verifying that syncing won't remove role assignments if they are valid for another cohort role assignment.
     */
    public function test_delete_cohort_role_assignment_cohorts_having_same_members() {
        $this->setAdminUser();

        // Create 2 cohorts, with a 1 user (user1) present in both,
        // and user2 and user3 members of 1 cohort each.
        $cohort1 = $this->getDataGenerator()->create_cohort();
        $cohort2 = $this->getDataGenerator()->create_cohort();
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        cohort_add_member($cohort1->id, $user1->id);
        cohort_add_member($cohort1->id, $user2->id);
        cohort_add_member($cohort2->id, $user1->id);
        cohort_add_member($cohort2->id, $user3->id);

        // And a role and a user to assign that role to.
        $user4 = $this->getDataGenerator()->create_user(); // A cohort manager, for example.
        $roleid = create_role('Role 1', 'myrole', 'test');

        // Assign the role for user4 in both cohorts.
        $params = (object) [
            'userid' => $user4->id,
            'roleid' => $roleid,
            'cohortid' => $cohort1->id
        ];
        $cohort1roleassignment = api::create_cohort_role_assignment($params);
        $params->cohortid = $cohort2->id;
        $cohort2roleassignment = api::create_cohort_role_assignment($params);

        $sync = api::sync_all_cohort_roles();

        // There is no guarantee about the order of roles assigned.
        // so confirm we have 3 role assignments, and they are for the users 1, 2 and 3.
        $this->assertCount(3, $sync['rolesadded']);
        $addedusers = array_column($sync['rolesadded'], 'useridassignedover');
        $this->assertContains($user1->id, $addedusers);
        $this->assertContains($user2->id, $addedusers);
        $this->assertContains($user3->id, $addedusers);

        // Remove the role assignment for user4/cohort1.
        // Verify only 1 role is unassigned as the others are still valid for the other cohort role assignment.
        $result = api::delete_cohort_role_assignment($cohort1roleassignment->get('id'));
        $this->assertTrue($result);

        $sync = api::sync_all_cohort_roles();

        $this->assertCount(0, $sync['rolesadded']);
        $this->assertCount(1, $sync['rolesremoved']);
        $removedusers = array_column($sync['rolesremoved'], 'useridassignedover');
        $this->assertContains($user2->id, $removedusers);
    }

    public function test_list_cohort_role_assignments() {
        $this->setAdminUser();
        $params = (object) array(
            'userid' => $this->userassignto->id,
            'roleid' => $this->roleid,
            'cohortid' => $this->cohort->id
        );
        $result = api::create_cohort_role_assignment($params);

        $list = api::list_cohort_role_assignments();
        $list[0]->is_valid();
        $this->assertEquals($list[0], $result);
    }

    public function test_count_cohort_role_assignments() {
        $this->setAdminUser();
        $params = (object) array(
            'userid' => $this->userassignto->id,
            'roleid' => $this->roleid,
            'cohortid' => $this->cohort->id
        );
        $result = api::create_cohort_role_assignment($params);

        $count = api::count_cohort_role_assignments();
        $this->assertEquals($count, 1);
    }

    public function test_sync_all_cohort_roles() {
        $this->setAdminUser();
        $params = (object) array(
            'userid' => $this->userassignto->id,
            'roleid' => $this->roleid,
            'cohortid' => $this->cohort->id
        );
        $result = api::create_cohort_role_assignment($params);

        // Verify roles are assigned when users enter the cohort.
        $sync = api::sync_all_cohort_roles();

        $rolesadded = array(array(
            'useridassignedto' => $this->userassignto->id,
            'useridassignedover' => $this->userassignover->id,
            'roleid' => $this->roleid
        ));
        $rolesremoved = array();
        $expected = array('rolesadded' => $rolesadded,
                          'rolesremoved' => $rolesremoved);
        $this->assertEquals($sync, $expected);

        // Verify roles are removed when users leave the cohort.
        cohort_remove_member($this->cohort->id, $this->userassignover->id);
        $sync = api::sync_all_cohort_roles();

        $rolesadded = array();
        $rolesremoved = array(array(
            'useridassignedto' => $this->userassignto->id,
            'useridassignedover' => $this->userassignover->id,
            'roleid' => $this->roleid
        ));
        $expected = array('rolesadded' => $rolesadded,
                          'rolesremoved' => $rolesremoved);
        $this->assertEquals($sync, $expected);

        // Verify roles assigned by any other component are not removed.
        $usercontext = \context_user::instance($this->userassignover->id);
        role_assign($this->roleid, $this->userassignto->id, $usercontext->id);
        $sync = api::sync_all_cohort_roles();

        $rolesadded = array();
        $rolesremoved = array();
        $expected = array('rolesadded' => $rolesadded,
                          'rolesremoved' => $rolesremoved);
        $this->assertEquals($sync, $expected);

        // Remove manual role assignment.
        role_unassign($this->roleid, $this->userassignto->id, $usercontext->id);
        // Add someone to the cohort again...
        cohort_add_member($this->cohort->id, $this->userassignover->id);
        $sync = api::sync_all_cohort_roles();
        $rolesadded = array(array(
            'useridassignedto' => $this->userassignto->id,
            'useridassignedover' => $this->userassignover->id,
            'roleid' => $this->roleid
        ));
        $rolesremoved = array();
        $expected = array('rolesadded' => $rolesadded,
                          'rolesremoved' => $rolesremoved);
        $this->assertEquals($sync, $expected);

        // Verify no fatal errors when a cohort is deleted.
        cohort_delete_cohort($this->cohort);
        $sync = api::sync_all_cohort_roles();

        $rolesadded = array();
        $rolesremoved = array(array(
            'useridassignedto' => $this->userassignto->id,
            'useridassignedover' => $this->userassignover->id,
            'roleid' => $this->roleid
        ));
        $expected = array('rolesadded' => $rolesadded,
                          'rolesremoved' => $rolesremoved);
        $this->assertEquals($sync, $expected);
    }

}

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
 * API tests.
 *
 * @package    tool_cohortroles
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use tool_cohortroles\api;

/**
 * API tests.
 *
 * @package    tool_cohortroles
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_cohortroles_api_testcase extends advanced_testcase {
    /** @var stdClass $cohort */
    protected $cohort = null;

    /** @var stdClass $userassignto */
    protected $userassignto = null;

    /** @var stdClass $userassignover */
    protected $userassignover = null;

    /** @var stdClass $role */
    protected $role = null;

    /**
     * Setup function- we will create a course and add an assign instance to it.
     */
    protected function setUp() {
        global $DB;

        $this->resetAfterTest(true);

        // Create some users.
        $this->cohort = $this->getDataGenerator()->create_cohort();
        $this->userassignto = $this->getDataGenerator()->create_user();
        $this->userassignover = $this->getDataGenerator()->create_user();
        $this->roleid = create_role('Sausage Roll', 'sausageroll', 'mmmm');
        cohort_add_member($this->cohort->id, $this->userassignover->id);
    }

    /**
     * @expectedException required_capability_exception
     */
    public function test_create_cohort_role_assignment_without_permission() {
        $this->setUser($this->userassignto);
        $params = (object) array(
            'userid' => $this->userassignto->id,
            'roleid' => $this->roleid,
            'cohortid' => $this->cohort->id
        );
        api::create_cohort_role_assignment($params);
    }

    /**
     * @expectedException core_competency\invalid_persistent_exception
     */
    public function test_create_cohort_role_assignment_with_invalid_data() {
        $this->setAdminUser();
        $params = (object) array(
            'userid' => $this->userassignto->id,
            'roleid' => -8,
            'cohortid' => $this->cohort->id
        );
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

    /**
     * @expectedException required_capability_exception
     */
    public function test_delete_cohort_role_assignment_without_permission() {
        $this->setAdminUser();
        $params = (object) array(
            'userid' => $this->userassignto->id,
            'roleid' => $this->roleid,
            'cohortid' => $this->cohort->id
        );
        $result = api::create_cohort_role_assignment($params);
        $this->setUser($this->userassignto);
        api::delete_cohort_role_assignment($result->get('id'));
    }

    /**
     * @expectedException dml_missing_record_exception
     */
    public function test_delete_cohort_role_assignment_with_invalid_data() {
        $this->setAdminUser();
        $params = (object) array(
            'userid' => $this->userassignto->id,
            'roleid' => $this->roleid,
            'cohortid' => $this->cohort->id
        );
        $result = api::create_cohort_role_assignment($params);
        api::delete_cohort_role_assignment($result->get('id') + 1);
    }

    public function test_delete_cohort_role_assignment() {
        $this->setAdminUser();
        $params = (object) array(
            'userid' => $this->userassignto->id,
            'roleid' => $this->roleid,
            'cohortid' => $this->cohort->id
        );
        $result = api::create_cohort_role_assignment($params);
        $worked = api::delete_cohort_role_assignment($result->get('id'));
        $this->assertTrue($worked);
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
        $usercontext = context_user::instance($this->userassignover->id);
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

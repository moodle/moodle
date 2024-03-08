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

namespace tool_dataprivacy;

use core\invalid_persistent_exception;
use core\task\manager;
use testing_data_generator;
use tool_dataprivacy\local\helper;
use tool_dataprivacy\task\process_data_request_task;
use tool_dataprivacy\task\initiate_data_request_task;

/**
 * API tests.
 *
 * @package    tool_dataprivacy
 * @covers     \tool_dataprivacy\api
 * @copyright  2018 Jun Pataleta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class api_test extends \advanced_testcase {

    /**
     * Ensure that the check_can_manage_data_registry function fails cap testing when a user without capabilities is
     * tested with the default context.
     */
    public function test_check_can_manage_data_registry_admin() {
        $this->resetAfterTest();

        $this->setAdminUser();
        // Technically this actually returns void, but assertNull will suffice to avoid a pointless test.
        $this->assertNull(api::check_can_manage_data_registry());
    }

    /**
     * Ensure that the check_can_manage_data_registry function fails cap testing when a user without capabilities is
     * tested with the default context.
     */
    public function test_check_can_manage_data_registry_without_cap_default() {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $this->expectException(\required_capability_exception::class);
        api::check_can_manage_data_registry();
    }

    /**
     * Ensure that the check_can_manage_data_registry function fails cap testing when a user without capabilities is
     * tested with the default context.
     */
    public function test_check_can_manage_data_registry_without_cap_system() {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $this->expectException(\required_capability_exception::class);
        api::check_can_manage_data_registry(\context_system::instance()->id);
    }

    /**
     * Ensure that the check_can_manage_data_registry function fails cap testing when a user without capabilities is
     * tested with the default context.
     */
    public function test_check_can_manage_data_registry_without_cap_own_user() {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $this->expectException(\required_capability_exception::class);
        api::check_can_manage_data_registry(\context_user::instance($user->id)->id);
    }

    /**
     * Test for api::update_request_status().
     */
    public function test_update_request_status() {
        $this->resetAfterTest();

        $generator = new testing_data_generator();
        $s1 = $generator->create_user();
        $this->setUser($s1);

        // Create the sample data request.
        $datarequest = api::create_data_request($s1->id, api::DATAREQUEST_TYPE_EXPORT);

        $requestid = $datarequest->get('id');

        // Update with a comment.
        $comment = 'This is an example of a comment';
        $result = api::update_request_status($requestid, api::DATAREQUEST_STATUS_AWAITING_APPROVAL, 0, $comment);
        $this->assertTrue($result);
        $datarequest = new data_request($requestid);
        $this->assertStringEndsWith($comment, $datarequest->get('dpocomment'));

        // Update with a comment which will be trimmed.
        $result = api::update_request_status($requestid, api::DATAREQUEST_STATUS_AWAITING_APPROVAL, 0, '  ');
        $this->assertTrue($result);
        $datarequest = new data_request($requestid);
        $this->assertStringEndsWith($comment, $datarequest->get('dpocomment'));

        // Update with a comment.
        $secondcomment = '  - More comments -  ';
        $result = api::update_request_status($requestid, api::DATAREQUEST_STATUS_AWAITING_APPROVAL, 0, $secondcomment);
        $this->assertTrue($result);
        $datarequest = new data_request($requestid);
        $this->assertMatchesRegularExpression("/.*{$comment}.*{$secondcomment}/s", $datarequest->get('dpocomment'));

        // Update with a valid status.
        $result = api::update_request_status($requestid, api::DATAREQUEST_STATUS_DOWNLOAD_READY);
        $this->assertTrue($result);

        // Fetch the request record again.
        $datarequest = new data_request($requestid);
        $this->assertEquals(api::DATAREQUEST_STATUS_DOWNLOAD_READY, $datarequest->get('status'));

        // Update with an invalid status.
        $this->expectException(invalid_persistent_exception::class);
        api::update_request_status($requestid, -1);
    }

    /**
     * Test for api::get_site_dpos() when there are no users with the DPO role.
     */
    public function test_get_site_dpos_no_dpos() {
        $this->resetAfterTest();

        $admin = get_admin();

        $dpos = api::get_site_dpos();
        $this->assertCount(1, $dpos);
        $dpo = reset($dpos);
        $this->assertEquals($admin->id, $dpo->id);
    }

    /**
     * Test for api::get_site_dpos() when there are no users with the DPO role.
     */
    public function test_get_site_dpos() {
        global $DB;

        $this->resetAfterTest();

        $generator = new testing_data_generator();
        $u1 = $generator->create_user();
        $u2 = $generator->create_user();

        $context = \context_system::instance();

        // Give the manager role with the capability to manage data requests.
        $managerroleid = $DB->get_field('role', 'id', array('shortname' => 'manager'));
        assign_capability('tool/dataprivacy:managedatarequests', CAP_ALLOW, $managerroleid, $context->id, true);
        // Assign u1 as a manager.
        role_assign($managerroleid, $u1->id, $context->id);

        // Give the editing teacher role with the capability to manage data requests.
        $editingteacherroleid = $DB->get_field('role', 'id', array('shortname' => 'editingteacher'));
        assign_capability('tool/dataprivacy:managedatarequests', CAP_ALLOW, $editingteacherroleid, $context->id, true);
        // Assign u1 as an editing teacher as well.
        role_assign($editingteacherroleid, $u1->id, $context->id);
        // Assign u2 as an editing teacher.
        role_assign($editingteacherroleid, $u2->id, $context->id);

        // Only map the manager role to the DPO role.
        set_config('dporoles', $managerroleid, 'tool_dataprivacy');

        $dpos = api::get_site_dpos();
        $this->assertCount(1, $dpos);
        $dpo = reset($dpos);
        $this->assertEquals($u1->id, $dpo->id);
    }

    /**
     * Test for \tool_dataprivacy\api::get_assigned_privacy_officer_roles().
     */
    public function test_get_assigned_privacy_officer_roles() {
        global $DB;

        $this->resetAfterTest();

        // Erroneously set the manager roles as the PO, even if it doesn't have the managedatarequests capability yet.
        $managerroleid = $DB->get_field('role', 'id', array('shortname' => 'manager'));
        set_config('dporoles', $managerroleid, 'tool_dataprivacy');
        // Get the assigned PO roles when nothing has been set yet.
        $roleids = api::get_assigned_privacy_officer_roles();
        // Confirm that the returned list is empty.
        $this->assertEmpty($roleids);

        $context = \context_system::instance();

        // Give the manager role with the capability to manage data requests.
        assign_capability('tool/dataprivacy:managedatarequests', CAP_ALLOW, $managerroleid, $context->id, true);

        // Give the editing teacher role with the capability to manage data requests.
        $editingteacherroleid = $DB->get_field('role', 'id', array('shortname' => 'editingteacher'));
        assign_capability('tool/dataprivacy:managedatarequests', CAP_ALLOW, $editingteacherroleid, $context->id, true);

        // Get the non-editing teacher role ID.
        $teacherroleid = $DB->get_field('role', 'id', array('shortname' => 'teacher'));

        // Erroneously map the manager and the non-editing teacher roles to the PO role.
        $badconfig = $managerroleid . ',' . $teacherroleid;
        set_config('dporoles', $badconfig, 'tool_dataprivacy');

        // Get the assigned PO roles.
        $roleids = api::get_assigned_privacy_officer_roles();

        // There should only be one PO role.
        $this->assertCount(1, $roleids);
        // Confirm it contains the manager role.
        $this->assertContainsEquals($managerroleid, $roleids);
        // And it does not contain the editing teacher role.
        $this->assertNotContainsEquals($editingteacherroleid, $roleids);
    }

    /**
     * Test for api::approve_data_request().
     */
    public function test_approve_data_request() {
        global $DB;

        $this->resetAfterTest();

        $generator = new testing_data_generator();
        $s1 = $generator->create_user();
        $u1 = $generator->create_user();

        $context = \context_system::instance();

        // Manager role.
        $managerroleid = $DB->get_field('role', 'id', array('shortname' => 'manager'));
        // Give the manager role with the capability to manage data requests.
        assign_capability('tool/dataprivacy:managedatarequests', CAP_ALLOW, $managerroleid, $context->id, true);
        // Assign u1 as a manager.
        role_assign($managerroleid, $u1->id, $context->id);

        // Map the manager role to the DPO role.
        set_config('dporoles', $managerroleid, 'tool_dataprivacy');

        // Create the sample data request.
        $this->setUser($s1);
        $datarequest = api::create_data_request($s1->id, api::DATAREQUEST_TYPE_EXPORT);
        $requestid = $datarequest->get('id');

        // Make this ready for approval.
        api::update_request_status($requestid, api::DATAREQUEST_STATUS_AWAITING_APPROVAL);

        $this->setUser($u1);
        $result = api::approve_data_request($requestid);
        $this->assertTrue($result);
        $datarequest = new data_request($requestid);
        $this->assertEquals($u1->id, $datarequest->get('dpo'));
        $this->assertEquals(api::DATAREQUEST_STATUS_APPROVED, $datarequest->get('status'));

        // Test adhoc task creation.
        $adhoctasks = manager::get_adhoc_tasks(process_data_request_task::class);
        $this->assertCount(1, $adhoctasks);
    }

    /**
     * Test for api::approve_data_request() when allow filtering of exports by course.
     */
    public function test_approve_data_request_with_allow_filtering() {
        global $DB;
        $this->resetAfterTest();
        set_config('allowfiltering', 1, 'tool_dataprivacy');
        $this->setAdminUser();

        $generator = new testing_data_generator();
        $s1 = $generator->create_user();
        $u1 = $generator->create_user();

        $context = \context_system::instance();

        // Manager role.
        $managerroleid = $DB->get_field('role', 'id', array('shortname' => 'manager'));
        // Give the manager role with the capability to manage data requests.
        assign_capability('tool/dataprivacy:managedatarequests', CAP_ALLOW, $managerroleid, $context->id, true);
        // Assign u1 as a manager.
        role_assign($managerroleid, $u1->id, $context->id);

        // Map the manager role to the DPO role.
        set_config('dporoles', $managerroleid, 'tool_dataprivacy');

        $course = $this->getDataGenerator()->create_course([]);

        $coursecontext1 = \context_course::instance($course->id);

        $this->getDataGenerator()->enrol_user($s1->id, $course->id, 'student');

        $datarequest = api::create_data_request($s1->id, api::DATAREQUEST_TYPE_EXPORT);
        $requestid = $datarequest->get('id');
        ob_start();
        $this->runAdhocTasks('tool_dataprivacy\task\initiate_data_request_task');
        ob_end_clean();

        $this->setUser($u1);
        $result = api::approve_data_request($requestid, [$coursecontext1]);
        $this->assertTrue($result);
        $datarequest = new data_request($requestid);
        $this->assertEquals($u1->id, $datarequest->get('dpo'));
        $this->assertEquals(api::DATAREQUEST_STATUS_APPROVED, $datarequest->get('status'));

        // Test adhoc task creation.
        $adhoctasks = manager::get_adhoc_tasks(process_data_request_task::class);
        $this->assertCount(1, $adhoctasks);
    }

    /**
     * Test for api::approve_data_request() when called by a user who doesn't have the DPO role.
     */
    public function test_approve_data_request_non_dpo_user() {
        $this->resetAfterTest();

        $generator = new testing_data_generator();
        $student = $generator->create_user();
        $teacher = $generator->create_user();

        // Create the sample data request.
        $this->setUser($student);
        $datarequest = api::create_data_request($student->id, api::DATAREQUEST_TYPE_EXPORT);

        $requestid = $datarequest->get('id');

        // Login as a user without DPO role.
        $this->setUser($teacher);
        $this->expectException(\required_capability_exception::class);
        api::approve_data_request($requestid);
    }

    /**
     * Test for api::add_request_contexts_with_status().
     */
    public function test_add_request_contexts_with_status() {
        global $DB;
        $this->resetAfterTest();
        set_config('allowfiltering', 1, 'tool_dataprivacy');

        $this->setAdminUser();
        $user = $this->getDataGenerator()->create_user();

        $course = $this->getDataGenerator()->create_course(['startdate' => time() - YEARSECS, 'enddate' => time() - YEARSECS]);
        $coursecontext = \context_course::instance($course->id);

        $this->getDataGenerator()->enrol_user($user->id, $course->id, 'student');

        // Create the initial contextlist.
        $initialcollection = new \core_privacy\local\request\contextlist_collection($user->id);

        $contextlist = new \core_privacy\local\request\contextlist();
        $contextlist->add_from_sql('SELECT id FROM {context} WHERE id = :contextid', ['contextid' => $coursecontext->id]);
        $contextlist->set_component('tool_dataprivacy');
        $initialcollection->add_contextlist($contextlist);

        $datarequest = api::create_data_request($user->id, api::DATAREQUEST_TYPE_EXPORT);
        $requestid = $datarequest->get('id');

        ob_start();
        api::add_request_contexts_with_status($initialcollection, $requestid, contextlist_context::STATUS_PENDING);
        ob_end_clean();

        $result = $DB->get_record('tool_dataprivacy_ctxlst_ctx', ['contextid' => $coursecontext->id]);
        $this->assertEquals($result->status, contextlist_context::STATUS_PENDING);

        $result1 = $DB->get_field('tool_dataprivacy_rqst_ctxlst', 'requestid', ['contextlistid' => $result->contextlistid]);
        $this->assertEquals($result1, $requestid);
    }

    /**
     * Test that deletion requests for the primary admin are rejected
     */
    public function test_reject_data_deletion_request_primary_admin() {
        $this->resetAfterTest();
        $this->setAdminUser();

        $datarequest = api::create_data_request(get_admin()->id, api::DATAREQUEST_TYPE_DELETE);

        // Approve the request and execute the ad-hoc process task.
        ob_start();
        api::approve_data_request($datarequest->get('id'));
        $this->runAdhocTasks('\tool_dataprivacy\task\process_data_request_task');
        ob_end_clean();

        $request = api::get_request($datarequest->get('id'));
        $this->assertEquals(api::DATAREQUEST_STATUS_REJECTED, $request->get('status'));

        // Confirm they weren't deleted.
        $user = \core_user::get_user($request->get('userid'));
        \core_user::require_active_user($user);
    }

    /**
     * Test for api::can_contact_dpo()
     */
    public function test_can_contact_dpo() {
        $this->resetAfterTest();

        // Default ('contactdataprotectionofficer' is disabled by default).
        $this->assertFalse(api::can_contact_dpo());

        // Enable.
        set_config('contactdataprotectionofficer', 1, 'tool_dataprivacy');
        $this->assertTrue(api::can_contact_dpo());

        // Disable again.
        set_config('contactdataprotectionofficer', 0, 'tool_dataprivacy');
        $this->assertFalse(api::can_contact_dpo());
    }

    /**
     * Test for api::can_manage_data_requests()
     */
    public function test_can_manage_data_requests() {
        global $DB;

        $this->resetAfterTest();

        // No configured site DPOs yet.
        $admin = get_admin();
        $this->assertTrue(api::can_manage_data_requests($admin->id));

        $generator = new testing_data_generator();
        $dpo = $generator->create_user();
        $nondpocapable = $generator->create_user();
        $nondpoincapable = $generator->create_user();

        $context = \context_system::instance();

        // Manager role.
        $managerroleid = $DB->get_field('role', 'id', array('shortname' => 'manager'));
        // Give the manager role with the capability to manage data requests.
        assign_capability('tool/dataprivacy:managedatarequests', CAP_ALLOW, $managerroleid, $context->id, true);
        // Assign u1 as a manager.
        role_assign($managerroleid, $dpo->id, $context->id);

        // Editing teacher role.
        $editingteacherroleid = $DB->get_field('role', 'id', array('shortname' => 'editingteacher'));
        // Give the editing teacher role with the capability to manage data requests.
        assign_capability('tool/dataprivacy:managedatarequests', CAP_ALLOW, $managerroleid, $context->id, true);
        // Assign u2 as an editing teacher.
        role_assign($editingteacherroleid, $nondpocapable->id, $context->id);

        // Map only the manager role to the DPO role.
        set_config('dporoles', $managerroleid, 'tool_dataprivacy');

        // User with capability and has DPO role.
        $this->assertTrue(api::can_manage_data_requests($dpo->id));
        // User with capability but has no DPO role.
        $this->assertFalse(api::can_manage_data_requests($nondpocapable->id));
        // User without the capability and has no DPO role.
        $this->assertFalse(api::can_manage_data_requests($nondpoincapable->id));
    }

    /**
     * Test that a user who has no capability to make any data requests for children cannot create data requests for any
     * other user.
     */
    public function test_can_create_data_request_for_user_no() {
        $this->resetAfterTest();

        $parent = $this->getDataGenerator()->create_user();
        $otheruser = $this->getDataGenerator()->create_user();

        $this->setUser($parent);
        $this->assertFalse(api::can_create_data_request_for_user($otheruser->id));
    }

    /**
     * Test that a user who has the capability to make any data requests for one other user cannot create data requests
     * for any other user.
     */
    public function test_can_create_data_request_for_user_some() {
        $this->resetAfterTest();

        $parent = $this->getDataGenerator()->create_user();
        $child = $this->getDataGenerator()->create_user();
        $otheruser = $this->getDataGenerator()->create_user();

        $systemcontext = \context_system::instance();
        $parentrole = $this->getDataGenerator()->create_role();
        assign_capability('tool/dataprivacy:makedatarequestsforchildren', CAP_ALLOW, $parentrole, $systemcontext);
        role_assign($parentrole, $parent->id, \context_user::instance($child->id));

        $this->setUser($parent);
        $this->assertFalse(api::can_create_data_request_for_user($otheruser->id));
    }

    /**
     * Test that a user who has the capability to make any data requests for one other user cannot create data requests
     * for any other user.
     */
    public function test_can_create_data_request_for_user_own_child() {
        $this->resetAfterTest();

        $parent = $this->getDataGenerator()->create_user();
        $child = $this->getDataGenerator()->create_user();

        $systemcontext = \context_system::instance();
        $parentrole = $this->getDataGenerator()->create_role();
        assign_capability('tool/dataprivacy:makedatarequestsforchildren', CAP_ALLOW, $parentrole, $systemcontext);
        role_assign($parentrole, $parent->id, \context_user::instance($child->id));

        $this->setUser($parent);
        $this->assertTrue(api::can_create_data_request_for_user($child->id));
    }

    /**
     * Test that a user who has no capability to make any data requests for children cannot create data requests for any
     * other user.
     */
    public function test_require_can_create_data_request_for_user_no() {
        $this->resetAfterTest();

        $parent = $this->getDataGenerator()->create_user();
        $otheruser = $this->getDataGenerator()->create_user();

        $this->setUser($parent);
        $this->expectException('required_capability_exception');
        api::require_can_create_data_request_for_user($otheruser->id);
    }

    /**
     * Test that a user who has the capability to make any data requests for one other user cannot create data requests
     * for any other user.
     */
    public function test_require_can_create_data_request_for_user_some() {
        $this->resetAfterTest();

        $parent = $this->getDataGenerator()->create_user();
        $child = $this->getDataGenerator()->create_user();
        $otheruser = $this->getDataGenerator()->create_user();

        $systemcontext = \context_system::instance();
        $parentrole = $this->getDataGenerator()->create_role();
        assign_capability('tool/dataprivacy:makedatarequestsforchildren', CAP_ALLOW, $parentrole, $systemcontext);
        role_assign($parentrole, $parent->id, \context_user::instance($child->id));

        $this->setUser($parent);
        $this->expectException('required_capability_exception');
        api::require_can_create_data_request_for_user($otheruser->id);
    }

    /**
     * Test that a user who has the capability to make any data requests for one other user cannot create data requests
     * for any other user.
     */
    public function test_require_can_create_data_request_for_user_own_child() {
        $this->resetAfterTest();

        $parent = $this->getDataGenerator()->create_user();
        $child = $this->getDataGenerator()->create_user();

        $systemcontext = \context_system::instance();
        $parentrole = $this->getDataGenerator()->create_role();
        assign_capability('tool/dataprivacy:makedatarequestsforchildren', CAP_ALLOW, $parentrole, $systemcontext);
        role_assign($parentrole, $parent->id, \context_user::instance($child->id));

        $this->setUser($parent);
        $this->assertTrue(api::require_can_create_data_request_for_user($child->id));
    }

    /**
     * Test for api::can_download_data_request_for_user()
     */
    public function test_can_download_data_request_for_user() {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator();

        // Three victims.
        $victim1 = $generator->create_user();
        $victim2 = $generator->create_user();
        $victim3 = $generator->create_user();

        // Assign a user as victim 1's parent.
        $systemcontext = \context_system::instance();
        $parentrole = $generator->create_role();
        assign_capability('tool/dataprivacy:makedatarequestsforchildren', CAP_ALLOW, $parentrole, $systemcontext);
        $parent = $generator->create_user();
        role_assign($parentrole, $parent->id, \context_user::instance($victim1->id));

        // Assign another user as data access wonder woman.
        $wonderrole = $generator->create_role();
        assign_capability('tool/dataprivacy:downloadallrequests', CAP_ALLOW, $wonderrole, $systemcontext);
        $staff = $generator->create_user();
        role_assign($wonderrole, $staff->id, $systemcontext);

        // Finally, victim 3 has been naughty; stop them accessing their own data.
        $naughtyrole = $generator->create_role();
        assign_capability('tool/dataprivacy:downloadownrequest', CAP_PROHIBIT, $naughtyrole, $systemcontext);
        role_assign($naughtyrole, $victim3->id, $systemcontext);

        // Victims 1 and 2 can access their own data, regardless of who requested it.
        $this->assertTrue(api::can_download_data_request_for_user($victim1->id, $victim1->id, $victim1->id));
        $this->assertTrue(api::can_download_data_request_for_user($victim2->id, $staff->id, $victim2->id));

        // Victim 3 cannot access his own data.
        $this->assertFalse(api::can_download_data_request_for_user($victim3->id, $victim3->id, $victim3->id));

        // Victims 1 and 2 cannot access another victim's data.
        $this->assertFalse(api::can_download_data_request_for_user($victim2->id, $victim1->id, $victim1->id));
        $this->assertFalse(api::can_download_data_request_for_user($victim1->id, $staff->id, $victim2->id));

        // Staff can access everyone's data.
        $this->assertTrue(api::can_download_data_request_for_user($victim1->id, $victim1->id, $staff->id));
        $this->assertTrue(api::can_download_data_request_for_user($victim2->id, $staff->id, $staff->id));
        $this->assertTrue(api::can_download_data_request_for_user($victim3->id, $staff->id, $staff->id));

        // Parent can access victim 1's data only if they requested it.
        $this->assertTrue(api::can_download_data_request_for_user($victim1->id, $parent->id, $parent->id));
        $this->assertFalse(api::can_download_data_request_for_user($victim1->id, $staff->id, $parent->id));
        $this->assertFalse(api::can_download_data_request_for_user($victim2->id, $parent->id, $parent->id));
    }

    /**
     * Data provider for data request creation tests.
     *
     * @return array
     */
    public function data_request_creation_provider() {
        return [
            'Export request by user, automatic approval off' => [
                false, api::DATAREQUEST_TYPE_EXPORT, 'automaticdataexportapproval', false, 0,
                api::DATAREQUEST_STATUS_AWAITING_APPROVAL, 0, 0
            ],
            'Export request by user, automatic approval on' => [
                false, api::DATAREQUEST_TYPE_EXPORT, 'automaticdataexportapproval', true, 0,
                api::DATAREQUEST_STATUS_APPROVED, 1 , 0
            ],
            'Export request by PO, automatic approval off' => [
                true, api::DATAREQUEST_TYPE_EXPORT, 'automaticdataexportapproval', false, 0,
                api::DATAREQUEST_STATUS_AWAITING_APPROVAL, 0, 0
            ],
            'Export request by PO, automatic approval off, allow filtering of exports by course' => [
                    true, api::DATAREQUEST_TYPE_EXPORT, 'automaticdataexportapproval', false, 0,
                    api::DATAREQUEST_STATUS_PENDING, 0, 1
            ],
            'Export request by PO, automatic approval on' => [
                true, api::DATAREQUEST_TYPE_EXPORT, 'automaticdataexportapproval', true, 'dpo',
                api::DATAREQUEST_STATUS_APPROVED, 1, 0
            ],
            'Delete request by user, automatic approval off' => [
                false, api::DATAREQUEST_TYPE_DELETE, 'automaticdatadeletionapproval', false, 0,
                api::DATAREQUEST_STATUS_AWAITING_APPROVAL, 0, 0
            ],
            'Delete request by user, automatic approval on' => [
                false, api::DATAREQUEST_TYPE_DELETE, 'automaticdatadeletionapproval', true, 0,
                api::DATAREQUEST_STATUS_APPROVED, 1, 0
            ],
            'Delete request by PO, automatic approval off' => [
                true, api::DATAREQUEST_TYPE_DELETE, 'automaticdatadeletionapproval', false, 0,
                api::DATAREQUEST_STATUS_AWAITING_APPROVAL, 0, 0
            ],
            'Delete request by PO, automatic approval on' => [
                true, api::DATAREQUEST_TYPE_DELETE, 'automaticdatadeletionapproval', true, 'dpo',
                api::DATAREQUEST_STATUS_APPROVED, 1, 0
            ],
        ];
    }

    /**
     * Test for api::create_data_request()
     *
     * @dataProvider data_request_creation_provider
     * @param bool $asprivacyofficer Whether the request is made as the Privacy Officer or the user itself.
     * @param string $type The data request type.
     * @param string $setting The automatic approval setting.
     * @param bool $automaticapproval Whether automatic data request approval is turned on or not.
     * @param int|string $expecteddpoval The expected value for the 'dpo' field. 'dpo' means we'd the expected value would be the
     *                                   user ID of the privacy officer which happens in the case where a PO requests on behalf of
     *                                   someone else and automatic data request approval is turned on.
     * @param int $expectedstatus The expected status of the data request.
     * @param int $expectedtaskcount The number of expected queued data requests tasks.
     * @param bool $allowfiltering Whether allow filtering of exports by course turn on or off.
     * @throws coding_exception
     * @throws invalid_persistent_exception
     */
    public function test_create_data_request(
        $asprivacyofficer,
        $type,
        $setting,
        $automaticapproval,
        $expecteddpoval,
        $expectedstatus,
        $expectedtaskcount,
        $allowfiltering,
    ) {
        global $USER;

        $this->resetAfterTest();

        $generator = new testing_data_generator();
        $user = $generator->create_user();
        $comment = 'sample comment';

        // Login.
        if ($asprivacyofficer) {
            $this->setAdminUser();
        } else {
            $this->setUser($user->id);
        }

        // Set the automatic data request approval setting value.
        set_config($setting, $automaticapproval, 'tool_dataprivacy');

        // If set to 'dpo' use the currently logged-in user's ID (which should be the admin user's ID).
        if ($expecteddpoval === 'dpo') {
            $expecteddpoval = $USER->id;
        }
        if ($allowfiltering) {
            set_config('allowfiltering', 1, 'tool_dataprivacy');
        }

        // Test data request creation.
        $datarequest = api::create_data_request($user->id, $type, $comment);
        $this->assertEquals($user->id, $datarequest->get('userid'));
        $this->assertEquals($USER->id, $datarequest->get('requestedby'));
        $this->assertEquals($expecteddpoval, $datarequest->get('dpo'));
        $this->assertEquals($type, $datarequest->get('type'));
        $this->assertEquals($expectedstatus, $datarequest->get('status'));
        $this->assertEquals($comment, $datarequest->get('comments'));
        $this->assertEquals($automaticapproval, $datarequest->get('systemapproved'));

        // Test number of queued data request tasks.
        $datarequesttasks = manager::get_adhoc_tasks(process_data_request_task::class);
        $this->assertCount($expectedtaskcount, $datarequesttasks);

        if ($allowfiltering) {
            // Test number of queued initiate data request tasks.
            $datarequesttasks = manager::get_adhoc_tasks(initiate_data_request_task::class);
            $this->assertCount(1, $datarequesttasks);
        }
    }

    /**
     * Test for api::create_data_request() made by a parent.
     */
    public function test_create_data_request_by_parent() {
        global $DB;

        $this->resetAfterTest();

        $generator = new testing_data_generator();
        $user = $generator->create_user();
        $parent = $generator->create_user();
        $comment = 'sample comment';

        // Get the teacher role pretend it's the parent roles ;).
        $systemcontext = \context_system::instance();
        $usercontext = \context_user::instance($user->id);
        $parentroleid = $DB->get_field('role', 'id', array('shortname' => 'teacher'));
        // Give the manager role with the capability to manage data requests.
        assign_capability('tool/dataprivacy:makedatarequestsforchildren', CAP_ALLOW, $parentroleid, $systemcontext->id, true);
        // Assign the parent to user.
        role_assign($parentroleid, $parent->id, $usercontext->id);

        // Login as the user's parent.
        $this->setUser($parent);

        // Test data request creation.
        $datarequest = api::create_data_request($user->id, api::DATAREQUEST_TYPE_EXPORT, $comment);
        $this->assertEquals($user->id, $datarequest->get('userid'));
        $this->assertEquals($parent->id, $datarequest->get('requestedby'));
        $this->assertEquals(0, $datarequest->get('dpo'));
        $this->assertEquals(api::DATAREQUEST_TYPE_EXPORT, $datarequest->get('type'));
        $this->assertEquals(api::DATAREQUEST_STATUS_AWAITING_APPROVAL, $datarequest->get('status'));
        $this->assertEquals($comment, $datarequest->get('comments'));
    }

    /**
     * Test for api::deny_data_request()
     */
    public function test_deny_data_request() {
        $this->resetAfterTest();

        $generator = new testing_data_generator();
        $user = $generator->create_user();
        $comment = 'sample comment';

        // Login as user.
        $this->setUser($user->id);

        // Test data request creation.
        $datarequest = api::create_data_request($user->id, api::DATAREQUEST_TYPE_EXPORT, $comment);

        // Login as the admin (default DPO when no one is set).
        $this->setAdminUser();

        // Make this ready for approval.
        api::update_request_status($datarequest->get('id'), api::DATAREQUEST_STATUS_AWAITING_APPROVAL);

        // Deny the data request.
        $result = api::deny_data_request($datarequest->get('id'));
        $this->assertTrue($result);
    }

    /**
     * Data provider for \tool_dataprivacy_api_testcase::test_get_data_requests().
     *
     * @return array
     */
    public function get_data_requests_provider() {
        $completeonly = [api::DATAREQUEST_STATUS_COMPLETE, api::DATAREQUEST_STATUS_DOWNLOAD_READY, api::DATAREQUEST_STATUS_DELETED];
        $completeandcancelled = array_merge($completeonly, [api::DATAREQUEST_STATUS_CANCELLED]);

        return [
            // Own data requests.
            ['user', false, $completeonly],
            // Non-DPO fetching all requets.
            ['user', true, $completeonly],
            // Admin fetching all completed and cancelled requests.
            ['dpo', true, $completeandcancelled],
            // Admin fetching all completed requests.
            ['dpo', true, $completeonly],
            // Guest fetching all requests.
            ['guest', true, $completeonly],
        ];
    }

    /**
     * Test for api::get_data_requests()
     *
     * @dataProvider get_data_requests_provider
     * @param string $usertype The type of the user logging in.
     * @param boolean $fetchall Whether to fetch all records.
     * @param int[] $statuses Status filters.
     */
    public function test_get_data_requests($usertype, $fetchall, $statuses) {
        $this->resetAfterTest();

        $generator = new testing_data_generator();
        $user1 = $generator->create_user();
        $user2 = $generator->create_user();
        $user3 = $generator->create_user();
        $user4 = $generator->create_user();
        $user5 = $generator->create_user();
        $users = [$user1, $user2, $user3, $user4, $user5];

        switch ($usertype) {
            case 'user':
                $loggeduser = $user1;
                break;
            case 'dpo':
                $loggeduser = get_admin();
                break;
            case 'guest':
                $loggeduser = guest_user();
                break;
        }

        $comment = 'Data %s request comment by user %d';
        $exportstring = helper::get_shortened_request_type_string(api::DATAREQUEST_TYPE_EXPORT);
        $deletionstring = helper::get_shortened_request_type_string(api::DATAREQUEST_TYPE_DELETE);
        // Make a data requests for the users.
        foreach ($users as $user) {
            $this->setUser($user);
            api::create_data_request($user->id, api::DATAREQUEST_TYPE_EXPORT, sprintf($comment, $exportstring, $user->id));
            api::create_data_request($user->id, api::DATAREQUEST_TYPE_EXPORT, sprintf($comment, $deletionstring, $user->id));
        }

        // Log in as the target user.
        $this->setUser($loggeduser);
        // Get records count based on the filters.
        $userid = $loggeduser->id;
        if ($fetchall) {
            $userid = 0;
        }
        $count = api::get_data_requests_count($userid);
        if (api::is_site_dpo($loggeduser->id)) {
            // DPOs should see all the requests.
            $this->assertEquals(count($users) * 2, $count);
        } else {
            if (empty($userid)) {
                // There should be no data requests for this user available.
                $this->assertEquals(0, $count);
            } else {
                // There should be only one (request with pending status).
                $this->assertEquals(2, $count);
            }
        }
        // Get data requests.
        $requests = api::get_data_requests($userid);
        // The number of requests should match the count.
        $this->assertCount($count, $requests);

        // Test filtering by status.
        if ($count && !empty($statuses)) {
            $filteredcount = api::get_data_requests_count($userid, $statuses);
            // There should be none as they are all pending.
            $this->assertEquals(0, $filteredcount);
            $filteredrequests = api::get_data_requests($userid, $statuses);
            $this->assertCount($filteredcount, $filteredrequests);

            $statuscounts = [];
            foreach ($statuses as $stat) {
                $statuscounts[$stat] = 0;
            }
            $numstatus = count($statuses);
            // Get all requests with status filter and update statuses, randomly.
            foreach ($requests as $request) {
                if (rand(0, 1)) {
                    continue;
                }

                if ($numstatus > 1) {
                    $index = rand(0, $numstatus - 1);
                    $status = $statuses[$index];
                } else {
                    $status = reset($statuses);
                }
                $statuscounts[$status]++;
                api::update_request_status($request->get('id'), $status);
            }
            $total = array_sum($statuscounts);
            $filteredcount = api::get_data_requests_count($userid, $statuses);
            $this->assertEquals($total, $filteredcount);
            $filteredrequests = api::get_data_requests($userid, $statuses);
            $this->assertCount($filteredcount, $filteredrequests);
            // Confirm the filtered requests match the status filter(s).
            foreach ($filteredrequests as $request) {
                $this->assertContainsEquals($request->get('status'), $statuses);
            }

            if ($numstatus > 1) {
                // Fetch by individual status to check the numbers match.
                foreach ($statuses as $status) {
                    $filteredcount = api::get_data_requests_count($userid, [$status]);
                    $this->assertEquals($statuscounts[$status], $filteredcount);
                    $filteredrequests = api::get_data_requests($userid, [$status]);
                    $this->assertCount($filteredcount, $filteredrequests);
                }
            }
        }
    }

    /**
     * Test for api::get_approved_contextlist_collection_for_request.
     */
    public function test_get_approved_contextlist_collection_for_request() {
        $this->resetAfterTest();
        set_config('allowfiltering', 1, 'tool_dataprivacy');
        $this->setAdminUser();

        $user = $this->getDataGenerator()->create_user();

        $course = $this->getDataGenerator()->create_course([]);

        $forum = $this->getDataGenerator()->create_module('forum', ['course' => $course->id]);

        $generator = $this->getDataGenerator()->get_plugin_generator('mod_forum');

        $record = new \stdClass();
        $record->course = $course->id;
        $record->userid = $user->id;
        $record->forum = $forum->id;
        $generator->create_discussion($record);

        $generator->create_discussion($record);

        $coursecontext1 = \context_course::instance($course->id);

        $forumcontext1 = \context_module::instance($forum->cmid);

        $this->getDataGenerator()->enrol_user($user->id, $course->id, 'student');

        $datarequest = api::create_data_request($user->id, api::DATAREQUEST_TYPE_EXPORT);

        ob_start();
        $this->runAdhocTasks('tool_dataprivacy\task\initiate_data_request_task');
        ob_end_clean();

        api::approve_contexts_belonging_to_request($datarequest->get('id'), [$coursecontext1->id]);
        $contextlistcollection = api::get_approved_contextlist_collection_for_request($datarequest);
        $approvecontexts = [];
        foreach ($contextlistcollection->get_contextlists() as $contextlist) {
            foreach ($contextlist->get_contextids() as $contextid) {
                $approvecontexts[] = $contextid;
            }
        }
        $this->assertContains(strval($coursecontext1->id), $approvecontexts);
        $this->assertContains(strval($forumcontext1->id), $approvecontexts);
    }

    /**
     * Data provider for test_has_ongoing_request.
     */
    public function status_provider() {
        return [
            [api::DATAREQUEST_STATUS_AWAITING_APPROVAL, true],
            [api::DATAREQUEST_STATUS_APPROVED, true],
            [api::DATAREQUEST_STATUS_PROCESSING, true],
            [api::DATAREQUEST_STATUS_COMPLETE, false],
            [api::DATAREQUEST_STATUS_CANCELLED, false],
            [api::DATAREQUEST_STATUS_REJECTED, false],
            [api::DATAREQUEST_STATUS_DOWNLOAD_READY, false],
            [api::DATAREQUEST_STATUS_EXPIRED, false],
            [api::DATAREQUEST_STATUS_DELETED, false],
        ];
    }

    /**
     * Test for api::has_ongoing_request()
     *
     * @dataProvider status_provider
     * @param int $status The request status.
     * @param bool $expected The expected result.
     */
    public function test_has_ongoing_request($status, $expected) {
        $this->resetAfterTest();

        $generator = new testing_data_generator();
        $user1 = $generator->create_user();

        // Make a data request as user 1.
        $this->setUser($user1);
        $request = api::create_data_request($user1->id, api::DATAREQUEST_TYPE_EXPORT);
        // Set the status.
        api::update_request_status($request->get('id'), $status);

        // Check if this request is ongoing.
        $result = api::has_ongoing_request($user1->id, api::DATAREQUEST_TYPE_EXPORT);
        $this->assertEquals($expected, $result);
    }

    /**
     * Test for api::is_active()
     *
     * @dataProvider status_provider
     * @param int $status The request status
     * @param bool $expected The expected result
     */
    public function test_is_active($status, $expected) {
        // Check if this request is ongoing.
        $result = api::is_active($status);
        $this->assertEquals($expected, $result);
    }

    /**
     * Test for api::is_site_dpo()
     */
    public function test_is_site_dpo() {
        global $DB;

        $this->resetAfterTest();

        // No configured site DPOs yet.
        $admin = get_admin();
        $this->assertTrue(api::is_site_dpo($admin->id));

        $generator = new testing_data_generator();
        $dpo = $generator->create_user();
        $nondpo = $generator->create_user();

        $context = \context_system::instance();

        // Manager role.
        $managerroleid = $DB->get_field('role', 'id', array('shortname' => 'manager'));
        // Give the manager role with the capability to manage data requests.
        assign_capability('tool/dataprivacy:managedatarequests', CAP_ALLOW, $managerroleid, $context->id, true);
        // Assign u1 as a manager.
        role_assign($managerroleid, $dpo->id, $context->id);

        // Map only the manager role to the DPO role.
        set_config('dporoles', $managerroleid, 'tool_dataprivacy');

        // User is a DPO.
        $this->assertTrue(api::is_site_dpo($dpo->id));
        // User is not a DPO.
        $this->assertFalse(api::is_site_dpo($nondpo->id));
    }

    /**
     * Data provider function for test_notify_dpo
     *
     * @return array
     */
    public function notify_dpo_provider() {
        return [
            [false, api::DATAREQUEST_TYPE_EXPORT, 'requesttypeexport', 'Export my user data'],
            [false, api::DATAREQUEST_TYPE_DELETE, 'requesttypedelete', 'Delete my user data'],
            [false, api::DATAREQUEST_TYPE_OTHERS, 'requesttypeothers', 'Nothing. Just wanna say hi'],
            [true, api::DATAREQUEST_TYPE_EXPORT, 'requesttypeexport', 'Admin export data of another user'],
        ];
    }

    /**
     * Test for api::notify_dpo()
     *
     * @dataProvider notify_dpo_provider
     * @param bool $byadmin Whether the admin requests data on behalf of the user
     * @param int $type The request type
     * @param string $typestringid The request lang string identifier
     * @param string $comments The requestor's message to the DPO.
     */
    public function test_notify_dpo($byadmin, $type, $typestringid, $comments) {
        $this->resetAfterTest();

        $generator = new testing_data_generator();
        $user1 = $generator->create_user();
        // Let's just use admin as DPO (It's the default if not set).
        $dpo = get_admin();
        if ($byadmin) {
            $this->setAdminUser();
            $requestedby = $dpo;
        } else {
            $this->setUser($user1);
            $requestedby = $user1;
        }

        // Make a data request for user 1.
        $request = api::create_data_request($user1->id, $type, $comments);

        $sink = $this->redirectMessages();
        $messageid = api::notify_dpo($dpo, $request);
        $this->assertNotFalse($messageid);
        $messages = $sink->get_messages();
        $this->assertCount(1, $messages);
        $message = reset($messages);

        // Check some of the message properties.
        $this->assertEquals($requestedby->id, $message->useridfrom);
        $this->assertEquals($dpo->id, $message->useridto);
        $typestring = get_string($typestringid, 'tool_dataprivacy');
        $subject = get_string('datarequestemailsubject', 'tool_dataprivacy', $typestring);
        $this->assertEquals($subject, $message->subject);
        $this->assertEquals('tool_dataprivacy', $message->component);
        $this->assertEquals('contactdataprotectionofficer', $message->eventtype);
        $this->assertStringContainsString(fullname($dpo), $message->fullmessage);
        $this->assertStringContainsString(fullname($user1), $message->fullmessage);
    }

    /**
     * Test data purposes CRUD actions.
     *
     * @return null
     */
    public function test_purpose_crud() {
        $this->resetAfterTest();

        $this->setAdminUser();

        // Add.
        $purpose = api::create_purpose((object)[
            'name' => 'bbb',
            'description' => '<b>yeah</b>',
            'descriptionformat' => 1,
            'retentionperiod' => 'PT1M',
            'lawfulbases' => 'gdpr_art_6_1_a,gdpr_art_6_1_c,gdpr_art_6_1_e'
        ]);
        $this->assertInstanceOf('\tool_dataprivacy\purpose', $purpose);
        $this->assertEquals('bbb', $purpose->get('name'));
        $this->assertEquals('PT1M', $purpose->get('retentionperiod'));
        $this->assertEquals('gdpr_art_6_1_a,gdpr_art_6_1_c,gdpr_art_6_1_e', $purpose->get('lawfulbases'));

        // Update.
        $purpose->set('retentionperiod', 'PT2M');
        $purpose = api::update_purpose($purpose->to_record());
        $this->assertEquals('PT2M', $purpose->get('retentionperiod'));

        // Retrieve.
        $purpose = api::create_purpose((object)['name' => 'aaa', 'retentionperiod' => 'PT1M', 'lawfulbases' => 'gdpr_art_6_1_a']);
        $purposes = api::get_purposes();
        $this->assertCount(2, $purposes);
        $this->assertEquals('aaa', $purposes[0]->get('name'));
        $this->assertEquals('bbb', $purposes[1]->get('name'));

        // Delete.
        api::delete_purpose($purposes[0]->get('id'));
        $this->assertCount(1, api::get_purposes());
        api::delete_purpose($purposes[1]->get('id'));
        $this->assertCount(0, api::get_purposes());
    }

    /**
     * Test data categories CRUD actions.
     *
     * @return null
     */
    public function test_category_crud() {
        $this->resetAfterTest();

        $this->setAdminUser();

        // Add.
        $category = api::create_category((object)[
            'name' => 'bbb',
            'description' => '<b>yeah</b>',
            'descriptionformat' => 1
        ]);
        $this->assertInstanceOf('\tool_dataprivacy\category', $category);
        $this->assertEquals('bbb', $category->get('name'));

        // Update.
        $category->set('name', 'bcd');
        $category = api::update_category($category->to_record());
        $this->assertEquals('bcd', $category->get('name'));

        // Retrieve.
        $category = api::create_category((object)['name' => 'aaa']);
        $categories = api::get_categories();
        $this->assertCount(2, $categories);
        $this->assertEquals('aaa', $categories[0]->get('name'));
        $this->assertEquals('bcd', $categories[1]->get('name'));

        // Delete.
        api::delete_category($categories[0]->get('id'));
        $this->assertCount(1, api::get_categories());
        api::delete_category($categories[1]->get('id'));
        $this->assertCount(0, api::get_categories());
    }

    /**
     * Test context instances.
     *
     * @return null
     */
    public function test_context_instances() {
        global $DB;

        $this->resetAfterTest();

        $this->setAdminUser();

        list($purposes, $categories, $courses, $modules) = $this->add_purposes_and_categories();

        $coursecontext1 = \context_course::instance($courses[0]->id);
        $coursecontext2 = \context_course::instance($courses[1]->id);

        $record1 = (object)['contextid' => $coursecontext1->id, 'purposeid' => $purposes[0]->get('id'),
            'categoryid' => $categories[0]->get('id')];
        $contextinstance1 = api::set_context_instance($record1);

        $record2 = (object)['contextid' => $coursecontext2->id, 'purposeid' => $purposes[1]->get('id'),
            'categoryid' => $categories[1]->get('id')];
        $contextinstance2 = api::set_context_instance($record2);

        $this->assertCount(2, $DB->get_records('tool_dataprivacy_ctxinstance'));

        api::unset_context_instance($contextinstance1);
        $this->assertCount(1, $DB->get_records('tool_dataprivacy_ctxinstance'));

        $update = (object)['id' => $contextinstance2->get('id'), 'contextid' => $coursecontext2->id,
            'purposeid' => $purposes[0]->get('id'), 'categoryid' => $categories[0]->get('id')];
        $contextinstance2 = api::set_context_instance($update);
        $this->assertCount(1, $DB->get_records('tool_dataprivacy_ctxinstance'));
    }

    /**
     * Test contextlevel.
     *
     * @return null
     */
    public function test_contextlevel() {
        global $DB;

        $this->resetAfterTest();

        $this->setAdminUser();
        list($purposes, $categories, $courses, $modules) = $this->add_purposes_and_categories();

        $record = (object)[
            'purposeid' => $purposes[0]->get('id'),
            'categoryid' => $categories[0]->get('id'),
            'contextlevel' => CONTEXT_SYSTEM,
        ];
        $contextlevel = api::set_contextlevel($record);
        $this->assertInstanceOf('\tool_dataprivacy\contextlevel', $contextlevel);
        $this->assertEquals($record->contextlevel, $contextlevel->get('contextlevel'));
        $this->assertEquals($record->purposeid, $contextlevel->get('purposeid'));
        $this->assertEquals($record->categoryid, $contextlevel->get('categoryid'));

        // Now update it.
        $record->purposeid = $purposes[1]->get('id');
        $contextlevel = api::set_contextlevel($record);
        $this->assertEquals($record->contextlevel, $contextlevel->get('contextlevel'));
        $this->assertEquals($record->purposeid, $contextlevel->get('purposeid'));
        $this->assertEquals(1, $DB->count_records('tool_dataprivacy_ctxlevel'));

        $record->contextlevel = CONTEXT_USER;
        $contextlevel = api::set_contextlevel($record);
        $this->assertEquals(2, $DB->count_records('tool_dataprivacy_ctxlevel'));
    }

    /**
     * Test effective context levels purpose and category defaults.
     *
     * @return null
     */
    public function test_effective_contextlevel_defaults() {
        $this->setAdminUser();

        $this->resetAfterTest();

        list($purposes, $categories, $courses, $modules) = $this->add_purposes_and_categories();

        list($purposeid, $categoryid) = data_registry::get_effective_default_contextlevel_purpose_and_category(CONTEXT_SYSTEM);
        $this->assertEquals(false, $purposeid);
        $this->assertEquals(false, $categoryid);

        list($purposevar, $categoryvar) = data_registry::var_names_from_context(
            \context_helper::get_class_for_level(CONTEXT_SYSTEM)
        );
        set_config($purposevar, $purposes[0]->get('id'), 'tool_dataprivacy');

        list($purposeid, $categoryid) = data_registry::get_effective_default_contextlevel_purpose_and_category(CONTEXT_SYSTEM);
        $this->assertEquals($purposes[0]->get('id'), $purposeid);
        $this->assertEquals(false, $categoryid);

        // Course defined values should have preference.
        list($purposevar, $categoryvar) = data_registry::var_names_from_context(
            \context_helper::get_class_for_level(CONTEXT_COURSE)
        );
        set_config($purposevar, $purposes[1]->get('id'), 'tool_dataprivacy');
        set_config($categoryvar, $categories[0]->get('id'), 'tool_dataprivacy');

        list($purposeid, $categoryid) = data_registry::get_effective_default_contextlevel_purpose_and_category(CONTEXT_COURSE);
        $this->assertEquals($purposes[1]->get('id'), $purposeid);
        $this->assertEquals($categories[0]->get('id'), $categoryid);

        // Context level defaults are also allowed to be set to 'inherit'.
        set_config($purposevar, context_instance::INHERIT, 'tool_dataprivacy');
    }

    /**
     * Ensure that when nothing is configured, all values return false.
     */
    public function test_get_effective_contextlevel_unset() {
        // Before setup, get_effective_contextlevel_purpose will return false.
        $this->assertFalse(api::get_effective_contextlevel_category(CONTEXT_SYSTEM));
        $this->assertFalse(api::get_effective_contextlevel_purpose(CONTEXT_SYSTEM));

        $this->assertFalse(api::get_effective_contextlevel_category(CONTEXT_USER));
        $this->assertFalse(api::get_effective_contextlevel_purpose(CONTEXT_USER));
    }

    /**
     * Ensure that when nothing is configured, all values return false.
     */
    public function test_get_effective_context_unset() {
        // Before setup, get_effective_contextlevel_purpose will return false.
        $this->assertFalse(api::get_effective_context_category(\context_system::instance()));
        $this->assertFalse(api::get_effective_context_purpose(\context_system::instance()));
    }

    /**
     * Ensure that fetching the effective value for context levels is only available to system, and user context levels.
     *
     * @dataProvider invalid_effective_contextlevel_provider
     * @param   int $contextlevel
     */
    public function test_set_contextlevel_invalid_contextlevels($contextlevel) {

        $this->expectException(\coding_exception::class);
        api::set_contextlevel((object) [
                'contextlevel' => $contextlevel,
            ]);

    }

    /**
     * Test effective contextlevel return.
     */
    public function test_effective_contextlevel() {
        $this->resetAfterTest();

        // Set the initial purpose and category.
        $purpose1 = api::create_purpose((object)['name' => 'p1', 'retentionperiod' => 'PT1H', 'lawfulbases' => 'gdpr_art_6_1_a']);
        $category1 = api::create_category((object)['name' => 'a']);
        api::set_contextlevel((object)[
            'contextlevel' => CONTEXT_SYSTEM,
            'purposeid' => $purpose1->get('id'),
            'categoryid' => $category1->get('id'),
        ]);

        $this->assertEquals($purpose1, api::get_effective_contextlevel_purpose(CONTEXT_SYSTEM));
        $this->assertEquals($category1, api::get_effective_contextlevel_category(CONTEXT_SYSTEM));

        // The user context inherits from the system context when not set.
        $this->assertEquals($purpose1, api::get_effective_contextlevel_purpose(CONTEXT_USER));
        $this->assertEquals($category1, api::get_effective_contextlevel_category(CONTEXT_USER));

        // Forcing the behaviour to inherit will have the same result.
        api::set_contextlevel((object) [
                'contextlevel' => CONTEXT_USER,
                'purposeid' => context_instance::INHERIT,
                'categoryid' => context_instance::INHERIT,
            ]);
        $this->assertEquals($purpose1, api::get_effective_contextlevel_purpose(CONTEXT_USER));
        $this->assertEquals($category1, api::get_effective_contextlevel_category(CONTEXT_USER));

        // Setting specific values will override the inheritance behaviour.
        $purpose2 = api::create_purpose((object)['name' => 'p2', 'retentionperiod' => 'PT2H', 'lawfulbases' => 'gdpr_art_6_1_a']);
        $category2 = api::create_category((object)['name' => 'b']);
        // Set the system context level to purpose 1.
        api::set_contextlevel((object) [
                'contextlevel' => CONTEXT_USER,
                'purposeid' => $purpose2->get('id'),
                'categoryid' => $category2->get('id'),
            ]);

        $this->assertEquals($purpose2, api::get_effective_contextlevel_purpose(CONTEXT_USER));
        $this->assertEquals($category2, api::get_effective_contextlevel_category(CONTEXT_USER));
    }

    /**
     * Ensure that fetching the effective value for context levels is only available to system, and user context levels.
     *
     * @dataProvider invalid_effective_contextlevel_provider
     * @param   int $contextlevel
     */
    public function test_effective_contextlevel_invalid_contextlevels($contextlevel) {
        $this->resetAfterTest();

        $purpose1 = api::create_purpose((object)['name' => 'p1', 'retentionperiod' => 'PT1H', 'lawfulbases' => 'gdpr_art_6_1_a']);
        $category1 = api::create_category((object)['name' => 'a']);
        api::set_contextlevel((object)[
            'contextlevel' => CONTEXT_SYSTEM,
            'purposeid' => $purpose1->get('id'),
            'categoryid' => $category1->get('id'),
        ]);

        $this->expectException(\coding_exception::class);
        api::get_effective_contextlevel_purpose($contextlevel);
    }

    /**
     * Data provider for invalid contextlevel fetchers.
     */
    public function invalid_effective_contextlevel_provider() {
        return [
            [CONTEXT_COURSECAT],
            [CONTEXT_COURSE],
            [CONTEXT_MODULE],
            [CONTEXT_BLOCK],
        ];
    }

    /**
     * Ensure that context inheritance works up the context tree.
     */
    public function test_effective_context_inheritance() {
        $this->resetAfterTest();

        $systemdata = $this->create_and_set_purpose_for_contextlevel('PT1S', CONTEXT_SYSTEM);

        /*
         * System
         * - Cat
         *   - Subcat
         *     - Course
         *       - Forum
         * - User
         *   - User block
         */
        $cat = $this->getDataGenerator()->create_category();
        $subcat = $this->getDataGenerator()->create_category(['parent' => $cat->id]);
        $course = $this->getDataGenerator()->create_course(['category' => $subcat->id]);
        $forum = $this->getDataGenerator()->create_module('forum', ['course' => $course->id]);
        list(, $forumcm) = get_course_and_cm_from_instance($forum->id, 'forum');

        $user = $this->getDataGenerator()->create_user();

        $contextsystem = \context_system::instance();
        $contextcat = \context_coursecat::instance($cat->id);
        $contextsubcat = \context_coursecat::instance($subcat->id);
        $contextcourse = \context_course::instance($course->id);
        $contextforum = \context_module::instance($forumcm->id);
        $contextuser = \context_user::instance($user->id);

        // Initially everything is set to Inherit.
        $this->assertEquals($systemdata->purpose, api::get_effective_context_purpose($contextsystem));
        $this->assertEquals($systemdata->purpose, api::get_effective_context_purpose($contextcat));
        $this->assertEquals($systemdata->purpose, api::get_effective_context_purpose($contextcat, "-1"));
        $this->assertEquals($systemdata->purpose, api::get_effective_context_purpose($contextcat, "0"));
        $this->assertEquals($systemdata->purpose, api::get_effective_context_purpose($contextsubcat));
        $this->assertEquals($systemdata->purpose, api::get_effective_context_purpose($contextsubcat, "-1"));
        $this->assertEquals($systemdata->purpose, api::get_effective_context_purpose($contextsubcat, "0"));
        $this->assertEquals($systemdata->purpose, api::get_effective_context_purpose($contextcourse));
        $this->assertEquals($systemdata->purpose, api::get_effective_context_purpose($contextcourse, "-1"));
        $this->assertEquals($systemdata->purpose, api::get_effective_context_purpose($contextcourse, "0"));
        $this->assertEquals($systemdata->purpose, api::get_effective_context_purpose($contextforum));
        $this->assertEquals($systemdata->purpose, api::get_effective_context_purpose($contextforum, "-1"));
        $this->assertEquals($systemdata->purpose, api::get_effective_context_purpose($contextforum, "0"));
        $this->assertEquals($systemdata->purpose, api::get_effective_context_purpose($contextuser));
        $this->assertEquals($systemdata->purpose, api::get_effective_context_purpose($contextuser, "-1"));
        $this->assertEquals($systemdata->purpose, api::get_effective_context_purpose($contextuser, "0"));

        $this->assertEquals($systemdata->category, api::get_effective_context_category($contextsystem));
        $this->assertEquals($systemdata->category, api::get_effective_context_category($contextcat));
        $this->assertEquals($systemdata->category, api::get_effective_context_category($contextcat, "-1"));
        $this->assertEquals($systemdata->category, api::get_effective_context_category($contextcat, "0"));
        $this->assertEquals($systemdata->category, api::get_effective_context_category($contextsubcat));
        $this->assertEquals($systemdata->category, api::get_effective_context_category($contextsubcat, "-1"));
        $this->assertEquals($systemdata->category, api::get_effective_context_category($contextsubcat, "0"));
        $this->assertEquals($systemdata->category, api::get_effective_context_category($contextcourse));
        $this->assertEquals($systemdata->category, api::get_effective_context_category($contextcourse, "-1"));
        $this->assertEquals($systemdata->category, api::get_effective_context_category($contextcourse, "0"));
        $this->assertEquals($systemdata->category, api::get_effective_context_category($contextforum));
        $this->assertEquals($systemdata->category, api::get_effective_context_category($contextforum, "-1"));
        $this->assertEquals($systemdata->category, api::get_effective_context_category($contextforum, "0"));
        $this->assertEquals($systemdata->category, api::get_effective_context_category($contextuser));
        $this->assertEquals($systemdata->category, api::get_effective_context_category($contextuser, "-1"));
        $this->assertEquals($systemdata->category, api::get_effective_context_category($contextuser, "0"));

        // When actively set, user will use the specified value.
        $userdata = $this->create_and_set_purpose_for_contextlevel('PT1S', CONTEXT_USER);

        $this->assertEquals($systemdata->purpose, api::get_effective_context_purpose($contextsystem));
        $this->assertEquals($systemdata->purpose, api::get_effective_context_purpose($contextcat));
        $this->assertEquals($systemdata->purpose, api::get_effective_context_purpose($contextcat, "-1"));
        $this->assertEquals($systemdata->purpose, api::get_effective_context_purpose($contextcat, "0"));
        $this->assertEquals($systemdata->purpose, api::get_effective_context_purpose($contextsubcat));
        $this->assertEquals($systemdata->purpose, api::get_effective_context_purpose($contextsubcat, "-1"));
        $this->assertEquals($systemdata->purpose, api::get_effective_context_purpose($contextsubcat, "0"));
        $this->assertEquals($systemdata->purpose, api::get_effective_context_purpose($contextcourse));
        $this->assertEquals($systemdata->purpose, api::get_effective_context_purpose($contextcourse, "-1"));
        $this->assertEquals($systemdata->purpose, api::get_effective_context_purpose($contextcourse, "0"));
        $this->assertEquals($systemdata->purpose, api::get_effective_context_purpose($contextforum));
        $this->assertEquals($systemdata->purpose, api::get_effective_context_purpose($contextforum, "-1"));
        $this->assertEquals($systemdata->purpose, api::get_effective_context_purpose($contextforum, "0"));
        $this->assertEquals($userdata->purpose, api::get_effective_context_purpose($contextuser));
        $this->assertEquals($systemdata->purpose, api::get_effective_context_purpose($contextuser, "-1"));

        $this->assertEquals($systemdata->category, api::get_effective_context_category($contextsystem));
        $this->assertEquals($systemdata->category, api::get_effective_context_category($contextcat));
        $this->assertEquals($systemdata->category, api::get_effective_context_category($contextcat, "-1"));
        $this->assertEquals($systemdata->category, api::get_effective_context_category($contextcat, "0"));
        $this->assertEquals($systemdata->category, api::get_effective_context_category($contextsubcat));
        $this->assertEquals($systemdata->category, api::get_effective_context_category($contextsubcat, "-1"));
        $this->assertEquals($systemdata->category, api::get_effective_context_category($contextsubcat, "0"));
        $this->assertEquals($systemdata->category, api::get_effective_context_category($contextcourse));
        $this->assertEquals($systemdata->category, api::get_effective_context_category($contextcourse, "-1"));
        $this->assertEquals($systemdata->category, api::get_effective_context_category($contextcourse, "0"));
        $this->assertEquals($systemdata->category, api::get_effective_context_category($contextforum));
        $this->assertEquals($systemdata->category, api::get_effective_context_category($contextforum, "-1"));
        $this->assertEquals($systemdata->category, api::get_effective_context_category($contextforum, "0"));
        $this->assertEquals($userdata->category, api::get_effective_context_category($contextuser));
        $this->assertEquals($systemdata->purpose, api::get_effective_context_purpose($contextuser, "-1"));

        // Set a context for the top category.
        $catpurpose = new purpose(0, (object) [
                'name' => 'Purpose',
                'retentionperiod' => 'P1D',
                'lawfulbases' => 'gdpr_art_6_1_a',
            ]);
        $catpurpose->save();
        $catcategory = new category(0, (object) ['name' => 'Category']);
        $catcategory->save();
        api::set_context_instance((object) [
                'contextid' => $contextcat->id,
                'purposeid' => $catpurpose->get('id'),
                'categoryid' => $catcategory->get('id'),
            ]);

        $this->assertEquals($systemdata->purpose, api::get_effective_context_purpose($contextsystem));
        $this->assertEquals($catpurpose, api::get_effective_context_purpose($contextcat));
        $this->assertEquals($systemdata->purpose, api::get_effective_context_purpose($contextcat, "-1"));
        $this->assertEquals($catpurpose, api::get_effective_context_purpose($contextcat, "0"));
        $this->assertEquals($catpurpose, api::get_effective_context_purpose($contextsubcat));
        $this->assertEquals($catpurpose, api::get_effective_context_purpose($contextsubcat, "-1"));
        $this->assertEquals($catpurpose, api::get_effective_context_purpose($contextsubcat, "0"));
        $this->assertEquals($catpurpose, api::get_effective_context_purpose($contextcourse));
        $this->assertEquals($catpurpose, api::get_effective_context_purpose($contextcourse, "-1"));
        $this->assertEquals($catpurpose, api::get_effective_context_purpose($contextcourse, "0"));
        $this->assertEquals($catpurpose, api::get_effective_context_purpose($contextforum));
        $this->assertEquals($catpurpose, api::get_effective_context_purpose($contextforum, "-1"));
        $this->assertEquals($catpurpose, api::get_effective_context_purpose($contextforum, "0"));
        $this->assertEquals($catpurpose, api::get_effective_context_purpose($contextforum, "0"));

        $this->assertEquals($systemdata->category, api::get_effective_context_category($contextsystem));
        $this->assertEquals($catcategory, api::get_effective_context_category($contextcat));
        $this->assertEquals($systemdata->category, api::get_effective_context_category($contextcat, "-1"));
        $this->assertEquals($catcategory, api::get_effective_context_category($contextcat, "0"));
        $this->assertEquals($catcategory, api::get_effective_context_category($contextsubcat));
        $this->assertEquals($catcategory, api::get_effective_context_category($contextsubcat, "-1"));
        $this->assertEquals($catcategory, api::get_effective_context_category($contextsubcat, "0"));
        $this->assertEquals($catcategory, api::get_effective_context_category($contextcourse));
        $this->assertEquals($catcategory, api::get_effective_context_category($contextcourse, "-1"));
        $this->assertEquals($catcategory, api::get_effective_context_category($contextcourse, "0"));
        $this->assertEquals($catcategory, api::get_effective_context_category($contextforum));
        $this->assertEquals($catcategory, api::get_effective_context_category($contextforum, "-1"));
        $this->assertEquals($catcategory, api::get_effective_context_category($contextforum, "0"));

        // Set a context for the sub category.
        $subcatpurpose = new purpose(0, (object) [
                'name' => 'Purpose',
                'retentionperiod' => 'P1D',
                'lawfulbases' => 'gdpr_art_6_1_a',
            ]);
        $subcatpurpose->save();
        $subcatcategory = new category(0, (object) ['name' => 'Category']);
        $subcatcategory->save();
        api::set_context_instance((object) [
                'contextid' => $contextsubcat->id,
                'purposeid' => $subcatpurpose->get('id'),
                'categoryid' => $subcatcategory->get('id'),
            ]);

        $this->assertEquals($systemdata->purpose, api::get_effective_context_purpose($contextsystem));
        $this->assertEquals($catpurpose, api::get_effective_context_purpose($contextcat));
        $this->assertEquals($systemdata->purpose, api::get_effective_context_purpose($contextcat, "-1"));
        $this->assertEquals($catpurpose, api::get_effective_context_purpose($contextcat, "0"));
        $this->assertEquals($subcatpurpose, api::get_effective_context_purpose($contextsubcat));
        $this->assertEquals($catpurpose, api::get_effective_context_purpose($contextsubcat, "-1"));
        $this->assertEquals($subcatpurpose, api::get_effective_context_purpose($contextsubcat, "0"));
        $this->assertEquals($subcatpurpose, api::get_effective_context_purpose($contextcourse));
        $this->assertEquals($subcatpurpose, api::get_effective_context_purpose($contextcourse, "-1"));
        $this->assertEquals($subcatpurpose, api::get_effective_context_purpose($contextcourse, "0"));
        $this->assertEquals($subcatpurpose, api::get_effective_context_purpose($contextforum));
        $this->assertEquals($subcatpurpose, api::get_effective_context_purpose($contextforum, "-1"));
        $this->assertEquals($subcatpurpose, api::get_effective_context_purpose($contextforum, "0"));

        $this->assertEquals($systemdata->category, api::get_effective_context_category($contextsystem));
        $this->assertEquals($catcategory, api::get_effective_context_category($contextcat));
        $this->assertEquals($systemdata->category, api::get_effective_context_category($contextcat, "-1"));
        $this->assertEquals($catcategory, api::get_effective_context_category($contextcat, "0"));
        $this->assertEquals($subcatcategory, api::get_effective_context_category($contextsubcat));
        $this->assertEquals($catcategory, api::get_effective_context_category($contextsubcat, "-1"));
        $this->assertEquals($subcatcategory, api::get_effective_context_category($contextsubcat, "0"));
        $this->assertEquals($subcatcategory, api::get_effective_context_category($contextcourse));
        $this->assertEquals($subcatcategory, api::get_effective_context_category($contextcourse, "-1"));
        $this->assertEquals($subcatcategory, api::get_effective_context_category($contextcourse, "0"));
        $this->assertEquals($subcatcategory, api::get_effective_context_category($contextforum));
        $this->assertEquals($subcatcategory, api::get_effective_context_category($contextforum, "-1"));
        $this->assertEquals($subcatcategory, api::get_effective_context_category($contextforum, "0"));

        // Set a context for the course.
        $coursepurpose = new purpose(0, (object) [
                'name' => 'Purpose',
                'retentionperiod' => 'P1D',
                'lawfulbases' => 'gdpr_art_6_1_a',
            ]);
        $coursepurpose->save();
        $coursecategory = new category(0, (object) ['name' => 'Category']);
        $coursecategory->save();
        api::set_context_instance((object) [
                'contextid' => $contextcourse->id,
                'purposeid' => $coursepurpose->get('id'),
                'categoryid' => $coursecategory->get('id'),
            ]);

        $this->assertEquals($systemdata->purpose, api::get_effective_context_purpose($contextsystem));
        $this->assertEquals($catpurpose, api::get_effective_context_purpose($contextcat));
        $this->assertEquals($systemdata->purpose, api::get_effective_context_purpose($contextcat, "-1"));
        $this->assertEquals($catpurpose, api::get_effective_context_purpose($contextcat, "0"));
        $this->assertEquals($subcatpurpose, api::get_effective_context_purpose($contextsubcat));
        $this->assertEquals($catpurpose, api::get_effective_context_purpose($contextsubcat, "-1"));
        $this->assertEquals($subcatpurpose, api::get_effective_context_purpose($contextsubcat, "0"));
        $this->assertEquals($coursepurpose, api::get_effective_context_purpose($contextcourse));
        $this->assertEquals($subcatpurpose, api::get_effective_context_purpose($contextcourse, "-1"));
        $this->assertEquals($coursepurpose, api::get_effective_context_purpose($contextcourse, "0"));
        $this->assertEquals($coursepurpose, api::get_effective_context_purpose($contextforum));
        $this->assertEquals($coursepurpose, api::get_effective_context_purpose($contextforum, "-1"));
        $this->assertEquals($coursepurpose, api::get_effective_context_purpose($contextforum, "0"));

        $this->assertEquals($systemdata->category, api::get_effective_context_category($contextsystem));
        $this->assertEquals($catcategory, api::get_effective_context_category($contextcat));
        $this->assertEquals($systemdata->category, api::get_effective_context_category($contextcat, "-1"));
        $this->assertEquals($catcategory, api::get_effective_context_category($contextcat, "0"));
        $this->assertEquals($subcatcategory, api::get_effective_context_category($contextsubcat));
        $this->assertEquals($catcategory, api::get_effective_context_category($contextsubcat, "-1"));
        $this->assertEquals($subcatcategory, api::get_effective_context_category($contextsubcat, "0"));
        $this->assertEquals($coursecategory, api::get_effective_context_category($contextcourse));
        $this->assertEquals($subcatcategory, api::get_effective_context_category($contextcourse, "-1"));
        $this->assertEquals($coursecategory, api::get_effective_context_category($contextcourse, "0"));
        $this->assertEquals($coursecategory, api::get_effective_context_category($contextforum));
        $this->assertEquals($coursecategory, api::get_effective_context_category($contextforum, "-1"));
        $this->assertEquals($coursecategory, api::get_effective_context_category($contextforum, "0"));

        // Set a context for the forum.
        $forumpurpose = new purpose(0, (object) [
                'name' => 'Purpose',
                'retentionperiod' => 'P1D',
                'lawfulbases' => 'gdpr_art_6_1_a',
            ]);
        $forumpurpose->save();
        $forumcategory = new category(0, (object) ['name' => 'Category']);
        $forumcategory->save();
        api::set_context_instance((object) [
                'contextid' => $contextforum->id,
                'purposeid' => $forumpurpose->get('id'),
                'categoryid' => $forumcategory->get('id'),
            ]);

        $this->assertEquals($systemdata->purpose, api::get_effective_context_purpose($contextsystem));
        $this->assertEquals($catpurpose, api::get_effective_context_purpose($contextcat));
        $this->assertEquals($systemdata->purpose, api::get_effective_context_purpose($contextcat, "-1"));
        $this->assertEquals($catpurpose, api::get_effective_context_purpose($contextcat, "0"));
        $this->assertEquals($subcatpurpose, api::get_effective_context_purpose($contextsubcat));
        $this->assertEquals($catpurpose, api::get_effective_context_purpose($contextsubcat, "-1"));
        $this->assertEquals($subcatpurpose, api::get_effective_context_purpose($contextsubcat, "0"));
        $this->assertEquals($coursepurpose, api::get_effective_context_purpose($contextcourse));
        $this->assertEquals($subcatpurpose, api::get_effective_context_purpose($contextcourse, "-1"));
        $this->assertEquals($coursepurpose, api::get_effective_context_purpose($contextcourse, "0"));
        $this->assertEquals($forumpurpose, api::get_effective_context_purpose($contextforum));
        $this->assertEquals($coursepurpose, api::get_effective_context_purpose($contextforum, "-1"));
        $this->assertEquals($forumpurpose, api::get_effective_context_purpose($contextforum, "0"));

        $this->assertEquals($systemdata->category, api::get_effective_context_category($contextsystem));
        $this->assertEquals($catcategory, api::get_effective_context_category($contextcat));
        $this->assertEquals($systemdata->category, api::get_effective_context_category($contextcat, "-1"));
        $this->assertEquals($catcategory, api::get_effective_context_category($contextcat, "0"));
        $this->assertEquals($subcatcategory, api::get_effective_context_category($contextsubcat));
        $this->assertEquals($catcategory, api::get_effective_context_category($contextsubcat, "-1"));
        $this->assertEquals($subcatcategory, api::get_effective_context_category($contextsubcat, "0"));
        $this->assertEquals($coursecategory, api::get_effective_context_category($contextcourse));
        $this->assertEquals($subcatcategory, api::get_effective_context_category($contextcourse, "-1"));
        $this->assertEquals($coursecategory, api::get_effective_context_category($contextcourse, "0"));
        $this->assertEquals($forumcategory, api::get_effective_context_category($contextforum));
        $this->assertEquals($coursecategory, api::get_effective_context_category($contextforum, "-1"));
        $this->assertEquals($forumcategory, api::get_effective_context_category($contextforum, "0"));
    }

    /**
     * Ensure that context inheritance works up the context tree when inherit values are explicitly set at the
     * contextlevel.
     *
     * Although it should not be possible to set hard INHERIT values at this level, there may be legacy data which still
     * contains this.
     */
    public function test_effective_context_inheritance_explicitly_set() {
        $this->resetAfterTest();

        $systemdata = $this->create_and_set_purpose_for_contextlevel('PT1S', CONTEXT_SYSTEM);

        /*
         * System
         * - Cat
         *   - Subcat
         *     - Course
         *       - Forum
         * - User
         *   - User block
         */
        $cat = $this->getDataGenerator()->create_category();
        $subcat = $this->getDataGenerator()->create_category(['parent' => $cat->id]);
        $course = $this->getDataGenerator()->create_course(['category' => $subcat->id]);
        $forum = $this->getDataGenerator()->create_module('forum', ['course' => $course->id]);
        list(, $forumcm) = get_course_and_cm_from_instance($forum->id, 'forum');

        $contextsystem = \context_system::instance();
        $contextcat = \context_coursecat::instance($cat->id);
        $contextsubcat = \context_coursecat::instance($subcat->id);
        $contextcourse = \context_course::instance($course->id);
        $contextforum = \context_module::instance($forumcm->id);

        // Initially everything is set to Inherit.
        $this->assertEquals($systemdata->purpose, api::get_effective_context_purpose($contextsystem));
        $this->assertEquals($systemdata->purpose, api::get_effective_context_purpose($contextcat));
        $this->assertEquals($systemdata->purpose, api::get_effective_context_purpose($contextsubcat));
        $this->assertEquals($systemdata->purpose, api::get_effective_context_purpose($contextcourse));
        $this->assertEquals($systemdata->purpose, api::get_effective_context_purpose($contextforum));

        $this->assertEquals($systemdata->category, api::get_effective_context_category($contextsystem));
        $this->assertEquals($systemdata->category, api::get_effective_context_category($contextcat));
        $this->assertEquals($systemdata->category, api::get_effective_context_category($contextsubcat));
        $this->assertEquals($systemdata->category, api::get_effective_context_category($contextcourse));
        $this->assertEquals($systemdata->category, api::get_effective_context_category($contextforum));

        // Set a default value of inherit for CONTEXT_COURSECAT.
        $classname = \context_helper::get_class_for_level(CONTEXT_COURSECAT);
        list($purposevar, $categoryvar) = data_registry::var_names_from_context($classname);
        set_config($purposevar, '-1', 'tool_dataprivacy');
        set_config($categoryvar, '-1', 'tool_dataprivacy');

        $this->assertEquals($systemdata->purpose, api::get_effective_context_purpose($contextsystem));
        $this->assertEquals($systemdata->purpose, api::get_effective_context_purpose($contextcat));
        $this->assertEquals($systemdata->purpose, api::get_effective_context_purpose($contextsubcat));
        $this->assertEquals($systemdata->purpose, api::get_effective_context_purpose($contextcourse));
        $this->assertEquals($systemdata->purpose, api::get_effective_context_purpose($contextforum));

        $this->assertEquals($systemdata->category, api::get_effective_context_category($contextsystem));
        $this->assertEquals($systemdata->category, api::get_effective_context_category($contextcat));
        $this->assertEquals($systemdata->category, api::get_effective_context_category($contextsubcat));
        $this->assertEquals($systemdata->category, api::get_effective_context_category($contextcourse));
        $this->assertEquals($systemdata->category, api::get_effective_context_category($contextforum));

        // Set a default value of inherit for CONTEXT_COURSE.
        $classname = \context_helper::get_class_for_level(CONTEXT_COURSE);
        list($purposevar, $categoryvar) = data_registry::var_names_from_context($classname);
        set_config($purposevar, '-1', 'tool_dataprivacy');
        set_config($categoryvar, '-1', 'tool_dataprivacy');

        $this->assertEquals($systemdata->purpose, api::get_effective_context_purpose($contextsystem));
        $this->assertEquals($systemdata->purpose, api::get_effective_context_purpose($contextcat));
        $this->assertEquals($systemdata->purpose, api::get_effective_context_purpose($contextsubcat));
        $this->assertEquals($systemdata->purpose, api::get_effective_context_purpose($contextcourse));
        $this->assertEquals($systemdata->purpose, api::get_effective_context_purpose($contextforum));

        $this->assertEquals($systemdata->category, api::get_effective_context_category($contextsystem));
        $this->assertEquals($systemdata->category, api::get_effective_context_category($contextcat));
        $this->assertEquals($systemdata->category, api::get_effective_context_category($contextsubcat));
        $this->assertEquals($systemdata->category, api::get_effective_context_category($contextcourse));
        $this->assertEquals($systemdata->category, api::get_effective_context_category($contextforum));

        // Set a default value of inherit for CONTEXT_MODULE.
        $classname = \context_helper::get_class_for_level(CONTEXT_MODULE);
        list($purposevar, $categoryvar) = data_registry::var_names_from_context($classname);
        set_config($purposevar, '-1', 'tool_dataprivacy');
        set_config($categoryvar, '-1', 'tool_dataprivacy');

        $this->assertEquals($systemdata->purpose, api::get_effective_context_purpose($contextsystem));
        $this->assertEquals($systemdata->purpose, api::get_effective_context_purpose($contextcat));
        $this->assertEquals($systemdata->purpose, api::get_effective_context_purpose($contextsubcat));
        $this->assertEquals($systemdata->purpose, api::get_effective_context_purpose($contextcourse));
        $this->assertEquals($systemdata->purpose, api::get_effective_context_purpose($contextforum));

        $this->assertEquals($systemdata->category, api::get_effective_context_category($contextsystem));
        $this->assertEquals($systemdata->category, api::get_effective_context_category($contextcat));
        $this->assertEquals($systemdata->category, api::get_effective_context_category($contextsubcat));
        $this->assertEquals($systemdata->category, api::get_effective_context_category($contextcourse));
        $this->assertEquals($systemdata->category, api::get_effective_context_category($contextforum));
    }

    /**
     * Creates test purposes and categories.
     *
     * @return null
     */
    protected function add_purposes_and_categories() {
        $this->resetAfterTest();

        $purpose1 = api::create_purpose((object)['name' => 'p1', 'retentionperiod' => 'PT1H', 'lawfulbases' => 'gdpr_art_6_1_a']);
        $purpose2 = api::create_purpose((object)['name' => 'p2', 'retentionperiod' => 'PT2H', 'lawfulbases' => 'gdpr_art_6_1_b']);
        $purpose3 = api::create_purpose((object)['name' => 'p3', 'retentionperiod' => 'PT3H', 'lawfulbases' => 'gdpr_art_6_1_c']);

        $cat1 = api::create_category((object)['name' => 'a']);
        $cat2 = api::create_category((object)['name' => 'b']);
        $cat3 = api::create_category((object)['name' => 'c']);

        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();

        $module1 = $this->getDataGenerator()->create_module('resource', array('course' => $course1));
        $module2 = $this->getDataGenerator()->create_module('resource', array('course' => $course2));

        return [
            [$purpose1, $purpose2, $purpose3],
            [$cat1, $cat2, $cat3],
            [$course1, $course2],
            [$module1, $module2]
        ];
    }

    /**
     * Test that delete requests do not filter out protected purpose contexts if the the site is properly configured.
     */
    public function test_get_approved_contextlist_collection_for_collection_delete_course_no_site_config() {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();

        $course = $this->getDataGenerator()->create_course(['startdate' => time() - YEARSECS, 'enddate' => time() - YEARSECS]);
        $coursecontext = \context_course::instance($course->id);

        $forum = $this->getDataGenerator()->create_module('forum', ['course' => $course->id]);
        list(, $forumcm) = get_course_and_cm_from_instance($forum->id, 'forum');
        $contextforum = \context_module::instance($forumcm->id);

        $this->getDataGenerator()->enrol_user($user->id, $course->id, 'student');

        // Create the initial contextlist.
        $initialcollection = new \core_privacy\local\request\contextlist_collection($user->id);

        $contextlist = new \core_privacy\local\request\contextlist();
        $contextlist->add_from_sql('SELECT id FROM {context} WHERE id = :contextid', ['contextid' => $coursecontext->id]);
        $contextlist->set_component('tool_dataprivacy');
        $initialcollection->add_contextlist($contextlist);

        $contextlist = new \core_privacy\local\request\contextlist();
        $contextlist->add_from_sql('SELECT id FROM {context} WHERE id = :contextid', ['contextid' => $contextforum->id]);
        $contextlist->set_component('mod_forum');
        $initialcollection->add_contextlist($contextlist);

        $collection = api::get_approved_contextlist_collection_for_collection(
                $initialcollection, $user, api::DATAREQUEST_TYPE_DELETE);

        $this->assertCount(2, $collection);

        $list = $collection->get_contextlist_for_component('tool_dataprivacy');
        $this->assertCount(1, $list);

        $list = $collection->get_contextlist_for_component('mod_forum');
        $this->assertCount(1, $list);
    }

    /**
     * Test that delete requests do not filter out protected purpose contexts if they are already expired.
     */
    public function test_get_approved_contextlist_collection_for_collection_delete_course_expired_protected() {
        $this->resetAfterTest();

        $purposes = $this->setup_basics('PT1H', 'PT1H', 'PT1H');
        $purposes->course->purpose->set('protected', 1)->save();

        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course(['startdate' => time() - YEARSECS, 'enddate' => time() - YEARSECS]);
        $coursecontext = \context_course::instance($course->id);

        $this->getDataGenerator()->enrol_user($user->id, $course->id, 'student');

        // Create the initial contextlist.
        $contextlist = new \core_privacy\local\request\contextlist();
        $contextlist->add_from_sql('SELECT id FROM {context} WHERE id = :contextid', ['contextid' => $coursecontext->id]);
        $contextlist->set_component('tool_dataprivacy');

        $initialcollection = new \core_privacy\local\request\contextlist_collection($user->id);
        $initialcollection->add_contextlist($contextlist);

        $purposes->course->purpose->set('protected', 1)->save();
        $collection = api::get_approved_contextlist_collection_for_collection(
                $initialcollection, $user, api::DATAREQUEST_TYPE_DELETE);

        $this->assertCount(1, $collection);

        $list = $collection->get_contextlist_for_component('tool_dataprivacy');
        $this->assertCount(1, $list);
    }

    /**
     * Test that delete requests does filter out protected purpose contexts which are not expired.
     */
    public function test_get_approved_contextlist_collection_for_collection_delete_course_unexpired_protected() {
        $this->resetAfterTest();

        $purposes = $this->setup_basics('PT1H', 'PT1H', 'P1Y');
        $purposes->course->purpose->set('protected', 1)->save();

        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course(['startdate' => time() - YEARSECS, 'enddate' => time()]);
        $coursecontext = \context_course::instance($course->id);

        $this->getDataGenerator()->enrol_user($user->id, $course->id, 'student');

        // Create the initial contextlist.
        $contextlist = new \core_privacy\local\request\contextlist();
        $contextlist->add_from_sql('SELECT id FROM {context} WHERE id = :contextid', ['contextid' => $coursecontext->id]);
        $contextlist->set_component('tool_dataprivacy');

        $initialcollection = new \core_privacy\local\request\contextlist_collection($user->id);
        $initialcollection->add_contextlist($contextlist);

        $purposes->course->purpose->set('protected', 1)->save();
        $collection = api::get_approved_contextlist_collection_for_collection(
                $initialcollection, $user, api::DATAREQUEST_TYPE_DELETE);

        $this->assertCount(0, $collection);

        $list = $collection->get_contextlist_for_component('tool_dataprivacy');
        $this->assertEmpty($list);
    }

    /**
     * Test that delete requests do not filter out unexpired contexts if they are not protected.
     */
    public function test_get_approved_contextlist_collection_for_collection_delete_course_unexpired_unprotected() {
        $this->resetAfterTest();

        $purposes = $this->setup_basics('PT1H', 'PT1H', 'P1Y');
        $purposes->course->purpose->set('protected', 1)->save();

        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course(['startdate' => time() - YEARSECS, 'enddate' => time()]);
        $coursecontext = \context_course::instance($course->id);

        $this->getDataGenerator()->enrol_user($user->id, $course->id, 'student');

        // Create the initial contextlist.
        $contextlist = new \core_privacy\local\request\contextlist();
        $contextlist->add_from_sql('SELECT id FROM {context} WHERE id = :contextid', ['contextid' => $coursecontext->id]);
        $contextlist->set_component('tool_dataprivacy');

        $initialcollection = new \core_privacy\local\request\contextlist_collection($user->id);
        $initialcollection->add_contextlist($contextlist);

        $purposes->course->purpose->set('protected', 0)->save();
        $collection = api::get_approved_contextlist_collection_for_collection(
                $initialcollection, $user, api::DATAREQUEST_TYPE_DELETE);

        $this->assertCount(1, $collection);

        $list = $collection->get_contextlist_for_component('tool_dataprivacy');
        $this->assertCount(1, $list);
    }

    /**
     * Data provider for \tool_dataprivacy_api_testcase::test_set_context_defaults
     */
    public function set_context_defaults_provider() {
        $contextlevels = [
            [CONTEXT_COURSECAT],
            [CONTEXT_COURSE],
            [CONTEXT_MODULE],
            [CONTEXT_BLOCK],
        ];
        $paramsets = [
            [true, true, false, false], // Inherit category and purpose, Not for activity, Don't override.
            [true, false, false, false], // Inherit category but not purpose, Not for activity, Don't override.
            [false, true, false, false], // Inherit purpose but not category, Not for activity, Don't override.
            [false, false, false, false], // Don't inherit both category and purpose, Not for activity, Don't override.
            [false, false, false, true], // Don't inherit both category and purpose, Not for activity, Override instances.
        ];
        $data = [];
        foreach ($contextlevels as $level) {
            foreach ($paramsets as $set) {
                $data[] = array_merge($level, $set);
            }
            if ($level == CONTEXT_MODULE) {
                // Add a combination where defaults for activity is being set.
                $data[] = [CONTEXT_MODULE, false, false, true, false];
                $data[] = [CONTEXT_MODULE, false, false, true, true];
            }
        }
        return $data;
    }

    /**
     * Test for \tool_dataprivacy\api::set_context_defaults()
     *
     * @dataProvider set_context_defaults_provider
     * @param int $contextlevel The context level
     * @param bool $inheritcategory Whether to set category value as INHERIT.
     * @param bool $inheritpurpose Whether to set purpose value as INHERIT.
     * @param bool $foractivity Whether to set defaults for an activity.
     * @param bool $override Whether to override instances.
     */
    public function test_set_context_defaults($contextlevel, $inheritcategory, $inheritpurpose, $foractivity, $override) {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator();

        // Generate course cat, course, block, assignment, forum instances.
        $coursecat = $generator->create_category();
        $course = $generator->create_course(['category' => $coursecat->id]);
        $block = $generator->create_block('online_users');
        $assign = $generator->create_module('assign', ['course' => $course->id]);
        $forum = $generator->create_module('forum', ['course' => $course->id]);

        $coursecatcontext = \context_coursecat::instance($coursecat->id);
        $coursecontext = \context_course::instance($course->id);
        $blockcontext = \context_block::instance($block->id);

        list($course, $assigncm) = get_course_and_cm_from_instance($assign->id, 'assign');
        list($course, $forumcm) = get_course_and_cm_from_instance($forum->id, 'forum');
        $assigncontext = \context_module::instance($assigncm->id);
        $forumcontext = \context_module::instance($forumcm->id);

        // Generate purposes and categories.
        $category1 = api::create_category((object)['name' => 'Test category 1']);
        $category2 = api::create_category((object)['name' => 'Test category 2']);
        $purpose1 = api::create_purpose((object)[
            'name' => 'Test purpose 1', 'retentionperiod' => 'PT1M', 'lawfulbases' => 'gdpr_art_6_1_a'
        ]);
        $purpose2 = api::create_purpose((object)[
            'name' => 'Test purpose 2', 'retentionperiod' => 'PT1M', 'lawfulbases' => 'gdpr_art_6_1_a'
        ]);

        // Assign purposes and categories to contexts.
        $coursecatctxinstance = api::set_context_instance((object) [
            'contextid' => $coursecatcontext->id,
            'purposeid' => $purpose1->get('id'),
            'categoryid' => $category1->get('id'),
        ]);
        $coursectxinstance = api::set_context_instance((object) [
            'contextid' => $coursecontext->id,
            'purposeid' => $purpose1->get('id'),
            'categoryid' => $category1->get('id'),
        ]);
        $blockctxinstance = api::set_context_instance((object) [
            'contextid' => $blockcontext->id,
            'purposeid' => $purpose1->get('id'),
            'categoryid' => $category1->get('id'),
        ]);
        $assignctxinstance = api::set_context_instance((object) [
            'contextid' => $assigncontext->id,
            'purposeid' => $purpose1->get('id'),
            'categoryid' => $category1->get('id'),
        ]);
        $forumctxinstance = api::set_context_instance((object) [
            'contextid' => $forumcontext->id,
            'purposeid' => $purpose1->get('id'),
            'categoryid' => $category1->get('id'),
        ]);

        $categoryid = $inheritcategory ? context_instance::INHERIT : $category2->get('id');
        $purposeid = $inheritpurpose ? context_instance::INHERIT : $purpose2->get('id');
        $activity = '';
        if ($contextlevel == CONTEXT_MODULE && $foractivity) {
            $activity = 'assign';
        }
        $result = api::set_context_defaults($contextlevel, $categoryid, $purposeid, $activity, $override);
        $this->assertTrue($result);

        $targetctxinstance = false;
        switch ($contextlevel) {
            case CONTEXT_COURSECAT:
                $targetctxinstance = $coursecatctxinstance;
                break;
            case CONTEXT_COURSE:
                $targetctxinstance = $coursectxinstance;
                break;
            case CONTEXT_MODULE:
                $targetctxinstance = $assignctxinstance;
                break;
            case CONTEXT_BLOCK:
                $targetctxinstance = $blockctxinstance;
                break;
        }
        $this->assertNotFalse($targetctxinstance);

        // Check the context instances.
        $instanceexists = context_instance::record_exists($targetctxinstance->get('id'));
        if ($override) {
            // If overridden, context instances on this context level would have been deleted.
            $this->assertFalse($instanceexists);

            // Check forum context instance.
            $forumctxexists = context_instance::record_exists($forumctxinstance->get('id'));
            if ($contextlevel != CONTEXT_MODULE || $foractivity) {
                // The forum context instance won't be affected in this test if:
                // - The overridden defaults are not for context modules.
                // - Only the defaults for assign have been set.
                $this->assertTrue($forumctxexists);
            } else {
                // If we're overriding for the whole course module context level,
                // then this forum context instance will be deleted as well.
                $this->assertFalse($forumctxexists);
            }
        } else {
            // Otherwise, the context instance record remains.
            $this->assertTrue($instanceexists);
        }

        // Check defaults.
        list($defaultpurpose, $defaultcategory) = data_registry::get_defaults($contextlevel, $activity);
        if (!$inheritpurpose) {
            $this->assertEquals($purposeid, $defaultpurpose);
        }
        if (!$inheritcategory) {
            $this->assertEquals($categoryid, $defaultcategory);
        }
    }

    /**
     * Setup the basics with the specified retention period.
     *
     * @param   string  $system Retention policy for the system.
     * @param   string  $user Retention policy for users.
     * @param   string  $course Retention policy for courses.
     * @param   string  $activity Retention policy for activities.
     */
    protected function setup_basics(string $system, string $user, string $course = null, string $activity = null): \stdClass {
        $this->resetAfterTest();

        $purposes = (object) [
            'system' => $this->create_and_set_purpose_for_contextlevel($system, CONTEXT_SYSTEM),
            'user' => $this->create_and_set_purpose_for_contextlevel($user, CONTEXT_USER),
        ];

        if (null !== $course) {
            $purposes->course = $this->create_and_set_purpose_for_contextlevel($course, CONTEXT_COURSE);
        }

        if (null !== $activity) {
            $purposes->activity = $this->create_and_set_purpose_for_contextlevel($activity, CONTEXT_MODULE);
        }

        return $purposes;
    }

    /**
     * Create a retention period and set it for the specified context level.
     *
     * @param   string  $retention
     * @param   int     $contextlevel
     */
    protected function create_and_set_purpose_for_contextlevel(string $retention, int $contextlevel) {
        $purpose = new purpose(0, (object) [
            'name' => 'Test purpose ' . rand(1, 1000),
            'retentionperiod' => $retention,
            'lawfulbases' => 'gdpr_art_6_1_a',
        ]);
        $purpose->create();

        $cat = new category(0, (object) ['name' => 'Test category']);
        $cat->create();

        if ($contextlevel <= CONTEXT_USER) {
            $record = (object) [
                'purposeid'     => $purpose->get('id'),
                'categoryid'    => $cat->get('id'),
                'contextlevel'  => $contextlevel,
            ];
            api::set_contextlevel($record);
        } else {
            list($purposevar, ) = data_registry::var_names_from_context(
                    \context_helper::get_class_for_level(CONTEXT_COURSE)
                );
            set_config($purposevar, $purpose->get('id'), 'tool_dataprivacy');
        }

        return (object) [
            'purpose' => $purpose,
            'category' => $cat,
        ];
    }

    /**
     * Ensure that the find_ongoing_request_types_for_users only returns requests which are active.
     */
    public function test_find_ongoing_request_types_for_users() {
        $this->resetAfterTest();

        // Create users and their requests:.
        // - u1 has no requests of any type.
        // - u2 has one rejected export request.
        // - u3 has one rejected other request.
        // - u4 has one rejected delete request.
        // - u5 has one active and one rejected export request.
        // - u6 has one active and one rejected other request.
        // - u7 has one active and one rejected delete request.
        // - u8 has one active export, and one active delete request.
        $u1 = $this->getDataGenerator()->create_user();
        $u1expect = (object) [];

        $u2 = $this->getDataGenerator()->create_user();
        $this->create_request_with_type_and_status($u2->id, api::DATAREQUEST_TYPE_EXPORT, api::DATAREQUEST_STATUS_REJECTED);
        $u2expect = (object) [];

        $u3 = $this->getDataGenerator()->create_user();
        $this->create_request_with_type_and_status($u3->id, api::DATAREQUEST_TYPE_OTHERS, api::DATAREQUEST_STATUS_REJECTED);
        $u3expect = (object) [];

        $u4 = $this->getDataGenerator()->create_user();
        $this->create_request_with_type_and_status($u4->id, api::DATAREQUEST_TYPE_DELETE, api::DATAREQUEST_STATUS_REJECTED);
        $u4expect = (object) [];

        $u5 = $this->getDataGenerator()->create_user();
        $this->create_request_with_type_and_status($u5->id, api::DATAREQUEST_TYPE_EXPORT, api::DATAREQUEST_STATUS_REJECTED);
        $this->create_request_with_type_and_status($u5->id, api::DATAREQUEST_TYPE_EXPORT, api::DATAREQUEST_STATUS_APPROVED);
        $u5expect = (object) [
            api::DATAREQUEST_TYPE_EXPORT => true,
        ];

        $u6 = $this->getDataGenerator()->create_user();
        $this->create_request_with_type_and_status($u6->id, api::DATAREQUEST_TYPE_OTHERS, api::DATAREQUEST_STATUS_REJECTED);
        $this->create_request_with_type_and_status($u6->id, api::DATAREQUEST_TYPE_OTHERS, api::DATAREQUEST_STATUS_APPROVED);
        $u6expect = (object) [
            api::DATAREQUEST_TYPE_OTHERS => true,
        ];

        $u7 = $this->getDataGenerator()->create_user();
        $this->create_request_with_type_and_status($u7->id, api::DATAREQUEST_TYPE_DELETE, api::DATAREQUEST_STATUS_REJECTED);
        $this->create_request_with_type_and_status($u7->id, api::DATAREQUEST_TYPE_DELETE, api::DATAREQUEST_STATUS_APPROVED);
        $u7expect = (object) [
            api::DATAREQUEST_TYPE_DELETE => true,
        ];

        $u8 = $this->getDataGenerator()->create_user();
        $this->create_request_with_type_and_status($u8->id, api::DATAREQUEST_TYPE_EXPORT, api::DATAREQUEST_STATUS_APPROVED);
        $this->create_request_with_type_and_status($u8->id, api::DATAREQUEST_TYPE_DELETE, api::DATAREQUEST_STATUS_APPROVED);
        $u8expect = (object) [
            api::DATAREQUEST_TYPE_EXPORT => true,
            api::DATAREQUEST_TYPE_DELETE => true,
        ];

        // Test with no users specified.
        $result = api::find_ongoing_request_types_for_users([]);
        $this->assertEquals([], $result);

        // Fetch a subset of the users.
        $result = api::find_ongoing_request_types_for_users([$u3->id, $u4->id, $u5->id]);
        $this->assertEquals([
                $u3->id => $u3expect,
                $u4->id => $u4expect,
                $u5->id => $u5expect,
            ], $result);

        // Fetch the empty user.
        $result = api::find_ongoing_request_types_for_users([$u1->id]);
        $this->assertEquals([
                $u1->id => $u1expect,
            ], $result);

        // Fetch all.
        $result = api::find_ongoing_request_types_for_users(
            [$u1->id, $u2->id, $u3->id, $u4->id, $u5->id, $u6->id, $u7->id, $u8->id]);
        $this->assertEquals([
                $u1->id => $u1expect,
                $u2->id => $u2expect,
                $u3->id => $u3expect,
                $u4->id => $u4expect,
                $u5->id => $u5expect,
                $u6->id => $u6expect,
                $u7->id => $u7expect,
                $u8->id => $u8expect,
            ], $result);
    }

    /**
     * Create  a new data request for the user with the type and status specified.
     *
     * @param   int     $userid
     * @param   int     $type
     * @param   int     $status
     * @return  \tool_dataprivacy\data_request
     */
    protected function create_request_with_type_and_status(int $userid, int $type, int $status): \tool_dataprivacy\data_request {
        $request = new \tool_dataprivacy\data_request(0, (object) [
            'userid' => $userid,
            'type' => $type,
            'status' => $status,
        ]);

        $request->save();

        return $request;
    }

    /**
     * Test whether user can create data download request for themselves
     */
    public function test_can_create_data_download_request_for_self(): void {
        global $DB;

        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        // The default user role allows for the creation of download data requests.
        $this->assertTrue(api::can_create_data_download_request_for_self());

        // Prohibit that capability.
        $userrole = $DB->get_field('role', 'id', ['shortname' => 'user'], MUST_EXIST);
        assign_capability('tool/dataprivacy:downloadownrequest', CAP_PROHIBIT, $userrole, \context_user::instance($user->id));

        $this->assertFalse(api::can_create_data_download_request_for_self());
    }

    /**
     * Test user cannot create data deletion request for themselves if they don't have
     * "tool/dataprivacy:requestdelete" capability.
     *
     * @throws coding_exception
     */
    public function test_can_create_data_deletion_request_for_self_no() {
        $this->resetAfterTest();
        $userid = $this->getDataGenerator()->create_user()->id;
        $roleid = $this->getDataGenerator()->create_role();
        assign_capability('tool/dataprivacy:requestdelete', CAP_PROHIBIT, $roleid, \context_user::instance($userid));
        role_assign($roleid, $userid, \context_user::instance($userid));
        $this->setUser($userid);
        $this->assertFalse(api::can_create_data_deletion_request_for_self());
    }

    /**
     * Test primary admin cannot create data deletion request for themselves
     */
    public function test_can_create_data_deletion_request_for_self_primary_admin() {
        $this->resetAfterTest();
        $this->setAdminUser();
        $this->assertFalse(api::can_create_data_deletion_request_for_self());
    }

    /**
     * Test secondary admin can create data deletion request for themselves
     */
    public function test_can_create_data_deletion_request_for_self_secondary_admin() {
        $this->resetAfterTest();

        $admin1 = $this->getDataGenerator()->create_user();
        $admin2 = $this->getDataGenerator()->create_user();

        // The primary admin is the one listed first in the 'siteadmins' config.
        set_config('siteadmins', implode(',', [$admin1->id, $admin2->id]));

        // Set the current user as the second admin (non-primary).
        $this->setUser($admin2);

        $this->assertTrue(api::can_create_data_deletion_request_for_self());
    }

    /**
     * Test user can create data deletion request for themselves if they have
     * "tool/dataprivacy:requestdelete" capability.
     *
     * @throws coding_exception
     */
    public function test_can_create_data_deletion_request_for_self_yes() {
        $this->resetAfterTest();
        $userid = $this->getDataGenerator()->create_user()->id;
        $this->setUser($userid);
        $this->assertTrue(api::can_create_data_deletion_request_for_self());
    }

    /**
     * Test user cannot create data deletion request for another user if they
     * don't have "tool/dataprivacy:requestdeleteforotheruser" capability.
     *
     * @throws coding_exception
     * @throws dml_exception
     */
    public function test_can_create_data_deletion_request_for_other_no() {
        $this->resetAfterTest();
        $userid = $this->getDataGenerator()->create_user()->id;
        $this->setUser($userid);
        $this->assertFalse(api::can_create_data_deletion_request_for_other());
    }

    /**
     * Test user can create data deletion request for another user if they
     * don't have "tool/dataprivacy:requestdeleteforotheruser" capability.
     *
     * @throws coding_exception
     */
    public function test_can_create_data_deletion_request_for_other_yes() {
        $this->resetAfterTest();
        $userid = $this->getDataGenerator()->create_user()->id;
        $roleid = $this->getDataGenerator()->create_role();
        $contextsystem = \context_system::instance();
        assign_capability('tool/dataprivacy:requestdeleteforotheruser', CAP_ALLOW, $roleid, $contextsystem);
        role_assign($roleid, $userid, $contextsystem);
        $this->setUser($userid);
        $this->assertTrue(api::can_create_data_deletion_request_for_other($userid));
    }

    /**
     * Check parents can create data deletion request for their children (unless the child is the primary admin),
     * but not other users.
     *
     * @throws coding_exception
     * @throws dml_exception
     */
    public function test_can_create_data_deletion_request_for_children() {
        $this->resetAfterTest();

        $parent = $this->getDataGenerator()->create_user();
        $child = $this->getDataGenerator()->create_user();
        $otheruser = $this->getDataGenerator()->create_user();

        $contextsystem = \context_system::instance();
        $parentrole = $this->getDataGenerator()->create_role();
        assign_capability('tool/dataprivacy:makedatarequestsforchildren', CAP_ALLOW,
            $parentrole, $contextsystem);
        assign_capability('tool/dataprivacy:makedatadeletionrequestsforchildren', CAP_ALLOW,
            $parentrole, $contextsystem);
        role_assign($parentrole, $parent->id, \context_user::instance($child->id));

        $this->setUser($parent);
        $this->assertTrue(api::can_create_data_deletion_request_for_children($child->id));
        $this->assertFalse(api::can_create_data_deletion_request_for_children($otheruser->id));

        // Now make child the primary admin, confirm parent can't make deletion request.
        set_config('siteadmins', $child->id);
        $this->assertFalse(api::can_create_data_deletion_request_for_children($child->id));
    }

    /**
     * Data provider function for testing \tool_dataprivacy\api::queue_data_request_task().
     *
     * @return array
     */
    public function queue_data_request_task_provider() {
        return [
            'With user ID provided' => [true],
            'Without user ID provided' => [false],
        ];
    }

    /**
     * Test for \tool_dataprivacy\api::queue_data_request_task().
     *
     * @dataProvider queue_data_request_task_provider
     * @param bool $withuserid
     */
    public function test_queue_data_request_task(bool $withuserid) {
        $this->resetAfterTest();

        $this->setAdminUser();

        if ($withuserid) {
            $user = $this->getDataGenerator()->create_user();
            api::queue_data_request_task(1, $user->id);
            $expecteduserid = $user->id;
        } else {
            api::queue_data_request_task(1);
            $expecteduserid = null;
        }

        // Test number of queued data request tasks.
        $datarequesttasks = manager::get_adhoc_tasks(process_data_request_task::class);
        $this->assertCount(1, $datarequesttasks);
        $requesttask = reset($datarequesttasks);
        $this->assertEquals($expecteduserid, $requesttask->get_userid());
    }

    /**
     * Data provider for test_is_automatic_request_approval_on().
     */
    public function automatic_request_approval_setting_provider() {
        return [
            'Data export, not set' => [
                'automaticdataexportapproval', api::DATAREQUEST_TYPE_EXPORT, null, false
            ],
            'Data export, turned on' => [
                'automaticdataexportapproval', api::DATAREQUEST_TYPE_EXPORT, true, true
            ],
            'Data export, turned off' => [
                'automaticdataexportapproval', api::DATAREQUEST_TYPE_EXPORT, false, false
            ],
            'Data deletion, not set' => [
                'automaticdatadeletionapproval', api::DATAREQUEST_TYPE_DELETE, null, false
            ],
            'Data deletion, turned on' => [
                'automaticdatadeletionapproval', api::DATAREQUEST_TYPE_DELETE, true, true
            ],
            'Data deletion, turned off' => [
                'automaticdatadeletionapproval', api::DATAREQUEST_TYPE_DELETE, false, false
            ],
        ];
    }

    /**
     * Test for \tool_dataprivacy\api::is_automatic_request_approval_on().
     *
     * @dataProvider automatic_request_approval_setting_provider
     * @param string $setting The automatic approval setting.
     * @param int $type The data request type.
     * @param bool $value The setting's value.
     * @param bool $expected The expected result.
     */
    public function test_is_automatic_request_approval_on($setting, $type, $value, $expected) {
        $this->resetAfterTest();

        if ($value !== null) {
            set_config($setting, $value, 'tool_dataprivacy');
        }

        $this->assertEquals($expected, api::is_automatic_request_approval_on($type));
    }

    /**
     * Test approve part of context list before export if filtering of exports by course is allowed.
     */
    public function test_approve_contexts_belonging_to_request(): void {
        global $DB;
        set_config('allowfiltering', 1, 'tool_dataprivacy');
        $this->resetAfterTest();
        $this->setAdminUser();

        $user = $this->getDataGenerator()->create_user();

        $course = $this->getDataGenerator()->create_course([]);
        $course2 = $this->getDataGenerator()->create_course([]);

        $forum = $this->getDataGenerator()->create_module('forum', ['course' => $course->id]);
        $forum2 = $this->getDataGenerator()->create_module('forum', ['course' => $course2->id]);

        $generator = $this->getDataGenerator()->get_plugin_generator('mod_forum');

        $record = new \stdClass();
        $record->course = $course->id;
        $record->userid = $user->id;
        $record->forum = $forum->id;
        $generator->create_discussion($record);

        $record->course = $course2->id;
        $record->forum = $forum2->id;
        $generator->create_discussion($record);

        $coursecontext1 = \context_course::instance($course->id);
        $coursecontext2 = \context_course::instance($course2->id);

        $forumcontext1 = \context_module::instance($forum->cmid);
        $forumcontext2 = \context_module::instance($forum2->cmid);

        $this->getDataGenerator()->enrol_user($user->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($user->id, $course2->id, 'student');

        $datarequest = api::create_data_request($user->id, api::DATAREQUEST_TYPE_EXPORT);

        ob_start();
        $this->runAdhocTasks('tool_dataprivacy\task\initiate_data_request_task');
        ob_end_clean();

        $contextcount = $DB->count_records('tool_dataprivacy_ctxlst_ctx');
        api::approve_contexts_belonging_to_request($datarequest->get('id'), [$coursecontext1->id]);
        $items = $DB->get_records('tool_dataprivacy_ctxlst_ctx',  null, '', 'id, contextid, status');

        $approvecontexts = [];
        $rejectedcontext = [];
        foreach ($items as $item) {
            if ($item->status == contextlist_context::STATUS_APPROVED) {
                $approvecontexts[] = $item->contextid;
            }
            if ($item->status == contextlist_context::STATUS_REJECTED) {
                $rejectedcontext[] = $item->contextid;
            }
        }

        // Check no pending context left.
        $this->assertEquals($contextcount, count($approvecontexts) + count($rejectedcontext));

        $this->assertContains(strval($coursecontext1->id), $approvecontexts);
        $this->assertContains(strval($forumcontext1->id), $approvecontexts);
        $this->assertContains(strval($coursecontext2->id), $rejectedcontext);
        $this->assertContains(strval($forumcontext2->id), $rejectedcontext);
    }

    /**
     * Test update request contexts with status.
     */
    public function test_update_request_contexts_with_status(): void {
        global $DB;
        set_config('allowfiltering', 1, 'tool_dataprivacy');
        $this->resetAfterTest();
        $this->setAdminUser();

        $user = $this->getDataGenerator()->create_user();

        $course = $this->getDataGenerator()->create_course([]);

        $forum = $this->getDataGenerator()->create_module('forum', ['course' => $course->id]);

        $generator = $this->getDataGenerator()->get_plugin_generator('mod_forum');

        $record = new \stdClass();
        $record->course = $course->id;
        $record->userid = $user->id;
        $record->forum = $forum->id;
        $generator->create_discussion($record);

        $coursecontext = \context_course::instance($course->id);

        $forumcontext = \context_module::instance($forum->cmid);

        $this->getDataGenerator()->enrol_user($user->id, $course->id, 'student');

        $datarequest = api::create_data_request($user->id, api::DATAREQUEST_TYPE_EXPORT);

        ob_start();
        $this->runAdhocTasks('tool_dataprivacy\task\initiate_data_request_task');
        ob_end_clean();

        $requestid = $datarequest->get("id");

        api::update_request_contexts_with_status($requestid, contextlist_context::STATUS_APPROVED);
        // Test all request contexts is updated with status approved.
        $results = $DB->get_records(contextlist_context::TABLE, ['contextid' => $coursecontext->id]);
        foreach ($results as $result) {
            $this->assertEquals($result->status, contextlist_context::STATUS_APPROVED);
        }
        $results = $DB->get_records(contextlist_context::TABLE, ['contextid' => $forumcontext->id]);
        foreach ($results as $result) {
            $this->assertEquals($result->status, contextlist_context::STATUS_APPROVED);
        }
    }

    /**
     * Test api get_course_contexts_for_view_filter.
     */
    public function test_get_course_contexts_for_view_filter(): void {
        set_config('allowfiltering', 1, 'tool_dataprivacy');
        $this->resetAfterTest();
        $this->setAdminUser();

        $user = $this->getDataGenerator()->create_user();

        $course = $this->getDataGenerator()->create_course([]);
        $course2 = $this->getDataGenerator()->create_course([]);

        $record = new \stdClass();
        $record->course = $course->id;
        $record->userid = $user->id;

        $coursecontext1 = \context_course::instance($course->id);
        $coursecontext2 = \context_course::instance($course2->id);

        $this->getDataGenerator()->enrol_user($user->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($user->id, $course2->id, 'student');

        $datarequest = api::create_data_request($user->id, api::DATAREQUEST_TYPE_EXPORT);

        ob_start();
        $this->runAdhocTasks('tool_dataprivacy\task\initiate_data_request_task');
        ob_end_clean();

        api::approve_contexts_belonging_to_request($datarequest->get('id'), [$coursecontext1->id]);
        $requestid = $datarequest->get('id');

        $result = api::get_course_contexts_for_view_filter($requestid);
        $this->assertContains($coursecontext1, $result);
        $this->assertContains($coursecontext2, $result);
    }

    /**
     * Test api validate_create_data_request.
     */
    public function test_validate_create_data_request() {
        $this->resetAfterTest();

        $systemcontext = \context_system::instance();
        $user = $this->getDataGenerator()->create_user();
        // User with permissions for doing requests for others.
        $requester = $this->getDataGenerator()->create_user();
        $role = $this->getDataGenerator()->create_role();
        assign_capability('tool/dataprivacy:makedatarequestsforchildren', CAP_ALLOW, $role, $systemcontext);
        role_assign($role, $requester->id, \context_user::instance($user->id));
        // User without permissions for doing requests.
        $nopermissionuser = $this->getDataGenerator()->create_user();
        $nopermissionrole = $this->getDataGenerator()->create_role();
        assign_capability('tool/dataprivacy:requestdelete', CAP_PROHIBIT, $nopermissionrole, \context_user::instance($nopermissionuser->id));
        assign_capability('tool/dataprivacy:downloadownrequest', CAP_PROHIBIT, $nopermissionrole, \context_user::instance($nopermissionuser->id));
        assign_capability('tool/dataprivacy:requestdeleteforotheruser', CAP_PROHIBIT, $nopermissionrole, $systemcontext);
        role_assign($nopermissionrole, $nopermissionuser->id, \context_user::instance($nopermissionuser->id));
        role_assign($nopermissionrole, $nopermissionuser->id, $systemcontext);

        $this->setUser($user);
        // All good.
        $errors = api::validate_create_data_request((object) [
            'userid' => $user->id,
            'type' => api::DATAREQUEST_TYPE_EXPORT,
        ]);
        $this->assertEmpty($errors);

        // Invalid data request type.
        $errors = api::validate_create_data_request((object) [
            'userid' => $user->id,
            'type' => 1250,
        ]);
        $this->assertCount(1, $errors);
        $this->assertArrayHasKey('errorinvalidrequesttype', $errors);

        // Request already exists.
        api::create_data_request($user->id, api::DATAREQUEST_TYPE_EXPORT);
        $errors = api::validate_create_data_request((object) [
            'userid' => $user->id,
            'type' => api::DATAREQUEST_TYPE_EXPORT,
        ]);
        $this->assertCount(1, $errors);
        $this->assertArrayHasKey('errorrequestalreadyexists', $errors);

        // No permission to request data deletion for itself.
        $this->setUser($nopermissionuser);
        $errors = api::validate_create_data_request((object) [
            'userid' => $nopermissionuser->id,
            'type' => api::DATAREQUEST_TYPE_DELETE,
        ]);
        $this->assertCount(1, $errors);
        $this->assertArrayHasKey('errorcannotrequestdeleteforself', $errors);

        // No permission to request data deletion for other.
        $this->setUser($nopermissionuser);
        $errors = api::validate_create_data_request((object) [
            'userid' => $user->id,
            'type' => api::DATAREQUEST_TYPE_DELETE,
        ]);
        $this->assertCount(1, $errors);
        $this->assertArrayHasKey('errorcannotrequestdeleteforother', $errors);

         // No permission to request data export for itself.
         $this->setUser($nopermissionuser);
         $errors = api::validate_create_data_request((object) [
             'userid' => $nopermissionuser->id,
             'type' => api::DATAREQUEST_TYPE_EXPORT,
         ]);
         $this->assertCount(1, $errors);
         $this->assertArrayHasKey('errorcannotrequestexportforself', $errors);
    }
}

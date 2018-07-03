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
 * @package    tool_dataprivacy
 * @copyright  2018 Jun Pataleta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core\invalid_persistent_exception;
use core\task\manager;
use tool_dataprivacy\context_instance;
use tool_dataprivacy\api;
use tool_dataprivacy\data_registry;
use tool_dataprivacy\expired_context;
use tool_dataprivacy\data_request;
use tool_dataprivacy\local\helper;
use tool_dataprivacy\task\initiate_data_request_task;
use tool_dataprivacy\task\process_data_request_task;

defined('MOODLE_INTERNAL') || die();
global $CFG;

/**
 * API tests.
 *
 * @package    tool_dataprivacy
 * @copyright  2018 Jun Pataleta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_dataprivacy_api_testcase extends advanced_testcase {

    /**
     * setUp.
     */
    public function setUp() {
        $this->resetAfterTest();
    }

    /**
     * Test for api::update_request_status().
     */
    public function test_update_request_status() {
        $generator = new testing_data_generator();
        $s1 = $generator->create_user();
        $this->setUser($s1);

        // Create the sample data request.
        $datarequest = api::create_data_request($s1->id, api::DATAREQUEST_TYPE_EXPORT);

        $requestid = $datarequest->get('id');

        // Update with a valid status.
        $result = api::update_request_status($requestid, api::DATAREQUEST_STATUS_COMPLETE);
        $this->assertTrue($result);

        // Fetch the request record again.
        $datarequest = new data_request($requestid);
        $this->assertEquals(api::DATAREQUEST_STATUS_COMPLETE, $datarequest->get('status'));

        // Update with an invalid status.
        $this->expectException(invalid_persistent_exception::class);
        api::update_request_status($requestid, -1);
    }

    /**
     * Test for api::get_site_dpos() when there are no users with the DPO role.
     */
    public function test_get_site_dpos_no_dpos() {
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
        $generator = new testing_data_generator();
        $u1 = $generator->create_user();
        $u2 = $generator->create_user();

        $context = context_system::instance();

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
     * Test for api::approve_data_request().
     */
    public function test_approve_data_request() {
        global $DB;

        $generator = new testing_data_generator();
        $s1 = $generator->create_user();
        $u1 = $generator->create_user();

        $context = context_system::instance();

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
     * Test for api::approve_data_request() with the request not yet waiting for approval.
     */
    public function test_approve_data_request_not_yet_ready() {
        global $DB;

        $generator = new testing_data_generator();
        $s1 = $generator->create_user();
        $u1 = $generator->create_user();

        $context = context_system::instance();

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

        $this->setUser($u1);
        $this->expectException(moodle_exception::class);
        api::approve_data_request($requestid);
    }

    /**
     * Test for api::approve_data_request() when called by a user who doesn't have the DPO role.
     */
    public function test_approve_data_request_non_dpo_user() {
        $generator = new testing_data_generator();
        $student = $generator->create_user();
        $teacher = $generator->create_user();

        // Create the sample data request.
        $this->setUser($student);
        $datarequest = api::create_data_request($student->id, api::DATAREQUEST_TYPE_EXPORT);

        $requestid = $datarequest->get('id');

        // Login as a user without DPO role.
        $this->setUser($teacher);
        $this->expectException(required_capability_exception::class);
        api::approve_data_request($requestid);
    }

    /**
     * Test for api::can_contact_dpo()
     */
    public function test_can_contact_dpo() {
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

        // No configured site DPOs yet.
        $admin = get_admin();
        $this->assertTrue(api::can_manage_data_requests($admin->id));

        $generator = new testing_data_generator();
        $dpo = $generator->create_user();
        $nondpocapable = $generator->create_user();
        $nondpoincapable = $generator->create_user();

        $context = context_system::instance();

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
     * Test for api::create_data_request()
     */
    public function test_create_data_request() {
        $generator = new testing_data_generator();
        $user = $generator->create_user();
        $comment = 'sample comment';

        // Login as user.
        $this->setUser($user->id);

        // Test data request creation.
        $datarequest = api::create_data_request($user->id, api::DATAREQUEST_TYPE_EXPORT, $comment);
        $this->assertEquals($user->id, $datarequest->get('userid'));
        $this->assertEquals($user->id, $datarequest->get('requestedby'));
        $this->assertEquals(0, $datarequest->get('dpo'));
        $this->assertEquals(api::DATAREQUEST_TYPE_EXPORT, $datarequest->get('type'));
        $this->assertEquals(api::DATAREQUEST_STATUS_PENDING, $datarequest->get('status'));
        $this->assertEquals($comment, $datarequest->get('comments'));

        // Test adhoc task creation.
        $adhoctasks = manager::get_adhoc_tasks(initiate_data_request_task::class);
        $this->assertCount(1, $adhoctasks);
    }

    /**
     * Test for api::create_data_request() made by DPO.
     */
    public function test_create_data_request_by_dpo() {
        global $USER;

        $generator = new testing_data_generator();
        $user = $generator->create_user();
        $comment = 'sample comment';

        // Login as DPO (Admin is DPO by default).
        $this->setAdminUser();

        // Test data request creation.
        $datarequest = api::create_data_request($user->id, api::DATAREQUEST_TYPE_EXPORT, $comment);
        $this->assertEquals($user->id, $datarequest->get('userid'));
        $this->assertEquals($USER->id, $datarequest->get('requestedby'));
        $this->assertEquals($USER->id, $datarequest->get('dpo'));
        $this->assertEquals(api::DATAREQUEST_TYPE_EXPORT, $datarequest->get('type'));
        $this->assertEquals(api::DATAREQUEST_STATUS_PENDING, $datarequest->get('status'));
        $this->assertEquals($comment, $datarequest->get('comments'));

        // Test adhoc task creation.
        $adhoctasks = manager::get_adhoc_tasks(initiate_data_request_task::class);
        $this->assertCount(1, $adhoctasks);
    }

    /**
     * Test for api::create_data_request() made by a parent.
     */
    public function test_create_data_request_by_parent() {
        global $DB;

        $generator = new testing_data_generator();
        $user = $generator->create_user();
        $parent = $generator->create_user();
        $comment = 'sample comment';

        // Get the teacher role pretend it's the parent roles ;).
        $systemcontext = context_system::instance();
        $usercontext = context_user::instance($user->id);
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
        $this->assertEquals(api::DATAREQUEST_STATUS_PENDING, $datarequest->get('status'));
        $this->assertEquals($comment, $datarequest->get('comments'));

        // Test adhoc task creation.
        $adhoctasks = manager::get_adhoc_tasks(initiate_data_request_task::class);
        $this->assertCount(1, $adhoctasks);
    }

    /**
     * Test for api::deny_data_request()
     */
    public function test_deny_data_request() {
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
     * Test for api::deny_data_request()
     */
    public function test_deny_data_request_without_permissions() {
        $generator = new testing_data_generator();
        $user = $generator->create_user();
        $comment = 'sample comment';

        // Login as user.
        $this->setUser($user->id);

        // Test data request creation.
        $datarequest = api::create_data_request($user->id, api::DATAREQUEST_TYPE_EXPORT, $comment);

        // Login as a non-DPO user and try to call deny_data_request.
        $user2 = $generator->create_user();
        $this->setUser($user2);
        $this->expectException(required_capability_exception::class);
        api::deny_data_request($datarequest->get('id'));
    }

    /**
     * Data provider for \tool_dataprivacy_api_testcase::test_get_data_requests().
     *
     * @return array
     */
    public function get_data_requests_provider() {
        $generator = new testing_data_generator();
        $user1 = $generator->create_user();
        $user2 = $generator->create_user();
        $user3 = $generator->create_user();
        $user4 = $generator->create_user();
        $user5 = $generator->create_user();
        $users = [$user1, $user2, $user3, $user4, $user5];
        $completeonly = [api::DATAREQUEST_STATUS_COMPLETE];
        $completeandcancelled = [api::DATAREQUEST_STATUS_COMPLETE, api::DATAREQUEST_STATUS_CANCELLED];

        return [
            // Own data requests.
            [$users, $user1, false, $completeonly],
            // Non-DPO fetching all requets.
            [$users, $user2, true, $completeonly],
            // Admin fetching all completed and cancelled requests.
            [$users, get_admin(), true, $completeandcancelled],
            // Admin fetching all completed requests.
            [$users, get_admin(), true, $completeonly],
            // Guest fetching all requests.
            [$users, guest_user(), true, $completeonly],
        ];
    }

    /**
     * Test for api::get_data_requests()
     *
     * @dataProvider get_data_requests_provider
     * @param stdClass[] $users Array of users to create data requests for.
     * @param stdClass $loggeduser The user logging in.
     * @param boolean $fetchall Whether to fetch all records.
     * @param int[] $statuses Status filters.
     */
    public function test_get_data_requests($users, $loggeduser, $fetchall, $statuses) {
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
                $this->assertContains($request->get('status'), $statuses);
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
     * Data provider for test_has_ongoing_request.
     */
    public function status_provider() {
        return [
            [api::DATAREQUEST_STATUS_PENDING, true],
            [api::DATAREQUEST_STATUS_PREPROCESSING, true],
            [api::DATAREQUEST_STATUS_AWAITING_APPROVAL, true],
            [api::DATAREQUEST_STATUS_APPROVED, true],
            [api::DATAREQUEST_STATUS_PROCESSING, true],
            [api::DATAREQUEST_STATUS_COMPLETE, false],
            [api::DATAREQUEST_STATUS_CANCELLED, false],
            [api::DATAREQUEST_STATUS_REJECTED, false],
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

        // No configured site DPOs yet.
        $admin = get_admin();
        $this->assertTrue(api::is_site_dpo($admin->id));

        $generator = new testing_data_generator();
        $dpo = $generator->create_user();
        $nondpo = $generator->create_user();

        $context = context_system::instance();

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
        $this->assertContains(fullname($dpo), $message->fullmessage);
        $this->assertContains(fullname($user1), $message->fullmessage);
    }

    /**
     * Test of creating purpose as a user without privileges.
     */
    public function test_create_purpose_non_dpo_user() {
        $pleb = $this->getDataGenerator()->create_user();

        $this->setUser($pleb);
        $this->expectException(required_capability_exception::class);
        api::create_purpose((object)[
            'name' => 'aaa',
            'description' => '<b>yeah</b>',
            'descriptionformat' => 1,
            'retentionperiod' => 'PT1M'
        ]);
    }

    /**
     * Test fetching of purposes as a user without privileges.
     */
    public function test_get_purposes_non_dpo_user() {
        $pleb = $this->getDataGenerator()->create_user();
        $this->setAdminUser();
        api::create_purpose((object)[
            'name' => 'bbb',
            'description' => '<b>yeah</b>',
            'descriptionformat' => 1,
            'retentionperiod' => 'PT1M',
            'lawfulbases' => 'gdpr_art_6_1_a'
        ]);

        $this->setUser($pleb);
        $this->expectException(required_capability_exception::class);
        api::get_purposes();
    }

    /**
     * Test updating of purpose as a user without privileges.
     */
    public function test_update_purposes_non_dpo_user() {
        $pleb = $this->getDataGenerator()->create_user();
        $this->setAdminUser();
        $purpose = api::create_purpose((object)[
            'name' => 'bbb',
            'description' => '<b>yeah</b>',
            'descriptionformat' => 1,
            'retentionperiod' => 'PT1M',
            'lawfulbases' => 'gdpr_art_6_1_a'
        ]);

        $this->setUser($pleb);
        $this->expectException(required_capability_exception::class);
        $purpose->set('retentionperiod', 'PT2M');
        api::update_purpose($purpose->to_record());
    }

    /**
     * Test purpose deletion as a user without privileges.
     */
    public function test_delete_purpose_non_dpo_user() {
        $pleb = $this->getDataGenerator()->create_user();
        $this->setAdminUser();
        $purpose = api::create_purpose((object)[
            'name' => 'bbb',
            'description' => '<b>yeah</b>',
            'descriptionformat' => 1,
            'retentionperiod' => 'PT1M',
            'lawfulbases' => 'gdpr_art_6_1_a'
        ]);

        $this->setUser($pleb);
        $this->expectException(required_capability_exception::class);
        api::delete_purpose($purpose->get('id'));
    }

    /**
     * Test data purposes CRUD actions.
     *
     * @return null
     */
    public function test_purpose_crud() {

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
     * Test creation of data categories as a user without privileges.
     */
    public function test_create_category_non_dpo_user() {
        $pleb = $this->getDataGenerator()->create_user();

        $this->setUser($pleb);
        $this->expectException(required_capability_exception::class);
        api::create_category((object)[
            'name' => 'bbb',
            'description' => '<b>yeah</b>',
            'descriptionformat' => 1
        ]);
    }

    /**
     * Test fetching of data categories as a user without privileges.
     */
    public function test_get_categories_non_dpo_user() {
        $pleb = $this->getDataGenerator()->create_user();

        $this->setAdminUser();
        api::create_category((object)[
            'name' => 'bbb',
            'description' => '<b>yeah</b>',
            'descriptionformat' => 1
        ]);

        // Back to a regular user.
        $this->setUser($pleb);
        $this->expectException(required_capability_exception::class);
        api::get_categories();
    }

    /**
     * Test updating of data category as a user without privileges.
     */
    public function test_update_category_non_dpo_user() {
        $pleb = $this->getDataGenerator()->create_user();

        $this->setAdminUser();
        $category = api::create_category((object)[
            'name' => 'bbb',
            'description' => '<b>yeah</b>',
            'descriptionformat' => 1
        ]);

        // Back to a regular user.
        $this->setUser($pleb);
        $this->expectException(required_capability_exception::class);
        $category->set('name', 'yeah');
        api::update_category($category->to_record());
    }

    /**
     * Test deletion of data category as a user without privileges.
     */
    public function test_delete_category_non_dpo_user() {
        $pleb = $this->getDataGenerator()->create_user();

        $this->setAdminUser();
        $category = api::create_category((object)[
            'name' => 'bbb',
            'description' => '<b>yeah</b>',
            'descriptionformat' => 1
        ]);

        // Back to a regular user.
        $this->setUser($pleb);
        $this->expectException(required_capability_exception::class);
        api::delete_category($category->get('id'));
        $this->fail('Users shouldn\'t be allowed to manage categories by default');
    }

    /**
     * Test data categories CRUD actions.
     *
     * @return null
     */
    public function test_category_crud() {

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

        // Course inherits from system if not defined.
        list($purposeid, $categoryid) = data_registry::get_effective_default_contextlevel_purpose_and_category(CONTEXT_COURSE);
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

        list($purposeid, $categoryid) = data_registry::get_effective_default_contextlevel_purpose_and_category(CONTEXT_COURSE);
        $this->assertEquals($purposes[0]->get('id'), $purposeid);
        $this->assertEquals($categories[0]->get('id'), $categoryid);

        list($purposeid, $categoryid) = data_registry::get_effective_default_contextlevel_purpose_and_category(CONTEXT_MODULE);
        $this->assertEquals($purposes[0]->get('id'), $purposeid);
        $this->assertEquals($categories[0]->get('id'), $categoryid);
    }

    /**
     * Test effective contextlevel return.
     *
     * @return null
     */
    public function test_effective_contextlevel() {
        $this->setAdminUser();

        list($purposes, $categories, $courses, $modules) = $this->add_purposes_and_categories();

        // Set the system context level to purpose 1.
        $record = (object)[
            'contextlevel' => CONTEXT_SYSTEM,
            'purposeid' => $purposes[1]->get('id'),
            'categoryid' => $categories[1]->get('id'),
        ];
        api::set_contextlevel($record);

        $purpose = api::get_effective_contextlevel_purpose(CONTEXT_SYSTEM);
        $this->assertEquals($purposes[1]->get('id'), $purpose->get('id'));

        // Value 'not set' will get the default value for the context level. For context level defaults
        // both 'not set' and 'inherit' result in inherit, so the parent context (system) default
        // will be retrieved.
        $purpose = api::get_effective_contextlevel_purpose(CONTEXT_USER);
        $this->assertEquals($purposes[1]->get('id'), $purpose->get('id'));

        // The behaviour forcing an inherit from context system should result in the same effective
        // purpose.
        $record->purposeid = context_instance::INHERIT;
        $record->contextlevel = CONTEXT_USER;
        api::set_contextlevel($record);
        $purpose = api::get_effective_contextlevel_purpose(CONTEXT_USER);
        $this->assertEquals($purposes[1]->get('id'), $purpose->get('id'));

        $record->purposeid = $purposes[2]->get('id');
        $record->contextlevel = CONTEXT_USER;
        api::set_contextlevel($record);

        $purpose = api::get_effective_contextlevel_purpose(CONTEXT_USER);
        $this->assertEquals($purposes[2]->get('id'), $purpose->get('id'));

        // Only system and user allowed.
        $this->expectException(coding_exception::class);
        $record->contextlevel = CONTEXT_COURSE;
        $record->purposeid = $purposes[1]->get('id');
        api::set_contextlevel($record);
    }

    /**
     * Test effective context purposes and categories.
     *
     * @return null
     */
    public function test_effective_context() {
        $this->setAdminUser();

        list($purposes, $categories, $courses, $modules) = $this->add_purposes_and_categories();

        // Define system defaults (all context levels below will inherit).
        list($purposevar, $categoryvar) = data_registry::var_names_from_context(
            \context_helper::get_class_for_level(CONTEXT_SYSTEM)
        );
        set_config($purposevar, $purposes[0]->get('id'), 'tool_dataprivacy');
        set_config($categoryvar, $categories[0]->get('id'), 'tool_dataprivacy');

        // Define course defaults.
        list($purposevar, $categoryvar) = data_registry::var_names_from_context(
            \context_helper::get_class_for_level(CONTEXT_COURSE)
        );
        set_config($purposevar, $purposes[1]->get('id'), 'tool_dataprivacy');
        set_config($categoryvar, $categories[1]->get('id'), 'tool_dataprivacy');

        $course0context = \context_course::instance($courses[0]->id);
        $course1context = \context_course::instance($courses[1]->id);
        $mod0context = \context_module::instance($modules[0]->cmid);
        $mod1context = \context_module::instance($modules[1]->cmid);

        // Set course instance values.
        $record = (object)[
            'contextid' => $course0context->id,
            'purposeid' => $purposes[1]->get('id'),
            'categoryid' => $categories[2]->get('id'),
        ];
        api::set_context_instance($record);
        $category = api::get_effective_context_category($course0context);
        $this->assertEquals($record->categoryid, $category->get('id'));

        // Module instances get the context level default if nothing specified.
        $category = api::get_effective_context_category($mod0context);
        $this->assertEquals($categories[1]->get('id'), $category->get('id'));

        // Module instances get the parent context category if they inherit.
        $record->contextid = $mod0context->id;
        $record->categoryid = context_instance::INHERIT;
        api::set_context_instance($record);
        $category = api::get_effective_context_category($mod0context);
        $this->assertEquals($categories[2]->get('id'), $category->get('id'));

        // The $forcedvalue param allows us to override the actual value (method php-docs for more info).
        $category = api::get_effective_context_category($mod0context, $categories[1]->get('id'));
        $this->assertEquals($categories[1]->get('id'), $category->get('id'));
        $category = api::get_effective_context_category($mod0context, $categories[0]->get('id'));
        $this->assertEquals($categories[0]->get('id'), $category->get('id'));

        // Module instances get the parent context category if they inherit; in
        // this case the parent context category is not set so it should use the
        // context level default (see 'Define course defaults' above).
        $record->contextid = $mod1context->id;
        $record->categoryid = context_instance::INHERIT;
        api::set_context_instance($record);
        $category = api::get_effective_context_category($mod1context);
        $this->assertEquals($categories[1]->get('id'), $category->get('id'));

        // User instances use the value set at user context level instead of the user default.

        // User defaults to cat 0 and user context level to 1.
        list($purposevar, $categoryvar) = data_registry::var_names_from_context(
            \context_helper::get_class_for_level(CONTEXT_USER)
        );
        set_config($purposevar, $purposes[0]->get('id'), 'tool_dataprivacy');
        set_config($categoryvar, $categories[0]->get('id'), 'tool_dataprivacy');
        $usercontextlevel = (object)[
            'contextlevel' => CONTEXT_USER,
            'purposeid' => $purposes[1]->get('id'),
            'categoryid' => $categories[1]->get('id'),
        ];
        api::set_contextlevel($usercontextlevel);

        $newuser = $this->getDataGenerator()->create_user();
        $usercontext = \context_user::instance($newuser->id);
        $category = api::get_effective_context_category($usercontext);
        $this->assertEquals($categories[1]->get('id'), $category->get('id'));
    }

    /**
     * Tests the deletion of expired contexts.
     *
     * @return null
     */
    public function test_expired_context_deletion() {
        global $DB;

        $this->setAdminUser();

        list($purposes, $categories, $courses, $modules) = $this->add_purposes_and_categories();

        $course0context = \context_course::instance($courses[0]->id);
        $course1context = \context_course::instance($courses[1]->id);

        $expiredcontext0 = api::create_expired_context($course0context->id);
        $this->assertEquals(1, $DB->count_records('tool_dataprivacy_ctxexpired'));
        $expiredcontext1 = api::create_expired_context($course1context->id);
        $this->assertEquals(2, $DB->count_records('tool_dataprivacy_ctxexpired'));

        api::delete_expired_context($expiredcontext0->get('id'));
        $this->assertEquals(1, $DB->count_records('tool_dataprivacy_ctxexpired'));
    }

    /**
     * Tests the status of expired contexts.
     *
     * @return null
     */
    public function test_expired_context_status() {
        global $DB;

        $this->setAdminUser();

        list($purposes, $categories, $courses, $modules) = $this->add_purposes_and_categories();

        $course0context = \context_course::instance($courses[0]->id);

        $expiredcontext = api::create_expired_context($course0context->id);

        // Default status.
        $this->assertEquals(expired_context::STATUS_EXPIRED, $expiredcontext->get('status'));

        api::set_expired_context_status($expiredcontext, expired_context::STATUS_APPROVED);
        $this->assertEquals(expired_context::STATUS_APPROVED, $expiredcontext->get('status'));
    }

    /**
     * Creates test purposes and categories.
     *
     * @return null
     */
    protected function add_purposes_and_categories() {

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
     * Test that delete requests filter out protected purpose contexts.
     */
    public function test_add_request_contexts_with_status_delete() {
        $data = $this->setup_test_add_request_contexts_with_status(api::DATAREQUEST_TYPE_DELETE);
        $contextids = $data->list->get_contextids();

        $this->assertCount(1, $contextids);
        $this->assertEquals($data->contexts->unprotected, $contextids);
    }

    /**
     * Test that export requests don't filter out protected purpose contexts.
     */
    public function test_add_request_contexts_with_status_export() {
        $data = $this->setup_test_add_request_contexts_with_status(api::DATAREQUEST_TYPE_EXPORT);
        $contextids = $data->list->get_contextids();

        $this->assertCount(2, $contextids);
        $this->assertEquals($data->contexts->used, $contextids, '', 0.0, 10, true);
    }

    /**
     * Perform setup for the test_add_request_contexts_with_status_xxxxx tests.
     *
     * @param       int $type The type of request to create
     * @return      \stdClass
     */
    protected function setup_test_add_request_contexts_with_status($type) {
        $this->setAdminUser();

        // User under test.
        $s1 = $this->getDataGenerator()->create_user();

        // Create three sample contexts.
        // 1 which should not be returned; and
        // 1 which will be returned and is not protected; and
        // 1 which will be returned and is protected.

        $c1 = $this->getDataGenerator()->create_course();
        $c2 = $this->getDataGenerator()->create_course();
        $c3 = $this->getDataGenerator()->create_course();

        $ctx1 = \context_course::instance($c1->id);
        $ctx2 = \context_course::instance($c2->id);
        $ctx3 = \context_course::instance($c3->id);

        $unprotected = api::create_purpose((object)[
            'name' => 'Unprotected', 'retentionperiod' => 'PT1M', 'lawfulbases' => 'gdpr_art_6_1_a']);
        $protected = api::create_purpose((object) [
            'name' => 'Protected', 'retentionperiod' => 'PT1M', 'lawfulbases' => 'gdpr_art_6_1_a', 'protected' => true]);

        $cat1 = api::create_category((object)['name' => 'a']);

        // Set the defaults.
        list($purposevar, $categoryvar) = data_registry::var_names_from_context(
            \context_helper::get_class_for_level(CONTEXT_SYSTEM)
        );
        set_config($purposevar, $unprotected->get('id'), 'tool_dataprivacy');
        set_config($categoryvar, $cat1->get('id'), 'tool_dataprivacy');

        $contextinstance1 = api::set_context_instance((object) [
                'contextid' => $ctx1->id,
                'purposeid' => $unprotected->get('id'),
                'categoryid' => $cat1->get('id'),
            ]);

        $contextinstance2 = api::set_context_instance((object) [
                'contextid' => $ctx2->id,
                'purposeid' => $unprotected->get('id'),
                'categoryid' => $cat1->get('id'),
            ]);

        $contextinstance3 = api::set_context_instance((object) [
                'contextid' => $ctx3->id,
                'purposeid' => $protected->get('id'),
                'categoryid' => $cat1->get('id'),
            ]);

        $collection = new \core_privacy\local\request\contextlist_collection($s1->id);
        $contextlist = new \core_privacy\local\request\contextlist();
        $contextlist->set_component('tool_dataprivacy');
        $contextlist->add_from_sql('SELECT id FROM {context} WHERE id IN(:ctx2, :ctx3)', [
                'ctx2' => $ctx2->id,
                'ctx3' => $ctx3->id,
            ]);

        $collection->add_contextlist($contextlist);

        // Create the sample data request.
        $datarequest = api::create_data_request($s1->id, $type);
        $requestid = $datarequest->get('id');

        // Add the full collection with contexts 2, and 3.
        api::add_request_contexts_with_status($collection, $requestid, \tool_dataprivacy\contextlist_context::STATUS_PENDING);

        // Mark it as approved.
        api::update_request_contexts_with_status($requestid, \tool_dataprivacy\contextlist_context::STATUS_APPROVED);

        // Fetch the list.
        $approvedcollection = api::get_approved_contextlist_collection_for_request($datarequest);

        return (object) [
            'contexts' => (object) [
                'unused' => [
                    $ctx1->id,
                ],
                'used' => [
                    $ctx2->id,
                    $ctx3->id,
                ],
                'unprotected' => [
                    $ctx2->id,
                ],
                'protected' => [
                    $ctx3->id,
                ],
            ],
            'list' => $approvedcollection->get_contextlist_for_component('tool_dataprivacy'),
        ];
    }
}

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
 * External tests.
 *
 * @package    tool_dataprivacy
 * @copyright  2018 Jun Pataleta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');

use tool_dataprivacy\api;
use tool_dataprivacy\external;

/**
 * External testcase.
 *
 * @package    tool_dataprivacy
 * @copyright  2018 Jun Pataleta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_dataprivacy_external_testcase extends externallib_advanced_testcase {

    /** @var stdClass The user making the request. */
    protected $requester;

    /** @var int The data request ID. */
    protected $requestid;

    /**
     * Setup function- we will create a course and add an assign instance to it.
     */
    protected function setUp() {
        $this->resetAfterTest();

        $generator = new testing_data_generator();
        $requester = $generator->create_user();

        $comment = 'sample comment';

        // Login as user.
        $this->setUser($requester->id);

        // Test data request creation.
        $datarequest = api::create_data_request($requester->id, api::DATAREQUEST_TYPE_EXPORT, $comment);
        $this->requestid = $datarequest->get('id');
        $this->requester = $requester;

        // Log out the user and set force login to true.
        $this->setUser();
    }

    /**
     * Test for external::approve_data_request() with the user not logged in.
     */
    public function test_approve_data_request_not_logged_in() {
        $this->expectException(require_login_exception::class);
        external::approve_data_request($this->requestid);
    }

    /**
     * Test for external::approve_data_request() with the user not having a DPO role.
     */
    public function test_approve_data_request_not_dpo() {
        // Login as the requester.
        $this->setUser($this->requester->id);
        $this->expectException(required_capability_exception::class);
        external::approve_data_request($this->requestid);
    }

    /**
     * Test for external::approve_data_request() for request that's not ready for approval
     */
    public function test_approve_data_request_not_waiting_for_approval() {
        // Admin as DPO. (The default when no one's assigned as a DPO in the site).
        $this->setAdminUser();
        $this->expectException(moodle_exception::class);
        external::approve_data_request($this->requestid);
    }

    /**
     * Test for external::approve_data_request()
     */
    public function test_approve_data_request() {
        // Admin as DPO. (The default when no one's assigned as a DPO in the site).
        $this->setAdminUser();
        api::update_request_status($this->requestid, api::DATAREQUEST_STATUS_AWAITING_APPROVAL);
        $result = external::approve_data_request($this->requestid);
        $return = (object) external_api::clean_returnvalue(external::approve_data_request_returns(), $result);
        $this->assertTrue($return->result);
        $this->assertEmpty($return->warnings);
    }

    /**
     * Test for external::approve_data_request() for a non-existent request ID.
     */
    public function test_approve_data_request_non_existent() {
        // Admin as DPO. (The default when no one's assigned as a DPO in the site).
        $this->setAdminUser();
        api::update_request_status($this->requestid, api::DATAREQUEST_STATUS_AWAITING_APPROVAL);
        $result = external::approve_data_request($this->requestid + 1);
        $return = (object) external_api::clean_returnvalue(external::approve_data_request_returns(), $result);
        $this->assertFalse($return->result);
        $this->assertCount(1, $return->warnings);
        $warning = reset($return->warnings);
        $this->assertEquals('errorrequestnotfound', $warning['warningcode']);
    }

    /**
     * Test for external::cancel_data_request() of another user.
     */
    public function test_cancel_data_request_other_user() {
        $generator = $this->getDataGenerator();
        $otheruser = $generator->create_user();

        // Login as another user.
        $this->setUser($otheruser);

        $result = external::cancel_data_request($this->requestid);
        $return = (object) external_api::clean_returnvalue(external::approve_data_request_returns(), $result);
        $this->assertFalse($return->result);
        $this->assertCount(1, $return->warnings);
        $warning = reset($return->warnings);
        $this->assertEquals('errorrequestnotfound', $warning['warningcode']);
    }

    /**
     * Test for external::cancel_data_request()
     */
    public function test_cancel_data_request() {
        // Login as the requester.
        $this->setUser($this->requester);

        $result = external::cancel_data_request($this->requestid);
        $return = (object) external_api::clean_returnvalue(external::approve_data_request_returns(), $result);
        $this->assertTrue($return->result);
        $this->assertEmpty($return->warnings);
    }

    /**
     * Test contact DPO.
     */
    public function test_contact_dpo() {
        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $this->setUser($user);

        $message = 'Hello world!';
        $result = external::contact_dpo($message);
        $return = (object) external_api::clean_returnvalue(external::contact_dpo_returns(), $result);
        $this->assertTrue($return->result);
        $this->assertEmpty($return->warnings);
    }

    /**
     * Test contact DPO with message containing invalid input.
     */
    public function test_contact_dpo_with_nasty_input() {
        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $this->setUser($user);

        $this->expectException('invalid_parameter_exception');
        external::contact_dpo('de<>\\..scription');
    }

    /**
     * Test for external::deny_data_request() with the user not logged in.
     */
    public function test_deny_data_request_not_logged_in() {
        $this->expectException(require_login_exception::class);
        external::deny_data_request($this->requestid);
    }

    /**
     * Test for external::deny_data_request() with the user not having a DPO role.
     */
    public function test_deny_data_request_not_dpo() {
        // Login as the requester.
        $this->setUser($this->requester->id);
        $this->expectException(required_capability_exception::class);
        external::deny_data_request($this->requestid);
    }

    /**
     * Test for external::deny_data_request() for request that's not ready for approval
     */
    public function test_deny_data_request_not_waiting_for_approval() {
        // Admin as DPO. (The default when no one's assigned as a DPO in the site).
        $this->setAdminUser();
        $this->expectException(moodle_exception::class);
        external::deny_data_request($this->requestid);
    }

    /**
     * Test for external::deny_data_request()
     */
    public function test_deny_data_request() {
        // Admin as DPO. (The default when no one's assigned as a DPO in the site).
        $this->setAdminUser();
        api::update_request_status($this->requestid, api::DATAREQUEST_STATUS_AWAITING_APPROVAL);
        $result = external::approve_data_request($this->requestid);
        $return = (object) external_api::clean_returnvalue(external::deny_data_request_returns(), $result);
        $this->assertTrue($return->result);
        $this->assertEmpty($return->warnings);
    }

    /**
     * Test for external::deny_data_request() for a non-existent request ID.
     */
    public function test_deny_data_request_non_existent() {
        // Admin as DPO. (The default when no one's assigned as a DPO in the site).
        $this->setAdminUser();
        api::update_request_status($this->requestid, api::DATAREQUEST_STATUS_AWAITING_APPROVAL);
        $result = external::deny_data_request($this->requestid + 1);
        $return = (object) external_api::clean_returnvalue(external::deny_data_request_returns(), $result);
        $this->assertFalse($return->result);
        $this->assertCount(1, $return->warnings);
        $warning = reset($return->warnings);
        $this->assertEquals('errorrequestnotfound', $warning['warningcode']);
    }

    /**
     * Test for external::get_data_request() with the user not logged in.
     */
    public function test_get_data_request_not_logged_in() {
        $this->expectException(require_login_exception::class);
        external::get_data_request($this->requestid);
    }

    /**
     * Test for external::get_data_request() with the user not having a DPO role.
     */
    public function test_get_data_request_not_dpo() {
        $generator = $this->getDataGenerator();
        $otheruser = $generator->create_user();
        // Login as the requester.
        $this->setUser($otheruser);
        $this->expectException(required_capability_exception::class);
        external::get_data_request($this->requestid);
    }

    /**
     * Test for external::get_data_request()
     */
    public function test_get_data_request() {
        // Admin as DPO. (The default when no one's assigned as a DPO in the site).
        $this->setAdminUser();
        $result = external::get_data_request($this->requestid);
        $return = (object) external_api::clean_returnvalue(external::get_data_request_returns(), $result);
        $this->assertEquals(api::DATAREQUEST_TYPE_EXPORT, $return->result['type']);
        $this->assertEquals('sample comment', $return->result['comments']);
        $this->assertEquals($this->requester->id, $return->result['userid']);
        $this->assertEquals($this->requester->id, $return->result['requestedby']);
        $this->assertEmpty($return->warnings);
    }

    /**
     * Test for external::get_data_request() for a non-existent request ID.
     */
    public function test_get_data_request_non_existent() {
        // Admin as DPO. (The default when no one's assigned as a DPO in the site).
        $this->setAdminUser();
        $this->expectException(dml_missing_record_exception::class);
        external::get_data_request($this->requestid + 1);
    }
}

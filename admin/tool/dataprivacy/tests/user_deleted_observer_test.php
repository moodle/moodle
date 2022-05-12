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

use tool_dataprivacy\event\user_deleted_observer;

/**
 * Event observer test.
 *
 * @package    tool_dataprivacy
 * @copyright  2018 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_deleted_observer_test extends \advanced_testcase {

    /**
     * Ensure that a delete data request is created upon user deletion.
     */
    public function test_create_delete_data_request() {
        $this->resetAfterTest();

        // Enable automatic creation of delete data requests.
        set_config('automaticdeletionrequests', 1, 'tool_dataprivacy');

        // Create another user who is not a DPO.
        $user = $this->getDataGenerator()->create_user();

        $event = $this->trigger_delete_user_event($user);

        user_deleted_observer::create_delete_data_request($event);
        // Validate that delete data request has been created.
        $this->assertTrue(api::has_ongoing_request($user->id, api::DATAREQUEST_TYPE_DELETE));
    }

    /**
     * Ensure that a delete data request is not created upon user deletion if automatic creation of
     * delete data requests is disabled.
     */
    public function test_create_delete_data_request_automatic_creation_disabled() {
        $this->resetAfterTest();

        // Disable automatic creation of delete data requests.
        set_config('automaticdeletionrequests', 0, 'tool_dataprivacy');

        // Create another user who is not a DPO.
        $user = $this->getDataGenerator()->create_user();

        $event = $this->trigger_delete_user_event($user);

        user_deleted_observer::create_delete_data_request($event);
        // Validate that delete data request has been created.
        $this->assertFalse(api::has_ongoing_request($user->id, api::DATAREQUEST_TYPE_DELETE));
    }

    /**
     * Ensure that a delete data request is being created upon user deletion
     * if an ongoing export data request (or any other except delete data request) for that user already exists.
     */
    public function test_create_delete_data_request_export_data_request_preexists() {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Enable automatic creation of delete data requests.
        set_config('automaticdeletionrequests', 1, 'tool_dataprivacy');

        // Create another user who is not a DPO.
        $user = $this->getDataGenerator()->create_user();
        // Create a delete data request for $user.
        api::create_data_request($user->id, api::DATAREQUEST_TYPE_EXPORT);
        // Validate that delete data request has been created.
        $this->assertTrue(api::has_ongoing_request($user->id, api::DATAREQUEST_TYPE_EXPORT));
        $this->assertEquals(0, api::get_data_requests_count($user->id, [], [api::DATAREQUEST_TYPE_DELETE]));

        $event = $this->trigger_delete_user_event($user);

        user_deleted_observer::create_delete_data_request($event);
        // Validate that delete data request has been created.
        $this->assertEquals(1, api::get_data_requests_count($user->id, [], [api::DATAREQUEST_TYPE_DELETE]));
    }

    /**
     * Ensure that a delete data request is not being created upon user deletion
     * if an ongoing delete data request for that user already exists.
     */
    public function test_create_delete_data_request_ongoing_delete_data_request_preexists() {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Enable automatic creation of delete data requests.
        set_config('automaticdeletionrequests', 1, 'tool_dataprivacy');

        // Create another user who is not a DPO.
        $user = $this->getDataGenerator()->create_user();
        // Create a delete data request for $user.
        api::create_data_request($user->id, api::DATAREQUEST_TYPE_DELETE);
        // Validate that delete data request has been created.
        $this->assertTrue(api::has_ongoing_request($user->id, api::DATAREQUEST_TYPE_DELETE));

        $event = $this->trigger_delete_user_event($user);

        user_deleted_observer::create_delete_data_request($event);
        // Validate that additional delete data request has not been created.
        $this->assertEquals(1, api::get_data_requests_count($user->id, [], [api::DATAREQUEST_TYPE_DELETE]));
    }

    /**
     * Ensure that a delete data request is being created upon user deletion
     * if a finished delete data request (excluding complete) for that user already exists.
     */
    public function test_create_delete_data_request_canceled_delete_data_request_preexists() {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Enable automatic creation of delete data requests.
        set_config('automaticdeletionrequests', 1, 'tool_dataprivacy');

        // Create another user who is not a DPO.
        $user = $this->getDataGenerator()->create_user();
        // Create a delete data request for $user.
        $datarequest = api::create_data_request($user->id, api::DATAREQUEST_TYPE_DELETE);
        $requestid = $datarequest->get('id');
        api::update_request_status($requestid, api::DATAREQUEST_STATUS_CANCELLED);

        // Validate that delete data request has been created and the status has been updated to 'Canceled'.
        $this->assertEquals(1, api::get_data_requests_count($user->id, [], [api::DATAREQUEST_TYPE_DELETE]));
        $this->assertFalse(api::has_ongoing_request($user->id, api::DATAREQUEST_TYPE_DELETE));

        $event = $this->trigger_delete_user_event($user);

        user_deleted_observer::create_delete_data_request($event);
        // Validate that additional delete data request has been created.
        $this->assertEquals(2, api::get_data_requests_count($user->id, [], [api::DATAREQUEST_TYPE_DELETE]));
        $this->assertTrue(api::has_ongoing_request($user->id, api::DATAREQUEST_TYPE_DELETE));
    }

    /**
     * Ensure that a delete data request is being created upon user deletion
     * if a completed delete data request for that user already exists.
     */
    public function test_create_delete_data_request_completed_delete_data_request_preexists() {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Enable automatic creation of delete data requests.
        set_config('automaticdeletionrequests', 1, 'tool_dataprivacy');

        // Create another user who is not a DPO.
        $user = $this->getDataGenerator()->create_user();
        // Create a delete data request for $user.
        $datarequest = api::create_data_request($user->id, api::DATAREQUEST_TYPE_DELETE);
        $requestid = $datarequest->get('id');
        api::update_request_status($requestid, api::DATAREQUEST_STATUS_COMPLETE);

        // Validate that delete data request has been created and the status has been updated to 'Completed'.
        $this->assertEquals(1, api::get_data_requests_count($user->id, [], [api::DATAREQUEST_TYPE_DELETE]));
        $this->assertFalse(api::has_ongoing_request($user->id, api::DATAREQUEST_TYPE_DELETE));

        $event = $this->trigger_delete_user_event($user);

        user_deleted_observer::create_delete_data_request($event);
        // Validate that additional delete data request has not been created.
        $this->assertEquals(1, api::get_data_requests_count($user->id, [], [api::DATAREQUEST_TYPE_DELETE]));
        $this->assertFalse(api::has_ongoing_request($user->id, api::DATAREQUEST_TYPE_DELETE));
    }

    /**
     * Helper to trigger and capture the delete user event.
     *
     * @param object $user The user object.
     * @return \core\event\user_deleted $event The returned event.
     */
    private function trigger_delete_user_event($user) {

        $sink = $this->redirectEvents();
        delete_user($user);
        $events = $sink->get_events();
        $sink->close();
        $event = reset($events);
        // Validate event data.
        $this->assertInstanceOf('\core\event\user_deleted', $event);

        return $event;
    }
}

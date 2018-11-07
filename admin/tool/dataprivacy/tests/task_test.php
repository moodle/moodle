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
 * Tests for scheduled tasks.
 *
 * @package    tool_dataprivacy
 * @copyright  2018 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once('data_privacy_testcase.php');

use tool_dataprivacy\api;

/**
 * Tests for scheduled tasks.
 *
 * @package    tool_dataprivacy
 * @copyright  2018 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_dataprivacy_task_testcase extends data_privacy_testcase {

    /**
     * Test tearDown.
     */
    public function tearDown() {
        \core_privacy\local\request\writer::reset();
    }

    /**
     * Ensure that a delete data request for pre-existing deleted users
     * is created when there are not any existing data requests
     * for that particular user.
     */
    public function test_delete_existing_deleted_users_task_no_previous_requests() {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Enable automatic creation of delete data requests.
        set_config('automaticdeletionrequests', 1, 'tool_dataprivacy');

        // Create a user.
        $user = $this->getDataGenerator()->create_user();
        // Mark the user as deleted.
        $user->deleted = 1;
        $DB->update_record('user', $user);

        // The user should not have a delete data request.
        $this->assertCount(0, api::get_data_requests($user->id, [],
                [api::DATAREQUEST_TYPE_DELETE]));

        $this->execute_task('tool_dataprivacy\task\delete_existing_deleted_users');
        // After running the scheduled task, the deleted user should have a delete data request.
        $this->assertCount(1, api::get_data_requests($user->id, [],
                [api::DATAREQUEST_TYPE_DELETE]));
    }

    /**
     * Ensure that a delete data request for pre-existing deleted users
     * is not being created when automatic creation of delete data requests is disabled.
     */
    public function test_delete_existing_deleted_users_task_automatic_creation_disabled() {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Disable automatic creation of delete data requests.
        set_config('automaticdeletionrequests', 0, 'tool_dataprivacy');

        // Create a user.
        $user = $this->getDataGenerator()->create_user();
        // Mark the user as deleted.
        $user->deleted = 1;
        $DB->update_record('user', $user);

        // The user should not have a delete data request.
        $this->assertCount(0, api::get_data_requests($user->id, [],
            [api::DATAREQUEST_TYPE_DELETE]));

        $this->execute_task('tool_dataprivacy\task\delete_existing_deleted_users');
        // After running the scheduled task, the deleted user should still not have a delete data request.
        $this->assertCount(0, api::get_data_requests($user->id, [],
            [api::DATAREQUEST_TYPE_DELETE]));
    }

    /**
     * Ensure that a delete data request for pre-existing deleted users
     * is created when there are existing non-delete data requests
     * for that particular user.
     */
    public function test_delete_existing_deleted_users_task_existing_export_data_requests() {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Enable automatic creation of delete data requests.
        set_config('automaticdeletionrequests', 1, 'tool_dataprivacy');

        // Create a user.
        $user = $this->getDataGenerator()->create_user();
        // Create export data request for the user.
        api::create_data_request($user->id, api::DATAREQUEST_TYPE_EXPORT);
        // Mark the user as deleted.
        $user->deleted = 1;
        $DB->update_record('user', $user);

        // The user should have a export data request.
        $this->assertCount(1, api::get_data_requests($user->id, [],
                [api::DATAREQUEST_TYPE_EXPORT]));
        // The user should not have a delete data request.
        $this->assertCount(0, api::get_data_requests($user->id, [],
                [api::DATAREQUEST_TYPE_DELETE]));

        $this->execute_task('tool_dataprivacy\task\delete_existing_deleted_users');
        // After running the scheduled task, the deleted user should have a delete data request.
        $this->assertCount(1, api::get_data_requests($user->id, [],
                [api::DATAREQUEST_TYPE_DELETE]));
    }

    /**
     * Ensure that a delete data request for pre-existing deleted users
     * is not created when there are existing ongoing delete data requests
     * for that particular user.
     */
    public function test_delete_existing_deleted_users_task_existing_ongoing_delete_data_requests() {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Enable automatic creation of delete data requests.
        set_config('automaticdeletionrequests', 1, 'tool_dataprivacy');

        // Create a user.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);
        // Create delete data request for the user.
        $datarequest = api::create_data_request($user->id, api::DATAREQUEST_TYPE_DELETE);
        $requestid = $datarequest->get('id');
        api::update_request_status($requestid, api::DATAREQUEST_STATUS_AWAITING_APPROVAL);

        // The user should have an ongoing delete data request.
        $this->assertCount(1, api::get_data_requests($user->id,
                [api::DATAREQUEST_STATUS_AWAITING_APPROVAL], [api::DATAREQUEST_TYPE_DELETE]));

        // Mark the user as deleted.
        $user->deleted = 1;
        $DB->update_record('user', $user);

        // The user should still have the existing ongoing delete data request.
        $this->assertCount(1, \tool_dataprivacy\api::get_data_requests($user->id,
                [api::DATAREQUEST_STATUS_AWAITING_APPROVAL], [api::DATAREQUEST_TYPE_DELETE]));

        $this->execute_task('tool_dataprivacy\task\delete_existing_deleted_users');
        // After running the scheduled task, the user should have only one delete data request.
        $this->assertCount(1, api::get_data_requests($user->id, [],
                [api::DATAREQUEST_TYPE_DELETE]));
    }

    /**
     * Ensure that a delete data request for pre-existing deleted users
     * is not created when there are existing finished delete data requests
     * for that particular user.
     */
    public function test_delete_existing_deleted_users_task_existing_finished_delete_data_requests() {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Enable automatic creation of delete data requests.
        set_config('automaticdeletionrequests', 1, 'tool_dataprivacy');

        // Create a user.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);
        // Create delete data request for the user.
        $datarequest = api::create_data_request($user->id, api::DATAREQUEST_TYPE_DELETE);
        $requestid = $datarequest->get('id');
        api::update_request_status($requestid, api::DATAREQUEST_STATUS_CANCELLED);

        // The user should have a delete data request.
        $this->assertCount(1, api::get_data_requests($user->id, [],
                [api::DATAREQUEST_TYPE_DELETE]));
        // The user should not have an ongoing data requests.
        $this->assertFalse(api::has_ongoing_request($user->id, api::DATAREQUEST_TYPE_DELETE));

        // Mark the user as deleted.
        $user->deleted = 1;
        $DB->update_record('user', $user);

        // The user should still have the existing cancelled delete data request.
        $this->assertCount(1, \tool_dataprivacy\api::get_data_requests($user->id,
                [api::DATAREQUEST_STATUS_CANCELLED], [api::DATAREQUEST_TYPE_DELETE]));

        $this->execute_task('tool_dataprivacy\task\delete_existing_deleted_users');
        // After running the scheduled task, the user should still have one delete data requests.
        $this->assertCount(1, api::get_data_requests($user->id, [],
                [api::DATAREQUEST_TYPE_DELETE]));
        // The user should only have the existing cancelled delete data request.
        $this->assertCount(1, \tool_dataprivacy\api::get_data_requests($user->id,
                [api::DATAREQUEST_STATUS_CANCELLED], [api::DATAREQUEST_TYPE_DELETE]));
    }

    /**
     * Helper to execute a particular task.
     *
     * @param string $task The task.
     */
    private function execute_task($task) {
        // Run the scheduled task.
        ob_start();
        $task = \core\task\manager::get_scheduled_task($task);
        $task->execute();
        ob_end_clean();
    }
}

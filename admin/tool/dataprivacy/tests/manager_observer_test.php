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
 * Tests for the manager observer.
 *
 * @package    tool_dataprivacy
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * API tests.
 *
 * @package    tool_dataprivacy
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_dataprivacy_manager_observer_testcase extends advanced_testcase {

    /**
     * Helper to set andn return two users who are DPOs.
     */
    protected function setup_site_dpos() {
        global $DB;
        $this->resetAfterTest();

        $generator = new testing_data_generator();
        $u1 = $this->getDataGenerator()->create_user();
        $u2 = $this->getDataGenerator()->create_user();

        $context = context_system::instance();

        // Give the manager role with the capability to manage data requests.
        $managerroleid = $DB->get_field('role', 'id', array('shortname' => 'manager'));
        assign_capability('tool/dataprivacy:managedatarequests', CAP_ALLOW, $managerroleid, $context->id, true);

        // Assign both users as manager.
        role_assign($managerroleid, $u1->id, $context->id);
        role_assign($managerroleid, $u2->id, $context->id);

        // Only map the manager role to the DPO role.
        set_config('dporoles', $managerroleid, 'tool_dataprivacy');

        return \tool_dataprivacy\api::get_site_dpos();
    }

    /**
     * Ensure that when users are configured as DPO, they are sent an message upon failure.
     */
    public function test_handle_component_failure() {
        $this->resetAfterTest();

        // Create another user who is not a DPO.
        $this->getDataGenerator()->create_user();

        // Create the DPOs.
        $dpos = $this->setup_site_dpos();

        $observer = new \tool_dataprivacy\manager_observer();

        // Handle the failure, catching messages.
        $mailsink = $this->redirectMessages();
        $mailsink->clear();
        $observer->handle_component_failure(new \Exception('error'), 'foo', 'bar', 'baz', ['foobarbaz', 'bum']);

        // Messages should be sent to both DPOs only.
        $this->assertEquals(2, $mailsink->count());

        $messages = $mailsink->get_messages();
        $messageusers = array_map(function($message) {
            return $message->useridto;
        }, $messages);

        $this->assertEquals(array_keys($dpos), $messageusers, '', 0.0, 0, true);
    }

    /**
     * Ensure that when no user is configured as DPO, the message is sent to admin instead.
     */
    public function test_handle_component_failure_no_dpo() {
        $this->resetAfterTest();

        // Create another user who is not a DPO or admin.
        $this->getDataGenerator()->create_user();

        $observer = new \tool_dataprivacy\manager_observer();

        $mailsink = $this->redirectMessages();
        $mailsink->clear();
        $observer->handle_component_failure(new \Exception('error'), 'foo', 'bar', 'baz', ['foobarbaz', 'bum']);

        // Messages should have been sent only to the admin.
        $this->assertEquals(1, $mailsink->count());

        $messages = $mailsink->get_messages();
        $message = reset($messages);

        $admin = \core_user::get_user_by_username('admin');
        $this->assertEquals($admin->id, $message->useridto);
    }
}

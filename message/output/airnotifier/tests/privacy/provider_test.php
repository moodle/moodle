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
 * Base class for unit tests for message_airnotifier.
 *
 * @package    message_airnotifier
 * @copyright  2018 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace message_airnotifier\privacy;

defined('MOODLE_INTERNAL') || die();

use core_privacy\tests\provider_testcase;
use message_airnotifier\privacy\provider;
use core_privacy\local\request\approved_userlist;

/**
 * Unit tests for message\output\airnotifier\classes\privacy\provider.php
 *
 * @copyright  2018 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class provider_test extends provider_testcase {

    /**
     * Basic setup for these tests.
     */
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest(true);
    }

    /**
     * /
     * @param object $user User object
     * @param string $pushid unique string
     */
    protected function add_device($user, $pushid) {
        global $DB;

        // Add fake core device.
        $device = array(
            'appid' => 'com.moodle.moodlemobile',
            'name' => 'occam',
            'model' => 'Nexus 4',
            'platform' => 'Android',
            'version' => '4.2.2',
            'pushid' => $pushid,
            'uuid' => 'asdnfl348qlksfaasef859',
            'userid' => $user->id,
            'timecreated' => time(),
            'timemodified' => time(),
        );
        $coredeviceid = $DB->insert_record('user_devices', (object) $device);

        $airnotifierdev = array(
            'userdeviceid' => $coredeviceid,
            'enable' => 1
        );
        $airnotifierdevid = $DB->insert_record('message_airnotifier_devices', (object) $airnotifierdev);
    }

    /**
     * Test returning metadata.
     */
    public function test_get_metadata(): void {
        $collection = new \core_privacy\local\metadata\collection('message_airnotifier');
        $collection = \message_airnotifier\privacy\provider::get_metadata($collection);
        $this->assertNotEmpty($collection);
    }

    /**
     * Test getting the context for the user ID related to this plugin.
     */
    public function test_get_contexts_for_userid(): void {

        $user = $this->getDataGenerator()->create_user();
        $context = \context_user::instance($user->id);

        $this->add_device($user, 'apuJih874kj');
        $this->add_device($user, 'bdu09Ikjjsu');

        $contextlist = \message_airnotifier\privacy\provider::get_contexts_for_userid($user->id);
        $this->assertEquals($context->id, $contextlist->current()->id);
    }

    /**
     * Test that data is exported correctly for this plugin.
     */
    public function test_export_user_data(): void {
        $user = $this->getDataGenerator()->create_user();
        $context = \context_user::instance($user->id);

        $this->add_device($user, 'apuJih874kj');
        $this->add_device($user, 'bdu09Ikjjsu');

        $writer = \core_privacy\local\request\writer::with_context($context);
        $this->assertFalse($writer->has_any_data());
        $this->export_context_data_for_user($user->id, $context, 'message_airnotifier');

        // First device.
        $data = $writer->get_data([get_string('privacy:subcontext', 'message_airnotifier'), 'Nexus 4_apuJih874kj']);
        $this->assertEquals('com.moodle.moodlemobile', $data->appid);

        // Second device.
        $data = $writer->get_data([get_string('privacy:subcontext', 'message_airnotifier'), 'Nexus 4_bdu09Ikjjsu']);
        $this->assertEquals('bdu09Ikjjsu', $data->pushid);
    }

    /**
     * Test that user data is deleted using the context.
     */
    public function test_delete_data_for_all_users_in_context(): void {
        global $DB;

        $user = $this->getDataGenerator()->create_user();
        $context = \context_user::instance($user->id);

        $this->add_device($user, 'apuJih874kj');

        // Check that we have an entry.
        $devices = $DB->get_records('message_airnotifier_devices');
        $this->assertCount(1, $devices);

        \message_airnotifier\privacy\provider::delete_data_for_all_users_in_context($context);

        // Check that it has now been deleted.
        $devices = $DB->get_records('message_airnotifier_devices');
        $this->assertCount(0, $devices);
    }

    /**
     * Test that user data is deleted for this user.
     */
    public function test_delete_data_for_user(): void {
        global $DB;

        $user = $this->getDataGenerator()->create_user();
        $context = \context_user::instance($user->id);

        $this->add_device($user, 'apuJih874kj');

        // Check that we have an entry.
        $devices = $DB->get_records('message_airnotifier_devices');
        $this->assertCount(1, $devices);

        $approvedlist = new \core_privacy\local\request\approved_contextlist($user, 'message_airnotifier', [$context->id]);
        \message_airnotifier\privacy\provider::delete_data_for_user($approvedlist);

        // Check that it has now been deleted.
        $devices = $DB->get_records('message_airnotifier_devices');
        $this->assertCount(0, $devices);
    }

    /**
     * Test that only users with a user context are fetched.
     */
    public function test_get_users_in_context(): void {
        $component = 'message_airnotifier';

        // Create user.
        $user = $this->getDataGenerator()->create_user();
        $usercontext = \context_user::instance($user->id);

        // The lists of users for the user context should be empty.
        // Related user data have not been created yet.
        $userlist = new \core_privacy\local\request\userlist($usercontext, $component);
        provider::get_users_in_context($userlist);
        $this->assertCount(0, $userlist);

        $this->add_device($user, 'apuJih874kj');
        $this->add_device($user, 'bdu09Ikjjsu');

        // The list of users for userlist should return one user (user).
        provider::get_users_in_context($userlist);
        $this->assertCount(1, $userlist);
        $expected = [$user->id];
        $actual = $userlist->get_userids();
        $this->assertEquals($expected, $actual);

        // The list of users should only return users in the user context.
        $systemcontext = \context_system::instance();
        $userlist1 = new \core_privacy\local\request\userlist($systemcontext, $component);
        provider::get_users_in_context($userlist1);
        $this->assertCount(0, $userlist1);
    }

    /**
     * Test that data for users in approved userlist is deleted.
     */
    public function test_delete_data_for_users(): void {
        $component = 'message_airnotifier';

        // Create user1.
        $user1 = $this->getDataGenerator()->create_user();
        $usercontext1 = \context_user::instance($user1->id);
        // Create user2.
        $user2 = $this->getDataGenerator()->create_user();
        $usercontext2 = \context_user::instance($user2->id);

        $this->add_device($user1, 'apuJih874kj');
        $this->add_device($user1, 'cpuJih874kp');
        $this->add_device($user2, 'bdu09Ikjjsu');

        // The list of users for usercontext1 should return one user (user1).
        $userlist1 = new \core_privacy\local\request\userlist($usercontext1, $component);
        provider::get_users_in_context($userlist1);
        $this->assertCount(1, $userlist1);

        // The list of users for usercontext2 should return one user (user2).
        $userlist2 = new \core_privacy\local\request\userlist($usercontext2, $component);
        provider::get_users_in_context($userlist2);
        $this->assertCount(1, $userlist2);

        $approvedlist = new approved_userlist($usercontext1, $component, $userlist1->get_userids());
        // Delete using delete_data_for_user.
        provider::delete_data_for_users($approvedlist);

        // Re-fetch users in usercontext1 - the user data should now be empty.
        $userlist1 = new \core_privacy\local\request\userlist($usercontext1, $component);
        provider::get_users_in_context($userlist1);
        $this->assertCount(0, $userlist1);

        // The list of users for usercontext2 should still return one user (user2).
        $userlist2 = new \core_privacy\local\request\userlist($usercontext2, $component);
        provider::get_users_in_context($userlist2);
        $this->assertCount(1, $userlist2);

        // User data should only be removed in the user context.
        $systemcontext = \context_system::instance();
        $approvedlist = new approved_userlist($systemcontext, $component, $userlist2->get_userids());
        // Delete using delete_data_for_user.
        provider::delete_data_for_users($approvedlist);
        // Re-fetch users in usercontext2 - the user data should still be present.
        $userlist2 = new \core_privacy\local\request\userlist($usercontext2, $component);
        provider::get_users_in_context($userlist2);
        $this->assertCount(1, $userlist2);
    }
}

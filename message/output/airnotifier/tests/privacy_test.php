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

defined('MOODLE_INTERNAL') || die();

use \core_privacy\tests\provider_testcase;
/**
 * Unit tests for message\output\airnotifier\classes\privacy\provider.php
 *
 * @copyright  2018 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class message_airnotifier_testcase extends provider_testcase {

    /**
     * Basic setup for these tests.
     */
    public function setUp() {
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
    public function test_get_metadata() {
        $collection = new \core_privacy\local\metadata\collection('message_airnotifier');
        $collection = \message_airnotifier\privacy\provider::get_metadata($collection);
        $this->assertNotEmpty($collection);
    }

    /**
     * Test getting the context for the user ID related to this plugin.
     */
    public function test_get_contexts_for_userid() {

        $user = $this->getDataGenerator()->create_user();
        $context = context_user::instance($user->id);

        $this->add_device($user, 'apuJih874kj');
        $this->add_device($user, 'bdu09Ikjjsu');

        $contextlist = \message_airnotifier\privacy\provider::get_contexts_for_userid($user->id);
        $this->assertEquals($context->id, $contextlist->current()->id);
    }

    /**
     * Test that data is exported correctly for this plugin.
     */
    public function test_export_user_data() {
        $user = $this->getDataGenerator()->create_user();
        $context = context_user::instance($user->id);

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
    public function test_delete_data_for_all_users_in_context() {
        global $DB;

        $user = $this->getDataGenerator()->create_user();
        $context = context_user::instance($user->id);

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
    public function test_delete_data_for_user() {
        global $DB;

        $user = $this->getDataGenerator()->create_user();
        $context = context_user::instance($user->id);

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
}

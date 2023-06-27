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
 * External airnotifier functions unit tests
 *
 * @package    message_airnotifier
 * @category   external
 * @copyright  2012 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');

/**
 * External airnotifier functions unit tests
 *
 * @package    message_airnotifier
 * @category   external
 * @copyright  2012 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class message_airnotifier_external_testcase extends externallib_advanced_testcase {

    /**
     * Tests set up
     */
    protected function setUp(): void {
        global $CFG;
        require_once($CFG->dirroot . '/message/output/airnotifier/externallib.php');
    }

    /**
     * Test is_system_configured
     */
    public function test_is_system_configured() {
        global $DB;

        $this->resetAfterTest(true);

        $user  = self::getDataGenerator()->create_user();
        self::setUser($user);

        // In a clean installation, it should be not configured.
        $configured = message_airnotifier_external::is_system_configured();
        $configured = external_api::clean_returnvalue(message_airnotifier_external::is_system_configured_returns(), $configured);
        $this->assertEquals(0, $configured);

        // Fake configuration.
        set_config('airnotifieraccesskey', random_string());
        // Enable the plugin.
        $DB->set_field('message_processors', 'enabled', 1, array('name' => 'airnotifier'));

        $configured = message_airnotifier_external::is_system_configured();
        $configured = external_api::clean_returnvalue(message_airnotifier_external::is_system_configured_returns(), $configured);
        $this->assertEquals(1, $configured);
    }

    /**
     * Test are_notification_preferences_configured
     */
    public function test_are_notification_preferences_configured() {

        $this->resetAfterTest(true);

        $user1  = self::getDataGenerator()->create_user();
        $user2  = self::getDataGenerator()->create_user();
        $user3  = self::getDataGenerator()->create_user();

        self::setUser($user1);

        set_user_preference('message_provider_moodle_instantmessage_loggedin', 'airnotifier', $user1);
        set_user_preference('message_provider_moodle_instantmessage_loggedoff', 'airnotifier', $user1);
        set_user_preference('message_provider_moodle_instantmessage_loggedin', 'airnotifier', $user2);
        set_user_preference('message_provider_moodle_instantmessage_loggedin', 'airnotifier', $user3);

        $params = array($user1->id, $user2->id, $user3->id);

        $preferences = message_airnotifier_external::are_notification_preferences_configured($params);
        $returnsdescription = message_airnotifier_external::are_notification_preferences_configured_returns();
        $preferences = external_api::clean_returnvalue($returnsdescription, $preferences);

        $expected = array(
            array(
                'userid' => $user1->id,
                'configured' => 1
            )
        );

        $this->assertEquals(1, count($preferences['users']));
        $this->assertEquals($expected, $preferences['users']);
        $this->assertEquals(2, count($preferences['warnings']));

        // Now, remove one user.
        delete_user($user2);
        $preferences = message_airnotifier_external::are_notification_preferences_configured($params);
        $preferences = external_api::clean_returnvalue($returnsdescription, $preferences);
        $this->assertEquals(1, count($preferences['users']));
        $this->assertEquals($expected, $preferences['users']);
        $this->assertEquals(2, count($preferences['warnings']));

        // Now, remove one user1 preference (the user still has one prefernce for airnotifier).
        unset_user_preference('message_provider_moodle_instantmessage_loggedin', $user1);
        $preferences = message_airnotifier_external::are_notification_preferences_configured($params);
        $preferences = external_api::clean_returnvalue($returnsdescription, $preferences);
        $this->assertEquals($expected, $preferences['users']);

        // Delete the last user1 preference.
        unset_user_preference('message_provider_moodle_instantmessage_loggedoff', $user1);
        $preferences = message_airnotifier_external::are_notification_preferences_configured($params);
        $preferences = external_api::clean_returnvalue($returnsdescription, $preferences);
        $expected = array(
            array(
                'userid' => $user1->id,
                'configured' => 1
            )
        );
        $this->assertEquals($expected, $preferences['users']);
    }

    /**
     * Test get_user_devices
     */
    public function test_get_user_devices() {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/user/externallib.php');

        $this->resetAfterTest(true);
        $this->setAdminUser();

        // System not configured.
        $devices = message_airnotifier_external::get_user_devices('');
        $devices = external_api::clean_returnvalue(message_airnotifier_external::get_user_devices_returns(), $devices);
        $this->assertCount(1, $devices['warnings']);
        $this->assertEquals('systemnotconfigured', $devices['warnings'][0]['warningcode']);

        // Fake configuration.
        set_config('airnotifieraccesskey', random_string());
        // Enable the plugin.
        $DB->set_field('message_processors', 'enabled', 1, array('name' => 'airnotifier'));

        // Get devices.
        $devices = message_airnotifier_external::get_user_devices('');
        $devices = external_api::clean_returnvalue(message_airnotifier_external::get_user_devices_returns(), $devices);
        $this->assertCount(0, $devices['warnings']);
        // No devices, unfortunatelly we cannot create devices (we can't mock airnotifier server).
        $this->assertCount(0, $devices['devices']);
    }

    /**
     * Test get_user_devices permissions
     */
    public function test_get_user_devices_permissions() {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/user/externallib.php');

        $this->resetAfterTest(true);
        $user  = self::getDataGenerator()->create_user();
        $otheruser  = self::getDataGenerator()->create_user();
        self::setUser($user);

        // No permission to get other users devices.
        $this->expectException('required_capability_exception');
        $devices = message_airnotifier_external::get_user_devices('', $otheruser->id);
    }

    /**
     * Test enable_device
     */
    public function test_enable_device() {
        global $USER, $DB;

        $this->resetAfterTest(true);
        $this->setAdminUser();

        // Add fake core device.
        $device = array(
            'appid' => 'com.moodle.moodlemobile',
            'name' => 'occam',
            'model' => 'Nexus 4',
            'platform' => 'Android',
            'version' => '4.2.2',
            'pushid' => 'apushdkasdfj4835',
            'uuid' => 'asdnfl348qlksfaasef859',
            'userid' => $USER->id,
            'timecreated' => time(),
            'timemodified' => time(),
        );
        $coredeviceid = $DB->insert_record('user_devices', (object) $device);

        $airnotifierdev = array(
            'userdeviceid' => $coredeviceid,
            'enable' => 1
        );
        $airnotifierdevid = $DB->insert_record('message_airnotifier_devices', (object) $airnotifierdev);

        // Disable and enable.
        $result = message_airnotifier_external::enable_device($airnotifierdevid, false);
        $result = external_api::clean_returnvalue(message_airnotifier_external::enable_device_returns(), $result);
        $this->assertCount(0, $result['warnings']);
        $this->assertTrue($result['success']);
        $this->assertEquals(0, $DB->get_field('message_airnotifier_devices', 'enable', array('id' => $airnotifierdevid)));

        $result = message_airnotifier_external::enable_device($airnotifierdevid, true);
        $result = external_api::clean_returnvalue(message_airnotifier_external::enable_device_returns(), $result);
        $this->assertCount(0, $result['warnings']);
        $this->assertTrue($result['success']);
        $this->assertEquals(1, $DB->get_field('message_airnotifier_devices', 'enable', array('id' => $airnotifierdevid)));
    }
}

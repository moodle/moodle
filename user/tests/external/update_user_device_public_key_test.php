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

namespace core_user\external;

use core_external\external_api;
use stdClass;

/**
 * Tests for the devicekey class.
 *
 * @package core_user
 * @covers \core_user\external\update_user_device_public_key
 */
class update_user_device_public_key_test extends \advanced_testcase {
    /**
     * Helper to create a device record.
     *
     * @return stdClass
     */
    protected function create_device_record(): stdClass {
        global $USER, $DB;

        $device = (object) [
            'appid' => 'com.moodle.moodlemobile',
            'name' => 'occam',
            'model' => 'Nexus 4',
            'platform' => 'Android',
            'version' => '4.2.2',
            'pushid' => 'apushdkasdfj4835',
            'uuid' => 'ABCDE3723ksdfhasfaasef859',
            'userid' => $USER->id,
            'timecreated' => time(),
            'timemodified' => time(),
        ];
        $device->id = $DB->insert_record('user_devices', $device);

        return $device;
    }

    public function test_execute(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $device = $this->create_device_record();

        $devicekeypair = sodium_crypto_box_keypair();
        $publickey = sodium_bin2base64(
            sodium_crypto_box_publickey($devicekeypair),
            SODIUM_BASE64_VARIANT_ORIGINAL
        );

        // Test sending a key to a valid device.
        $result = update_user_device_public_key::execute(
            $device->uuid,
            $device->appid,
            $publickey,
        );

        $result = external_api::clean_returnvalue(update_user_device_public_key::execute_returns(), $result);
        $this->assertTrue($result['status']);
        $this->assertEmpty($result['warnings']);
    }

    public function test_execute_with_invalid_device_appid(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $device = $this->create_device_record();

        $devicekeypair = sodium_crypto_box_keypair();
        $publickey = sodium_bin2base64(
            sodium_crypto_box_publickey($devicekeypair),
            SODIUM_BASE64_VARIANT_ORIGINAL
        );

        // Invalid appid.
        $result = update_user_device_public_key::execute(
            $device->uuid,
            'invalidappid',
            $publickey,
        );

        $result = external_api::clean_returnvalue(update_user_device_public_key::execute_returns(), $result);
        $this->assertFalse($result['status']);
        $this->assertNotEmpty($result['warnings']);
    }

    public function test_execute_with_invalid_device_uuid(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $device = $this->create_device_record();

        $devicekeypair = sodium_crypto_box_keypair();
        $publickey = sodium_bin2base64(
            sodium_crypto_box_publickey($devicekeypair),
            SODIUM_BASE64_VARIANT_ORIGINAL
        );

        // Invalid appid.
        $result = update_user_device_public_key::execute(
            'invaliduuid',
            $device->appid,
            $publickey,
        );

        $result = external_api::clean_returnvalue(update_user_device_public_key::execute_returns(), $result);
        $this->assertFalse($result['status']);
        $this->assertNotEmpty($result['warnings']);
    }
}

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
 * This file contains unit test related to xAPI library.
 *
 * @package    core_xapi
 * @copyright  2020 Ferran Recio
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_xapi\local\statement;

use advanced_testcase;
use core_xapi\xapi_exception;
use core_xapi\iri;

defined('MOODLE_INTERNAL') || die();

/**
 * Contains test cases for testing statement agent class.
 *
 * @package    core_xapi
 * @since      Moodle 3.9
 * @copyright  2020 Ferran Recio
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class item_agent_test extends advanced_testcase {

    /**
     * Test item creation.
     */
    public function test_create(): void {
        global $CFG;

        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();

        // Ceate using account.
        $data = (object) [
            'objectType' => 'Agent',
            'account' => (object) [
                'homePage' => $CFG->wwwroot,
                'name' => $user->id,
            ],
        ];
        $item = item_agent::create_from_data($data);

        $this->assertEquals(json_encode($item), json_encode($data));
        $itemuser = $item->get_user();
        $this->assertEquals($itemuser->id, $user->id);
        $itemusers = $item->get_all_users();
        $this->assertCount(1, $itemusers);

        // Ceate using mbox.
        $data = (object) [
            'objectType' => 'Agent',
            'mbox' => $user->email,
        ];
        $item = item_agent::create_from_data($data);

        $this->assertEquals(json_encode($item), json_encode($data));
        $itemuser = $item->get_user();
        $this->assertEquals($itemuser->id, $user->id);
        $itemusers = $item->get_all_users();
        $this->assertCount(1, $itemusers);
    }

    /**
     * Test item creation from Record.
     */
    public function test_create_from_user(): void {
        global $CFG;

        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();

        $item = item_agent::create_from_user($user);

        $itemuser = $item->get_user();
        $this->assertEquals($itemuser->id, $user->id);
        $itemusers = $item->get_all_users();
        $this->assertCount(1, $itemusers);

        // Check generated data.
        $data = $item->get_data();
        $this->assertEquals('Agent', $data->objectType);
        $this->assertEquals($CFG->wwwroot, $data->account->homePage);
        $this->assertEquals($user->id, $data->account->name);
    }

    /**
     * Test for invalid structures.
     *
     * @dataProvider invalid_data_provider
     * @param string $objecttype object type attribute
     * @param bool $validhome if valid homepage is user
     * @param bool $validid if valid group id is used
     */
    public function test_invalid_data(string $objecttype, bool $validhome, bool $validid): void {
        global $CFG;

        // Create one course with a group if necessary.
        $id = 'Wrong ID';
        if ($validid) {
            $this->resetAfterTest();
            $user = $this->getDataGenerator()->create_user();
            $id = $user->id;
        }

        $homepage = 'Invalid homepage!';
        if ($validhome) {
            $homepage = $CFG->wwwroot;
        }

        $data = (object) [
            'objectType' => $objecttype,
            'account' => (object) [
                'homePage' => $homepage,
                'name' => $id,
            ],
        ];

        $this->expectException(xapi_exception::class);
        $item = item_agent::create_from_data($data);
    }

    /**
     * Data provider for the test_invalid_data tests.
     *
     * @return  array
     */
    public static function invalid_data_provider(): array {
        return [
            'Wrong objecttype' => [
                'Invalid', true, true
            ],
            'Wrong homepage' => [
                'Agent', false, true
            ],
            'Wrong id' => [
                'Agent', true, false
            ],
        ];
    }

    /**
     * Test non supported account identifier xAPI formats.
     *
     * @dataProvider unspupported_create_provider
     * @param bool $usembox
     * @param bool $useaccount
     * @param bool $usesha1
     * @param bool $useopenid
     */
    public function test_unspupported_create(bool $usembox, bool $useaccount, bool $usesha1, bool $useopenid): void {
        global $CFG;

        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();

        // Ceate using both account and mbox.
        $data = (object) [
            'objectType' => 'Agent'
        ];

        if ($usembox) {
            $data->mbox = $user->email;
        }
        if ($useaccount) {
            $data->account = (object) [
                'homePage' => $CFG->wwwroot,
                'name' => $user->id,
            ];
        }
        if ($usesha1) {
            $data->mbox_sha1sum = sha1($user->email);
        }
        if ($useopenid) {
            // Note: this is not a real openid, it's just a value to test.
            $data->openid = 'https://www.moodle.openid.com/accounts/o8/id';
        }

        $this->expectException(xapi_exception::class);
        $item = item_agent::create_from_data($data);
    }

    /**
     * Data provider for the unsupported identifiers tests.
     *
     * @return  array
     */
    public static function unspupported_create_provider(): array {
        return [
            'Both mbox and account' => [
                true, true, false, false
            ],
            'Email SHA1' => [
                false, false, false, false
            ],
            'Open ID' => [
                false, false, false, false
            ],
        ];
    }

    /**
     * Test for missing object type.
     */
    public function test_missing_object_type(): void {
        $data = (object) ['id' => -1];
        $this->expectException(xapi_exception::class);
        $item = item_agent::create_from_data($data);
    }

    /**
     * Test for invalid user id.
     */
    public function test_inexistent_agent(): void {
        global $CFG;
        $data = (object) [
            'objectType' => 'Agent',
            'account' => (object) [
                'homePage' => $CFG->wwwroot,
                'name' => 0,
            ],
        ];
        $this->expectException(xapi_exception::class);
        $item = item_agent::create_from_data($data);
    }

    /**
     * Test for invalid agent record.
     */
    public function test_inexistent_agent_id(): void {
        $user = (object) ['name' => 'Me'];
        $this->expectException(xapi_exception::class);
        $item = item_agent::create_from_user($user);
    }
}

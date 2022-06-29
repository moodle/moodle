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
 * Contains test cases for testing statement group class.
 *
 * @package    core_xapi
 * @since      Moodle 3.9
 * @copyright  2020 Ferran Recio
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class item_group_test extends advanced_testcase {

    /**
     * Test item creation.
     */
    public function test_create(): void {
        global $CFG;

        $this->resetAfterTest();

        // Create one course with a group.
        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course->id);
        $user2 = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user2->id, $course->id);

        $group = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group->id, 'userid' => $user->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group->id, 'userid' => $user2->id));

        $data = (object) [
            'objectType' => 'Group',
            'account' => (object) [
                'homePage' => $CFG->wwwroot,
                'name' => $group->id,
            ],
        ];
        $item = item_group::create_from_data($data);

        $this->assertEquals(json_encode($item), json_encode($data));
        $itemgroup = $item->get_group();
        $this->assertEquals($itemgroup->id, $group->id);
        $itemusers = $item->get_all_users();
        $this->assertCount(2, $itemusers);

        // Get user in group item must throw an exception.
        $this->expectException(xapi_exception::class);
        $itemusers = $item->get_user();
    }

    /**
     * Test item creation from Record.
     */
    public function test_create_from_group(): void {
        global $CFG;

        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $group = $this->getDataGenerator()->create_group(array('courseid' => $course->id));

        $item = item_group::create_from_group($group);

        $itemgroup = $item->get_group();
        $this->assertEquals($itemgroup->id, $group->id);

        // Check generated data.
        $data = $item->get_data();
        $this->assertEquals('Group', $data->objectType);
        $this->assertEquals($CFG->wwwroot, $data->account->homePage);
        $this->assertEquals($group->id, $data->account->name);
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
            $course = $this->getDataGenerator()->create_course();
            $group = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
            $id = $group->id;
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
        $item = item_group::create_from_data($data);
    }

    /**
     * Data provider for the test_invalid_data tests.
     *
     * @return  array
     */
    public function invalid_data_provider() : array {
        return [
            'Wrong objecttype' => [
                'Invalid', true, true
            ],
            'Wrong homepage' => [
                'Group', false, true
            ],
            'Wrong id' => [
                'Group', true, false
            ],
        ];
    }

    /**
     * Test for missing object type.
     */
    public function test_missing_object_type(): void {
        $data = (object) ['id' => -1];
        $this->expectException(xapi_exception::class);
        $item = item_group::create_from_data($data);
    }

    /**
     * Test for invalid anonymous group.
     */
    public function test_invalid_anonymous_group(): void {
        $data = (object) [
            'objectType' => 'Group'
        ];
        $this->expectException(xapi_exception::class);
        $item = item_group::create_from_data($data);
    }

    /**
     * Test for invalid anonymous group.
     */
    public function test_inexistent_group(): void {
        global $CFG;
        $data = (object) [
            'objectType' => 'Group',
            'account' => (object) [
                'homePage' => $CFG->wwwroot,
                'name' => 0,
            ],
        ];
        $this->expectException(xapi_exception::class);
        $item = item_group::create_from_data($data);
    }

    /**
     * Test for invalid group record.
     */
    public function test_inexistent_group_id(): void {
        $group = (object) ['name' => 'My Group'];
        $this->expectException(xapi_exception::class);
        $item = item_group::create_from_group($group);
    }
}

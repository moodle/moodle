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
 * Contains test cases for testing statement actor class.
 *
 * @package    core_xapi
 * @since      Moodle 3.9
 * @copyright  2020 Ferran Recio
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class item_actor_testcase extends advanced_testcase {

    /**
     * Test item creation with agent.
     */
    public function test_creation_agent(): void {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();

        $item = item_agent::create_from_user($user);
        $data = $item->get_data();

        $item = item_actor::create_from_data($data);

        $this->assertEquals(json_encode($item), json_encode($data));
        $this->assertEquals('core_xapi\local\statement\item_agent', get_class($item));

        // Create without specify type.
        unset($data->objectType);

        $item = item_actor::create_from_data($data);

        $this->assertEquals(json_encode($item), json_encode($data));
        $this->assertEquals('core_xapi\local\statement\item_agent', get_class($item));

        // Check user.
        $itemuser = $item->get_user();
        $this->assertEquals($itemuser->id, $user->id);
        $itemusers = $item->get_all_users();
        $this->assertCount(1, $itemusers);
    }

    /**
     * Test item creation with group.
     */
    public function test_creation_group(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course->id);
        $group = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group->id, 'userid' => $user->id));

        $item = item_group::create_from_group($group);
        $data = $item->get_data();

        $item = item_actor::create_from_data($data);

        $this->assertEquals(json_encode($item), json_encode($data));
        $this->assertEquals('core_xapi\local\statement\item_group', get_class($item));

        // Check group.
        $itemgroup = $item->get_group();
        $this->assertEquals($itemgroup->id, $group->id);
        $itemusers = $item->get_all_users();
        $this->assertCount(1, $itemusers);

        // Code must prevent from using group as a single user.
        $this->expectException(xapi_exception::class);
        $itemusers = $item->get_user();
    }

    /**
     * Test for invalid structures.
     */
    public function test_invalid_data(): void {
        $this->expectException(xapi_exception::class);
        $data = (object) [
            'objectType' => 'Fake',
        ];
        $item = item_actor::create_from_data($data);
    }
}

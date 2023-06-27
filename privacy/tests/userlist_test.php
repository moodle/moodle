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
 * Unit Tests for the approved userlist Class
 *
 * @package     core_privacy
 * @category    test
 * @copyright   2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

use \core_privacy\local\request\userlist;

/**
 * Tests for the \core_privacy API's approved userlist functionality.
 *
 * @copyright   2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core_privacy\local\request\userlist
 */
class userlist_test extends advanced_testcase {

    /**
     * Ensure that valid SQL results in the relevant users being added.
     *
     * @covers ::add_from_sql
     */
    public function test_add_from_sql() {
        global $DB;

        $sql = "SELECT c.id FROM {user} c";
        $params = [];
        $allusers = $DB->get_records_sql($sql, $params);

        $uut = new userlist(\context_system::instance(), 'core_privacy');
        $uut->add_from_sql('id', $sql, $params);

        $this->assertCount(count($allusers), $uut);
    }

    /**
     * Ensure that adding a single user adds that user.
     *
     * @covers ::add_user
     */
    public function test_add_user() {
        $this->resetAfterTest();

        $u1 = $this->getDataGenerator()->create_user();
        $u2 = $this->getDataGenerator()->create_user();

        $uut = new userlist(\context_system::instance(), 'core_privacy');
        $uut->add_user($u1->id);

        $this->assertCount(1, $uut);
        $this->assertEquals($uut->current(), $u1);
    }


    /**
     * Ensure that adding multiple users by ID adds those users.
     *
     * @covers ::add_users
     */
    public function test_add_users() {
        $this->resetAfterTest();

        $u1 = $this->getDataGenerator()->create_user();
        $u2 = $this->getDataGenerator()->create_user();
        $u3 = $this->getDataGenerator()->create_user();
        $expected = [$u1->id, $u3->id];

        $uut = new userlist(\context_system::instance(), 'core_privacy');
        $uut->add_users([$u1->id, $u3->id]);

        $this->assertCount(2, $uut);

        foreach ($uut as $user) {
            $this->assertNotFalse(array_search($user->id, $expected));
        }
    }
}

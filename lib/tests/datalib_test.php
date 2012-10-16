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
 * Test for various bits of datalib.php.
 *
 * @package   core
 * @category  phpunit
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Test for various bits of datalib.php.
 *
 * @package core_css
 * @category css
 * @copyright 2012 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class datalib_testcase extends advanced_testcase {
    protected function normalise_sql($sort) {
        return preg_replace('~\s+~', ' ', $sort);
    }

    protected function assert_same_sql($expected, $actual) {
        $this->assertEquals($this->normalise_sql($expected), $this->normalise_sql($actual));
    }

    public function test_users_order_by_sql_simple() {
        list($sort, $params) = users_order_by_sql();
        $this->assert_same_sql('lastname, firstname, id', $sort);
        $this->assertEquals(array(), $params);
    }

    public function test_users_order_by_sql_table_prefix() {
        list($sort, $params) = users_order_by_sql('u');
        $this->assert_same_sql('u.lastname, u.firstname, u.id', $sort);
        $this->assertEquals(array(), $params);
    }

    public function test_users_order_by_sql_search_no_extra_fields() {
        global $CFG, $DB;
        $CFG->showuseridentity = '';
        $this->resetAfterTest(true);

        list($sort, $params) = users_order_by_sql('', 'search', context_system::instance());
        $this->assert_same_sql('CASE WHEN
                    ' . $DB->sql_fullname() . ' = :usersortexact1 OR
                    LOWER(firstname) = LOWER(:usersortexact2) OR
                    LOWER(lastname) = LOWER(:usersortexact3)
                THEN 0 ELSE 1 END, lastname, firstname, id', $sort);
        $this->assertEquals(array('usersortexact1' => 'search', 'usersortexact2' => 'search',
                'usersortexact3' => 'search'), $params);
    }

    public function test_users_order_by_sql_search_with_extra_fields_and_prefix() {
        global $CFG, $DB;
        $CFG->showuseridentity = 'email,idnumber';
        $this->setAdminUser();
        $this->resetAfterTest(true);

        list($sort, $params) = users_order_by_sql('u', 'search', context_system::instance());
        $this->assert_same_sql('CASE WHEN
                    ' . $DB->sql_fullname('u.firstname', 'u.lastname') . ' = :usersortexact1 OR
                    LOWER(u.firstname) = LOWER(:usersortexact2) OR
                    LOWER(u.lastname) = LOWER(:usersortexact3) OR
                    LOWER(u.email) = LOWER(:usersortexact4) OR
                    LOWER(u.idnumber) = LOWER(:usersortexact5)
                THEN 0 ELSE 1 END, u.lastname, u.firstname, u.id', $sort);
        $this->assertEquals(array('usersortexact1' => 'search', 'usersortexact2' => 'search',
                'usersortexact3' => 'search', 'usersortexact4' => 'search', 'usersortexact5' => 'search'), $params);
    }

    public function test_get_admin() {
        global $CFG, $DB;

        $this->resetAfterTest();

        $this->assertSame('2', $CFG->siteadmins); // Admin always has id 2 in new installs.
        $defaultadmin = get_admin();
        $this->assertEquals($defaultadmin->id, 2);

        unset_config('siteadmins');
        $this->assertFalse(get_admin());

        set_config('siteadmins', -1);
        $this->assertFalse(get_admin());

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        set_config('siteadmins', $user1->id.','.$user2->id);
        $admin = get_admin();
        $this->assertEquals($user1->id, $admin->id);

        set_config('siteadmins', '-1,'.$user2->id.','.$user1->id);
        $admin = get_admin();
        $this->assertEquals($user2->id, $admin->id);

        $odlread = $DB->perf_get_reads();
        get_admin(); // No DB queries on repeated call expected.
        get_admin();
        get_admin();
        $this->assertEquals($odlread, $DB->perf_get_reads());
    }

    public function test_get_admins() {
        global $CFG, $DB;

        $this->resetAfterTest();

        $this->assertSame('2', $CFG->siteadmins); // Admin always has id 2 in new installs.

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        $user4 = $this->getDataGenerator()->create_user();

        $admins = get_admins();
        $this->assertEquals(1, count($admins));
        $admin = reset($admins);
        $this->assertTrue(isset($admins[$admin->id]));
        $this->assertEquals(2, $admin->id);

        unset_config('siteadmins');
        $this->assertSame(array(), get_admins());

        set_config('siteadmins', -1);
        $this->assertSame(array(), get_admins());

        set_config('siteadmins', '-1,'.$user2->id.','.$user1->id.','.$user3->id);
        $this->assertEquals(array($user2->id=>$user2, $user1->id=>$user1, $user3->id=>$user3), get_admins());

        $odlread = $DB->perf_get_reads();
        get_admins(); // This should make just one query.
        $this->assertEquals($odlread+1, $DB->perf_get_reads());
    }
}

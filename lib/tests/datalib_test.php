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
 * @package   core
 * @category  phpunit
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_datalib_testcase extends advanced_testcase {
    protected function normalise_sql($sort) {
        return preg_replace('~\s+~', ' ', $sort);
    }

    protected function assert_same_sql($expected, $actual) {
        $this->assertSame($this->normalise_sql($expected), $this->normalise_sql($actual));
    }

    /**
     * Do a test of the user search SQL with database users.
     */
    public function test_users_search_sql() {
        global $DB;
        $this->resetAfterTest();

        // Set up test users.
        $user1 = array(
            'username' => 'usernametest1',
            'idnumber' => 'idnumbertest1',
            'firstname' => 'First Name User Test 1',
            'lastname' => 'Last Name User Test 1',
            'email' => 'usertest1@email.com',
            'address' => '2 Test Street Perth 6000 WA',
            'phone1' => '01010101010',
            'phone2' => '02020203',
            'icq' => 'testuser1',
            'skype' => 'testuser1',
            'yahoo' => 'testuser1',
            'aim' => 'testuser1',
            'msn' => 'testuser1',
            'department' => 'Department of user 1',
            'institution' => 'Institution of user 1',
            'description' => 'This is a description for user 1',
            'descriptionformat' => FORMAT_MOODLE,
            'city' => 'Perth',
            'url' => 'http://moodle.org',
            'country' => 'au'
            );
        $user1 = self::getDataGenerator()->create_user($user1);
        $user2 = array(
            'username' => 'usernametest2',
            'idnumber' => 'idnumbertest2',
            'firstname' => 'First Name User Test 2',
            'lastname' => 'Last Name User Test 2',
            'email' => 'usertest2@email.com',
            'address' => '222 Test Street Perth 6000 WA',
            'phone1' => '01010101010',
            'phone2' => '02020203',
            'icq' => 'testuser1',
            'skype' => 'testuser1',
            'yahoo' => 'testuser1',
            'aim' => 'testuser1',
            'msn' => 'testuser1',
            'department' => 'Department of user 2',
            'institution' => 'Institution of user 2',
            'description' => 'This is a description for user 2',
            'descriptionformat' => FORMAT_MOODLE,
            'city' => 'Perth',
            'url' => 'http://moodle.org',
            'country' => 'au'
            );
        $user2 = self::getDataGenerator()->create_user($user2);

        // Search by name (anywhere in text).
        list($sql, $params) = users_search_sql('User Test 2', '');
        $results = $DB->get_records_sql("SELECT id FROM {user} WHERE $sql ORDER BY username", $params);
        $this->assertFalse(array_key_exists($user1->id, $results));
        $this->assertTrue(array_key_exists($user2->id, $results));

        // Search by (most of) full name.
        list($sql, $params) = users_search_sql('First Name User Test 2 Last Name User', '');
        $results = $DB->get_records_sql("SELECT id FROM {user} WHERE $sql ORDER BY username", $params);
        $this->assertFalse(array_key_exists($user1->id, $results));
        $this->assertTrue(array_key_exists($user2->id, $results));

        // Search by name (start of text) valid or not.
        list($sql, $params) = users_search_sql('User Test 2', '', false);
        $results = $DB->get_records_sql("SELECT id FROM {user} WHERE $sql ORDER BY username", $params);
        $this->assertEquals(0, count($results));
        list($sql, $params) = users_search_sql('First Name User Test 2', '', false);
        $results = $DB->get_records_sql("SELECT id FROM {user} WHERE $sql ORDER BY username", $params);
        $this->assertFalse(array_key_exists($user1->id, $results));
        $this->assertTrue(array_key_exists($user2->id, $results));

        // Search by extra fields included or not (address).
        list($sql, $params) = users_search_sql('Test Street', '', true);
        $results = $DB->get_records_sql("SELECT id FROM {user} WHERE $sql ORDER BY username", $params);
        $this->assertCount(0, $results);
        list($sql, $params) = users_search_sql('Test Street', '', true, array('address'));
        $results = $DB->get_records_sql("SELECT id FROM {user} WHERE $sql ORDER BY username", $params);
        $this->assertCount(2, $results);

        // Exclude user.
        list($sql, $params) = users_search_sql('User Test', '', true, array(), array($user1->id));
        $results = $DB->get_records_sql("SELECT id FROM {user} WHERE $sql ORDER BY username", $params);
        $this->assertFalse(array_key_exists($user1->id, $results));
        $this->assertTrue(array_key_exists($user2->id, $results));

        // Include only user.
        list($sql, $params) = users_search_sql('User Test', '', true, array(), array(), array($user1->id));
        $results = $DB->get_records_sql("SELECT id FROM {user} WHERE $sql ORDER BY username", $params);
        $this->assertTrue(array_key_exists($user1->id, $results));
        $this->assertFalse(array_key_exists($user2->id, $results));

        // Join with another table and use different prefix.
        set_user_preference('amphibian', 'frog', $user1);
        set_user_preference('amphibian', 'salamander', $user2);
        list($sql, $params) = users_search_sql('User Test 1', 'qq');
        $results = $DB->get_records_sql("
                SELECT up.id, up.value
                  FROM {user} qq
                  JOIN {user_preferences} up ON up.userid = qq.id
                 WHERE up.name = :prefname
                       AND $sql", array_merge(array('prefname' => 'amphibian'), $params));
        $this->assertEquals(1, count($results));
        foreach ($results as $record) {
            $this->assertSame('frog', $record->value);
        }
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
        $this->resetAfterTest(true);

        $CFG->showuseridentity = '';

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
        $this->resetAfterTest();

        $CFG->showuseridentity = 'email,idnumber';
        $this->setAdminUser();

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
        $this->assertCount(1, $admins);
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

    public function test_get_course() {
        global $DB, $PAGE, $SITE;
        $this->resetAfterTest();

        // First test course will be current course ($COURSE).
        $course1obj = $this->getDataGenerator()->create_course(array('shortname' => 'FROGS'));
        $PAGE->set_course($course1obj);

        // Second test course is not current course.
        $course2obj = $this->getDataGenerator()->create_course(array('shortname' => 'ZOMBIES'));

        // Check it does not make any queries when requesting the $COURSE/$SITE.
        $before = $DB->perf_get_queries();
        $result = get_course($course1obj->id);
        $this->assertEquals($before, $DB->perf_get_queries());
        $this->assertSame('FROGS', $result->shortname);
        $result = get_course($SITE->id);
        $this->assertEquals($before, $DB->perf_get_queries());

        // Check it makes 1 query to request other courses.
        $result = get_course($course2obj->id);
        $this->assertSame('ZOMBIES', $result->shortname);
        $this->assertEquals($before + 1, $DB->perf_get_queries());
    }

    public function test_increment_revision_number() {
        global $DB;
        $this->resetAfterTest();

        // Use one of the fields that are used with increment_revision_number().
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $DB->set_field('course', 'cacherev', 1, array());

        $record1 = $DB->get_record('course', array('id'=>$course1->id));
        $record2 = $DB->get_record('course', array('id'=>$course2->id));
        $this->assertEquals(1, $record1->cacherev);
        $this->assertEquals(1, $record2->cacherev);

        // Incrementing some lower value.
        $this->setCurrentTimeStart();
        increment_revision_number('course', 'cacherev', 'id = :id', array('id'=>$course1->id));
        $record1 = $DB->get_record('course', array('id'=>$course1->id));
        $record2 = $DB->get_record('course', array('id'=>$course2->id));
        $this->assertTimeCurrent($record1->cacherev);
        $this->assertEquals(1, $record2->cacherev);

        // Incrementing in the same second.
        $rev1 = $DB->get_field('course', 'cacherev', array('id'=>$course1->id));
        $now = time();
        $DB->set_field('course', 'cacherev', $now, array('id'=>$course1->id));
        increment_revision_number('course', 'cacherev', 'id = :id', array('id'=>$course1->id));
        $rev2 = $DB->get_field('course', 'cacherev', array('id'=>$course1->id));
        $this->assertGreaterThan($rev1, $rev2);
        increment_revision_number('course', 'cacherev', 'id = :id', array('id'=>$course1->id));
        $rev3 = $DB->get_field('course', 'cacherev', array('id'=>$course1->id));
        $this->assertGreaterThan($rev2, $rev3);
        $this->assertGreaterThan($now+1, $rev3);
        increment_revision_number('course', 'cacherev', 'id = :id', array('id'=>$course1->id));
        $rev4 = $DB->get_field('course', 'cacherev', array('id'=>$course1->id));
        $this->assertGreaterThan($rev3, $rev4);
        $this->assertGreaterThan($now+2, $rev4);

        // Recovering from runaway revision.
        $DB->set_field('course', 'cacherev', time()+60*60*60, array('id'=>$course2->id));
        $record2 = $DB->get_record('course', array('id'=>$course2->id));
        $this->assertGreaterThan(time(), $record2->cacherev);
        $this->setCurrentTimeStart();
        increment_revision_number('course', 'cacherev', 'id = :id', array('id'=>$course2->id));
        $record2b = $DB->get_record('course', array('id'=>$course2->id));
        $this->assertTimeCurrent($record2b->cacherev);

        // Update all revisions.
        $DB->set_field('course', 'cacherev', 1, array());
        $this->setCurrentTimeStart();
        increment_revision_number('course', 'cacherev', '');
        $record1 = $DB->get_record('course', array('id'=>$course1->id));
        $record2 = $DB->get_record('course', array('id'=>$course2->id));
        $this->assertTimeCurrent($record1->cacherev);
        $this->assertEquals($record1->cacherev, $record2->cacherev);
    }
}

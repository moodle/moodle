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
            'email' => 'usertest1@example.com',
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
            'country' => 'AU'
            );
        $user1 = self::getDataGenerator()->create_user($user1);
        $user2 = array(
            'username' => 'usernametest2',
            'idnumber' => 'idnumbertest2',
            'firstname' => 'First Name User Test 2',
            'lastname' => 'Last Name User Test 2',
            'email' => 'usertest2@example.com',
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
            'country' => 'AU'
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

    public function test_get_coursemodule_from_id() {
        global $CFG;

        $this->resetAfterTest();
        $this->setAdminUser(); // Some generators have bogus access control.

        $this->assertFileExists("$CFG->dirroot/mod/folder/lib.php");
        $this->assertFileExists("$CFG->dirroot/mod/glossary/lib.php");

        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();

        $folder1a = $this->getDataGenerator()->create_module('folder', array('course' => $course1, 'section' => 3));
        $folder1b = $this->getDataGenerator()->create_module('folder', array('course' => $course1));
        $glossary1 = $this->getDataGenerator()->create_module('glossary', array('course' => $course1));

        $folder2 = $this->getDataGenerator()->create_module('folder', array('course' => $course2));

        $cm = get_coursemodule_from_id('folder', $folder1a->cmid);
        $this->assertInstanceOf('stdClass', $cm);
        $this->assertSame('folder', $cm->modname);
        $this->assertSame($folder1a->id, $cm->instance);
        $this->assertSame($folder1a->course, $cm->course);
        $this->assertObjectNotHasAttribute('sectionnum', $cm);

        $this->assertEquals($cm, get_coursemodule_from_id('', $folder1a->cmid));
        $this->assertEquals($cm, get_coursemodule_from_id('folder', $folder1a->cmid, $course1->id));
        $this->assertEquals($cm, get_coursemodule_from_id('folder', $folder1a->cmid, 0));
        $this->assertFalse(get_coursemodule_from_id('folder', $folder1a->cmid, -10));

        $cm2 = get_coursemodule_from_id('folder', $folder1a->cmid, 0, true);
        $this->assertEquals(3, $cm2->sectionnum);
        unset($cm2->sectionnum);
        $this->assertEquals($cm, $cm2);

        $this->assertFalse(get_coursemodule_from_id('folder', -11));

        try {
            get_coursemodule_from_id('folder', -11, 0, false, MUST_EXIST);
            $this->fail('dml_missing_record_exception expected');
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('dml_missing_record_exception', $e);
        }

        try {
            get_coursemodule_from_id('', -11, 0, false, MUST_EXIST);
            $this->fail('dml_missing_record_exception expected');
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('dml_missing_record_exception', $e);
        }

        try {
            get_coursemodule_from_id('a b', $folder1a->cmid, 0, false, MUST_EXIST);
            $this->fail('coding_exception expected');
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }

        try {
            get_coursemodule_from_id('abc', $folder1a->cmid, 0, false, MUST_EXIST);
            $this->fail('dml_read_exception expected');
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('dml_read_exception', $e);
        }
    }

    public function test_get_coursemodule_from_instance() {
        global $CFG;

        $this->resetAfterTest();
        $this->setAdminUser(); // Some generators have bogus access control.

        $this->assertFileExists("$CFG->dirroot/mod/folder/lib.php");
        $this->assertFileExists("$CFG->dirroot/mod/glossary/lib.php");

        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();

        $folder1a = $this->getDataGenerator()->create_module('folder', array('course' => $course1, 'section' => 3));
        $folder1b = $this->getDataGenerator()->create_module('folder', array('course' => $course1));

        $folder2 = $this->getDataGenerator()->create_module('folder', array('course' => $course2));

        $cm = get_coursemodule_from_instance('folder', $folder1a->id);
        $this->assertInstanceOf('stdClass', $cm);
        $this->assertSame('folder', $cm->modname);
        $this->assertSame($folder1a->id, $cm->instance);
        $this->assertSame($folder1a->course, $cm->course);
        $this->assertObjectNotHasAttribute('sectionnum', $cm);

        $this->assertEquals($cm, get_coursemodule_from_instance('folder', $folder1a->id, $course1->id));
        $this->assertEquals($cm, get_coursemodule_from_instance('folder', $folder1a->id, 0));
        $this->assertFalse(get_coursemodule_from_instance('folder', $folder1a->id, -10));

        $cm2 = get_coursemodule_from_instance('folder', $folder1a->id, 0, true);
        $this->assertEquals(3, $cm2->sectionnum);
        unset($cm2->sectionnum);
        $this->assertEquals($cm, $cm2);

        $this->assertFalse(get_coursemodule_from_instance('folder', -11));

        try {
            get_coursemodule_from_instance('folder', -11, 0, false, MUST_EXIST);
            $this->fail('dml_missing_record_exception expected');
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('dml_missing_record_exception', $e);
        }

        try {
            get_coursemodule_from_instance('a b', $folder1a->cmid, 0, false, MUST_EXIST);
            $this->fail('coding_exception expected');
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }

        try {
            get_coursemodule_from_instance('', $folder1a->cmid, 0, false, MUST_EXIST);
            $this->fail('coding_exception expected');
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }

        try {
            get_coursemodule_from_instance('abc', $folder1a->cmid, 0, false, MUST_EXIST);
            $this->fail('dml_read_exception expected');
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('dml_read_exception', $e);
        }
    }

    public function test_get_coursemodules_in_course() {
        global $CFG;

        $this->resetAfterTest();
        $this->setAdminUser(); // Some generators have bogus access control.

        $this->assertFileExists("$CFG->dirroot/mod/folder/lib.php");
        $this->assertFileExists("$CFG->dirroot/mod/glossary/lib.php");
        $this->assertFileExists("$CFG->dirroot/mod/label/lib.php");

        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();

        $folder1a = $this->getDataGenerator()->create_module('folder', array('course' => $course1, 'section' => 3));
        $folder1b = $this->getDataGenerator()->create_module('folder', array('course' => $course1));
        $glossary1 = $this->getDataGenerator()->create_module('glossary', array('course' => $course1));

        $folder2 = $this->getDataGenerator()->create_module('folder', array('course' => $course2));
        $glossary2a = $this->getDataGenerator()->create_module('glossary', array('course' => $course2));
        $glossary2b = $this->getDataGenerator()->create_module('glossary', array('course' => $course2));

        $modules = get_coursemodules_in_course('folder', $course1->id);
        $this->assertCount(2, $modules);

        $cm = $modules[$folder1a->cmid];
        $this->assertSame('folder', $cm->modname);
        $this->assertSame($folder1a->id, $cm->instance);
        $this->assertSame($folder1a->course, $cm->course);
        $this->assertObjectNotHasAttribute('sectionnum', $cm);
        $this->assertObjectNotHasAttribute('revision', $cm);
        $this->assertObjectNotHasAttribute('display', $cm);

        $cm = $modules[$folder1b->cmid];
        $this->assertSame('folder', $cm->modname);
        $this->assertSame($folder1b->id, $cm->instance);
        $this->assertSame($folder1b->course, $cm->course);
        $this->assertObjectNotHasAttribute('sectionnum', $cm);
        $this->assertObjectNotHasAttribute('revision', $cm);
        $this->assertObjectNotHasAttribute('display', $cm);

        $modules = get_coursemodules_in_course('folder', $course1->id, 'revision, display');
        $this->assertCount(2, $modules);

        $cm = $modules[$folder1a->cmid];
        $this->assertSame('folder', $cm->modname);
        $this->assertSame($folder1a->id, $cm->instance);
        $this->assertSame($folder1a->course, $cm->course);
        $this->assertObjectNotHasAttribute('sectionnum', $cm);
        $this->assertObjectHasAttribute('revision', $cm);
        $this->assertObjectHasAttribute('display', $cm);

        $modules = get_coursemodules_in_course('label', $course1->id);
        $this->assertCount(0, $modules);

        try {
            get_coursemodules_in_course('a b', $course1->id);
            $this->fail('coding_exception expected');
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }

        try {
            get_coursemodules_in_course('abc', $course1->id);
            $this->fail('dml_read_exception expected');
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('dml_read_exception', $e);
        }
    }

    public function test_get_all_instances_in_courses() {
        global $CFG;

        $this->resetAfterTest();
        $this->setAdminUser(); // Some generators have bogus access control.

        $this->assertFileExists("$CFG->dirroot/mod/folder/lib.php");
        $this->assertFileExists("$CFG->dirroot/mod/glossary/lib.php");

        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $course3 = $this->getDataGenerator()->create_course();

        $folder1a = $this->getDataGenerator()->create_module('folder', array('course' => $course1, 'section' => 3));
        $folder1b = $this->getDataGenerator()->create_module('folder', array('course' => $course1));
        $glossary1 = $this->getDataGenerator()->create_module('glossary', array('course' => $course1));

        $folder2 = $this->getDataGenerator()->create_module('folder', array('course' => $course2));
        $glossary2a = $this->getDataGenerator()->create_module('glossary', array('course' => $course2));
        $glossary2b = $this->getDataGenerator()->create_module('glossary', array('course' => $course2));

        $folder3 = $this->getDataGenerator()->create_module('folder', array('course' => $course3));

        $modules = get_all_instances_in_courses('folder', array($course1->id => $course1, $course2->id => $course2));
        $this->assertCount(3, $modules);

        foreach ($modules as $cm) {
            if ($folder1a->cmid == $cm->coursemodule) {
                $folder = $folder1a;
            } else if ($folder1b->cmid == $cm->coursemodule) {
                $folder = $folder1b;
            } else if ($folder2->cmid == $cm->coursemodule) {
                $folder = $folder2;
            } else {
                $this->fail('Unexpected cm'. $cm->coursemodule);
            }
            $this->assertSame($folder->name, $cm->name);
            $this->assertSame($folder->course, $cm->course);
        }

        try {
            get_all_instances_in_courses('a b', array($course1->id => $course1, $course2->id => $course2));
            $this->fail('coding_exception expected');
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }

        try {
            get_all_instances_in_courses('', array($course1->id => $course1, $course2->id => $course2));
            $this->fail('coding_exception expected');
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }
    }

    public function test_get_all_instances_in_course() {
        global $CFG;

        $this->resetAfterTest();
        $this->setAdminUser(); // Some generators have bogus access control.

        $this->assertFileExists("$CFG->dirroot/mod/folder/lib.php");
        $this->assertFileExists("$CFG->dirroot/mod/glossary/lib.php");

        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $course3 = $this->getDataGenerator()->create_course();

        $folder1a = $this->getDataGenerator()->create_module('folder', array('course' => $course1, 'section' => 3));
        $folder1b = $this->getDataGenerator()->create_module('folder', array('course' => $course1));
        $glossary1 = $this->getDataGenerator()->create_module('glossary', array('course' => $course1));

        $folder2 = $this->getDataGenerator()->create_module('folder', array('course' => $course2));
        $glossary2a = $this->getDataGenerator()->create_module('glossary', array('course' => $course2));
        $glossary2b = $this->getDataGenerator()->create_module('glossary', array('course' => $course2));

        $folder3 = $this->getDataGenerator()->create_module('folder', array('course' => $course3));

        $modules = get_all_instances_in_course('folder', $course1);
        $this->assertCount(2, $modules);

        foreach ($modules as $cm) {
            if ($folder1a->cmid == $cm->coursemodule) {
                $folder = $folder1a;
            } else if ($folder1b->cmid == $cm->coursemodule) {
                $folder = $folder1b;
            } else {
                $this->fail('Unexpected cm'. $cm->coursemodule);
            }
            $this->assertSame($folder->name, $cm->name);
            $this->assertSame($folder->course, $cm->course);
        }

        try {
            get_all_instances_in_course('a b', $course1);
            $this->fail('coding_exception expected');
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }

        try {
            get_all_instances_in_course('', $course1);
            $this->fail('coding_exception expected');
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }
    }

    /**
     * Test max courses in category
     */
    public function test_max_courses_in_category() {
        global $CFG;
        $this->resetAfterTest();

        // Default settings.
        $this->assertEquals(MAX_COURSES_IN_CATEGORY, get_max_courses_in_category());

        // Misc category.
        $misc = core_course_category::get_default();
        $this->assertEquals(MAX_COURSES_IN_CATEGORY, $misc->sortorder);

        $category1 = $this->getDataGenerator()->create_category();
        $category2 = $this->getDataGenerator()->create_category();

        // Check category sort orders.
        $this->assertEquals(MAX_COURSES_IN_CATEGORY, core_course_category::get($misc->id)->sortorder);
        $this->assertEquals(MAX_COURSES_IN_CATEGORY * 2, core_course_category::get($category1->id)->sortorder);
        $this->assertEquals(MAX_COURSES_IN_CATEGORY * 3, core_course_category::get($category2->id)->sortorder);

        // Create courses.
        $course1 = $this->getDataGenerator()->create_course(['category' => $category1->id]);
        $course2 = $this->getDataGenerator()->create_course(['category' => $category2->id]);
        $course3 = $this->getDataGenerator()->create_course(['category' => $category1->id]);
        $course4 = $this->getDataGenerator()->create_course(['category' => $category2->id]);

        // Check course sort orders.
        $this->assertEquals(MAX_COURSES_IN_CATEGORY * 2 + 2, get_course($course1->id)->sortorder);
        $this->assertEquals(MAX_COURSES_IN_CATEGORY * 3 + 2, get_course($course2->id)->sortorder);
        $this->assertEquals(MAX_COURSES_IN_CATEGORY * 2 + 1, get_course($course3->id)->sortorder);
        $this->assertEquals(MAX_COURSES_IN_CATEGORY * 3 + 1, get_course($course4->id)->sortorder);

        // Increase max course in category.
        $CFG->maxcoursesincategory = 20000;
        $this->assertEquals(20000, get_max_courses_in_category());

        // The sort order has not yet fixed, these sort orders should be the same as before.
        // Categories.
        $this->assertEquals(MAX_COURSES_IN_CATEGORY, core_course_category::get($misc->id)->sortorder);
        $this->assertEquals(MAX_COURSES_IN_CATEGORY * 2, core_course_category::get($category1->id)->sortorder);
        $this->assertEquals(MAX_COURSES_IN_CATEGORY * 3, core_course_category::get($category2->id)->sortorder);
        // Courses in category 1.
        $this->assertEquals(MAX_COURSES_IN_CATEGORY * 2 + 2, get_course($course1->id)->sortorder);
        $this->assertEquals(MAX_COURSES_IN_CATEGORY * 2 + 1, get_course($course3->id)->sortorder);
        // Courses in category 2.
        $this->assertEquals(MAX_COURSES_IN_CATEGORY * 3 + 2, get_course($course2->id)->sortorder);
        $this->assertEquals(MAX_COURSES_IN_CATEGORY * 3 + 1, get_course($course4->id)->sortorder);

        // Create new category so that the sort orders are applied.
        $category3 = $this->getDataGenerator()->create_category();
        // Categories.
        $this->assertEquals(20000, core_course_category::get($misc->id)->sortorder);
        $this->assertEquals(20000 * 2, core_course_category::get($category1->id)->sortorder);
        $this->assertEquals(20000 * 3, core_course_category::get($category2->id)->sortorder);
        $this->assertEquals(20000 * 4, core_course_category::get($category3->id)->sortorder);
        // Courses in category 1.
        $this->assertEquals(20000 * 2 + 2, get_course($course1->id)->sortorder);
        $this->assertEquals(20000 * 2 + 1, get_course($course3->id)->sortorder);
        // Courses in category 2.
        $this->assertEquals(20000 * 3 + 2, get_course($course2->id)->sortorder);
        $this->assertEquals(20000 * 3 + 1, get_course($course4->id)->sortorder);
    }

    /**
     * Test debug message for max courses in category
     */
    public function test_debug_max_courses_in_category() {
        global $CFG;
        $this->resetAfterTest();

        // Set to small value so that we can check the debug message.
        $CFG->maxcoursesincategory = 3;
        $this->assertEquals(3, get_max_courses_in_category());

        $category1 = $this->getDataGenerator()->create_category();

        // There is only one course, no debug message.
        $this->getDataGenerator()->create_course(['category' => $category1->id]);
        $this->assertDebuggingNotCalled();
        // There are two courses, no debug message.
        $this->getDataGenerator()->create_course(['category' => $category1->id]);
        $this->assertDebuggingNotCalled();
        // There is debug message when number of courses reaches the maximum number.
        $this->getDataGenerator()->create_course(['category' => $category1->id]);
        $this->assertDebuggingCalled("The number of courses (category id: $category1->id) has reached max number of courses " .
            "in a category (" . get_max_courses_in_category() . "). It will cause a sorting performance issue. " .
            "Please set higher value for \$CFG->maxcoursesincategory in config.php. " .
            "Please also make sure \$CFG->maxcoursesincategory * MAX_COURSE_CATEGORIES less than max integer. " .
            "See tracker issues: MDL-25669 and MDL-69573");
    }
}

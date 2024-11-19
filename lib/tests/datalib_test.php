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

namespace core;

/**
 * Test for various bits of datalib.php.
 *
 * @package   core
 * @category  test
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class datalib_test extends \advanced_testcase {
    protected function normalise_sql($sort) {
        return preg_replace('~\s+~', ' ', $sort);
    }

    protected function assert_same_sql($expected, $actual) {
        $this->assertSame($this->normalise_sql($expected), $this->normalise_sql($actual));
    }

    /**
     * Do a test of the user search SQL with database users.
     */
    public function test_users_search_sql(): void {
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
            'department' => 'Department of user 1',
            'institution' => 'Institution of user 1',
            'description' => 'This is a description for user 1',
            'descriptionformat' => FORMAT_MOODLE,
            'city' => 'Perth',
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
            'department' => 'Department of user 2',
            'institution' => 'Institution of user 2',
            'description' => 'This is a description for user 2',
            'descriptionformat' => FORMAT_MOODLE,
            'city' => 'Perth',
            'country' => 'AU'
            );
        $user2 = self::getDataGenerator()->create_user($user2);

        // Search by name (anywhere in text).
        list($sql, $params) = users_search_sql('User Test 2', '', USER_SEARCH_CONTAINS);
        $results = $DB->get_records_sql("SELECT id FROM {user} WHERE $sql ORDER BY username", $params);
        $this->assertFalse(array_key_exists($user1->id, $results));
        $this->assertTrue(array_key_exists($user2->id, $results));

        // Search by (most of) full name.
        list($sql, $params) = users_search_sql('First Name User Test 2 Last Name User', '', USER_SEARCH_CONTAINS);
        $results = $DB->get_records_sql("SELECT id FROM {user} WHERE $sql ORDER BY username", $params);
        $this->assertFalse(array_key_exists($user1->id, $results));
        $this->assertTrue(array_key_exists($user2->id, $results));

        // Search by name (start of text) valid or not.
        list($sql, $params) = users_search_sql('User Test 2', '');
        $results = $DB->get_records_sql("SELECT id FROM {user} WHERE $sql ORDER BY username", $params);
        $this->assertEquals(0, count($results));
        list($sql, $params) = users_search_sql('First Name User Test 2', '');
        $results = $DB->get_records_sql("SELECT id FROM {user} WHERE $sql ORDER BY username", $params);
        $this->assertFalse(array_key_exists($user1->id, $results));
        $this->assertTrue(array_key_exists($user2->id, $results));

        // Search by extra fields included or not (address).
        list($sql, $params) = users_search_sql('Test Street', '', USER_SEARCH_CONTAINS);
        $results = $DB->get_records_sql("SELECT id FROM {user} WHERE $sql ORDER BY username", $params);
        $this->assertCount(0, $results);
        list($sql, $params) = users_search_sql('Test Street', '', USER_SEARCH_CONTAINS, array('address'));
        $results = $DB->get_records_sql("SELECT id FROM {user} WHERE $sql ORDER BY username", $params);
        $this->assertCount(2, $results);

        // Exclude user.
        list($sql, $params) = users_search_sql('User Test', '', USER_SEARCH_CONTAINS, array(), array($user1->id));
        $results = $DB->get_records_sql("SELECT id FROM {user} WHERE $sql ORDER BY username", $params);
        $this->assertFalse(array_key_exists($user1->id, $results));
        $this->assertTrue(array_key_exists($user2->id, $results));

        // Include only user.
        list($sql, $params) = users_search_sql('User Test', '', USER_SEARCH_CONTAINS, array(), array(), array($user1->id));
        $results = $DB->get_records_sql("SELECT id FROM {user} WHERE $sql ORDER BY username", $params);
        $this->assertTrue(array_key_exists($user1->id, $results));
        $this->assertFalse(array_key_exists($user2->id, $results));

        // Exact match only.
        [$sql, $params] = users_search_sql('Last Name User Test 1', '', USER_SEARCH_EXACT_MATCH, [], null, null, true);
        $results = $DB->get_records_sql("SELECT id FROM {user} WHERE $sql ORDER BY username", $params);
        $this->assertTrue(array_key_exists($user1->id, $results));
        $this->assertFalse(array_key_exists($user2->id, $results));

        // Join with another table and use different prefix.
        set_user_preference('amphibian', 'frog', $user1);
        set_user_preference('amphibian', 'salamander', $user2);
        list($sql, $params) = users_search_sql('User Test 1', 'qq', USER_SEARCH_CONTAINS);
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

        // Join with another table and include other table fields in search.
        set_user_preference('reptile', 'snake', $user1);
        set_user_preference('reptile', 'lizard', $user2);
        list($sql, $params) = users_search_sql('snake', 'qq', USER_SEARCH_CONTAINS, ['up.value']);
        $results = $DB->get_records_sql("
                SELECT up.id, up.value
                  FROM {user} qq
                  JOIN {user_preferences} up ON up.userid = qq.id
                 WHERE up.name = :prefname
                       AND $sql", array_merge(array('prefname' => 'reptile'), $params));
        $this->assertEquals(1, count($results));
        foreach ($results as $record) {
            $this->assertSame('snake', $record->value);
        }
    }

    public function test_users_order_by_sql_simple(): void {
        list($sort, $params) = users_order_by_sql();
        $this->assert_same_sql('lastname, firstname, id', $sort);
        $this->assertEquals(array(), $params);
    }

    public function test_users_order_by_sql_table_prefix(): void {
        list($sort, $params) = users_order_by_sql('u');
        $this->assert_same_sql('u.lastname, u.firstname, u.id', $sort);
        $this->assertEquals(array(), $params);
    }

    public function test_users_order_by_sql_search_no_extra_fields(): void {
        global $CFG, $DB;
        $this->resetAfterTest(true);

        $CFG->showuseridentity = '';

        list($sort, $params) = users_order_by_sql('', 'search', \context_system::instance());
        $this->assert_same_sql('CASE WHEN
                    ' . $DB->sql_fullname() . ' = :usersortexact1 OR
                    LOWER(firstname) = LOWER(:usersortexact2) OR
                    LOWER(lastname) = LOWER(:usersortexact3)
                THEN 0 ELSE 1 END, lastname, firstname, id', $sort);
        $this->assertEquals(array('usersortexact1' => 'search', 'usersortexact2' => 'search',
                'usersortexact3' => 'search'), $params);
    }

    public function test_users_order_by_sql_search_with_extra_fields_and_prefix(): void {
        global $CFG, $DB;
        $this->resetAfterTest();

        $CFG->showuseridentity = 'email,idnumber';
        $this->setAdminUser();

        list($sort, $params) = users_order_by_sql('u', 'search', \context_system::instance());
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

    public function test_users_order_by_sql_search_with_custom_fields(): void {
        global $CFG, $DB;
        $this->resetAfterTest();

        $CFG->showuseridentity = 'email,idnumber';
        $this->setAdminUser();

        list($sort, $params) =
                users_order_by_sql('u', 'search', \context_system::instance(), ['profile_field_customfield' => 'x.customfield']);
        $this->assert_same_sql('CASE WHEN
                    ' . $DB->sql_fullname('u.firstname', 'u.lastname') . ' = :usersortexact1 OR
                    LOWER(u.firstname) = LOWER(:usersortexact2) OR
                    LOWER(u.lastname) = LOWER(:usersortexact3) OR
                    LOWER(x.customfield) = LOWER(:usersortexact4)
                THEN 0 ELSE 1 END, u.lastname, u.firstname, u.id', $sort);
        $this->assertEquals(array('usersortexact1' => 'search', 'usersortexact2' => 'search',
                'usersortexact3' => 'search', 'usersortexact4' => 'search'), $params);
    }

    public function test_get_admin(): void {
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

    public function test_get_admins(): void {
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

    public function test_get_course(): void {
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

    /**
     * Test that specifying fields when calling get_courses always returns required fields "id, category, visible"
     */
    public function test_get_courses_with_fields(): void {
        $this->resetAfterTest();

        $category = $this->getDataGenerator()->create_category();
        $course = $this->getDataGenerator()->create_course(['category' => $category->id]);

        // Specify "id" only.
        $courses = get_courses($category->id, 'c.sortorder', 'c.id');
        $this->assertCount(1, $courses);
        $this->assertEquals((object) [
            'id' => $course->id,
            'category' => $course->category,
            'visible' => $course->visible,
        ], reset($courses));

        // Specify some optional fields.
        $courses = get_courses($category->id, 'c.sortorder', 'c.id, c.shortname, c.fullname');
        $this->assertCount(1, $courses);
        $this->assertEquals((object) [
            'id' => $course->id,
            'category' => $course->category,
            'visible' => $course->visible,
            'shortname' => $course->shortname,
            'fullname' => $course->fullname,
        ], reset($courses));
    }

    public function test_increment_revision_number(): void {
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

    public function test_get_coursemodule_from_id(): void {
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
        $this->assertObjectNotHasProperty('sectionnum', $cm);

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
        } catch (\moodle_exception $e) {
            $this->assertInstanceOf('dml_missing_record_exception', $e);
        }

        try {
            get_coursemodule_from_id('', -11, 0, false, MUST_EXIST);
            $this->fail('dml_missing_record_exception expected');
        } catch (\moodle_exception $e) {
            $this->assertInstanceOf('dml_missing_record_exception', $e);
        }

        try {
            get_coursemodule_from_id('a b', $folder1a->cmid, 0, false, MUST_EXIST);
            $this->fail('coding_exception expected');
        } catch (\moodle_exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }

        try {
            get_coursemodule_from_id('abc', $folder1a->cmid, 0, false, MUST_EXIST);
            $this->fail('dml_read_exception expected');
        } catch (\moodle_exception $e) {
            $this->assertInstanceOf('dml_read_exception', $e);
        }
    }

    public function test_get_coursemodule_from_instance(): void {
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
        $this->assertObjectNotHasProperty('sectionnum', $cm);

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
        } catch (\moodle_exception $e) {
            $this->assertInstanceOf('dml_missing_record_exception', $e);
        }

        try {
            get_coursemodule_from_instance('a b', $folder1a->cmid, 0, false, MUST_EXIST);
            $this->fail('coding_exception expected');
        } catch (\moodle_exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }

        try {
            get_coursemodule_from_instance('', $folder1a->cmid, 0, false, MUST_EXIST);
            $this->fail('coding_exception expected');
        } catch (\moodle_exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }

        try {
            get_coursemodule_from_instance('abc', $folder1a->cmid, 0, false, MUST_EXIST);
            $this->fail('dml_read_exception expected');
        } catch (\moodle_exception $e) {
            $this->assertInstanceOf('dml_read_exception', $e);
        }
    }

    public function test_get_coursemodules_in_course(): void {
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
        $this->assertObjectNotHasProperty('sectionnum', $cm);
        $this->assertObjectNotHasProperty('revision', $cm);
        $this->assertObjectNotHasProperty('display', $cm);

        $cm = $modules[$folder1b->cmid];
        $this->assertSame('folder', $cm->modname);
        $this->assertSame($folder1b->id, $cm->instance);
        $this->assertSame($folder1b->course, $cm->course);
        $this->assertObjectNotHasProperty('sectionnum', $cm);
        $this->assertObjectNotHasProperty('revision', $cm);
        $this->assertObjectNotHasProperty('display', $cm);

        $modules = get_coursemodules_in_course('folder', $course1->id, 'revision, display');
        $this->assertCount(2, $modules);

        $cm = $modules[$folder1a->cmid];
        $this->assertSame('folder', $cm->modname);
        $this->assertSame($folder1a->id, $cm->instance);
        $this->assertSame($folder1a->course, $cm->course);
        $this->assertObjectNotHasProperty('sectionnum', $cm);
        $this->assertObjectHasProperty('revision', $cm);
        $this->assertObjectHasProperty('display', $cm);

        $modules = get_coursemodules_in_course('label', $course1->id);
        $this->assertCount(0, $modules);

        try {
            get_coursemodules_in_course('a b', $course1->id);
            $this->fail('coding_exception expected');
        } catch (\moodle_exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }

        try {
            get_coursemodules_in_course('abc', $course1->id);
            $this->fail('dml_read_exception expected');
        } catch (\moodle_exception $e) {
            $this->assertInstanceOf('dml_read_exception', $e);
        }
    }

    public function test_get_all_instances_in_courses(): void {
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
        } catch (\moodle_exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }

        try {
            get_all_instances_in_courses('', array($course1->id => $course1, $course2->id => $course2));
            $this->fail('coding_exception expected');
        } catch (\moodle_exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }
    }

    public function test_get_all_instances_in_course(): void {
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
        } catch (\moodle_exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }

        try {
            get_all_instances_in_course('', $course1);
            $this->fail('coding_exception expected');
        } catch (\moodle_exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }
    }

    /**
     * Test max courses in category
     */
    public function test_max_courses_in_category(): void {
        global $CFG;
        $this->resetAfterTest();

        // Default settings.
        $this->assertEquals(MAX_COURSES_IN_CATEGORY, get_max_courses_in_category());

        // Misc category.
        $misc = \core_course_category::get_default();
        $this->assertEquals(MAX_COURSES_IN_CATEGORY, $misc->sortorder);

        $category1 = $this->getDataGenerator()->create_category();
        $category2 = $this->getDataGenerator()->create_category();

        // Check category sort orders.
        $this->assertEquals(MAX_COURSES_IN_CATEGORY, \core_course_category::get($misc->id)->sortorder);
        $this->assertEquals(MAX_COURSES_IN_CATEGORY * 2, \core_course_category::get($category1->id)->sortorder);
        $this->assertEquals(MAX_COURSES_IN_CATEGORY * 3, \core_course_category::get($category2->id)->sortorder);

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
        $this->assertEquals(MAX_COURSES_IN_CATEGORY, \core_course_category::get($misc->id)->sortorder);
        $this->assertEquals(MAX_COURSES_IN_CATEGORY * 2, \core_course_category::get($category1->id)->sortorder);
        $this->assertEquals(MAX_COURSES_IN_CATEGORY * 3, \core_course_category::get($category2->id)->sortorder);
        // Courses in category 1.
        $this->assertEquals(MAX_COURSES_IN_CATEGORY * 2 + 2, get_course($course1->id)->sortorder);
        $this->assertEquals(MAX_COURSES_IN_CATEGORY * 2 + 1, get_course($course3->id)->sortorder);
        // Courses in category 2.
        $this->assertEquals(MAX_COURSES_IN_CATEGORY * 3 + 2, get_course($course2->id)->sortorder);
        $this->assertEquals(MAX_COURSES_IN_CATEGORY * 3 + 1, get_course($course4->id)->sortorder);

        // Create new category so that the sort orders are applied.
        $category3 = $this->getDataGenerator()->create_category();
        // Categories.
        $this->assertEquals(20000, \core_course_category::get($misc->id)->sortorder);
        $this->assertEquals(20000 * 2, \core_course_category::get($category1->id)->sortorder);
        $this->assertEquals(20000 * 3, \core_course_category::get($category2->id)->sortorder);
        $this->assertEquals(20000 * 4, \core_course_category::get($category3->id)->sortorder);
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
    public function test_debug_max_courses_in_category(): void {
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

    /**
     * Tests the get_users_listing function.
     */
    public function test_get_users_listing(): void {
        global $DB;

        $this->resetAfterTest();

        $generator = $this->getDataGenerator();

        // Set up profile field.
        $generator->create_custom_profile_field(['datatype' => 'text',
                'shortname' => 'specialid', 'name' => 'Special user id']);

        // Set up the show user identity option.
        set_config('showuseridentity', 'department,profile_field_specialid');

        // Get all the existing user ids (we're going to remove these from test results).
        $existingids = array_fill_keys($DB->get_fieldset_select('user', 'id', '1 = 1'), true);

        // Create some test user accounts.
        $userids = [];
        foreach (['a', 'b', 'c', 'd'] as $key) {
            $record = [
                'username' => 'user_' . $key,
                'firstname' => $key . '_first',
                'lastname' => 'last_' . $key,
                'department' => 'department_' . $key,
                'profile_field_specialid' => 'special_' . $key,
                'lastaccess' => ord($key)
            ];
            $user = $generator->create_user($record);
            $userids[] = $user->id;
        }

        // Check default result with no parameters.
        $results = get_users_listing();
        $results = array_diff_key($results, $existingids);

        // It should return all the results in order.
        $this->assertEquals($userids, array_keys($results));

        // Results should have some general fields and name fields, check some samples.
        $this->assertEquals('user_a', $results[$userids[0]]->username);
        $this->assertEquals('user_a@example.com', $results[$userids[0]]->email);
        $this->assertEquals(1, $results[$userids[0]]->confirmed);
        $this->assertEquals('a_first', $results[$userids[0]]->firstname);
        $this->assertObjectHasProperty('firstnamephonetic', $results[$userids[0]]);

        // Should not have the custom field or department because no context specified.
        $this->assertObjectNotHasProperty('department', $results[$userids[0]]);
        $this->assertObjectNotHasProperty('profile_field_specialid', $results[$userids[0]]);

        // Check sorting.
        $results = get_users_listing('username', 'DESC');
        $results = array_diff_key($results, $existingids);
        $this->assertEquals([$userids[3], $userids[2], $userids[1], $userids[0]], array_keys($results));

        // Check default fallback sort field works as expected.
        $results = get_users_listing('blah2', 'ASC');
        $results = array_diff_key($results, $existingids);
        $this->assertEquals([$userids[0], $userids[1], $userids[2], $userids[3]], array_keys($results));

        // Check default fallback sort direction works as expected.
        $results = get_users_listing('lastaccess', 'blah2');
        $results = array_diff_key($results, $existingids);
        $this->assertEquals([$userids[0], $userids[1], $userids[2], $userids[3]], array_keys($results));

        // Add the options to showuseridentity and check it returns those fields but only if you
        // specify a context AND have permissions.
        $results = get_users_listing('lastaccess', 'asc', 0, 0, '', '', '', '', null,
                \context_system::instance());
        $this->assertObjectNotHasProperty('department', $results[$userids[0]]);
        $this->assertObjectNotHasProperty('profile_field_specialid', $results[$userids[0]]);
        $this->setAdminUser();
        $results = get_users_listing('lastaccess', 'asc', 0, 0, '', '', '', '', null,
                \context_system::instance());
        $this->assertEquals('department_a', $results[$userids[0]]->department);
        $this->assertEquals('special_a', $results[$userids[0]]->profile_field_specialid);

        // Check search (full name, email, username).
        $results = get_users_listing('lastaccess', 'asc', 0, 0, 'b_first last_b');
        $this->assertEquals([$userids[1]], array_keys($results));
        $results = get_users_listing('lastaccess', 'asc', 0, 0, 'c@example');
        $this->assertEquals([$userids[2]], array_keys($results));
        $results = get_users_listing('lastaccess', 'asc', 0, 0, 'user_d');
        $this->assertEquals([$userids[3]], array_keys($results));

        // Check first and last initial restriction (all the test ones have same last initial).
        $results = get_users_listing('lastaccess', 'asc', 0, 0, '', 'C');
        $this->assertEquals([$userids[2]], array_keys($results));
        $results = get_users_listing('lastaccess', 'asc', 0, 0, '', '', 'L');
        $results = array_diff_key($results, $existingids);
        $this->assertEquals($userids, array_keys($results));

        // Check the extra where clause, either with the 'u.' prefix or not.
        $results = get_users_listing('lastaccess', 'asc', 0, 0, '', '', '', 'id IN (:x,:y)',
                ['x' => $userids[1], 'y' => $userids[3]]);
        $results = array_diff_key($results, $existingids);
        $this->assertEquals([$userids[1], $userids[3]], array_keys($results));
        $results = get_users_listing('lastaccess', 'asc', 0, 0, '', '', '', 'u.id IN (:x,:y)',
                ['x' => $userids[1], 'y' => $userids[3]]);
        $results = array_diff_key($results, $existingids);
        $this->assertEquals([$userids[1], $userids[3]], array_keys($results));
    }

    /**
     * Data provider for test_get_safe_orderby().
     *
     * @return array
     */
    public static function get_safe_orderby_provider(): array {
        $orderbymap = [
            'courseid' => 'c.id',
            'somecustomvalue' => 'c.startdate, c.shortname',
            'default' => 'c.fullname',
        ];
        $orderbymapnodefault = [
            'courseid' => 'c.id',
            'somecustomvalue' => 'c.startdate, c.shortname',
        ];

        return [
            'Valid option, no direction specified' => [
                $orderbymap,
                'somecustomvalue',
                '',
                ' ORDER BY c.startdate, c.shortname',
            ],
            'Valid option, valid direction specified' => [
                $orderbymap,
                'courseid',
                'DESC',
                ' ORDER BY c.id DESC',
            ],
            'Valid option, valid lowercase direction specified' => [
                $orderbymap,
                'courseid',
                'asc',
                ' ORDER BY c.id ASC',
            ],
            'Valid option, invalid direction specified' => [
                $orderbymap,
                'courseid',
                'BOOP',
                ' ORDER BY c.id',
            ],
            'Valid option, invalid lowercase direction specified' => [
                $orderbymap,
                'courseid',
                'boop',
                ' ORDER BY c.id',
            ],
            'Invalid option default fallback, with valid direction' => [
                $orderbymap,
                'thisdoesnotexist',
                'ASC',
                ' ORDER BY c.fullname ASC',
            ],
            'Invalid option default fallback, with invalid direction' => [
                $orderbymap,
                'thisdoesnotexist',
                'BOOP',
                ' ORDER BY c.fullname',
            ],
            'Invalid option without default, with valid direction' => [
                $orderbymapnodefault,
                'thisdoesnotexist',
                'ASC',
                '',
            ],
            'Invalid option without default, with invalid direction' => [
                $orderbymapnodefault,
                'thisdoesnotexist',
                'NOPE',
                '',
            ],
        ];
    }

    /**
     * Tests the get_safe_orderby function.
     *
     * @dataProvider get_safe_orderby_provider
     * @param array $orderbymap The ORDER BY parameter mapping array.
     * @param string $orderbykey The string key being provided, to check against the map.
     * @param string $direction The optional direction to order by.
     * @param string $expected The expected string output of the method.
     */
    public function test_get_safe_orderby(array $orderbymap, string $orderbykey, string $direction, string $expected): void {
        $actual = get_safe_orderby($orderbymap, $orderbykey, $direction);
        $this->assertEquals($expected, $actual);
    }

    /**
     * Data provider for test_get_safe_orderby_multiple().
     *
     * @return array
     */
    public static function get_safe_orderby_multiple_provider(): array {
        $orderbymap = [
            'courseid' => 'c.id',
            'firstname' => 'u.firstname',
            'default' => 'c.startdate',
        ];
        $orderbymapnodefault = [
            'courseid' => 'c.id',
            'firstname' => 'u.firstname',
        ];

        return [
            'Valid options, no directions specified' => [
                $orderbymap,
                ['courseid', 'firstname'],
                [],
                ' ORDER BY c.id, u.firstname',
            ],
            'Valid options, some direction specified' => [
                $orderbymap,
                ['courseid', 'firstname'],
                ['DESC'],
                ' ORDER BY c.id DESC, u.firstname',
            ],
            'Valid options, all directions specified' => [
                $orderbymap,
                ['courseid', 'firstname'],
                ['ASC', 'desc'],
                ' ORDER BY c.id ASC, u.firstname DESC',
            ],
            'Valid options, valid and invalid directions specified' => [
                $orderbymap,
                ['courseid', 'firstname'],
                ['BOOP', 'DESC'],
                ' ORDER BY c.id, u.firstname DESC',
            ],
            'Valid options, all invalid directions specified' => [
                $orderbymap,
                ['courseid', 'firstname'],
                ['BOOP', 'SNOOT'],
                ' ORDER BY c.id, u.firstname',
            ],
            'Valid and invalid option default fallback, with valid directions' => [
                $orderbymap,
                ['thisdoesnotexist', 'courseid'],
                ['asc', 'DESC'],
                ' ORDER BY c.startdate ASC, c.id DESC',
            ],
            'Valid and invalid option default fallback, with invalid direction' => [
                $orderbymap,
                ['courseid', 'thisdoesnotexist'],
                ['BOOP', 'SNOOT'],
                ' ORDER BY c.id, c.startdate',
            ],
            'Valid and invalid option without default, with valid direction' => [
                $orderbymapnodefault,
                ['thisdoesnotexist', 'courseid'],
                ['ASC', 'DESC'],
                ' ORDER BY c.id DESC',
            ],
            'Valid and invalid option without default, with invalid direction' => [
                $orderbymapnodefault,
                ['thisdoesnotexist', 'courseid'],
                ['BOOP', 'SNOOT'],
                ' ORDER BY c.id',
            ],
            'Invalid option only without default, with valid direction' => [
                $orderbymapnodefault,
                ['thisdoesnotexist'],
                ['ASC'],
                '',
            ],
            'Invalid option only without default, with invalid direction' => [
                $orderbymapnodefault,
                ['thisdoesnotexist'],
                ['BOOP'],
                '',
            ],
            'Single valid option, direction specified' => [
                $orderbymap,
                ['firstname'],
                ['ASC'],
                ' ORDER BY u.firstname ASC',
            ],
            'Single valid option, direction not specified' => [
                $orderbymap,
                ['firstname'],
                [],
                ' ORDER BY u.firstname',
            ],
        ];
    }

    /**
     * Tests the get_safe_orderby_multiple function.
     *
     * @dataProvider get_safe_orderby_multiple_provider
     * @param array $orderbymap The ORDER BY parameter mapping array.
     * @param array $orderbykeys The array of string keys being provided, to check against the map.
     * @param array $directions The optional directions to order by.
     * @param string $expected The expected string output of the method.
     */
    public function test_get_safe_orderby_multiple(array $orderbymap, array $orderbykeys, array $directions,
            string $expected): void {
        $actual = get_safe_orderby_multiple($orderbymap, $orderbykeys, $directions);
        $this->assertEquals($expected, $actual);
    }
}

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
 * Tests core_user class.
 *
 * @package    core
 * @copyright  2013 Rajesh Taneja <rajesh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Test core_user class.
 *
 * @package    core
 * @copyright  2013 Rajesh Taneja <rajesh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_user_testcase extends advanced_testcase {

    /**
     * Setup test data.
     */
    protected function setUp(): void {
        $this->resetAfterTest(true);
    }

    public function test_get_user() {
        global $CFG;


        // Create user and try fetach it with api.
        $user = $this->getDataGenerator()->create_user();
        $this->assertEquals($user, core_user::get_user($user->id, '*', MUST_EXIST));

        // Test noreply user.
        $CFG->noreplyuserid = null;
        $noreplyuser = core_user::get_noreply_user();
        $this->assertEquals(1, $noreplyuser->emailstop);
        $this->assertFalse(core_user::is_real_user($noreplyuser->id));
        $this->assertEquals($CFG->noreplyaddress, $noreplyuser->email);
        $this->assertEquals(get_string('noreplyname'), $noreplyuser->firstname);

        // Set user as noreply user and make sure noreply propery is set.
        core_user::reset_internal_users();
        $CFG->noreplyuserid = $user->id;
        $noreplyuser = core_user::get_noreply_user();
        $this->assertEquals(1, $noreplyuser->emailstop);
        $this->assertTrue(core_user::is_real_user($noreplyuser->id));

        // Test support user.
        core_user::reset_internal_users();
        $CFG->supportemail = null;
        $CFG->noreplyuserid = null;
        $supportuser = core_user::get_support_user();
        $adminuser = get_admin();
        $this->assertEquals($adminuser, $supportuser);
        $this->assertTrue(core_user::is_real_user($supportuser->id));

        // When supportemail is set.
        core_user::reset_internal_users();
        $CFG->supportemail = 'test@example.com';
        $supportuser = core_user::get_support_user();
        $this->assertEquals(core_user::SUPPORT_USER, $supportuser->id);
        $this->assertFalse(core_user::is_real_user($supportuser->id));

        // Set user as support user and make sure noreply propery is set.
        core_user::reset_internal_users();
        $CFG->supportuserid = $user->id;
        $supportuser = core_user::get_support_user();
        $this->assertEquals($user, $supportuser);
        $this->assertTrue(core_user::is_real_user($supportuser->id));
    }

    /**
     * Test get_user_by_username method.
     */
    public function test_get_user_by_username() {
        $record = array();
        $record['username'] = 'johndoe';
        $record['email'] = 'johndoe@example.com';
        $record['timecreated'] = time();

        // Create a default user for the test.
        $userexpected = $this->getDataGenerator()->create_user($record);

        // Assert that the returned user is the espected one.
        $this->assertEquals($userexpected, core_user::get_user_by_username('johndoe'));

        // Assert that a subset of fields is correctly returned.
        $this->assertEquals((object) $record, core_user::get_user_by_username('johndoe', 'username,email,timecreated'));

        // Assert that a user with a different mnethostid will no be returned.
        $this->assertFalse(core_user::get_user_by_username('johndoe', 'username,email,timecreated', 2));

        // Create a new user from a different host.
        $record['mnethostid'] = 2;
        $userexpected2 = $this->getDataGenerator()->create_user($record);

        // Assert that the new user is returned when specified the correct mnethostid.
        $this->assertEquals($userexpected2, core_user::get_user_by_username('johndoe', '*', 2));

        // Assert that a user not in the db return false.
        $this->assertFalse(core_user::get_user_by_username('janedoe'));
    }

    public function test_search() {
        global $DB;

        self::init_search_tests();

        // Set up three courses for test.
        $generator = $this->getDataGenerator();
        $course1 = $generator->create_course();
        $course2 = $generator->create_course();
        $course3 = $generator->create_course();

        // Manager user in system level.
        $manager = $generator->create_user(['firstname' => 'Manager', 'lastname' => 'Person',
                'email' => 'x@x.x']);
        $systemcontext = \context_system::instance();
        $generator->role_assign($DB->get_field('role', 'id', ['shortname' => 'manager']),
                $manager->id, $systemcontext->id);

        // Teachers in one and two courses.
        $teacher1 = $generator->create_user(['firstname' => 'Alberto', 'lastname' => 'Unwin',
                'email' => 'a.unwin@x.x']);
        $generator->enrol_user($teacher1->id, $course1->id, 'teacher');
        $teacher2and3 = $generator->create_user(['firstname' => 'Alexandra', 'lastname' => 'Penguin',
                'email' => 'sillypenguin@x.x']);
        $generator->enrol_user($teacher2and3->id, $course2->id, 'teacher');
        $generator->enrol_user($teacher2and3->id, $course3->id, 'teacher');

        // Students in each course and some on multiple courses.
        $student1 = $generator->create_user(['firstname' => 'Amanda', 'lastname' => 'Hodder',
                'email' => 'hodder_a@x.x']);
        $generator->enrol_user($student1->id, $course1->id, 'student');
        $student2 = $generator->create_user(['firstname' => 'Audrey', 'lastname' => 'Methuen',
                'email' => 'audrey@x.x']);
        $generator->enrol_user($student2->id, $course2->id, 'student');
        $student3 = $generator->create_user(['firstname' => 'Austin', 'lastname' => 'Bloomsbury',
                'email' => 'a.bloomsbury@x.x']);
        $generator->enrol_user($student3->id, $course3->id, 'student');
        $student1and2 = $generator->create_user(['firstname' => 'Augustus', 'lastname' => 'Random',
                'email' => 'random@x.x']);
        $generator->enrol_user($student1and2->id, $course1->id, 'student');
        $generator->enrol_user($student1and2->id, $course2->id, 'student');
        $studentall = $generator->create_user(['firstname' => 'Amelia', 'lastname' => 'House',
                'email' => 'house@x.x']);
        $generator->enrol_user($studentall->id, $course1->id, 'student');
        $generator->enrol_user($studentall->id, $course2->id, 'student');
        $generator->enrol_user($studentall->id, $course3->id, 'student');

        // Special mixed user (name does not begin with A) is a teacher in one course and student
        // in another.
        $mixed = $generator->create_user(['firstname' => 'Xavier', 'lastname' => 'Harper',
                'email' => 'xh1248@x.x']);
        $generator->enrol_user($mixed->id, $course1->id, 'student');
        $generator->enrol_user($mixed->id, $course3->id, 'teacher');

        // As admin user, try searching for somebody at system level by first name, checking the
        // results.
        $this->setAdminUser();
        $result = core_user::search('Amelia');
        $this->assertCount(1, $result);

        // Check some basic fields, and test other fields are present.
        $this->assertEquals($studentall->id, $result[0]->id);
        $this->assertEquals('Amelia', $result[0]->firstname);
        $this->assertEquals('House', $result[0]->lastname);
        $this->assertEquals('house@x.x', $result[0]->email);
        $this->assertEquals(0, $result[0]->deleted);
        $this->assertObjectHasAttribute('firstnamephonetic', $result[0]);
        $this->assertObjectHasAttribute('lastnamephonetic', $result[0]);
        $this->assertObjectHasAttribute('middlename', $result[0]);
        $this->assertObjectHasAttribute('alternatename', $result[0]);
        $this->assertObjectHasAttribute('imagealt', $result[0]);
        $this->assertObjectHasAttribute('username', $result[0]);

        // Now search by lastname, both names, and partials, case-insensitive.
        $this->assertEquals($result, core_user::search('House'));
        $this->assertEquals($result, core_user::search('Amelia house'));
        $this->assertEquals($result, core_user::search('amelI'));
        $this->assertEquals($result, core_user::search('hoUs'));
        $this->assertEquals($result, core_user::search('Amelia H'));

        // Admin user can also search by email (full or partial).
        $this->assertEquals($result, core_user::search('house@x.x'));
        $this->assertEquals($result, core_user::search('hOuse@'));

        // What if we just search for A? (They all begin with A except the manager.)
        $result = core_user::search('a');
        $this->assertCount(7, $result);

        // Au gets us Audrey, Austin, and Augustus - in alphabetical order by surname.
        $result = core_user::search('au');
        $this->assertCount(3, $result);
        $this->assertEquals('Austin', $result[0]->firstname);
        $this->assertEquals('Audrey', $result[1]->firstname);
        $this->assertEquals('Augustus', $result[2]->firstname);

        // But if we search within course 2 we'll get Audrey and Augustus first.
        $course2context = \context_course::instance($course2->id);
        $result = core_user::search('au', $course2context);
        $this->assertCount(3, $result);
        $this->assertEquals('Audrey', $result[0]->firstname);
        $this->assertEquals('Augustus', $result[1]->firstname);
        $this->assertEquals('Austin', $result[2]->firstname);

        // Try doing a few searches as manager - we should get the same results and can still
        // search by email too.
        $this->setUser($manager);
        $result = core_user::search('a');
        $this->assertCount(7, $result);
        $result = core_user::search('au', $course2context);
        $this->assertCount(3, $result);
        $result = core_user::search('house@x.x');
        $this->assertCount(1, $result);

        // Teacher 1. No site-level permission so can't see users outside the enrolled course.
        $this->setUser($teacher1);
        $result = core_user::search('au');
        $this->assertCount(1, $result);
        $this->assertEquals('Augustus', $result[0]->firstname);

        // Can still search by email for that user.
        $result = core_user::search('random@x.x');
        $this->assertCount(1, $result);

        // Search everyone - teacher can only see four users (including themself).
        $result = core_user::search('a');
        $this->assertCount(4, $result);

        // Search within course 2 - you get the same four users (which doesn't include
        // everyone on that course) but the two on course 2 should be first.
        $result = core_user::search('a', $course2context);
        $this->assertCount(4, $result);
        $this->assertEquals('Amelia', $result[0]->firstname);
        $this->assertEquals('Augustus', $result[1]->firstname);

        // Other teacher.
        $this->setUser($teacher2and3);
        $result = core_user::search('au');
        $this->assertCount(3, $result);

        $result = core_user::search('a');
        $this->assertCount(5, $result);

        // Student can only see users on course 3.
        $this->setUser($student3);
        $result = core_user::search('a');
        $this->assertCount(3, $result);

        $result = core_user::search('au');
        $this->assertCount(1, $result);
        $this->assertEquals('Austin', $result[0]->firstname);

        // Student cannot search by email.
        $result = core_user::search('a.bloomsbury@x.x');
        $this->assertCount(0, $result);

        // Student on all courses can see all the A users.
        $this->setUser($studentall);
        $result = core_user::search('a');
        $this->assertCount(7, $result);

        // Mixed user can see users on courses 1 and 3.
        $this->setUser($mixed);
        $result = core_user::search('a');
        $this->assertCount(6, $result);

        // Mixed user can search by email for students on course 3 but not on course 1.
        $result = core_user::search('hodder_a@x.x');
        $this->assertCount(0, $result);
        $result = core_user::search('house@x.x');
        $this->assertCount(1, $result);
    }

    /**
     * Tests the search() function with limits on the number to return.
     */
    public function test_search_with_count() {
        self::init_search_tests();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();

        // Check default limit (30).
        for ($i = 0; $i < 31; $i++) {
            $student = $generator->create_user(['firstname' => 'Guy', 'lastname' => 'Xxx' . $i,
                    'email' => 'xxx@x.x']);
            $generator->enrol_user($student->id, $course->id, 'student');
        }
        $this->setAdminUser();
        $result = core_user::search('Guy');
        $this->assertCount(30, $result);

        // Check a small limit.
        $result = core_user::search('Guy', null, 10);
        $this->assertCount(10, $result);

        // Check no limit.
        $result = core_user::search('Guy', null, 0);
        $this->assertCount(31, $result);
    }

    /**
     * When course is in separate groups mode and user is a student, they can't see people who
     * are not in the same group. This is checked by the user profile permission thing and not
     * currently by the original query.
     */
    public function test_search_group_permissions() {
        global $DB;

        self::init_search_tests();

        // Create one user to do the searching.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course(['groupmode' => SEPARATEGROUPS]);
        $searcher = $generator->create_user(['firstname' => 'Searchy', 'lastname' => 'Sam',
                'email' => 'xxx@x.x']);
        $generator->enrol_user($searcher->id, $course->id, 'student');
        $group = $generator->create_group(['courseid' => $course->id]);
        groups_add_member($group, $searcher);

        // Create a large number of people so that we have to make multiple database reads.
        $targets = [];
        for ($i = 0; $i < 50; $i++) {
            $student = $generator->create_user(['firstname' => 'Guy', 'lastname' => 'Xxx' . $i,
                    'email' => 'xxx@x.x']);
            $generator->enrol_user($student->id, $course->id, 'student');
            $targets[] = $student;
        }

        // The first and last people are in the same group.
        groups_add_member($group, $targets[0]);
        groups_add_member($group, $targets[49]);

        // As searcher, we only find the 2 in the same group.
        $this->setUser($searcher);
        $result = core_user::search('Guy');
        $this->assertCount(2, $result);

        // If we change the course to visible groups though, we get the max number.
        $DB->set_field('course', 'groupmode', VISIBLEGROUPS, ['id' => $course->id]);
        $result = core_user::search('Guy');
        $this->assertCount(30, $result);
    }

    /**
     * When course is in separate groups mode and user is a student, they can't see people who
     * are not in the same group. This is checked by the user profile permission thing and not
     * currently by the original query.
     */
    public function test_search_deleted_users() {
        self::init_search_tests();

        // Create one user to do the searching.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $searcher = $generator->create_user(['firstname' => 'Searchy', 'lastname' => 'Sam',
                'email' => 'xxx@x.x']);
        $generator->enrol_user($searcher->id, $course->id, 'student');

        // Create another two users to search for.
        $student1 = $generator->create_user(['firstname' => 'Amelia', 'lastname' => 'Aardvark']);
        $student2 = $generator->create_user(['firstname' => 'Amelia', 'lastname' => 'Beetle']);
        $generator->enrol_user($student1->id, $course->id, 'student');
        $generator->enrol_user($student2->id, $course->id, 'student');

        // As searcher, we find both users.
        $this->setUser($searcher);
        $result = core_user::search('Amelia');
        $this->assertCount(2, $result);

        // What if one is deleted?
        delete_user($student1);
        $result = core_user::search('Amelia');
        $this->assertCount(1, $result);
        $this->assertEquals('Beetle', $result[0]->lastname);

        // Delete the other, for good measure.
        delete_user($student2);
        $result = core_user::search('Amelia');
        $this->assertCount(0, $result);
    }

    /**
     * Carries out standard setup for the search test functions.
     */
    protected static function init_search_tests() {
        global $DB;

        // For all existing users, set their name and email to something stupid so we don't
        // accidentally find one, confusing the test counts.
        $DB->set_field('user', 'firstname', 'Zaphod');
        $DB->set_field('user', 'lastname', 'Beeblebrox');
        $DB->set_field('user', 'email', 'zaphod@beeblebrox.example.org');

        // This is the default value, but let's set it just to be certain in case it changes later.
        // It affects what fields admin (and other users with the viewuseridentity permission) can
        // search in addition to the name.
        set_config('showuseridentity', 'email');
    }

    /**
     * Test require_active_user
     */
    public function test_require_active_user() {
        global $DB;

        // Create a default user for the test.
        $userexpected = $this->getDataGenerator()->create_user();

        // Simple case, all good.
        core_user::require_active_user($userexpected, true, true);

        // Set user not confirmed.
        $DB->set_field('user', 'confirmed', 0, array('id' => $userexpected->id));
        try {
            core_user::require_active_user($userexpected);
        } catch (moodle_exception $e) {
            $this->assertEquals('usernotconfirmed', $e->errorcode);
        }
        $DB->set_field('user', 'confirmed', 1, array('id' => $userexpected->id));

        // Set nologin auth method.
        $DB->set_field('user', 'auth', 'nologin', array('id' => $userexpected->id));
        try {
            core_user::require_active_user($userexpected, false, true);
        } catch (moodle_exception $e) {
            $this->assertEquals('suspended', $e->errorcode);
        }
        // Check no exceptions are thrown if we don't specify to check suspended.
        core_user::require_active_user($userexpected);
        $DB->set_field('user', 'auth', 'manual', array('id' => $userexpected->id));

        // Set user suspended.
        $DB->set_field('user', 'suspended', 1, array('id' => $userexpected->id));
        try {
            core_user::require_active_user($userexpected, true);
        } catch (moodle_exception $e) {
            $this->assertEquals('suspended', $e->errorcode);
        }
        // Check no exceptions are thrown if we don't specify to check suspended.
        core_user::require_active_user($userexpected);

        // Delete user.
        delete_user($userexpected);
        try {
            core_user::require_active_user($userexpected);
        } catch (moodle_exception $e) {
            $this->assertEquals('userdeleted', $e->errorcode);
        }

        // Use a not real user.
        $noreplyuser = core_user::get_noreply_user();
        try {
            core_user::require_active_user($noreplyuser, true);
        } catch (moodle_exception $e) {
            $this->assertEquals('invaliduser', $e->errorcode);
        }

        // Get the guest user.
        $guestuser = $DB->get_record('user', array('username' => 'guest'));
        try {
            core_user::require_active_user($guestuser, true);
        } catch (moodle_exception $e) {
            $this->assertEquals('guestsarenotallowed', $e->errorcode);
        }

    }

    /**
     * Test get_property_definition() method.
     */
    public function test_get_property_definition() {
        // Try to get a existing property.
        $properties = core_user::get_property_definition('id');
        $this->assertEquals($properties['type'], PARAM_INT);
        $properties = core_user::get_property_definition('username');
        $this->assertEquals($properties['type'], PARAM_USERNAME);

        // Invalid property.
        try {
            core_user::get_property_definition('fullname');
        } catch (coding_exception $e) {
            $this->assertRegExp('/Invalid property requested./', $e->getMessage());
        }

        // Empty parameter.
        try {
            core_user::get_property_definition('');
        } catch (coding_exception $e) {
            $this->assertRegExp('/Invalid property requested./', $e->getMessage());
        }
    }

    /**
     * Test validate() method.
     */
    public function test_validate() {

        // Create user with just with username and firstname.
        $record = array('username' => 's10', 'firstname' => 'Bebe Stevens');
        $validation = core_user::validate((object)$record);

        // Validate the user, should return true as the user data is correct.
        $this->assertTrue($validation);

        // Create user with incorrect data (invalid country and theme).
        $record = array('username' => 's1', 'firstname' => 'Eric Cartman', 'country' => 'UU', 'theme' => 'beise');

        // Should return an array with 2 errors.
        $validation = core_user::validate((object)$record);
        $this->assertArrayHasKey('country', $validation);
        $this->assertArrayHasKey('theme', $validation);
        $this->assertCount(2, $validation);

        // Create user with malicious data (xss).
        $record = array('username' => 's3', 'firstname' => 'Kyle<script>alert(1);<script> Broflovski');

        // Should return an array with 1 error.
        $validation = core_user::validate((object)$record);
        $this->assertCount(1, $validation);
        $this->assertArrayHasKey('firstname', $validation);
    }

    /**
     * Test clean_data() method.
     */
    public function test_clean_data() {
        $this->resetAfterTest(false);

        $user = new stdClass();
        $user->firstname = 'John <script>alert(1)</script> Doe';
        $user->username = 'john%#&~%*_doe';
        $user->email = ' john@testing.com ';
        $user->deleted = 'no';
        $user->description = '<b>A description <script>alert(123);</script>about myself.</b>';
        $usercleaned = core_user::clean_data($user);

        // Expected results.
        $this->assertEquals('John alert(1) Doe', $usercleaned->firstname);
        $this->assertEquals('john@testing.com', $usercleaned->email);
        $this->assertEquals(0, $usercleaned->deleted);
        $this->assertEquals('<b>A description <script>alert(123);</script>about myself.</b>', $user->description);
        $this->assertEquals('john_doe', $user->username);

        // Try to clean an invalid property (userfullname).
        $user->userfullname = 'John Doe';
        core_user::clean_data($user);
        $this->assertDebuggingCalled("The property 'userfullname' could not be cleaned.");
    }

    /**
     * Test clean_field() method.
     */
    public function test_clean_field() {

        // Create a 'malicious' user object/
        $user = new stdClass();
        $user->firstname = 'John <script>alert(1)</script> Doe';
        $user->username = 'john%#&~%*_doe';
        $user->email = ' john@testing.com ';
        $user->deleted = 'no';
        $user->description = '<b>A description <script>alert(123);</script>about myself.</b>';
        $user->userfullname = 'John Doe';

        // Expected results.
        $this->assertEquals('John alert(1) Doe', core_user::clean_field($user->firstname, 'firstname'));
        $this->assertEquals('john_doe', core_user::clean_field($user->username, 'username'));
        $this->assertEquals('john@testing.com', core_user::clean_field($user->email, 'email'));
        $this->assertEquals(0, core_user::clean_field($user->deleted, 'deleted'));
        $this->assertEquals('<b>A description <script>alert(123);</script>about myself.</b>', core_user::clean_field($user->description, 'description'));

        // Try to clean an invalid property (fullname).
        core_user::clean_field($user->userfullname, 'fullname');
        $this->assertDebuggingCalled("The property 'fullname' could not be cleaned.");
    }

    /**
     * Test get_property_type() method.
     */
    public function test_get_property_type() {

        // Fetch valid properties and verify if the type is correct.
        $type = core_user::get_property_type('username');
        $this->assertEquals(PARAM_USERNAME, $type);
        $type = core_user::get_property_type('email');
        $this->assertEquals(PARAM_RAW_TRIMMED, $type);
        $type = core_user::get_property_type('timezone');
        $this->assertEquals(PARAM_TIMEZONE, $type);

        // Try to fetch type of a non-existent properties.
        $nonexistingproperty = 'userfullname';
        $this->expectException('coding_exception');
        $this->expectExceptionMessage('Invalid property requested: ' . $nonexistingproperty);
        core_user::get_property_type($nonexistingproperty);
        $nonexistingproperty = 'mobilenumber';
        $this->expectExceptionMessage('Invalid property requested: ' . $nonexistingproperty);
        core_user::get_property_type($nonexistingproperty);
    }

    /**
     * Test get_property_null() method.
     */
    public function test_get_property_null() {
        // Fetch valid properties and verify if it is NULL_ALLOWED or NULL_NOT_ALLOWED.
        $property = core_user::get_property_null('username');
        $this->assertEquals(NULL_NOT_ALLOWED, $property);
        $property = core_user::get_property_null('password');
        $this->assertEquals(NULL_NOT_ALLOWED, $property);
        $property = core_user::get_property_null('imagealt');
        $this->assertEquals(NULL_ALLOWED, $property);
        $property = core_user::get_property_null('middlename');
        $this->assertEquals(NULL_ALLOWED, $property);

        // Try to fetch type of a non-existent properties.
        $nonexistingproperty = 'lastnamefonetic';
        $this->expectException('coding_exception');
        $this->expectExceptionMessage('Invalid property requested: ' . $nonexistingproperty);
        core_user::get_property_null($nonexistingproperty);
        $nonexistingproperty = 'midlename';
        $this->expectExceptionMessage('Invalid property requested: ' . $nonexistingproperty);
        core_user::get_property_null($nonexistingproperty);
    }

    /**
     * Test get_property_choices() method.
     */
    public function test_get_property_choices() {

        // Test against country property choices.
        $choices = core_user::get_property_choices('country');
        $this->assertArrayHasKey('AU', $choices);
        $this->assertArrayHasKey('BR', $choices);
        $this->assertArrayNotHasKey('WW', $choices);
        $this->assertArrayNotHasKey('TX', $choices);

        // Test against lang property choices.
        $choices = core_user::get_property_choices('lang');
        $this->assertArrayHasKey('en', $choices);
        $this->assertArrayNotHasKey('ww', $choices);
        $this->assertArrayNotHasKey('yy', $choices);

        // Test against theme property choices.
        $choices = core_user::get_property_choices('theme');
        $this->assertArrayHasKey('boost', $choices);
        $this->assertArrayHasKey('classic', $choices);
        $this->assertArrayNotHasKey('unknowntheme', $choices);
        $this->assertArrayNotHasKey('wrongtheme', $choices);

        // Try to fetch type of a non-existent properties.
        $nonexistingproperty = 'language';
        $this->expectException('coding_exception');
        $this->expectExceptionMessage('Invalid property requested: ' . $nonexistingproperty);
        core_user::get_property_null($nonexistingproperty);
        $nonexistingproperty = 'coutries';
        $this->expectExceptionMessage('Invalid property requested: ' . $nonexistingproperty);
        core_user::get_property_null($nonexistingproperty);
    }

    /**
     * Test get_property_default().
     *
     *
     * @expectedException        coding_exception
     * @expectedExceptionMessage Invalid property requested, or the property does not has a default value.
     */
    public function test_get_property_default() {
        global $CFG;
        $this->resetAfterTest();

        $country = core_user::get_property_default('country');
        $this->assertEquals($CFG->country, $country);
        set_config('country', 'AU');
        core_user::reset_caches();
        $country = core_user::get_property_default('country');
        $this->assertEquals($CFG->country, $country);

        $lang = core_user::get_property_default('lang');
        $this->assertEquals($CFG->lang, $lang);
        set_config('lang', 'en');
        $lang = core_user::get_property_default('lang');
        $this->assertEquals($CFG->lang, $lang);

        $this->setTimezone('Europe/London', 'Pacific/Auckland');
        core_user::reset_caches();
        $timezone = core_user::get_property_default('timezone');
        $this->assertEquals('Europe/London', $timezone);
        $this->setTimezone('99', 'Pacific/Auckland');
        core_user::reset_caches();
        $timezone = core_user::get_property_default('timezone');
        $this->assertEquals('Pacific/Auckland', $timezone);

        core_user::get_property_default('firstname');
    }

    /**
     * Ensure that the noreply user is not cached.
     */
    public function test_get_noreply_user() {
        global $CFG;

        // Create a new fake language 'xx' with the 'noreplyname'.
        $langfolder = $CFG->dataroot . '/lang/xx';
        check_dir_exists($langfolder);
        $langconfig = "<?php\n\defined('MOODLE_INTERNAL') || die();";
        file_put_contents($langfolder . '/langconfig.php', $langconfig);
        $langconfig = "<?php\n\$string['noreplyname'] = 'XXX';";
        file_put_contents($langfolder . '/moodle.php', $langconfig);

        $CFG->lang='en';
        $enuser = \core_user::get_noreply_user();

        $CFG->lang='xx';
        $xxuser = \core_user::get_noreply_user();

        $this->assertNotEquals($enuser, $xxuser);
    }

    /**
     * Test is_real_user method.
     */
    public function test_is_real_user() {
        global $CFG, $USER;

        // Real users are real users.
        $auser = $this->getDataGenerator()->create_user();
        $guest = guest_user();
        $this->assertTrue(\core_user::is_real_user($auser->id));
        $this->assertTrue(\core_user::is_real_user($auser->id, true));
        $this->assertTrue(\core_user::is_real_user($guest->id));
        $this->assertTrue(\core_user::is_real_user($guest->id, true));

        // Non-logged in users are not real users.
        $this->assertSame(0, $USER->id, 'The non-logged in user should have an ID of 0.');
        $this->assertFalse(\core_user::is_real_user($USER->id));
        $this->assertFalse(\core_user::is_real_user($USER->id, true));

        // Other types of logged in users are real users.
        $this->setAdminUser();
        $this->assertTrue(\core_user::is_real_user($USER->id));
        $this->assertTrue(\core_user::is_real_user($USER->id, true));
        $this->setGuestUser();
        $this->assertTrue(\core_user::is_real_user($USER->id));
        $this->assertTrue(\core_user::is_real_user($USER->id, true));
        $this->setUser($auser);
        $this->assertTrue(\core_user::is_real_user($USER->id));
        $this->assertTrue(\core_user::is_real_user($USER->id, true));

        // Fake accounts are not real users.
        $CFG->noreplyuserid = null;
        $this->assertFalse(\core_user::is_real_user(core_user::get_noreply_user()->id));
        $this->assertFalse(\core_user::is_real_user(core_user::get_noreply_user()->id, true));
        $CFG->supportuserid = null;
        $CFG->supportemail = 'test@example.com';
        $this->assertFalse(\core_user::is_real_user(core_user::get_support_user()->id));
        $this->assertFalse(\core_user::is_real_user(core_user::get_support_user()->id, true));
    }

}

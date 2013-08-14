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
 * Test non-plugin enrollib parts.
 *
 * @package    core
 * @category   phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Test non-plugin enrollib parts.
 *
 * @package    core
 * @category   phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_enrol_testcase extends advanced_testcase {

    public function test_enrol_get_all_users_courses() {
        global $DB, $CFG;

        $this->resetAfterTest();

        $studentrole = $DB->get_record('role', array('shortname'=>'student'));
        $this->assertNotEmpty($studentrole);
        $teacherrole = $DB->get_record('role', array('shortname'=>'teacher'));
        $this->assertNotEmpty($teacherrole);

        $admin = get_admin();
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        $user4 = $this->getDataGenerator()->create_user();
        $user5 = $this->getDataGenerator()->create_user();

        $category1 = $this->getDataGenerator()->create_category(array('visible'=>0));
        $category2 = $this->getDataGenerator()->create_category();
        $course1 = $this->getDataGenerator()->create_course(array('category'=>$category1->id));
        $course2 = $this->getDataGenerator()->create_course(array('category'=>$category2->id));
        $course3 = $this->getDataGenerator()->create_course(array('category'=>$category2->id, 'visible'=>0));
        $course4 = $this->getDataGenerator()->create_course(array('category'=>$category2->id));

        $maninstance1 = $DB->get_record('enrol', array('courseid'=>$course1->id, 'enrol'=>'manual'), '*', MUST_EXIST);
        $DB->set_field('enrol', 'status', ENROL_INSTANCE_DISABLED, array('id'=>$maninstance1->id));
        $maninstance1 = $DB->get_record('enrol', array('courseid'=>$course1->id, 'enrol'=>'manual'), '*', MUST_EXIST);
        $maninstance2 = $DB->get_record('enrol', array('courseid'=>$course2->id, 'enrol'=>'manual'), '*', MUST_EXIST);
        $maninstance3 = $DB->get_record('enrol', array('courseid'=>$course3->id, 'enrol'=>'manual'), '*', MUST_EXIST);
        $maninstance4 = $DB->get_record('enrol', array('courseid'=>$course4->id, 'enrol'=>'manual'), '*', MUST_EXIST);

        $manual = enrol_get_plugin('manual');
        $this->assertNotEmpty($manual);

        $manual->enrol_user($maninstance1, $user1->id, $teacherrole->id);
        $manual->enrol_user($maninstance1, $user2->id, $studentrole->id);
        $manual->enrol_user($maninstance1, $user4->id, $teacherrole->id, 0, 0, ENROL_USER_SUSPENDED);
        $manual->enrol_user($maninstance1, $admin->id, $studentrole->id);

        $manual->enrol_user($maninstance2, $user1->id);
        $manual->enrol_user($maninstance2, $user2->id);
        $manual->enrol_user($maninstance2, $user3->id, 0, 1, time()+(60*60));

        $manual->enrol_user($maninstance3, $user1->id);
        $manual->enrol_user($maninstance3, $user2->id);
        $manual->enrol_user($maninstance3, $user3->id, 0, 1, time()-(60*60));
        $manual->enrol_user($maninstance3, $user4->id, 0, 0, 0, ENROL_USER_SUSPENDED);


        $courses = enrol_get_all_users_courses($CFG->siteguest);
        $this->assertSame(array(), $courses);

        $courses = enrol_get_all_users_courses(0);
        $this->assertSame(array(), $courses);

        // Results are sorted by visibility, sortorder by default (in our case order of creation)

        $courses = enrol_get_all_users_courses($admin->id);
        $this->assertCount(1, $courses);
        $this->assertEquals(array($course1->id), array_keys($courses));

        $courses = enrol_get_all_users_courses($admin->id, true);
        $this->assertCount(0, $courses);
        $this->assertEquals(array(), array_keys($courses));

        $courses = enrol_get_all_users_courses($user1->id);
        $this->assertCount(3, $courses);
        $this->assertEquals(array($course2->id, $course1->id, $course3->id), array_keys($courses));

        $courses = enrol_get_all_users_courses($user1->id, true);
        $this->assertCount(2, $courses);
        $this->assertEquals(array($course2->id, $course3->id), array_keys($courses));

        $courses = enrol_get_all_users_courses($user2->id);
        $this->assertCount(3, $courses);
        $this->assertEquals(array($course2->id, $course1->id, $course3->id), array_keys($courses));

        $courses = enrol_get_all_users_courses($user2->id, true);
        $this->assertCount(2, $courses);
        $this->assertEquals(array($course2->id, $course3->id), array_keys($courses));

        $courses = enrol_get_all_users_courses($user3->id);
        $this->assertCount(2, $courses);
        $this->assertEquals(array($course2->id, $course3->id), array_keys($courses));

        $courses = enrol_get_all_users_courses($user3->id, true);
        $this->assertCount(1, $courses);
        $this->assertEquals(array($course2->id), array_keys($courses));

        $courses = enrol_get_all_users_courses($user4->id);
        $this->assertCount(2, $courses);
        $this->assertEquals(array($course1->id, $course3->id), array_keys($courses));

        $courses = enrol_get_all_users_courses($user4->id, true);
        $this->assertCount(0, $courses);
        $this->assertEquals(array(), array_keys($courses));

        // Make sure sorting and columns work.

        $basefields = array('id', 'category', 'sortorder', 'shortname', 'fullname', 'idnumber',
            'startdate', 'visible', 'groupmode', 'groupmodeforce');

        $courses = enrol_get_all_users_courses($user2->id, true);
        $course = reset($courses);
        context_helper::preload_from_record($course);
        $course = (array)$course;
        $this->assertEquals($basefields, array_keys($course), '', 0, 10, true);

        $courses = enrol_get_all_users_courses($user2->id, false, 'modinfo');
        $course = reset($courses);
        $this->assertTrue(property_exists($course, 'modinfo'));

        $courses = enrol_get_all_users_courses($user2->id, false, null, 'id DESC');
        $this->assertEquals(array($course3->id, $course2->id, $course1->id), array_keys($courses));
    }

    public function test_enrol_user_sees_own_courses() {
        global $DB, $CFG;

        $this->resetAfterTest();

        $studentrole = $DB->get_record('role', array('shortname'=>'student'));
        $this->assertNotEmpty($studentrole);
        $teacherrole = $DB->get_record('role', array('shortname'=>'teacher'));
        $this->assertNotEmpty($teacherrole);

        $admin = get_admin();
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        $user4 = $this->getDataGenerator()->create_user();
        $user5 = $this->getDataGenerator()->create_user();
        $user6 = $this->getDataGenerator()->create_user();

        $category1 = $this->getDataGenerator()->create_category(array('visible'=>0));
        $category2 = $this->getDataGenerator()->create_category();
        $course1 = $this->getDataGenerator()->create_course(array('category'=>$category1->id));
        $course2 = $this->getDataGenerator()->create_course(array('category'=>$category2->id));
        $course3 = $this->getDataGenerator()->create_course(array('category'=>$category2->id, 'visible'=>0));
        $course4 = $this->getDataGenerator()->create_course(array('category'=>$category2->id));

        $maninstance1 = $DB->get_record('enrol', array('courseid'=>$course1->id, 'enrol'=>'manual'), '*', MUST_EXIST);
        $DB->set_field('enrol', 'status', ENROL_INSTANCE_DISABLED, array('id'=>$maninstance1->id));
        $maninstance1 = $DB->get_record('enrol', array('courseid'=>$course1->id, 'enrol'=>'manual'), '*', MUST_EXIST);
        $maninstance2 = $DB->get_record('enrol', array('courseid'=>$course2->id, 'enrol'=>'manual'), '*', MUST_EXIST);
        $maninstance3 = $DB->get_record('enrol', array('courseid'=>$course3->id, 'enrol'=>'manual'), '*', MUST_EXIST);
        $maninstance4 = $DB->get_record('enrol', array('courseid'=>$course4->id, 'enrol'=>'manual'), '*', MUST_EXIST);

        $manual = enrol_get_plugin('manual');
        $this->assertNotEmpty($manual);

        $manual->enrol_user($maninstance1, $admin->id, $studentrole->id);

        $manual->enrol_user($maninstance3, $user1->id, $teacherrole->id);

        $manual->enrol_user($maninstance2, $user2->id, $studentrole->id);

        $manual->enrol_user($maninstance1, $user3->id, $studentrole->id, 1, time()+(60*60));
        $manual->enrol_user($maninstance2, $user3->id, 0, 1, time()-(60*60));
        $manual->enrol_user($maninstance3, $user2->id, $studentrole->id);
        $manual->enrol_user($maninstance4, $user2->id, 0, 0, 0, ENROL_USER_SUSPENDED);

        $manual->enrol_user($maninstance1, $user4->id, $teacherrole->id, 0, 0, ENROL_USER_SUSPENDED);
        $manual->enrol_user($maninstance3, $user4->id, 0, 0, 0, ENROL_USER_SUSPENDED);


        $this->assertFalse(enrol_user_sees_own_courses($CFG->siteguest));
        $this->assertFalse(enrol_user_sees_own_courses(0));
        $this->assertFalse(enrol_user_sees_own_courses($admin));
        $this->assertFalse(enrol_user_sees_own_courses(-222)); // Nonexistent user.

        $this->assertTrue(enrol_user_sees_own_courses($user1));
        $this->assertTrue(enrol_user_sees_own_courses($user2->id));
        $this->assertFalse(enrol_user_sees_own_courses($user3->id));
        $this->assertFalse(enrol_user_sees_own_courses($user4));
        $this->assertFalse(enrol_user_sees_own_courses($user5));

        $this->setAdminUser();
        $this->assertFalse(enrol_user_sees_own_courses());

        $this->setGuestUser();
        $this->assertFalse(enrol_user_sees_own_courses());

        $this->setUser(0);
        $this->assertFalse(enrol_user_sees_own_courses());

        $this->setUser($user1);
        $this->assertTrue(enrol_user_sees_own_courses());

        $this->setUser($user2);
        $this->assertTrue(enrol_user_sees_own_courses());

        $this->setUser($user3);
        $this->assertFalse(enrol_user_sees_own_courses());

        $this->setUser($user4);
        $this->assertFalse(enrol_user_sees_own_courses());

        $this->setUser($user5);
        $this->assertFalse(enrol_user_sees_own_courses());

        $user1 = $DB->get_record('user', array('id'=>$user1->id));
        $this->setUser($user1);
        $reads = $DB->perf_get_reads();
        $this->assertTrue(enrol_user_sees_own_courses());
        $this->assertGreaterThan($reads, $DB->perf_get_reads());

        $user1 = $DB->get_record('user', array('id'=>$user1->id));
        $this->setUser($user1);
        require_login($course3);
        $reads = $DB->perf_get_reads();
        $this->assertTrue(enrol_user_sees_own_courses());
        $this->assertEquals($reads, $DB->perf_get_reads());
    }

    public function test_enrol_get_shared_courses() {
        $this->resetAfterTest();

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();

        $course1 = $this->getDataGenerator()->create_course();
        $this->getDataGenerator()->enrol_user($user1->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course1->id);

        $course2 = $this->getDataGenerator()->create_course();
        $this->getDataGenerator()->enrol_user($user1->id, $course2->id);

        // Test that user1 and user2 have courses in common.
        $this->assertTrue(enrol_get_shared_courses($user1, $user2, false, true));
        // Test that user1 and user3 have no courses in common.
        $this->assertFalse(enrol_get_shared_courses($user1, $user3, false, true));

        // Test retrieving the courses in common.
        $sharedcourses = enrol_get_shared_courses($user1, $user2, true);

        // Only should be one shared course.
        $this->assertCount(1, $sharedcourses);
        $sharedcourse = array_shift($sharedcourses);
        // It should be course 1.
        $this->assertEquals($sharedcourse->id, $course1->id);
    }
}

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
 * PHPUnit integration tests
 *
 * @package    core
 * @category   phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Test data generator
 *
 * @package    core
 * @category   phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_phpunit_generator_testcase extends advanced_testcase {
    public function test_create() {
        global $DB;

        $this->resetAfterTest(true);
        $generator = $this->getDataGenerator();

        $count = $DB->count_records('user');
        $user = $generator->create_user();
        $this->assertEquals($count+1, $DB->count_records('user'));
        $this->assertSame($user->username, clean_param($user->username, PARAM_USERNAME));
        $this->assertSame($user->email, clean_param($user->email, PARAM_EMAIL));
        $user = $generator->create_user(array('firstname'=>'Žluťoučký', 'lastname'=>'Koníček'));
        $this->assertSame($user->username, clean_param($user->username, PARAM_USERNAME));
        $this->assertSame($user->email, clean_param($user->email, PARAM_EMAIL));

        $count = $DB->count_records('course_categories');
        $category = $generator->create_category();
        $this->assertEquals($count+1, $DB->count_records('course_categories'));
        $this->assertRegExp('/^Course category \d/', $category->name);
        $this->assertSame('', $category->idnumber);
        $this->assertRegExp('/^Test course category \d/', $category->description);
        $this->assertSame(FORMAT_MOODLE, $category->descriptionformat);

        $count = $DB->count_records('cohort');
        $cohort = $generator->create_cohort();
        $this->assertEquals($count+1, $DB->count_records('cohort'));
        $this->assertEquals(context_system::instance()->id, $cohort->contextid);
        $this->assertRegExp('/^Cohort \d/', $cohort->name);
        $this->assertSame('', $cohort->idnumber);
        $this->assertRegExp('/^Test cohort \d/', $cohort->description);
        $this->assertSame(FORMAT_MOODLE, $cohort->descriptionformat);
        $this->assertSame('', $cohort->component);
        $this->assertLessThanOrEqual(time(), $cohort->timecreated);
        $this->assertSame($cohort->timecreated, $cohort->timemodified);

        $count = $DB->count_records('course');
        $course = $generator->create_course();
        $this->assertEquals($count+1, $DB->count_records('course'));
        $this->assertRegExp('/^Test course \d/', $course->fullname);
        $this->assertRegExp('/^tc_\d/', $course->shortname);
        $this->assertSame('', $course->idnumber);
        $this->assertSame('topics', $course->format);
        $this->assertEquals(0, $course->newsitems);
        $this->assertEquals(5, $course->numsections);
        $this->assertRegExp('/^Test course \d/', $course->summary);
        $this->assertSame(FORMAT_MOODLE, $course->summaryformat);

        $section = $generator->create_course_section(array('course'=>$course->id, 'section'=>3));
        $this->assertEquals($course->id, $section->course);

        $scale = $generator->create_scale();
        $this->assertNotEmpty($scale);
    }

    public function test_create_module() {
        global $CFG, $SITE;
        if (!file_exists("$CFG->dirroot/mod/page/")) {
            $this->markTestSkipped('Can not find standard Page module');
        }

        $this->resetAfterTest(true);
        $generator = $this->getDataGenerator();

        $page = $generator->create_module('page', array('course'=>$SITE->id));
        $this->assertNotEmpty($page);
        $cm = get_coursemodule_from_instance('page', $page->id, $SITE->id, true);
        $this->assertEquals(0, $cm->sectionnum);

        $page = $generator->create_module('page', array('course'=>$SITE->id), array('section'=>3));
        $this->assertNotEmpty($page);
        $cm = get_coursemodule_from_instance('page', $page->id, $SITE->id, true);
        $this->assertEquals(3, $cm->sectionnum);
    }

    public function test_create_block() {
        global $CFG;
        if (!file_exists("$CFG->dirroot/blocks/online_users/")) {
            $this->markTestSkipped('Can not find standard Online users block');
        }

        $this->resetAfterTest(true);
        $generator = $this->getDataGenerator();

        $page = $generator->create_block('online_users');
        $this->assertNotEmpty($page);
    }

    public function test_enrol_user() {
        global $DB;

        $this->resetAfterTest();

        $selfplugin = enrol_get_plugin('self');
        $this->assertNotEmpty($selfplugin);

        $manualplugin = enrol_get_plugin('manual');
        $this->assertNotEmpty($manualplugin);

        // Prepare some data.

        $studentrole = $DB->get_record('role', array('shortname'=>'student'));
        $this->assertNotEmpty($studentrole);
        $teacherrole = $DB->get_record('role', array('shortname'=>'teacher'));
        $this->assertNotEmpty($teacherrole);

        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $course3 = $this->getDataGenerator()->create_course();

        $context1 = context_course::instance($course1->id);
        $context2 = context_course::instance($course2->id);
        $context3 = context_course::instance($course3->id);

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();

        $this->assertEquals(3, $DB->count_records('enrol', array('enrol'=>'self')));
        $instance1 = $DB->get_record('enrol', array('courseid'=>$course1->id, 'enrol'=>'self'), '*', MUST_EXIST);
        $instance2 = $DB->get_record('enrol', array('courseid'=>$course2->id, 'enrol'=>'self'), '*', MUST_EXIST);
        $instance3 = $DB->get_record('enrol', array('courseid'=>$course3->id, 'enrol'=>'self'), '*', MUST_EXIST);

        $this->assertEquals($studentrole->id, $instance1->roleid);
        $this->assertEquals($studentrole->id, $instance2->roleid);
        $this->assertEquals($studentrole->id, $instance3->roleid);

        $this->assertEquals(3, $DB->count_records('enrol', array('enrol'=>'manual')));
        $maninstance1 = $DB->get_record('enrol', array('courseid'=>$course1->id, 'enrol'=>'manual'), '*', MUST_EXIST);
        $maninstance2 = $DB->get_record('enrol', array('courseid'=>$course2->id, 'enrol'=>'manual'), '*', MUST_EXIST);
        $maninstance3 = $DB->get_record('enrol', array('courseid'=>$course3->id, 'enrol'=>'manual'), '*', MUST_EXIST);
        $maninstance3->roleid = $teacherrole->id;
        $DB->update_record('enrol', $maninstance3, array('id'=>$maninstance3->id));

        $this->assertEquals($studentrole->id, $maninstance1->roleid);
        $this->assertEquals($studentrole->id, $maninstance2->roleid);
        $this->assertEquals($teacherrole->id, $maninstance3->roleid);

        $result = $this->getDataGenerator()->enrol_user($user1->id, $course1->id);
        $this->assertTrue($result);
        $this->assertTrue($DB->record_exists('user_enrolments', array('enrolid'=>$maninstance1->id, 'userid'=>$user1->id)));
        $this->assertTrue($DB->record_exists('role_assignments', array('contextid'=>$context1->id, 'userid'=>$user1->id, 'roleid'=>$studentrole->id)));

        $result = $this->getDataGenerator()->enrol_user($user1->id, $course2->id, $teacherrole->id, 'manual');
        $this->assertTrue($result);
        $this->assertTrue($DB->record_exists('user_enrolments', array('enrolid'=>$maninstance2->id, 'userid'=>$user1->id)));
        $this->assertTrue($DB->record_exists('role_assignments', array('contextid'=>$context2->id, 'userid'=>$user1->id, 'roleid'=>$teacherrole->id)));

        $result = $this->getDataGenerator()->enrol_user($user1->id, $course3->id, 0, 'manual');
        $this->assertTrue($result);
        $this->assertTrue($DB->record_exists('user_enrolments', array('enrolid'=>$maninstance3->id, 'userid'=>$user1->id)));
        $this->assertFalse($DB->record_exists('role_assignments', array('contextid'=>$context3->id, 'userid'=>$user1->id)));


        $result = $this->getDataGenerator()->enrol_user($user2->id, $course1->id, null, 'self');
        $this->assertTrue($result);
        $this->assertTrue($DB->record_exists('user_enrolments', array('enrolid'=>$instance1->id, 'userid'=>$user2->id)));
        $this->assertTrue($DB->record_exists('role_assignments', array('contextid'=>$context1->id, 'userid'=>$user2->id, 'roleid'=>$studentrole->id)));


        $selfplugin->add_instance($course2, array('status'=>ENROL_INSTANCE_ENABLED, 'roleid'=>$teacherrole->id));
        $result = $this->getDataGenerator()->enrol_user($user2->id, $course2->id, null, 'self');
        $this->assertFalse($result);

        $DB->delete_records('enrol', array('enrol'=>'self', 'courseid'=>$course3->id));
        $result = $this->getDataGenerator()->enrol_user($user2->id, $course3->id, null, 'self');
        $this->assertFalse($result);

    }
}

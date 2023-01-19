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
 * locallib tests.
 *
 * @package    mod_lesson
 * @category   test
 * @copyright  2016 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_lesson;

use lesson;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot.'/mod/lesson/locallib.php');

/**
 * locallib testcase.
 *
 * @package    mod_lesson
 * @category   test
 * @copyright  2016 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class locallib_test extends \advanced_testcase {

    /**
     * Test duplicating a lesson page element.
     */
    public function test_duplicate_page() {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $lessonmodule = $this->getDataGenerator()->create_module('lesson', array('course' => $course->id));
        // Convert to a lesson object.
        $lesson = new lesson($lessonmodule);

        // Set up a generator to create content.
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_lesson');
        $tfrecord = $generator->create_question_truefalse($lesson);
        $lesson->duplicate_page($tfrecord->id);

        // Lesson pages.
        $records = $DB->get_records('lesson_pages', array('qtype' => 2));
        $sameelements = array('lessonid', 'qtype', 'qoption', 'layout', 'display', 'title', 'contents', 'contentsformat');
        $baserecord = array_shift($records);
        $secondrecord = array_shift($records);
        foreach ($sameelements as $element) {
            $this->assertEquals($baserecord->$element, $secondrecord->$element);
        }
        // Need lesson answers as well.
        $baserecordanswers = array_values($DB->get_records('lesson_answers', array('pageid' => $baserecord->id)));
        $secondrecordanswers = array_values($DB->get_records('lesson_answers', array('pageid' => $secondrecord->id)));
        $sameanswerelements = array('lessonid', 'jumpto', 'grade', 'score', 'flags', 'answer', 'answerformat', 'response',
                'responseformat');
        foreach ($baserecordanswers as $key => $baseanswer) {
            foreach ($sameanswerelements as $element) {
                $this->assertEquals($baseanswer->$element, $secondrecordanswers[$key]->$element);
            }
        }
    }

    /**
     * Test test_lesson_get_user_deadline().
     */
    public function test_lesson_get_user_deadline() {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        $basetimestamp = time(); // The timestamp we will base the enddates on.

        // Create generator, course and lessons.
        $student1 = $this->getDataGenerator()->create_user();
        $student2 = $this->getDataGenerator()->create_user();
        $student3 = $this->getDataGenerator()->create_user();
        $teacher = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $lessongenerator = $this->getDataGenerator()->get_plugin_generator('mod_lesson');

        // Both lessons close in two hours.
        $lesson1 = $lessongenerator->create_instance(array('course' => $course->id, 'deadline' => $basetimestamp + 7200));
        $lesson2 = $lessongenerator->create_instance(array('course' => $course->id, 'deadline' => $basetimestamp + 7200));
        $group1 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        $group2 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));

        $student1id = $student1->id;
        $student2id = $student2->id;
        $student3id = $student3->id;
        $teacherid = $teacher->id;

        // Users enrolments.
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));
        $this->getDataGenerator()->enrol_user($student1id, $course->id, $studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($student2id, $course->id, $studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($student3id, $course->id, $studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($teacherid, $course->id, $teacherrole->id, 'manual');

        // Create groups.
        $group1 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        $group2 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        $group1id = $group1->id;
        $group2id = $group2->id;
        $this->getDataGenerator()->create_group_member(array('userid' => $student1id, 'groupid' => $group1id));
        $this->getDataGenerator()->create_group_member(array('userid' => $student2id, 'groupid' => $group2id));

        // Group 1 gets an group override for lesson 1 to close in three hours.
        $record1 = (object) [
            'lessonid' => $lesson1->id,
            'groupid' => $group1id,
            'deadline' => $basetimestamp + 10800 // In three hours.
        ];
        $DB->insert_record('lesson_overrides', $record1);

        // Let's test lesson 1 closes in three hours for user student 1 since member of group 1.
        // lesson 2 closes in two hours.
        $this->setUser($student1id);
        $params = new \stdClass();

        $comparearray = array();
        $object = new \stdClass();
        $object->id = $lesson1->id;
        $object->userdeadline = $basetimestamp + 10800; // The overriden deadline for lesson 1.

        $comparearray[$lesson1->id] = $object;

        $object = new \stdClass();
        $object->id = $lesson2->id;
        $object->userdeadline = $basetimestamp + 7200; // The unchanged deadline for lesson 2.

        $comparearray[$lesson2->id] = $object;

        $this->assertEquals($comparearray, lesson_get_user_deadline($course->id));

        // Let's test lesson 1 closes in two hours (the original value) for user student 3 since member of no group.
        $this->setUser($student3id);
        $params = new \stdClass();

        $comparearray = array();
        $object = new \stdClass();
        $object->id = $lesson1->id;
        $object->userdeadline = $basetimestamp + 7200; // The original deadline for lesson 1.

        $comparearray[$lesson1->id] = $object;

        $object = new \stdClass();
        $object->id = $lesson2->id;
        $object->userdeadline = $basetimestamp + 7200; // The original deadline for lesson 2.

        $comparearray[$lesson2->id] = $object;

        $this->assertEquals($comparearray, lesson_get_user_deadline($course->id));

        // User 2 gets an user override for lesson 1 to close in four hours.
        $record2 = (object) [
            'lessonid' => $lesson1->id,
            'userid' => $student2id,
            'deadline' => $basetimestamp + 14400 // In four hours.
        ];
        $DB->insert_record('lesson_overrides', $record2);

        // Let's test lesson 1 closes in four hours for user student 2 since personally overriden.
        // lesson 2 closes in two hours.
        $this->setUser($student2id);

        $comparearray = array();
        $object = new \stdClass();
        $object->id = $lesson1->id;
        $object->userdeadline = $basetimestamp + 14400; // The overriden deadline for lesson 1.

        $comparearray[$lesson1->id] = $object;

        $object = new \stdClass();
        $object->id = $lesson2->id;
        $object->userdeadline = $basetimestamp + 7200; // The unchanged deadline for lesson 2.

        $comparearray[$lesson2->id] = $object;

        $this->assertEquals($comparearray, lesson_get_user_deadline($course->id));

        // Let's test a teacher sees the original times.
        // lesson 1 and lesson 2 close in two hours.
        $this->setUser($teacherid);

        $comparearray = array();
        $object = new \stdClass();
        $object->id = $lesson1->id;
        $object->userdeadline = $basetimestamp + 7200; // The unchanged deadline for lesson 1.

        $comparearray[$lesson1->id] = $object;

        $object = new \stdClass();
        $object->id = $lesson2->id;
        $object->userdeadline = $basetimestamp + 7200; // The unchanged deadline for lesson 2.

        $comparearray[$lesson2->id] = $object;

        $this->assertEquals($comparearray, lesson_get_user_deadline($course->id));
    }

    public function test_is_participant() {
        global $USER, $DB;
        $this->resetAfterTest();
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $student2 = $this->getDataGenerator()->create_and_enrol($course, 'student', [], 'manual', 0, 0, ENROL_USER_SUSPENDED);
        $lessonmodule = $this->getDataGenerator()->create_module('lesson', array('course' => $course->id));

        // Login as student.
        $this->setUser($student);
        // Convert to a lesson object.
        $lesson = new lesson($lessonmodule);
        $this->assertEquals(true, $lesson->is_participant($student->id),
            'Student is enrolled, active and can participate');

        // Login as student2.
        $this->setUser($student2);
        $this->assertEquals(false, $lesson->is_participant($student2->id),
            'Student is enrolled, suspended and can NOT participate');

        // Login as an admin.
        $this->setAdminUser();
        $this->assertEquals(false, $lesson->is_participant($USER->id),
            'Admin is not enrolled and can NOT participate');

        $this->getDataGenerator()->enrol_user(2, $course->id);
        $this->assertEquals(true, $lesson->is_participant($USER->id),
            'Admin is enrolled and can participate');

        $this->getDataGenerator()->enrol_user(2, $course->id, [], 'manual', 0, 0, ENROL_USER_SUSPENDED);
        $this->assertEquals(true, $lesson->is_participant($USER->id),
            'Admin is enrolled, suspended and can participate');
    }

    /**
     * Data provider for test_get_last_attempt.
     *
     * @return array
     */
    public function get_last_attempt_dataprovider() {
        return [
            [0, [(object)['id' => 1], (object)['id' => 2], (object)['id' => 3]], (object)['id' => 3]],
            [1, [(object)['id' => 1], (object)['id' => 2], (object)['id' => 3]], (object)['id' => 1]],
            [2, [(object)['id' => 1], (object)['id' => 2], (object)['id' => 3]], (object)['id' => 2]],
            [3, [(object)['id' => 1], (object)['id' => 2], (object)['id' => 3]], (object)['id' => 3]],
            [4, [(object)['id' => 1], (object)['id' => 2], (object)['id' => 3]], (object)['id' => 3]],
        ];
    }

    /**
     * Test the get_last_attempt() method.
     *
     * @dataProvider get_last_attempt_dataprovider
     * @param int $maxattempts Lesson setting.
     * @param array $attempts The list of student attempts.
     * @param object $expected Expected result.
     */
    public function test_get_last_attempt($maxattempts, $attempts, $expected) {
        $this->resetAfterTest();
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $lesson = $this->getDataGenerator()->create_module('lesson', ['course' => $course, 'maxattempts' => $maxattempts]);
        $lesson = new lesson($lesson);
        $this->assertEquals($expected, $lesson->get_last_attempt($attempts));
    }
}

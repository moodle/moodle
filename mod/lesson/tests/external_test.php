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
 * Lesson module external functions tests
 *
 * @package    mod_lesson
 * @category   external
 * @copyright  2017 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.3
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');
require_once($CFG->dirroot . '/mod/lesson/locallib.php');

/**
 * Lesson module external functions tests
 *
 * @package    mod_lesson
 * @category   external
 * @copyright  2017 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.3
 */
class mod_lesson_external_testcase extends externallib_advanced_testcase {

    /**
     * Set up for every test
     */
    public function setUp() {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        // Setup test data.
        $this->course = $this->getDataGenerator()->create_course();
        $this->lesson = $this->getDataGenerator()->create_module('lesson', array('course' => $this->course->id));
        $this->context = context_module::instance($this->lesson->cmid);
        $this->cm = get_coursemodule_from_instance('lesson', $this->lesson->id);

        // Create users.
        $this->student = self::getDataGenerator()->create_user();
        $this->teacher = self::getDataGenerator()->create_user();

        // Users enrolments.
        $this->studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));
        $this->getDataGenerator()->enrol_user($this->student->id, $this->course->id, $this->studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($this->teacher->id, $this->course->id, $this->teacherrole->id, 'manual');
    }


    /**
     * Test test_mod_lesson_get_lessons_by_courses
     */
    public function test_mod_lesson_get_lessons_by_courses() {
        global $DB;

        // Create additional course.
        $course2 = self::getDataGenerator()->create_course();

        // Second lesson.
        $record = new stdClass();
        $record->course = $course2->id;
        $lesson2 = self::getDataGenerator()->create_module('lesson', $record);

        // Execute real Moodle enrolment as we'll call unenrol() method on the instance later.
        $enrol = enrol_get_plugin('manual');
        $enrolinstances = enrol_get_instances($course2->id, true);
        foreach ($enrolinstances as $courseenrolinstance) {
            if ($courseenrolinstance->enrol == "manual") {
                $instance2 = $courseenrolinstance;
                break;
            }
        }
        $enrol->enrol_user($instance2, $this->student->id, $this->studentrole->id);

        self::setUser($this->student);

        $returndescription = mod_lesson_external::get_lessons_by_courses_returns();

        // Create what we expect to be returned when querying the two courses.
        // First for the student user.
        $expectedfields = array('id', 'coursemodule', 'course', 'name', 'intro', 'introformat', 'introfiles', 'practice',
                                'modattempts', 'usepassword', 'grade', 'custom', 'ongoing', 'usemaxgrade',
                                'maxanswers', 'maxattempts', 'review', 'nextpagedefault', 'feedback', 'minquestions',
                                'maxpages', 'timelimit', 'retake', 'mediafile', 'mediafiles', 'mediaheight', 'mediawidth',
                                'mediaclose', 'slideshow', 'width', 'height', 'bgcolor', 'displayleft', 'displayleftif',
                                'progressbar');

        // Add expected coursemodule and data.
        $lesson1 = $this->lesson;
        $lesson1->coursemodule = $lesson1->cmid;
        $lesson1->introformat = 1;
        $lesson1->section = 0;
        $lesson1->visible = true;
        $lesson1->groupmode = 0;
        $lesson1->groupingid = 0;
        $lesson1->introfiles = [];
        $lesson1->mediafiles = [];

        $lesson2->coursemodule = $lesson2->cmid;
        $lesson2->introformat = 1;
        $lesson2->section = 0;
        $lesson2->visible = true;
        $lesson2->groupmode = 0;
        $lesson2->groupingid = 0;
        $lesson2->introfiles = [];
        $lesson2->mediafiles = [];

        foreach ($expectedfields as $field) {
            $expected1[$field] = $lesson1->{$field};
            $expected2[$field] = $lesson2->{$field};
        }

        $expectedlessons = array($expected2, $expected1);

        // Call the external function passing course ids.
        $result = mod_lesson_external::get_lessons_by_courses(array($course2->id, $this->course->id));
        $result = external_api::clean_returnvalue($returndescription, $result);

        $this->assertEquals($expectedlessons, $result['lessons']);
        $this->assertCount(0, $result['warnings']);

        // Call the external function without passing course id.
        $result = mod_lesson_external::get_lessons_by_courses();
        $result = external_api::clean_returnvalue($returndescription, $result);
        $this->assertEquals($expectedlessons, $result['lessons']);
        $this->assertCount(0, $result['warnings']);

        // Unenrol user from second course and alter expected lessons.
        $enrol->unenrol_user($instance2, $this->student->id);
        array_shift($expectedlessons);

        // Call the external function without passing course id.
        $result = mod_lesson_external::get_lessons_by_courses();
        $result = external_api::clean_returnvalue($returndescription, $result);
        $this->assertEquals($expectedlessons, $result['lessons']);

        // Call for the second course we unenrolled the user from, expected warning.
        $result = mod_lesson_external::get_lessons_by_courses(array($course2->id));
        $this->assertCount(1, $result['warnings']);
        $this->assertEquals('1', $result['warnings'][0]['warningcode']);
        $this->assertEquals($course2->id, $result['warnings'][0]['itemid']);

        // Now, try as a teacher for getting all the additional fields.
        self::setUser($this->teacher);

        $additionalfields = array('password', 'dependency', 'conditions', 'activitylink', 'available', 'deadline',
                                    'timemodified', 'completionendreached', 'completiontimespent');

        foreach ($additionalfields as $field) {
            $expectedlessons[0][$field] = $lesson1->{$field};
        }

        $result = mod_lesson_external::get_lessons_by_courses();
        $result = external_api::clean_returnvalue($returndescription, $result);
        $this->assertEquals($expectedlessons, $result['lessons']);

        // Admin also should get all the information.
        self::setAdminUser();

        $result = mod_lesson_external::get_lessons_by_courses(array($this->course->id));
        $result = external_api::clean_returnvalue($returndescription, $result);
        $this->assertEquals($expectedlessons, $result['lessons']);

        // Now, add a restriction.
        $this->setUser($this->student);
        $DB->set_field('lesson', 'usepassword', 1, array('id' => $lesson1->id));
        $DB->set_field('lesson', 'password', 'abc', array('id' => $lesson1->id));

        $lessons = mod_lesson_external::get_lessons_by_courses(array($this->course->id));
        $lessons = external_api::clean_returnvalue(mod_lesson_external::get_lessons_by_courses_returns(), $lessons);
        $this->assertFalse(isset($lessons['lessons'][0]['intro']));
    }

}

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
 * Silly class to access mod_lesson_external internal methods.
 *
 * @package mod_lesson
 * @copyright 2017 Juan Leyva <juan@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since  Moodle 3.3
 */
class testable_mod_lesson_external extends mod_lesson_external {

    /**
     * Validates a new attempt.
     *
     * @param  lesson  $lesson lesson instance
     * @param  array   $params request parameters
     * @param  boolean $return whether to return the errors or throw exceptions
     * @return [array          the errors (if return set to true)
     * @since  Moodle 3.3
     */
    public static function validate_attempt(lesson $lesson, $params, $return = false) {
        return parent::validate_attempt($lesson, $params, $return);
    }
}

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
        $lessongenerator = $this->getDataGenerator()->get_plugin_generator('mod_lesson');
        $this->page1 = $lessongenerator->create_content($this->lesson);
        $this->page2 = $lessongenerator->create_question_truefalse($this->lesson);
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

    /**
     * Test the validate_attempt function.
     */
    public function test_validate_attempt() {
        global $DB;

        $this->setUser($this->student);
        // Test deadline.
        $oldtime = time() - DAYSECS;
        $DB->set_field('lesson', 'deadline', $oldtime, array('id' => $this->lesson->id));

        $lesson = new lesson($DB->get_record('lesson', array('id' => $this->lesson->id)));
        $validation = testable_mod_lesson_external::validate_attempt($lesson, ['password' => ''], true);
        $this->assertEquals('lessonclosed', key($validation));
        $this->assertCount(1, $validation);

        // Test not available yet.
        $futuretime = time() + DAYSECS;
        $DB->set_field('lesson', 'deadline', 0, array('id' => $this->lesson->id));
        $DB->set_field('lesson', 'available', $futuretime, array('id' => $this->lesson->id));

        $lesson = new lesson($DB->get_record('lesson', array('id' => $this->lesson->id)));
        $validation = testable_mod_lesson_external::validate_attempt($lesson, ['password' => ''], true);
        $this->assertEquals('lessonopen', key($validation));
        $this->assertCount(1, $validation);

        // Test password.
        $DB->set_field('lesson', 'deadline', 0, array('id' => $this->lesson->id));
        $DB->set_field('lesson', 'available', 0, array('id' => $this->lesson->id));
        $DB->set_field('lesson', 'usepassword', 1, array('id' => $this->lesson->id));
        $DB->set_field('lesson', 'password', 'abc', array('id' => $this->lesson->id));

        $lesson = new lesson($DB->get_record('lesson', array('id' => $this->lesson->id)));
        $validation = testable_mod_lesson_external::validate_attempt($lesson, ['password' => ''], true);
        $this->assertEquals('passwordprotectedlesson', key($validation));
        $this->assertCount(1, $validation);

        $lesson = new lesson($DB->get_record('lesson', array('id' => $this->lesson->id)));
        $validation = testable_mod_lesson_external::validate_attempt($lesson, ['password' => 'abc'], true);
        $this->assertCount(0, $validation);

        // Dependencies.
        $record = new stdClass();
        $record->course = $this->course->id;
        $lesson2 = self::getDataGenerator()->create_module('lesson', $record);
        $DB->set_field('lesson', 'usepassword', 0, array('id' => $this->lesson->id));
        $DB->set_field('lesson', 'password', '', array('id' => $this->lesson->id));
        $DB->set_field('lesson', 'dependency', $lesson->id, array('id' => $this->lesson->id));

        $lesson = new lesson($DB->get_record('lesson', array('id' => $this->lesson->id)));
        $lesson->conditions = serialize((object) ['completed' => true, 'timespent' => 0, 'gradebetterthan' => 0]);
        $validation = testable_mod_lesson_external::validate_attempt($lesson, ['password' => ''], true);
        $this->assertEquals('completethefollowingconditions', key($validation));
        $this->assertCount(1, $validation);

        // Lesson withou pages.
        $lesson = new lesson($lesson2);
        $validation = testable_mod_lesson_external::validate_attempt($lesson, ['password' => ''], true);
        $this->assertEquals('lessonnotready2', key($validation));
        $this->assertCount(1, $validation);

        // Test retakes.
        $DB->set_field('lesson', 'dependency', 0, array('id' => $this->lesson->id));
        $DB->set_field('lesson', 'retake', 0, array('id' => $this->lesson->id));
        $record = [
            'lessonid' => $this->lesson->id,
            'userid' => $this->student->id,
            'grade' => 100,
            'late' => 0,
            'completed' => 1,
        ];
        $DB->insert_record('lesson_grades', (object) $record);
        $lesson = new lesson($DB->get_record('lesson', array('id' => $this->lesson->id)));
        $validation = testable_mod_lesson_external::validate_attempt($lesson, ['password' => ''], true);
        $this->assertEquals('noretake', key($validation));
        $this->assertCount(1, $validation);
    }

    /**
     * Test the get_lesson_access_information function.
     */
    public function test_get_lesson_access_information() {
        global $DB;

        $this->setUser($this->student);
        // Add previous attempt.
        $record = [
            'lessonid' => $this->lesson->id,
            'userid' => $this->student->id,
            'grade' => 100,
            'late' => 0,
            'completed' => 1,
        ];
        $DB->insert_record('lesson_grades', (object) $record);

        $result = mod_lesson_external::get_lesson_access_information($this->lesson->id);
        $result = external_api::clean_returnvalue(mod_lesson_external::get_lesson_access_information_returns(), $result);
        $this->assertFalse($result['canmanage']);
        $this->assertFalse($result['cangrade']);
        $this->assertFalse($result['canviewreports']);

        $this->assertFalse($result['leftduringtimedsession']);
        $this->assertEquals(1, $result['reviewmode']);
        $this->assertEquals(1, $result['attemptscount']);
        $this->assertEquals(0, $result['lastpageseen']);
        $this->assertEquals($this->page2->id, $result['firstpageid']);
        $this->assertCount(1, $result['preventaccessreasons']);
        $this->assertEquals('noretake', $result['preventaccessreasons'][0]['reason']);
        $this->assertEquals(null, $result['preventaccessreasons'][0]['data']);
        $this->assertEquals(get_string('noretake', 'lesson'), $result['preventaccessreasons'][0]['message']);

        // Now check permissions as admin.
        $this->setAdminUser();
        $result = mod_lesson_external::get_lesson_access_information($this->lesson->id);
        $result = external_api::clean_returnvalue(mod_lesson_external::get_lesson_access_information_returns(), $result);
        $this->assertTrue($result['canmanage']);
        $this->assertTrue($result['cangrade']);
        $this->assertTrue($result['canviewreports']);
    }

}

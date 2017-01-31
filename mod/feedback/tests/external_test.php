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
 * Feedback module external functions tests
 *
 * @package    mod_feedback
 * @category   external
 * @copyright  2017 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.3
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');

use mod_feedback\external\feedback_summary_exporter;

/**
 * Feedback module external functions tests
 *
 * @package    mod_feedback
 * @category   external
 * @copyright  2017 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.3
 */
class mod_feedback_external_testcase extends externallib_advanced_testcase {

    /**
     * Set up for every test
     */
    public function setUp() {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        // Setup test data.
        $this->course = $this->getDataGenerator()->create_course();
        $this->feedback = $this->getDataGenerator()->create_module('feedback', array('course' => $this->course->id));
        $this->context = context_module::instance($this->feedback->cmid);
        $this->cm = get_coursemodule_from_instance('feedback', $this->feedback->id);

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
     * Test test_mod_feedback_get_feedbacks_by_courses
     */
    public function test_mod_feedback_get_feedbacks_by_courses() {
        global $DB;

        // Create additional course.
        $course2 = self::getDataGenerator()->create_course();

        // Second feedback.
        $record = new stdClass();
        $record->course = $course2->id;
        $feedback2 = self::getDataGenerator()->create_module('feedback', $record);

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

        $returndescription = mod_feedback_external::get_feedbacks_by_courses_returns();

        // Create what we expect to be returned when querying the two courses.
        // First for the student user.
        $expectedfields = array('id', 'coursemodule', 'course', 'name', 'intro', 'introformat', 'introfiles', 'anonymous',
            'multiple_submit', 'autonumbering', 'page_after_submitformat', 'publish_stats', 'completionsubmit');

        $properties = feedback_summary_exporter::read_properties_definition();

        // Add expected coursemodule and data.
        $feedback1 = $this->feedback;
        $feedback1->coursemodule = $feedback1->cmid;
        $feedback1->introformat = 1;
        $feedback1->introfiles = [];

        $feedback2->coursemodule = $feedback2->cmid;
        $feedback2->introformat = 1;
        $feedback2->introfiles = [];

        foreach ($expectedfields as $field) {
            if (!empty($properties[$field]) && $properties[$field]['type'] == PARAM_BOOL) {
                $feedback1->{$field} = (bool) $feedback1->{$field};
                $feedback2->{$field} = (bool) $feedback2->{$field};
            }
            $expected1[$field] = $feedback1->{$field};
            $expected2[$field] = $feedback2->{$field};
        }

        $expectedfeedbacks = array($expected2, $expected1);

        // Call the external function passing course ids.
        $result = mod_feedback_external::get_feedbacks_by_courses(array($course2->id, $this->course->id));
        $result = external_api::clean_returnvalue($returndescription, $result);

        $this->assertEquals($expectedfeedbacks, $result['feedbacks']);
        $this->assertCount(0, $result['warnings']);

        // Call the external function without passing course id.
        $result = mod_feedback_external::get_feedbacks_by_courses();
        $result = external_api::clean_returnvalue($returndescription, $result);
        $this->assertEquals($expectedfeedbacks, $result['feedbacks']);
        $this->assertCount(0, $result['warnings']);

        // Unenrol user from second course and alter expected feedbacks.
        $enrol->unenrol_user($instance2, $this->student->id);
        array_shift($expectedfeedbacks);

        // Call the external function without passing course id.
        $result = mod_feedback_external::get_feedbacks_by_courses();
        $result = external_api::clean_returnvalue($returndescription, $result);
        $this->assertEquals($expectedfeedbacks, $result['feedbacks']);

        // Call for the second course we unenrolled the user from, expected warning.
        $result = mod_feedback_external::get_feedbacks_by_courses(array($course2->id));
        $this->assertCount(1, $result['warnings']);
        $this->assertEquals('1', $result['warnings'][0]['warningcode']);
        $this->assertEquals($course2->id, $result['warnings'][0]['itemid']);

        // Now, try as a teacher for getting all the additional fields.
        self::setUser($this->teacher);

        $additionalfields = array('email_notification', 'site_after_submit', 'page_after_submit', 'timeopen', 'timeclose',
            'timemodified', 'pageaftersubmitfiles');

        $feedback1->pageaftersubmitfiles = [];

        foreach ($additionalfields as $field) {
            if (!empty($properties[$field]) && $properties[$field]['type'] == PARAM_BOOL) {
                $feedback1->{$field} = (bool) $feedback1->{$field};
            }
            $expectedfeedbacks[0][$field] = $feedback1->{$field};
        }
        $expectedfeedbacks[0]['page_after_submitformat'] = 1;

        $result = mod_feedback_external::get_feedbacks_by_courses();
        $result = external_api::clean_returnvalue($returndescription, $result);
        $this->assertEquals($expectedfeedbacks, $result['feedbacks']);

        // Admin also should get all the information.
        self::setAdminUser();

        $result = mod_feedback_external::get_feedbacks_by_courses(array($this->course->id));
        $result = external_api::clean_returnvalue($returndescription, $result);
        $this->assertEquals($expectedfeedbacks, $result['feedbacks']);
    }
}

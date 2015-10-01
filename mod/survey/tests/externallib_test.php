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
 * Survey module external functions tests
 *
 * @package    mod_survey
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');
require_once($CFG->dirroot . '/mod/survey/lib.php');

/**
 * Survey module external functions tests
 *
 * @package    mod_survey
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */
class mod_survey_external_testcase extends externallib_advanced_testcase {

    /**
     * Set up for every test
     */
    public function setUp() {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        // Setup test data.
        $this->course = $this->getDataGenerator()->create_course();
        $this->survey = $this->getDataGenerator()->create_module('survey', array('course' => $this->course->id));
        $this->context = context_module::instance($this->survey->cmid);
        $this->cm = get_coursemodule_from_instance('survey', $this->survey->id);

        // Create users.
        $this->student = self::getDataGenerator()->create_user();
        $this->teacher = self::getDataGenerator()->create_user();

        // Users enrolments.
        $this->studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));
        $this->getDataGenerator()->enrol_user($this->student->id, $this->course->id, $this->studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($this->teacher->id, $this->course->id, $this->teacherrole->id, 'manual');
    }


    /*
     * Test get surveys by courses
     */
    public function test_mod_survey_get_surveys_by_courses() {
        global $DB;

        // Create additional course.
        $course2 = self::getDataGenerator()->create_course();

        // Second survey.
        $record = new stdClass();
        $record->course = $course2->id;
        $survey2 = self::getDataGenerator()->create_module('survey', $record);
        // Force empty intro.
        $DB->set_field('survey', 'intro', '', array('id' => $survey2->id));

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

        $returndescription = mod_survey_external::get_surveys_by_courses_returns();

        // Create what we expect to be returned when querying the two courses.
        // First for the student user.
        $expectedfields = array('id', 'coursemodule', 'course', 'name', 'intro', 'introformat', 'template', 'days', 'questions',
                                    'surveydone');

        // Add expected coursemodule and data.
        $survey1 = $this->survey;
        $survey1->coursemodule = $survey1->cmid;
        $survey1->introformat = 1;
        $survey1->surveydone = 0;
        $survey1->section = 0;
        $survey1->visible = true;
        $survey1->groupmode = 0;
        $survey1->groupingid = 0;

        $survey2->coursemodule = $survey2->cmid;
        $survey2->introformat = 1;
        $survey2->surveydone = 0;
        $survey2->section = 0;
        $survey2->visible = true;
        $survey2->groupmode = 0;
        $survey2->groupingid = 0;
        $tempo = $DB->get_field("survey", "intro", array("id" => $survey2->template));
        $survey2->intro = nl2br(get_string($tempo, "survey"));

        foreach ($expectedfields as $field) {
                $expected1[$field] = $survey1->{$field};
                $expected2[$field] = $survey2->{$field};
        }

        $expectedsurveys = array($expected2, $expected1);

        // Call the external function passing course ids.
        $result = mod_survey_external::get_surveys_by_courses(array($course2->id, $this->course->id));
        $result = external_api::clean_returnvalue($returndescription, $result);

        $this->assertEquals($expectedsurveys, $result['surveys']);
        $this->assertCount(0, $result['warnings']);

        // Call the external function without passing course id.
        $result = mod_survey_external::get_surveys_by_courses();
        $result = external_api::clean_returnvalue($returndescription, $result);
        $this->assertEquals($expectedsurveys, $result['surveys']);
        $this->assertCount(0, $result['warnings']);

        // Unenrol user from second course and alter expected surveys.
        $enrol->unenrol_user($instance2, $this->student->id);
        array_shift($expectedsurveys);

        // Call the external function without passing course id.
        $result = mod_survey_external::get_surveys_by_courses();
        $result = external_api::clean_returnvalue($returndescription, $result);
        $this->assertEquals($expectedsurveys, $result['surveys']);

        // Call for the second course we unenrolled the user from, expected warning.
        $result = mod_survey_external::get_surveys_by_courses(array($course2->id));
        $this->assertCount(1, $result['warnings']);
        $this->assertEquals('1', $result['warnings'][0]['warningcode']);
        $this->assertEquals($course2->id, $result['warnings'][0]['itemid']);

        // Now, try as a teacher for getting all the additional fields.
        self::setUser($this->teacher);

        $additionalfields = array('timecreated', 'timemodified', 'section', 'visible', 'groupmode', 'groupingid');

        foreach ($additionalfields as $field) {
                $expectedsurveys[0][$field] = $survey1->{$field};
        }

        $result = mod_survey_external::get_surveys_by_courses();
        $result = external_api::clean_returnvalue($returndescription, $result);
        $this->assertEquals($expectedsurveys, $result['surveys']);

        // Admin also should get all the information.
        self::setAdminUser();

        $result = mod_survey_external::get_surveys_by_courses(array($this->course->id));
        $result = external_api::clean_returnvalue($returndescription, $result);
        $this->assertEquals($expectedsurveys, $result['surveys']);

        // Now, prohibit capabilities.
        $this->setUser($this->student);
        $contextcourse1 = context_course::instance($this->course->id);
        // Prohibit capability = mod/survey:participate on Course1 for students.
        assign_capability('mod/survey:participate', CAP_PROHIBIT, $this->studentrole->id, $contextcourse1->id);
        accesslib_clear_all_caches_for_unit_testing();

        $surveys = mod_survey_external::get_surveys_by_courses(array($this->course->id));
        $surveys = external_api::clean_returnvalue(mod_survey_external::get_surveys_by_courses_returns(), $surveys);
        $this->assertFalse(isset($surveys['surveys'][0]['intro']));
    }

}

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
 * External tool module external functions tests
 *
 * @package    mod_lti
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');
require_once($CFG->dirroot . '/mod/lti/lib.php');

/**
 * External tool module external functions tests
 *
 * @package    mod_lti
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */
class mod_lti_external_testcase extends externallib_advanced_testcase {

    /**
     * Set up for every test
     */
    public function setUp() {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        // Setup test data.
        $this->course = $this->getDataGenerator()->create_course();
        $this->lti = $this->getDataGenerator()->create_module('lti', array('course' => $this->course->id));
        $this->context = context_module::instance($this->lti->cmid);
        $this->cm = get_coursemodule_from_instance('lti', $this->lti->id);

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
     * Test view_lti
     */
    public function test_get_tool_launch_data() {
        global $USER;

        $result = mod_lti_external::get_tool_launch_data($this->lti->id);
        $result = external_api::clean_returnvalue(mod_lti_external::get_tool_launch_data_returns(), $result);

        // Basic test, the function returns what it's expected.
        self::assertEquals($this->lti->toolurl, $result['endpoint']);
        self::assertCount(35, $result['parameters']);

        // Check some parameters.
        $parameters = array();
        foreach ($result['parameters'] as $param) {
            $parameters[$param['name']] = $param['value'];
        }
        self::assertEquals($this->lti->resourcekey, $parameters['oauth_consumer_key']);
        self::assertEquals($this->course->fullname, $parameters['context_title']);
        self::assertEquals($this->course->shortname, $parameters['context_label']);
        self::assertEquals($USER->id, $parameters['user_id']);
        self::assertEquals($USER->firstname, $parameters['lis_person_name_given']);
        self::assertEquals($USER->lastname, $parameters['lis_person_name_family']);
        self::assertEquals(fullname($USER), $parameters['lis_person_name_full']);
        self::assertEquals($USER->username, $parameters['ext_user_username']);

    }

    /*
     * Test get ltis by courses
     */
    public function test_mod_lti_get_ltis_by_courses() {
        global $DB;

        // Create additional course.
        $course2 = self::getDataGenerator()->create_course();

        // Second lti.
        $record = new stdClass();
        $record->course = $course2->id;
        $lti2 = self::getDataGenerator()->create_module('lti', $record);

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

        $returndescription = mod_lti_external::get_ltis_by_courses_returns();

        // Create what we expect to be returned when querying the two courses.
        // First for the student user.
        $expectedfields = array('id', 'coursemodule', 'course', 'name', 'intro', 'introformat', 'launchcontainer',
                                'showtitlelaunch', 'showdescriptionlaunch', 'icon', 'secureicon');

        // Add expected coursemodule and data.
        $lti1 = $this->lti;
        $lti1->coursemodule = $lti1->cmid;
        $lti1->introformat = 1;
        $lti1->section = 0;
        $lti1->visible = true;
        $lti1->groupmode = 0;
        $lti1->groupingid = 0;

        $lti2->coursemodule = $lti2->cmid;
        $lti2->introformat = 1;
        $lti2->section = 0;
        $lti2->visible = true;
        $lti2->groupmode = 0;
        $lti2->groupingid = 0;

        foreach ($expectedfields as $field) {
                $expected1[$field] = $lti1->{$field};
                $expected2[$field] = $lti2->{$field};
        }

        $expectedltis = array($expected2, $expected1);

        // Call the external function passing course ids.
        $result = mod_lti_external::get_ltis_by_courses(array($course2->id, $this->course->id));
        $result = external_api::clean_returnvalue($returndescription, $result);

        $this->assertEquals($expectedltis, $result['ltis']);
        $this->assertCount(0, $result['warnings']);

        // Call the external function without passing course id.
        $result = mod_lti_external::get_ltis_by_courses();
        $result = external_api::clean_returnvalue($returndescription, $result);
        $this->assertEquals($expectedltis, $result['ltis']);
        $this->assertCount(0, $result['warnings']);

        // Unenrol user from second course and alter expected ltis.
        $enrol->unenrol_user($instance2, $this->student->id);
        array_shift($expectedltis);

        // Call the external function without passing course id.
        $result = mod_lti_external::get_ltis_by_courses();
        $result = external_api::clean_returnvalue($returndescription, $result);
        $this->assertEquals($expectedltis, $result['ltis']);

        // Call for the second course we unenrolled the user from, expected warning.
        $result = mod_lti_external::get_ltis_by_courses(array($course2->id));
        $this->assertCount(1, $result['warnings']);
        $this->assertEquals('1', $result['warnings'][0]['warningcode']);
        $this->assertEquals($course2->id, $result['warnings'][0]['itemid']);

        // Now, try as a teacher for getting all the additional fields.
        self::setUser($this->teacher);

        $additionalfields = array('timecreated', 'timemodified', 'typeid', 'toolurl', 'securetoolurl',
                        'instructorchoicesendname', 'instructorchoicesendemailaddr', 'instructorchoiceallowroster',
                        'instructorchoiceallowsetting', 'instructorcustomparameters', 'instructorchoiceacceptgrades', 'grade',
                        'resourcekey', 'password', 'debuglaunch', 'servicesalt', 'visible', 'groupmode', 'groupingid');

        foreach ($additionalfields as $field) {
                $expectedltis[0][$field] = $lti1->{$field};
        }

        $result = mod_lti_external::get_ltis_by_courses();
        $result = external_api::clean_returnvalue($returndescription, $result);
        $this->assertEquals($expectedltis, $result['ltis']);

        // Admin also should get all the information.
        self::setAdminUser();

        $result = mod_lti_external::get_ltis_by_courses(array($this->course->id));
        $result = external_api::clean_returnvalue($returndescription, $result);
        $this->assertEquals($expectedltis, $result['ltis']);

        // Now, prohibit capabilities.
        $this->setUser($this->student);
        $contextcourse1 = context_course::instance($this->course->id);
        // Prohibit capability = mod:lti:view on Course1 for students.
        assign_capability('mod/lti:view', CAP_PROHIBIT, $this->studentrole->id, $contextcourse1->id);
        // Empty all the caches that may be affected by this change.
        accesslib_clear_all_caches_for_unit_testing();
        course_modinfo::clear_instance_cache();

        $ltis = mod_lti_external::get_ltis_by_courses(array($this->course->id));
        $ltis = external_api::clean_returnvalue(mod_lti_external::get_ltis_by_courses_returns(), $ltis);
        $this->assertCount(0, $ltis['ltis']);
    }

    /**
     * Test view_lti
     */
    public function test_view_lti() {
        global $DB;

        // Test invalid instance id.
        try {
            mod_lti_external::view_lti(0);
            $this->fail('Exception expected due to invalid mod_lti instance id.');
        } catch (moodle_exception $e) {
            $this->assertEquals('invalidrecord', $e->errorcode);
        }

        // Test not-enrolled user.
        $usernotenrolled = self::getDataGenerator()->create_user();
        $this->setUser($usernotenrolled);
        try {
            mod_lti_external::view_lti($this->lti->id);
            $this->fail('Exception expected due to not enrolled user.');
        } catch (moodle_exception $e) {
            $this->assertEquals('requireloginerror', $e->errorcode);
        }

        // Test user with full capabilities.
        $this->setUser($this->student);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();

        $result = mod_lti_external::view_lti($this->lti->id);
        $result = external_api::clean_returnvalue(mod_lti_external::view_lti_returns(), $result);

        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = array_shift($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_lti\event\course_module_viewed', $event);
        $this->assertEquals($this->context, $event->get_context());
        $moodlelti = new \moodle_url('/mod/lti/view.php', array('id' => $this->cm->id));
        $this->assertEquals($moodlelti, $event->get_url());
        $this->assertEventContextNotUsed($event);
        $this->assertNotEmpty($event->get_name());

        // Test user with no capabilities.
        // We need a explicit prohibit since this capability is only defined in authenticated user and guest roles.
        assign_capability('mod/lti:view', CAP_PROHIBIT, $this->studentrole->id, $this->context->id);
        // Empty all the caches that may be affected by this change.
        accesslib_clear_all_caches_for_unit_testing();
        course_modinfo::clear_instance_cache();

        try {
            mod_lti_external::view_lti($this->lti->id);
            $this->fail('Exception expected due to missing capability.');
        } catch (moodle_exception $e) {
            $this->assertEquals('requireloginerror', $e->errorcode);
        }

    }

}

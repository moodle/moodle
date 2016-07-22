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
 * Unit tests for mod_workshop_portfolio_caller class defined in mod/workshop/classes/portfolio_caller.php
 *
 * @package    mod_workshop
 * @copyright  2016 An Pham Van <an.phamvan@harveynash.vn>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/mod/workshop/locallib.php');
require_once(__DIR__ . '/fixtures/testable.php');
require_once($CFG->dirroot . '/mod/workshop/classes/portfolio_caller.php');

/**
 * Unit tests for mod_workshop_portfolio_caller class
 *
 * @package    mod_workshop
 * @copyright  2016 An Pham Van <an.phamvan@harveynash.vn>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_workshop_porfolio_caller_testcase extends advanced_testcase {

    /** @var stdClass $workshop Basic workshop data stored in an object. */
    protected $workshop;
    /** @var stdClass mod info */
    protected $cm;

    /**
     * Setup testing environment.
     */
    protected function setUp() {
        parent::setUp();
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $workshop = $this->getDataGenerator()->create_module('workshop', array('course' => $course));
        $this->cm = get_coursemodule_from_instance('workshop', $workshop->id, $course->id, false, MUST_EXIST);
        $this->workshop = new testable_workshop($workshop, $this->cm, $course);
    }

    /**
     * Tear down.
     */
    protected function tearDown() {
        $this->workshop = null;
        $this->cm = null;
        parent::tearDown();
    }

    /**
     * Test function load_data()
     * Case 1: User exports the assessment of his/her own submission.
     * Assert that this function can load the correct assessment.
     */
    public function test_load_data_for_own_submissionassessment() {
        $this->resetAfterTest(true);

        $student1 = $this->getDataGenerator()->create_user();
        $student2 = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($student1->id, $this->workshop->course->id);
        $this->getDataGenerator()->enrol_user($student2->id, $this->workshop->course->id);
        $workshopgenerator = $this->getDataGenerator()->get_plugin_generator('mod_workshop');
        $subid1 = $workshopgenerator->create_submission($this->workshop->id, $student1->id);
        $asid1 = $workshopgenerator->create_assessment($subid1, $student2->id);

        $portfoliocaller = new mod_workshop_portfolio_caller(array('submissionid' => $subid1, 'assessmentid' => $asid1));
        $portfoliocaller->load_data();

        $reflector = new ReflectionObject($portfoliocaller);
        $assessment = $reflector->getProperty('assessment');
        $assessment->setAccessible(true);
        $result = $assessment->getValue($portfoliocaller);

        $this->assertEquals($asid1, $result->id);
    }

    /**
     * Test function load_data()
     * Case 2: User exports his/her own submission.
     * Assert that this function can load the correct submission.
     */
    public function test_load_data_for_own_submission() {
        $this->resetAfterTest(true);

        $student1 = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($student1->id, $this->workshop->course->id);
        $workshopgenerator = $this->getDataGenerator()->get_plugin_generator('mod_workshop');
        $subid1 = $workshopgenerator->create_submission($this->workshop->id, $student1->id);

        $portfoliocaller = new mod_workshop_portfolio_caller(array('submissionid' => $subid1));
        $portfoliocaller->load_data();

        $reflector = new ReflectionObject($portfoliocaller);
        $submission = $reflector->getProperty('submission');
        $submission->setAccessible(true);

        $result = $submission->getValue($portfoliocaller);

        $this->assertEquals($subid1, $result->id);
    }

    /**
     * Test function get_return_url()
     * Assert that this function can return the correct url.
     */
    public function test_get_return_url() {
        $this->resetAfterTest(true);

        $student1 = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($student1->id, $this->workshop->course->id);
        $workshopgenerator = $this->getDataGenerator()->get_plugin_generator('mod_workshop');
        $subid1 = $workshopgenerator->create_submission($this->workshop->id, $student1->id);

        $portfoliocaller = new mod_workshop_portfolio_caller(array('submissionid' => $subid1));

        $reflector = new ReflectionObject($portfoliocaller);
        $cm = $reflector->getProperty('cm');
        $cm->setAccessible(true);
        $cm->setValue($portfoliocaller, $this->cm);

        $expected = 'http://www.example.com/moodle/mod/workshop/submission.php?cmid='.$this->cm->id;
        $this->assertEquals($expected, $portfoliocaller->get_return_url());
    }

    /**
     * Test function get_navigation()
     * Assert that this function can return the navigation array.
     */
    public function test_get_navigation() {
        $this->resetAfterTest(true);

        $student1 = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($student1->id, $this->workshop->course->id);
        $workshopgenerator = $this->getDataGenerator()->get_plugin_generator('mod_workshop');
        $subid1 = $workshopgenerator->create_submission($this->workshop->id, $student1->id);

        $portfoliocaller = new mod_workshop_portfolio_caller(array('submissionid' => $subid1));
        $portfoliocaller->load_data();

        $reflector = new ReflectionObject($portfoliocaller);
        $cm = $reflector->getProperty('cm');
        $cm->setAccessible(true);
        $cm->setValue($portfoliocaller, $this->cm);

        $this->assertTrue(is_array($portfoliocaller->get_navigation()));
    }

    /**
     * Test function check_permissions()
     * Case 1: User exports assessment.
     * Assert that this function can return a boolean value
     * to indicate that the user has capability to export the assessment.
     */
    public function test_check_permissions_exportownsubmissionassessment() {
        global $DB;
        $this->resetAfterTest(true);

        $context = context_module::instance($this->cm->id);
        $student1 = $this->getDataGenerator()->create_user();
        $student2 = $this->getDataGenerator()->create_user();
        $roleids = $DB->get_records_menu('role', null, '', 'shortname, id');
        $this->getDataGenerator()->enrol_user($student1->id, $this->workshop->course->id, $roleids['student']);
        $this->getDataGenerator()->enrol_user($student2->id, $this->workshop->course->id, $roleids['student']);
        $workshopgenerator = $this->getDataGenerator()->get_plugin_generator('mod_workshop');
        $subid1 = $workshopgenerator->create_submission($this->workshop->id, $student1->id);
        $asid1 = $workshopgenerator->create_assessment($subid1, $student2->id);
        $this->setUser($student1);

        $portfoliocaller = new mod_workshop_portfolio_caller(array('submissionid' => $subid1, 'assessmentid' => $asid1));

        $reflector = new ReflectionObject($portfoliocaller);
        $cm = $reflector->getProperty('cm');
        $cm->setAccessible(true);
        $cm->setValue($portfoliocaller, $this->cm);

        // Case 1: If user has capabilities exportownsubmission prevented and exportownsubmissionassessment prevented
        // then check_permissions should return false.
        role_change_permission($roleids['student'], $context, 'mod/workshop:exportownsubmission', CAP_PREVENT);
        role_change_permission($roleids['student'], $context, 'mod/workshop:exportownsubmissionassessment', CAP_PREVENT);
        $this->assertFalse($portfoliocaller->check_permissions());

        // Case 2: If user has capabilities exportownsubmission allowed and exportownsubmissionassessment prevented
        // then check_permissions should return false.
        role_change_permission($roleids['student'], $context, 'mod/workshop:exportownsubmission', CAP_ALLOW);
        role_change_permission($roleids['student'], $context, 'mod/workshop:exportownsubmissionassessment', CAP_PREVENT);
        $this->assertFalse($portfoliocaller->check_permissions());

        // Case 3: If user has capabilities exportownsubmission prevented and exportownsubmissionassessment allowed
        // then check_permissions should return false.
        role_change_permission($roleids['student'], $context, 'mod/workshop:exportownsubmission', CAP_PREVENT);
        role_change_permission($roleids['student'], $context, 'mod/workshop:exportownsubmissionassessment', CAP_ALLOW);
        $this->assertFalse($portfoliocaller->check_permissions());

        // Case 4: If user has capabilities exportownsubmission allowed and exportownsubmissionassessment allowed
        // then check_permissions should return true.
        role_change_permission($roleids['student'], $context, 'mod/workshop:exportownsubmission', CAP_ALLOW);
        role_change_permission($roleids['student'], $context, 'mod/workshop:exportownsubmissionassessment', CAP_ALLOW);
        $this->assertTrue($portfoliocaller->check_permissions());
    }

    /**
     * Test function check_permissions()
     * Case 2: User exports submission.
     * Assert that this function can return a boolean value
     * to indicate that the user has capability to export submission.
     */
    public function test_check_permissions_exportownsubmission() {
        global $DB;
        $this->resetAfterTest(true);

        $context = context_module::instance($this->cm->id);
        $student1 = $this->getDataGenerator()->create_user();
        $roleids = $DB->get_records_menu('role', null, '', 'shortname, id');
        $this->getDataGenerator()->enrol_user($student1->id, $this->workshop->course->id, $roleids['student']);
        $workshopgenerator = $this->getDataGenerator()->get_plugin_generator('mod_workshop');
        $subid1 = $workshopgenerator->create_submission($this->workshop->id, $student1->id);
        $this->setUser($student1);

        $portfoliocaller = new mod_workshop_portfolio_caller(array('submissionid' => $subid1));
        $reflector = new ReflectionObject($portfoliocaller);
        $cm = $reflector->getProperty('cm');
        $cm->setAccessible(true);
        $cm->setValue($portfoliocaller, $this->cm);

        // Case 1: If user has capability to export submission then check_permissions should return true.
        role_change_permission($roleids['student'], $context, 'mod/workshop:exportownsubmission', CAP_ALLOW);
        $this->assertTrue($portfoliocaller->check_permissions());

        // Case 2: If user doesn't have capability to export submission then check_permissions should return false.
        role_change_permission($roleids['student'], $context, 'mod/workshop:exportownsubmission', CAP_PREVENT);
        $this->assertFalse($portfoliocaller->check_permissions());
    }

    /**
     * Test function get_sha1()
     * Case 1: User exports the assessment of his/her own submission.
     * Assert that this function can return a hash string.
     */
    public function test_get_sha1_assessment() {
        $this->resetAfterTest(true);

        $student1 = $this->getDataGenerator()->create_user();
        $student2 = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($student1->id, $this->workshop->course->id);
        $this->getDataGenerator()->enrol_user($student2->id, $this->workshop->course->id);
        $workshopgenerator = $this->getDataGenerator()->get_plugin_generator('mod_workshop');
        $subid1 = $workshopgenerator->create_submission($this->workshop->id, $student1->id);
        $asid1 = $workshopgenerator->create_assessment($subid1, $student2->id);

        $portfoliocaller = new mod_workshop_portfolio_caller(array('submissionid' => $subid1, 'assessmentid' => $asid1));
        $portfoliocaller->load_data();

        $this->assertTrue(is_string($portfoliocaller->get_sha1()));
    }

    /**
     * Test function get_sha1()
     * Case 2: User exports his/her own submission.
     * Assert that this function can return a hash string.
     */
    public function test_get_sha1_submission() {
        $this->resetAfterTest(true);

        $student1 = $this->getDataGenerator()->create_user();
        $student2 = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($student1->id, $this->workshop->course->id);
        $this->getDataGenerator()->enrol_user($student2->id, $this->workshop->course->id);
        $workshopgenerator = $this->getDataGenerator()->get_plugin_generator('mod_workshop');
        $subid1 = $workshopgenerator->create_submission($this->workshop->id, $student1->id);

        $portfoliocaller = new mod_workshop_portfolio_caller(array('submissionid' => $subid1));
        $portfoliocaller->load_data();

        $this->assertTrue(is_string($portfoliocaller->get_sha1()));
    }

    /**
     * Test function display_name()
     * Assert that this function can return the name of the module ('Workshop').
     */
    public function test_display_name() {
        $this->resetAfterTest(true);
        $this->assertEquals('Workshop', mod_workshop_portfolio_caller::display_name());
    }

}

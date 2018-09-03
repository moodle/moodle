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
 * Tests for the enrol_lti_plugin class.
 *
 * @package enrol_lti
 * @copyright 2016 Jun Pataleta <jun@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use enrol_lti\data_connector;
use enrol_lti\tool_provider;
use IMSGlobal\LTI\ToolProvider\ResourceLink;
use IMSGlobal\LTI\ToolProvider\ToolConsumer;
use IMSGlobal\LTI\ToolProvider\ToolProvider;
use IMSGlobal\LTI\ToolProvider\User;

defined('MOODLE_INTERNAL') || die();

/**
 * Tests for the enrol_lti_plugin class.
 *
 * @package enrol_lti
 * @copyright 2016 Jun Pataleta <jun@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class enrol_lti_testcase extends advanced_testcase {

    /**
     * Test set up.
     *
     * This is executed before running any tests in this file.
     */
    public function setUp() {
        $this->resetAfterTest();
        $this->setAdminUser();
    }

    /**
     * Test for enrol_lti_plugin::delete_instance().
     */
    public function test_delete_instance() {
        global $DB;

        // Create tool enrolment instance.
        $data = new stdClass();
        $data->enrolstartdate = time();
        $data->secret = 'secret';
        $tool = $this->getDataGenerator()->create_lti_tool($data);

        // Create consumer and related data.
        $dataconnector = new data_connector();
        $consumer = new ToolConsumer('testkey', $dataconnector);
        $consumer->secret = $tool->secret;
        $consumer->ltiVersion = ToolProvider::LTI_VERSION1;
        $consumer->name = 'TEST CONSUMER NAME';
        $consumer->consumerName = 'TEST CONSUMER INSTANCE NAME';
        $consumer->consumerGuid = 'TEST CONSUMER INSTANCE GUID';
        $consumer->consumerVersion = 'TEST CONSUMER INFO VERSION';
        $consumer->enabled = true;
        $consumer->protected = true;
        $consumer->save();

        $resourcelink = ResourceLink::fromConsumer($consumer, 'testresourcelinkid');
        $resourcelink->save();

        $ltiuser = User::fromResourceLink($resourcelink, '');
        $ltiuser->ltiResultSourcedId = 'testLtiResultSourcedId';
        $ltiuser->ltiUserId = 'testuserid';
        $ltiuser->email = 'user1@example.com';
        $ltiuser->save();

        $tp = new tool_provider($tool->id);
        $tp->user = $ltiuser;
        $tp->resourceLink = $resourcelink;
        $tp->consumer = $consumer;
        $tp->map_tool_to_consumer();

        $mappingparams = [
            'toolid' => $tool->id,
            'consumerid' => $tp->consumer->getRecordId()
        ];

        // Check first that the related records exist.
        $this->assertTrue($DB->record_exists('enrol_lti_tool_consumer_map', $mappingparams));
        $this->assertTrue($DB->record_exists('enrol_lti_lti2_consumer', [ 'id' => $consumer->getRecordId() ]));
        $this->assertTrue($DB->record_exists('enrol_lti_lti2_resource_link', [ 'id' => $resourcelink->getRecordId() ]));
        $this->assertTrue($DB->record_exists('enrol_lti_lti2_user_result', [ 'id' => $ltiuser->getRecordId() ]));

        // Perform deletion.
        $enrollti = new enrol_lti_plugin();
        $instance = $DB->get_record('enrol', ['id' => $tool->enrolid]);
        $enrollti->delete_instance($instance);

        // Check that the related records have been deleted.
        $this->assertFalse($DB->record_exists('enrol_lti_tool_consumer_map', $mappingparams));
        $this->assertFalse($DB->record_exists('enrol_lti_lti2_consumer', [ 'id' => $consumer->getRecordId() ]));
        $this->assertFalse($DB->record_exists('enrol_lti_lti2_resource_link', [ 'id' => $resourcelink->getRecordId() ]));
        $this->assertFalse($DB->record_exists('enrol_lti_lti2_user_result', [ 'id' => $ltiuser->getRecordId() ]));

        // Check that the enrolled users and the tool instance has been deleted.
        $this->assertFalse($DB->record_exists('enrol_lti_users', [ 'toolid' => $tool->id ]));
        $this->assertFalse($DB->record_exists('enrol_lti_tools', [ 'id' => $tool->id ]));
        $this->assertFalse($DB->record_exists('enrol', [ 'id' => $instance->id ]));
    }

    /**
     * Test for getting user enrolment actions.
     */
    public function test_get_user_enrolment_actions() {
        global $CFG, $DB, $PAGE;
        $this->resetAfterTest();

        // Set page URL to prevent debugging messages.
        $PAGE->set_url('/enrol/editinstance.php');

        $pluginname = 'lti';

        // Only enable the lti enrol plugin.
        $CFG->enrol_plugins_enabled = $pluginname;

        $generator = $this->getDataGenerator();

        // Get the enrol plugin.
        $plugin = enrol_get_plugin($pluginname);

        // Create a course.
        $course = $generator->create_course();
        $context = context_course::instance($course->id);
        $teacherroleid = $DB->get_field('role', 'id', ['shortname' => 'editingteacher'], MUST_EXIST);
        $studentroleid = $DB->get_field('role', 'id', ['shortname' => 'student'], MUST_EXIST);

        // Enable this enrol plugin for the course.
        $fields = ['contextid' => $context->id, 'roleinstructor' => $teacherroleid, 'rolelearner' => $studentroleid];
        $plugin->add_instance($course, $fields);

        // Create a student.
        $student = $generator->create_user();
        // Enrol the student to the course.
        $generator->enrol_user($student->id, $course->id, 'student', $pluginname);

        // Teachers don't have enrol/lti:unenrol capability by default. Login as admin for simplicity.
        $this->setAdminUser();

        require_once($CFG->dirroot . '/enrol/locallib.php');
        $manager = new course_enrolment_manager($PAGE, $course);
        $userenrolments = $manager->get_user_enrolments($student->id);
        $this->assertCount(1, $userenrolments);

        $ue = reset($userenrolments);
        $actions = $plugin->get_user_enrolment_actions($manager, $ue);
        // LTI enrolment has 1 enrol actions for active users -- unenrol.
        $this->assertCount(1, $actions);
    }
}

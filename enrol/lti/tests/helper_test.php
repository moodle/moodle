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
 * Test the helper functionality.
 *
 * @package enrol_lti
 * @copyright 2016 Mark Nelson <markn@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Test the helper functionality.
 *
 * @package enrol_lti
 * @copyright 2016 Mark Nelson <markn@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class enrol_lti_helper_testcase extends advanced_testcase {

    /**
     * @var stdClass $user1 A user.
     */
    public $user1;

    /**
     * @var stdClass $user2 A user.
     */
    public $user2;

    /**
     * Test set up.
     *
     * This is executed before running any test in this file.
     */
    public function setUp() {
        $this->resetAfterTest();

        // Set this user as the admin.
        $this->setAdminUser();

        // Get some of the information we need.
        $this->user1 = self::getDataGenerator()->create_user();
        $this->user2 = self::getDataGenerator()->create_user();
    }

    /**
     * Test the update user profile image function.
     */
    public function test_update_user_profile_image() {
        global $DB, $CFG;

        // Set the profile image.
        \enrol_lti\helper::update_user_profile_image($this->user1->id, $this->getExternalTestFileUrl('/test.jpg'));

        // Get the new user record.
        $this->user1 = $DB->get_record('user', array('id' => $this->user1->id));

        // Set the page details.
        $page = new moodle_page();
        $page->set_url('/user/profile.php');
        $page->set_context(context_system::instance());
        $renderer = $page->get_renderer('core');
        $usercontext = context_user::instance($this->user1->id);

        // Get the user's profile picture and make sure it is correct.
        $userpicture = new user_picture($this->user1);
        $this->assertSame($CFG->wwwroot . '/pluginfile.php/' . $usercontext->id . '/user/icon/clean/f2?rev=' .$this->user1->picture,
            $userpicture->get_url($page, $renderer)->out(false));
    }

    /**
     * Test that we can not enrol past the maximum number of users allowed.
     */
    public function test_enrol_user_max_enrolled() {
        global $DB;

        // Set up the LTI enrolment tool.
        $data = new stdClass();
        $data->maxenrolled = 1;
        $tool = $this->create_tool($data);

        // Now get all the information we need.
        $tool = \enrol_lti\helper::get_lti_tool($tool->id);

        // Enrol a user.
        $result = \enrol_lti\helper::enrol_user($tool, $this->user1->id);

        // Check that the user was enrolled.
        $this->assertEquals(true, $result);
        $this->assertEquals(1, $DB->count_records('user_enrolments', array('enrolid' => $tool->enrolid)));

        // Try and enrol another user - should not happen.
        $result = \enrol_lti\helper::enrol_user($tool, $this->user2->id);

        // Check that this user was not enrolled and we are told why.
        $this->assertEquals(\enrol_lti\helper::ENROLMENT_MAX_ENROLLED, $result);
        $this->assertEquals(1, $DB->count_records('user_enrolments', array('enrolid' => $tool->enrolid)));
    }

    /**
     * Test that we can not enrol when the enrolment has not started.
     */
    public function test_enrol_user_enrolment_not_started() {
        global $DB;

        // Set up the LTI enrolment tool.
        $data = new stdClass();
        $data->enrolstartdate = time() + DAYSECS; // Make sure it is in the future.
        $tool = $this->create_tool($data);

        // Now get all the information we need.
        $tool = \enrol_lti\helper::get_lti_tool($tool->id);

        // Try and enrol a user - should not happen.
        $result = \enrol_lti\helper::enrol_user($tool, $this->user1->id);

        // Check that this user was not enrolled and we are told why.
        $this->assertEquals(\enrol_lti\helper::ENROLMENT_NOT_STARTED, $result);
        $this->assertEquals(0, $DB->count_records('user_enrolments', array('enrolid' => $tool->enrolid)));
    }

    /**
     * Test that we can not enrol when the enrolment has finished.
     */
    public function test_enrol_user_enrolment_finished() {
        global $DB;

        // Set up the LTI enrolment tool.
        $data = new stdClass();
        $data->enrolenddate = time() - DAYSECS; // Make sure it is in the past.
        $tool = $this->create_tool($data);

        // Now get all the information we need.
        $tool = \enrol_lti\helper::get_lti_tool($tool->id);

        // Try and enrol a user - should not happen.
        $result = \enrol_lti\helper::enrol_user($tool, $this->user1->id);

        // Check that this user was not enrolled and we are told why.
        $this->assertEquals(\enrol_lti\helper::ENROLMENT_FINISHED, $result);
        $this->assertEquals(0, $DB->count_records('user_enrolments', array('enrolid' => $tool->enrolid)));
    }

    /**
     * Test returning the number of available tools.
     */
    public function test_count_lti_tools() {
        // Create two tools belonging to the same course.
        $course1 = $this->getDataGenerator()->create_course();
        $data = new stdClass();
        $data->courseid = $course1->id;
        $this->create_tool($data);
        $this->create_tool($data);

        // Create two more tools in a separate course.
        $course2 = $this->getDataGenerator()->create_course();
        $data = new stdClass();
        $data->courseid = $course2->id;
        $this->create_tool($data);

        // Set the next tool to disabled.
        $data->status = ENROL_INSTANCE_DISABLED;
        $this->create_tool($data);

        // Count all the tools.
        $count = \enrol_lti\helper::count_lti_tools();
        $this->assertEquals(4, $count);

        // Count all the tools in course 1.
        $count = \enrol_lti\helper::count_lti_tools(array('courseid' => $course1->id));
        $this->assertEquals(2, $count);

        // Count all the tools in course 2 that are disabled.
        $count = \enrol_lti\helper::count_lti_tools(array('courseid' => $course2->id, 'status' => ENROL_INSTANCE_DISABLED));
        $this->assertEquals(1, $count);

        // Count all the tools that are enabled.
        $count = \enrol_lti\helper::count_lti_tools(array('status' => ENROL_INSTANCE_ENABLED));
        $this->assertEquals(3, $count);
    }

    /**
     * Test returning the list of available tools.
     */
    public function test_get_lti_tools() {
        // Create two tools belonging to the same course.
        $course1 = $this->getDataGenerator()->create_course();
        $data = new stdClass();
        $data->courseid = $course1->id;
        $tool1 = $this->create_tool($data);
        $tool2 = $this->create_tool($data);

        // Create two more tools in a separate course.
        $course2 = $this->getDataGenerator()->create_course();
        $data = new stdClass();
        $data->courseid = $course2->id;
        $tool3 = $this->create_tool($data);

        // Set the next tool to disabled.
        $data->status = ENROL_INSTANCE_DISABLED;
        $tool4 = $this->create_tool($data);

        // Get all the tools.
        $tools = \enrol_lti\helper::get_lti_tools();

        // Check that we got all the tools.
        $this->assertEquals(4, count($tools));

        // Get all the tools in course 1.
        $tools = \enrol_lti\helper::get_lti_tools(array('courseid' => $course1->id));

        // Check that we got all the tools in course 1.
        $this->assertEquals(2, count($tools));
        $this->assertTrue(isset($tools[$tool1->id]));
        $this->assertTrue(isset($tools[$tool2->id]));

        // Get all the tools in course 2 that are disabled.
        $tools = \enrol_lti\helper::get_lti_tools(array('courseid' => $course2->id, 'status' => ENROL_INSTANCE_DISABLED));

        // Check that we got all the tools in course 2 that are disabled.
        $this->assertEquals(1, count($tools));
        $this->assertTrue(isset($tools[$tool4->id]));

        // Get all the tools that are enabled.
        $tools = \enrol_lti\helper::get_lti_tools(array('status' => ENROL_INSTANCE_ENABLED));

        // Check that we got all the tools that are enabled.
        $this->assertEquals(3, count($tools));
        $this->assertTrue(isset($tools[$tool1->id]));
        $this->assertTrue(isset($tools[$tool2->id]));
        $this->assertTrue(isset($tools[$tool3->id]));
    }

    /**
     * Test getting the launch url of a tool.
     */
    public function test_get_launch_url() {
        $course1 = $this->getDataGenerator()->create_course();
        $data = new stdClass();
        $data->courseid = $course1->id;
        $tool1 = $this->create_tool($data);

        $id = $tool1->id;
        $launchurl = \enrol_lti\helper::get_launch_url($id);
        $this->assertEquals('http://www.example.com/moodle/enrol/lti/tool.php?id=' . $id, $launchurl->out());
    }

    /**
     * Test getting the cartridge url of a tool.
     */
    public function test_get_cartridge_url() {
        global $CFG;

        $slasharguments = $CFG->slasharguments;

        $CFG->slasharguments = false;

        $course1 = $this->getDataGenerator()->create_course();
        $data = new stdClass();
        $data->courseid = $course1->id;
        $tool1 = $this->create_tool($data);

        $id = $tool1->id;
        $token = \enrol_lti\helper::generate_tool_token($id);
        $launchurl = \enrol_lti\helper::get_cartridge_url($tool1);
        $this->assertEquals('http://www.example.com/moodle/enrol/lti/cartridge.php?id=' . $id . '&amp;token=' . $token,
                            $launchurl->out());

        $CFG->slasharguments = true;

        $launchurl = \enrol_lti\helper::get_cartridge_url($tool1);
        $this->assertEquals('http://www.example.com/moodle/enrol/lti/cartridge.php/' . $id . '/' . $token . '/cartridge.xml',
                            $launchurl->out());

        $CFG->slasharguments = $slasharguments;
    }

    /**
     * Test getting the name of a tool.
     */
    public function test_get_name() {
        $course1 = $this->getDataGenerator()->create_course();
        $data = new stdClass();
        $data->courseid = $course1->id;
        $tool1 = $this->create_tool($data);

        $name = \enrol_lti\helper::get_name($tool1);
        $this->assertEquals('Course: Test course 1', $name);

        $tool1->name = 'Shared course';
        $name = \enrol_lti\helper::get_name($tool1);
        $this->assertEquals('Shared course', $name);
    }

    /**
     * Test getting the description of a tool.
     */
    public function test_get_description() {
        $course1 = $this->getDataGenerator()->create_course();
        $data = new stdClass();
        $data->courseid = $course1->id;
        $tool1 = $this->create_tool($data);

        $description = \enrol_lti\helper::get_description($tool1);
        $this->assertContains('Test course 1 Lorem ipsum dolor sit amet', $description);

        $module1 = $this->getDataGenerator()->create_module('assign', array(
                'course' => $course1->id
            ));
        $data = new stdClass();
        $data->cmid = $module1->cmid;
        $tool2 = $this->create_tool($data);
        $description = \enrol_lti\helper::get_description($tool2);
        $this->assertContains('Test assign 1', $description);
    }

    /**
     * Test verifying a tool token.
     */
    public function test_verify_tool_token() {
        $course1 = $this->getDataGenerator()->create_course();
        $data = new stdClass();
        $data->courseid = $course1->id;
        $tool1 = $this->create_tool($data);

        $token = \enrol_lti\helper::generate_tool_token($tool1->id);
        $this->assertTrue(\enrol_lti\helper::verify_tool_token($tool1->id, $token));
        $this->assertFalse(\enrol_lti\helper::verify_tool_token($tool1->id, 'incorrect token!'));
    }

    /**
     * Data provider for the set_xpath test.
     */
    public function set_xpath_provider() {
        return [
            "Correct structure" => [
                "parameters" => [
                    "/root" => [
                        "/firstnode" => "Content 1",
                        "/parentnode" => [
                            "/childnode" => "Content 2"
                        ]
                    ]
                ],
                "expected" => "test_correct_xpath-expected.xml"
            ],
            "A null value, but no node to remove" => [
                "parameters" => [
                    "/root" => [
                        "/nonexistant" => null,
                        "/firstnode" => "Content 1"
                    ]
                ],
                "expected" => "test_missing_node-expected.xml"
            ],
            "A string value, but no node existing to set" => [
                "parameters" => [
                    "/root" => [
                        "/nonexistant" => "This will not be set",
                        "/firstnode" => "Content 1"
                    ]
                ],
                "expected" => "test_missing_node-expected.xml"
            ],
            "Array but no children exist" => [
                "parameters" => [
                    "/root" => [
                        "/nonexistant" => [
                            "/alsononexistant" => "This will not be set"
                        ],
                        "/firstnode" => "Content 1"
                    ]
                ],
                "expected" => "test_missing_node-expected.xml"
            ],
            "Remove nodes" => [
                "parameters" => [
                    "/root" => [
                        "/parentnode" => [
                            "/childnode" => null
                        ],
                        "/firstnode" => null
                    ]
                ],
                "expected" => "test_nodes_removed-expected.xml"
            ],
            "Get by attribute" => [
                "parameters" => [
                    "/root" => [
                        "/ambiguous[@id='1']" => 'Content 1'
                    ]
                ],
                "expected" => "test_ambiguous_nodes-expected.xml"
            ]
        ];
    }

    /**
     * Test set_xpath.
     * @dataProvider set_xpath_provider
     * @param array $parameters A hash of parameters represented by a heirarchy of xpath expressions
     * @param string $expected The name of the fixture file containing the expected result.
     */
    public function test_set_xpath($parameters, $expected) {
        $helper = new ReflectionClass('enrol_lti\\helper');
        $function = $helper->getMethod('set_xpath');
        $function->setAccessible(true);

        $document = new \DOMDocument();
        $document->load(realpath(__DIR__ . '/fixtures/input.xml'));
        $xpath = new \DOMXpath($document);
        $function->invokeArgs(null, [$xpath, $parameters]);
        $result = $document->saveXML();
        $expected = file_get_contents(realpath(__DIR__ . '/fixtures/' . $expected));
        $this->assertEquals($expected, $result);
    }

    /**
     * Test set_xpath when an incorrect xpath expression is given.
     * @expectedException coding_exception
     */
    public function test_set_xpath_incorrect_xpath() {
        $parameters = [
            "/root" => [
                "/firstnode" => null,
                "/parentnode*&#^*#(" => [
                    "/childnode" => null
                ],
            ]
        ];
        $helper = new ReflectionClass('enrol_lti\\helper');
        $function = $helper->getMethod('set_xpath');
        $function->setAccessible(true);

        $document = new \DOMDocument();
        $document->load(realpath(__DIR__ . '/fixtures/input.xml'));
        $xpath = new \DOMXpath($document);
        $function->invokeArgs(null, [$xpath, $parameters]);
        $result = $document->saveXML();
        $expected = file_get_contents(realpath(__DIR__ . '/fixtures/' . $expected));
        $this->assertEquals($expected, $result);
    }

    /**
     * Test create cartridge.
     */
    public function test_create_cartridge() {
        global $CFG;

        $course1 = $this->getDataGenerator()->create_course();
        $data = new stdClass();
        $data->courseid = $course1->id;
        $tool1 = $this->create_tool($data);

        $cartridge = \enrol_lti\helper::create_cartridge($tool1->id);
        $this->assertContains('<blti:title>Test LTI</blti:title>', $cartridge);
        $this->assertContains("<blti:icon>$CFG->wwwroot/theme/image.php/_s/clean/theme/1/favicon</blti:icon>", $cartridge);
        $this->assertContains("<blti:launch_url>$CFG->wwwroot/enrol/lti/tool.php?id=$tool1->id</blti:launch_url>", $cartridge);
    }

    /**
     * Helper function used to create a tool.
     *
     * @param array $data
     * @return stdClass the tool
     */
    protected function create_tool($data = array()) {
        global $DB;

        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $teacherrole = $DB->get_record('role', array('shortname' => 'teacher'));

        // Create a course if no course id was specified.
        if (empty($data->courseid)) {
            $course = $this->getDataGenerator()->create_course();
            $data->courseid = $course->id;
        } else {
            $course = get_course($data->courseid);
        }

        if (!empty($data->cmid)) {
            $data->contextid = context_module::instance($data->cmid)->id;
        } else {
            $data->contextid = context_course::instance($data->courseid)->id;
        }

        // Set it to enabled if no status was specified.
        if (!isset($data->status)) {
            $data->status = ENROL_INSTANCE_ENABLED;
        }

        // Add some extra necessary fields to the data.
        $data->name = 'Test LTI';
        $data->roleinstructor = $studentrole->id;
        $data->rolelearner = $teacherrole->id;

        // Get the enrol LTI plugin.
        $enrolplugin = enrol_get_plugin('lti');
        $instanceid = $enrolplugin->add_instance($course, (array) $data);

        // Get the tool associated with this instance.
        return $DB->get_record('enrol_lti_tools', array('enrolid' => $instanceid));
    }
}

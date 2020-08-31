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
 * Tests for the sync_members scheduled task class.
 *
 * @package enrol_lti
 * @copyright 2016 Jun Pataleta <jun@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use enrol_lti\data_connector;
use enrol_lti\helper;
use enrol_lti\task\sync_members;
use enrol_lti\tool_provider;
use IMSGlobal\LTI\ToolProvider\Context;
use IMSGlobal\LTI\ToolProvider\ResourceLink;
use IMSGlobal\LTI\ToolProvider\ToolConsumer;
use IMSGlobal\LTI\ToolProvider\User;

defined('MOODLE_INTERNAL') || die();

/**
 * Tests for the sync_members scheduled task class.
 *
 * @package enrol_lti
 * @copyright 2016 Jun Pataleta <jun@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class sync_members_testcase extends advanced_testcase {
    /** @var dummy_sync_members_task $task */
    protected $task;

    /** @var  stdClass $tool The published tool. */
    protected $tool;

    /** @var User[] $members */
    protected $members;

    /** @var  ToolConsumer $consumer */
    protected $consumer;

    /** @var  Context $context */
    protected $context;

    /** @var  ResourceLink $resourcelink */
    protected $resourcelink;

    public function setUp(): void {
        $this->resetAfterTest();

        // Set this user as the admin.
        $this->setAdminUser();

        $this->task = new dummy_sync_members_task();

        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $tooldata = [
            'courseid' => $course->id,
            'membersyncmode' => helper::MEMBER_SYNC_ENROL_AND_UNENROL,
            'membersync' => 1,
        ];
        $tool = $generator->create_lti_tool((object)$tooldata);
        $this->tool = helper::get_lti_tool($tool->id);

        $dataconnector = $this->task->get_dataconnector();
        $this->consumer = new ToolConsumer('Consumer1Key', $dataconnector);
        $this->consumer->name = 'Consumer1';
        $this->consumer->secret = 'Consumer1Secret';
        $this->consumer->save();

        $toolprovider = new tool_provider($this->tool->id);
        $toolprovider->consumer = $this->consumer;
        $toolprovider->map_tool_to_consumer();

        $imageurl = $this->getExternalTestFileUrl('test.jpg');
        $count = 10;
        $this->members = [];
        for ($i = 1; $i <= $count; $i++) {
            $user = new User();
            $user->firstname = 'Firstname' . $i;
            $user->lastname = 'Lastname' . $i;
            $user->ltiUserId = 'user' . $i;
            // Set user image values for some users.
            if ($i % 3 == 0) {
                $user->image = $imageurl;
            }
            $this->members[] = $user;
        }

        $this->context = Context::fromConsumer($this->consumer, 'testlticontextid');
        $this->context->save();

        $this->resourcelink = ResourceLink::fromContext($this->context, 'testresourcelinkid');
        $this->resourcelink->save();
    }

    /**
     * Test for sync_members::do_context_membership_request().
     */
    public function test_do_context_membership_request() {
        // Suppress output.
        ob_start();
        $members = $this->task->do_context_membership_request($this->context);
        ob_end_clean();
        $this->assertFalse($members);
    }

    /**
     * Test for sync_members::do_resourcelink_membership_request().
     */
    public function test_do_resourcelink_membership_request() {
        $members = $this->task->do_resourcelink_membership_request($this->resourcelink);
        $this->assertFalse($members);
    }

    /**
     * Test for sync_members::execute() when auth_lti is disabled.
     */
    public function test_execute_authdisabled() {
        ob_start();
        $this->task->execute();
        $output = ob_get_clean();
        $message = 'Skipping task - ' . get_string('pluginnotenabled', 'auth', get_string('pluginname', 'auth_lti'));
        $this->assertStringContainsString($message, $output);
    }

    /**
     * Test for sync_members::execute() when enrol_lti is disabled.
     */
    public function test_execute_enroldisabled() {
        // Enable auth_lti.
        $this->enable_auth();

        ob_start();
        $this->task->execute();
        $output = ob_get_clean();
        $message = 'Skipping task - ' . get_string('enrolisdisabled', 'enrol_lti');
        $this->assertStringContainsString($message, $output);
    }

    /**
     * Test for sync_members::execute().
     */
    public function test_execute() {
        // Enable auth_lti.
        $this->enable_auth();

        // Enable enrol_lti.
        $this->enable_enrol();

        ob_start();
        $this->task->execute();
        $output = ob_get_clean();

        $membersyncmessage = "Completed - Synced members for tool '{$this->tool->id}' in the course '{$this->tool->courseid}'";
        $this->assertStringContainsString($membersyncmessage, $output);

        $imagesyncmessage = "Completed - Synced 0 profile images.";
        $this->assertStringContainsString($imagesyncmessage, $output);
    }

    /**
     * Test for sync_members::fetch_members_from_consumer() with no resource link nor context associated with the consumer.
     */
    public function test_fetch_members_from_consumer_noresourcelink_nocontext() {
        // Suppress output.
        ob_start();
        $members = $this->task->fetch_members_from_consumer($this->consumer);
        ob_end_clean();
        $this->assertFalse($members);
    }

    /**
     * Test for sync_members::get_name().
     */
    public function test_get_name() {
        $this->assertEquals(get_string('tasksyncmembers', 'enrol_lti'), $this->task->get_name());
    }

    /**
     * Test for sync_members::should_sync_enrol().
     */
    public function test_should_sync_enrol() {
        $this->assertTrue($this->task->should_sync_enrol(helper::MEMBER_SYNC_ENROL_AND_UNENROL));
        $this->assertTrue($this->task->should_sync_enrol(helper::MEMBER_SYNC_ENROL_NEW));
        $this->assertFalse($this->task->should_sync_enrol(helper::MEMBER_SYNC_UNENROL_MISSING));
    }

    /**
     * Test for sync_members::should_sync_unenrol().
     */
    public function test_should_sync_unenrol() {
        $this->assertTrue($this->task->should_sync_unenrol(helper::MEMBER_SYNC_ENROL_AND_UNENROL));
        $this->assertFalse($this->task->should_sync_unenrol(helper::MEMBER_SYNC_ENROL_NEW));
        $this->assertTrue($this->task->should_sync_unenrol(helper::MEMBER_SYNC_UNENROL_MISSING));
    }

    /**
     * Test for sync_members::sync_member_information().
     */
    public function test_sync_member_information() {
        list($totalcount, $enrolledcount) = $this->task->sync_member_information($this->tool, $this->consumer, $this->members);
        $membercount = count($this->members);
        $this->assertCount(10, $this->members);
        $this->assertEquals($membercount, $totalcount);
        $this->assertEquals($membercount, $enrolledcount);
    }

    /**
     * Test for sync_members::sync_profile_images().
     */
    public function test_sync_profile_images() {
        $task = $this->task;
        list($totalcount, $enrolledcount) = $task->sync_member_information($this->tool, $this->consumer, $this->members);
        $membercount = count($this->members);
        $this->assertCount(10, $this->members);
        $this->assertEquals($membercount, $totalcount);
        $this->assertEquals($membercount, $enrolledcount);

        // Suppress output.
        ob_start();
        $this->assertEquals(3, $task->sync_profile_images());
        ob_end_clean();
    }

    /**
     * Test for sync_members::sync_unenrol().
     */
    public function test_sync_unenrol() {
        $tool = $this->tool;
        $task = $this->task;

        $task->sync_member_information($tool, $this->consumer, $this->members);

        // Simulate that the fetched list of current users has been reduced by 3.
        $unenrolcount = 3;
        for ($i = 0; $i < $unenrolcount; $i++) {
            $task->pop_current_users();
        }
        $this->assertEquals($unenrolcount, $task->sync_unenrol($tool));
    }

    /**
     * Enable auth_lti plugin.
     */
    protected function enable_auth() {
        $auths = get_enabled_auth_plugins(true);
        if (!in_array('lti', $auths)) {
            $auths[] = 'lti';
        }
        set_config('auth', implode(',', $auths));
    }

    /**
     * Enable enrol_lti plugin.
     */
    protected function enable_enrol() {
        $enabled = enrol_get_plugins(true);
        $enabled['lti'] = true;
        $enabled = array_keys($enabled);
        set_config('enrol_plugins_enabled', implode(',', $enabled));
    }
}

/**
 * Class dummy_sync_members_task.
 *
 * A class that extends sync_members so that we can expose the protected methods that we would like to test.
 *
 * @copyright 2016 Jun Pataleta <jun@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class dummy_sync_members_task extends sync_members {
    /**
     * Exposes/generates the dataconnector property.
     *
     * @return data_connector
     */
    public function get_dataconnector() {
        if (!$this->dataconnector) {
            $this->dataconnector = new data_connector();
        }
        return $this->dataconnector;
    }

    /**
     * Helper method that removes an element in the array of current users.
     */
    public function pop_current_users() {
        array_pop($this->currentusers);
    }

    /**
     * Exposes sync_members::do_context_membership_request()
     *
     * @param Context $context The context object.
     * @param ResourceLink $resourcelink The resource link object.
     * @param string $membershipsurltemplate The memberships endpoint URL template.
     * @return bool|User[] Array of User objects upon successful membership service request. False, otherwise.
     */
    public function do_context_membership_request(Context $context, ResourceLink $resourcelink = null,
                                                  $membershipsurltemplate = '') {
        $members = parent::do_context_membership_request($context, $resourcelink, $membershipsurltemplate);
        return $members;
    }


    /**
     * Exposes sync_members::do_resourcelink_membership_request()
     *
     * @param ResourceLink $resourcelink
     * @return bool|User[]
     */
    public function do_resourcelink_membership_request(ResourceLink $resourcelink) {
        $members = parent::do_resourcelink_membership_request($resourcelink);
        return $members;
    }

    /**
     * Exposes sync_members::fetch_members_from_consumer()
     *
     * @param ToolConsumer $consumer
     * @return bool|User[]
     */
    public function fetch_members_from_consumer(ToolConsumer $consumer) {
        $members = parent::fetch_members_from_consumer($consumer);
        return $members;
    }

    /**
     * Exposes sync_members::should_sync_unenrol()
     *
     * @param int $syncmode The tool's membersyncmode.
     * @return bool
     */
    public function should_sync_unenrol($syncmode) {
        $shouldsync = parent::should_sync_unenrol($syncmode);
        return $shouldsync;
    }

    /**
     * Exposes sync_members::should_sync_enrol()
     *
     * @param int $syncmode The tool's membersyncmode.
     * @return bool
     */
    public function should_sync_enrol($syncmode) {
        $shouldsync = parent::should_sync_enrol($syncmode);
        return $shouldsync;
    }

    /**
     * Exposes sync_members::sync_member_information()
     *
     * @param stdClass $tool
     * @param ToolConsumer $consumer
     * @param User[] $members
     * @return array
     */
    public function sync_member_information(stdClass $tool, ToolConsumer $consumer, $members) {
        $result = parent::sync_member_information($tool, $consumer, $members);
        return $result;
    }

    /**
     * Exposes sync_members::sync_profile_images()
     *
     * @return int
     */
    public function sync_profile_images() {
        $count = parent::sync_profile_images();
        return $count;
    }

    /**
     * Exposes sync_members::sync_unenrol()
     *
     * @param stdClass $tool
     * @return int
     */
    public function sync_unenrol(stdClass $tool) {
        $count = parent::sync_unenrol($tool);
        return $count;
    }
}

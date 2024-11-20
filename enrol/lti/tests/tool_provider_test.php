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
 * Tests for the tool_provider class.
 *
 * @package enrol_lti
 * @copyright 2016 Jun Pataleta <jun@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_lti;

use core\session\manager;
use IMSGlobal\LTI\HTTPMessage;
use IMSGlobal\LTI\ToolProvider\ResourceLink;
use IMSGlobal\LTI\ToolProvider\ToolConsumer;
use IMSGlobal\LTI\ToolProvider\ToolProvider;
use IMSGlobal\LTI\ToolProvider\User;

defined('MOODLE_INTERNAL') || die();

/**
 * Tests for the tool_provider class.
 *
 * @package enrol_lti
 * @copyright 2016 Jun Pataleta <jun@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_provider_test extends \advanced_testcase {

    /**
     * @var \stdClass $tool The LTI tool.
     */
    protected $tool;

    /**
     * Test set up.
     *
     * This is executed before running any tests in this file.
     */
    public function setUp(): void {
        global $SESSION;
        parent::setUp();
        $this->resetAfterTest();

        manager::init_empty_session();

        // Set this user as the admin.
        $this->setAdminUser();

        $data = new \stdClass();
        $data->enrolstartdate = time();
        $data->secret = 'secret';
        $toolrecord = $this->getDataGenerator()->create_lti_tool($data);
        $this->tool = helper::get_lti_tool($toolrecord->id);
        $SESSION->notifications = [];
    }

    /**
     * Passing non-existent tool ID.
     */
    public function test_constructor_with_non_existent_tool(): void {
        $this->expectException('dml_exception');
        new tool_provider(-1);
    }

    /**
     * Constructor test.
     */
    public function test_constructor(): void {
        global $CFG, $SITE;

        $tool = $this->tool;
        $tp = new tool_provider($tool->id);

        $this->assertNull($tp->consumer);
        $this->assertNull($tp->returnUrl);
        $this->assertNull($tp->resourceLink);
        $this->assertNull($tp->context);
        $this->assertNotNull($tp->dataConnector);
        $this->assertEquals('', $tp->defaultEmail);
        $this->assertEquals(ToolProvider::ID_SCOPE_ID_ONLY, $tp->idScope);
        $this->assertFalse($tp->allowSharing);
        $this->assertEquals(ToolProvider::CONNECTION_ERROR_MESSAGE, $tp->message);
        $this->assertNull($tp->reason);
        $this->assertEmpty($tp->details);
        $this->assertEquals($CFG->wwwroot, $tp->baseUrl);

        $this->assertNotNull($tp->vendor);
        $this->assertEquals($SITE->shortname, $tp->vendor->id);
        $this->assertEquals($SITE->fullname, $tp->vendor->name);
        $this->assertEquals($SITE->summary, $tp->vendor->description);

        $token = helper::generate_proxy_token($tool->id);
        $name = helper::get_name($tool);
        $description = helper::get_description($tool);

        $this->assertNotNull($tp->product);
        $this->assertEquals($token, $tp->product->id);
        $this->assertEquals($name, $tp->product->name);
        $this->assertEquals($description, $tp->product->description);

        $this->assertNotNull($tp->requiredServices);
        $this->assertEmpty($tp->optionalServices);
        $this->assertNotNull($tp->resourceHandlers);
    }

    /**
     * Test for handle request.
     */
    public function test_handle_request_no_request_data(): void {
        $tool = $this->tool;
        $tp = new tool_provider($tool->id);

        // Tool provider object should have been created fine. OK flag should be fine for now.
        $this->assertTrue($tp->ok);

        // Call handleRequest but suppress output.
        ob_start();
        $tp->handleRequest();
        ob_end_clean();

        // There's basically no request data submitted so OK flag should turn out false.
        $this->assertFalse($tp->ok);
    }

    /**
     * Test for tool_provider::onError().
     */
    public function test_on_error(): void {
        $tool = $this->tool;
        $tp = new dummy_tool_provider($tool->id);
        $message = "THIS IS AN ERROR!";
        $tp->message = $message;
        $tp->onError();
        $errormessage = get_string('failedrequest', 'enrol_lti', ['reason' => $message]);
        $this->assertStringContainsString($errormessage, $tp->get_error_output());
    }

    /**
     * Test for tool_provider::onRegister() with no tool consumer set.
     */
    public function test_on_register_no_consumer(): void {
        $tool = $this->tool;

        $tp = new dummy_tool_provider($tool->id);
        $tp->onRegister();

        $this->assertFalse($tp->ok);
        $this->assertEquals(get_string('invalidtoolconsumer', 'enrol_lti'), $tp->message);
    }

    /**
     * Test for tool_provider::onRegister() without return URL.
     */
    public function test_on_register_no_return_url(): void {
        $tool = $this->tool;

        $dataconnector = new data_connector();
        $consumer = new ToolConsumer('testkey', $dataconnector);
        $consumer->ltiVersion = ToolProvider::LTI_VERSION2;
        $consumer->secret = $tool->secret;
        $consumer->name = 'TEST CONSUMER NAME';
        $consumer->consumerName = 'TEST CONSUMER INSTANCE NAME';
        $consumer->consumerGuid = 'TEST CONSUMER INSTANCE GUID';
        $consumer->consumerVersion = 'TEST CONSUMER INFO VERSION';
        $consumer->enabled = true;
        $consumer->protected = true;
        $consumer->save();

        $tp = new dummy_tool_provider($tool->id);
        $tp->consumer = $consumer;

        $tp->onRegister();
        $this->assertFalse($tp->ok);
        $this->assertEquals(get_string('returnurlnotset', 'enrol_lti'), $tp->message);
    }

    /**
     * Test for tool_provider::onRegister() when registration fails.
     */
    public function test_on_register_failed(): void {
        global $CFG;
        $tool = $this->tool;

        $dataconnector = new data_connector();
        $consumer = new dummy_tool_consumer('testkey', $dataconnector);
        $consumer->ltiVersion = ToolProvider::LTI_VERSION2;
        $consumer->secret = $tool->secret;
        $consumer->name = 'TEST CONSUMER NAME';
        $consumer->consumerName = 'TEST CONSUMER INSTANCE NAME';
        $consumer->consumerGuid = 'TEST CONSUMER INSTANCE GUID';
        $consumer->consumerVersion = 'TEST CONSUMER INFO VERSION';
        $consumer->enabled = true;
        $consumer->protected = true;
        $profilejson = file_get_contents(self::get_fixture_path(__NAMESPACE__, 'tool_consumer_profile.json'));
        $consumer->profile = json_decode($profilejson);
        $consumer->save();

        $tp = new dummy_tool_provider($tool->id);
        $tp->consumer = $consumer;
        $tp->returnUrl = $CFG->wwwroot;

        $tp->onRegister();

        // The OK flag will be false.
        $this->assertFalse($tp->ok);
        // Check message.
        $this->assertEquals(get_string('couldnotestablishproxy', 'enrol_lti'), $tp->message);
    }

    /**
     * Test for tool_provider::onRegister() when registration succeeds.
     */
    public function test_on_register(): void {
        global $CFG, $DB;
        $tool = $this->tool;

        $dataconnector = new data_connector();
        $consumer = new dummy_tool_consumer('testkey', $dataconnector, false, true);
        $consumer->ltiVersion = ToolProvider::LTI_VERSION2;
        $consumer->secret = $tool->secret;
        $consumer->name = 'TEST CONSUMER NAME';
        $consumer->consumerName = 'TEST CONSUMER INSTANCE NAME';
        $consumer->consumerGuid = 'TEST CONSUMER INSTANCE GUID';
        $consumer->consumerVersion = 'TEST CONSUMER INFO VERSION';
        $consumer->enabled = true;
        $consumer->protected = true;
        $profilejson = file_get_contents(self::get_fixture_path(__NAMESPACE__, 'tool_consumer_profile.json'));
        $consumer->profile = json_decode($profilejson);
        $consumer->save();

        $tp = new dummy_tool_provider($tool->id);
        $tp->consumer = $consumer;
        $tp->returnUrl = $CFG->wwwroot;

        // Capture output of onLaunch() method and save it as a string.
        ob_start();
        $tp->onRegister();
        $output = ob_get_clean();

        $successmessage = get_string('successfulregistration', 'enrol_lti');

        // Check output contents. Confirm that it has the success message and return URL.
        $this->assertStringContainsString($successmessage, $output);
        $this->assertStringContainsString($tp->returnUrl, $output);

        // The OK flag will be true on successful registration.
        $this->assertTrue($tp->ok);

        // Check tool provider message.
        $this->assertEquals($successmessage, $tp->message);

        // Check published tool and tool consumer mapping.
        $mappingparams = [
            'toolid' => $tool->id,
            'consumerid' => $tp->consumer->getRecordId()
        ];
        $this->assertTrue($DB->record_exists('enrol_lti_tool_consumer_map', $mappingparams));
    }

    /**
     * Test for tool_provider::onLaunch().
     */
    public function test_on_launch_no_frame_embedding(): void {
        $tp = $this->build_dummy_tp();

        // Capture output of onLaunch() method and save it as a string.
        ob_start();
        // Suppress session header errors.
        @$tp->onLaunch();
        $output = ob_get_clean();

        $this->assertStringContainsString(get_string('frameembeddingnotenabled', 'enrol_lti'), $output);
    }

    /**
     * Test for tool_provider::onLaunch().
     */
    public function test_on_launch_with_frame_embedding(): void {
        global $CFG;
        $CFG->allowframembedding = true;

        $tp = $this->build_dummy_tp();

        // If redirect was called here, we will encounter an 'unsupported redirect error'.
        // We just want to verify that redirect() was called if frame embedding is allowed.
        $this->expectException('moodle_exception');

        // Suppress session header errors.
        @$tp->onLaunch();
    }

    /**
     * Test for tool_provider::onLaunch() with invalid secret and no tool proxy (for LTI 1 launches).
     */
    public function test_on_launch_with_invalid_secret_and_no_proxy(): void {
        $tp = $this->build_dummy_tp('badsecret');

        // Suppress session header errors.
        @$tp->onLaunch();
        $this->assertFalse($tp->ok);
        $this->assertEquals(get_string('invalidrequest', 'enrol_lti'), $tp->message);
    }

    /**
     * Test for tool_provider::onLaunch() with invalid launch URL.
     */
    public function test_on_launch_proxy_with_invalid_launch_url(): void {
        $proxy = [
            'tool_profile' => [
                'resource_handler' => [
                    [
                        'message' => [
                            [
                                'message_type' => 'basic-lti-launch-request',
                                'path' => '/enrol/lti/tool.php'
                            ]
                        ]
                    ]
                ]
            ]
        ];
        $tp = $this->build_dummy_tp($this->tool->secret, $proxy);
        // Suppress session header errors.
        @$tp->onLaunch();

        $this->assertFalse($tp->ok);
        $this->assertEquals(get_string('invalidrequest', 'enrol_lti'), $tp->message);
    }

    /**
     * Test for tool_provider::onLaunch() with invalid launch URL.
     */
    public function test_on_launch_proxy_with_valid_launch_url(): void {
        $tool = $this->tool;

        $proxy = [
            'tool_profile' => [
                'resource_handler' => [
                    [
                        'message' => [
                            [
                                'message_type' => 'basic-lti-launch-request',
                                'path' => '/enrol/lti/tool.php?id=' . $tool->id
                            ]
                        ]
                    ]
                ]
            ]
        ];
        $tp = $this->build_dummy_tp($this->tool->secret, $proxy);

        // Capture output of onLaunch() method and save it as a string.
        ob_start();
        // Suppress session header errors.
        @$tp->onLaunch();
        $output = ob_get_clean();

        $this->assertTrue($tp->ok);
        $this->assertEquals(get_string('success'), $tp->message);
        $this->assertStringContainsString(get_string('frameembeddingnotenabled', 'enrol_lti'), $output);
    }

    /**
     * Test for tool_provider::onLaunch() for a request with message type other than basic-lti-launch-request.
     */
    public function test_on_launch_proxy_with_invalid_message_type(): void {
        $tool = $this->tool;

        $proxy = [
            'tool_profile' => [
                'resource_handler' => [
                    [
                        'message' => [
                            [
                                'message_type' => 'ContentItemSelectionRequest',
                                'path' => '/enrol/lti/tool.php?id=' . $tool->id
                            ]
                        ]
                    ]
                ]
            ]
        ];
        $tp = $this->build_dummy_tp($this->tool->secret, $proxy);

        // Suppress session header errors.
        @$tp->onLaunch();

        $this->assertFalse($tp->ok);
        $this->assertEquals(get_string('invalidrequest', 'enrol_lti'), $tp->message);
    }

    /**
     * Test for tool_provider::onLaunch() to verify that a user image can be set from the resource link's custom_user_image setting.
     */
    public function test_on_launch_with_user_image_from_resource_link(): void {
        global $DB;

        $userimageurl = $this->getExternalTestFileUrl('test.jpg');
        $resourcelinksettings = [
            'custom_user_image' => $userimageurl
        ];
        $tp = $this->build_dummy_tp($this->tool->secret, null, $resourcelinksettings);

        // Suppress output and session header errors.
        ob_start();
        @$tp->onLaunch();
        ob_end_clean();

        $this->assertEquals($userimageurl, $tp->resourceLink->getSetting('custom_user_image'));

        $username = helper::create_username($tp->consumer->getKey(), $tp->user->ltiUserId);
        $user = $DB->get_record('user', ['username' => $username]);
        // User was found.
        $this->assertNotFalse($user);
        // User picture was set.
        $this->assertNotEmpty($user->picture);
    }

    /**
     * Test for tool_provider::onLaunch() to verify that a LTI user has been enrolled.
     */
    public function test_on_launch_user_enrolment(): void {
        global $DB;

        $tp = $this->build_dummy_tp($this->tool->secret);

        // Suppress output and session header errors.
        ob_start();
        @$tp->onLaunch();
        ob_end_clean();

        $username = helper::create_username($tp->consumer->getKey(), $tp->user->ltiUserId);
        $user = $DB->get_record('user', ['username' => $username]);
        // User was found.
        $this->assertNotFalse($user);
        // User picture was not set.
        $this->assertEmpty($user->picture);

        // Check user enrolment.
        $enrolled = $DB->record_exists('user_enrolments', ['enrolid' => $this->tool->enrolid, 'userid' => $user->id]);
        $this->assertTrue($enrolled);
    }

    /**
     * Test for tool_provider::onLaunch() when the consumer object has not been set.
     */
    public function test_on_launch_no_consumer(): void {
        global $DB;

        $tool = $this->tool;

        $tp = new dummy_tool_provider($tool->id);
        $tp->onLaunch();
        $this->assertFalse($tp->ok);
        $this->assertEquals(get_string('invalidtoolconsumer', 'enrol_lti'), $tp->message);

        // Check published tool and tool consumer has not yet been mapped due to failure.
        $mappingparams = [
            'toolid' => $tool->id
        ];
        $this->assertFalse($DB->record_exists('enrol_lti_tool_consumer_map', $mappingparams));
    }

    /**
     * Test for tool_provider::onLaunch() when we have a non-existent consumer data.
     */
    public function test_on_launch_invalid_consumer(): void {
        $tool = $this->tool;

        $dataconnector = new data_connector();
        // Build consumer object but don't save it.
        $consumer = new dummy_tool_consumer('testkey', $dataconnector);

        $tp = new dummy_tool_provider($tool->id);
        $tp->consumer = $consumer;
        $tp->onLaunch();
        $this->assertFalse($tp->ok);
        $this->assertEquals(get_string('invalidtoolconsumer', 'enrol_lti'), $tp->message);
    }

    /**
     * Test for tool_provider::map_tool_to_consumer().
     */
    public function test_map_tool_to_consumer(): void {
        global $DB;

        $tp = $this->build_dummy_tp();
        $tp->map_tool_to_consumer();

        // Check published tool and tool consumer mapping.
        $mappingparams = [
            'toolid' => $this->tool->id,
            'consumerid' => $tp->consumer->getRecordId()
        ];
        $this->assertTrue($DB->record_exists('enrol_lti_tool_consumer_map', $mappingparams));
    }

    /**
     * Test for tool_provider::map_tool_to_consumer().
     */
    public function test_map_tool_to_consumer_no_consumer(): void {
        $tp = new dummy_tool_provider($this->tool->id);
        $this->expectException('moodle_exception');
        $tp->map_tool_to_consumer();
    }

    /**
     * Builds a dummy tool provider object.
     *
     * @param string $secret Consumer secret.
     * @param array|\stdClass $proxy Tool proxy data.
     * @param null $resourcelinksettings Key-value array for resource link settings.
     * @return dummy_tool_provider
     */
    protected function build_dummy_tp($secret = null, $proxy = null, $resourcelinksettings = null) {
        $tool = $this->tool;

        $dataconnector = new data_connector();
        $consumer = new ToolConsumer('testkey', $dataconnector);

        $ltiversion = ToolProvider::LTI_VERSION2;
        if ($secret === null && $proxy === null) {
            $consumer->secret = $tool->secret;
            $ltiversion = ToolProvider::LTI_VERSION1;
        } else {
            $consumer->secret = $secret;
        }
        $consumer->ltiVersion = $ltiversion;

        $consumer->name = 'TEST CONSUMER NAME';
        $consumer->consumerName = 'TEST CONSUMER INSTANCE NAME';
        $consumer->consumerGuid = 'TEST CONSUMER INSTANCE GUID';
        $consumer->consumerVersion = 'TEST CONSUMER INFO VERSION';
        $consumer->enabled = true;
        $consumer->protected = true;
        if ($proxy !== null) {
            $consumer->toolProxy = json_encode($proxy);
        }
        $consumer->save();

        $resourcelink = ResourceLink::fromConsumer($consumer, 'testresourcelinkid');
        if (!empty($resourcelinksettings)) {
            foreach ($resourcelinksettings as $setting => $value) {
                $resourcelink->setSetting($setting, $value);
            }
        }
        $resourcelink->save();

        $ltiuser = User::fromResourceLink($resourcelink, '');
        $ltiuser->ltiResultSourcedId = 'testLtiResultSourcedId';
        $ltiuser->ltiUserId = 'testuserid';
        $ltiuser->email = 'user1@example.com';
        $ltiuser->save();

        $tp = new dummy_tool_provider($tool->id);
        $tp->user = $ltiuser;
        $tp->resourceLink = $resourcelink;
        $tp->consumer = $consumer;

        return $tp;
    }
}

/**
 * Class dummy_tool_provider.
 *
 * A class that extends tool_provider so that we can expose the protected methods that we have overridden.
 *
 * @copyright 2016 Jun Pataleta <jun@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class dummy_tool_provider extends tool_provider {

    /**
     * Exposes tool_provider::onError().
     */
    public function onError() {
        parent::onError();
    }

    /**
     * Exposes tool_provider::onLaunch().
     */
    public function onLaunch() {
        parent::onLaunch();
    }

    /**
     * Exposes tool_provider::onRegister().
     */
    public function onRegister() {
        parent::onRegister();
    }

    /**
     * Expose protected variable errorOutput.
     *
     * @return string
     */
    public function get_error_output() {
        return $this->errorOutput;
    }
}

/**
 * Class dummy_tool_consumer
 *
 * A class that extends ToolConsumer in order to override and simulate sending and receiving data to tool consumer endpoint.
 *
 * @copyright 2016 Jun Pataleta <jun@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class dummy_tool_consumer extends ToolConsumer {

    /**
     * @var bool Flag to indicate whether to send an OK response or a failed response.
     */
    protected $success = false;

    /**
     * dummy_tool_consumer constructor.
     *
     * @param null|string $key
     * @param mixed|null $dataconnector
     * @param bool $autoenable
     * @param bool $success
     */
    public function __construct($key = null, $dataconnector = null, $autoenable = false, $success = false) {
        parent::__construct($key, $dataconnector, $autoenable);
        $this->success = $success;
    }

    /**
     * Override ToolConsumer::doServiceRequest() to simulate sending/receiving data to and from the tool consumer.
     *
     * @param object $service
     * @param string $method
     * @param string $format
     * @param mixed $data
     * @return HTTPMessage
     */
    public function doServiceRequest($service, $method, $format, $data) {
        $response = (object)['tool_proxy_guid' => 1];
        $header = ToolConsumer::addSignature($service->endpoint, $this->getKey(), $this->secret, $data, $method, $format);
        $http = new HTTPMessage($service->endpoint, $method, $data, $header);

        if ($this->success) {
            $http->responseJson = $response;
            $http->ok = true;
            $http->status = 201;
        }

        return $http;
    }
}

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
//
// This file is part of BasicLTI4Moodle
//
// BasicLTI4Moodle is an IMS BasicLTI (Basic Learning Tools for Interoperability)
// consumer for Moodle 1.9 and Moodle 2.0. BasicLTI is a IMS Standard that allows web
// based learning tools to be easily integrated in LMS as native ones. The IMS BasicLTI
// specification is part of the IMS standard Common Cartridge 1.1 Sakai and other main LMS
// are already supporting or going to support BasicLTI. This project Implements the consumer
// for Moodle. Moodle is a Free Open source Learning Management System by Martin Dougiamas.
// BasicLTI4Moodle is a project iniciated and leaded by Ludo(Marc Alier) and Jordi Piguillem
// at the GESSI research group at UPC.
// SimpleLTI consumer for Moodle is an implementation of the early specification of LTI
// by Charles Severance (Dr Chuck) htp://dr-chuck.com , developed by Jordi Piguillem in a
// Google Summer of Code 2008 project co-mentored by Charles Severance and Marc Alier.
//
// BasicLTI4Moodle is copyright 2009 by Marc Alier Forment, Jordi Piguillem and Nikolas Galanis
// of the Universitat Politecnica de Catalunya http://www.upc.edu
// Contact info: Marc Alier Forment granludo @ gmail.com or marc.alier @ upc.edu.

/**
 * This file contains unit tests for (some of) lti/locallib.php
 *
 * @package    mod_lti
 * @category   phpunit
 * @copyright  2009 Marc Alier, Jordi Piguillem, Nikolas Galanis
 * @copyright  2009 Universitat Politecnica de Catalunya http://www.upc.edu
 * @author     Charles Severance csev@unmich.edu
 * @author     Marc Alier (marc.alier@upc.edu)
 * @author     Jordi Piguillem
 * @author     Nikolas Galanis
 * @author     Chris Scribner
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_lti;

use mod_lti_external;
use mod_lti_testcase;

defined('MOODLE_INTERNAL') || die;

global $CFG;
require_once($CFG->dirroot . '/mod/lti/locallib.php');
require_once($CFG->dirroot . '/mod/lti/servicelib.php');
require_once($CFG->dirroot . '/mod/lti/tests/mod_lti_testcase.php');

/**
 * Local library tests
 *
 * @package    mod_lti
 * @copyright  Copyright (c) 2012 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class locallib_test extends mod_lti_testcase {

    /**
     * @covers ::lti_split_parameters()
     *
     * Test the split parameters function
     */
    public function test_split_parameters() {
        $this->assertEquals(lti_split_parameters(''), array());
        $this->assertEquals(lti_split_parameters('a=1'), array('a' => '1'));
        $this->assertEquals(lti_split_parameters("a=1\nb=2"), array('a' => '1', 'b' => '2'));
        $this->assertEquals(lti_split_parameters("a=1\n\rb=2"), array('a' => '1', 'b' => '2'));
        $this->assertEquals(lti_split_parameters("a=1\r\nb=2"), array('a' => '1', 'b' => '2'));
    }

    public function test_split_custom_parameters() {
        $this->resetAfterTest();

        $tool = new \stdClass();
        $tool->enabledcapability = '';
        $tool->parameter = '';
        $tool->ltiversion = 'LTI-1p0';
        $this->assertEquals(lti_split_custom_parameters(null, $tool, array(), "x=1\ny=2", false),
            array('custom_x' => '1', 'custom_y' => '2'));

        // Check params with caps.
        $this->assertEquals(lti_split_custom_parameters(null, $tool, array(), "X=1", true),
            array('custom_x' => '1', 'custom_X' => '1'));

        // Removed repeat of previous test with a semicolon separator.

        $this->assertEquals(lti_split_custom_parameters(null, $tool, array(), 'Review:Chapter=1.2.56', true),
            array(
                'custom_review_chapter' => '1.2.56',
                'custom_Review:Chapter' => '1.2.56'));

        $this->assertEquals(lti_split_custom_parameters(null, $tool, array(),
            'Complex!@#$^*(){}[]KEY=Complex!@#$^*;(){}[]½Value', true),
            array(
                'custom_complex____________key' => 'Complex!@#$^*;(){}[]½Value',
                'custom_Complex!@#$^*(){}[]KEY' => 'Complex!@#$^*;(){}[]½Value'));

        // Test custom parameter that returns $USER property.
        $user = $this->getDataGenerator()->create_user(array('middlename' => 'SOMETHING'));
        $this->setUser($user);
        $this->assertEquals(array('custom_x' => '1', 'custom_y' => 'SOMETHING'),
            lti_split_custom_parameters(null, $tool, array(), "x=1\ny=\$Person.name.middle", false));
    }

    /**
     * This test has been disabled because the test-tool is
     * being moved and probably it won't work anymore for this.
     * We should be testing here local stuff only and leave
     * outside-checks to the conformance tests. MDL-30347
     */
    public function disabled_test_sign_parameters() {
        $correct = array ( 'context_id' => '12345', 'context_label' => 'SI124', 'context_title' => 'Social Computing',
            'ext_submit' => 'Click Me', 'lti_message_type' => 'basic-lti-launch-request', 'lti_version' => 'LTI-1p0',
            'oauth_consumer_key' => 'lmsng.school.edu', 'oauth_nonce' => '47458148e33a8f9dafb888c3684cf476',
            'oauth_signature' => 'qWgaBIezihCbeHgcwUy14tZcyDQ=', 'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => '1307141660', 'oauth_version' => '1.0', 'resource_link_id' => '123',
            'resource_link_title' => 'Weekly Blog', 'roles' => 'Learner', 'tool_consumer_instance_guid' => 'lmsng.school.edu',
            'user_id' => '789');

        $requestparams = array('resource_link_id' => '123', 'resource_link_title' => 'Weekly Blog', 'user_id' => '789',
            'roles' => 'Learner', 'context_id' => '12345', 'context_label' => 'SI124', 'context_title' => 'Social Computing');

        $parms = lti_sign_parameters($requestparams, 'http://www.imsglobal.org/developer/LTI/tool.php', 'POST',
            'lmsng.school.edu', 'secret', 'Click Me', 'lmsng.school.edu' /*, $org_desc*/);
        $this->assertTrue(isset($parms['oauth_nonce']));
        $this->assertTrue(isset($parms['oauth_signature']));
        $this->assertTrue(isset($parms['oauth_timestamp']));

        // Those things that are hard to mock.
        $correct['oauth_nonce'] = $parms['oauth_nonce'];
        $correct['oauth_signature'] = $parms['oauth_signature'];
        $correct['oauth_timestamp'] = $parms['oauth_timestamp'];
        ksort($parms);
        ksort($correct);
        $this->assertEquals($parms, $correct);
    }

    /**
     * This test has been disabled because, since its creation,
     * the sourceId generation has changed and surely this is outdated.
     * Some day these should be replaced by proper tests, but until then
     * conformance tests say this is working. MDL-30347
     */
    public function disabled_test_parse_grade_replace_message() {
        $message = '
            <imsx_POXEnvelopeRequest xmlns = "http://www.imsglobal.org/services/ltiv1p1/xsd/imsoms_v1p0">
              <imsx_POXHeader>
                <imsx_POXRequestHeaderInfo>
                  <imsx_version>V1.0</imsx_version>
                  <imsx_messageIdentifier>999998123</imsx_messageIdentifier>
                </imsx_POXRequestHeaderInfo>
              </imsx_POXHeader>
              <imsx_POXBody>
                <replaceResultRequest>
                  <resultRecord>
                    <sourcedGUID>
                      <sourcedId>' .
            '{&quot;data&quot;:{&quot;instanceid&quot;:&quot;2&quot;,&quot;userid&quot;:&quot;2&quot;},&quot;hash&quot;:' .
            '&quot;0b5078feab59b9938c333ceaae21d8e003a7b295e43cdf55338445254421076b&quot;}' .
                      '</sourcedId>
                    </sourcedGUID>
                    <result>
                      <resultScore>
                        <language>en-us</language>
                        <textString>0.92</textString>
                      </resultScore>
                    </result>
                  </resultRecord>
                </replaceResultRequest>
              </imsx_POXBody>
            </imsx_POXEnvelopeRequest>
';

        $parsed = lti_parse_grade_replace_message(new SimpleXMLElement($message));

        $this->assertEquals($parsed->userid, '2');
        $this->assertEquals($parsed->instanceid, '2');
        $this->assertEquals($parsed->sourcedidhash, '0b5078feab59b9938c333ceaae21d8e003a7b295e43cdf55338445254421076b');

        $ltiinstance = (object)array('servicesalt' => '4e5fcc06de1d58.44963230');

        lti_verify_sourcedid($ltiinstance, $parsed);
    }

    public function test_lti_ensure_url_is_https() {
        $this->assertEquals('https://moodle.org', lti_ensure_url_is_https('http://moodle.org'));
        $this->assertEquals('https://moodle.org', lti_ensure_url_is_https('moodle.org'));
        $this->assertEquals('https://moodle.org', lti_ensure_url_is_https('https://moodle.org'));
    }

    /**
     * Test lti_get_url_thumbprint against various URLs
     */
    public function test_lti_get_url_thumbprint() {
        // Note: trailing and double slash are expected right now.  Must evaluate if it must be removed at some point.
        $this->assertEquals('moodle.org/', lti_get_url_thumbprint('http://MOODLE.ORG'));
        $this->assertEquals('moodle.org/', lti_get_url_thumbprint('http://www.moodle.org'));
        $this->assertEquals('moodle.org/', lti_get_url_thumbprint('https://www.moodle.org'));
        $this->assertEquals('moodle.org/', lti_get_url_thumbprint('moodle.org'));
        $this->assertEquals('moodle.org//this/is/moodle', lti_get_url_thumbprint('http://moodle.org/this/is/moodle'));
        $this->assertEquals('moodle.org//this/is/moodle', lti_get_url_thumbprint('https://moodle.org/this/is/moodle'));
        $this->assertEquals('moodle.org//this/is/moodle', lti_get_url_thumbprint('moodle.org/this/is/moodle'));
        $this->assertEquals('moodle.org//this/is/moodle', lti_get_url_thumbprint('moodle.org/this/is/moodle?'));
        $this->assertEquals('moodle.org//this/is/moodle?foo=bar', lti_get_url_thumbprint('moodle.org/this/is/moodle?foo=bar'));
    }

    /*
     * Verify that lti_build_request does handle resource_link_id as expected
     */
    public function test_lti_buid_request_resource_link_id() {
        $this->resetAfterTest();

        self::setUser($this->getDataGenerator()->create_user());
        $course   = $this->getDataGenerator()->create_course();
        $instance = $this->getDataGenerator()->create_module('lti', array(
            'intro'       => "<p>This</p>\nhas\r\n<p>some</p>\nnew\n\rlines",
            'introformat' => FORMAT_HTML,
            'course'      => $course->id,
        ));

        $typeconfig = array(
            'acceptgrades'     => 1,
            'forcessl'         => 0,
            'sendname'         => 2,
            'sendemailaddr'    => 2,
            'customparameters' => '',
        );

        // Normal call, we expect $instance->id to be used as resource_link_id.
        $params = lti_build_request($instance, $typeconfig, $course, null);
        $this->assertSame($instance->id, $params['resource_link_id']);

        // If there is a resource_link_id set, it gets precedence.
        $instance->resource_link_id = $instance->id + 99;
        $params = lti_build_request($instance, $typeconfig, $course, null);
        $this->assertSame($instance->resource_link_id, $params['resource_link_id']);

        // With none set, resource_link_id is not set either.
        unset($instance->id);
        unset($instance->resource_link_id);
        $params = lti_build_request($instance, $typeconfig, $course, null);
        $this->assertArrayNotHasKey('resource_link_id', $params);
    }

    /**
     * Test lti_build_request's resource_link_description and ensure
     * that the newlines in the description are correct.
     */
    public function test_lti_build_request_description() {
        $this->resetAfterTest();

        self::setUser($this->getDataGenerator()->create_user());
        $course   = $this->getDataGenerator()->create_course();
        $instance = $this->getDataGenerator()->create_module('lti', array(
            'intro'       => "<p>This</p>\nhas\r\n<p>some</p>\nnew\n\rlines",
            'introformat' => FORMAT_HTML,
            'course'      => $course->id,
        ));

        $typeconfig = array(
            'acceptgrades'     => 1,
            'forcessl'         => 0,
            'sendname'         => 2,
            'sendemailaddr'    => 2,
            'customparameters' => '',
        );

        $params = lti_build_request($instance, $typeconfig, $course, null);

        $ncount = substr_count($params['resource_link_description'], "\n");
        $this->assertGreaterThan(0, $ncount);

        $rcount = substr_count($params['resource_link_description'], "\r");
        $this->assertGreaterThan(0, $rcount);

        $this->assertEquals($ncount, $rcount, 'The number of \n characters should be the same as the number of \r characters');

        $rncount = substr_count($params['resource_link_description'], "\r\n");
        $this->assertGreaterThan(0, $rncount);

        $this->assertEquals($ncount, $rncount, 'All newline characters should be a combination of \r\n');
    }

    /**
     * Tests lti_prepare_type_for_save's handling of the "Force SSL" configuration.
     */
    public function test_lti_prepare_type_for_save_forcessl() {
        $type = new \stdClass();
        $config = new \stdClass();

        // Try when the forcessl config property is not set.
        lti_prepare_type_for_save($type, $config);
        $this->assertObjectHasAttribute('lti_forcessl', $config);
        $this->assertEquals(0, $config->lti_forcessl);
        $this->assertEquals(0, $type->forcessl);

        // Try when forcessl config property is set.
        $config->lti_forcessl = 1;
        lti_prepare_type_for_save($type, $config);
        $this->assertObjectHasAttribute('lti_forcessl', $config);
        $this->assertEquals(1, $config->lti_forcessl);
        $this->assertEquals(1, $type->forcessl);

        // Try when forcessl config property is set to 0.
        $config->lti_forcessl = 0;
        lti_prepare_type_for_save($type, $config);
        $this->assertObjectHasAttribute('lti_forcessl', $config);
        $this->assertEquals(0, $config->lti_forcessl);
        $this->assertEquals(0, $type->forcessl);
    }

    /**
     * Tests lti_load_type_from_cartridge and lti_load_type_if_cartridge
     */
    public function test_lti_load_type_from_cartridge() {
        $type = new \stdClass();
        $type->lti_toolurl = $this->getExternalTestFileUrl('/ims_cartridge_basic_lti_link.xml');

        lti_load_type_if_cartridge($type);

        $this->assertEquals('Example tool', $type->lti_typename);
        $this->assertEquals('Example tool description', $type->lti_description);
        $this->assertEquals('http://www.example.com/lti/provider.php', $type->lti_toolurl);
        $this->assertEquals('http://download.moodle.org/unittest/test.jpg', $type->lti_icon);
        $this->assertEquals('https://download.moodle.org/unittest/test.jpg', $type->lti_secureicon);
    }

    /**
     * Tests lti_load_tool_from_cartridge and lti_load_tool_if_cartridge
     */
    public function test_lti_load_tool_from_cartridge() {
        $lti = new \stdClass();
        $lti->toolurl = $this->getExternalTestFileUrl('/ims_cartridge_basic_lti_link.xml');

        lti_load_tool_if_cartridge($lti);

        $this->assertEquals('Example tool', $lti->name);
        $this->assertEquals('Example tool description', $lti->intro);
        $this->assertEquals('http://www.example.com/lti/provider.php', $lti->toolurl);
        $this->assertEquals('https://www.example.com/lti/provider.php', $lti->securetoolurl);
        $this->assertEquals('http://download.moodle.org/unittest/test.jpg', $lti->icon);
        $this->assertEquals('https://download.moodle.org/unittest/test.jpg', $lti->secureicon);
    }

    /**
     * Tests for lti_build_content_item_selection_request().
     */
    public function test_lti_build_content_item_selection_request() {
        $this->resetAfterTest();

        $this->setAdminUser();
        // Create a tool proxy.
        $proxy = mod_lti_external::create_tool_proxy('Test proxy', $this->getExternalTestFileUrl('/test.html'), array(), array());

        // Create a tool type, associated with that proxy.
        $type = new \stdClass();
        $data = new \stdClass();
        $data->lti_contentitem = true;
        $type->state = LTI_TOOL_STATE_CONFIGURED;
        $type->name = "Test tool";
        $type->description = "Example description";
        $type->toolproxyid = $proxy->id;
        $type->baseurl = $this->getExternalTestFileUrl('/test.html');

        $typeid = lti_add_type($type, $data);

        $typeconfig = lti_get_type_config($typeid);

        $course = $this->getDataGenerator()->create_course();
        $returnurl = new \moodle_url('/');

        // Default parameters.
        $result = lti_build_content_item_selection_request($typeid, $course, $returnurl);
        $this->assertNotEmpty($result);
        $this->assertNotEmpty($result->params);
        $this->assertNotEmpty($result->url);
        $params = $result->params;
        $url = $result->url;
        $this->assertEquals($typeconfig['toolurl'], $url);
        $this->assertEquals('ContentItemSelectionRequest', $params['lti_message_type']);
        $this->assertEquals(LTI_VERSION_1, $params['lti_version']);
        $this->assertEquals('application/vnd.ims.lti.v1.ltilink', $params['accept_media_types']);
        $this->assertEquals('frame,iframe,window', $params['accept_presentation_document_targets']);
        $this->assertEquals($returnurl->out(false), $params['content_item_return_url']);
        $this->assertEquals('false', $params['accept_unsigned']);
        $this->assertEquals('true', $params['accept_multiple']);
        $this->assertEquals('false', $params['accept_copy_advice']);
        $this->assertEquals('false', $params['auto_create']);
        $this->assertEquals($type->name, $params['title']);
        $this->assertFalse(isset($params['resource_link_id']));
        $this->assertFalse(isset($params['resource_link_title']));
        $this->assertFalse(isset($params['resource_link_description']));
        $this->assertFalse(isset($params['launch_presentation_return_url']));
        $this->assertFalse(isset($params['lis_result_sourcedid']));
        $this->assertEquals($params['tool_consumer_instance_guid'], 'www.example.com');

        // Custom parameters.
        $title = 'My custom title';
        $text = 'This is the tool description';
        $mediatypes = ['image/*', 'video/*'];
        $targets = ['embed', 'iframe'];
        $result = lti_build_content_item_selection_request($typeid, $course, $returnurl, $title, $text, $mediatypes, $targets,
            true, true, true, true, true);
        $this->assertNotEmpty($result);
        $this->assertNotEmpty($result->params);
        $this->assertNotEmpty($result->url);
        $params = $result->params;
        $this->assertEquals(implode(',', $mediatypes), $params['accept_media_types']);
        $this->assertEquals(implode(',', $targets), $params['accept_presentation_document_targets']);
        $this->assertEquals('true', $params['accept_unsigned']);
        $this->assertEquals('true', $params['accept_multiple']);
        $this->assertEquals('true', $params['accept_copy_advice']);
        $this->assertEquals('true', $params['auto_create']);
        $this->assertEquals($title, $params['title']);
        $this->assertEquals($text, $params['text']);

        // Invalid flag values.
        $result = lti_build_content_item_selection_request($typeid, $course, $returnurl, $title, $text, $mediatypes, $targets,
            'aa', -1, 0, 1, 0xabc);
        $this->assertNotEmpty($result);
        $this->assertNotEmpty($result->params);
        $this->assertNotEmpty($result->url);
        $params = $result->params;
        $this->assertEquals(implode(',', $mediatypes), $params['accept_media_types']);
        $this->assertEquals(implode(',', $targets), $params['accept_presentation_document_targets']);
        $this->assertEquals('false', $params['accept_unsigned']);
        $this->assertEquals('false', $params['accept_multiple']);
        $this->assertEquals('false', $params['accept_copy_advice']);
        $this->assertEquals('false', $params['auto_create']);
        $this->assertEquals($title, $params['title']);
        $this->assertEquals($text, $params['text']);
    }

    /**
     * Test for lti_build_content_item_selection_request() with nonexistent tool type ID parameter.
     */
    public function test_lti_build_content_item_selection_request_invalid_tooltype() {
        $this->resetAfterTest();

        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $returnurl = new \moodle_url('/');

        // Should throw Exception on non-existent tool type.
        $this->expectException('moodle_exception');
        lti_build_content_item_selection_request(1, $course, $returnurl);
    }

    /**
     * Test for lti_build_content_item_selection_request() with invalid media types parameter.
     */
    public function test_lti_build_content_item_selection_request_invalid_mediatypes() {
        $this->resetAfterTest();

        $this->setAdminUser();

        // Create a tool type, associated with that proxy.
        $type = new \stdClass();
        $data = new \stdClass();
        $data->lti_contentitem = true;
        $type->state = LTI_TOOL_STATE_CONFIGURED;
        $type->name = "Test tool";
        $type->description = "Example description";
        $type->baseurl = $this->getExternalTestFileUrl('/test.html');

        $typeid = lti_add_type($type, $data);
        $course = $this->getDataGenerator()->create_course();
        $returnurl = new \moodle_url('/');

        // Should throw coding_exception on non-array media types.
        $mediatypes = 'image/*,video/*';
        $this->expectException('coding_exception');
        lti_build_content_item_selection_request($typeid, $course, $returnurl, '', '', $mediatypes);
    }

    /**
     * Test for lti_build_content_item_selection_request() with invalid presentation targets parameter.
     */
    public function test_lti_build_content_item_selection_request_invalid_presentationtargets() {
        $this->resetAfterTest();

        $this->setAdminUser();

        // Create a tool type, associated with that proxy.
        $type = new \stdClass();
        $data = new \stdClass();
        $data->lti_contentitem = true;
        $type->state = LTI_TOOL_STATE_CONFIGURED;
        $type->name = "Test tool";
        $type->description = "Example description";
        $type->baseurl = $this->getExternalTestFileUrl('/test.html');

        $typeid = lti_add_type($type, $data);
        $course = $this->getDataGenerator()->create_course();
        $returnurl = new \moodle_url('/');

        // Should throw coding_exception on non-array presentation targets.
        $targets = 'frame,iframe';
        $this->expectException('coding_exception');
        lti_build_content_item_selection_request($typeid, $course, $returnurl, '', '', [], $targets);
    }

    /**
     * Provider for test_lti_get_best_tool_by_url.
     *
     * @return array of [urlToTest, expectedTool, allTools]
     */
    public function lti_get_best_tool_by_url_provider() {
        $tools = [
            (object) [
                'name' => 'Here',
                'baseurl' => 'https://example.com/i/am/?where=here',
                'tooldomain' => 'example.com',
                'state' => LTI_TOOL_STATE_CONFIGURED,
                'course' => SITEID
            ],
            (object) [
                'name' => 'There',
                'baseurl' => 'https://example.com/i/am/?where=there',
                'tooldomain' => 'example.com',
                'state' => LTI_TOOL_STATE_CONFIGURED,
                'course' => SITEID
            ],
            (object) [
                'name' => 'Not here',
                'baseurl' => 'https://example.com/i/am/?where=not/here',
                'tooldomain' => 'example.com',
                'state' => LTI_TOOL_STATE_CONFIGURED,
                'course' => SITEID
            ],
            (object) [
                'name' => 'Here',
                'baseurl' => 'https://example.com/i/am/',
                'tooldomain' => 'example.com',
                'state' => LTI_TOOL_STATE_CONFIGURED,
                'course' => SITEID
            ],
            (object) [
                'name' => 'Here',
                'baseurl' => 'https://example.com/i/was',
                'tooldomain' => 'example.com',
                'state' => LTI_TOOL_STATE_CONFIGURED,
                'course' => SITEID
            ],
            (object) [
                'name' => 'Here',
                'baseurl' => 'https://badexample.com/i/am/?where=here',
                'tooldomain' => 'badexample.com',
                'state' => LTI_TOOL_STATE_CONFIGURED,
                'course' => SITEID
            ],
        ];

        $data = [
            [
                'url' => $tools[0]->baseurl,
                'expected' => $tools[0],
            ],
            [
                'url' => $tools[1]->baseurl,
                'expected' => $tools[1],
            ],
            [
                'url' => $tools[2]->baseurl,
                'expected' => $tools[2],
            ],
            [
                'url' => $tools[3]->baseurl,
                'expected' => $tools[3],
            ],
            [
                'url' => $tools[4]->baseurl,
                'expected' => $tools[4],
            ],
            [
                'url' => $tools[5]->baseurl,
                'expected' => $tools[5],
            ],
            [
                'url' => 'https://nomatch.com/i/am/',
                'expected' => null
            ],
            [
                'url' => 'https://example.com',
                'expected' => null
            ],
            [
                'url' => 'https://example.com/i/am/?where=unknown',
                'expected' => $tools[3]
            ]
        ];

        // Construct the final array as required by the provider API. Each row
        // of the array contains the URL to test, the expected tool, and
        // the complete list of tools.
        return array_map(function($data) use ($tools) {
            return [$data['url'], $data['expected'], $tools];
        }, $data);
    }

    /**
     * Test lti_get_best_tool_by_url.
     *
     * @dataProvider lti_get_best_tool_by_url_provider
     * @param string $url The URL to test.
     * @param object $expected The expected tool matching the URL.
     * @param array $tools The pool of tools to match the URL with.
     */
    public function test_lti_get_best_tool_by_url($url, $expected, $tools) {
        $actual = lti_get_best_tool_by_url($url, $tools, null);
        $this->assertSame($expected, $actual);
    }

    /**
     * Test lti_get_jwt_message_type_mapping().
     */
    public function test_lti_get_jwt_message_type_mapping() {
        $mapping = [
            'basic-lti-launch-request' => 'LtiResourceLinkRequest',
            'ContentItemSelectionRequest' => 'LtiDeepLinkingRequest',
            'LtiDeepLinkingResponse' => 'ContentItemSelection',
            'LtiSubmissionReviewRequest' => 'LtiSubmissionReviewRequest'
        ];

        $this->assertEquals($mapping, lti_get_jwt_message_type_mapping());
    }

    /**
     * Test lti_get_jwt_claim_mapping()
     */
    public function test_lti_get_jwt_claim_mapping() {
        $mapping = [
            'accept_copy_advice' => [
                'suffix' => 'dl',
                'group' => 'deep_linking_settings',
                'claim' => 'accept_copy_advice',
                'isarray' => false,
                'type' => 'boolean'
            ],
            'accept_media_types' => [
                'suffix' => 'dl',
                'group' => 'deep_linking_settings',
                'claim' => 'accept_media_types',
                'isarray' => true
            ],
            'accept_multiple' => [
                'suffix' => 'dl',
                'group' => 'deep_linking_settings',
                'claim' => 'accept_multiple',
                'isarray' => false,
                'type' => 'boolean'
            ],
            'accept_presentation_document_targets' => [
                'suffix' => 'dl',
                'group' => 'deep_linking_settings',
                'claim' => 'accept_presentation_document_targets',
                'isarray' => true
            ],
            'accept_types' => [
                'suffix' => 'dl',
                'group' => 'deep_linking_settings',
                'claim' => 'accept_types',
                'isarray' => true
            ],
            'accept_unsigned' => [
                'suffix' => 'dl',
                'group' => 'deep_linking_settings',
                'claim' => 'accept_unsigned',
                'isarray' => false,
                'type' => 'boolean'
            ],
            'auto_create' => [
                'suffix' => 'dl',
                'group' => 'deep_linking_settings',
                'claim' => 'auto_create',
                'isarray' => false,
                'type' => 'boolean'
            ],
            'can_confirm' => [
                'suffix' => 'dl',
                'group' => 'deep_linking_settings',
                'claim' => 'can_confirm',
                'isarray' => false,
                'type' => 'boolean'
            ],
            'content_item_return_url' => [
                'suffix' => 'dl',
                'group' => 'deep_linking_settings',
                'claim' => 'deep_link_return_url',
                'isarray' => false
            ],
            'content_items' => [
                'suffix' => 'dl',
                'group' => '',
                'claim' => 'content_items',
                'isarray' => true
            ],
            'data' => [
                'suffix' => 'dl',
                'group' => 'deep_linking_settings',
                'claim' => 'data',
                'isarray' => false
            ],
            'text' => [
                'suffix' => 'dl',
                'group' => 'deep_linking_settings',
                'claim' => 'text',
                'isarray' => false
            ],
            'title' => [
                'suffix' => 'dl',
                'group' => 'deep_linking_settings',
                'claim' => 'title',
                'isarray' => false
            ],
            'lti_msg' => [
                'suffix' => 'dl',
                'group' => '',
                'claim' => 'msg',
                'isarray' => false
            ],
            'lti_log' => [
                'suffix' => 'dl',
                'group' => '',
                'claim' => 'log',
                'isarray' => false
            ],
            'lti_errormsg' => [
                'suffix' => 'dl',
                'group' => '',
                'claim' => 'errormsg',
                'isarray' => false
            ],
            'lti_errorlog' => [
                'suffix' => 'dl',
                'group' => '',
                'claim' => 'errorlog',
                'isarray' => false
            ],
            'context_id' => [
                'suffix' => '',
                'group' => 'context',
                'claim' => 'id',
                'isarray' => false
            ],
            'context_label' => [
                'suffix' => '',
                'group' => 'context',
                'claim' => 'label',
                'isarray' => false
            ],
            'context_title' => [
                'suffix' => '',
                'group' => 'context',
                'claim' => 'title',
                'isarray' => false
            ],
            'context_type' => [
                'suffix' => '',
                'group' => 'context',
                'claim' => 'type',
                'isarray' => true
            ],
            'lis_course_offering_sourcedid' => [
                'suffix' => '',
                'group' => 'lis',
                'claim' => 'course_offering_sourcedid',
                'isarray' => false
            ],
            'lis_course_section_sourcedid' => [
                'suffix' => '',
                'group' => 'lis',
                'claim' => 'course_section_sourcedid',
                'isarray' => false
            ],
            'launch_presentation_css_url' => [
                'suffix' => '',
                'group' => 'launch_presentation',
                'claim' => 'css_url',
                'isarray' => false
            ],
            'launch_presentation_document_target' => [
                'suffix' => '',
                'group' => 'launch_presentation',
                'claim' => 'document_target',
                'isarray' => false
            ],
            'launch_presentation_height' => [
                'suffix' => '',
                'group' => 'launch_presentation',
                'claim' => 'height',
                'isarray' => false
            ],
            'launch_presentation_locale' => [
                'suffix' => '',
                'group' => 'launch_presentation',
                'claim' => 'locale',
                'isarray' => false
            ],
            'launch_presentation_return_url' => [
                'suffix' => '',
                'group' => 'launch_presentation',
                'claim' => 'return_url',
                'isarray' => false
            ],
            'launch_presentation_width' => [
                'suffix' => '',
                'group' => 'launch_presentation',
                'claim' => 'width',
                'isarray' => false
            ],
            'lis_person_contact_email_primary' => [
                'suffix' => '',
                'group' => null,
                'claim' => 'email',
                'isarray' => false
            ],
            'lis_person_name_family' => [
                'suffix' => '',
                'group' => null,
                'claim' => 'family_name',
                'isarray' => false
            ],
            'lis_person_name_full' => [
                'suffix' => '',
                'group' => null,
                'claim' => 'name',
                'isarray' => false
            ],
            'lis_person_name_given' => [
                'suffix' => '',
                'group' => null,
                'claim' => 'given_name',
                'isarray' => false
            ],
            'lis_person_sourcedid' => [
                'suffix' => '',
                'group' => 'lis',
                'claim' => 'person_sourcedid',
                'isarray' => false
            ],
            'user_id' => [
                'suffix' => '',
                'group' => null,
                'claim' => 'sub',
                'isarray' => false
            ],
            'user_image' => [
                'suffix' => '',
                'group' => null,
                'claim' => 'picture',
                'isarray' => false
            ],
            'roles' => [
                'suffix' => '',
                'group' => '',
                'claim' => 'roles',
                'isarray' => true
            ],
            'role_scope_mentor' => [
                'suffix' => '',
                'group' => '',
                'claim' => 'role_scope_mentor',
                'isarray' => false
            ],
            'deployment_id' => [
                'suffix' => '',
                'group' => '',
                'claim' => 'deployment_id',
                'isarray' => false
            ],
            'lti_message_type' => [
                'suffix' => '',
                'group' => '',
                'claim' => 'message_type',
                'isarray' => false
            ],
            'lti_version' => [
                'suffix' => '',
                'group' => '',
                'claim' => 'version',
                'isarray' => false
            ],
            'resource_link_description' => [
                'suffix' => '',
                'group' => 'resource_link',
                'claim' => 'description',
                'isarray' => false
            ],
            'resource_link_id' => [
                'suffix' => '',
                'group' => 'resource_link',
                'claim' => 'id',
                'isarray' => false
            ],
            'resource_link_title' => [
                'suffix' => '',
                'group' => 'resource_link',
                'claim' => 'title',
                'isarray' => false
            ],
            'tool_consumer_info_product_family_code' => [
                'suffix' => '',
                'group' => 'tool_platform',
                'claim' => 'product_family_code',
                'isarray' => false
            ],
            'tool_consumer_info_version' => [
                'suffix' => '',
                'group' => 'tool_platform',
                'claim' => 'version',
                'isarray' => false
            ],
            'tool_consumer_instance_contact_email' => [
                'suffix' => '',
                'group' => 'tool_platform',
                'claim' => 'contact_email',
                'isarray' => false
            ],
            'tool_consumer_instance_description' => [
                'suffix' => '',
                'group' => 'tool_platform',
                'claim' => 'description',
                'isarray' => false
            ],
            'tool_consumer_instance_guid' => [
                'suffix' => '',
                'group' => 'tool_platform',
                'claim' => 'guid',
                'isarray' => false
            ],
            'tool_consumer_instance_name' => [
                'suffix' => '',
                'group' => 'tool_platform',
                'claim' => 'name',
                'isarray' => false
            ],
            'tool_consumer_instance_url' => [
                'suffix' => '',
                'group' => 'tool_platform',
                'claim' => 'url',
                'isarray' => false
            ],
            'custom_context_memberships_v2_url' => [
                'suffix' => 'nrps',
                'group' => 'namesroleservice',
                'claim' => 'context_memberships_url',
                'isarray' => false
            ],
            'custom_context_memberships_versions' => [
                'suffix' => 'nrps',
                'group' => 'namesroleservice',
                'claim' => 'service_versions',
                'isarray' => true
            ],
            'custom_gradebookservices_scope' => [
                'suffix' => 'ags',
                'group' => 'endpoint',
                'claim' => 'scope',
                'isarray' => true
            ],
            'custom_lineitems_url' => [
                'suffix' => 'ags',
                'group' => 'endpoint',
                'claim' => 'lineitems',
                'isarray' => false
            ],
            'custom_lineitem_url' => [
                'suffix' => 'ags',
                'group' => 'endpoint',
                'claim' => 'lineitem',
                'isarray' => false
            ],
            'custom_results_url' => [
                'suffix' => 'ags',
                'group' => 'endpoint',
                'claim' => 'results',
                'isarray' => false
            ],
            'custom_result_url' => [
                'suffix' => 'ags',
                'group' => 'endpoint',
                'claim' => 'result',
                'isarray' => false
            ],
            'custom_scores_url' => [
                'suffix' => 'ags',
                'group' => 'endpoint',
                'claim' => 'scores',
                'isarray' => false
            ],
            'custom_score_url' => [
                'suffix' => 'ags',
                'group' => 'endpoint',
                'claim' => 'score',
                'isarray' => false
            ],
            'lis_outcome_service_url' => [
                'suffix' => 'bo',
                'group' => 'basicoutcome',
                'claim' => 'lis_outcome_service_url',
                'isarray' => false
            ],
            'lis_result_sourcedid' => [
                'suffix' => 'bo',
                'group' => 'basicoutcome',
                'claim' => 'lis_result_sourcedid',
                'isarray' => false
            ],
            'for_user_id' => [
                'suffix' => '',
                'group' => 'for_user',
                'claim' => 'user_id',
                'isarray' => false
            ],
        ];
        $actual = lti_get_jwt_claim_mapping();
        $this->assertEquals($mapping, $actual);
    }

    /**
     * Test lti_build_standard_message().
     */
    public function test_lti_build_standard_message_institution_name_set() {
        global $CFG;

        $this->resetAfterTest();

        $CFG->mod_lti_institution_name = 'some institution name lols';

        $course   = $this->getDataGenerator()->create_course();
        $instance = $this->getDataGenerator()->create_module('lti',
            [
                'course' => $course->id,
            ]
        );

        $message = lti_build_standard_message($instance, '2', LTI_VERSION_1);

        $this->assertEquals('moodle-2', $message['ext_lms']);
        $this->assertEquals('moodle', $message['tool_consumer_info_product_family_code']);
        $this->assertEquals(LTI_VERSION_1, $message['lti_version']);
        $this->assertEquals('basic-lti-launch-request', $message['lti_message_type']);
        $this->assertEquals('2', $message['tool_consumer_instance_guid']);
        $this->assertEquals('some institution name lols', $message['tool_consumer_instance_name']);
        $this->assertEquals('PHPUnit test site', $message['tool_consumer_instance_description']);
    }

    /**
     * Test lti_build_standard_message().
     */
    public function test_lti_build_standard_message_institution_name_not_set() {
        $this->resetAfterTest();

        $course   = $this->getDataGenerator()->create_course();
        $instance = $this->getDataGenerator()->create_module('lti',
            [
                'course' => $course->id,
            ]
        );

        $message = lti_build_standard_message($instance, '2', LTI_VERSION_2);

        $this->assertEquals('moodle-2', $message['ext_lms']);
        $this->assertEquals('moodle', $message['tool_consumer_info_product_family_code']);
        $this->assertEquals(LTI_VERSION_2, $message['lti_version']);
        $this->assertEquals('basic-lti-launch-request', $message['lti_message_type']);
        $this->assertEquals('2', $message['tool_consumer_instance_guid']);
        $this->assertEquals('phpunit', $message['tool_consumer_instance_name']);
        $this->assertEquals('PHPUnit test site', $message['tool_consumer_instance_description']);
    }

    /**
     * Test lti_verify_jwt_signature().
     */
    public function test_lti_verify_jwt_signature() {
        $this->resetAfterTest();

        $this->setAdminUser();

        // Create a tool type, associated with that proxy.
        $type = new \stdClass();
        $type->state = LTI_TOOL_STATE_CONFIGURED;
        $type->name = "Test tool";
        $type->description = "Example description";
        $type->baseurl = $this->getExternalTestFileUrl('/test.html');

        $config = new \stdClass();
        $config->lti_publickey = '-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAnzyis1ZjfNB0bBgKFMSv
vkTtwlvBsaJq7S5wA+kzeVOVpVWwkWdVha4s38XM/pa/yr47av7+z3VTmvDRyAHc
aT92whREFpLv9cj5lTeJSibyr/Mrm/YtjCZVWgaOYIhwrXwKLqPr/11inWsAkfIy
tvHWTxZYEcXLgAXFuUuaS3uF9gEiNQwzGTU1v0FqkqTBr4B8nW3HCN47XUu0t8Y0
e+lf4s4OxQawWD79J9/5d3Ry0vbV3Am1FtGJiJvOwRsIfVChDpYStTcHTCMqtvWb
V6L11BWkpzGXSW4Hv43qa+GSYOD2QU68Mb59oSk2OB+BtOLpJofmbGEGgvmwyCI9
MwIDAQAB
-----END PUBLIC KEY-----';

        $config->lti_keytype = LTI_RSA_KEY;

        $typeid = lti_add_type($type, $config);

        lti_verify_jwt_signature($typeid, '', 'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4g' .
            'RG9lIiwiYWRtaW4iOnRydWUsImlhdCI6MTUxNjIzOTAyMn0.POstGetfAytaZS82wHcjoTyoqhMyxXiWdR7Nn7A29DNSl0EiXLdwJ6xC6AfgZWF1bOs' .
            'S_TuYI3OG85AmiExREkrS6tDfTQ2B3WXlrr-wp5AokiRbz3_oB4OxG-W9KcEEbDRcZc0nH3L7LzYptiy1PtAylQGxHTWZXtGz4ht0bAecBgmpdgXMgu' .
            'EIcoqPJ1n3pIWk_dUZegpqx0Lka21H6XxUTxiy8OcaarA8zdnPUnV6AmNP3ecFawIFYdvJB_cm-GvpCSbr8G8y_Mllj8f4x9nBH8pQux89_6gUY618iY' .
            'v7tuPWBFfEbLxtF2pZS6YC1aSfLQxeNe8djT9YjpvRZA');
    }

    /**
     * Test lti_verify_jwt_signature_jwk().
     */
    public function test_lti_verify_jwt_signature_jwk() {
        $this->resetAfterTest();

        $this->setAdminUser();

        // Create a tool type, associated with that proxy.
        $type = new \stdClass();
        $type->state = LTI_TOOL_STATE_CONFIGURED;
        $type->name = "Test tool";
        $type->description = "Example description";
        $type->baseurl = $this->getExternalTestFileUrl('/test.html');

        $config = new \stdClass();
        $config->lti_publickeyset = $this->getExternalTestFileUrl('/lti_keyset.json');

        $config->lti_keytype = LTI_JWK_KEYSET;

        $typeid = lti_add_type($type, $config);

        $jwt = 'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCIsImtpZCI6IjU3YzExNzdkMmQ1M2EwMjFjNzM';
        $jwt .= '3NTY0OTFjMTM3YjE3In0.eyJpc3MiOiJnclJvbkd3RTd1WjRwZ28iLCJzdWIiOiJnclJvb';
        $jwt .= 'kd3RTd1WjRwZ28iLCJhdWQiOiJodHRwOi8vbG9jYWxob3N0L21vb2RsZS9tb2QvbHRpL3R';
        $jwt .= 'va2VuLnBocCIsImp0aSI6IjFlMUJPVEczVFJjbFdUem00dERsMGc9PSIsImlhdCI6MTU4M';
        $jwt .= 'Dg1NTUwNX0.Lowhc9ovNAXRb2rkAnv1oozDXlRD54Mz2JS1i8Zx4yGWQzmXzam-La19_g0';
        $jwt .= 'CTnwlKM6gxaInnRKFRAcwhJVcWec389liLAjMbna6d6iTWYTZr7q_4BIe3CT_oTMWASGta';
        $jwt .= 'Paaq53ch1rO4YdueEtmtd1K47ibo4Lhu1jmP_icc3lxjfnqiv4vIYdy7W2JQEzpk1ImuQr';
        $jwt .= 'AlO1xR3fZ6bgcJhVIaw5xoaZD3ZgEjuZOQXMkywv1bL-mL17RX336CzHd8rYZg82QXrBzb';
        $jwt .= 'NWzAlaZxv9VSug8t6mORvM6TkYYWjqEBKemgkD5rNh1BHrPcjWP7vy2Jz7YMjLsmuvDuLK';
        $jwt .= '_PHYIKL--s4gcXWoYmOu1vj-SgoPczTJPoiBD35hAKqVHy5ggHaYHBy95_bbcFd8H1smHw';
        $jwt .= 'pejrAFj1QAwGyTISLzUm08oq7Ak0tSxRKKXw4lpZAka1MmYxO3tJ_3-MXw6Bwz12bNgitJ';
        $jwt .= 'lQd6n3kkGLCJAmANeRkPsH6eZVwF0n2cjh2O1JAwyNcMD2vs4I8ftM1EqqoE2M3r6kt3AC';
        $jwt .= 'EscmqzizI3j80USBCLUUb1UTsfJb2g7oyApJAp-13Q3InR3QyvWO8unG5VraFE7IL5I28h';
        $jwt .= 'MkQAHuCI90DFmXB4leflAu7wNlIK_U8xkGl8X8Mnv6MWgg94Ki8jgIq_kA85JAqI';

        lti_verify_jwt_signature($typeid, '', $jwt);
    }

    /**
     * Test lti_verify_jwt_signature().
     */
    public function test_lti_verify_jwt_signature_with_lti2() {
        $this->resetAfterTest();

        $this->setAdminUser();

        // Create a tool proxy.
        $proxy = mod_lti_external::create_tool_proxy('Test proxy', $this->getExternalTestFileUrl('/test.html'), array(), array());

        // Create a tool type, associated with that proxy.
        $type = new \stdClass();
        $type->state = LTI_TOOL_STATE_CONFIGURED;
        $type->name = "Test tool";
        $type->description = "Example description";
        $type->toolproxyid = $proxy->id;
        $type->baseurl = $this->getExternalTestFileUrl('/test.html');

        $data = new \stdClass();
        $data->lti_contentitem = true;

        $typeid = lti_add_type($type, $data);

        $this->expectExceptionMessage('JWT security not supported with LTI 2');
        lti_verify_jwt_signature($typeid, '', '');
    }

    /**
     * Test lti_verify_jwt_signature().
     */
    public function test_lti_verify_jwt_signature_no_consumer_key() {
        $this->resetAfterTest();

        $this->setAdminUser();

        // Create a tool type, associated with that proxy.
        $type = new \stdClass();
        $type->state = LTI_TOOL_STATE_CONFIGURED;
        $type->name = "Test tool";
        $type->description = "Example description";
        $type->clientid = 'consumerkey';
        $type->baseurl = $this->getExternalTestFileUrl('/test.html');

        $config = new \stdClass();
        $typeid = lti_add_type($type, $config);

        $this->expectExceptionMessage(get_string('errorincorrectconsumerkey', 'mod_lti'));
        lti_verify_jwt_signature($typeid, '', '');
    }

    /**
     * Test lti_verify_jwt_signature().
     */
    public function test_lti_verify_jwt_signature_no_public_key() {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a tool type, associated with that proxy.
        $type = new \stdClass();
        $type->state = LTI_TOOL_STATE_CONFIGURED;
        $type->name = "Test tool";
        $type->description = "Example description";
        $type->clientid = 'consumerkey';
        $type->baseurl = $this->getExternalTestFileUrl('/test.html');

        $config = new \stdClass();
        $config->lti_keytype = LTI_RSA_KEY;
        $typeid = lti_add_type($type, $config);

        $this->expectExceptionMessage('No public key configured');
        lti_verify_jwt_signature($typeid, 'consumerkey', '');
    }

    /**
     * Test lti_convert_content_items().
     */
    public function test_lti_convert_content_items() {
        $contentitems = [];
        $contentitems[] = [
            'type' => 'ltiResourceLink',
            'url' => 'http://example.com/messages/launch',
            'title' => 'Test title',
            'text' => 'Test text',
            'iframe' => []
        ];
        $contentitems[] = [
            'type' => 'ltiResourceLink',
            'url' => 'http://example.com/messages/launch2',
            'title' => 'Test title2',
            'text' => 'Test text2',
            'iframe' => [
                'height' => 200,
                'width' => 300
            ],
            'window' => []
        ];
        $contentitems[] = [
            'type' => 'ltiResourceLink',
            'url' => 'http://example.com/messages/launch3',
            'title' => 'Test title3',
            'text' => 'Test text3',
            'window' => [
                'targetName' => 'test-win',
                'height' => 400
            ]
        ];

        $contentitems = json_encode($contentitems);

        $json = lti_convert_content_items($contentitems);

        $jsondecode = json_decode($json);

        $strcontext = '@context';
        $strgraph = '@graph';
        $strtype = '@type';

        $objgraph = new \stdClass();
        $objgraph->url = 'http://example.com/messages/launch';
        $objgraph->title = 'Test title';
        $objgraph->text = 'Test text';
        $objgraph->placementAdvice = new \stdClass();
        $objgraph->placementAdvice->presentationDocumentTarget = 'iframe';
        $objgraph->{$strtype} = 'LtiLinkItem';
        $objgraph->mediaType = 'application\/vnd.ims.lti.v1.ltilink';

        $objgraph2 = new \stdClass();
        $objgraph2->url = 'http://example.com/messages/launch2';
        $objgraph2->title = 'Test title2';
        $objgraph2->text = 'Test text2';
        $objgraph2->placementAdvice = new \stdClass();
        $objgraph2->placementAdvice->presentationDocumentTarget = 'iframe';
        $objgraph2->placementAdvice->displayHeight = 200;
        $objgraph2->placementAdvice->displayWidth = 300;
        $objgraph2->{$strtype} = 'LtiLinkItem';
        $objgraph2->mediaType = 'application\/vnd.ims.lti.v1.ltilink';

        $objgraph3 = new \stdClass();
        $objgraph3->url = 'http://example.com/messages/launch3';
        $objgraph3->title = 'Test title3';
        $objgraph3->text = 'Test text3';
        $objgraph3->placementAdvice = new \stdClass();
        $objgraph3->placementAdvice->presentationDocumentTarget = 'window';
        $objgraph3->placementAdvice->displayHeight = 400;
        $objgraph3->placementAdvice->windowTarget = 'test-win';
        $objgraph3->{$strtype} = 'LtiLinkItem';
        $objgraph3->mediaType = 'application\/vnd.ims.lti.v1.ltilink';

        $expected = new \stdClass();
        $expected->{$strcontext} = 'http://purl.imsglobal.org/ctx/lti/v1/ContentItem';
        $expected->{$strgraph} = [];
        $expected->{$strgraph}[] = $objgraph;
        $expected->{$strgraph}[] = $objgraph2;
        $expected->{$strgraph}[] = $objgraph3;

        $this->assertEquals($expected, $jsondecode);
    }

    /**
     * Test adding a single gradable item through content item.
     */
    public function test_lti_tool_configuration_from_content_item_single_gradable() {
        $this->resetAfterTest();
        $this->setAdminUser();

        $type = new \stdClass();
        $type->name = "Test tool";
        $type->baseurl = "http://example.com";
        $config = new \stdClass();
        $config->lti_acceptgrades = LTI_SETTING_DELEGATE;
        $typeid = lti_add_type($type, $config);

        $generator = $this->getDataGenerator()->get_plugin_generator('mod_lti');
        $contentitems = [];
        $contentitems[] = [
            'type' => 'ltiResourceLink',
            'url' => 'http://example.com/messages/launch',
            'title' => 'Test title',
            'lineItem' => [
                'resourceId' => 'r12345',
                'tag' => 'final',
                'scoreMaximum' => 10.0
            ],
            'frame' => []
        ];
        $contentitemsjson13 = json_encode($contentitems);
        $json11 = lti_convert_content_items($contentitemsjson13);

        $config = lti_tool_configuration_from_content_item($typeid,
                                                           'ContentItemSelection',
                                                           $type->ltiversion,
                                                           'ConsumerKey',
                                                           $json11);

        $this->assertEquals($contentitems[0]['url'], $config->toolurl);
        $this->assertEquals(LTI_SETTING_ALWAYS, $config->instructorchoiceacceptgrades);
        $this->assertEquals($contentitems[0]['lineItem']['tag'], $config->lineitemtag);
        $this->assertEquals($contentitems[0]['lineItem']['resourceId'], $config->lineitemresourceid);
        $this->assertEquals($contentitems[0]['lineItem']['scoreMaximum'], $config->grade_modgrade_point);
        $this->assertEquals('', $config->lineitemsubreviewurl);
        $this->assertEquals('', $config->lineitemsubreviewparams);
    }

    /**
     * @covers ::lti_tool_configuration_from_content_item()
     *
     * Test adding a single gradable item through content item with an empty subreview url.
     */
    public function test_lti_tool_configuration_from_content_item_single_gradable_subreview_default_emptyurl() {
        $this->resetAfterTest();
        $this->setAdminUser();

        $type = new \stdClass();
        $type->name = "Test tool";
        $type->baseurl = "http://example.com";
        $config = new \stdClass();
        $config->lti_acceptgrades = LTI_SETTING_DELEGATE;
        $typeid = lti_add_type($type, $config);

        $contentitems = [];
        $contentitems[] = [
            'type' => 'ltiResourceLink',
            'url' => 'http://example.com/messages/launch',
            'title' => 'Test title',
            'lineItem' => [
                'resourceId' => 'r12345',
                'tag' => 'final',
                'scoreMaximum' => 10.0,
                'submissionReview' => [
                    'url' => ''
                ]
            ],
            'frame' => []
        ];
        $contentitemsjson13 = json_encode($contentitems);
        $json11 = lti_convert_content_items($contentitemsjson13);

        $config = lti_tool_configuration_from_content_item($typeid,
                                                           'ContentItemSelection',
                                                           $type->ltiversion,
                                                           'ConsumerKey',
                                                           $json11);

        $this->assertEquals('DEFAULT', $config->lineitemsubreviewurl);
        $this->assertEquals('', $config->lineitemsubreviewparams);
    }

    /**
     * @covers ::lti_tool_configuration_from_content_item()
     *
     * Test adding a single gradable item through content item.
     */
    public function test_lti_tool_configuration_from_content_item_single_gradable_subreview_default() {
        $this->resetAfterTest();
        $this->setAdminUser();

        $type = new \stdClass();
        $type->name = "Test tool";
        $type->baseurl = "http://example.com";
        $config = new \stdClass();
        $config->lti_acceptgrades = LTI_SETTING_DELEGATE;
        $typeid = lti_add_type($type, $config);

        $contentitems = [];
        $contentitems[] = [
            'type' => 'ltiResourceLink',
            'url' => 'http://example.com/messages/launch',
            'title' => 'Test title',
            'lineItem' => [
                'resourceId' => 'r12345',
                'tag' => 'final',
                'scoreMaximum' => 10.0,
                'submissionReview' => []
            ],
            'frame' => []
        ];
        $contentitemsjson13 = json_encode($contentitems);
        $json11 = lti_convert_content_items($contentitemsjson13);

        $config = lti_tool_configuration_from_content_item($typeid,
                                                           'ContentItemSelection',
                                                           $type->ltiversion,
                                                           'ConsumerKey',
                                                           $json11);

        $this->assertEquals($contentitems[0]['url'], $config->toolurl);
        $this->assertEquals(LTI_SETTING_ALWAYS, $config->instructorchoiceacceptgrades);
        $this->assertEquals($contentitems[0]['lineItem']['tag'], $config->lineitemtag);
        $this->assertEquals($contentitems[0]['lineItem']['resourceId'], $config->lineitemresourceid);
        $this->assertEquals($contentitems[0]['lineItem']['scoreMaximum'], $config->grade_modgrade_point);
        $this->assertEquals('DEFAULT', $config->lineitemsubreviewurl);
        $this->assertEquals('', $config->lineitemsubreviewparams);
    }

    /**
     * Test adding multiple gradable items through content item.
     */
    public function test_lti_tool_configuration_from_content_item_multiple() {
        $this->resetAfterTest();
        $this->setAdminUser();

        $type = new \stdClass();
        $type->name = "Test tool";
        $type->baseurl = "http://example.com";
        $config = new \stdClass();
        $config->lti_acceptgrades = LTI_SETTING_DELEGATE;
        $typeid = lti_add_type($type, $config);

        $generator = $this->getDataGenerator()->get_plugin_generator('mod_lti');
        $contentitems = [];
        $contentitems[] = [
            'type' => 'ltiResourceLink',
            'url' => 'http://example.com/messages/launch',
            'title' => 'Test title',
            'text' => 'Test text',
            'icon' => [
                'url' => 'http://lti.example.com/image.jpg',
                'width' => 100
            ],
            'frame' => []
        ];
        $contentitems[] = [
            'type' => 'ltiResourceLink',
            'url' => 'http://example.com/messages/launchgraded',
            'title' => 'Test Graded',
            'lineItem' => [
                'resourceId' => 'r12345',
                'tag' => 'final',
                'scoreMaximum' => 10.0,
                'submissionReview' => [
                    'url' => 'https://testsub.url',
                    'custom' => ['a' => 'b']
                ]
            ],
            'frame' => []
        ];
        $contentitemsjson13 = json_encode($contentitems);
        $json11 = lti_convert_content_items($contentitemsjson13);

        $config = lti_tool_configuration_from_content_item($typeid,
                                                           'ContentItemSelection',
                                                           $type->ltiversion,
                                                           'ConsumerKey',
                                                           $json11);
        $this->assertNotNull($config->multiple);
        $this->assertEquals(2, count( $config->multiple ));
        $this->assertEquals($contentitems[0]['title'], $config->multiple[0]->name);
        $this->assertEquals($contentitems[0]['url'], $config->multiple[0]->toolurl);
        $this->assertEquals(LTI_SETTING_NEVER, $config->multiple[0]->instructorchoiceacceptgrades);
        $this->assertEquals($contentitems[1]['url'], $config->multiple[1]->toolurl);
        $this->assertEquals(LTI_SETTING_ALWAYS, $config->multiple[1]->instructorchoiceacceptgrades);
        $this->assertEquals($contentitems[1]['lineItem']['tag'], $config->multiple[1]->lineitemtag);
        $this->assertEquals($contentitems[1]['lineItem']['resourceId'], $config->multiple[1]->lineitemresourceid);
        $this->assertEquals($contentitems[1]['lineItem']['scoreMaximum'], $config->multiple[1]->grade_modgrade_point);
        $this->assertEquals($contentitems[1]['lineItem']['submissionReview']['url'], $config->multiple[1]->lineitemsubreviewurl);
        $this->assertEquals("a=b", $config->multiple[1]->lineitemsubreviewparams);
    }

    /**
     * Test adding a single non gradable item through content item.
     */
    public function test_lti_tool_configuration_from_content_item_single() {
        $this->resetAfterTest();
        $this->setAdminUser();

        $type = new \stdClass();
        $type->name = "Test tool";
        $type->baseurl = "http://example.com";
        $config = new \stdClass();
        $typeid = lti_add_type($type, $config);

        $generator = $this->getDataGenerator()->get_plugin_generator('mod_lti');
        $contentitems = [];
        $contentitems[] = [
            'type' => 'ltiResourceLink',
            'url' => 'http://example.com/messages/launch',
            'title' => 'Test title',
            'text' => 'Test text',
            'icon' => [
                'url' => 'http://lti.example.com/image.jpg',
                'width' => 100
            ],
            'frame' => []
        ];
        $contentitemsjson13 = json_encode($contentitems);
        $json11 = lti_convert_content_items($contentitemsjson13);

        $config = lti_tool_configuration_from_content_item($typeid,
                                                           'ContentItemSelection',
                                                           $type->ltiversion,
                                                           'ConsumerKey',
                                                           $json11);
        $this->assertEquals($contentitems[0]['title'], $config->name);
        $this->assertEquals($contentitems[0]['text'], $config->introeditor['text']);
        $this->assertEquals($contentitems[0]['url'], $config->toolurl);
        $this->assertEquals($contentitems[0]['icon']['url'], $config->icon);
        $this->assertEquals(LTI_SETTING_NEVER, $config->instructorchoiceacceptgrades);

    }

    /**
     * Test lti_sign_jwt().
     */
    public function test_lti_sign_jwt() {
        $this->resetAfterTest();

        $this->setAdminUser();

        // Create a tool type, associated with that proxy.
        $type = new \stdClass();
        $type->state = LTI_TOOL_STATE_CONFIGURED;
        $type->name = "Test tool";
        $type->description = "Example description";
        $type->clientid = 'consumerkey';
        $type->baseurl = $this->getExternalTestFileUrl('/test.html');

        $config = new \stdClass();
        $typeid = lti_add_type($type, $config);

        $params = [];
        $params['roles'] = 'urn:lti:role:ims/lis/testrole,' .
            'urn:lti:instrole:ims/lis/testinstrole,' .
            'urn:lti:sysrole:ims/lis/testsysrole,' .
            'hi';
        $params['accept_copy_advice'] = [
            'suffix' => 'dl',
            'group' => 'deep_linking_settings',
            'claim' => 'accept_copy_advice',
            'isarray' => false
        ];
        $params['lis_result_sourcedid'] = [
            'suffix' => 'bos',
            'group' => 'basicoutcomesservice',
            'claim' => 'lis_result_sourcedid',
            'isarray' => false
        ];
        $endpoint = 'https://www.example.com/moodle';
        $oauthconsumerkey = 'consumerkey';
        $nonce = '';

        $jwt = lti_sign_jwt($params, $endpoint, $oauthconsumerkey, $typeid, $nonce);

        $this->assertArrayHasKey('id_token', $jwt);
        $this->assertNotEmpty($jwt['id_token']);
    }

    /**
     * Test lti_convert_from_jwt()
     */
    public function test_lti_convert_from_jwt() {
        $this->resetAfterTest();

        $this->setAdminUser();

        // Create a tool type, associated with that proxy.
        $type = new \stdClass();
        $type->state = LTI_TOOL_STATE_CONFIGURED;
        $type->name = "Test tool";
        $type->description = "Example description";
        $type->clientid = 'sso.example.com';
        $type->baseurl = $this->getExternalTestFileUrl('/test.html');

        $config = new \stdClass();
        $config->lti_publickey = '-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAnzyis1ZjfNB0bBgKFMSv
vkTtwlvBsaJq7S5wA+kzeVOVpVWwkWdVha4s38XM/pa/yr47av7+z3VTmvDRyAHc
aT92whREFpLv9cj5lTeJSibyr/Mrm/YtjCZVWgaOYIhwrXwKLqPr/11inWsAkfIy
tvHWTxZYEcXLgAXFuUuaS3uF9gEiNQwzGTU1v0FqkqTBr4B8nW3HCN47XUu0t8Y0
e+lf4s4OxQawWD79J9/5d3Ry0vbV3Am1FtGJiJvOwRsIfVChDpYStTcHTCMqtvWb
V6L11BWkpzGXSW4Hv43qa+GSYOD2QU68Mb59oSk2OB+BtOLpJofmbGEGgvmwyCI9
MwIDAQAB
-----END PUBLIC KEY-----';
        $config->lti_keytype = LTI_RSA_KEY;

        $typeid = lti_add_type($type, $config);

        $params = lti_convert_from_jwt($typeid, 'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwib' .
            'mFtZSI6IkpvaG4gRG9lIiwiYWRtaW4iOnRydWUsImlhdCI6MTUxNjIzOTAyMiwiaXNzIjoic3NvLmV4YW1wbGUuY29tIn0.XURVvEb5ueAvFsn-S9EB' .
            'BSfKbsgUzfRQqmJ6evlrYdx7sXWoZXw1nYjaLTg-mawvBr7MVvrdG9qh6oN8OfkQ7bfMwiz4tjBMJ4B4q_sig5BDYIKwMNjZL5GGCBs89FQrgqZBhxw' .
            '3exTjPBEn69__w40o0AhCsBohPMh0ZsAyHug5dhm8vIuOP667repUJzM8uKCD6L4bEL6vQE8EwU6WQOmfJ2SDmRs-1pFkiaFd6hmPn6AVX7ETtzQmlT' .
            'X-nXe9weQjU1lH4AQG2Yfnn-7lS94bt6E76Zt-XndP3IY7W48EpnRfUK9Ff1fZlomT4MPahdNP1eP8gT2iMz7vYpCfmA');

        $this->assertEquals('sso.example.com', $params['oauth_consumer_key']);
        $this->assertEquals('John Doe', $params['lis_person_name_full']);
    }

    /**
     * Test lti_get_permitted_service_scopes().
     */
    public function test_lti_get_permitted_service_scopes() {
        $this->resetAfterTest();

        $this->setAdminUser();

        // Create a tool type, associated with that proxy.
        $type = new \stdClass();
        $type->state = LTI_TOOL_STATE_CONFIGURED;
        $type->name = "Test tool";
        $type->description = "Example description";
        $type->baseurl = $this->getExternalTestFileUrl('/test.html');

        $typeconfig = new \stdClass();
        $typeconfig->lti_acceptgrades = true;

        $typeid = lti_add_type($type, $typeconfig);

        $tool = lti_get_type($typeid);

        $config = lti_get_type_config($typeid);
        $permittedscopes = lti_get_permitted_service_scopes($tool, $config);

        $expected = [
            'https://purl.imsglobal.org/spec/lti-bo/scope/basicoutcome'
        ];
        $this->assertEquals($expected, $permittedscopes);
    }

    /**
     * Test get_tool_type_config().
     */
    public function test_get_tool_type_config() {
        $this->resetAfterTest();

        $this->setAdminUser();

        // Create a tool type, associated with that proxy.
        $type = new \stdClass();
        $type->state = LTI_TOOL_STATE_CONFIGURED;
        $type->name = "Test tool";
        $type->description = "Example description";
        $type->clientid = "Test client ID";
        $type->baseurl = $this->getExternalTestFileUrl('/test.html');

        $config = new \stdClass();

        $typeid = lti_add_type($type, $config);

        $type = lti_get_type($typeid);

        $typeconfig = get_tool_type_config($type);

        $this->assertEquals('https://www.example.com/moodle', $typeconfig['platformid']);
        $this->assertEquals($type->clientid, $typeconfig['clientid']);
        $this->assertEquals($typeid, $typeconfig['deploymentid']);
        $this->assertEquals('https://www.example.com/moodle/mod/lti/certs.php', $typeconfig['publickeyseturl']);
        $this->assertEquals('https://www.example.com/moodle/mod/lti/token.php', $typeconfig['accesstokenurl']);
        $this->assertEquals('https://www.example.com/moodle/mod/lti/auth.php', $typeconfig['authrequesturl']);
    }

    /**
     * Test lti_new_access_token().
     */
    public function test_lti_new_access_token() {
        global $DB;

        $this->resetAfterTest();

        $this->setAdminUser();

        // Create a tool type, associated with that proxy.
        $type = new \stdClass();
        $type->state = LTI_TOOL_STATE_CONFIGURED;
        $type->name = "Test tool";
        $type->description = "Example description";
        $type->clientid = "Test client ID";
        $type->baseurl = $this->getExternalTestFileUrl('/test.html');

        $config = new \stdClass();

        $typeid = lti_add_type($type, $config);

        $scopes = ['lti_some_scope', 'lti_another_scope'];

        lti_new_access_token($typeid, $scopes);

        $token = $DB->get_records('lti_access_tokens');
        $this->assertEquals(1, count($token));

        $token = reset($token);

        $this->assertEquals($typeid, $token->typeid);
        $this->assertEquals(json_encode(array_values($scopes)), $token->scope);
        $this->assertEquals($token->timecreated + LTI_ACCESS_TOKEN_LIFE, $token->validuntil);
        $this->assertNull($token->lastaccess);
    }

    /**
     * Test lti_build_login_request().
     */
    public function test_lti_build_login_request() {
        global $USER, $CFG;

        $this->resetAfterTest();

        $USER->id = 123456789;

        $course   = $this->getDataGenerator()->create_course();
        $instance = $this->getDataGenerator()->create_module('lti',
            [
                'course' => $course->id,
            ]
        );

        $config = new \stdClass();
        $config->lti_clientid = 'some-client-id';
        $config->typeid = 'some-type-id';
        $config->lti_toolurl = 'some-lti-tool-url';

        $request = lti_build_login_request($course->id, $instance->cmid, $instance, $config, 'basic-lti-launch-request');
        $this->assertEquals($CFG->wwwroot, $request['iss']);
        $this->assertEquals('http://some-lti-tool-url', $request['target_link_uri']);
        $this->assertEquals(123456789, $request['login_hint']);
        $this->assertTrue(strpos($request['lti_message_hint'], "\"cmid\":{$instance->cmid}") > 0);
        $this->assertTrue(strpos($request['lti_message_hint'],  "\"launchid\":\"ltilaunch{$instance->id}_") > 0);
        $this->assertEquals('some-client-id', $request['client_id']);
        $this->assertEquals('some-type-id', $request['lti_deployment_id']);
    }

    /**
     * @covers ::lti_get_launch_data()
     *
     * Test for_user is passed as parameter when specified.
     */
    public function test_lti_get_launch_data_with_for_user() {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();
        $config = new \stdClass();
        $config->lti_organizationid = '';
        $course = $this->getDataGenerator()->create_course();
        $type = $this->create_type($config);
        $link = $this->create_instance($type, $course);
        $launchdata = lti_get_launch_data($link, '', '', 345);
        $this->assertEquals($launchdata[1]['lti_message_type'], 'basic-lti-launch-request');
        $this->assertEquals($launchdata[1]['for_user_id'], 345);
    }

    /**
     * Test default orgid is host if not specified in config (tool installed in earlier version of Moodle).
     */
    public function test_lti_get_launch_data_default_organizationid_unset_usehost() {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();
        $config = new \stdClass();
        $config->lti_organizationid = '';
        $course = $this->getDataGenerator()->create_course();
        $type = $this->create_type($config);
        $link = $this->create_instance($type, $course);
        $launchdata = lti_get_launch_data($link);
        $this->assertEquals($launchdata[1]['tool_consumer_instance_guid'], 'www.example.com');
    }

    /**
     * Test default org id is set to host when config is usehost.
     */
    public function test_lti_get_launch_data_default_organizationid_set_usehost() {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();
        $config = new \stdClass();
        $config->lti_organizationid = '';
        $config->lti_organizationid_default = LTI_DEFAULT_ORGID_SITEHOST;
        $course = $this->getDataGenerator()->create_course();
        $type = $this->create_type($config);
        $link = $this->create_instance($type, $course);
        $launchdata = lti_get_launch_data($link);
        $this->assertEquals($launchdata[1]['tool_consumer_instance_guid'], 'www.example.com');
    }

    /**
     * Test default org id is set to site id when config is usesiteid.
     */
    public function test_lti_get_launch_data_default_organizationid_set_usesiteid() {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();
        $config = new \stdClass();
        $config->lti_organizationid = '';
        $config->lti_organizationid_default = LTI_DEFAULT_ORGID_SITEID;
        $course = $this->getDataGenerator()->create_course();
        $type = $this->create_type($config);
        $link = $this->create_instance($type, $course);
        $launchdata = lti_get_launch_data($link);
        $this->assertEquals($launchdata[1]['tool_consumer_instance_guid'], md5(get_site_identifier()));
    }

    /**
     * Test orgid can be overridden in which case default is ignored.
     */
    public function test_lti_get_launch_data_default_organizationid_orgid_override() {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();
        $config = new \stdClass();
        $config->lti_organizationid = 'overridden!';
        $config->lti_organizationid_default = LTI_DEFAULT_ORGID_SITEID;
        $course = $this->getDataGenerator()->create_course();
        $type = $this->create_type($config);
        $link = $this->create_instance($type, $course);
        $launchdata = lti_get_launch_data($link);
        $this->assertEquals($launchdata[1]['tool_consumer_instance_guid'], 'overridden!');
    }

    public function test_get_course_history() {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();
        $parentparentcourse = $this->getDataGenerator()->create_course();
        $parentcourse = $this->getDataGenerator()->create_course();
        $parentcourse->originalcourseid = $parentparentcourse->id;
        $DB->update_record('course', $parentcourse);
        $course = $this->getDataGenerator()->create_course();
        $course->originalcourseid = $parentcourse->id;
        $DB->update_record('course', $course);
        $this->assertEquals(get_course_history($parentparentcourse), []);
        $this->assertEquals(get_course_history($parentcourse), [$parentparentcourse->id]);
        $this->assertEquals(get_course_history($course), [$parentcourse->id, $parentparentcourse->id]);
        $course->originalcourseid = 38903;
        $DB->update_record('course', $course);
        $this->assertEquals(get_course_history($course), [38903]);
    }

    /**
     * Test the lti_get_ims_role helper function.
     *
     * @dataProvider lti_get_ims_role_provider
     * @covers ::lti_get_ims_role()
     *
     * @param bool $islti2 whether the method is called with LTI 2.0 role names or not.
     * @param string $rolename the name of the role (student, teacher, admin)
     * @param null|string $switchedto the role to switch to, or false if not using the 'switch to' functionality.
     * @param string $expected the expected role name.
     */
    public function test_lti_get_ims_role(bool $islti2, string $rolename, ?string $switchedto, string $expected) {
        global $DB;
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $user = $rolename == 'admin' ? get_admin() : $this->getDataGenerator()->create_and_enrol($course, $rolename);

        if ($switchedto) {
            $this->setUser($user);
            $role = $DB->get_record('role', array('shortname' => $switchedto));
            role_switch($role->id, \context_course::instance($course->id));
        }

        $this->assertEquals($expected, lti_get_ims_role($user, 0, $course->id, $islti2));
    }

    /**
     * Data provider for testing lti_get_ims_role.
     *
     * @return array[] the test case data.
     */
    public function lti_get_ims_role_provider() {
        return [
            'Student, LTI 1.1, no role switch' => [
                'islti2' => false,
                'rolename' => 'student',
                'switchedto' => null,
                'expected' => 'Learner'
            ],
            'Student, LTI 2.0, no role switch' => [
                'islti2' => true,
                'rolename' => 'student',
                'switchedto' => null,
                'expected' => 'Learner'
            ],
            'Teacher, LTI 1.1, no role switch' => [
                'islti2' => false,
                'rolename' => 'editingteacher',
                'switchedto' => null,
                'expected' => 'Instructor'
            ],
            'Teacher, LTI 2.0, no role switch' => [
                'islti2' => true,
                'rolename' => 'editingteacher',
                'switchedto' => null,
                'expected' => 'Instructor'
            ],
            'Admin, LTI 1.1, no role switch' => [
                'islti2' => false,
                'rolename' => 'admin',
                'switchedto' => null,
                'expected' => 'Instructor,urn:lti:sysrole:ims/lis/Administrator,urn:lti:instrole:ims/lis/Administrator'
            ],
            'Admin, LTI 2.0, no role switch' => [
                'islti2' => true,
                'rolename' => 'admin',
                'switchedto' => null,
                'expected' => 'Instructor,http://purl.imsglobal.org/vocab/lis/v2/person#Administrator'
            ],
            'Admin, LTI 1.1, role switch student' => [
                'islti2' => false,
                'rolename' => 'admin',
                'switchedto' => 'student',
                'expected' => 'Learner'
            ],
            'Admin, LTI 2.0, role switch student' => [
                'islti2' => true,
                'rolename' => 'admin',
                'switchedto' => 'student',
                'expected' => 'Learner'
            ],
            'Admin, LTI 1.1, role switch teacher' => [
                'islti2' => false,
                'rolename' => 'admin',
                'switchedto' => 'editingteacher',
                'expected' => 'Instructor'
            ],
            'Admin, LTI 2.0, role switch teacher' => [
                'islti2' => true,
                'rolename' => 'admin',
                'switchedto' => 'editingteacher',
                'expected' => 'Instructor'
            ],
        ];
    }

    /**
     * Test lti_get_lti_types_and_proxies with no limit or offset.
     */
    public function test_lti_get_lti_types_and_proxies_with_no_limit() {
        $this->resetAfterTest();
        $this->setAdminUser();
        $this->generate_tool_types_and_proxies(10);
        list($proxies, $types) = lti_get_lti_types_and_proxies();

        $this->assertCount(10, $proxies);
        $this->assertCount(10, $types);
    }

    /**
     * Test lti_get_lti_types_and_proxies with limits.
     */
    public function test_lti_get_lti_types_and_proxies_with_limit() {
        $this->resetAfterTest();
        $this->setAdminUser();
        $this->generate_tool_types_and_proxies(10);

        // Get the middle 10 data sets (of 20 total).
        list($proxies, $types) = lti_get_lti_types_and_proxies(10, 5);

        $this->assertCount(5, $proxies);
        $this->assertCount(5, $types);

        // Get the last 5 data sets with large limit (of 20 total).
        list($proxies, $types) = lti_get_lti_types_and_proxies(50, 15);

        $this->assertCount(0, $proxies);
        $this->assertCount(5, $types);

        // Get the last 13 data sets with large limit (of 20 total).
        list($proxies, $types) = lti_get_lti_types_and_proxies(50, 7);

        $this->assertCount(3, $proxies);
        $this->assertCount(10, $types);
    }

    /**
     * Test lti_get_lti_types_and_proxies with limits and only fetching orphaned proxies.
     */
    public function test_lti_get_lti_types_and_proxies_with_limit_and_orphaned_proxies() {
        $this->resetAfterTest();
        $this->setAdminUser();
        $this->generate_tool_types_and_proxies(10, 5);

        // Get the first 10 data sets (of 15 total).
        list($proxies, $types) = lti_get_lti_types_and_proxies(10, 0, true);

        $this->assertCount(5, $proxies);
        $this->assertCount(5, $types);

        // Get the middle 10 data sets with large limit (of 15 total).
        list($proxies, $types) = lti_get_lti_types_and_proxies(10, 2, true);

        $this->assertCount(3, $proxies);
        $this->assertCount(7, $types);

        // Get the last 5 data sets with large limit (of 15 total).
        list($proxies, $types) = lti_get_lti_types_and_proxies(50, 10, true);

        $this->assertCount(0, $proxies);
        $this->assertCount(5, $types);
    }

    /**
     * Test lti_get_lti_types_and_proxies_count.
     */
    public function test_lti_get_lti_types_and_proxies_count_with_no_filters() {
        $this->resetAfterTest();
        $this->setAdminUser();
        $this->generate_tool_types_and_proxies(10, 5);

        $totalcount = lti_get_lti_types_and_proxies_count();
        $this->assertEquals(25, $totalcount); // 10 types, 15 proxies.
    }

    /**
     * Test lti_get_lti_types_and_proxies_count only counting orphaned proxies.
     */
    public function test_lti_get_lti_types_and_proxies_count_with_only_orphaned_proxies() {
        $this->resetAfterTest();
        $this->setAdminUser();
        $this->generate_tool_types_and_proxies(10, 5);

        $orphanedcount = lti_get_lti_types_and_proxies_count(true);
        $this->assertEquals(15, $orphanedcount); // 10 types, 5 proxies.
    }

    /**
     * Test lti_get_lti_types_and_proxies_count only matching tool type with toolproxyid.
     */
    public function test_lti_get_lti_types_and_proxies_count_type_with_proxyid() {
        $this->resetAfterTest();
        $this->setAdminUser();
        ['proxies' => $proxies, 'types' => $types] = $this->generate_tool_types_and_proxies(10, 5);

        $countwithproxyid = lti_get_lti_types_and_proxies_count(false, $proxies[0]->id);
        $this->assertEquals(16, $countwithproxyid); // 1 type, 15 proxies.
    }

    /**
     * Verify that empty curl responses lead to the proper moodle_exception, not to XML ValueError.
     *
     * @covers ::lti_load_cartridge()
     */
    public function test_empty_reponse_lti_load_cartridge() {
        // Mock the curl response to empty string, this is hardly
        // reproducible in real life (only Windows + GHA).
        \curl::mock_response('');

        $this->expectException(\moodle_exception::class);
        lti_load_cartridge('http://example.com/mocked/empty/response', []);
    }

    /**
     * Create an LTI Tool.
     *
     * @param object $config tool config.
     *
     * @return object tool.
     */
    private function create_type(object $config) {
        $type = new \stdClass();
        $type->state = LTI_TOOL_STATE_CONFIGURED;
        $type->name = "Test tool";
        $type->description = "Example description";
        $type->clientid = "Test client ID";
        $type->baseurl = $this->getExternalTestFileUrl('/test.html');

        $configbase = new \stdClass();
        $configbase->lti_acceptgrades = LTI_SETTING_NEVER;
        $configbase->lti_sendname = LTI_SETTING_NEVER;
        $configbase->lti_sendemailaddr = LTI_SETTING_NEVER;
        $mergedconfig = (object) array_merge( (array) $configbase, (array) $config);
        $typeid = lti_add_type($type, $mergedconfig);
        return lti_get_type($typeid);
    }

    /**
     * Create an LTI Instance for the tool in a given course.
     *
     * @param object $type tool for which an instance should be added.
     * @param object $course course where the instance should be added.
     *
     * @return object instance.
     */
    private function create_instance(object $type, object $course) {
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_lti');
        return $generator->create_instance(array('course' => $course->id,
                  'toolurl' => $type->baseurl,
                  'typeid' => $type->id
                  ), array());
    }

    /**
     * Generate a number of LTI tool types and proxies.
     *
     * @param int $toolandproxycount How many tool types and associated proxies to create. E.g. Value of 10 will create 10 types
     * and 10 proxies.
     * @param int $orphanproxycount How many orphaned proxies to create.
     * @return array[]
     */
    private function generate_tool_types_and_proxies(int $toolandproxycount = 0, int $orphanproxycount = 0) {
        $proxies = [];
        $types = [];
        for ($i = 0; $i < $toolandproxycount; $i++) {
            $proxies[$i] = $this->generate_tool_proxy($i);
            $types[$i] = $this->generate_tool_type($i, $proxies[$i]->id);

        }
        for ($i = $toolandproxycount; $i < ($toolandproxycount + $orphanproxycount); $i++) {
            $proxies[$i] = $this->generate_tool_proxy($i);
        }

        return ['proxies' => $proxies, 'types' => $types];
    }
}

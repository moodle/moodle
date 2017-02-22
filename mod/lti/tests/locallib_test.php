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

defined('MOODLE_INTERNAL') || die;

global $CFG;
require_once($CFG->dirroot . '/mod/lti/locallib.php');
require_once($CFG->dirroot . '/mod/lti/servicelib.php');

/**
 * Local library tests
 *
 * @package    mod_lti
 * @copyright  Copyright (c) 2012 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_lti_locallib_testcase extends advanced_testcase {

    public function test_split_custom_parameters() {
        $this->resetAfterTest();

        $tool = new stdClass();
        $tool->enabledcapability = '';
        $tool->parameter = '';
        $this->assertEquals(lti_split_custom_parameters(null, $tool, array(), "x=1\ny=2", false),
            array('custom_x' => '1', 'custom_y' => '2'));

        // Removed repeat of previous test with a semicolon separator.

        $this->assertEquals(lti_split_custom_parameters(null, $tool, array(), 'Review:Chapter=1.2.56', false),
            array('custom_review_chapter' => '1.2.56'));

        $this->assertEquals(lti_split_custom_parameters(null, $tool, array(),
            'Complex!@#$^*(){}[]KEY=Complex!@#$^*;(){}[]½Value', false),
            array('custom_complex____________key' => 'Complex!@#$^*;(){}[]½Value'));

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
        $type = new stdClass();
        $config = new stdClass();

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
        $type = new stdClass();
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
        $lti = new stdClass();
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
        $type = new stdClass();
        $data = new stdClass();
        $data->lti_contentitem = true;
        $type->state = LTI_TOOL_STATE_CONFIGURED;
        $type->name = "Test tool";
        $type->description = "Example description";
        $type->toolproxyid = $proxy->id;
        $type->baseurl = $this->getExternalTestFileUrl('/test.html');

        $typeid = lti_add_type($type, $data);

        $typeconfig = lti_get_type_config($typeid);

        $course = $this->getDataGenerator()->create_course();
        $returnurl = new moodle_url('/');

        // Default parameters.
        $result = lti_build_content_item_selection_request($typeid, $course, $returnurl);
        $this->assertNotEmpty($result);
        $this->assertNotEmpty($result->params);
        $this->assertNotEmpty($result->url);
        $params = $result->params;
        $url = $result->url;
        $this->assertEquals($typeconfig['toolurl'], $url);
        $this->assertEquals('ContentItemSelectionRequest', $params['lti_message_type']);
        $this->assertEquals(LTI_VERSION_2, $params['lti_version']);
        $this->assertEquals('application/vnd.ims.lti.v1.ltilink', $params['accept_media_types']);
        $this->assertEquals('frame,iframe,window', $params['accept_presentation_document_targets']);
        $this->assertEquals($returnurl->out(false), $params['content_item_return_url']);
        $this->assertEquals('false', $params['accept_unsigned']);
        $this->assertEquals('false', $params['accept_multiple']);
        $this->assertEquals('false', $params['accept_copy_advice']);
        $this->assertEquals('false', $params['auto_create']);
        $this->assertEquals($type->name, $params['title']);
        $this->assertFalse(isset($params['resource_link_id']));
        $this->assertFalse(isset($params['resource_link_title']));
        $this->assertFalse(isset($params['resource_link_description']));
        $this->assertFalse(isset($params['launch_presentation_return_url']));
        $this->assertFalse(isset($params['lis_result_sourcedid']));

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
        $returnurl = new moodle_url('/');

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
        $type = new stdClass();
        $data = new stdClass();
        $data->lti_contentitem = true;
        $type->state = LTI_TOOL_STATE_CONFIGURED;
        $type->name = "Test tool";
        $type->description = "Example description";
        $type->baseurl = $this->getExternalTestFileUrl('/test.html');

        $typeid = lti_add_type($type, $data);
        $course = $this->getDataGenerator()->create_course();
        $returnurl = new moodle_url('/');

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
        $type = new stdClass();
        $data = new stdClass();
        $data->lti_contentitem = true;
        $type->state = LTI_TOOL_STATE_CONFIGURED;
        $type->name = "Test tool";
        $type->description = "Example description";
        $type->baseurl = $this->getExternalTestFileUrl('/test.html');

        $typeid = lti_add_type($type, $data);
        $course = $this->getDataGenerator()->create_course();
        $returnurl = new moodle_url('/');

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
}

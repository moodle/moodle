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

namespace mod_lti\local\ltiopenid;

/**
 * OpenId LTI Registration library tests
 *
 * @package    mod_lti
 * @copyright  2020 Claude Vervoort, Cengage
 * @author     Claude Vervoort
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class registration_test extends \advanced_testcase {

    /**
     * @var string A has-it-all client registration.
     */
    private $registrationfulljson = <<<EOD
    {
        "application_type": "web",
        "response_types": ["id_token"],
        "grant_types": ["implict", "client_credentials"],
        "initiate_login_uri": "https://client.example.org/lti/init",
        "redirect_uris":
        ["https://client.example.org/callback",
        "https://client.example.org/callback2"],
        "client_name": "Virtual Garden",
        "client_name#ja": "バーチャルガーデン",
        "jwks_uri": "https://client.example.org/.well-known/jwks.json",
        "logo_uri": "https://client.example.org/logo.png",
        "policy_uri": "https://client.example.org/privacy",
        "policy_uri#ja": "https://client.example.org/privacy?lang=ja",
        "tos_uri": "https://client.example.org/tos",
        "tos_uri#ja": "https://client.example.org/tos?lang=ja",
        "token_endpoint_auth_method": "private_key_jwt",
        "contacts": ["ve7jtb@example.org", "mary@example.org"],
        "scope": "https://purl.imsglobal.org/spec/lti-ags/scope/score https://purl.imsglobal.org/spec/lti-ags/scope/lineitem",
        "https://purl.imsglobal.org/spec/lti-tool-configuration": {
            "domain": "client.example.org",
            "description": "Learn Botany by tending to your little (virtual) garden.",
            "description#ja": "小さな（仮想）庭に行くことで植物学を学びましょう。",
            "target_link_uri": "https://client.example.org/lti",
            "custom_parameters": {
                "context_history": "\$Context.id.history"
            },
            "claims": ["iss", "sub", "name", "given_name", "family_name", "email"],
            "messages": [
                {
                    "type": "LtiDeepLinkingRequest",
                    "target_link_uri": "https://client.example.org/lti/dl",
                    "label": "Add a virtual garden",
                    "label#ja": "バーチャルガーデンを追加する"
                }
            ]
        }
    }
EOD;

    /**
     * @var string A minimalist client registration.
     */
    private $registrationminimaljson = <<<EOD
    {
        "application_type": "web",
        "response_types": ["id_token"],
        "grant_types": ["implict", "client_credentials"],
        "initiate_login_uri": "https://client.example.org/lti/init",
        "redirect_uris":
        ["https://client.example.org/callback"],
        "client_name": "Virtual Garden",
        "jwks_uri": "https://client.example.org/.well-known/jwks.json",
        "token_endpoint_auth_method": "private_key_jwt",
        "https://purl.imsglobal.org/spec/lti-tool-configuration": {
            "domain": "www.example.org",
            "target_link_uri": "https://www.example.org/lti"
        }
    }
EOD;

    /**
     * @var string A minimalist with deep linking client registration.
     */
    private $registrationminimaldljson = <<<EOD
    {
        "application_type": "web",
        "response_types": ["id_token"],
        "grant_types": ["implict", "client_credentials"],
        "initiate_login_uri": "https://client.example.org/lti/init",
        "redirect_uris":
        ["https://client.example.org/callback"],
        "client_name": "Virtual Garden",
        "jwks_uri": "https://client.example.org/.well-known/jwks.json",
        "token_endpoint_auth_method": "private_key_jwt",
        "https://purl.imsglobal.org/spec/lti-tool-configuration": {
            "domain": "client.example.org",
            "target_link_uri": "https://client.example.org/lti",
            "messages": [
                {
                    "type": "LtiDeepLinkingRequest"
                }
            ]
        }
    }
EOD;

    /**
     * Test the mapping from Registration JSON to LTI Config for a has-it-all tool registration.
     */
    public function test_to_config_full(): void {
        $registration = json_decode($this->registrationfulljson, true);
        $registration['scope'] .= ' https://purl.imsglobal.org/spec/lti-nrps/scope/contextmembership.readonly';
        $config = registration_helper::get()->registration_to_config($registration, 'TheClientId');
        $this->assertEquals('JWK_KEYSET', $config->lti_keytype);
        $this->assertEquals(LTI_VERSION_1P3, $config->lti_ltiversion);
        $this->assertEquals('TheClientId', $config->lti_clientid);
        $this->assertEquals('Virtual Garden', $config->lti_typename);
        $this->assertEquals('Learn Botany by tending to your little (virtual) garden.', $config->lti_description);
        $this->assertEquals('https://client.example.org/lti/init', $config->lti_initiatelogin);
        $this->assertEquals(implode(PHP_EOL, ["https://client.example.org/callback",
            "https://client.example.org/callback2"]), $config->lti_redirectionuris);
        $this->assertEquals("context_history=\$Context.id.history", $config->lti_customparameters);
        $this->assertEquals("https://client.example.org/.well-known/jwks.json", $config->lti_publickeyset);
        $this->assertEquals("https://client.example.org/logo.png", $config->lti_icon);
        $this->assertEquals(2, $config->ltiservice_gradesynchronization);
        $this->assertEquals(LTI_SETTING_DELEGATE, $config->lti_acceptgrades);
        $this->assertEquals(1, $config->ltiservice_memberships);
        $this->assertEquals(0, $config->ltiservice_toolsettings);
        $this->assertEquals('client.example.org', $config->lti_tooldomain);
        $this->assertEquals('https://client.example.org/lti', $config->lti_toolurl);
        $this->assertEquals(LTI_SETTING_ALWAYS, $config->lti_sendname);
        $this->assertEquals(LTI_SETTING_ALWAYS, $config->lti_sendemailaddr);
        $this->assertEquals(1, $config->lti_contentitem);
        $this->assertEquals('https://client.example.org/lti/dl', $config->lti_toolurl_ContentItemSelectionRequest);
    }

    /**
     * Test the mapping from Registration JSON to LTI Config for a minimal tool registration.
     */
    public function test_to_config_minimal(): void {
        $registration = json_decode($this->registrationminimaljson, true);
        $config = registration_helper::get()->registration_to_config($registration, 'TheClientId');
        $this->assertEquals('JWK_KEYSET', $config->lti_keytype);
        $this->assertEquals(LTI_VERSION_1P3, $config->lti_ltiversion);
        $this->assertEquals('TheClientId', $config->lti_clientid);
        $this->assertEquals('Virtual Garden', $config->lti_typename);
        $this->assertEmpty($config->lti_description);
        // Special case here where Moodle ignores www for domains.
        $this->assertEquals('example.org', $config->lti_tooldomain);
        $this->assertEquals('https://www.example.org/lti', $config->lti_toolurl);
        $this->assertEquals('https://client.example.org/lti/init', $config->lti_initiatelogin);
        $this->assertEquals('https://client.example.org/callback', $config->lti_redirectionuris);
        $this->assertEmpty($config->lti_customparameters);
        $this->assertEquals("https://client.example.org/.well-known/jwks.json", $config->lti_publickeyset);
        $this->assertEmpty($config->lti_icon);
        $this->assertEquals(0, $config->ltiservice_gradesynchronization);
        $this->assertEquals(LTI_SETTING_NEVER, $config->lti_acceptgrades);
        $this->assertEquals(0, $config->ltiservice_memberships);
        $this->assertEquals(LTI_SETTING_NEVER, $config->lti_sendname);
        $this->assertEquals(LTI_SETTING_NEVER, $config->lti_sendemailaddr);
        $this->assertEquals(0, $config->lti_contentitem);
    }

    /**
     * Test the mapping from Registration JSON to LTI Config for a minimal tool with
     * deep linking support registration.
     */
    public function test_to_config_minimal_with_deeplinking(): void {
        $registration = json_decode($this->registrationminimaldljson, true);
        $config = registration_helper::get()->registration_to_config($registration, 'TheClientId');
        $this->assertEquals(1, $config->lti_contentitem);
        $this->assertEmpty($config->lti_toolurl_ContentItemSelectionRequest);
    }

    /**
     * Validation Test: initiation login.
     */
    public function test_validation_initlogin(): void {
        $registration = json_decode($this->registrationfulljson, true);
        $this->expectException(registration_exception::class);
        $this->expectExceptionCode(400);
        unset($registration['initiate_login_uri']);
        registration_helper::get()->registration_to_config($registration, 'TheClientId');
    }

    /**
     * Validation Test: redirect uris.
     */
    public function test_validation_redirecturis(): void {
        $registration = json_decode($this->registrationfulljson, true);
        $this->expectException(registration_exception::class);
        $this->expectExceptionCode(400);
        unset($registration['redirect_uris']);
        registration_helper::get()->registration_to_config($registration, 'TheClientId');
    }

    /**
     * Validation Test: jwks uri empty.
     */
    public function test_validation_jwks(): void {
        $registration = json_decode($this->registrationfulljson, true);
        $this->expectException(registration_exception::class);
        $this->expectExceptionCode(400);
        $registration['jwks_uri'] = '';
        registration_helper::get()->registration_to_config($registration, 'TheClientId');
    }

    /**
     * Validation Test: no domain nor targetlinkuri is rejected.
     */
    public function test_validation_missing_domain_targetlinkuri(): void {
        $registration = json_decode($this->registrationminimaljson, true);
        $this->expectException(registration_exception::class);
        $this->expectExceptionCode(400);
        unset($registration['https://purl.imsglobal.org/spec/lti-tool-configuration']['domain']);
        unset($registration['https://purl.imsglobal.org/spec/lti-tool-configuration']['target_link_uri']);
        registration_helper::get()->registration_to_config($registration, 'TheClientId');
    }

    /**
     * Validation Test: mismatch between domain and targetlinkuri is rejected.
     */
    public function test_validation_domain_targetlinkuri_match(): void {
        $registration = json_decode($this->registrationminimaljson, true);
        $this->expectException(registration_exception::class);
        $this->expectExceptionCode(400);
        $registration['https://purl.imsglobal.org/spec/lti-tool-configuration']['domain'] = 'not.the.right.domain';
        registration_helper::get()->registration_to_config($registration, 'TheClientId');
    }

    /**
     * Validation Test: domain is required.
     */
    public function test_validation_domain_targetlinkuri_onlylink(): void {
        $registration = json_decode($this->registrationminimaljson, true);
        unset($registration['https://purl.imsglobal.org/spec/lti-tool-configuration']['domain']);
        $this->expectException(registration_exception::class);
        $this->expectExceptionCode(400);
        $config = registration_helper::get()->registration_to_config($registration, 'TheClientId');
    }

    /**
     * Validation Test: base url (targetlinkuri) is built from domain if not present.
     */
    public function test_validation_domain_targetlinkuri_onlydomain(): void {
        $registration = json_decode($this->registrationminimaljson, true);
        unset($registration['https://purl.imsglobal.org/spec/lti-tool-configuration']['target_link_uri']);
        $config = registration_helper::get()->registration_to_config($registration, 'TheClientId');
        $this->assertEquals('example.org', $config->lti_tooldomain);
        $this->assertEquals('https://www.example.org', $config->lti_toolurl);
    }

    /**
     * Test the transformation from lti config to OpenId LTI Client Registration response.
     */
    public function test_config_to_registration(): void {
        $orig = json_decode($this->registrationfulljson, true);
        $orig['scope'] .= ' https://purl.imsglobal.org/spec/lti-nrps/scope/contextmembership.readonly';
        $reghelper = registration_helper::get();
        $reg = $reghelper->config_to_registration($reghelper->registration_to_config($orig, 'clid'), 12);
        $this->assertEquals('clid', $reg['client_id']);
        $this->assertEquals($orig['response_types'], $reg['response_types']);
        $this->assertEquals($orig['initiate_login_uri'], $reg['initiate_login_uri']);
        $this->assertEquals($orig['redirect_uris'], $reg['redirect_uris']);
        $this->assertEquals($orig['jwks_uri'], $reg['jwks_uri']);
        $this->assertEquals($orig['logo_uri'], $reg['logo_uri']);
        $this->assertEquals('https://purl.imsglobal.org/spec/lti-ags/scope/score '.
            'https://purl.imsglobal.org/spec/lti-ags/scope/result.readonly '.
            'https://purl.imsglobal.org/spec/lti-ags/scope/lineitem.readonly '.
            'https://purl.imsglobal.org/spec/lti-ags/scope/lineitem '.
            'https://purl.imsglobal.org/spec/lti-nrps/scope/contextmembership.readonly', $reg['scope']);
        $ltiorig = $orig['https://purl.imsglobal.org/spec/lti-tool-configuration'];
        $lti = $reg['https://purl.imsglobal.org/spec/lti-tool-configuration'];
        $this->assertEquals("12", $lti['deployment_id']);
        $this->assertEquals($ltiorig['target_link_uri'], $lti['target_link_uri']);
        $this->assertEquals($ltiorig['domain'], $lti['domain']);
        $this->assertEquals($ltiorig['custom_parameters'], $lti['custom_parameters']);
        $this->assertEquals($ltiorig['description'], $lti['description']);
        $dlmsgorig = $ltiorig['messages'][0];
        $dlmsg = $lti['messages'][0];
        $this->assertEquals($dlmsgorig['type'], $dlmsg['type']);
        $this->assertEquals($dlmsgorig['target_link_uri'], $dlmsg['target_link_uri']);
        $this->assertTrue(in_array('iss', $lti['claims']));
        $this->assertTrue(in_array('sub', $lti['claims']));
        $this->assertTrue(in_array('email', $lti['claims']));
        $this->assertTrue(in_array('family_name', $lti['claims']));
        $this->assertTrue(in_array('given_name', $lti['claims']));
        $this->assertTrue(in_array('name', $lti['claims']));
    }

    /**
     * Test the transformation from lti config to OpenId LTI Client Registration response for the minimal version.
     */
    public function test_config_to_registration_minimal(): void {
        $orig = json_decode($this->registrationminimaljson, true);
        $reghelper = registration_helper::get();
        $reg = $reghelper->config_to_registration($reghelper->registration_to_config($orig, 'clid'), 12);
        $this->assertEquals('clid', $reg['client_id']);
        $this->assertEquals($orig['response_types'], $reg['response_types']);
        $this->assertEquals($orig['initiate_login_uri'], $reg['initiate_login_uri']);
        $this->assertEquals($orig['redirect_uris'], $reg['redirect_uris']);
        $this->assertEquals($orig['jwks_uri'], $reg['jwks_uri']);
        $this->assertEquals('', $reg['scope']);
        $ltiorig = $orig['https://purl.imsglobal.org/spec/lti-tool-configuration'];
        $lti = $reg['https://purl.imsglobal.org/spec/lti-tool-configuration'];
        $this->assertTrue(in_array('iss', $lti['claims']));
        $this->assertTrue(in_array('sub', $lti['claims']));
        $this->assertFalse(in_array('email', $lti['claims']));
        $this->assertFalse(in_array('family_name', $lti['claims']));
        $this->assertFalse(in_array('given_name', $lti['claims']));
        $this->assertFalse(in_array('name', $lti['claims']));
    }

    /**
     * Test the transformation from lti config 1.1 to Registration Response.
     */
    public function test_config_to_registration_lti11(): void {
        $config = [];
        $config['contentitem'] = 1;
        $config['toolurl_ContentItemSelectionRequest'] = '';
        $config['sendname'] = 0;
        $config['sendemailaddr'] = 1;
        $config['acceptgrades'] = 2;
        $config['resourcekey'] = 'testkey';
        $config['password'] = 'testp@ssw0rd';
        $config['customparameters'] = 'a1=b1';
        $type = [];
        $type['id'] = 130;
        $type['name'] = 'LTI Test 1.1';
        $type['baseurl'] = 'https://base.test.url/test';
        $type['tooldomain'] = 'base.test.url';
        $type['ltiversion'] = 'LTI-1p0';
        $type['icon'] = 'https://base.test.url/icon.png';

        $reg = registration_helper::get()->config_to_registration((object)$config, $type['id'], (object)$type);
        $this->assertFalse(isset($reg['client_id']));
        $this->assertFalse(isset($reg['initiate_login_uri']));
        $this->assertEquals($type['name'], $reg['client_name']);
        $lti = $reg['https://purl.imsglobal.org/spec/lti-tool-configuration'];
        $this->assertEquals(LTI_VERSION_1, $lti['version']);
        $this->assertEquals('b1', $lti['custom_parameters']['a1']);
        $this->assertEquals('LtiDeepLinkingRequest', $lti['messages'][0]['type']);
        $this->assertEquals('base.test.url', $lti['domain']);
        $this->assertEquals($type['baseurl'], $lti['target_link_uri']);
        $oauth = $lti['oauth_consumer'];
        $this->assertEquals('testkey', $oauth['key']);
        $this->assertFalse(empty($oauth['nonce']));
        $this->assertEquals(hash('sha256', 'testkeytestp@ssw0rd'.$oauth['nonce']), $oauth['sign']);
        $this->assertTrue(in_array('iss', $lti['claims']));
        $this->assertTrue(in_array('sub', $lti['claims']));
        $this->assertTrue(in_array('email', $lti['claims']));
        $this->assertFalse(in_array('family_name', $lti['claims']));
        $this->assertFalse(in_array('given_name', $lti['claims']));
        $this->assertFalse(in_array('name', $lti['claims']));
    }

    /**
     * Test the transformation from lti config 2.0 to Registration Response.
     * For LTI 2.0 we limit to just passing the previous key/secret.
     */
    public function test_config_to_registration_lti20(): void {
        $config = [];
        $config['contentitem'] = 1;
        $config['toolurl_ContentItemSelectionRequest'] = '';
        $type = [];
        $type['id'] = 131;
        $type['name'] = 'LTI Test 1.2';
        $type['baseurl'] = 'https://base.test.url/test';
        $type['tooldomain'] = 'base.test.url';
        $type['ltiversion'] = 'LTI-2p0';
        $type['icon'] = 'https://base.test.url/icon.png';
        $type['toolproxyid'] = 9;
        $toolproxy = [];
        $toolproxy['id'] = 9;
        $toolproxy['guid'] = 'lti2guidtest';
        $toolproxy['secret'] = 'peM7YDx420bo';

        $reghelper = $this->getMockBuilder(registration_helper::class)
            ->onlyMethods(['get_tool_proxy'])
            ->getMock();
        $map = [[$toolproxy['id'], $toolproxy]];
        $reghelper->method('get_tool_proxy')
            ->will($this->returnValueMap($map));
        $reg = $reghelper->config_to_registration((object)$config, $type['id'], (object)$type);
        $this->assertFalse(isset($reg['client_id']));
        $this->assertFalse(isset($reg['initiate_login_uri']));
        $this->assertEquals($type['name'], $reg['client_name']);
        $lti = $reg['https://purl.imsglobal.org/spec/lti-tool-configuration'];
        $this->assertEquals(LTI_VERSION_2, $lti['version']);
        $this->assertEquals('LtiDeepLinkingRequest', $lti['messages'][0]['type']);
        $this->assertEquals('base.test.url', $lti['domain']);
        $this->assertEquals($type['baseurl'], $lti['target_link_uri']);
        $oauth = $lti['oauth_consumer'];
        $this->assertEquals('lti2guidtest', $toolproxy['guid']);
        $this->assertFalse(empty($oauth['nonce']));
        $this->assertEquals(hash('sha256', 'lti2guidtestpeM7YDx420bo'.$oauth['nonce']), $oauth['sign']);
        $this->assertTrue(in_array('iss', $lti['claims']));
        $this->assertTrue(in_array('sub', $lti['claims']));
        $this->assertFalse(in_array('email', $lti['claims']));
        $this->assertFalse(in_array('family_name', $lti['claims']));
        $this->assertFalse(in_array('given_name', $lti['claims']));
        $this->assertFalse(in_array('name', $lti['claims']));
    }
}

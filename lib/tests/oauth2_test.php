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
 * Tests for oauth2 apis (\core\oauth2\*).
 *
 * @package    core
 * @copyright  2017 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Tests for oauth2 apis (\core\oauth2\*).
 *
 * @package    core
 * @copyright  2017 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
class core_oauth2_testcase extends advanced_testcase {

    /**
     * Tests the crud operations on oauth2 issuers.
     */
    public function test_create_and_delete_standard_issuers() {
        $this->resetAfterTest();
        $this->setAdminUser();
        \core\oauth2\api::create_standard_issuer('google');
        \core\oauth2\api::create_standard_issuer('facebook');
        \core\oauth2\api::create_standard_issuer('microsoft');
        \core\oauth2\api::create_standard_issuer('nextcloud', 'https://dummy.local/nextcloud/');

        $this->expectException(\moodle_exception::class);
        \core\oauth2\api::create_standard_issuer('nextcloud');

        $issuers = \core\oauth2\api::get_all_issuers();

        $this->assertEquals($issuers[0]->get('name'), 'Google');
        $this->assertEquals($issuers[1]->get('name'), 'Facebook');
        $this->assertEquals($issuers[2]->get('name'), 'Microsoft');
        $this->assertEquals($issuers[3]->get('name'), 'Nextcloud');

        \core\oauth2\api::move_down_issuer($issuers[0]->get('id'));

        $issuers = \core\oauth2\api::get_all_issuers();

        $this->assertEquals($issuers[0]->get('name'), 'Facebook');
        $this->assertEquals($issuers[1]->get('name'), 'Google');
        $this->assertEquals($issuers[2]->get('name'), 'Microsoft');
        $this->assertEquals($issuers[3]->get('name'), 'Nextcloud');

        \core\oauth2\api::delete_issuer($issuers[1]->get('id'));

        $issuers = \core\oauth2\api::get_all_issuers();

        $this->assertEquals($issuers[0]->get('name'), 'Facebook');
        $this->assertEquals($issuers[1]->get('name'), 'Microsoft');
        $this->assertEquals($issuers[2]->get('name'), 'Nextcloud');
    }

    /**
     * Tests we can list and delete each of the persistents related to an issuer.
     */
    public function test_getters() {
        $this->resetAfterTest();
        $this->setAdminUser();
        $issuer = \core\oauth2\api::create_standard_issuer('microsoft');

        $same = \core\oauth2\api::get_issuer($issuer->get('id'));

        foreach ($same->properties_definition() as $name => $def) {
            $this->assertTrue($issuer->get($name) == $same->get($name));
        }

        $endpoints = \core\oauth2\api::get_endpoints($issuer);
        $same = \core\oauth2\api::get_endpoint($endpoints[0]->get('id'));
        $this->assertEquals($endpoints[0]->get('id'), $same->get('id'));
        $this->assertEquals($endpoints[0]->get('name'), $same->get('name'));

        $todelete = $endpoints[0];
        \core\oauth2\api::delete_endpoint($todelete->get('id'));
        $endpoints = \core\oauth2\api::get_endpoints($issuer);
        $this->assertNotEquals($endpoints[0]->get('id'), $todelete->get('id'));

        $userfields = \core\oauth2\api::get_user_field_mappings($issuer);
        $same = \core\oauth2\api::get_user_field_mapping($userfields[0]->get('id'));
        $this->assertEquals($userfields[0]->get('id'), $same->get('id'));

        $todelete = $userfields[0];
        \core\oauth2\api::delete_user_field_mapping($todelete->get('id'));
        $userfields = \core\oauth2\api::get_user_field_mappings($issuer);
        $this->assertNotEquals($userfields[0]->get('id'), $todelete->get('id'));
    }

    /**
     * Tests we can get a logged in oauth client for a system account.
     */
    public function test_get_system_oauth_client() {
        $this->resetAfterTest();
        $this->setAdminUser();

        $issuer = \core\oauth2\api::create_standard_issuer('microsoft');

        $requiredscopes = \core\oauth2\api::get_system_scopes_for_issuer($issuer);
        // Fake a system account.
        $data = (object) [
            'issuerid' => $issuer->get('id'),
            'refreshtoken' => 'abc',
            'grantedscopes' => $requiredscopes,
            'email' => 'sys@example.com',
            'username' => 'sys'
        ];
        $sys = new \core\oauth2\system_account(0, $data);
        $sys->create();

        // Fake a response with an access token.
        $response = json_encode(
            (object) [
                'access_token' => 'fdas...',
                'token_type' => 'Bearer',
                'expires_in' => '3600',
                'id_token' => 'llfsd..',
            ]
        );
        curl::mock_response($response);
        $client = \core\oauth2\api::get_system_oauth_client($issuer);
        $this->assertTrue($client->is_logged_in());
    }

    /**
     * Tests we can enable and disable an issuer.
     */
    public function test_enable_disable_issuer() {
        $this->resetAfterTest();
        $this->setAdminUser();

        $issuer = \core\oauth2\api::create_standard_issuer('microsoft');

        $issuerid = $issuer->get('id');

        \core\oauth2\api::enable_issuer($issuerid);
        $check = \core\oauth2\api::get_issuer($issuer->get('id'));
        $this->assertTrue((boolean)$check->get('enabled'));

        \core\oauth2\api::enable_issuer($issuerid);
        $check = \core\oauth2\api::get_issuer($issuer->get('id'));
        $this->assertTrue((boolean)$check->get('enabled'));

        \core\oauth2\api::disable_issuer($issuerid);
        $check = \core\oauth2\api::get_issuer($issuer->get('id'));
        $this->assertFalse((boolean)$check->get('enabled'));

        \core\oauth2\api::enable_issuer($issuerid);
        $check = \core\oauth2\api::get_issuer($issuer->get('id'));
        $this->assertTrue((boolean)$check->get('enabled'));
    }

    /**
     * Test the alloweddomains for an issuer.
     */
    public function test_issuer_alloweddomains() {
        $this->resetAfterTest();
        $this->setAdminUser();

        $issuer = \core\oauth2\api::create_standard_issuer('microsoft');

        $issuer->set('alloweddomains', '');

        // Anything is allowed when domain is empty.
        $this->assertTrue($issuer->is_valid_login_domain(''));
        $this->assertTrue($issuer->is_valid_login_domain('a@b'));
        $this->assertTrue($issuer->is_valid_login_domain('longer.example@example.com'));

        $issuer->set('alloweddomains', 'example.com');

        // One domain - must match exactly - no substrings etc.
        $this->assertFalse($issuer->is_valid_login_domain(''));
        $this->assertFalse($issuer->is_valid_login_domain('a@b'));
        $this->assertFalse($issuer->is_valid_login_domain('longer.example@example'));
        $this->assertTrue($issuer->is_valid_login_domain('longer.example@example.com'));

        $issuer->set('alloweddomains', 'example.com,example.net');
        // Multiple domains - must match any exactly - no substrings etc.
        $this->assertFalse($issuer->is_valid_login_domain(''));
        $this->assertFalse($issuer->is_valid_login_domain('a@b'));
        $this->assertFalse($issuer->is_valid_login_domain('longer.example@example'));
        $this->assertFalse($issuer->is_valid_login_domain('invalid@email@example.net'));
        $this->assertTrue($issuer->is_valid_login_domain('longer.example@example.net'));
        $this->assertTrue($issuer->is_valid_login_domain('longer.example@example.com'));

        $issuer->set('alloweddomains', '*.example.com');
        // Wildcard.
        $this->assertFalse($issuer->is_valid_login_domain(''));
        $this->assertFalse($issuer->is_valid_login_domain('a@b'));
        $this->assertFalse($issuer->is_valid_login_domain('longer.example@example'));
        $this->assertFalse($issuer->is_valid_login_domain('longer.example@example.com'));
        $this->assertTrue($issuer->is_valid_login_domain('longer.example@sub.example.com'));
    }

}

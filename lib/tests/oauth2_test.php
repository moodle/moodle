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

namespace core;

use core\oauth2\access_token;
use core\oauth2\api;
use core\oauth2\endpoint;
use core\oauth2\issuer;
use core\oauth2\system_account;
use \core\oauth2\user_field_mapping;

/**
 * Tests for oauth2 apis (\core\oauth2\*).
 *
 * @package    core
 * @copyright  2017 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 * @coversDefaultClass \core\oauth2\api
 */
class oauth2_test extends \advanced_testcase {

    /**
     * Tests the crud operations on oauth2 issuers.
     */
    public function test_create_and_delete_standard_issuers() {
        $this->resetAfterTest();
        $this->setAdminUser();
        api::create_standard_issuer('google');
        api::create_standard_issuer('facebook');
        api::create_standard_issuer('microsoft');
        api::create_standard_issuer('nextcloud', 'https://dummy.local/nextcloud/');

        $issuers = api::get_all_issuers();

        $this->assertEquals($issuers[0]->get('name'), 'Google');
        $this->assertEquals($issuers[1]->get('name'), 'Facebook');
        $this->assertEquals($issuers[2]->get('name'), 'Microsoft');
        $this->assertEquals($issuers[3]->get('name'), 'Nextcloud');

        api::move_down_issuer($issuers[0]->get('id'));

        $issuers = api::get_all_issuers();

        $this->assertEquals($issuers[0]->get('name'), 'Facebook');
        $this->assertEquals($issuers[1]->get('name'), 'Google');
        $this->assertEquals($issuers[2]->get('name'), 'Microsoft');
        $this->assertEquals($issuers[3]->get('name'), 'Nextcloud');

        api::delete_issuer($issuers[1]->get('id'));

        $issuers = api::get_all_issuers();

        $this->assertEquals($issuers[0]->get('name'), 'Facebook');
        $this->assertEquals($issuers[1]->get('name'), 'Microsoft');
        $this->assertEquals($issuers[2]->get('name'), 'Nextcloud');
    }

    /**
     * Tests the crud operations on oauth2 issuers.
     */
    public function test_create_nextcloud_without_url() {
        $this->resetAfterTest();
        $this->setAdminUser();

        $this->expectException(\moodle_exception::class);
        api::create_standard_issuer('nextcloud');
    }

    /**
     * Tests we can list and delete each of the persistents related to an issuer.
     */
    public function test_getters() {
        $this->resetAfterTest();
        $this->setAdminUser();
        $issuer = api::create_standard_issuer('microsoft');

        $same = api::get_issuer($issuer->get('id'));

        foreach ($same->properties_definition() as $name => $def) {
            $this->assertTrue($issuer->get($name) == $same->get($name));
        }

        $endpoints = api::get_endpoints($issuer);
        $same = api::get_endpoint($endpoints[0]->get('id'));
        $this->assertEquals($endpoints[0]->get('id'), $same->get('id'));
        $this->assertEquals($endpoints[0]->get('name'), $same->get('name'));

        $todelete = $endpoints[0];
        api::delete_endpoint($todelete->get('id'));
        $endpoints = api::get_endpoints($issuer);
        $this->assertNotEquals($endpoints[0]->get('id'), $todelete->get('id'));

        $userfields = api::get_user_field_mappings($issuer);
        $same = api::get_user_field_mapping($userfields[0]->get('id'));
        $this->assertEquals($userfields[0]->get('id'), $same->get('id'));

        $todelete = $userfields[0];
        api::delete_user_field_mapping($todelete->get('id'));
        $userfields = api::get_user_field_mappings($issuer);
        $this->assertNotEquals($userfields[0]->get('id'), $todelete->get('id'));
    }

    /**
     * Data provider for \core_oauth2_testcase::test_get_system_oauth_client().
     *
     * @return array
     */
    public function system_oauth_client_provider() {
        return [
            [
                (object) [
                    'access_token' => 'fdas...',
                    'token_type' => 'Bearer',
                    'expires_in' => '3600',
                    'id_token' => 'llfsd..',
                ], HOURSECS - 10
            ],
            [
                (object) [
                    'access_token' => 'fdas...',
                    'token_type' => 'Bearer',
                    'id_token' => 'llfsd..',
                ], WEEKSECS
            ],
        ];
    }

    /**
     * Tests we can get a logged in oauth client for a system account.
     *
     * @dataProvider system_oauth_client_provider
     * @param \stdClass $responsedata The response data to be mocked.
     * @param int $expiresin The expected expiration time.
     */
    public function test_get_system_oauth_client($responsedata, $expiresin) {
        $this->resetAfterTest();
        $this->setAdminUser();

        $issuer = api::create_standard_issuer('microsoft');

        $requiredscopes = api::get_system_scopes_for_issuer($issuer);
        // Fake a system account.
        $data = (object) [
            'issuerid' => $issuer->get('id'),
            'refreshtoken' => 'abc',
            'grantedscopes' => $requiredscopes,
            'email' => 'sys@example.com',
            'username' => 'sys'
        ];
        $sys = new system_account(0, $data);
        $sys->create();

        // Fake a response with an access token.
        $response = json_encode($responsedata);
        \curl::mock_response($response);
        $client = api::get_system_oauth_client($issuer);
        $this->assertTrue($client->is_logged_in());

        // Check token expiry.
        $accesstoken = access_token::get_record(['issuerid' => $issuer->get('id')]);

        // Get the difference between the actual and expected expiry times.
        // They might differ by a couple of seconds depending on the timing when the token gets actually processed.
        $expiresdifference = time() + $expiresin - $accesstoken->get('expires');

        // Assert that the actual token expiration is more or less the same as the expected.
        $this->assertGreaterThanOrEqual(0, $expiresdifference);
        $this->assertLessThanOrEqual(3, $expiresdifference);
    }

    /**
     * Tests we can enable and disable an issuer.
     */
    public function test_enable_disable_issuer() {
        $this->resetAfterTest();
        $this->setAdminUser();

        $issuer = api::create_standard_issuer('microsoft');

        $issuerid = $issuer->get('id');

        api::enable_issuer($issuerid);
        $check = api::get_issuer($issuer->get('id'));
        $this->assertTrue((boolean)$check->get('enabled'));

        api::enable_issuer($issuerid);
        $check = api::get_issuer($issuer->get('id'));
        $this->assertTrue((boolean)$check->get('enabled'));

        api::disable_issuer($issuerid);
        $check = api::get_issuer($issuer->get('id'));
        $this->assertFalse((boolean)$check->get('enabled'));

        api::enable_issuer($issuerid);
        $check = api::get_issuer($issuer->get('id'));
        $this->assertTrue((boolean)$check->get('enabled'));
    }

    /**
     * Test the alloweddomains for an issuer.
     */
    public function test_issuer_alloweddomains() {
        $this->resetAfterTest();
        $this->setAdminUser();

        $issuer = api::create_standard_issuer('microsoft');

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

    /**
     * Test endpoints creation for issuers.
     * @dataProvider create_endpoints_for_standard_issuer_provider
     *
     * @covers ::create_endpoints_for_standard_issuer
     *
     * @param string $type Issuer type to create.
     * @param string|null $discoveryurl Expected discovery URL or null if this endpoint doesn't exist.
     * @param bool $hasmappingfields True if it's expected the issuer to create has mapping fields.
     * @param string|null $baseurl The service URL (mandatory parameter for some issuers, such as NextCloud or IMS OBv2.1).
     * @param string|null $expectedexception Name of the expected expection or null if no exception will be thrown.
     */
    public function test_create_endpoints_for_standard_issuer(string $type, ?string $discoveryurl = null,
        bool $hasmappingfields = true, ?string $baseurl = null, ?string $expectedexception = null): void {

        $this->resetAfterTest();

        // Mark test as long because it connects with external services.
        if (!PHPUNIT_LONGTEST) {
            $this->markTestSkipped('PHPUNIT_LONGTEST is not defined');
        }

        $this->setAdminUser();

        // Method create_endpoints_for_standard_issuer is called internally from create_standard_issuer.
        if ($expectedexception) {
            $this->expectException($expectedexception);
        }
        $issuer = api::create_standard_issuer($type, $baseurl);

        // Check endpoints have been created.
        $endpoints = api::get_endpoints($issuer);
        $this->assertNotEmpty($endpoints);
        $this->assertNotEmpty($issuer->get('image'));
        // Check discovery URL.
        if ($discoveryurl) {
            $this->assertStringContainsString($discoveryurl, $issuer->get_endpoint_url('discovery'));
        } else {
            $this->assertFalse($issuer->get_endpoint_url('discovery'));
        }
        // Check userfield mappings.
        $userfieldmappings =api::get_user_field_mappings($issuer);
        if ($hasmappingfields) {
            $this->assertNotEmpty($userfieldmappings);
        } else {
            $this->assertEmpty($userfieldmappings);
        }
    }

    /**
     * Data provider for test_create_endpoints_for_standard_issuer.
     *
     * @return array
     */
    public function create_endpoints_for_standard_issuer_provider(): array {
        return [
            'Google' => [
                'type' => 'google',
                'discoveryurl' => '.well-known/openid-configuration',
            ],
            'Google will work too with a valid baseurl parameter' => [
                'type' => 'google',
                'discoveryurl' => '.well-known/openid-configuration',
                'hasmappingfields' => true,
                'baseurl' => 'https://accounts.google.com/',
            ],
            'IMS OBv2.1' => [
                'type' => 'imsobv2p1',
                'discoveryurl' => '.well-known/badgeconnect.json',
                'hasmappingfields' => false,
                'baseurl' => 'https://dc.imsglobal.org/',
            ],
            'IMS OBv2.1 without slash in baseurl should work too' => [
                'type' => 'imsobv2p1',
                'discoveryurl' => '.well-known/badgeconnect.json',
                'hasmappingfields' => false,
                'baseurl' => 'https://dc.imsglobal.org',
            ],
            'IMS OBv2.1 with empty baseurl should return an exception' => [
                'type' => 'imsobv2p1',
                'discoveryurl' => null,
                'hasmappingfields' => false,
                'baseurl' => null,
                'expectedexception' => \moodle_exception::class,
            ],
            'Microsoft' => [
                'type' => 'microsoft',
            ],
            'Facebook' => [
                'type' => 'facebook',
            ],
            'NextCloud' => [
                'type' => 'nextcloud',
                'discoveryurl' => null,
                'hasmappingfields' => true,
                'baseurl' => 'https://dummy.local/nextcloud/',
            ],
            'NextCloud with empty baseurl should return an exception' => [
                'type' => 'nextcloud',
                'discoveryurl' => null,
                'hasmappingfields' => true,
                'baseurl' => null,
                'expectedexception' => \moodle_exception::class,
            ],
            'Invalid type should return an exception' => [
                'type' => 'fictitious',
                'discoveryurl' => null,
                'hasmappingfields' => true,
                'baseurl' => null,
                'expectedexception' => \moodle_exception::class,
            ],
        ];
    }

    /**
     * Test for get all issuers.
     */
    public function test_get_all_issuers() {
        $this->resetAfterTest();
        $this->setAdminUser();
        $googleissuer = api::create_standard_issuer('google');
        api::create_standard_issuer('facebook');
        api::create_standard_issuer('microsoft');

        // Set Google issuer to be shown only on login page.
        $record = $googleissuer->to_record();
        $record->showonloginpage = $googleissuer::LOGINONLY;
        api::update_issuer($record);

        $issuers = api::get_all_issuers();
        $this->assertCount(2, $issuers);
        $expected = ['Microsoft', 'Facebook'];
        $this->assertEqualsCanonicalizing($expected, [$issuers[0]->get_display_name(), $issuers[1]->get_display_name()]);

        $issuers = api::get_all_issuers(true);
        $this->assertCount(3, $issuers);
        $expected = ['Google', 'Microsoft', 'Facebook'];
        $this->assertEqualsCanonicalizing($expected,
            [$issuers[0]->get_display_name(), $issuers[1]->get_display_name(), $issuers[2]->get_display_name()]);
    }

    /**
     * Test for is available for login.
     */
    public function test_is_available_for_login() {
        $this->resetAfterTest();
        $this->setAdminUser();
        $googleissuer = api::create_standard_issuer('google');

        // Set Google issuer to be shown only on login page.
        $record = $googleissuer->to_record();
        $record->showonloginpage = $googleissuer::LOGINONLY;
        api::update_issuer($record);

        $this->assertFalse($googleissuer->is_available_for_login());

        // Set a clientid and clientsecret.
        $googleissuer->set('clientid', 'clientid');
        $googleissuer->set('clientsecret', 'secret');
        $googleissuer->update();

        $this->assertTrue($googleissuer->is_available_for_login());

        // Set showonloginpage to service only.
        $googleissuer->set('showonloginpage', issuer::SERVICEONLY);
        $googleissuer->update();

        $this->assertFalse($googleissuer->is_available_for_login());

        // Set showonloginpage to everywhere (service and login) and disable issuer.
        $googleissuer->set('showonloginpage', issuer::EVERYWHERE);
        $googleissuer->set('enabled', 0);
        $googleissuer->update();

        $this->assertFalse($googleissuer->is_available_for_login());

        // Enable issuer.
        $googleissuer->set('enabled', 1);
        $googleissuer->update();

        $this->assertTrue($googleissuer->is_available_for_login());

        // Remove userinfo endpoint from issuer.
        $endpoint = endpoint::get_record([
            'issuerid' => $googleissuer->get('id'),
            'name' => 'userinfo_endpoint'
        ]);
        api::delete_endpoint($endpoint->get('id'));

        $this->assertFalse($googleissuer->is_available_for_login());
    }

    /**
     * Data provider for test_get_internalfield_list and test_get_internalfields.
     *
     * @return array
     */
    public function create_custom_profile_fields(): array {
        return [
            'data' =>
            [
                'given' => [
                    'Hobbies' => [
                        'shortname' => 'hobbies',
                        'name' => 'Hobbies',
                    ]
                ],
                'expected' => [
                    'Hobbies' => [
                        'shortname' => 'hobbies',
                        'name' => 'Hobbies',
                    ]
                ]
            ],
            [
                'given' => [
                    'Billing' => [
                        'shortname' => 'billingaddress',
                        'name' => 'Billing Address',
                    ],
                    'Payment' => [
                        'shortname' => 'creditcardnumber',
                        'name' => 'Credit Card Number',
                    ]
                ],
                'expected' => [
                    'Billing' => [
                        'shortname' => 'billingaddress',
                        'name' => 'Billing Address',
                    ],
                    'Payment' => [
                        'shortname' => 'creditcardnumber',
                        'name' => 'Credit Card Number',
                    ]
                ]
            ]
        ];
    }

    /**
     * Test getting the list of internal fields.
     *
     * @dataProvider create_custom_profile_fields
     * @covers ::get_internalfield_list
     * @param array $given Categories and profile fields.
     * @param array $expected Expected value.
     */
    public function test_get_internalfield_list(array $given, array $expected): void {
        $this->resetAfterTest();
        self::generate_custom_profile_fields($given);

        $userfieldmapping = new user_field_mapping();
        $internalfieldlist = $userfieldmapping->get_internalfield_list();

        foreach ($expected as $category => $value) {
            // Custom profile fields must exist.
            $this->assertNotEmpty($internalfieldlist[$category]);

            // Category must have the custom profile fields with expected value.
            $this->assertEquals(
                $internalfieldlist[$category][\core_user\fields::PROFILE_FIELD_PREFIX . $value['shortname']],
                $value['name']
            );
        }
    }

    /**
     * Test getting the list of internal fields with flat array.
     *
     * @dataProvider create_custom_profile_fields
     * @covers ::get_internalfields
     * @param array $given Categories and profile fields.
     * @param array $expected Expected value.
     */
    public function test_get_internalfields(array $given, array $expected): void {
        $this->resetAfterTest();
        self::generate_custom_profile_fields($given);

        $userfieldmapping = new user_field_mapping();
        $internalfields = $userfieldmapping->get_internalfields();

        // Custom profile fields must exist.
        foreach ($expected as $category => $value) {
            $this->assertContains( \core_user\fields::PROFILE_FIELD_PREFIX . $value['shortname'], $internalfields);
        }
    }

    /**
     * Test getting the list of empty external/custom profile fields.
     *
     * @covers ::get_internalfields
     */
    public function test_get_empty_internalfield_list(): void {

        // Get internal (profile) fields.
        $userfieldmapping = new user_field_mapping();
        $internalfieldlist = $userfieldmapping->get_internalfields();

        // Get user fields.
        $userfields = array_merge(\core_user::AUTHSYNCFIELDS, ['picture', 'username']);

        // Internal fields and user fields must exact same.
        $this->assertEquals($userfields, $internalfieldlist);
    }

    /**
     * Test getting Return the list of profile fields.
     *
     * @dataProvider create_custom_profile_fields
     * @covers ::get_profile_field_list
     * @param array $given Categories and profile fields.
     * @param array $expected Expected value.
     */
    public function test_get_profile_field_list(array $given, array $expected): void {
        $this->resetAfterTest();
        self::generate_custom_profile_fields($given);

        $profilefieldlist = get_profile_field_list();

        foreach ($expected as $category => $value) {
            $this->assertEquals(
                $profilefieldlist[$category][\core_user\fields::PROFILE_FIELD_PREFIX . $value['shortname']],
                $value['name']
            );
        }
    }

    /**
     * Test getting the list of valid custom profile user fields.
     *
     * @dataProvider create_custom_profile_fields
     * @covers ::get_profile_field_names
     * @param array $given Categories and profile fields.
     * @param array $expected Expected value.
     */
    public function test_get_profile_field_names(array $given, array $expected): void {
        $this->resetAfterTest();
        self::generate_custom_profile_fields($given);

        $profilefieldnames = get_profile_field_names();

        // Custom profile fields must exist.
        foreach ($expected as $category => $value) {
            $this->assertContains( \core_user\fields::PROFILE_FIELD_PREFIX . $value['shortname'], $profilefieldnames);
        }
    }

    /**
     * Generate data into DB for Testing getting user fields mapping.
     *
     * @param array $given Categories and profile fields.
     */
    private function generate_custom_profile_fields(array $given): void {
        // Create a profile category and the profile fields.
        foreach ($given as $category => $value) {
            $customprofilefieldcategory = ['name' => $category, 'sortorder' => 1];
            $category = $this->getDataGenerator()->create_custom_profile_field_category($customprofilefieldcategory);
            $this->getDataGenerator()->create_custom_profile_field(
                ['shortname' => $value['shortname'],
                'name' => $value['name'],
                'categoryid' => $category->id,
                'required' => 1, 'visible' => 1, 'locked' => 0, 'datatype' => 'text', 'defaultdata' => null]);
        }
    }

}

<?php
// This file is part of IOMAD SAML2 Authentication Plugin
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

namespace auth_iomadsaml2;

/**
 * Unit tests for auth class.
 *
 * @package     auth_iomadsaml2
 * @category    test
 * @group       auth_iomadsaml2
 * @covers      \auth_iomadsaml2\auth
 * @copyright   2018 Catalyst IT Australia {@link http://www.catalyst-au.net}
 * @copyright   2021 Moodle Pty Ltd <support@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class auth_iomadsaml2_test extends \advanced_testcase {
    /**
     * Set up
     */
    public function setUp(): void {
        $this->resetAfterTest();
    }

    /**
     * Clean up after each test.
     */
    protected function tearDown(): void {
        global $SESSION;

        parent::tearDown();
        unset($SESSION->notifications);
        unset($SESSION->saml);
        unset($_GET['saml']);
    }

    /**
     * A helper function to create a custom profile field.
     *
     * @param string $shortname Short name of the field.
     * @param string $datatype Type of the field, e.g. text, checkbox, datetime, menu and etc.
     * @param bool $unique Should the field to be unique?
     *
     * @return \stdClass
     */
    protected function add_user_profile_field(string $shortname, string $datatype, bool $unique = false) : \stdClass {
        global $DB;

        // Create a new profile field.
        $data = new \stdClass();
        $data->shortname = $shortname;
        $data->datatype = $datatype;
        $data->name = 'Test ' . $shortname;
        $data->description = 'This is a test field';
        $data->required = false;
        $data->locked = false;
        $data->forceunique = $unique;
        $data->signup = false;
        $data->visible = '0';
        $data->categoryid = '0';

        $DB->insert_record('user_info_field', $data);

        return $data;
    }

    /**
     * Get generator
     *
     * @return auth_iomadsaml2_generator|auth_iomadsaml2\testing\generator
     */
    protected function get_generator() {
        if (class_exists('\core\testing\component_generator')) { // Required for Totara 15 support
            return $generator = \auth_iomadsaml2\testing\generator::instance();
        } else {
            return $this->getDataGenerator()->get_plugin_generator('auth_iomadsaml2');
        }
    }

    /**
     * Retrieve mocked auth instance.
     *
     * @return \auth_iomadsaml2\auth
     */
    protected function get_mocked_auth(): \auth_iomadsaml2\auth {
        // Setup mock, make error_page throw exception containing argument as
        // exception message. This is needed to check $msg argument and stop
        // execution like original method does.
        $auth = $this->getMockBuilder(\auth_iomadsaml2\auth::class)
            ->setMethods(['error_page'])->getMock();

        $auth->expects($this->once())
            ->method('error_page')
            ->will($this->returnCallback(function($msg) {
                throw new \coding_exception($msg);
            }));
        return $auth;
    }

    /**
     * Test test_is_configured
     */
    public function test_is_configured(): void {
        global $DB;
        // Add one IdP.
        $entity1 = $this->get_generator()->create_idp_entity([], false);

        $auth = get_auth_plugin('iomadsaml2');
        $files = array(
            'crt' => $auth->certcrt,
            'pem' => $auth->certpem,
            'xml' => $auth->get_file(md5($entity1->metadataurl) . '.idp.xml'),
        );

        // Sanity check.
        $this->assertFalse($auth->is_configured());

        // File crt: true.
        // File pem: false.
        // File xml: false.
        // File result: failure.
        touch($files['crt']);
        $this->assertFalse($auth->is_configured());

        // File crt: true.
        // File pem: true.
        // File xml: false.
        // File result: failure.
        touch($files['pem']);
        $this->assertFalse($auth->is_configured());

        // File crt: true.
        // File pem: true.
        // File xml: true.
        // File result: success.
        touch($files['xml']);
        $this->assertTrue($auth->is_configured());

        // Make IdP inactive.
        $DB->update_record('auth_iomadsaml2_idps', [
            'id' => $entity1->id,
            'activeidp' => 0,
        ]);
        $auth = get_auth_plugin('iomadsaml2');

        $this->assertFalse($auth->is_configured());
    }

    public function test_is_configured_works_with_multi_idp_in_one_xml(): void {
        // Add two IdPs.
        $metadataurl = 'https://idp.example.org/idp/shibboleth';
        $this->get_generator()->create_idp_entity(['metadataurl' => $metadataurl], false);
        $this->get_generator()->create_idp_entity(['metadataurl' => $metadataurl], false);

        $auth = get_auth_plugin('iomadsaml2');
        touch($auth->certcrt);
        touch($auth->certpem);
        $this->assertFalse($auth->is_configured());

        // Create xml.
        touch($auth->get_file(md5($metadataurl). ".idp.xml"));
        $this->assertTrue($auth->is_configured());
    }

    public function test_class_constructor(): void {
        // Sanity check.
        $auth = get_auth_plugin('iomadsaml2');
        $this->assertFalse($auth->is_configured());
        $this->assertCount(0, $auth->metadataentities);

        // Create one entity.
        $entity1 = $this->get_generator()->create_idp_entity();
        $auth = get_auth_plugin('iomadsaml2');
        $this->assertTrue($auth->is_configured());
        $this->assertCount(1, $auth->metadataentities);

        // Name attribute is matching defaultname.
        $this->assertEquals($entity1->defaultname, reset($auth->metadataentities)->name);

        // Encoded entityid present as an attribute as well as the key.
        $this->assertArrayHasKey(md5($entity1->entityid), $auth->metadataentities);
        $this->assertEquals(md5($entity1->entityid), reset($auth->metadataentities)->md5entityid);

        // Multiidp flag is false.
        $reflector = new \ReflectionClass($auth);
        $property = $reflector->getParentClass()->getProperty('multiidp');
        $property->setAccessible(true);
        $this->assertFalse($property->getValue($auth));

        // DefaultIdP is not defined.
        $property = $reflector->getParentClass()->getProperty('defaultidp');
        $property->setAccessible(true);
        $this->assertNull($property->getValue($auth));

        // Create non-active entity. Nothing should change.
        $preventities = $auth->metadataentities;
        $this->get_generator()->create_idp_entity(['activeidp' => 0]);
        $auth = get_auth_plugin('iomadsaml2');
        $this->assertCount(1, $auth->metadataentities);
        $this->assertEquals(reset($preventities), reset($auth->metadataentities));

        // Multiidp flag is false.
        $reflector = new \ReflectionClass($auth);
        $property = $reflector->getParentClass()->getProperty('multiidp');
        $property->setAccessible(true);
        $this->assertFalse($property->getValue($auth));

        // DefaultIdP is not defined.
        $property = $reflector->getParentClass()->getProperty('defaultidp');
        $property->setAccessible(true);
        $this->assertNull($property->getValue($auth));

        // Create another entity with displayname and default flag set.
        $entity3 = $this->get_generator()->create_idp_entity(['displayname' => 'Login 1', 'defaultidp' => 1]);
        $auth = get_auth_plugin('iomadsaml2');
        $this->assertCount(2, $auth->metadataentities);

        // Backwards compatibility with older PHPUnit - use old Canonicalizing method.
        if (method_exists($this, 'assertEqualsCanonicalizing')) {
            // Check entity name.
            $this->assertEqualsCanonicalizing(['Login 1', $entity1->defaultname], array_column($auth->metadataentities, 'name'));
            // Encoded entityid present as an attribute as well as the key.
            $this->assertEqualsCanonicalizing([md5($entity1->entityid), md5($entity3->entityid)],
                array_column($auth->metadataentities, 'md5entityid'));
            $this->assertEqualsCanonicalizing([md5($entity1->entityid), md5($entity3->entityid)],
                array_keys($auth->metadataentities));
        } else {
            // Check entity name.
            $this->assertEquals(['Login 1', $entity1->defaultname],
                array_column($auth->metadataentities, 'name'), '', 0, 10, true);
            // Encoded entityid present as an attribute as well as the key.
            $this->assertEquals([md5($entity1->entityid), md5($entity3->entityid)],
                array_column($auth->metadataentities, 'md5entityid'), '', 0, 10, true);
            $this->assertEquals([md5($entity1->entityid), md5($entity3->entityid)],
                array_keys($auth->metadataentities), '', 0, 10, true);
        }

        // Multiidp flag is true.
        $reflector = new \ReflectionClass($auth);
        $property = $reflector->getParentClass()->getProperty('multiidp');
        $property->setAccessible(true);
        $this->assertTrue($property->getValue($auth));

        // DefaultIdP is defined and matching third entity.
        $property = $reflector->getParentClass()->getProperty('defaultidp');
        $property->setAccessible(true);
        $this->assertNotNull($property->getValue($auth));
        $this->assertEquals($auth->metadataentities[md5($entity3->entityid)], $property->getValue($auth));
    }

    public function test_loginpage_idp_list(): void {
        global $DB;

        // Add IdP entity.
        $entity1 = $this->get_generator()->create_idp_entity();

        // Single list item is expected.
        $auth = get_auth_plugin('iomadsaml2');
        $list = $auth->loginpage_idp_list('/');
        $this->assertCount(1, $list);

        // Inspect the plugin configured item name.
        $this->assertEquals(get_config('auth_iomadsaml2', 'idpname'), $list[0]['name']);

        // Inspect the item url.
        $url = $list[0]['url'];
        $this->assertInstanceOf(\moodle_url::class, $url);
        $this->assertEquals('/moodle/auth/iomadsaml2/login.php', $url->get_path());
        $this->assertEquals('/', $url->get_param('wants'));
        $this->assertEquals(md5($entity1->entityid), $url->get_param('idp'));
        $this->assertEquals('off', $url->get_param('passive'));

        // Wantsurl is pointing to auth/iomadsaml2/login.php.
        $list = $auth->loginpage_idp_list('/auth/iomadsaml2/login.php');
        $url = $list[0]['url'];
        $this->assertInstanceOf(\moodle_url::class, $url);
        $this->assertEquals('/moodle/auth/iomadsaml2/login.php', $url->get_path());
        $this->assertNull($url->get_param('wants'));
        $this->assertNull($url->get_param('idp'));
        $this->assertEquals('off', $url->get_param('passive'));

        // Unset default name in config (used for overriding).
        set_config('idpname', '', 'auth_iomadsaml2');
        $auth = get_auth_plugin('iomadsaml2');
        $list = $auth->loginpage_idp_list('/');
        $this->assertEquals($entity1->defaultname, $list[0]['name']);

        // Set metadata display name.
        $DB->update_record('auth_iomadsaml2_idps', [
            'id' => $entity1->id,
            'displayname' => 'Test',
        ]);
        $auth = get_auth_plugin('iomadsaml2');
        $list = $auth->loginpage_idp_list('/');
        $this->assertEquals('Test', $list[0]['name']);

        // Unset metadata names, expect default.
        $DB->update_record('auth_iomadsaml2_idps', [
            'id' => $entity1->id,
            'displayname' => '',
            'defaultname' => '',
        ]);
        $auth = get_auth_plugin('iomadsaml2');
        $list = $auth->loginpage_idp_list('/');
        $this->assertEquals($auth->config->idpdefaultname, $list[0]['name']);

        // Expect name in idpmetadata config to be used when no displayname
        // or defaultname are defined in entity.
        set_config('idpmetadata', 'Hello ' . $entity1->metadataurl, 'auth_iomadsaml2');
        $auth = get_auth_plugin('iomadsaml2');
        $list = $auth->loginpage_idp_list('/');
        $this->assertEquals('Hello', $list[0]['name']);

        // Expect debug message if idpmetadata config does not match one stored in DB.
        set_config('idpmetadata', $entity1->metadataurl . 'modified', 'auth_iomadsaml2');
        $auth = get_auth_plugin('iomadsaml2');
        $auth->loginpage_idp_list('/');
        $this->assertDebuggingCalled();

        // Deactivate.
        $DB->update_record('auth_iomadsaml2_idps', [
            'id' => $entity1->id,
            'activeidp' => 0,
        ]);
        $auth = get_auth_plugin('iomadsaml2');
        $list = $auth->loginpage_idp_list('/');
        $this->assertEmpty($list);
    }

    public function test_loginpage_idp_list_multiple(): void {
        global $DB;

        // Add two IdPs.
        $entity1 = $this->get_generator()->create_idp_entity(['displayname' => 'Login 1']);
        $entity2 = $this->get_generator()->create_idp_entity(['displayname' => 'Login 2']);

        // Two list items are expected.
        $auth = get_auth_plugin('iomadsaml2');
        $list = $auth->loginpage_idp_list('/');
        $this->assertCount(2, $list);

        // Unset default name in config (used for overriding).
        set_config('idpname', '', 'auth_iomadsaml2');
        $auth = get_auth_plugin('iomadsaml2');
        $list = $auth->loginpage_idp_list('/');

        // Backwards compatibility with older PHPUnit - use old Canonicalizing method.
        if (method_exists($this, 'assertEqualsCanonicalizing')) {
            $this->assertEqualsCanonicalizing([$entity1->displayname, $entity2->displayname], array_column($list, 'name'));
        } else {
            $this->assertEquals([$entity1->displayname, $entity2->displayname], array_column($list, 'name'), '', 0, 10, true);
        }

        // Unset display name for first entity, it will be replaced by entity default name.
        $DB->update_record('auth_iomadsaml2_idps', [
            'id' => $entity1->id,
            'displayname' => '',
        ]);
        $auth = get_auth_plugin('iomadsaml2');
        $list = $auth->loginpage_idp_list('/');

        // Backwards compatibility with older PHPUnit - use old Canonicalizing method.
        if (method_exists($this, 'assertEqualsCanonicalizing')) {
            $this->assertEqualsCanonicalizing([$entity1->defaultname, $entity2->displayname], array_column($list, 'name'));
        } else {
            $this->assertEquals([$entity1->defaultname, $entity2->displayname], array_column($list, 'name'), '', 0, 10, true);
        }

        // Unset default name for first entity, it will be replaced by default with hostname mentioned.
        $DB->update_record('auth_iomadsaml2_idps', [
            'id' => $entity1->id,
            'defaultname' => '',
        ]);
        $idpname1 = get_string('idpnamedefault_varaible', 'auth_iomadsaml2', parse_url($entity1->entityid, PHP_URL_HOST));
        $auth = get_auth_plugin('iomadsaml2');
        $list = $auth->loginpage_idp_list('/');

        // Backwards compatibility with older PHPUnit - use old Canonicalizing method.
        if (method_exists($this, 'assertEqualsCanonicalizing')) {
            $this->assertEqualsCanonicalizing([$idpname1, $entity2->displayname], array_column($list, 'name'));
        } else {
            $this->assertEquals([$idpname1, $entity2->displayname], array_column($list, 'name'), '', 0, 10, true);
        }

        // Deactivate first entity.
        $DB->update_record('auth_iomadsaml2_idps', [
            'id' => $entity1->id,
            'activeidp' => 0,
        ]);
        $auth = get_auth_plugin('iomadsaml2');
        $list = $auth->loginpage_idp_list('/');
        $this->assertCount(1, $list);
        $this->assertEquals($entity2->displayname, $list[0]['name']);
    }

    public function test_saml_login_complete_missing_idpattr(): void {
        $attribs = [
            'uid' => ['samlu1'],
            'email' => ['samluser1@example.com'],
        ];
        set_config('idpattr', 'blabla', 'auth_iomadsaml2');
        $auth = $this->get_mocked_auth();

        $sink = $this->redirectEvents();
        try {
            $auth->saml_login_complete($attribs);
            $this->fail('Exception expected');
        } catch (\coding_exception $e) {
            // Validate reason.
            if (method_exists($this, 'assertStringContainsString')) {
                $this->assertStringContainsString(get_string('noattribute', 'auth_iomadsaml2', 'blabla'), $e->getMessage());
            } else {
                $this->assertContains(get_string('noattribute', 'auth_iomadsaml2', 'blabla'), $e->getMessage());
            }
        }

        // Checking that the event contains the expected values.
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = array_pop($events);
        $this->assertInstanceOf('\core\event\user_login_failed', $event);
        $this->assertEquals(AUTH_LOGIN_NOUSER, $event->get_data()['other']['reason']);
    }

    public function test_saml_login_complete_group_restriction(): void {
        $attribs = [
            'uid' => ['samlu1'],
            'email' => ['samluser1@example.com'],
            'library' => ['overdue'],
        ];
        set_config('grouprules', 'deny library=overdue', 'auth_iomadsaml2');
        $auth = $this->get_mocked_auth();

        $sink = $this->redirectEvents();
        try {
            $auth->saml_login_complete($attribs);
            $this->fail('Exception expected');
        } catch (\coding_exception $e) {
            // Validate reason.
            if (method_exists($this, 'assertStringContainsString')) {
                $this->assertStringContainsString(get_string('flagmessage_default', 'auth_iomadsaml2'), $e->getMessage());
            } else {
                $this->assertContains(get_string('flagmessage_default', 'auth_iomadsaml2'), $e->getMessage());
            }
        }

        // Checking that the event contains the expected values.
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = array_pop($events);
        $this->assertInstanceOf('\core\event\user_login_failed', $event);
        $this->assertEquals(AUTH_LOGIN_UNAUTHORISED, $event->get_data()['other']['reason']);
    }

    public function test_saml_login_complete_email_taken(): void {
        $attribs = [
            'uid' => ['samlu1'],
            'email' => ['samluser1@example.com'],
        ];
        set_config('autocreate', '1', 'auth_iomadsaml2');
        set_config('field_map_email', 'email', 'auth_iomadsaml2');
        $this->getDataGenerator()->create_user(['email' => 'samluser1@example.com']);
        $auth = $this->get_mocked_auth();

        $sink = $this->redirectEvents();
        try {
            $auth->saml_login_complete($attribs);
            $this->fail('Exception expected');
        } catch (\coding_exception $e) {
            // Validate reason.
            if (method_exists($this, 'assertStringContainsString')) {
                $this->assertStringContainsString(get_string('emailtaken', 'auth_iomadsaml2', $attribs['email'][0]), $e->getMessage());
            } else {
                $this->assertContains(get_string('emailtaken', 'auth_iomadsaml2', $attribs['email'][0]), $e->getMessage());
            }
        }

        // Checking that the event contains the expected values.
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = array_pop($events);
        $this->assertInstanceOf('\core\event\user_login_failed', $event);
        $this->assertEquals(AUTH_LOGIN_FAILED, $event->get_data()['other']['reason']);
    }

    public function test_saml_login_complete_allowemailaddresses(): void {
        global $CFG;
        $attribs = [
            'uid' => ['samlu1'],
            'email' => ['samluser1@example.com'],
        ];
        set_config('autocreate', '1', 'auth_iomadsaml2');
        $CFG->allowemailaddresses = 'other.com';
        $auth = $this->get_mocked_auth();

        $sink = $this->redirectEvents();
        try {
            $auth->saml_login_complete($attribs);
            $this->fail('Exception expected');
        } catch (\coding_exception $e) {
            // Validate reason.
            if (method_exists($this, 'assertStringContainsString')) {
                $this->assertStringContainsString(get_string('flagmessage_default', 'auth_iomadsaml2'), $e->getMessage());
            } else {
                $this->assertContains(get_string('flagmessage_default', 'auth_iomadsaml2'), $e->getMessage());
            }
        }

        // Checking that the event contains the expected values.
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = array_pop($events);
        $this->assertInstanceOf('\core\event\user_login_failed', $event);
        $this->assertEquals(AUTH_LOGIN_FAILED, $event->get_data()['other']['reason']);
    }

    public function test_saml_login_complete_no_autocreate(): void {
        $attribs = [
            'uid' => ['samlu1'],
            'email' => ['samluser1@example.com'],
        ];
        set_config('autocreate', '0', 'auth_iomadsaml2');
        $auth = $this->get_mocked_auth();

        $sink = $this->redirectEvents();
        try {
            $auth->saml_login_complete($attribs);
            $this->fail('Exception expected');
        } catch (\coding_exception $e) {
            // Validate reason.
            if (method_exists($this, 'assertStringContainsString')) {
                $this->assertStringContainsString(get_string('nouser', 'auth_iomadsaml2', $attribs['uid'][0]), $e->getMessage());
            } else {
                $this->assertContains(get_string('nouser', 'auth_iomadsaml2', $attribs['uid'][0]), $e->getMessage());
            }
        }

        // Checking that the event contains the expected values.
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = array_pop($events);
        $this->assertInstanceOf('\core\event\user_login_failed', $event);
        $this->assertEquals(AUTH_LOGIN_NOUSER, $event->get_data()['other']['reason']);
    }

    public function test_saml_login_complete_suspended(): void {
        $attribs = [
            'uid' => ['samlu1'],
            'email' => ['samluser1@example.com'],
        ];
        $this->getDataGenerator()->create_user(['username' => 'samlu1', 'suspended' => 1]);
        $auth = $this->get_mocked_auth();

        $sink = $this->redirectEvents();
        try {
            $auth->saml_login_complete($attribs);
            $this->fail('Exception expected');
        } catch (\coding_exception $e) {
            // Validate reason.
            if (method_exists($this, 'assertStringContainsString')) {
                $this->assertStringContainsString(get_string('suspendeduser', 'auth_iomadsaml2', $attribs['uid'][0]), $e->getMessage());
            } else {
                $this->assertContains(get_string('suspendeduser', 'auth_iomadsaml2', $attribs['uid'][0]), $e->getMessage());
            }
        }

        // Checking that the event contains the expected values.
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = array_pop($events);
        $this->assertInstanceOf('\core\event\user_login_failed', $event);
        $this->assertEquals(AUTH_LOGIN_SUSPENDED, $event->get_data()['other']['reason']);
    }

    public function test_saml_login_complete_wrong_auth(): void {
        $attribs = [
            'uid' => ['samlu1'],
            'email' => ['samluser1@example.com'],
        ];
        set_config('anyauth', '0', 'auth_iomadsaml2');
        $this->getDataGenerator()->create_user(['username' => 'samlu1', 'auth' => 'manual']);
        $auth = $this->get_mocked_auth();

        $sink = $this->redirectEvents();
        try {
            $auth->saml_login_complete($attribs);
            $this->fail('Exception expected');
        } catch (\coding_exception $e) {
            // Validate reason.
            if (method_exists($this, 'assertStringContainsString')) {
                $this->assertStringContainsString(get_string('wrongauth', 'auth_iomadsaml2', $attribs['uid'][0]), $e->getMessage());
            } else {
                $this->assertContains(get_string('wrongauth', 'auth_iomadsaml2', $attribs['uid'][0]), $e->getMessage());
            }
        }

        // Checking that the event contains the expected values.
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = array_pop($events);
        $this->assertInstanceOf('\core\event\user_login_failed', $event);
        $this->assertEquals(AUTH_LOGIN_UNAUTHORISED, $event->get_data()['other']['reason']);
    }

    public function test_saml_login_complete_disabled_auth(): void {
        $attribs = [
            'uid' => ['samlu1'],
            'email' => ['samluser1@example.com'],
        ];
        set_config('anyauth', '1', 'auth_iomadsaml2');
        $this->getDataGenerator()->create_user(['username' => 'samlu1', 'auth' => 'shibboleth']);
        $auth = $this->get_mocked_auth();

        $sink = $this->redirectEvents();
        try {
            $auth->saml_login_complete($attribs);
            $this->fail('Exception expected');
        } catch (\coding_exception $e) {
            // Validate reason.
            $msg = get_string('anyauthotherdisabled', 'auth_iomadsaml2', [
                'username' => $attribs['uid'][0], 'auth' => 'shibboleth',
            ]);
            if (method_exists($this, 'assertStringContainsString')) {
                $this->assertStringContainsString($msg, $e->getMessage());
            } else {
                $this->assertContains($msg, $e->getMessage());
            }
        }

        // Checking that the event contains the expected values.
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = array_pop($events);
        $this->assertInstanceOf('\core\event\user_login_failed', $event);
        $this->assertEquals(AUTH_LOGIN_UNAUTHORISED, $event->get_data()['other']['reason']);
    }

    public function test_saml_login_complete_new_account(): void {
        global $USER;
        $attribs = [
            'uid' => ['samlu1'],
            'email' => ['samluser1@example.com'],
        ];
        set_config('autocreate', '1', 'auth_iomadsaml2');
        set_config('field_map_email', 'email', 'auth_iomadsaml2');

        // Sanity check.
        $this->assertFalse(isloggedin());

        $sink = $this->redirectEvents();

        // Try to login, suppress output.
        $auth = new \auth_iomadsaml2\auth();
        @$auth->saml_login_complete($attribs);

        // Check global object.
        $this->assertEquals($attribs['uid'][0], $USER->username);
        $this->assertEquals($attribs['email'][0], $USER->email);

        // Checking that the events contain the expected values.
        $events = $sink->get_events();
        $this->assertCount(3, $events);
        $event = array_pop($events);
        $this->assertInstanceOf('\core\event\user_loggedin', $event);
        $this->assertEquals($USER->id, $event->get_data()['objectid']);
        $event = array_pop($events);
        $this->assertInstanceOf('\core\event\user_updated', $event);
        $this->assertEquals($USER->id, $event->get_data()['objectid']);
        $event = array_pop($events);
        $this->assertInstanceOf('\core\event\user_created', $event);
        $this->assertEquals($USER->id, $event->get_data()['objectid']);
    }

    public function test_saml_login_complete_existing_account(): void {
        global $USER;
        $attribs = [
            'uid' => ['samlu1'],
            'email' => ['samluser1@example.com'],
        ];
        set_config('field_map_email', 'email', 'auth_iomadsaml2');
        set_config('field_updatelocal_email', 'onlogin', 'auth_iomadsaml2');
        $user = $this->getDataGenerator()->create_user(['username' => 'samlu1', 'auth' => 'iomadsaml2']);

        // Sanity check.
        $this->assertFalse(isloggedin());
        $this->assertNotEquals($attribs['email'][0], $user->email);

        $sink = $this->redirectEvents();

        // Try to login, suppress output.
        $auth = new \auth_iomadsaml2\auth();
        @$auth->saml_login_complete($attribs);

        // Check global object, make sure email was updated.
        $this->assertEquals($attribs['uid'][0], $USER->username);
        $this->assertEquals($attribs['email'][0], $USER->email);

        // Checking that the events contain the expected values.
        $events = $sink->get_events();
        $this->assertCount(2, $events);
        $event = array_pop($events);
        $this->assertInstanceOf('\core\event\user_loggedin', $event);
        $this->assertEquals($USER->id, $event->get_data()['objectid']);
        $event = array_pop($events);
        $this->assertInstanceOf('\core\event\user_updated', $event);
        $this->assertEquals($USER->id, $event->get_data()['objectid']);
    }

    public function test_saml_login_complete_existing_account_match_custom_profile_field(): void {
        global $USER;

        $field1 = $this->add_user_profile_field('field1', 'text', true);

        $attribs = [
            'uid' => ['samluser'],
        ];

        set_config('mdlattr', 'profile_field_field1', 'auth_iomadsaml2');

        $user = $this->getDataGenerator()->create_user(['auth' => 'iomadsaml2']);
        profile_save_data((object)['id' => $user->id, 'profile_field_' . $field1->shortname => 'samluser']);

        $this->assertFalse(isloggedin());

        $sink = $this->redirectEvents();

        // Try to login, suppress output.
        $auth = new \auth_iomadsaml2\auth();
        @$auth->saml_login_complete($attribs);

        // Check global object, make sure email was updated.
        $this->assertEquals($user->id, $USER->id);
        $this->assertEquals($user->username, $USER->username);

        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = array_pop($events);
        $this->assertInstanceOf('\core\event\user_loggedin', $event);
        $this->assertEquals($USER->id, $event->get_data()['objectid']);
    }

    /**
     * Test test_should_login_redirect
     *
     * @dataProvider provider_should_login_redirect
     * @param array $cfg core config
     * @param array $config plugin config
     * @param bool $param
     * @param bool $multiidp
     * @param bool $session
     * @param bool $expected The expected return value
     */
    public function test_should_login_redirect($cfg, $config, $param, $multiidp, $session, $expected): void {
        global $SESSION;

        foreach ($config as $key => $value) {
            set_config($key, $value, 'auth_iomadsaml2');
        }

        $SESSION->saml = $session;

        // HTML get param optional_param('saml', 0, PARAM_BOOL).
        if ($param !== null) {
            if ($param == 'error') {
                $_GET['SimpleSAML_Auth_State_exceptionId'] = '...';
            } else if ($param == 'post') {
                $_SERVER['REQUEST_METHOD'] = 'POST';
            } else {
                $_GET['saml'] = $param;
            }
        }

        // HTML get param optional_param('multiidp', 0, PARAM_BOOL).
        if ($multiidp === true) {
            $_GET['multiidp'] = true;
        }

        /** @var auth_plugin_iomadsaml2 $auth */
        $auth = get_auth_plugin('iomadsaml2');
        $result = $auth->should_login_redirect();

        $this->assertEquals($expected, $result);
    }

    /**
     * Dataprovider for the test_should_login_redirect testcase
     *
     * @return array of testcases
     */
    public function provider_should_login_redirect(): array {
        $midp = (new \moodle_url('/auth/iomadsaml2/selectidp.php'))->out();
        return [
            // Login normal, dual login on.
            "1. dual: y, param: null, multiidp: false, session: false" => [
                [],
                ['duallogin' => true],
                null, false, false,
                false],

            // Login normal, dual login on.
            "2. dual: y, param: off, multiidp: false, session: false" => [
                [],
                ['duallogin' => true],
                'off', false, false,
                false],

            // SAML redirect, ?saml=on.
            "3. dual: y, param: on, multiidp: false, session: false" => [
                [],
                ['duallogin' => true],
                'on', false, false,
                true],

            // Login normal, $SESSION->saml=0.
            "4. dual: n, param: null, multiidp: false, session: false" => [
                [],
                ['duallogin' => false],
                null, false, false,
                false],

            // Login normal, ?saml=off.
            "5. dual: n, param: off, multiidp: false, session: false" => [
                [],
                ['duallogin' => false],
                'off', false, false,
                false],

            // SAML redirect, ?saml=on.
            "6. dual: n, param: on, multiidp: false, session: false" => [
                [],
                ['duallogin' => false],
                'on', false, false,
                true],

            // SAML redirect, $SESSION->saml=1.
            "7. dual: n, param: null, multiidp: false, session: true" => [
                [],
                ['duallogin' => false],
                null, false, true,
                true],

            // Login normal, ?saml=off.
            "8. dual: n, param: off, multiidp: false, session: true" => [
                [],
                ['duallogin' => false],
                'off', false, true,
                false],

            // SAML redirect, ?saml=on.
            "9. dual: n, param: on, multiidp: false, session: true" => [
                [],
                ['duallogin' => false],
                'on', false, true,
                true],

            // For passive mode always redirect, IOMAD SAML2 will redirect back if not logged in.
            "10. dual: p, param: null, multiidp: false, session: true" => [
                [],
                ['duallogin' => 'passive'],
                null, false, true,
                true],

            // Except if ?saml=off.
            "11. dual: p, param: off, multiidp: false, session: true" => [
                [],
                ['duallogin' => 'passive'],
                'off', false, true,
                false],

            "12. dual: p, param: on, multiidp: false, session: true" => [
                [],
                ['duallogin' => 'passive'],
                'on', false, true,
                true],

            // Except if ?saml=off.
            "14. dual: p, param: off, multiidp: false, session: false" => [
                [],
                ['duallogin' => 'passive'],
                'off', false, false,
                false],

            "15. dual: p, param: on, multiidp: false, session: false" => [
                [],
                ['duallogin' => 'passive'],
                'on', false, false,
                true],

            // Passive redirect back.
            "16. dual: p, with SAMLerror" => [
                [],
                ['duallogin' => 'passive'],
                'error', false, false,
                false],

            // POSTing.
            "17. dual: p using POST" => [
                [],
                ['duallogin' => 'passive'],
                'post', false, false,
                false],

            // Param multi-idp.
            // Login normal, dual login on. Multi IdP true.
            "18. dual: y, param: null, multiidp: true, session: false" => [
                [],
                ['duallogin' => true],
                null, true, false,
                $midp],

            // Login normal, dual login on. Multi IdP true.
            "19. dual: y, param: off, multiidp: true, session: false" => [
                [],
                ['duallogin' => true],
                'off', true, false,
                false],

            // SAML redirect, ?saml=on. Multi IdP true.
            "20. dual: y, param: on, multiidp: true, session: false" => [
                [],
                ['duallogin' => true],
                'on', true, false,
                $midp],
        ];
    }

    /**
     * Test test_check_whitelisted_ip_redirect
     *
     * @dataProvider provider_check_whitelisted_ip_redirect
     * @param string $saml
     * @param string $remoteip
     * @param string $whitelist
     * @param bool $expected The expected return value
     */
    public function test_check_whitelisted_ip_redirect($saml, $remoteip, $whitelist, $expected): void {
        // Setting an address here as getremoteaddr() will return default 0.0.0.0 which then is ignored by the address_in_subnet
        // function.
        $_SERVER['REMOTE_ADDR'] = $remoteip;

        $this->get_generator()->create_idp_entity(['whitelist' => $whitelist]);
        $auth = get_auth_plugin('iomadsaml2');

        if ($saml !== null) {
            $_GET['saml'] = $saml;
        }

        $result = $auth->should_login_redirect();
        $this->assertTrue($result === $expected);
    }

    /**
     * Dataprovider for {@see self::test_check_whitelisted_ip_redirect} testcase
     *
     * @return array
     */
    public function provider_check_whitelisted_ip_redirect(): array {
        return [
            'saml off, no ip, no redirect'              => ['off', '1.2.3.4', '', false],
            'saml not specified, junk, no redirect'     => [null, '1.2.3.4', 'qwer1234!@#qwer', false],
            'saml not specified, junk+ip, yes redirect' => [null, '1.2.3.4', "qwer1234!@#qwer\n1.2.3.4", true],
            'saml not specified, localip, yes redirect' => [null, '1.2.3.4', "127.0.0.\n1.", true],
            'saml not specified, wrongip, no redirect' => [null, '4.3.2.1', "127.0.0.\n1.", false],
        ];
    }

    /**
     * Data provider with the test attributes for is_access_allowed_for_member_* methods.
     *
     * @return array
     */
    public function provider_is_access_allowed(): array {
        return [
            '' => [[
                ['uid' => 'test'], // User don't have groups attribute.
                ['uid' => 'test', 'groups' => ['blocked']], // In blocked group.
                ['uid' => 'test', 'groups' => ['allowed']],  // In allowed group.
                ['uid' => 'test', 'groups' => ['allowed', 'blocked']], // In both allowed first.
                ['uid' => 'test', 'groups' => ['blocked', 'allowed']], // In both blocked first.
                ['uid' => 'test', 'groups' => []],  // Groups exists, but empty.
            ]]
        ];
    }

    /**
     * Test access allowed if required attributes are not configured.
     *
     * @dataProvider provider_is_access_allowed
     * @param array $attributes
     */
    public function test_is_access_allowed_for_member_not_configured($attributes): void {
        set_config('idpattr', 'uid', 'auth_iomadsaml2');

        // User don't have groups attribute.
        $auth = get_auth_plugin('iomadsaml2');
        $this->assertTrue($auth->is_access_allowed_for_member($attributes[0]));

        // In blocked group.
        $auth = get_auth_plugin('iomadsaml2');
        $this->assertTrue($auth->is_access_allowed_for_member($attributes[1]));

        // In allowed group.
        $auth = get_auth_plugin('iomadsaml2');
        $this->assertTrue($auth->is_access_allowed_for_member($attributes[2]));

        // In both allowed first.
        $auth = get_auth_plugin('iomadsaml2');
        $this->assertTrue($auth->is_access_allowed_for_member($attributes[3]));

        // In both blocked first.
        $auth = get_auth_plugin('iomadsaml2');
        $this->assertTrue($auth->is_access_allowed_for_member($attributes[4]));

        // Groups exists, but empty.
        $auth = get_auth_plugin('iomadsaml2');
        $this->assertTrue($auth->is_access_allowed_for_member($attributes[5]));
    }

    /**
     * Test access allowed if configured, but restricted groups attribute is set to empty.
     *
     * @dataProvider provider_is_access_allowed
     * @param array $attributes
     */
    public function test_is_access_allowed_for_member_blocked_empty($attributes): void {
        set_config('idpattr', 'uid', 'auth_iomadsaml2');
        set_config('grouprules', 'allow groups=allowed', 'auth_iomadsaml2');

        $auth = get_auth_plugin('iomadsaml2');

        // User don't have groups attribute.
        $this->assertTrue($auth->is_access_allowed_for_member($attributes[0]));

        // In blocked group.
        $this->assertFalse($auth->is_access_allowed_for_member($attributes[1]));

        // In allowed group.
        $this->assertTrue($auth->is_access_allowed_for_member($attributes[2]));

        // In both allowed first.
        $this->assertTrue($auth->is_access_allowed_for_member($attributes[3]));

        // In both blocked first.
        $this->assertTrue($auth->is_access_allowed_for_member($attributes[4]));

        // Groups exist, but empty.
        $this->assertTrue($auth->is_access_allowed_for_member($attributes[5]));
    }

    /**
     * Test access allowed if configured, but allowed groups attribute is set to empty.
     *
     * @dataProvider provider_is_access_allowed
     * @param array $attributes
     */
    public function test_is_access_allowed_for_member_allowed_empty($attributes): void {
        set_config('idpattr', 'uid', 'auth_iomadsaml2');
        set_config('grouprules', 'deny groups=blocked', 'auth_iomadsaml2');
        $auth = get_auth_plugin('iomadsaml2');

        // User don't have groups attribute.
        $this->assertTrue($auth->is_access_allowed_for_member($attributes[0]));

        // In blocked group.
        $this->assertFalse($auth->is_access_allowed_for_member($attributes[1]));

        // In allowed group.
        $this->assertFalse($auth->is_access_allowed_for_member($attributes[2]));

        // In both allowed first.
        $this->assertFalse($auth->is_access_allowed_for_member($attributes[3]));

        // In both blocked first.
        $this->assertFalse($auth->is_access_allowed_for_member($attributes[4]));

        // Groups exist, but empty.
        $this->assertTrue($auth->is_access_allowed_for_member($attributes[5]));
    }

    /**
     * Test access allowed if fully configured.
     *
     * @dataProvider provider_is_access_allowed
     * @param array $attributes
     */
    public function test_is_access_allowed_for_member_allowed_and_blocked($attributes): void {
        set_config('idpattr', 'uid', 'auth_iomadsaml2');
        set_config('grouprules', "deny groups=blocked\nallow groups=allowed", 'auth_iomadsaml2');

        $auth = get_auth_plugin('iomadsaml2');

        // User don't have groups attribute.
        $this->assertTrue($auth->is_access_allowed_for_member($attributes[0]));

        // In blocked group.
        $this->assertFalse($auth->is_access_allowed_for_member($attributes[1]));

        // In allowed group.
        $this->assertTrue($auth->is_access_allowed_for_member($attributes[2]));

        // In both allowed first.
        $this->assertFalse($auth->is_access_allowed_for_member($attributes[3]));

        // In both blocked first.
        $this->assertFalse($auth->is_access_allowed_for_member($attributes[4]));

        // Groups exist, but empty.
        $this->assertTrue($auth->is_access_allowed_for_member($attributes[5]));
    }

    /**
     * Test access allowed if fully configured and allowed priority is set to yes.
     *
     * @dataProvider provider_is_access_allowed
     * @param array $attributes
     */
    public function test_is_access_allowed_for_member_allowed_and_blocked_with_allowed_priority($attributes): void {
        set_config('idpattr', 'uid', 'auth_iomadsaml2');
        set_config('grouprules', "allow groups=allowed\ndeny groups=blocked", 'auth_iomadsaml2');

        $auth = get_auth_plugin('iomadsaml2');

        // User don't have groups attribute.
        $this->assertTrue($auth->is_access_allowed_for_member($attributes[0]));

        // In blocked group.
        $this->assertFalse($auth->is_access_allowed_for_member($attributes[1]));

        // In allowed group.
        $this->assertTrue($auth->is_access_allowed_for_member($attributes[2]));

        // In both allowed first.
        $this->assertTrue($auth->is_access_allowed_for_member($attributes[3]));

        // In both blocked first.
        $this->assertTrue($auth->is_access_allowed_for_member($attributes[4]));

        // Groups exist, but empty.
        $this->assertTrue($auth->is_access_allowed_for_member($attributes[5]));
    }

    /**
     * Test test_update_custom_user_profile_fields
     *
     * @dataProvider provider_update_custom_user_profile_fields
     * @param array $attributes
     */
    public function test_update_custom_user_profile_fields($attributes): void {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/user/profile/lib.php');

        $this->resetAfterTest();

        $auth = get_auth_plugin('iomadsaml2');

        $user = $this->getDataGenerator()->create_user();

        $fieldname = key($attributes);

        // Add a custom profile field named $fieldname.
        $pid = $DB->insert_record('user_info_field', [
            'shortname'  => $fieldname,
            'name'       => 'Test Field',
            'categoryid' => 1,
            'datatype'   => 'text'
        ]);

        // Check both are returned using normal options.
        if (moodle_major_version() < '2.7.1') {
            $fields = auth_iomadsaml2_profile_get_custom_fields();
        } else {
            $fields = profile_get_custom_fields();
        }
        $this->assertArrayHasKey($pid, $fields);
        $this->assertEquals($fieldname, $fields[$pid]->shortname);

        // Is the key the same?
        $customprofilefields = $auth->get_custom_user_profile_fields();
        $key = 'profile_field_' . $fields[$pid]->shortname;
        $this->assertTrue(in_array($key, $customprofilefields));

        // Function print_auth_lock_options creates variables in the config object.
        set_config("field_map_$key", $fieldname, 'auth_iomadsaml2');
        set_config("field_updatelocal_$key", 'onlogin', 'auth_iomadsaml2');
        set_config("field_lock_$key", 'locked', 'auth_iomadsaml2');

        $update = $auth->update_user_profile_fields($user, $attributes);
        $this->assertTrue($update);
    }

    /**
     * Dataprovider for the test_update_custom_user_profile_fields testcase
     *
     * @return array of testcases
     */
    public function provider_update_custom_user_profile_fields(): array {
        return [
            [['testfield' => ['Test data']]],
            [['secondfield' => ['A different string']]],
        ];
    }

    /**
     * Test test_missing_user_custom_profile_fields
     * The custom profile field does not exist, but IdP attribute data is mapped.
     *
     * @dataProvider provider_missing_user_custom_profile_fields
     * @param array $attributes
     */
    public function test_missing_user_custom_profile_fields($attributes): void {
        global $CFG;
        require_once($CFG->dirroot . '/user/profile/lib.php');

        $auth = get_auth_plugin('iomadsaml2');

        $user = $this->getDataGenerator()->create_user();

        $fieldname = key($attributes);

        if (moodle_major_version() < '2.7.1') {
            $fields = auth_iomadsaml2_profile_get_custom_fields();
        } else {
            $fields = profile_get_custom_fields();
        }

        $key = 'profile_field_' . $fieldname;
        $this->assertFalse(in_array($key, $fields));

        // Function print_auth_lock_options creates variables in the config object.
        set_config("field_map_$key", $fieldname, 'auth_iomadsaml2');
        set_config("field_updatelocal_$key", 'onlogin', 'auth_iomadsaml2');
        set_config("field_lock_$key", 'locked', 'auth_iomadsaml2');

        $update = $auth->update_user_profile_fields($user, $attributes);
        $this->assertTrue($update);
    }

    /**
     * Dataprovider for the test_missing_user_custom_profile_fields testcase
     *
     * @return array of testcases
     */
    public function provider_missing_user_custom_profile_fields(): array {
        return array(
            array(['missingfield' => array('Test data')]),
            array(['secondfield' => array('A different string')]),
        );
    }

    /**
     * Test test_invalid_map_user_profile_fields
     *
     * @dataProvider provider_invalid_map_user_profile_fields
     * @param array $mapping
     * @param array $attributes
     */
    public function test_invalid_map_user_profile_fields($mapping, $attributes): void {
        global $CFG;
        require_once($CFG->dirroot . '/user/profile/lib.php');

        $auth = get_auth_plugin('iomadsaml2');

        $user = $this->getDataGenerator()->create_user();

        $field = $mapping['field'];
        $map = $mapping['mapping'];

        // Function print_auth_lock_options creates variables in the config object.
        set_config("field_map_$field", $map, 'auth_iomadsaml2');
        set_config("field_updatelocal_$field", 'onlogin', 'auth_iomadsaml2');
        set_config("field_lock_$field", 'locked', 'auth_iomadsaml2');

        $updateprofile = $auth->update_user_profile_fields($user, $attributes);
        $this->assertFalse($updateprofile);
    }

    /**
     * Dataprovider for the test_invalid_map_user_profile_fields testcase
     *
     * @return array of testcases
     */
    public function provider_invalid_map_user_profile_fields(): array {
        return [
            [
                ['field' => 'userame', 'mapping' => 'invalid'],
                ['attributefield' => ['Test data']],
            ],
        ];
    }

    public function test_get_email_from_attributes(): void {
        $auth = get_auth_plugin('iomadsaml2');
        $this->assertFalse($auth->get_email_from_attributes([]));
        $this->assertFalse($auth->get_email_from_attributes(['email' => ['test@test.com']]));

        set_config('field_map_email', 'test', 'auth_iomadsaml2');
        $auth = get_auth_plugin('iomadsaml2');

        $this->assertFalse($auth->get_email_from_attributes(['email' => ['test@test.com']]));

        set_config('field_map_email', 'email', 'auth_iomadsaml2');
        $auth = get_auth_plugin('iomadsaml2');
        $this->assertEquals('test@test.com', $auth->get_email_from_attributes(['email' => ['test@test.com']]));

        set_config('field_map_email', 'email', 'auth_iomadsaml2');
        $auth = get_auth_plugin('iomadsaml2');
        $this->assertEquals('test@test.com', $auth->get_email_from_attributes(['email' => ['test@test.com', 'test2@test.com']]));
    }

    public function test_is_email_taken(): void {
        $auth = get_auth_plugin('iomadsaml2');
        $user = $this->getDataGenerator()->create_user();

        $this->assertFalse($auth->is_email_taken(''));
        $this->assertFalse($auth->is_email_taken('', $user->username));

        $this->assertTrue($auth->is_email_taken($user->email));
        $this->assertTrue($auth->is_email_taken(strtoupper($user->email)));
        $this->assertTrue($auth->is_email_taken(ucfirst($user->email)));
        $this->assertFalse($auth->is_email_taken($user->email, $user->username));
        $this->assertFalse($auth->is_email_taken(strtoupper($user->email), $user->username));
        $this->assertFalse($auth->is_email_taken(ucfirst($user->email), $user->username));
    }

    /**
     * Tests we can update username from any SAML attribute on user creation.
     */
    public function test_update_user_profile_fields_updates_username_on_creation(): void {
        global $CFG;
        require_once($CFG->dirroot . '/user/profile/lib.php');

        $auth = get_auth_plugin('iomadsaml2');
        $user = $this->getDataGenerator()->create_user();

        $expected = 'updated_username';
        $this->assertNotEquals($expected, $user->username);

        set_config("field_map_username", 'field', 'auth_iomadsaml2');
        set_config("field_updatelocal_username", 'onlogin', 'auth_iomadsaml2');
        set_config("field_lock_username", 'locked', 'auth_iomadsaml2');

        $attributes = [
            'field' => [$expected]
        ];

        $this->assertTrue($auth->update_user_profile_fields($user, $attributes, true));
        $this->assertEquals($expected, $user->username);
    }

    /**
     * Tests we can update username with invalid case from any SAML attribute on user creation.
     */
    public function test_update_user_profile_fields_updates_username_on_creation_case_insensitive(): void {
        global $CFG;
        require_once($CFG->dirroot . '/user/profile/lib.php');

        $auth = get_auth_plugin('iomadsaml2');
        $user = $this->getDataGenerator()->create_user();

        $expected = 'updated_username';
        $uppercaseusername = strtoupper($expected);
        $this->assertNotEquals($expected, $user->username);

        set_config("field_map_username", 'field', 'auth_iomadsaml2');
        set_config("field_updatelocal_username", 'onlogin', 'auth_iomadsaml2');
        set_config("field_lock_username", 'locked', 'auth_iomadsaml2');

        $attributes = [
            'field' => [$uppercaseusername]
        ];

        $this->assertTrue($auth->update_user_profile_fields($user, $attributes, true));
        $this->assertEquals($expected, $user->username);
    }

    /**
     * Tests we can't update username from any SAML attribute once a user already created.
     */
    public function test_update_user_profile_fields_does_not_update_username_on_update(): void {
        global $CFG;
        require_once($CFG->dirroot . '/user/profile/lib.php');

        $auth = get_auth_plugin('iomadsaml2');
        $user = $this->getDataGenerator()->create_user();

        $expected = 'updated_username';
        $this->assertNotEquals($expected, $user->username);

        // Function print_auth_lock_options creates variables in the config object.
        set_config("field_map_username", 'field', 'auth_iomadsaml2');
        set_config("field_updatelocal_username", 'onlogin', 'auth_iomadsaml2');
        set_config("field_lock_username", 'locked', 'auth_iomadsaml2');

        $attributes = [
            'field' => [$expected]
        ];

        $this->assertFalse($auth->update_user_profile_fields($user, $attributes, false));
        $this->assertNotEquals($expected, $user->username);
    }

    /**
     * Tests we can update configured mapping field from any SAML attribute on user creation.
     */
    public function test_update_user_profile_fields_updates_mapping_field_on_creation(): void {
        global $CFG;
        require_once($CFG->dirroot . '/user/profile/lib.php');

        $auth = get_auth_plugin('iomadsaml2');
        $user = $this->getDataGenerator()->create_user();

        $expected = 'updated_alternatename';
        $this->assertNotEquals($expected, $user->alternatename);

        set_config("mdlattr", 'alternatename', 'auth_iomadsaml2');
        set_config("field_map_alternatename", 'field', 'auth_iomadsaml2');
        set_config("field_updatelocal_alternatename", 'onlogin', 'auth_iomadsaml2');
        set_config("field_lock_alternatename", 'locked', 'auth_iomadsaml2');

        $attributes = [
            'field' => [$expected]
        ];

        $this->assertTrue($auth->update_user_profile_fields($user, $attributes, true));
        $this->assertEquals($expected, $user->alternatename);
    }

    /**
     * Tests we can't update configured mapping field from any SAML attribute when a user already created.
     */
    public function test_update_user_profile_fields_does_not_update_mapping_field_on_update(): void {
        global $CFG;
        require_once($CFG->dirroot . '/user/profile/lib.php');

        set_config("mdlattr", 'alternatename', 'auth_iomadsaml2');
        $auth = get_auth_plugin('iomadsaml2');

        $user = $this->getDataGenerator()->create_user();

        $expected = 'updated_alternatename';
        $this->assertNotEquals($expected, $user->alternatename);

        set_config("field_map_alternatename", 'field', 'auth_iomadsaml2');
        set_config("field_updatelocal_alternatename", 'onlogin', 'auth_iomadsaml2');
        set_config("field_lock_alternatename", 'locked', 'auth_iomadsaml2');

        $attributes = [
            'field' => [$expected]
        ];

        $this->assertFalse($auth->update_user_profile_fields($user, $attributes, false));
        $this->assertNotEquals($expected, $user->alternatename);
    }


    /**
     * Tests multi-value attributes can be saved to user profile fields.
     */
    public function test_update_user_profile_fields_multi(): void {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/user/profile/lib.php');

        $this->resetAfterTest();

        // Set up the initials.
        $DB->insert_record('user_info_field', ['shortname' => 'specialities', 'name' => 'Specialities', 'required' => 1,
                'visible' => 1, 'locked' => 0, 'categoryid' => 1, 'datatype' => 'text']);
        $user = $this->getDataGenerator()->create_user(['auth' => 'iomadsaml2']);
        $auth = get_auth_plugin($user->auth);

        // Map IdP provided attributes to user profile fields.
        set_config("mdlattr", 'alternatename', 'auth_iomadsaml2');
        set_config("field_map_alternatename", 'field', 'auth_iomadsaml2');
        set_config("field_updatelocal_alternatename", 'onlogin', 'auth_iomadsaml2');
        set_config("field_lock_alternatename", 'locked', 'auth_iomadsaml2');

        set_config("mdlattr", 'specialities', 'auth_iomadsaml2');
        set_config("field_map_specialities", 'specialities', 'auth_iomadsaml2');
        set_config("field_updatelocal_specialities", 'onlogin', 'auth_iomadsaml2');
        set_config("field_lock_specialities", 'locked', 'auth_iomadsaml2');

        // False payload from IdP.
        $attributes = [
                'field' => ['single_value'],
                'specialities' => ['running', 'jumping', 'knitting']
        ];

        // Assert all the things.
        $this->assertTrue($auth->update_user_profile_fields($user, $attributes, true));
        $this->assertEquals('single_value', $user->alternatename);
        $this->assertEquals('running,jumping,knitting', $user->specialities);

        // Set the delimiter to something nonstandard.
        set_config('fielddelimiter', '|', 'auth_iomadsaml2');
        $this->assertTrue($auth->update_user_profile_fields($user, $attributes, true));
        $this->assertEquals('single_value', $user->alternatename);
        $this->assertEquals('running|jumping|knitting', $user->specialities);
    }
}

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
 * SAML2 SP metadata tests.
 *
 * @package    auth_iomadsaml2
 * @copyright  Brendan Heywood <brendan@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use auth_iomadsaml2\admin\iomadsaml2_settings;
use auth_iomadsaml2\admin\setting_idpmetadata;

defined('MOODLE_INTERNAL') || die();
require_once(__DIR__ . '/../locallib.php');

/**
 * Tests for SAML
 *
 * @copyright  Brendan Heywood <brendan@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class auth_iomadsaml2_locallib_testcase extends advanced_testcase {
    /**
     * Regression test for Issue 132.
     */
    public function test_it_can_initialise_more_than_once() {
        global $CFG, $DB;
        $this->resetAfterTest(true);

        // Add a fake IdP.
        $DB->insert_record('auth_iomadsaml2_idps', array(
            'metadataurl' => 'http://www.example.com',
            'entityid'    => 'http://www.example.com',
            'name'        => 'Test IdP',
            'activeidp'   => 1));

        for ($i = 0; $i < 3; $i++) {
            require($CFG->dirroot . '/auth/iomadsaml2/setup.php');
            $xml = auth_iomadsaml2_get_sp_metadata();
            self::assertNotNull($xml);
            self::resetAllData(false);
        }
    }

    public function test_auth_iomadsaml2_sp_metadata() {
        global $CFG;

        $this->resetAfterTest();

        // Set just enough config to generate SP metadata.
        $email = 'test@test.com';
        $url = 'http://www.example.com';
        set_config('supportemail', $email);
        set_config('idpmetadata', $url, 'auth_iomadsaml2');
        set_config('idpentityids', json_encode([$url => $url]), 'auth_iomadsaml2');

        require($CFG->dirroot . '/auth/iomadsaml2/setup.php');

        $rawxml = auth_iomadsaml2_get_sp_metadata();

        $xml = new SimpleXMLElement($rawxml);
        $xml->registerXPathNamespace('md',   'urn:oasis:names:tc:SAML:2.0:metadata');
        $xml->registerXPathNamespace('mdui', 'urn:oasis:names:tc:SAML:metadata:ui');

        $contact = $xml->xpath('//md:EntityDescriptor/md:ContactPerson');
        $this->assertNotNull($contact);

    }

    /**
     * If locked do not generate the cert, if unlocked then generate the cert.
     */
    public function test_setup_no_cert_generate_if_locked() {
        $this->resetAfterTest();
        $auth = get_auth_plugin('iomadsaml2');
        set_config('certs_locked', 1, 'auth_iomadsaml2');

        // Make sure we have no files.
        $crt = file_exists($auth->certcrt);
        if ($crt) {
            unlink($auth->certcrt);
        }
        $this->assertFalse($crt);

        // Call setup.php and see that it doesn't generate a cert.
        require(dirname(__FILE__) . '/../setup.php');
        $this->assertDebuggingCalled();
        $crt = file_exists($auth->certcrt);
        $this->assertFalse($crt);

        // Set config unlocked.
        set_config('certs_locked', 0, 'auth_iomadsaml2');

        // Call setup.php and see that it generates the certificate.
        require(dirname(__FILE__) . '/../setup.php');
        $crt = file_exists($auth->certcrt);
        $this->assertTrue($crt);
    }

    /**
     * If locked and we try to generate certs, throw an exception and do not generate the certs.
     */
    public function test_create_certificates_if_locked() {
        $this->resetAfterTest();
        $auth = get_auth_plugin('iomadsaml2');
        set_config('certs_locked', 1, 'auth_iomadsaml2');

        // Call the create_certificates function directly to assert that
        // it throws an exception and does not generate a cert.
        try {
            create_certificates($auth);
            // Fail if the exception is not thrown.
            $this->fail();
        } catch (\iomadsaml2_exception $e) {
            $this->assertFalse(file_exists($auth->certcrt));
        }
    }
}

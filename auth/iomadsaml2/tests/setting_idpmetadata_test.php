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

use auth_iomadsaml2\admin\setting_idpmetadata;
use auth_iomadsaml2\idp_data;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../_autoload.php');

/**
 * Test setting idp Metadata.
 *
 * @package     auth_iomadsaml2
 * @author      Daniel Thee Roperto <daniel.roperto@catalyst-au.net>
 * @copyright   2018 Catalyst IT Australia {@link http://www.catalyst-au.net}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class setting_idpmetadata_test extends advanced_testcase {
    /** @var setting_idpmetadata */
    private static $config;

    protected function setUp(): void {
        parent::setUp();
        self::$config = new setting_idpmetadata();
    }

    /**
     * Get test metadata URL.
     *
     * @return string
     */
    private function get_test_metadata_url() {
        if (!defined('AUTH_SAML2_TEST_IDP_METADATA')) {
            $this->markTestSkipped();
        }
        return AUTH_SAML2_TEST_IDP_METADATA;
    }

    public function test_it_validates_the_xml() {
        $this->resetAfterTest();
        $xml = file_get_contents(__DIR__ . '/fixtures/metadata.xml');
        $data = self::$config->validate($xml);
        self::assertTrue($data);
    }

    public function test_it_saves_all_idp_information() {
        global $CFG;

        $this->resetAfterTest();

        $xml = file_get_contents(__DIR__ . '/fixtures/metadata.xml');
        self::$config->write_setting($xml);
        $actual = get_config('auth_iomadsaml2');

        self::assertSame($xml, $actual->idpmetadata, 'Invalid config metadata.');

        $metadataidps = auth_iomadsaml2_get_idps();
        foreach ($metadataidps as $metadataurl => $idps) {
            self::assertSame('xml', $metadataurl);

            foreach ($idps as $idp) {
                self::assertSame('https://idp.example.org/idp/shibboleth', $idp->entityid);
                self::assertSame('Example.com test IDP', $idp->name);
            }
        }

        $file = md5('xml') . '.idp.xml';
        $file = "{$CFG->dataroot}/iomadsaml2/{$file}";
        self::assertFileExists($file);
        $actual = file_get_contents($file);
        self::assertSame(trim($xml), $actual, "Invalid saved XML contents for: {$file}");
    }

    public function test_it_saves_all_idps_information_from_single_xml() {
        global $CFG;

        $this->resetAfterTest();

        $xml = file_get_contents(__DIR__ . '/fixtures/dualmetadata.xml');
        self::$config->write_setting($xml);
        $actual = get_config('auth_iomadsaml2');

        self::assertSame($xml, $actual->idpmetadata, 'Invalid config metadata.');

        $metadataidps = auth_iomadsaml2_get_idps();
        foreach ($metadataidps as $metadataurl => $idps) {
            self::assertSame('xml', $metadataurl);

            $idp1md5 = md5('https://idp1.example.org/idp/shibboleth');
            $idp2md5 = md5('https://idp2.example.org/idp/shibboleth');

            self::assertTrue(array_key_exists($idp1md5, $idps));
            self::assertTrue(array_key_exists($idp2md5, $idps));

            self::assertSame('First Test IDP', $idps[$idp1md5]->name);
            self::assertSame('Second Test IDP', $idps[$idp2md5]->name);
        }

        $file = md5("xml") . '.idp.xml';
        $file = "{$CFG->dataroot}/iomadsaml2/{$file}";
        self::assertFileExists($file);
        $actual = file_get_contents($file);
        self::assertSame(trim($xml), $actual, "Invalid saved XML contents for: {$file}");
    }

    public function test_it_allows_empty_values() {
        self::assertTrue(self::$config->validate(''), 'Validate empty string.');
        self::assertTrue(self::$config->validate('  '), ' Should trim spaces.');
        self::assertTrue(self::$config->validate("\n \n"), 'Should trim newlines.');
    }

    public function test_it_gets_idp_data_for_xml() {
        $xml = file_get_contents(__DIR__ . '/fixtures/metadata.xml');
        $data = self::$config->get_idps_data($xml);
        self::assertCount(1, $data);
        $this->validate_idp_data_array($data);
    }

    public function test_it_gets_idp_data_for_two_urls() {
        $url = $this->get_test_metadata_url();
        $url = "{$url}\n{$url}?second";
        $data = self::$config->get_idps_data($url);
        self::assertCount(2, $data);
        $this->validate_idp_data_array($data);
    }

    public function test_it_returns_error_if_metadata_url_is_not_valid() {
        $error = self::$config->validate('http://invalid.url.metadata.test');
        self::assertDebuggingCalled();
        if (method_exists($this, 'assertStringContainsString')) {
            self::assertStringContainsString('Invalid metadata', $error);
            self::assertStringContainsString('http://invalid.url.metadata.test', $error);
        } else {
            // Maintains Support for Moodle 3.5 - remove when this branch does not support Moodle 3.5 anymore.
            self::assertContains('Invalid metadata', $error);
            self::assertContains('http://invalid.url.metadata.test', $error);

        }
    }

    /**
     * Validate idp data array.
     *
     * @param idp_data[] $idps
     */
    private function validate_idp_data_array($idps) {
        foreach ($idps as $idp) {
            self::assertInstanceOf(idp_data::class, $idp);
            self::assertNotNull($idp->get_rawxml());
        }
    }

    /**
     * Cleanup after all tests are executed.
     *
     * @return void
     */
    public static function tearDownAfterClass(): void {  // @codingStandardsIgnoreLine - ignore case of function.
        parent::tearDownAfterClass();
        if (self::$config) {
            self::$config = null;
        }
        libxml_clear_errors();
    }
}

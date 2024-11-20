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

namespace tool_installaddon;

use testable_tool_installaddon_installer;
use tool_installaddon_installer;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once(__DIR__.'/fixtures/testable_installer.php');

/**
 * Unit tests for the {@link tool_installaddon_installer} class
 *
 * @package     tool_installaddon
 * @category    test
 * @copyright 2013 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class installer_test extends \advanced_testcase {

    public function test_get_addons_repository_url(): void {
        $installer = testable_tool_installaddon_installer::instance();
        $url = $installer->get_addons_repository_url();
        $query = parse_url($url, PHP_URL_QUERY);
        $this->assertEquals(1, preg_match('~^site=(.+)$~', $query, $matches));
        $site = rawurldecode($matches[1]);
        $site = json_decode(base64_decode($site), true);
        $this->assertIsArray($site);
        $this->assertEquals(3, count($site));
        $this->assertSame('Nasty site', $site['fullname']);
        $this->assertSame('file:///etc/passwd', $site['url']);
        $this->assertSame("2.5'; DROP TABLE mdl_user; --", $site['majorversion']);
    }

    public function test_decode_remote_request(): void {
        $installer = testable_tool_installaddon_installer::instance();

        $request = base64_encode(json_encode(array(
            'name' => '<h1>Stamp collection</h1>"; DELETE FROM mdl_users; --',
            'component' => 'mod_stampcoll',
            'version' => 2013032800,
        )));
        $request = $installer->testable_decode_remote_request($request);
        $this->assertTrue(is_object($request));
        // One, my little hobbit, never trusts the input parameters!
        $this->assertEquals('Stamp collection&quot;; DELETE FROM mdl_users; --', $request->name);
        $this->assertEquals('mod_stampcoll', $request->component);
        $this->assertEquals(2013032800, $request->version);

        $request = base64_encode(json_encode(array(
            'name' => 'Theme with invalid version number',
            'component' => 'theme_invalid',
            'version' => '1.0',
        )));
        $this->assertSame(false, $installer->testable_decode_remote_request($request));

        $request = base64_encode(json_encode(array(
            'name' => 'Invalid activity name',
            'component' => 'mod_invalid_activity',
            'version' => 2013032800,
        )));
        $this->assertSame(false, $installer->testable_decode_remote_request($request));

        $request = base64_encode(json_encode(array(
            'name' => 'Moodle 3.0',
            'component' => 'core',
            'version' => 2022010100,
        )));
        $this->assertSame(false, $installer->testable_decode_remote_request($request));

        $request = base64_encode(json_encode(array(
            'name' => 'Invalid core subsystem',
            'component' => 'core_cache',
            'version' => 2014123400,
        )));
        $this->assertSame(false, $installer->testable_decode_remote_request($request));

        $request = base64_encode(json_encode(array(
            'name' => 'Non-existing plugintype',
            'component' => 'david_mudrak',
            'version' => 2012123199,
        )));
        $this->assertSame(false, $installer->testable_decode_remote_request($request));

        $request = base64_encode(json_encode(array(
            'name' => 'Bogus module name',
            'component' => 'mod_xxx_yyy',
            'version' => 2012123190,
        )));
        $this->assertSame(false, $installer->testable_decode_remote_request($request));
    }

    public function test_detect_plugin_component(): void {
        global $CFG;

        $installer = tool_installaddon_installer::instance();

        $zipfile = $CFG->libdir.'/tests/fixtures/update_validator/zips/bar.zip';
        $this->assertEquals('foo_bar', $installer->detect_plugin_component($zipfile));

        $zipfile = $CFG->libdir.'/tests/fixtures/update_validator/zips/invalidroot.zip';
        $this->assertFalse($installer->detect_plugin_component($zipfile));
    }

    public function test_detect_plugin_component_from_versionphp(): void {
        global $CFG;

        $installer = testable_tool_installaddon_installer::instance();
        $fixtures = $CFG->libdir.'/tests/fixtures/update_validator/';

        $this->assertEquals('bar_bar_conan', $installer->testable_detect_plugin_component_from_versionphp('
$plugin->version  = 2014121300;
  $plugin->component=   "bar_bar_conan"  ; // Go Arnie go!'));

        $versionphp = file_get_contents($fixtures.'/github/moodle-repository_mahara-master/version.php');
        $this->assertEquals('repository_mahara', $installer->testable_detect_plugin_component_from_versionphp($versionphp));

        $versionphp = file_get_contents($fixtures.'/nocomponent/baz/version.php');
        $this->assertFalse($installer->testable_detect_plugin_component_from_versionphp($versionphp));
    }

    public function test_make_installfromzip_storage(): void {
        $installer = testable_tool_installaddon_installer::instance();

        // Check we get writable directory.
        $storage1 = $installer->make_installfromzip_storage();
        $this->assertTrue(is_dir($storage1));
        $this->assertTrue(is_writable($storage1));
        file_put_contents($storage1.'/hello.txt', 'Find me if you can!');

        // Check we get unique directory on each call.
        $storage2 = $installer->make_installfromzip_storage();
        $this->assertTrue(is_dir($storage2));
        $this->assertTrue(is_writable($storage2));
        $this->assertFalse(file_exists($storage2.'/hello.txt'));

        // Check both are in the same parent directory.
        $this->assertEquals(dirname($storage1), dirname($storage2));
    }
}

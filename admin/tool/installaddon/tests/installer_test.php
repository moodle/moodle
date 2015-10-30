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
 * Provides the unit tests class and some helper classes
 *
 * @package     tool_installaddon
 * @category    test
 * @copyright   2013 David Mudrak <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Unit tests for the {@link tool_installaddon_installer} class
 *
 * @copyright 2013 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_installaddon_installer_testcase extends advanced_testcase {

    public function test_get_addons_repository_url() {
        $installer = testable_tool_installaddon_installer::instance();
        $url = $installer->get_addons_repository_url();
        $query = parse_url($url, PHP_URL_QUERY);
        $this->assertEquals(1, preg_match('~^site=(.+)$~', $query, $matches));
        $site = rawurldecode($matches[1]);
        $site = json_decode(base64_decode($site), true);
        $this->assertInternalType('array', $site);
        $this->assertEquals(3, count($site));
        $this->assertSame('Nasty site', $site['fullname']);
        $this->assertSame('file:///etc/passwd', $site['url']);
        $this->assertSame("2.5'; DROP TABLE mdl_user; --", $site['majorversion']);
    }

    public function test_extract_installfromzip_file() {
        $jobid = md5(rand().uniqid('test_', true));
        $sourcedir = make_temp_directory('tool_installaddon/'.$jobid.'/source');
        $contentsdir = make_temp_directory('tool_installaddon/'.$jobid.'/contents');
        copy(dirname(__FILE__).'/fixtures/zips/invalidroot.zip', $sourcedir.'/testinvalidroot.zip');

        $installer = tool_installaddon_installer::instance();
        $files = $installer->extract_installfromzip_file($sourcedir.'/testinvalidroot.zip', $contentsdir, 'fixed_root');
        $this->assertInternalType('array', $files);
        $this->assertCount(4, $files);
        $this->assertSame(true, $files['fixed_root/']);
        $this->assertSame(true, $files['fixed_root/lang/']);
        $this->assertSame(true, $files['fixed_root/lang/en/']);
        $this->assertSame(true, $files['fixed_root/lang/en/fixed_root.php']);
        foreach ($files as $file => $status) {
            if (substr($file, -1) === '/') {
                $this->assertTrue(is_dir($contentsdir.'/'.$file));
            } else {
                $this->assertTrue(is_file($contentsdir.'/'.$file));
            }
        }
    }

    public function test_decode_remote_request() {
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

    public function test_move_directory() {
        $jobid = md5(rand().uniqid('test_', true));
        $jobroot = make_temp_directory('tool_installaddon/'.$jobid);
        $contentsdir = make_temp_directory('tool_installaddon/'.$jobid.'/contents/sub/folder');
        file_put_contents($contentsdir.'/readme.txt', 'Hello world!');

        $installer = tool_installaddon_installer::instance();
        $installer->move_directory($jobroot.'/contents', $jobroot.'/moved', 0777, 0666);

        $this->assertFalse(is_dir($jobroot.'/contents'));
        $this->assertTrue(is_file($jobroot.'/moved/sub/folder/readme.txt'));
        $this->assertSame('Hello world!', file_get_contents($jobroot.'/moved/sub/folder/readme.txt'));
    }
}


/**
 * Testable subclass of the tested class
 *
 * @copyright 2013 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class testable_tool_installaddon_installer extends tool_installaddon_installer {

    public function get_site_fullname() {
        return strip_tags('<h1 onmouseover="alert(\'Hello Moodle.org!\');">Nasty site</h1>');
    }

    public function get_site_url() {
        return 'file:///etc/passwd';
    }

    public function get_site_major_version() {
        return "2.5'; DROP TABLE mdl_user; --";
    }

    public function testable_decode_remote_request($request) {
        return parent::decode_remote_request($request);
    }

    protected function should_send_site_info() {
        return true;
    }
}

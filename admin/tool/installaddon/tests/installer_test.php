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

global $CFG;
require_once($CFG->dirroot.'/'.$CFG->admin.'/tool/installaddon/classes/installer.php');


/**
 * Unit tests for the {@link tool_installaddon_installer} class
 *
 * @copyright 2013 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_installaddon_installer_test extends advanced_testcase {

    public function test_get_addons_repository_url() {
        $installer = testable_tool_installaddon_installer::instance();
        $url = $installer->get_addons_repository_url();
        $query = parse_url($url, PHP_URL_QUERY);
        $this->assertEquals(1, preg_match('~^site=(.+)$~', $query, $matches));
        $site = rawurldecode($matches[1]);
        $site = json_decode(base64_decode($site), true);
        $this->assertEquals('array', gettype($site));
        $this->assertEquals(3, count($site));
        $this->assertSame($installer->get_site_fullname(), $site['fullname']);
        $this->assertSame($installer->get_site_url(), $site['url']);
        $this->assertSame($installer->get_site_major_version(), $site['major_version']);
    }

    public function test_extract_installfromzip_file() {
        $jobid = md5(rand().uniqid('test_', true));
        $sourcedir = make_temp_directory('tool_installaddon/'.$jobid.'/source');
        $contentsdir = make_temp_directory('tool_installaddon/'.$jobid.'/contents');
        copy(dirname(__FILE__).'/fixtures/zips/invalidroot.zip', $sourcedir.'/testinvalidroot.zip');

        $installer = tool_installaddon_installer::instance();
        $files = $installer->extract_installfromzip_file($sourcedir.'/testinvalidroot.zip', $contentsdir, 'fixed_root');
        $this->assertEquals('array', gettype($files));
        $this->assertEquals(4, count($files));
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
}


/**
 * Testable subclass of the tested class
 *
 * @copyright 2013 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class testable_tool_installaddon_installer extends tool_installaddon_installer {

    public function get_site_fullname() {
        return '<h1 onmouseover="alert(\'Hello Moodle.org!\');">Nasty site</h1>';
    }

    public function get_site_url() {
        return 'file:///etc/passwd';
    }

    public function get_site_major_version() {
        return "2.5'; DROP TABLE mdl_user; --";
    }

    protected function should_send_site_info() {
        return true;
    }
}

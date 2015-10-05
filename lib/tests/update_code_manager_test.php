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
 * Provides core_update_code_manager_testcase class.
 *
 * @package     core_plugin
 * @category    test
 * @copyright   2015 David Mudrak <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once(__DIR__.'/fixtures/testable_update_code_manager.php');

/**
 * Tests for \core\update\code_manager features.
 *
 * @copyright 2015 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_update_code_manager_testcase extends advanced_testcase {

    public function test_get_remote_plugin_zip() {
        $codeman = new \core\update\testable_code_manager();

        $this->assertFalse($codeman->get_remote_plugin_zip('ftp://not.support.ed/', 'doesnotmatter'));
        $this->assertDebuggingCalled('Error fetching plugin ZIP: unsupported transport protocol: ftp://not.support.ed/');

        $this->assertEquals(0, $codeman->downloadscounter);
        $this->assertFalse($codeman->get_remote_plugin_zip('http://first/', ''));
        $this->assertDebuggingCalled('Error fetching plugin ZIP: md5 mismatch.');
        $this->assertEquals(1, $codeman->downloadscounter);
        $this->assertNotFalse($codeman->get_remote_plugin_zip('http://first/', md5('http://first/')));
        $this->assertEquals(2, $codeman->downloadscounter);
        $this->assertNotFalse($codeman->get_remote_plugin_zip('http://two/', md5('http://two/')));
        $this->assertEquals(3, $codeman->downloadscounter);
        $this->assertNotFalse($codeman->get_remote_plugin_zip('http://first/', md5('http://first/')));
        $this->assertEquals(3, $codeman->downloadscounter);
    }

    public function test_move_plugin_directory() {
        $codeman = new \core\update\testable_code_manager();

        $tmp = make_request_directory();
        $dir = make_writable_directory($tmp.'/mod/foo/lang/en');
        file_put_contents($dir.'/foo.txt', 'Hello world!');

        $codeman->move_plugin_directory($tmp.'/mod/foo', $tmp.'/mod/.foo.2015100200');

        $this->assertTrue(is_dir($tmp.'/mod'));
        $this->assertFalse(is_dir($tmp.'/mod/foo'));
        $this->assertTrue(is_file($tmp.'/mod/.foo.2015100200/lang/en/foo.txt'));
        $this->assertSame('Hello world!', file_get_contents($tmp.'/mod/.foo.2015100200/lang/en/foo.txt'));
    }

    /**
     * @expectedException moodle_exception
     */
    public function test_move_plugin_directory_invalid_target() {
        $codeman = new \core\update\testable_code_manager();
        $codeman->move_plugin_directory(make_request_directory(), 'this_is_not_valid_path');
    }

    /**
     * @expectedException moodle_exception
     */
    public function test_move_plugin_directory_nonwritable_target() {
        $codeman = new \core\update\testable_code_manager();
        // If this does not throw exception for you, please send me your IP address.
        $codeman->move_plugin_directory(make_request_directory(), '/');
    }

    /**
     * @expectedException moodle_exception
     */
    public function test_move_plugin_directory_existing_target() {
        $codeman = new \core\update\testable_code_manager();
        $dir1 = make_request_directory();
        $dir2 = make_request_directory();
        $codeman->move_plugin_directory($dir1, $dir2);
    }

    /**
     * @expectedException moodle_exception
     */
    public function test_move_plugin_directory_nonexisting_source() {
        $codeman = new \core\update\testable_code_manager();
        $codeman->move_plugin_directory(make_request_directory().'/source', make_request_directory().'/target');
    }

    public function test_unzip_plugin_file() {
        $codeman = new \core\update\testable_code_manager();
        $zipfilepath = __DIR__.'/fixtures/update_validator/zips/invalidroot.zip';
        $targetdir = make_request_directory();

        $files = $codeman->unzip_plugin_file($zipfilepath, $targetdir);

        $this->assertInternalType('array', $files);
        $this->assertCount(4, $files);
        $this->assertSame(true, $files['invalid-root/']);
        $this->assertSame(true, $files['invalid-root/lang/']);
        $this->assertSame(true, $files['invalid-root/lang/en/']);
        $this->assertSame(true, $files['invalid-root/lang/en/fixed_root.php']);
        foreach ($files as $file => $status) {
            if (substr($file, -1) === '/') {
                $this->assertTrue(is_dir($targetdir.'/'.$file));
            } else {
                $this->assertTrue(is_file($targetdir.'/'.$file));
            }
        }

        $files = $codeman->unzip_plugin_file($zipfilepath, $targetdir, 'fixed_root');

        $this->assertInternalType('array', $files);
        $this->assertCount(4, $files);
        $this->assertSame(true, $files['fixed_root/']);
        $this->assertSame(true, $files['fixed_root/lang/']);
        $this->assertSame(true, $files['fixed_root/lang/en/']);
        $this->assertSame(true, $files['fixed_root/lang/en/fixed_root.php']);
        foreach ($files as $file => $status) {
            if (substr($file, -1) === '/') {
                $this->assertTrue(is_dir($targetdir.'/'.$file));
            } else {
                $this->assertTrue(is_file($targetdir.'/'.$file));
            }
        }

        $zipfilepath = __DIR__.'/fixtures/update_validator/zips/bar.zip';
        $files = $codeman->unzip_plugin_file($zipfilepath, $targetdir, 'bar');
    }
}

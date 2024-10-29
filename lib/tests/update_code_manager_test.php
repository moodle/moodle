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

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once(__DIR__.'/fixtures/testable_update_code_manager.php');

/**
 * Tests for \core\update\code_manager features.
 *
 * @package   core_plugin
 * @category  test
 * @copyright 2015 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class update_code_manager_test extends \advanced_testcase {

    public function test_get_remote_plugin_zip(): void {
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

    public function test_get_remote_plugin_zip_corrupted_cache(): void {

        $temproot = make_request_directory();
        $codeman = new \core\update\testable_code_manager(null, $temproot);

        file_put_contents($temproot.'/distfiles/'.md5('http://valid/').'.zip', 'http://invalid/');

        // Even if the cache file is already there, its name does not match its
        // actual content. It must be removed and re-downaloaded.
        $returned = $codeman->get_remote_plugin_zip('http://valid/', md5('http://valid/'));

        $this->assertEquals(basename($returned), md5('http://valid/').'.zip');
        $this->assertEquals(file_get_contents($returned), 'http://valid/');
    }

    public function test_unzip_plugin_file(): void {
        $codeman = new \core\update\testable_code_manager();
        $zipfilepath = self::get_fixture_path(__NAMESPACE__, 'update_validator/zips/invalidroot.zip');
        $targetdir = make_request_directory();
        mkdir($targetdir.'/aaa_another');

        $files = $codeman->unzip_plugin_file($zipfilepath, $targetdir);

        $this->assertIsArray($files);
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

        $this->assertIsArray($files);
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

        $zipfilepath = self::get_fixture_path(__NAMESPACE__, 'update_validator/zips/bar.zip');
        $files = $codeman->unzip_plugin_file($zipfilepath, $targetdir, 'bar');
    }

    public function test_unzip_plugin_file_multidir(): void {
        $codeman = new \core\update\testable_code_manager();
        $zipfilepath = self::get_fixture_path(__NAMESPACE__, 'update_validator/zips/multidir.zip');
        $targetdir = make_request_directory();
        // Attempting to rename the root folder if there are multiple ones should lead to exception.
        $this->expectException(\moodle_exception::class);
        $files = $codeman->unzip_plugin_file($zipfilepath, $targetdir, 'foo');
    }

    public function test_get_plugin_zip_root_dir(): void {
        $codeman = new \core\update\testable_code_manager();

        $zipfilepath = self::get_fixture_path(__NAMESPACE__, 'update_validator/zips/invalidroot.zip');
        $this->assertEquals('invalid-root', $codeman->get_plugin_zip_root_dir($zipfilepath));

        $zipfilepath = self::get_fixture_path(__NAMESPACE__, 'update_validator/zips/bar.zip');
        $this->assertEquals('bar', $codeman->get_plugin_zip_root_dir($zipfilepath));

        $zipfilepath = self::get_fixture_path(__NAMESPACE__, 'update_validator/zips/multidir.zip');
        $this->assertSame(false, $codeman->get_plugin_zip_root_dir($zipfilepath));
    }

    public function test_list_plugin_folder_files(): void {
        $fixtures = self::get_fixture_path(__NAMESPACE__, 'update_validator/plugindir');
        $codeman = new \core\update\testable_code_manager();
        $files = $codeman->list_plugin_folder_files($fixtures.'/foobar');
        $this->assertIsArray($files);
        $this->assertEquals(6, count($files));
        $fixtures = str_replace(DIRECTORY_SEPARATOR, '/', $fixtures);
        $this->assertEquals($files['foobar/'], $fixtures.'/foobar');
        $this->assertEquals($files['foobar/lang/en/local_foobar.php'], $fixtures.'/foobar/lang/en/local_foobar.php');
    }

    public function test_zip_plugin_folder(): void {
        $fixtures = self::get_fixture_path(__NAMESPACE__, 'update_validator/plugindir');
        $storage = make_request_directory();
        $codeman = new \core\update\testable_code_manager();
        $codeman->zip_plugin_folder($fixtures.'/foobar', $storage.'/foobar.zip');
        $this->assertTrue(file_exists($storage.'/foobar.zip'));

        $fp = get_file_packer('application/zip');
        $zipfiles = $fp->list_files($storage.'/foobar.zip');
        $this->assertNotEmpty($zipfiles);
        foreach ($zipfiles as $zipfile) {
            if ($zipfile->is_directory) {
                $this->assertTrue(is_dir($fixtures.'/'.$zipfile->pathname));
            } else {
                $this->assertTrue(file_exists($fixtures.'/'.$zipfile->pathname));
            }
        }
    }

    public function test_archiving_plugin_version(): void {
        $fixtures = self::get_fixture_path(__NAMESPACE__, 'update_validator/plugindir');
        $codeman = new \core\update\testable_code_manager();

        $this->assertFalse($codeman->archive_plugin_version($fixtures.'/foobar', 'local_foobar', 0));
        $this->assertFalse($codeman->archive_plugin_version($fixtures.'/foobar', 'local_foobar', null));
        $this->assertFalse($codeman->archive_plugin_version($fixtures.'/foobar', '', 2015100900));
        $this->assertFalse($codeman->archive_plugin_version($fixtures.'/foobar-does-not-exist', 'local_foobar', 2013031900));

        $this->assertFalse($codeman->get_archived_plugin_version('local_foobar', 2013031900));
        $this->assertFalse($codeman->get_archived_plugin_version('mod_foobar', 2013031900));

        $this->assertTrue($codeman->archive_plugin_version($fixtures.'/foobar', 'local_foobar', 2013031900, true));

        $this->assertNotFalse($codeman->get_archived_plugin_version('local_foobar', 2013031900));
        $this->assertTrue(file_exists($codeman->get_archived_plugin_version('local_foobar', 2013031900)));
        $this->assertTrue(file_exists($codeman->get_archived_plugin_version('local_foobar', '2013031900')));

        $this->assertFalse($codeman->get_archived_plugin_version('mod_foobar', 2013031900));
        $this->assertFalse($codeman->get_archived_plugin_version('local_foobar', 2013031901));
        $this->assertFalse($codeman->get_archived_plugin_version('', 2013031901));
        $this->assertFalse($codeman->get_archived_plugin_version('local_foobar', ''));

        $this->assertTrue($codeman->archive_plugin_version($fixtures.'/foobar', 'local_foobar', '2013031900'));
        $this->assertTrue(file_exists($codeman->get_archived_plugin_version('local_foobar', 2013031900)));

    }
}

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

use file_progress;
use tgz_packer;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/filestorage/file_progress.php');

/**
 * Unit tests for /lib/filestorage/tgz_packer.php and tgz_extractor.php.
 *
 * @package core
 * @copyright 2013 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tgz_packer_test extends \advanced_testcase implements file_progress {
    /**
     * @var array Progress information passed to the progress reporter
     */
    protected $progress;

    /**
     * Puts contents with specified time.
     *
     * @param string $path File path
     * @param string $contents Contents of file
     * @param int $mtime Time modified
     */
    protected static function file_put_contents_at_time($path, $contents, $mtime) {
        file_put_contents($path, $contents);
        touch($path, $mtime);
    }

    /**
     * Set up some files to be archived.
     *
     * @return array Array listing files of all types
     */
    protected function prepare_file_list() {
        global $CFG;
        $this->resetAfterTest(true);

        // Make array listing files to archive.
        $filelist = array();

        // Normal file.
        self::file_put_contents_at_time($CFG->tempdir . '/file1.txt', 'File 1', 1377993601);
        $filelist['out1.txt'] = $CFG->tempdir . '/file1.txt';

        // Recursive directory w/ file and directory with file.
        check_dir_exists($CFG->tempdir . '/dir1/dir2');
        self::file_put_contents_at_time($CFG->tempdir . '/dir1/file2.txt', 'File 2', 1377993602);
        self::file_put_contents_at_time($CFG->tempdir . '/dir1/dir2/file3.txt', 'File 3', 1377993603);
        $filelist['out2'] = $CFG->tempdir . '/dir1';

        // Moodle stored_file.
        $context = \context_system::instance();
        $filerecord = array('contextid' => $context->id, 'component' => 'phpunit',
                'filearea' => 'data', 'itemid' => 0, 'filepath' => '/',
                'filename' => 'file4.txt', 'timemodified' => 1377993604);
        $fs = get_file_storage();
        $sf = $fs->create_file_from_string($filerecord, 'File 4');
        $filelist['out3.txt'] = $sf;

         // Moodle stored_file directory.
        $filerecord['itemid'] = 1;
        $filerecord['filepath'] = '/dir1/';
        $filerecord['filename'] = 'file5.txt';
        $filerecord['timemodified'] = 1377993605;
        $fs->create_file_from_string($filerecord, 'File 5');
        $filerecord['filepath'] = '/dir1/dir2/';
        $filerecord['filename'] = 'file6.txt';
        $filerecord['timemodified'] = 1377993606;
        $fs->create_file_from_string($filerecord, 'File 6');
        $filerecord['filepath'] = '/';
        $filerecord['filename'] = 'excluded.txt';
        $fs->create_file_from_string($filerecord, 'Excluded');
        $filelist['out4'] = $fs->get_file($context->id, 'phpunit', 'data', 1, '/dir1/', '.');

        // File stored as raw content.
        $filelist['out5.txt'] = array('File 7');

        // File where there's just an empty directory.
        $filelist['out6'] = null;

        return $filelist;
    }

    /**
     * Tests getting the item.
     */
    public function test_get_packer(): void {
        $packer = get_file_packer('application/x-gzip');
        $this->assertInstanceOf('tgz_packer', $packer);
    }

    /**
     * Tests basic archive and extract to file paths.
     */
    public function test_to_normal_files(): void {
        global $CFG;
        $packer = get_file_packer('application/x-gzip');

        // Archive files.
        $files = $this->prepare_file_list();
        $archivefile = $CFG->tempdir . '/test.tar.gz';
        $packer->archive_to_pathname($files, $archivefile);

        // Extract same files.
        $outdir = $CFG->tempdir . '/out';
        check_dir_exists($outdir);
        $result = $packer->extract_to_pathname($archivefile, $outdir);

        // The result array should have file entries + directory entries for
        // all implicit directories + entry for the explicit directory.
        $expectedpaths = array('out1.txt', 'out2/', 'out2/dir2/', 'out2/dir2/file3.txt',
                'out2/file2.txt', 'out3.txt', 'out4/', 'out4/dir2/', 'out4/file5.txt',
                'out4/dir2/file6.txt', 'out5.txt', 'out6/');
        sort($expectedpaths);
        $actualpaths = array_keys($result);
        sort($actualpaths);
        $this->assertEquals($expectedpaths, $actualpaths);
        foreach ($result as $path => $booleantrue) {
            $this->assertTrue($booleantrue);
        }

        // Check the files are as expected.
        $this->assertEquals('File 1', file_get_contents($outdir . '/out1.txt'));
        $this->assertEquals('File 2', file_get_contents($outdir . '/out2/file2.txt'));
        $this->assertEquals('File 3', file_get_contents($outdir . '/out2/dir2/file3.txt'));
        $this->assertEquals('File 4', file_get_contents($outdir . '/out3.txt'));
        $this->assertEquals('File 5', file_get_contents($outdir . '/out4/file5.txt'));
        $this->assertEquals('File 6', file_get_contents($outdir . '/out4/dir2/file6.txt'));
        $this->assertEquals('File 7', file_get_contents($outdir . '/out5.txt'));
        $this->assertTrue(is_dir($outdir . '/out6'));
    }

    /**
     * Tests archive and extract to Moodle file system.
     */
    public function test_to_stored_files(): void {
        global $CFG;
        $packer = get_file_packer('application/x-gzip');

        // Archive files.
        $files = $this->prepare_file_list();
        $archivefile = $CFG->tempdir . '/test.tar.gz';
        $context = \context_system::instance();
        $sf = $packer->archive_to_storage($files,
                $context->id, 'phpunit', 'archive', 1, '/', 'archive.tar.gz');
        $this->assertInstanceOf('stored_file', $sf);

        // Extract (from storage) to disk.
        $outdir = $CFG->tempdir . '/out';
        check_dir_exists($outdir);
        $packer->extract_to_pathname($sf, $outdir);

        // Check the files are as expected.
        $this->assertEquals('File 1', file_get_contents($outdir . '/out1.txt'));
        $this->assertEquals('File 2', file_get_contents($outdir . '/out2/file2.txt'));
        $this->assertEquals('File 3', file_get_contents($outdir . '/out2/dir2/file3.txt'));
        $this->assertEquals('File 4', file_get_contents($outdir . '/out3.txt'));
        $this->assertEquals('File 5', file_get_contents($outdir . '/out4/file5.txt'));
        $this->assertEquals('File 6', file_get_contents($outdir . '/out4/dir2/file6.txt'));
        $this->assertEquals('File 7', file_get_contents($outdir . '/out5.txt'));
        $this->assertTrue(is_dir($outdir . '/out6'));

        // Extract to Moodle storage.
        $packer->extract_to_storage($sf, $context->id, 'phpunit', 'data', 2, '/out/');
        $fs = get_file_storage();
        $out = $fs->get_file($context->id, 'phpunit', 'data', 2, '/out/', 'out1.txt');
        $this->assertNotEmpty($out);
        $this->assertEquals('File 1', $out->get_content());
        $out = $fs->get_file($context->id, 'phpunit', 'data', 2, '/out/out2/', 'file2.txt');
        $this->assertNotEmpty($out);
        $this->assertEquals('File 2', $out->get_content());
        $out = $fs->get_file($context->id, 'phpunit', 'data', 2, '/out/out2/dir2/', 'file3.txt');
        $this->assertNotEmpty($out);
        $this->assertEquals('File 3', $out->get_content());
        $out = $fs->get_file($context->id, 'phpunit', 'data', 2, '/out/', 'out3.txt');
        $this->assertNotEmpty($out);
        $this->assertEquals('File 4', $out->get_content());
        $out = $fs->get_file($context->id, 'phpunit', 'data', 2, '/out/out4/', 'file5.txt');
        $this->assertNotEmpty($out);
        $this->assertEquals('File 5', $out->get_content());
        $out = $fs->get_file($context->id, 'phpunit', 'data', 2, '/out/out4/dir2/', 'file6.txt');
        $this->assertNotEmpty($out);
        $this->assertEquals('File 6', $out->get_content());
        $out = $fs->get_file($context->id, 'phpunit', 'data', 2, '/out/', 'out5.txt');
        $this->assertNotEmpty($out);
        $this->assertEquals('File 7', $out->get_content());
        $out = $fs->get_file($context->id, 'phpunit', 'data', 2, '/out/out6/', '.');
        $this->assertNotEmpty($out);
        $this->assertTrue($out->is_directory());

        // These functions are supposed to overwrite existing files; test they
        // don't give errors when run twice.
        $sf = $packer->archive_to_storage($files,
                $context->id, 'phpunit', 'archive', 1, '/', 'archive.tar.gz');
        $this->assertInstanceOf('stored_file', $sf);
        $packer->extract_to_storage($sf, $context->id, 'phpunit', 'data', 2, '/out/');
    }

    /**
     * Tests extracting with a list of specified files.
     */
    public function test_only_specified_files(): void {
        global $CFG;
        $packer = get_file_packer('application/x-gzip');

        // Archive files.
        $files = $this->prepare_file_list();
        $archivefile = $CFG->tempdir . '/test.tar.gz';
        $packer->archive_to_pathname($files, $archivefile);

        // Extract same files.
        $outdir = $CFG->tempdir . '/out';
        check_dir_exists($outdir);
        $result = $packer->extract_to_pathname($archivefile, $outdir,
                array('out3.txt', 'out6/', 'out4/file5.txt'));

        // Check result reporting only includes specified files.
        $expectedpaths = array('out3.txt', 'out4/file5.txt', 'out6/');
        sort($expectedpaths);
        $actualpaths = array_keys($result);
        sort($actualpaths);
        $this->assertEquals($expectedpaths, $actualpaths);

        // Check the files are as expected.
        $this->assertFalse(file_exists($outdir . '/out1.txt'));
        $this->assertEquals('File 4', file_get_contents($outdir . '/out3.txt'));
        $this->assertEquals('File 5', file_get_contents($outdir . '/out4/file5.txt'));
        $this->assertTrue(is_dir($outdir . '/out6'));
    }

    /**
     * Tests extracting files returning only a boolean state with success.
     */
    public function test_extract_to_pathname_returnvalue_successful(): void {
        $packer = get_file_packer('application/x-gzip');

        // Prepare files.
        $files = $this->prepare_file_list();
        $archivefile = make_request_directory() . '/test.tgz';
        $packer->archive_to_pathname($files, $archivefile);

        // Extract same files.
        $outdir = make_request_directory();
        $result = $packer->extract_to_pathname($archivefile, $outdir, null, null, true);

        $this->assertTrue($result);
    }

    /**
     * Tests extracting files returning only a boolean state with failure.
     */
    public function test_extract_to_pathname_returnvalue_failure(): void {
        $packer = get_file_packer('application/x-gzip');

        // Create sample files.
        $archivefile = make_request_directory() . '/test.tgz';
        file_put_contents($archivefile, '');

        // Extract same files.
        $outdir = make_request_directory();

        $result = $packer->extract_to_pathname($archivefile, $outdir, null, null, true);

        $this->assertFalse($result);
    }

    /**
     * Tests the progress reporting.
     */
    public function test_file_progress(): void {
        global $CFG;

        // Set up.
        $filelist = $this->prepare_file_list();
        $packer = get_file_packer('application/x-gzip');
        $archive = "$CFG->tempdir/archive.tgz";
        $context = \context_system::instance();

        // Archive to pathname.
        $this->progress = array();
        $result = $packer->archive_to_pathname($filelist, $archive, true, $this);
        $this->assertTrue($result);
        // Should send progress at least once per file.
        $this->assertTrue(count($this->progress) >= count($filelist));
        // Progress should obey some restrictions.
        $this->check_progress_toward_max();

        // Archive to storage.
        $this->progress = array();
        $archivefile = $packer->archive_to_storage($filelist, $context->id,
                'phpunit', 'test', 0, '/', 'archive.tgz', null, true, $this);
        $this->assertInstanceOf('stored_file', $archivefile);
        $this->assertTrue(count($this->progress) >= count($filelist));
        $this->check_progress_toward_max();

        // Extract to pathname.
        $this->progress = array();
        $target = "$CFG->tempdir/test/";
        check_dir_exists($target);
        $result = $packer->extract_to_pathname($archive, $target, null, $this);
        remove_dir($target);
        // We only output progress once per block, and this is kind of a small file.
        $this->assertTrue(count($this->progress) >= 1);
        $this->check_progress_toward_max();

        // Extract to storage (from storage).
        $this->progress = array();
        $result = $packer->extract_to_storage($archivefile, $context->id,
                'phpunit', 'target', 0, '/', null, $this);
        $this->assertTrue(count($this->progress) >= 1);
        $this->check_progress_toward_max();

        // Extract to storage (from path).
        $this->progress = array();
        $result = $packer->extract_to_storage($archive, $context->id,
                'phpunit', 'target', 0, '/', null, $this);
        $this->assertTrue(count($this->progress) >= 1);
        $this->check_progress_toward_max();

        // Wipe created disk file.
        unlink($archive);
    }

    /**
     * Tests the list_files function with and without an index file.
     */
    public function test_list_files(): void {
        global $CFG;

        // Set up.
        $filelist = $this->prepare_file_list();
        $packer = get_file_packer('application/x-gzip');
        $archive = "$CFG->tempdir/archive.tgz";

        // Archive with an index (default).
        $packer = get_file_packer('application/x-gzip');
        $result = $packer->archive_to_pathname($filelist, $archive, true, $this);
        $this->assertTrue($result);
        $hashwith = \file_storage::hash_from_path($archive);

        // List files.
        $files = $packer->list_files($archive);

        // Check they match expected.
        $expectedinfo = array(
            array('out1.txt', 1377993601, false, 6),
            array('out2/', tgz_packer::DEFAULT_TIMESTAMP, true, 0),
            array('out2/dir2/', tgz_packer::DEFAULT_TIMESTAMP, true, 0),
            array('out2/dir2/file3.txt', 1377993603, false, 6),
            array('out2/file2.txt', 1377993602, false, 6),
            array('out3.txt', 1377993604, false, 6),
            array('out4/', tgz_packer::DEFAULT_TIMESTAMP, true, 0),
            array('out4/dir2/', tgz_packer::DEFAULT_TIMESTAMP, true, 0),
            array('out4/dir2/file6.txt', 1377993606, false, 6),
            array('out4/file5.txt', 1377993605, false, 6),
            array('out5.txt', tgz_packer::DEFAULT_TIMESTAMP, false, 6),
            array('out6/', tgz_packer::DEFAULT_TIMESTAMP, true, 0),
        );
        $this->assertEquals($expectedinfo, self::convert_info_for_assert($files));

        // Archive with no index. Should have same result.
        $this->progress = array();
        $packer->set_include_index(false);
        $result = $packer->archive_to_pathname($filelist, $archive, true, $this);
        $this->assertTrue($result);
        $hashwithout = \file_storage::hash_from_path($archive);
        $files = $packer->list_files($archive);
        $this->assertEquals($expectedinfo, self::convert_info_for_assert($files));

        // Check it actually is different (does have index in)!
        $this->assertNotEquals($hashwith, $hashwithout);

        // Put the index back on in case of future tests.
        $packer->set_include_index(true);
    }

    /**
     * Utility function to convert the file info array into a simpler format
     * for making comparisons.
     *
     * @param array $files Array from list_files result
     */
    protected static function convert_info_for_assert(array $files) {
        $actualinfo = array();
        foreach ($files as $file) {
            $actualinfo[] = array($file->pathname, $file->mtime, $file->is_directory, $file->size);
        }
        usort($actualinfo, function($a, $b) {
            return strcmp($a[0], $b[0]);
        });
        return $actualinfo;
    }

    public function test_is_tgz_file(): void {
        global $CFG;

        // Set up.
        $filelist = $this->prepare_file_list();
        $packer1 = get_file_packer('application/x-gzip');
        $packer2 = get_file_packer('application/zip');
        $archive2 = "$CFG->tempdir/archive.zip";

        // Archive in tgz and zip format.
        $context = \context_system::instance();
        $archive1 = $packer1->archive_to_storage($filelist, $context->id,
                'phpunit', 'test', 0, '/', 'archive.tgz', null, true, $this);
        $this->assertInstanceOf('stored_file', $archive1);
        $result = $packer2->archive_to_pathname($filelist, $archive2);
        $this->assertTrue($result);

        // Use is_tgz_file to detect which is which. First check is from storage,
        // second check is from filesystem.
        $this->assertTrue(tgz_packer::is_tgz_file($archive1));
        $this->assertFalse(tgz_packer::is_tgz_file($archive2));
    }

    /**
     * Checks that progress reported is numeric rather than indeterminate,
     * and follows the progress reporting rules.
     */
    protected function check_progress_toward_max() {
        $lastvalue = -1; $lastmax = -1;
        foreach ($this->progress as $progressitem) {
            list($value, $max) = $progressitem;
            if ($lastmax != -1) {
                $this->assertEquals($max, $lastmax);
            } else {
                $lastmax = $max;
            }
            $this->assertTrue(is_integer($value));
            $this->assertTrue(is_integer($max));
            $this->assertNotEquals(file_progress::INDETERMINATE, $max);
            $this->assertTrue($value <= $max);
            $this->assertTrue($value >= $lastvalue);
            $lastvalue = $value;
        }
    }

    /**
     * Handles file_progress interface.
     *
     * @param int $progress
     * @param int $max
     */
    public function progress($progress = file_progress::INDETERMINATE, $max = file_progress::INDETERMINATE) {
        $this->progress[] = array($progress, $max);
    }
}

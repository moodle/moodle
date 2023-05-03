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

use file_archive;
use file_progress;
use zip_archive;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/filestorage/file_progress.php');

/**
 * Unit tests for /lib/filestorage/zip_packer.php and zip_archive.php
 *
 * @package   core
 * @category  test
 * @copyright 2012 Petr Skoda
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class zip_packer_test extends \advanced_testcase implements file_progress {
    protected $testfile;
    protected $files;

    /**
     * @var array Progress information passed to the progress reporter
     */
    protected $progress;

    protected function setUp(): void {
        parent::setUp();

        $this->testfile = __DIR__.'/fixtures/test.txt';

        $fs = get_file_storage();
        $context = \context_system::instance();
        if (!$file = $fs->get_file($context->id, 'phpunit', 'data', 0, '/', 'test.txt')) {
            $file = $fs->create_file_from_pathname(
                array('contextid'=>$context->id, 'component'=>'phpunit', 'filearea'=>'data', 'itemid'=>0, 'filepath'=>'/', 'filename'=>'test.txt'),
                $this->testfile);
        }

        $this->files = array(
            'test.test' => $this->testfile,
            'testíček.txt' => $this->testfile,
            'Prüfung.txt' => $this->testfile,
            '测试.txt' => $this->testfile,
            '試験.txt' => $this->testfile,
            'Žluťoučký/Koníček.txt' => $file,
        );
    }

    public function test_get_packer() {
        $this->resetAfterTest(false);
        $packer = get_file_packer();
        $this->assertInstanceOf('zip_packer', $packer);

        $packer = get_file_packer('application/zip');
        $this->assertInstanceOf('zip_packer', $packer);
    }

    /**
     * @depends test_get_packer
     */
    public function test_list_files() {
        $this->resetAfterTest(false);

        $files = array(
            __DIR__.'/fixtures/test_moodle_22.zip',
            __DIR__.'/fixtures/test_moodle.zip',
            __DIR__.'/fixtures/test_tc_8.zip',
            __DIR__.'/fixtures/test_7zip_927.zip',
            __DIR__.'/fixtures/test_winzip_165.zip',
            __DIR__.'/fixtures/test_winrar_421.zip',
            __DIR__.'/fixtures/test_thumbsdb.zip',
        );

        if (function_exists('normalizer_normalize')) {
            // Unfortunately there is no way to standardise UTF-8 strings without INTL extension.
            $files[] = __DIR__.'/fixtures/test_infozip_3.zip';
            $files[] = __DIR__.'/fixtures/test_osx_1074.zip';
            $files[] = __DIR__.'/fixtures/test_osx_compress.zip';
        }

        $packer = get_file_packer('application/zip');

        foreach ($files as $archive) {
            $archivefiles = $packer->list_files($archive);
            $this->assertTrue(is_array($archivefiles), "Archive not extracted properly: ".basename($archive).' ');
            $this->assertTrue(count($this->files) === count($archivefiles) or count($this->files) === count($archivefiles) - 1); // Some zippers create empty dirs.
            foreach ($archivefiles as $file) {
                if ($file->pathname === 'Žluťoučký/') {
                    // Some zippers create empty dirs.
                    continue;
                }
                $this->assertArrayHasKey($file->pathname, $this->files, "File $file->pathname not extracted properly: ".basename($archive).' ');
            }
        }

        // Windows packer supports only DOS encoding.
        $archive = __DIR__.'/fixtures/test_win8_de.zip';
        $archivefiles = $packer->list_files($archive);
        $this->assertTrue(is_array($archivefiles), "Archive not extracted properly: ".basename($archive).' ');
        $this->assertEquals(2, count($archivefiles));
        foreach ($archivefiles as $file) {
            $this->assertTrue($file->pathname === 'Prüfung.txt' or $file->pathname === 'test.test');
        }

        $zip_archive = new zip_archive();
        $zip_archive->open(__DIR__.'/fixtures/test_win8_cz.zip', file_archive::OPEN, 'cp852');
        $archivefiles = $zip_archive->list_files();
        $this->assertTrue(is_array($archivefiles), "Archive not extracted properly: ".basename($archive).' ');
        $this->assertEquals(3, count($archivefiles));
        foreach ($archivefiles as $file) {
            $this->assertTrue($file->pathname === 'Žluťoučký/Koníček.txt' or $file->pathname === 'testíček.txt' or $file->pathname === 'test.test');
        }
        $zip_archive->close();

        // Empty archive extraction.
        $archive = __DIR__.'/fixtures/empty.zip';
        $archivefiles = $packer->list_files($archive);
        $this->assertSame(array(), $archivefiles);
    }

    /**
     * @depends test_list_files
     */
    public function test_archive_to_pathname() {
        global $CFG;

        $this->resetAfterTest(false);

        $packer = get_file_packer('application/zip');
        $archive = "$CFG->tempdir/archive.zip";

        $this->assertFileDoesNotExist($archive);
        $result = $packer->archive_to_pathname($this->files, $archive);
        $this->assertTrue($result);
        $this->assertFileExists($archive);

        $archivefiles = $packer->list_files($archive);
        $this->assertTrue(is_array($archivefiles));
        $this->assertEquals(count($this->files), count($archivefiles));
        foreach ($archivefiles as $file) {
            $this->assertArrayHasKey($file->pathname, $this->files);
        }

        // Test invalid files parameter.
        $archive = "$CFG->tempdir/archive2.zip";
        $this->assertFileDoesNotExist($archive);

        $this->assertFileDoesNotExist(__DIR__.'/xx/yy/ee.txt');
        $files = array('xtest.txt'=>__DIR__.'/xx/yy/ee.txt');

        $result = $packer->archive_to_pathname($files, $archive, false);
        $this->assertFalse($result);
        $this->assertDebuggingCalled();
        $this->assertFileDoesNotExist($archive);

        $result = $packer->archive_to_pathname($files, $archive);
        $this->assertTrue($result);
        $this->assertFileExists($archive);
        $this->assertDebuggingCalled();
        $archivefiles = $packer->list_files($archive);
        $this->assertSame(array(), $archivefiles);
        unlink($archive);

        $this->assertFileDoesNotExist(__DIR__.'/xx/yy/ee.txt');
        $this->assertFileExists(__DIR__.'/fixtures/test.txt');
        $files = array('xtest.txt'=>__DIR__.'/xx/yy/ee.txt', 'test.txt'=>__DIR__.'/fixtures/test.txt', 'ytest.txt'=>__DIR__.'/xx/yy/yy.txt');
        $result = $packer->archive_to_pathname($files, $archive);
        $this->assertTrue($result);
        $this->assertFileExists($archive);
        $archivefiles = $packer->list_files($archive);
        $this->assertCount(1, $archivefiles);
        $this->assertEquals('test.txt', $archivefiles[0]->pathname);
        $dms = $this->getDebuggingMessages();
        $this->assertCount(2, $dms);
        $this->resetDebugging();
        unlink($archive);
    }

    /**
     * @depends test_archive_to_pathname
     */
    public function test_archive_to_storage() {
        $this->resetAfterTest(false);

        $packer = get_file_packer('application/zip');
        $fs = get_file_storage();
        $context = \context_system::instance();

        $this->assertFalse($fs->file_exists($context->id, 'phpunit', 'test', 0, '/', 'archive.zip'));
        $result = $packer->archive_to_storage($this->files, $context->id, 'phpunit', 'test', 0, '/', 'archive.zip');
        $this->assertInstanceOf('stored_file', $result);
        $this->assertTrue($fs->file_exists($context->id, 'phpunit', 'test', 0, '/', 'archive.zip'));

        $archivefiles = $result->list_files($packer);
        $this->assertTrue(is_array($archivefiles));
        $this->assertEquals(count($this->files), count($archivefiles));
        foreach ($archivefiles as $file) {
            $this->assertArrayHasKey($file->pathname, $this->files);
        }
    }

    /**
     * @depends test_archive_to_storage
     */
    public function test_extract_to_pathname() {
        global $CFG;

        $this->resetAfterTest(false);

        $packer = get_file_packer('application/zip');
        $fs = get_file_storage();
        $context = \context_system::instance();

        $target = "$CFG->tempdir/test/";
        $testcontent = file_get_contents($this->testfile);

        @mkdir($target, $CFG->directorypermissions);
        $this->assertTrue(is_dir($target));

        $archive = "$CFG->tempdir/archive.zip";
        $this->assertFileExists($archive);
        $result = $packer->extract_to_pathname($archive, $target);
        $this->assertTrue(is_array($result));
        $this->assertEquals(count($this->files), count($result));
        foreach ($this->files as $file => $unused) {
            $this->assertTrue($result[$file]);
            $this->assertFileExists($target.$file);
            $this->assertSame($testcontent, file_get_contents($target.$file));
        }

        $archive = $fs->get_file($context->id, 'phpunit', 'test', 0, '/', 'archive.zip');
        $this->assertNotEmpty($archive);
        $result = $packer->extract_to_pathname($archive, $target);
        $this->assertTrue(is_array($result));
        $this->assertEquals(count($this->files), count($result));
        foreach ($this->files as $file => $unused) {
            $this->assertTrue($result[$file]);
            $this->assertFileExists($target.$file);
            $this->assertSame($testcontent, file_get_contents($target.$file));
        }
    }

    /**
     * Test functionality of {@see zip_packer} for entries with folders ending with dots.
     *
     * @link https://bugs.php.net/bug.php?id=77214
     */
    public function test_zip_entry_path_having_folder_ending_with_dot() {
        global $CFG;

        $this->resetAfterTest(false);

        $packer = get_file_packer('application/zip');
        $tmp = make_request_directory();
        $now = time();

        // Create a test archive containing a folder ending with dot.
        $zippath = $tmp . '/test_archive.zip';
        $zipcontents = [
            'HOW.TO' => ['Just run tests.'],
            'README.' => ['This is a test ZIP file'],
            './Current time' => [$now],
            'Data/sub1./sub2/1221' => ['1221'],
            'Data/sub1./sub2./Příliš žluťoučký kůň úpěl Ďábelské Ódy.txt' => [''],
        ];

        if ($CFG->ostype === 'WINDOWS') {
            // File names cannot end with dots on Windows and trailing dots are replaced with underscore.
            $filenamemap = [
                'HOW.TO' => 'HOW.TO',
                'README.' => 'README_',
                './Current time' => 'Current time',
                'Data/sub1./sub2/1221' => 'Data/sub1_/sub2/1221',
                'Data/sub1./sub2./Příliš žluťoučký kůň úpěl Ďábelské Ódy.txt' =>
                    'Data/sub1_/sub2_/Příliš žluťoučký kůň úpěl Ďábelské Ódy.txt',
            ];

        } else {
            $filenamemap = [
                'HOW.TO' => 'HOW.TO',
                'README.' => 'README.',
                './Current time' => 'Current time',
                'Data/sub1./sub2/1221' => 'Data/sub1./sub2/1221',
                'Data/sub1./sub2./Příliš žluťoučký kůň úpěl Ďábelské Ódy.txt' =>
                    'Data/sub1./sub2./Příliš žluťoučký kůň úpěl Ďábelské Ódy.txt',
            ];
        }

        // Check that the archive can be created.
        $result = $packer->archive_to_pathname($zipcontents, $zippath, false);
        $this->assertTrue($result);

        // Check list of files.
        $listfiles = $packer->list_files($zippath);
        $this->assertEquals(count($zipcontents), count($listfiles));

        foreach ($listfiles as $fileinfo) {
            $this->assertSame($fileinfo->pathname, $fileinfo->original_pathname);
            $this->assertArrayHasKey($fileinfo->pathname, $zipcontents);
        }

        // Check actual extracting.
        $targetpath = $tmp . '/target';
        check_dir_exists($targetpath);
        $result = $packer->extract_to_pathname($zippath, $targetpath, null, null, true);

        $this->assertTrue($result);

        foreach ($zipcontents as $filename => $filecontents) {
            $filecontents = reset($filecontents);
            $this->assertTrue(is_readable($targetpath . '/' . $filenamemap[$filename]));
            $this->assertEquals($filecontents, file_get_contents($targetpath . '/' . $filenamemap[$filename]));
        }
    }

    /**
     * @depends test_archive_to_storage
     */
    public function test_extract_to_pathname_onlyfiles() {
        global $CFG;

        $this->resetAfterTest(false);

        $packer = get_file_packer('application/zip');
        $fs = get_file_storage();
        $context = \context_system::instance();

        $target = "$CFG->tempdir/onlyfiles/";
        $testcontent = file_get_contents($this->testfile);

        @mkdir($target, $CFG->directorypermissions);
        $this->assertTrue(is_dir($target));

        $onlyfiles = array('test', 'test.test', 'Žluťoučký/Koníček.txt', 'Idontexist');
        $willbeextracted = array_intersect(array_keys($this->files), $onlyfiles);
        $donotextract = array_diff(array_keys($this->files), $onlyfiles);

        $archive = "$CFG->tempdir/archive.zip";
        $this->assertFileExists($archive);
        $result = $packer->extract_to_pathname($archive, $target, $onlyfiles);
        $this->assertTrue(is_array($result));
        $this->assertEquals(count($willbeextracted), count($result));

        foreach ($willbeextracted as $file) {
            $this->assertTrue($result[$file]);
            $this->assertFileExists($target.$file);
            $this->assertSame($testcontent, file_get_contents($target.$file));
        }
        foreach ($donotextract as $file) {
            $this->assertFalse(isset($result[$file]));
            $this->assertFileDoesNotExist($target.$file);
        }

    }

    /**
     * @depends test_archive_to_storage
     */
    public function test_extract_to_pathname_returnvalue_successful() {
        global $CFG;

        $this->resetAfterTest(false);

        $packer = get_file_packer('application/zip');

        $target = make_request_directory();

        $archive = "$CFG->tempdir/archive.zip";
        $this->assertFileExists($archive);
        $result = $packer->extract_to_pathname($archive, $target, null, null, true);
        $this->assertTrue($result);
    }

    /**
     * @depends test_archive_to_storage
     */
    public function test_extract_to_pathname_returnvalue_failure() {
        global $CFG;

        $this->resetAfterTest(false);

        $packer = get_file_packer('application/zip');

        $target = make_request_directory();

        $archive = "$CFG->tempdir/noarchive.zip";
        $result = $packer->extract_to_pathname($archive, $target, null, null, true);
        $this->assertFalse($result);
    }

    /**
     * @depends test_archive_to_storage
     */
    public function test_extract_to_storage() {
        global $CFG;

        $this->resetAfterTest(false);

        $packer = get_file_packer('application/zip');
        $fs = get_file_storage();
        $context = \context_system::instance();

        $testcontent = file_get_contents($this->testfile);

        $archive = $fs->get_file($context->id, 'phpunit', 'test', 0, '/', 'archive.zip');
        $this->assertNotEmpty($archive);
        $result = $packer->extract_to_storage($archive, $context->id, 'phpunit', 'target', 0, '/');
        $this->assertTrue(is_array($result));
        $this->assertEquals(count($this->files), count($result));
        foreach ($this->files as $file => $unused) {
            $this->assertTrue($result[$file]);
            $stored_file = $fs->get_file_by_hash(sha1("/$context->id/phpunit/target/0/$file"));
            $this->assertInstanceOf('stored_file', $stored_file);
            $this->assertSame($testcontent, $stored_file->get_content());
        }

        $archive = "$CFG->tempdir/archive.zip";
        $this->assertFileExists($archive);
        $result = $packer->extract_to_storage($archive, $context->id, 'phpunit', 'target', 0, '/');
        $this->assertTrue(is_array($result));
        $this->assertEquals(count($this->files), count($result));
        foreach ($this->files as $file => $unused) {
            $this->assertTrue($result[$file]);
            $stored_file = $fs->get_file_by_hash(sha1("/$context->id/phpunit/target/0/$file"));
            $this->assertInstanceOf('stored_file', $stored_file);
            $this->assertSame($testcontent, $stored_file->get_content());
        }
        unlink($archive);
    }

    /**
     * @depends test_extract_to_storage
     */
    public function test_add_files() {
        global $CFG;

        $this->resetAfterTest(false);

        $packer = get_file_packer('application/zip');
        $archive = "$CFG->tempdir/archive.zip";

        $this->assertFileDoesNotExist($archive);
        $packer->archive_to_pathname(array(), $archive);
        $this->assertFileExists($archive);

        $zip_archive = new zip_archive();
        $zip_archive->open($archive, file_archive::OPEN);
        $this->assertEquals(0, $zip_archive->count());

        $zip_archive->add_file_from_string('test.txt', 'test');
        $zip_archive->close();
        $zip_archive->open($archive, file_archive::OPEN);
        $this->assertEquals(1, $zip_archive->count());

        $zip_archive->add_directory('test2');
        $zip_archive->close();
        $zip_archive->open($archive, file_archive::OPEN);
        $files = $zip_archive->list_files();
        $this->assertCount(2, $files);
        $this->assertEquals('test.txt', $files[0]->pathname);
        $this->assertEquals('test2/', $files[1]->pathname);

        $result = $zip_archive->add_file_from_pathname('test.txt', __DIR__.'/nonexistent/file.txt');
        $this->assertFalse($result);
        $zip_archive->close();
        $zip_archive->open($archive, file_archive::OPEN);
        $this->assertEquals(2, $zip_archive->count());
        $zip_archive->close();

        unlink($archive);
    }

    public function test_close_archive() {
        global $CFG;

        $this->resetAfterTest(true);

        $archive = "$CFG->tempdir/archive.zip";
        $textfile = "$CFG->tempdir/textfile.txt";
        touch($textfile);

        $this->assertFileDoesNotExist($archive);
        $this->assertFileExists($textfile);

        // Create archive and close it without files.
        // (returns true, without any warning).
        $zip_archive = new zip_archive();
        $result = $zip_archive->open($archive, file_archive::CREATE);
        $this->assertTrue($result);
        $result = $zip_archive->close();
        $this->assertTrue($result);
        unlink($archive);

        // Create archive and close it with files.
        // (returns true, without any warning).
        $zip_archive = new zip_archive();
        $result = $zip_archive->open($archive, file_archive::CREATE);
        $this->assertTrue($result);
        $result = $zip_archive->add_file_from_string('test.txt', 'test');
        $this->assertTrue($result);
        $result = $zip_archive->add_file_from_pathname('test2.txt', $textfile);
        $result = $zip_archive->close();
        $this->assertTrue($result);
        unlink($archive);

        // Create archive and close if forcing error.
        // (returns true for old PHP versions and
        // false with warnings for new PHP versions). MDL-51863.
        $zip_archive = new zip_archive();
        $result = $zip_archive->open($archive, file_archive::CREATE);
        $this->assertTrue($result);
        $result = $zip_archive->add_file_from_string('test.txt', 'test');
        $this->assertTrue($result);
        $result = $zip_archive->add_file_from_pathname('test2.txt', $textfile);
        $this->assertTrue($result);
        // Delete the file before closing does force close() to fail.
        unlink($textfile);
        // Behavior is different between old PHP versions and new ones. Let's detect it.
        $result = false;
        try {
            // Old PHP versions were not printing any warning.
            $result = $zip_archive->close();
        } catch (\Exception $e) {
            // New PHP versions print PHP Warning.
            $this->assertInstanceOf('PHPUnit\Framework\Error\Warning', $e);
            $this->assertStringContainsString('ZipArchive::close', $e->getMessage());
        }
        // This is crazy, but it shows how some PHP versions do return true.
        try {
            // And some PHP versions do return correctly false (5.4.25, 5.6.14...)
            $this->assertFalse($result);
        } catch (\Exception $e) {
            // But others do insist into returning true (5.6.13...). Only can accept them.
            $this->assertInstanceOf('PHPUnit\Framework\ExpectationFailedException', $e);
            $this->assertTrue($result);
        }
        $this->assertFileDoesNotExist($archive);
    }

    /**
     * @depends test_add_files
     */
    public function test_open_archive() {
        global $CFG;

        $this->resetAfterTest(true);

        $archive = "$CFG->tempdir/archive.zip";

        $this->assertFileDoesNotExist($archive);

        $zip_archive = new zip_archive();
        $result = $zip_archive->open($archive, file_archive::OPEN);
        $this->assertFalse($result);
        $this->assertDebuggingCalled();

        $zip_archive = new zip_archive();
        $result = $zip_archive->open($archive, file_archive::CREATE);
        $this->assertTrue($result);
        $zip_archive->add_file_from_string('test.txt', 'test');
        $zip_archive->close();
        $zip_archive->open($archive, file_archive::OPEN);
        $this->assertEquals(1, $zip_archive->count());

        $zip_archive = new zip_archive();
        $result = $zip_archive->open($archive, file_archive::OVERWRITE);
        $this->assertTrue($result);
        $zip_archive->add_file_from_string('test2.txt', 'test');
        $zip_archive->close();
        $zip_archive->open($archive, file_archive::OPEN);
        $this->assertEquals(1, $zip_archive->count());
        $zip_archive->close();

        unlink($archive);
        $zip_archive = new zip_archive();
        $result = $zip_archive->open($archive, file_archive::OVERWRITE);
        $this->assertTrue($result);
        $zip_archive->add_file_from_string('test2.txt', 'test');
        $zip_archive->close();
        $zip_archive->open($archive, file_archive::OPEN);
        $this->assertEquals(1, $zip_archive->count());
        $zip_archive->close();

        unlink($archive);
    }

    /**
     * Test opening an encrypted archive
     */
    public function test_open_encrypted_archive() {
        $this->resetAfterTest();

        // The archive contains a single encrypted "hello.txt" file.
        $archive = __DIR__ . '/fixtures/passwordis1.zip';

        /** @var \zip_packer $packer */
        $packer = get_file_packer('application/zip');
        $result = $packer->extract_to_pathname($archive, make_temp_directory('zip'));

        $this->assertIsArray($result);
        $this->assertArrayHasKey('hello.txt', $result);
        $this->assertEquals('Can not read file from zip archive', $result['hello.txt']);
    }

    /**
     * Tests the progress reporting.
     */
    public function test_file_progress() {
        global $CFG;

        // Set up.
        $this->resetAfterTest(true);
        $packer = get_file_packer('application/zip');
        $archive = "$CFG->tempdir/archive.zip";
        $context = \context_system::instance();

        // Archive to pathname.
        $this->progress = array();
        $result = $packer->archive_to_pathname($this->files, $archive, true, $this);
        $this->assertTrue($result);
        // Should send progress at least once per file.
        $this->assertTrue(count($this->progress) >= count($this->files));
        // Each progress will be indeterminate.
        $this->assertEquals(
                array(file_progress::INDETERMINATE, file_progress::INDETERMINATE),
                $this->progress[0]);

        // Archive to pathname using entire folder and subfolder instead of file list.
        unlink($archive);
        $folder = make_temp_directory('zip_packer_progress');
        file_put_contents($folder . '/test1.txt', 'hello');
        $subfolder = $folder . '/sub';
        check_dir_exists($subfolder);
        file_put_contents($subfolder . '/test2.txt', 'world');
        file_put_contents($subfolder . '/test3.txt', 'and');
        file_put_contents($subfolder . '/test4.txt', 'other');
        file_put_contents($subfolder . '/test5.txt', 'worlds');
        $this->progress = array();
        $result = $packer->archive_to_pathname(array('' => $folder), $archive, true, $this);
        $this->assertTrue($result);
        // Should send progress at least once per file.
        $this->assertTrue(count($this->progress) >= 5);

        // Archive to storage.
        $this->progress = array();
        $archivefile = $packer->archive_to_storage($this->files, $context->id,
                'phpunit', 'test', 0, '/', 'archive.zip', null, true, $this);
        $this->assertInstanceOf('stored_file', $archivefile);
        $this->assertTrue(count($this->progress) >= count($this->files));
        $this->assertEquals(
                array(file_progress::INDETERMINATE, file_progress::INDETERMINATE),
                $this->progress[0]);

        // Extract to pathname.
        $this->progress = array();
        $target = "$CFG->tempdir/test/";
        check_dir_exists($target);
        $result = $packer->extract_to_pathname($archive, $target, null, $this);
        remove_dir($target);
        $this->assertEquals(count($this->files), count($result));
        $this->assertTrue(count($this->progress) >= count($this->files));
        $this->check_progress_toward_max();

        // Extract to storage (from storage).
        $this->progress = array();
        $result = $packer->extract_to_storage($archivefile, $context->id,
                'phpunit', 'target', 0, '/', null, $this);
        $this->assertEquals(count($this->files), count($result));
        $this->assertTrue(count($this->progress) >= count($this->files));
        $this->check_progress_toward_max();

        // Extract to storage (from path).
        $this->progress = array();
        $result = $packer->extract_to_storage($archive, $context->id,
                'phpunit', 'target', 0, '/', null, $this);
        $this->assertEquals(count($this->files), count($result));
        $this->assertTrue(count($this->progress) >= count($this->files));
        $this->check_progress_toward_max();

        // Wipe created disk file.
        unlink($archive);
    }

    /**
     * Checks that progress reported is numeric rather than indeterminate,
     * and follows the progress reporting rules.
     */
    private function check_progress_toward_max() {
        $lastvalue = -1;
        foreach ($this->progress as $progressitem) {
            list($value, $max) = $progressitem;
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

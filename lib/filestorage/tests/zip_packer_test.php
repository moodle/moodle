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
 * Unit tests for /lib/filestorage/zip_packer.php and zip_archive.php
 *
 * @package   core_files
 * @category  phpunit
 * @copyright 2012 Petr Skoda
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


class zip_packer_testcase extends advanced_testcase {
    protected $testfile;
    protected $files;

    protected function setUp() {
        parent::setUp();

        $this->testfile = __DIR__.'/fixtures/test.txt';

        $fs = get_file_storage();
        $context = context_system::instance();
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
        $moodle22 = __DIR__.'/fixtures/test_moodle_22.zip';
        $moodle = __DIR__.'/fixtures/test_moodle.zip';

        $packer = get_file_packer('application/zip');

        $archivefiles22 = $packer->list_files($moodle22);
        $this->assertTrue(is_array($archivefiles22));
        $this->assertEquals(count($this->files), count($archivefiles22));
        foreach($archivefiles22 as $file) {
            $this->assertArrayHasKey($file->pathname, $this->files);
        }

        $archivefiles = $packer->list_files($moodle);
        $this->assertTrue(is_array($archivefiles));
        $this->assertEquals(count($this->files), count($archivefiles));
        foreach($archivefiles as $file) {
            $this->assertArrayHasKey($file->pathname, $this->files);
        }
    }

    /**
     * @depends test_list_files
     */
    public function test_archive_to_pathname() {
        global $CFG;

        $this->resetAfterTest(false);

        $packer = get_file_packer('application/zip');
        $archive = "$CFG->tempdir/archive.zip";

        $this->assertFalse(file_exists($archive));
        $result = $packer->archive_to_pathname($this->files, $archive);
        $this->assertTrue($result);
        $this->assertTrue(file_exists($archive));

        $archivefiles = $packer->list_files($archive);
        $this->assertTrue(is_array($archivefiles));
        $this->assertEquals(count($this->files), count($archivefiles));
        foreach($archivefiles as $file) {
            $this->assertArrayHasKey($file->pathname, $this->files);
        }
    }

    /**
     * @depends test_archive_to_pathname
     */
    public function test_archive_to_storage() {
        $this->resetAfterTest(false);

        $packer = get_file_packer('application/zip');
        $fs = get_file_storage();
        $context = context_system::instance();

        $this->assertFalse($fs->file_exists($context->id, 'phpunit', 'test', 0, '/', 'archive.zip'));
        $result = $packer->archive_to_storage($this->files, $context->id, 'phpunit', 'test', 0, '/', 'archive.zip');
        $this->assertInstanceOf('stored_file', $result);
        $this->assertTrue($fs->file_exists($context->id, 'phpunit', 'test', 0, '/', 'archive.zip'));

        $archivefiles = $result->list_files($packer);
        $this->assertTrue(is_array($archivefiles));
        $this->assertEquals(count($this->files), count($archivefiles));
        foreach($archivefiles as $file) {
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
        $context = context_system::instance();

        $target = "$CFG->tempdir/test/";
        $testcontent = file_get_contents($this->testfile);

        @mkdir($target, $CFG->directorypermissions);
        $this->assertTrue(is_dir($target));

        $archive = "$CFG->tempdir/archive.zip";
        $this->assertTrue(file_exists($archive));
        $result = $packer->extract_to_pathname($archive, $target);
        $this->assertTrue(is_array($result));
        $this->assertEquals(count($this->files), count($result));
        foreach($this->files as $file=>$unused) {
            $this->assertTrue($result[$file]);
            $this->assertTrue(file_exists($target.$file));
            $this->assertSame($testcontent, file_get_contents($target.$file));
        }

        $archive = $fs->get_file($context->id, 'phpunit', 'test', 0, '/', 'archive.zip');
        $this->assertNotEmpty($archive);
        $result = $packer->extract_to_pathname($archive, $target);
        $this->assertTrue(is_array($result));
        $this->assertEquals(count($this->files), count($result));
        foreach($this->files as $file=>$unused) {
            $this->assertTrue($result[$file]);
            $this->assertTrue(file_exists($target.$file));
            $this->assertSame($testcontent, file_get_contents($target.$file));
        }
    }

    /**
     * @depends test_archive_to_storage
     */
    public function test_extract_to_storage() {
        global $CFG;

        $this->resetAfterTest(true);

        $packer = get_file_packer('application/zip');
        $fs = get_file_storage();
        $context = context_system::instance();

        $testcontent = file_get_contents($this->testfile);

        $archive = $fs->get_file($context->id, 'phpunit', 'test', 0, '/', 'archive.zip');
        $this->assertNotEmpty($archive);
        $result = $packer->extract_to_storage($archive, $context->id, 'phpunit', 'target', 0, '/');
        $this->assertTrue(is_array($result));
        $this->assertEquals(count($this->files), count($result));
        foreach($this->files as $file=>$unused) {
            $this->assertTrue($result[$file]);
            $stored_file = $fs->get_file_by_hash(sha1("/$context->id/phpunit/target/0/$file"));
            $this->assertInstanceOf('stored_file', $stored_file);
            $this->assertSame($testcontent, $stored_file->get_content());
        }

        $archive = "$CFG->tempdir/archive.zip";
        $this->assertTrue(file_exists($archive));
        $result = $packer->extract_to_storage($archive, $context->id, 'phpunit', 'target', 0, '/');
        $this->assertTrue(is_array($result));
        $this->assertEquals(count($this->files), count($result));
        foreach($this->files as $file=>$unused) {
            $this->assertTrue($result[$file]);
            $stored_file = $fs->get_file_by_hash(sha1("/$context->id/phpunit/target/0/$file"));
            $this->assertInstanceOf('stored_file', $stored_file);
            $this->assertSame($testcontent, $stored_file->get_content());
        }
    }
}

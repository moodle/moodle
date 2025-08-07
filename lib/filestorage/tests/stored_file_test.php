<?php
// This file is part of Moodle - https://moodle.org/
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

use advanced_testcase;
use context_system;

/**
 * Unit tests for lib/filestorage/stored_file.php.
 *
 * @package    core_files
 * @category   test
 * @covers     \stored_file
 * @copyright  2022 Mikhail Golenkov <mikhailgolenkov@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class stored_file_test extends advanced_testcase {

    /**
     * Test that the rotate_image() method does not rotate
     * an image that is not supposed to be rotated.
     */
    public function test_rotate_image_does_not_rotate_image(): void {
        global $CFG;
        $this->resetAfterTest();

        $filename = 'testimage.jpg';
        $filepath = $CFG->dirroot . '/lib/filestorage/tests/fixtures/' . $filename;
        $filerecord = [
            'contextid' => context_system::instance()->id,
            'component' => 'core',
            'filearea'  => 'unittest',
            'itemid'    => 0,
            'filepath'  => '/',
            'filename'  => $filename,
        ];
        $fs = get_file_storage();
        $storedfile = $fs->create_file_from_pathname($filerecord, $filepath);

        $result = $storedfile->rotate_image();
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertFalse($result[0]);
        $this->assertFalse($result[1]);
    }

    /**
     * Test that the rotate_image() method rotates an image
     * that is supposed to be rotated.
     */
    public function test_rotate_image_rotates_image(): void {
        global $CFG;
        $this->resetAfterTest();

        // This image was manually rotated to be upside down. Also, Orientation, ExifImageWidth
        // and ExifImageLength EXIF tags were written into its metadata.
        // This is needed to make sure that this image will be rotated by stored_file::rotate_image()
        // and stored as a new rotated file.
        $filename = 'testimage_rotated.jpg';
        $filepath = $CFG->dirroot . '/lib/filestorage/tests/fixtures/' . $filename;
        $filerecord = [
            'contextid' => context_system::instance()->id,
            'component' => 'core',
            'filearea'  => 'unittest',
            'itemid'    => 0,
            'filepath'  => '/',
            'filename'  => $filename,
        ];
        $fs = get_file_storage();
        $storedfile = $fs->create_file_from_pathname($filerecord, $filepath);

        list ($rotateddata, $size) = $storedfile->rotate_image();
        $this->assertNotFalse($rotateddata);
        $this->assertIsArray($size);
        $this->assertEquals(1200, $size['width']);
        $this->assertEquals(297, $size['height']);
    }

    /**
     * Ensure that get_content_file_handle returns a valid file handle.
     */
    public function test_get_psr_stream(): void {
        global $CFG;
        $this->resetAfterTest();

        $filename = 'testimage.jpg';
        $filepath = $CFG->dirroot . '/lib/filestorage/tests/fixtures/' . $filename;
        $filerecord = [
            'contextid' => context_system::instance()->id,
            'component' => 'core',
            'filearea'  => 'unittest',
            'itemid'    => 0,
            'filepath'  => '/',
            'filename'  => $filename,
        ];
        $fs = get_file_storage();
        $file = $fs->create_file_from_pathname($filerecord, $filepath);

        $stream = $file->get_psr_stream();
        $this->assertInstanceOf(\Psr\Http\Message\StreamInterface::class, $stream);
        $this->assertEquals(file_get_contents($filepath), $stream->getContents());
        $this->assertFalse($stream->isWritable());
        $stream->close();
    }

    /**
     * If the data gets into an incorrect state where a file references itself, this should not
     * get into endless recursion (stack overflow) but should throw an exception.
     */
    public function test_sync_external_file_with_recursive_reference(): void {
        global $DB;

        $this->resetAfterTest();

        $fs = get_file_storage();
        $filerecord = [
            'contextid' => context_system::instance()->id,
            'component' => 'core',
            'filearea' => 'unittest',
            'itemid' => 0,
            'filepath' => '/',
            'filename' => 'hello.txt',
        ];
        $file = $fs->create_file_from_string($filerecord, 'hello world');

        $referenceid = $DB->get_field('repository_instances', 'id', ['typeid' => FILE_INTERNAL]);
        $referencestr = \file_storage::pack_reference($filerecord);
        $copyrecord = [
            'contextid' => context_system::instance()->id,
            'component' => 'core',
            'filearea' => 'unittest',
            'itemid' => 1,
            'filepath' => '/',
            'filename' => 'hello.txt',
        ];
        $copy = $fs->create_file_from_reference($copyrecord, $referenceid, $referencestr);

        // Hack the original file so that it has the reference id to itself from the copy.
        $DB->set_field('files', 'referencefileid', $copy->get_referencefileid(), ['id' => $file->get_id()]);

        // Now sync the original file.
        $hackedfile = $fs->get_file_by_id($file->get_id());

        try {
            $hackedfile->sync_external_file();
            $this->fail('Should not work because this is a recursive reference');
        } catch (\moodle_exception $e) {
            $this->assertStringContainsString('File references itself: ' . $file->get_id(), $e->getMessage());
        }

        // Create another file that references the copy.
        $reference2str = \file_storage::pack_reference($copyrecord);
        $copy2record = [
            'contextid' => context_system::instance()->id,
            'component' => 'core',
            'filearea' => 'unittest',
            'itemid' => 2,
            'filepath' => '/',
            'filename' => 'hello.txt',
        ];
        $copy2 = $fs->create_file_from_reference($copy2record, $referenceid, $reference2str);

        // Now we change the original file to reference this second one - 2 levels of redirection.
        $DB->set_field('files', 'referencefileid', $copy2->get_referencefileid(), ['id' => $file->get_id()]);

        // Again try to sync the original file.
        $hackedfile = $fs->get_file_by_id($file->get_id());

        try {
            $hackedfile->sync_external_file();
            $this->fail('Should not work because this is a recursive reference');
        } catch (\moodle_exception $e) {
            $this->assertStringContainsString('File references itself: ' . $file->get_id(), $e->getMessage());
        }

        // Put the hacked file back how it started so the situation is valid.
        $DB->set_field('files', 'referencefileid', 0, ['id' => $file->get_id()]);
        $copy2->sync_external_file();
        $copy->sync_external_file();
        $file->sync_external_file();
    }

}

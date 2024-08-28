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

namespace core\fileredact;

use file_storage;
use stored_file;

/**
 * Tests for the EXIF remover service.
 *
 * If you wish to use these unit tests all you need to do is add the following definition to
 * your config.php file:
 *
 * define('TEST_PATH_TO_EXIFTOOL', '/usr/bin/exiftool');
 *
 * @package   core
 * @copyright Meirza <meirza.arson@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers \core\fileredact\services\exifremover_service
 */
final class exifremover_service_test extends \advanced_testcase {

    /** @var file_storage File storage. */
    private file_storage $fs;

    /**
     * Set up the test environment.
     */
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
        $this->fs = get_file_storage();
    }

    /**
     * Creates a temporary file for testing purposes.
     *
     * @return stored_file The stored file.
     */
    private function create_test_file(): stored_file {
        $filename = 'dummy.jpg';
        $path = __DIR__ . '/../fixtures/fileredact/' . $filename;
        $filerecord = (object) [
            'contextid' => \context_user::instance(get_admin()->id)->id,
            'component' => 'user',
            'filearea' => 'unittest',
            'itemid' => 0,
            'filepath' => '/',
            'filename' => $filename,
        ];
        $file = $this->fs->get_file($filerecord->contextid, $filerecord->component, $filerecord->filearea, $filerecord->itemid,
            $filerecord->filepath, $filerecord->filename);
        if ($file) {
            $file->delete();
        }

        return $this->fs->create_file_from_pathname($filerecord, $path);
    }

    /**
     * Creates a temporary invalid file for testing purposes.
     *
     * @return stored_file The stored file.
     */
    private function create_invalid_test_file(): stored_file {
        $filename = 'dummy_invalid.jpg';
        $filerecord = (object) [
            'contextid' => \context_user::instance(get_admin()->id)->id,
            'component' => 'user',
            'filearea' => 'unittest',
            'itemid' => 0,
            'filepath' => '/',
            'filename' => $filename,
        ];
        $file = $this->fs->get_file($filerecord->contextid, $filerecord->component, $filerecord->filearea, $filerecord->itemid,
            $filerecord->filepath, $filerecord->filename);
        if ($file) {
            $file->delete();
        }

        return $this->fs->create_file_from_string($filerecord, 'Dummy content');
    }

    /**
     * Tests the `exifremover_service` functionality using PHP GD.
     *
     * This test verifies the ability of the `exifremover_service` to remove all EXIF
     * tags from an image file when using PHP GD. It ensures that all tags, including
     * GPSLatitude, GPSLongitude, and Orientation, are removed from the EXIF data.
     *
     * @return void
     */
    public function test_exifremover_service_with_gd(): void {
        $file = $this->create_test_file();
        // Get the EXIF data from the new file.
        $currentexif = $this->get_new_exif($file->get_content());
        $this->assertStringContainsString('GPSLatitude', $currentexif);
        $this->assertStringContainsString('GPSLongitude', $currentexif);
        $this->assertStringContainsString('Orientation', $currentexif);

        $exifremoverservice = new services\exifremover_service($file);
        $exifremoverservice->execute();
        // Get the EXIF data from the new file.
        $newexif = $this->get_new_exif($file->get_content());

        // Removing the "all" tags will result in removing all existing tags.
        $this->assertStringNotContainsString('GPSLatitude', $newexif);
        $this->assertStringNotContainsString('GPSLongitude', $newexif);
        $this->assertStringNotContainsString('Orientation', $newexif);
    }

    /**
     * Tests the `exifremover_service` functionality using ExifTool.
     *
     * This test verifies the ability of the `exifremover_service` to remove specific
     * EXIF tags from an image file when configured to use ExifTool. The test includes
     * scenarios for removing all EXIF tags and for removing only GPS tags.
     */
    public function test_exifremover_service_with_exiftool(): void {
        if ( (defined('TEST_PATH_TO_EXIFTOOL') && TEST_PATH_TO_EXIFTOOL && !is_executable(TEST_PATH_TO_EXIFTOOL))
                || (!defined('TEST_PATH_TO_EXIFTOOL'))) {
            $this->markTestSkipped('Could not test the EXIF remover service, missing configuration. ' .
            "Example: define('TEST_PATH_TO_EXIFTOOL', '/usr/bin/exiftool');");
        }

        set_config('exifremovertoolpath', TEST_PATH_TO_EXIFTOOL, 'core_fileredact');

        // Remove All tags.
        set_config('exifremoverremovetags', 'all', 'core_fileredact');
        $file1 = $this->create_test_file();
        $exifremoverservice = new services\exifremover_service($file1);
        $exifremoverservice->execute();
        // Get the EXIF data from the new file.
        $newexif = $this->get_new_exif($file1->get_content());
        // Removing the "all" tags will result in removing all existing tags.
        $this->assertStringNotContainsString('GPSLatitude', $newexif);
        $this->assertStringNotContainsString('GPSLongitude', $newexif);
        $this->assertStringNotContainsString('Aperture', $newexif);
        // Orientation is a preserve tag. Ensure it always exists.
        $this->assertStringContainsString('Orientation', $newexif);

        // Remove the GPS tag only.
        set_config('exifremoverremovetags', 'gps', 'core_fileredact');
        $file2 = $this->create_test_file();
        $exifremoverservice = new services\exifremover_service($file2);
        $exifremoverservice->execute();
        // Get the EXIF data from the new file.
        $newexif = $this->get_new_exif($file2->get_content());
        // The GPS tag only removal will remove the tag containing "GPS" keyword.
        $this->assertStringNotContainsString('GPSLatitude', $newexif);
        $this->assertStringNotContainsString('GPSLongitude', $newexif);
        // And keep the other tags remaining.
        $this->assertStringContainsString('Aperture', $newexif);
        // Orientation is a preserve tag. Ensure it always exists.
        $this->assertStringContainsString('Orientation', $newexif);
    }

    /**
     * Tests the `is_mimetype_supported` method.
     *
     * This test initializes the `exifremover_service` and verifies if the given
     * MIME types are supported for EXIF removal using both PHP GD and ExifTool.
     */
    public function test_exifremover_service_is_mimetype_supported(): void {
        $file = $this->create_test_file();
        // Init uals(false, $resultthe service.
        $exifremoverservice = new services\exifremover_service($file);

        // Test using PHP GD.
        $rc = new \ReflectionClass(services\exifremover_service::class);
        $rcexifremover = $rc->getMethod('is_mimetype_supported');
        // As default, the exif remover only accepts the default mime type.
        $result = $rcexifremover->invokeArgs($exifremoverservice, [services\exifremover_service::DEFAULT_MIMETYPE]);
        $this->assertEquals(true, $result);
        // Other than the default, the function will returns false.
        $result = $rcexifremover->invokeArgs($exifremoverservice, ['image/tiff']);
        $this->assertEquals(false, $result);

        // Test using ExifTool.
        $useexiftool = $rc->getProperty('useexiftool');
        $useexiftool->setValue($exifremoverservice, true);
        // Set the supported mime types.
        set_config('exifremovermimetype', 'image/tiff', 'core_fileredact');
        // Other than the `image/tiff`, the function will returns false.
        $result = $rcexifremover->invokeArgs($exifremoverservice, ['image/png']);
        $this->assertEquals(false, $result);

    }

    /**
     * Tests the `clean_filename` method.
     *
     * This test initializes the `exifremover_service` with a mock file record and
     * invokes the `clean_filename` method via reflection to ensure it correctly
     * processes the given filename.
     *
     * @dataProvider exifremover_service_clean_filename_provider
     *
     * @param string $filename The filename to be cleaned by the `clean_filename` method.
     * @param string $expected The expected result after cleaning the filename.
     */
    public function test_exifremover_service_clean_filename($filename, $expected): void {
        $file = $this->create_test_file();
        // Init the service.
        $exifremoverservice = new services\exifremover_service($file);

        $rc = new \ReflectionClass(services\exifremover_service::class);
        $rccleanfilename = $rc->getMethod('clean_filename');

        $result = $rccleanfilename->invokeArgs($exifremoverservice, [$filename]);
        $this->assertEquals($expected, $result);
    }

    /**
     * Tests that the EXIF remover service alters the content hash of a file
     * when a new file is created from the original.
     */
    public function test_exifremover_contenthash(): void {
        $file = $this->create_test_file();
        $beforehash = $file->get_contenthash();
        $exifremoverservice = new services\exifremover_service($file);
        $exifremoverservice->execute();
        $afterhash = $file->get_contenthash();
        $this->assertNotSame($beforehash, $afterhash);
    }

    /**
     * Tests the EXIF remover service with an unknown filename and a valid EXIF tool path.
     */
    public function test_exiftool_filename_unknown(): void {
        if ( (defined('TEST_PATH_TO_EXIFTOOL') && TEST_PATH_TO_EXIFTOOL && !is_executable(TEST_PATH_TO_EXIFTOOL))
                || (!defined('TEST_PATH_TO_EXIFTOOL'))) {
            $this->markTestSkipped('Could not test the EXIF remover service, missing configuration. ' .
            "Example: define('TEST_PATH_TO_EXIFTOOL', '/usr/bin/exiftool');");
        }
        set_config('exifremovertoolpath', TEST_PATH_TO_EXIFTOOL, 'core_fileredact');
        $invalidfile = $this->create_invalid_test_file();
        $exifremoverservice = new services\exifremover_service($invalidfile);
        $this->expectException(\Exception::class);
        $exifremoverservice->execute();
    }

    /**
     * Tests the EXIF remover service with an unknown filename and an invalid EXIF tool path.
     */
    public function test_exiftool_notfound_filename_unknown(): void {
        set_config('exifremovertoolpath', 'fakeexiftool', 'core_fileredact');
        $invalidfile = $this->create_invalid_test_file();
        $exifremoverservice = new services\exifremover_service($invalidfile);
        $this->expectException(\moodle_exception::class);
        $this->expectExceptionMessage(get_string('fileredact:exifremover:failedprocessgd', 'core_files'));
        $exifremoverservice->execute();
    }

    /**
     * Retrieves the EXIF metadata of a file.
     *
     * @param string $content the content of file.
     * @return string The EXIF metadata as a string.
     */
    private function get_new_exif(string $content): string {
        $logpath = make_request_directory() . '/temp.jpg';
        file_put_contents($logpath, $content);
        $exif = exif_read_data($logpath);
        $string = "";
        foreach ($exif as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $subkey => $subvalue) {
                    $string .= "$subkey: $subvalue\n";
                }
            } else {
                $string .= "$key: $value\n";
            }
        }
        return $string;
    }

    /**
     * Data provider for test_exifremover_service_clean_filename().
     *
     * @return array
     */
    public static function exifremover_service_clean_filename_provider(): array {
        return [
            'Hyphen minus &#x002D' => [
                'filename' => '-if \'$LensModel eq "18-35mm"\'',
                'expected' => 'if $LensModel eq 18-35mm',
            ],
            'Minus &#x2212;' => [
                'filename' => '−filename.jpg',
                'expected' => 'filename.jpg',
            ],
        ];
    }
}

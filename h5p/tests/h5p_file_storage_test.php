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
 * Testing the H5P H5PFileStorage interface implementation.
 *
 * @package    core_h5p
 * @category   test
 * @copyright  2019 Victor Deniz <victor@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_h5p\local\tests;

use core_h5p\file_storage;
use core_h5p\autoloader;
use file_archive;
use zip_archive;

defined('MOODLE_INTERNAL') || die();

/**
 * Test class covering the H5PFileStorage interface implementation.
 *
 * @package    core_h5p
 * @copyright  2019 Victor Deniz <victor@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @runTestsInSeparateProcesses
 */
class h5p_file_storage_testcase extends \advanced_testcase {

    /** @var \core_h5p\file_storage H5P file storage instance */
    protected $h5p_file_storage;
    /** @var \file_storage Core Moodle file_storage associated to the H5P file_storage */
    protected $h5p_fs_fs;
    /** @var \context Moodle context of the H5P file_storage */
    protected $h5p_fs_context;
    /** @var string Path to temp directory */
    protected $h5p_tempath;
    /** @var \core_h5p_generator  H5P generator instance */
    protected $h5p_generator;
    /** @var array $files an array used in the cache tests. */
    protected $files = ['scripts' => [], 'styles' => []];
    /** @var int $libraryid an id for the library. */
    protected $libraryid = 1;

    protected function setUp() {
        parent::setUp();
        $this->resetAfterTest(true);

        autoloader::register();

        // Fetch generator.
        $generator = \testing_util::get_data_generator();
        $this->h5p_generator = $generator->get_plugin_generator('core_h5p');

        // Create file_storage_instance and create H5P temp directory.
        $this->h5p_file_storage = new file_storage();
        $this->h5p_tempath = $this->h5p_file_storage->getTmpPath();
        check_dir_exists($this->h5p_tempath);

        // Get value of protected properties.
        $h5p_fs_rc = new \ReflectionClass(file_storage::class);
        $h5p_file_storage_context = $h5p_fs_rc->getProperty('context');
        $h5p_file_storage_context->setAccessible(true);
        $this->h5p_fs_context = $h5p_file_storage_context->getValue($this->h5p_file_storage);

        $h5p_file_storage_fs = $h5p_fs_rc->getProperty('fs');
        $h5p_file_storage_fs->setAccessible(true);
        $this->h5p_fs_fs = $h5p_file_storage_fs->getValue($this->h5p_file_storage);
    }

    /**
     * Test that given the main directory of a library that all files are saved
     * into the file system.
     */
    public function test_saveLibrary(): void {

        $machinename = 'TestLib';
        $majorversion = 1;
        $minorversion = 0;
        [$lib, $files] = $this->h5p_generator->create_library($this->h5p_tempath, $this->libraryid, $machinename, $majorversion,
            $minorversion);

        // Now run the API call.
        $this->h5p_file_storage->saveLibrary($lib);

        // Check that files are in the Moodle file system.
        $file =  $this->h5p_fs_fs->get_file ($this->h5p_fs_context->id, file_storage::COMPONENT,
            file_storage::LIBRARY_FILEAREA, '1', "/{$machinename}-{$majorversion}.{$minorversion}/", 'library.json');
        $filepath = "/{$machinename}-{$majorversion}.{$minorversion}/";
        $this->assertEquals($filepath, $file->get_filepath());

        $file =  $this->h5p_fs_fs->get_file ($this->h5p_fs_context->id, file_storage::COMPONENT,
            file_storage::LIBRARY_FILEAREA, '1', "/{$machinename}-{$majorversion}.{$minorversion}/scripts/", 'testlib.min.js');
        $jsfilepath = "{$filepath}scripts/";
        $this->assertEquals($jsfilepath, $file->get_filepath());

        $file =  $this->h5p_fs_fs->get_file ($this->h5p_fs_context->id, file_storage::COMPONENT,
            file_storage::LIBRARY_FILEAREA, '1', "/{$machinename}-{$majorversion}.{$minorversion}/styles/", 'testlib.min.css');
        $cssfilepath = "{$filepath}styles/";
        $this->assertEquals($cssfilepath, $file->get_filepath());
    }

    /**
     * Test that a content file can be saved.
     */
    public function test_saveContent(): void {

        $source = $this->h5p_tempath . '/' . 'content.json';
        $this->h5p_generator->create_file($source);

        $this->h5p_file_storage->saveContent($this->h5p_tempath, ['id' => 5]);

        $file =  $this->h5p_fs_fs->get_file ($this->h5p_fs_context->id, file_storage::COMPONENT,
            file_storage::CONTENT_FILEAREA, '5', '/', 'content.json');
        $this->assertEquals(file_storage::CONTENT_FILEAREA, $file->get_filearea());
        $this->assertEquals('content.json', $file->get_filename());
        $this->assertEquals(5, $file->get_itemid());
    }

    /**
     * Test that content files located on the file system can be deleted.
     */
    public function test_deleteContent(): void {

        $source = $this->h5p_tempath . '/' . 'content.json';
        $this->h5p_generator->create_file($source);

        $this->h5p_file_storage->saveContent($this->h5p_tempath, ['id' => 5]);

        $file =  $this->h5p_fs_fs->get_file ($this->h5p_fs_context->id, file_storage::COMPONENT,
            file_storage::CONTENT_FILEAREA, '5', '/', 'content.json');
        $this->assertEquals('content.json', $file->get_filename());

        // Now to delete the record.
        $this->h5p_file_storage->deleteContent(['id' => 5]);
        $file =  $this->h5p_fs_fs->get_file ($this->h5p_fs_context->id, file_storage::COMPONENT,
            file_storage::CONTENT_FILEAREA, '5', '/', 'content.json');
        $this->assertFalse($file);
    }

    /**
     * Test that returning a temp path returns what is expected by the h5p library.
     */
    public function test_getTmpPath(): void {

        $temparray = explode('/', $this->h5p_tempath);
        $h5pdirectory = array_pop($temparray);
        $this->assertTrue(stripos($h5pdirectory, 'h5p-') === 0);
    }

    /**
     * Test that the content files can be exported to a specified location.
     */
    public function test_exportContent(): void {

        // Create a file to store.
        $source = $this->h5p_tempath . '/' . 'content.json';
        $this->h5p_generator->create_file($source);

        $this->h5p_file_storage->saveContent($this->h5p_tempath, ['id' => 5]);

        $file =  $this->h5p_fs_fs->get_file ($this->h5p_fs_context->id, file_storage::COMPONENT,
            file_storage::CONTENT_FILEAREA, '5', '/', 'content.json');
        $this->assertEquals('content.json', $file->get_filename());

        // Now export it.
        $destinationdirectory = $this->h5p_tempath . '/' . 'testdir';
        check_dir_exists($destinationdirectory);

        $this->h5p_file_storage->exportContent(5, $destinationdirectory);
        // Check that there is a file now in that directory.
        $contents = scandir($destinationdirectory);
        $value = array_search('content.json', $contents);
        $this->assertEquals('content.json', $contents[$value]);
    }

    /**
     * Test that libraries on the file system can be exported to a specified location.
     */
    public function test_exportLibrary(): void {

        $machinename = 'TestLib';
        $majorversion = 1;
        $minorversion = 0;
        [$lib, $files] = $this->h5p_generator->create_library($this->h5p_tempath, $this->libraryid, $machinename, $majorversion,
        $minorversion);

        // Now run the API call.
        $this->h5p_file_storage->saveLibrary($lib);

        $destinationdirectory = $this->h5p_tempath . '/' . 'testdir';
        check_dir_exists($destinationdirectory);

        $this->h5p_file_storage->exportLibrary($lib, $destinationdirectory);

        $filepath = "/{$machinename}-{$majorversion}.{$minorversion}/";
        // There should be at least three items here (but could be more with . and ..).
        $this->assertFileExists($destinationdirectory . $filepath . 'library.json');
        $this->assertFileExists($destinationdirectory . $filepath . 'scripts/' . 'testlib.min.js');
        $this->assertFileExists($destinationdirectory . $filepath . 'styles/' . 'testlib.min.css');
    }

    /**
     * Test that an export file can be saved into the file system.
     */
    public function test_saveExport(): void {

        $filename = 'someexportedfile.h5p';
        $source = $this->h5p_tempath . '/' . $filename;
        $this->h5p_generator->create_file($source);

        $this->h5p_file_storage->saveExport($source, $filename);

        // Check out if the file is there.
        $file =  $this->h5p_fs_fs->get_file ($this->h5p_fs_context->id, file_storage::COMPONENT,
            file_storage::EXPORT_FILEAREA, '0', '/', $filename);
        $this->assertEquals(file_storage::EXPORT_FILEAREA, $file->get_filearea());
    }

    /**
     * Test that an exort file can be deleted from the file system.
     * @return [type] [description]
     */
    public function test_deleteExport(): void {

        $filename = 'someexportedfile.h5p';
        $source = $this->h5p_tempath . '/' . $filename;
        $this->h5p_generator->create_file($source);

        $this->h5p_file_storage->saveExport($source, $filename);

        // Check out if the file is there.
        $file =  $this->h5p_fs_fs->get_file ($this->h5p_fs_context->id, file_storage::COMPONENT,
            file_storage::EXPORT_FILEAREA, '0', '/', $filename);
        $this->assertEquals(file_storage::EXPORT_FILEAREA, $file->get_filearea());

        // Time to delete.
        $this->h5p_file_storage->deleteExport($filename);

        // Check out if the file is there.
        $file =  $this->h5p_fs_fs->get_file ($this->h5p_fs_context->id, file_storage::COMPONENT,
            file_storage::EXPORT_FILEAREA, '0', '/', $filename);
        $this->assertFalse($file);
    }

    /**
     * Test to check if an export file already exists on the file system.
     */
    public function test_hasExport(): void {

        $filename = 'someexportedfile.h5p';
        $source = $this->h5p_tempath . '/' . $filename;
        $this->h5p_generator->create_file($source);

        // Check that it doesn't exist in the file system.
        $this->assertFalse($this->h5p_file_storage->hasExport($filename));

        $this->h5p_file_storage->saveExport($source, $filename);
        // Now it should be present.
        $this->assertTrue($this->h5p_file_storage->hasExport($filename));
    }

    /**
     * Test that all the library files for an H5P activity can be concatenated into "cache" files. One for js and another for css.
     */
    public function test_cacheAssets(): void {

        $basedirectory = $this->h5p_tempath . '/' . 'test-1.0';

        $machinename = 'TestLib';
        $majorversion = 1;
        $minorversion = 0;
        [$lib, $libfiles] = $this->h5p_generator->create_library($basedirectory, $this->libraryid, $machinename, $majorversion,
            $minorversion);
        array_push($this->files['scripts'], ...$libfiles['scripts']);
        array_push($this->files['styles'], ...$libfiles['styles']);

        // Now run the API call.
        $this->h5p_file_storage->saveLibrary($lib);

        // Second library.
        $basedirectory = $this->h5p_tempath . '/' . 'supertest-2.4';

        $this->libraryid++;
        $machinename = 'SuperTest';
        $majorversion = 2;
        $minorversion = 4;
        [$lib, $libfiles] = $this->h5p_generator->create_library($basedirectory, $this->libraryid, $machinename, $majorversion,
            $minorversion);
        array_push($this->files['scripts'], ...$libfiles['scripts']);
        array_push($this->files['styles'], ...$libfiles['styles']);

        $this->h5p_file_storage->saveLibrary($lib);

        $this->assertCount(2, $this->files['scripts']);
        $this->assertCount(2, $this->files['styles']);

        $key = 'testhashkey';

        $this->h5p_file_storage->cacheAssets($this->files, $key);
        $this->assertCount(1, $this->files['scripts']);
        $this->assertCount(1, $this->files['styles']);


        $expectedfile = '/' . file_storage::CACHED_ASSETS_FILEAREA . '/' . $key . '.js';
        $this->assertEquals($expectedfile, $this->files['scripts'][0]->path);
        $expectedfile = '/' . file_storage::CACHED_ASSETS_FILEAREA . '/' . $key . '.css';
        $this->assertEquals($expectedfile, $this->files['styles'][0]->path);
    }

    /**
     * Test that cached files can be retrieved via a key.
     */
    public function test_getCachedAssets() {

        $basedirectory = $this->h5p_tempath . '/' . 'test-1.0';

        $machinename = 'TestLib';
        $majorversion = 1;
        $minorversion = 0;
        [$lib, $libfiles] = $this->h5p_generator->create_library($basedirectory, $this->libraryid, $machinename, $majorversion,
            $minorversion);
        array_push($this->files['scripts'], ...$libfiles['scripts']);
        array_push($this->files['styles'], ...$libfiles['styles']);

        // Now run the API call.
        $this->h5p_file_storage->saveLibrary($lib);

        // Second library.
        $basedirectory = $this->h5p_tempath . '/' . 'supertest-2.4';

        $this->libraryid++;
        $machinename = 'SuperTest';
        $majorversion = 2;
        $minorversion = 4;
        [$lib, $libfiles] = $this->h5p_generator->create_library($basedirectory, $this->libraryid, $machinename, $majorversion,
            $minorversion);
        array_push($this->files['scripts'], ...$libfiles['scripts']);
        array_push($this->files['styles'], ...$libfiles['styles']);

        $this->h5p_file_storage->saveLibrary($lib);

        $this->assertCount(2, $this->files['scripts']);
        $this->assertCount(2, $this->files['styles']);

        $key = 'testhashkey';

        $this->h5p_file_storage->cacheAssets($this->files, $key);

        $testarray = $this->h5p_file_storage->getCachedAssets($key);
        $this->assertCount(1, $testarray['scripts']);
        $this->assertCount(1, $testarray['styles']);
        $expectedfile = '/' . file_storage::CACHED_ASSETS_FILEAREA . '/' . $key . '.js';
        $this->assertEquals($expectedfile, $testarray['scripts'][0]->path);
        $expectedfile = '/' . file_storage::CACHED_ASSETS_FILEAREA . '/' . $key . '.css';
        $this->assertEquals($expectedfile, $testarray['styles'][0]->path);
    }

    /**
     * Test that cache files in the files system can be removed.
     */
    public function test_deleteCachedAssets(): void {
        $basedirectory = $this->h5p_tempath . '/' . 'test-1.0';

        $machinename = 'TestLib';
        $majorversion = 1;
        $minorversion = 0;
        [$lib, $libfiles] = $this->h5p_generator->create_library($basedirectory, $this->libraryid, $machinename, $majorversion,
            $minorversion);
        array_push($this->files['scripts'], ...$libfiles['scripts']);
        array_push($this->files['styles'], ...$libfiles['styles']);

        // Now run the API call.
        $this->h5p_file_storage->saveLibrary($lib);

        // Second library.
        $basedirectory = $this->h5p_tempath . '/' . 'supertest-2.4';

        $this->libraryid++;
        $machinename = 'SuperTest';
        $majorversion = 2;
        $minorversion = 4;
        [$lib, $libfiles] = $this->h5p_generator->create_library($basedirectory, $this->libraryid, $machinename, $majorversion,
            $minorversion);
        array_push($this->files['scripts'], ...$libfiles['scripts']);
        array_push($this->files['styles'], ...$libfiles['styles']);

        $this->h5p_file_storage->saveLibrary($lib);

        $this->assertCount(2, $this->files['scripts']);
        $this->assertCount(2, $this->files['styles']);

        $key = 'testhashkey';

        $this->h5p_file_storage->cacheAssets($this->files, $key);

        $testarray = $this->h5p_file_storage->getCachedAssets($key);
        $this->assertCount(1, $testarray['scripts']);
        $this->assertCount(1, $testarray['styles']);

        // Time to delete.
        $this->h5p_file_storage->deleteCachedAssets([$key]);
        $testarray = $this->h5p_file_storage->getCachedAssets($key);
        $this->assertNull($testarray);
    }

    /**
     * Retrieve content from a file given a specific path.
     */
    public function test_getContent() {
        $basedirectory = $this->h5p_tempath . '/' . 'test-1.0';

        $machinename = 'TestLib';
        $majorversion = 1;
        $minorversion = 0;
        [$lib, $libfiles] = $this->h5p_generator->create_library($basedirectory, $this->libraryid, $machinename, $majorversion,
            $minorversion);
        array_push($this->files['scripts'], ...$libfiles['scripts']);
        array_push($this->files['styles'], ...$libfiles['styles']);

        // Now run the API call.
        $this->h5p_file_storage->saveLibrary($lib);

        $content = $this->h5p_file_storage->getContent($this->files['scripts'][0]->path);
        // The file content is created based on the file system path (\core_h5p_generator::create_file).
        $expectedcontent = hash("md5", $basedirectory. '/' . 'scripts' . '/' . 'testlib.min.js');

        $this->assertEquals($expectedcontent, $content);
    }

    /**
     * Test that an upgrade script can be found on the file system.
     */
    public function test_getUpgradeScript() {
        // Upload an upgrade file.
        $machinename = 'TestLib';
        $majorversion = 3;
        $minorversion = 1;
        $filepath = '/' . "{$machinename}-{$majorversion}.{$minorversion}" . '/';
        $fs = get_file_storage();
        $filerecord = [
            'contextid' => \context_system::instance()->id,
            'component' => file_storage::COMPONENT,
            'filearea' => file_storage::LIBRARY_FILEAREA,
            'itemid' => 15,
            'filepath' => $filepath,
            'filename' => 'upgrade.js'
        ];
        $filestorage = new file_storage();
        $fs->create_file_from_string($filerecord, 'test string info');
        $expectedfilepath = '/' . file_storage::LIBRARY_FILEAREA . $filepath . 'upgrade.js';
        $this->assertEquals($expectedfilepath, $filestorage->getUpgradeScript($machinename, $majorversion, $minorversion));
        $this->assertNull($filestorage->getUpgradeScript($machinename, $majorversion, 7));
    }

    /**
     * Test that information from a source can be saved to the specified path.
     * The zip file has the following contents
     * - h5ptest
     * |- content
     * |     |- content.json
     * |- testFont
     * |     |- testfont.min.css
     * |- testJavaScript
     * |     |- testscript.min.js
     * |- h5p.json
     */
    public function test_saveFileFromZip() {

        $ziparchive = new zip_archive();
        $path = __DIR__ . '/fixtures/h5ptest.zip';
        $result = $ziparchive->open($path, file_archive::OPEN);

        $files = $ziparchive->list_files();
        foreach ($files as $file) {
            if (!$file->is_directory) {
                $stream = $ziparchive->get_stream($file->index);
                $items = explode('/', $file->pathname);
                array_shift($items);
                $path = implode('/', $items);
                $this->h5p_file_storage->saveFileFromZip($this->h5p_tempath, $path, $stream);
                $filestocheck[] = $path;
            }
        }
        $ziparchive->close();

        foreach ($filestocheck as $filetocheck) {
            $pathtocheck = $this->h5p_tempath .'/'. $filetocheck;
            $this->assertFileExists($pathtocheck);
        }
    }

    /**
     * Test that a library is fully deleted from the file system
     */
    public function test_delete_library() {

        $basedirectory = $this->h5p_tempath . '/' . 'test-1.0';

        $machinename = 'TestLib';
        $majorversion = 1;
        $minorversion = 0;
        [$lib, $files] = $this->h5p_generator->create_library($basedirectory, $this->libraryid, $machinename, $majorversion,
            $minorversion);

        // Now run the API call.
        $this->h5p_file_storage->saveLibrary($lib);

        // Save a second library to ensure we aren't deleting all libraries, but just the one specified.
        $basedirectory = $this->h5p_tempath . '/' . 'awesomelib-2.1';

        $this->libraryid++;
        $machinename = 'AwesomeLib';
        $majorversion = 2;
        $minorversion = 1;
        [$lib2, $files2] = $this->h5p_generator->create_library($basedirectory, $this->libraryid, $machinename, $majorversion,
            $minorversion);

        // Now run the API call.
        $this->h5p_file_storage->saveLibrary($lib2);

        $files =  $this->h5p_fs_fs->get_area_files($this->h5p_fs_context->id, file_storage::COMPONENT,
                file_storage::LIBRARY_FILEAREA);
        $this->assertCount(14, $files);

        $this->h5p_file_storage->delete_library($lib);

        // Let's look at the records.
        $files =  $this->h5p_fs_fs->get_area_files($this->h5p_fs_context->id, file_storage::COMPONENT,
                file_storage::LIBRARY_FILEAREA);
        $this->assertCount(7, $files);

        // Check that the db count is still the same after setting the libraryId to false.
        $lib['libraryId'] = false;
        $this->h5p_file_storage->delete_library($lib);

        $files =  $this->h5p_fs_fs->get_area_files($this->h5p_fs_context->id, file_storage::COMPONENT,
                file_storage::LIBRARY_FILEAREA);
        $this->assertCount(7, $files);
    }
}
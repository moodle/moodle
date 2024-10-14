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

namespace core_h5p;

use core_h5p\file_storage;
use core_h5p\local\library\autoloader;
use core_h5p\helper;
use file_archive;
use moodle_exception;
use ReflectionMethod;
use stored_file;
use zip_archive;

/**
 * Test class covering the H5PFileStorage interface implementation.
 *
 * @package    core_h5p
 * @category   test
 * @copyright  2019 Victor Deniz <victor@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @runTestsInSeparateProcesses
 * @covers \core_h5p\file_storage
 */
final class file_storage_test extends \advanced_testcase {

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

    protected function setUp(): void {
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
        $this->h5p_fs_context = $h5p_file_storage_context->getValue($this->h5p_file_storage);

        $h5p_file_storage_fs = $h5p_fs_rc->getProperty('fs');
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
    public function test_getCachedAssets(): void {

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
    public function test_getContent(): void {
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
    public function test_getUpgradeScript(): void {
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
    public function test_saveFileFromZip(): void {

        $ziparchive = new zip_archive();
        $path = self::get_fixture_path(__NAMESPACE__, 'h5ptest.zip');
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
    public function test_delete_library(): void {

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

    /**
     * Test get_icon_url() function behaviour.
     *
     * @dataProvider get_icon_url_provider
     * @param  string  $filename  The name of the H5P file to load.
     * @param  bool    $expected  Whether the icon should exist or not.
     */
    public function test_get_icon_url(string $filename, bool $expected): void {
        global $DB;

        $this->resetAfterTest();
        $factory = new \core_h5p\factory();

        $admin = get_admin();

        // Prepare a valid .H5P file.
        $path = self::get_fixture_path(__NAMESPACE__, $filename);

        // Libraries can be updated when the file has been created by admin, even when the current user is not the admin.
        $this->setUser($admin);
        $file = helper::create_fake_stored_file_from_path($path, (int)$admin->id);
        $factory->get_framework()->set_file($file);
        $config = (object)[
            'frame' => 1,
            'export' => 1,
            'embed' => 0,
            'copyright' => 0,
        ];

        $h5pid = helper::save_h5p($factory, $file, $config);
        $h5p = $DB->get_record('h5p', ['id' => $h5pid]);
        $h5plib = $DB->get_record('h5p_libraries', ['id' => $h5p->mainlibraryid]);
        $iconurl = $this->h5p_file_storage->get_icon_url(
            $h5plib->id,
            $h5plib->machinename,
            $h5plib->majorversion,
            $h5plib->minorversion
        );
        if ($expected) {
            $this->assertStringContainsString(file_storage::ICON_FILENAME, $iconurl);
        } else {
            $this->assertFalse($iconurl);
        }
    }

    /**
     * Data provider for test_get_icon_url().
     *
     * @return array
     */
    public static function get_icon_url_provider(): array {
        return [
            'Icon included' => [
                'filltheblanks.h5p',
                true,
            ],
            'Icon not included' => [
                'greeting-card.h5p',
                false,
            ],
        ];
    }

    /**
     * Test the private method get_file, a wrapper for getting an H5P content file.
     */
    public function test_get_file(): void {

        $this->setAdminUser();
        $file = 'img/fake.png';
        $h5pcontentid = 3;

        // Add a file to a H5P content.
        $this->h5p_generator->create_content_file($file, file_storage::CONTENT_FILEAREA, $h5pcontentid);

        // Set get_file method accessibility.
        $method = new ReflectionMethod(file_storage::class, 'get_file');

        $contentfile = $method->invoke(new file_storage(), file_storage::CONTENT_FILEAREA, $h5pcontentid, $file);

        // Check that it returns an instance of store_file.
        $this->assertInstanceOf('stored_file', $contentfile);

        // Add a file to editor.
        $this->h5p_generator->create_content_file($file, 'draft', $h5pcontentid);

        $editorfile = $method->invoke(new file_storage(), 'draft', $h5pcontentid, $file);

        // Check that it returns an instance of store_file.
        $this->assertInstanceOf('stored_file', $editorfile);
    }

    /**
     * Test that a single file is added to Moodle files.
     */
    public function test_move_file(): void {

        // Create temp folder.
        $tempfolder = make_request_directory(false);

        // Create H5P content folder.
        $filepath = '/img/';
        $filename = 'fake.png';
        $h5pcontentfolder = $tempfolder . '/fakeH5Pcontent/content' . $filepath;
        if (!check_dir_exists($h5pcontentfolder, true, true)) {
            throw new moodle_exception('error_creating_temp_dir', 'error', $h5pcontentfolder);
        }

        $file = $h5pcontentfolder . $filename;
        touch($file);

        $h5pcontentid = 3;

        // Check the file doesn't exist in Moodle files.
        $this->assertFalse($this->h5p_fs_fs->file_exists($this->h5p_fs_context->id, file_storage::COMPONENT,
            file_storage::CONTENT_FILEAREA, $h5pcontentid, $filepath, $filename));

        // Set get_file method accessibility.
        $method = new ReflectionMethod(file_storage::class, 'move_file');

        $method->invoke(new file_storage(), $file, $h5pcontentid);

        // Check the file exist in Moodle files.
        $this->assertTrue($this->h5p_fs_fs->file_exists($this->h5p_fs_context->id, file_storage::COMPONENT,
            file_storage::CONTENT_FILEAREA, $h5pcontentid, $filepath, $filename));
    }

    /**
     * Test that a file is copied from another H5P content or the H5P editor.
     *
     * @return void
     */
    public function test_cloneContentFile(): void {

        $admin = get_admin();
        $usercontext = \context_user::instance($admin->id);
        $this->setUser($admin);
        // Upload a file to the editor.
        $file = 'images/fake.jpg';
        $filepath = '/'.dirname($file).'/';
        $filename = basename($file);

        $content = 'abcd';

        $filerecord = array(
            'contextid' => $usercontext->id,
            'component' => 'user',
            'filearea'  => 'draft',
            'itemid'    => 0,
            'filepath'  => $filepath,
            'filename'  => $filename,
        );

        $this->h5p_fs_fs->create_file_from_string($filerecord, $content);

        // Target H5P content, where the file will be cloned.
        $targetcontent = new \stdClass();
        $targetcontent->id = 999;

        // Check the file doesn't exists before cloning.
        $this->assertFalse($this->h5p_fs_fs->get_file($this->h5p_fs_context->id, file_storage::COMPONENT,
            file_storage::CONTENT_FILEAREA, $targetcontent->id, $filepath, $filename));

        // Copy file from the editor.
        $this->h5p_file_storage->cloneContentFile($file, 'editor', $targetcontent);

        // Check the file exists after cloning.
        $this->assertInstanceOf(\stored_file::class, $this->h5p_fs_fs->get_file($this->h5p_fs_context->id, file_storage::COMPONENT,
            file_storage::CONTENT_FILEAREA, $targetcontent->id, $filepath, $filename));

        // Simulate that an H5P content, with id $sourcecontentid, has a file.
        $file = 'images/fake2.jpg';
        $filepath = '/'.dirname($file).'/';
        $filename = basename($file);

        $sourcecontentid = 111;
        $filerecord['contextid'] = $this->h5p_fs_context->id;
        $filerecord['component'] = file_storage::COMPONENT;
        $filerecord['filearea'] = file_storage::CONTENT_FILEAREA;
        $filerecord['itemid'] = $sourcecontentid;
        $filerecord['filepath'] = $filepath;
        $filerecord['filename'] = $filename;

        $this->h5p_fs_fs->create_file_from_string($filerecord, $content);

        // Check the file doesn't exists before cloning.
        $this->assertFalse($this->h5p_fs_fs->get_file($this->h5p_fs_context->id, file_storage::COMPONENT,
            file_storage::CONTENT_FILEAREA, $targetcontent->id, $filepath, $filename));

        // Copy file from another H5P content.
        $this->h5p_file_storage->cloneContentFile($file, $sourcecontentid, $targetcontent);

        // Check the file exists after cloning.
        $this->assertInstanceOf(\stored_file::class, $this->h5p_fs_fs->get_file($this->h5p_fs_context->id, file_storage::COMPONENT,
            file_storage::CONTENT_FILEAREA, $targetcontent->id, $filepath, $filename));
    }

    /**
     * Test that a given file exists in an H5P content.
     *
     * @return void
     */
    public function test_getContentFile(): void {

        $file = 'img/fake.png';
        $contentid = 3;

        // Add a file to a H5P content.
        $this->h5p_generator->create_content_file($file, file_storage::CONTENT_FILEAREA, $contentid);

        // Get an existing file id.
        $fileid = $this->h5p_file_storage->getContentFile($file, $contentid);
        $this->assertNotNull($fileid);

        // Try to get a nonexistent file.
        $fileid = $this->h5p_file_storage->getContentFile($file, 5);
        $this->assertNull($fileid);
    }

    /**
     * Tests that the content folder of an H5P content is imported in the Moodle filesystem.
     */
    public function test_moveContentDiretory(): void {

        // Create temp folder.
        $tempfolder = make_request_directory(false);

        // Create H5P content folder.
        $h5pcontentfolder = $tempfolder . '/fakeH5Pcontent';
        $contentfolder = $h5pcontentfolder . '/content';
        if (!check_dir_exists($contentfolder, true, true)) {
            throw new moodle_exception('error_creating_temp_dir', 'error', $contentfolder);
        }

        // Add content.json file.
        touch($contentfolder . 'content.json');

        // Create several folders and files inside content folder.
        $filesexpected = array();
        $numfolders = random_int(2, 5);
        for ($numfolder = 1; $numfolder < $numfolders; $numfolder++) {
            $foldername = '/folder' . $numfolder;
            $newfolder = $contentfolder . $foldername;
            if (!check_dir_exists($newfolder, true, true)) {
                throw new moodle_exception('error_creating_temp_dir', 'error', $newfolder);
            }
            $numfiles = random_int(2, 5);
            for ($numfile = 1; $numfile < $numfiles; $numfile++) {
                $filename = '/file' . $numfile . '.ext';
                touch($newfolder . $filename);
                $filesexpected[] = $foldername . $filename;
            }
        }

        $targeth5pcontentid = 111;
        $this->h5p_file_storage->moveContentDirectory($h5pcontentfolder, $targeth5pcontentid);

        // Get database records.
        $files = $this->h5p_fs_fs->get_area_files(
            $this->h5p_fs_context->id,
            file_storage::COMPONENT,
            file_storage::CONTENT_FILEAREA,
            $targeth5pcontentid,
            'filepath, filename',
            false
        );

        $filepaths = array_map(static function(stored_file $file): string {
            return $file->get_filepath() . $file->get_filename();
        }, $files);

        // Check that created files match with database records.
        $this->assertEquals($filesexpected, array_values($filepaths));
    }

    /**
     * Test that an H5P content file is removed.
     */
    public function test_removeContentFile(): void {

        $file = 'img/fake.png';
        $filepath = '/' . dirname($file) . '/';
        $filename = basename($file);
        $h5pcontentid = 3;

        // Add a file to a H5P content.
        $this->h5p_generator->create_content_file($file, file_storage::CONTENT_FILEAREA, $h5pcontentid);

        // Check the file exists.
        $this->assertTrue($this->h5p_fs_fs->file_exists($this->h5p_fs_context->id, file_storage::COMPONENT,
            file_storage::CONTENT_FILEAREA, $h5pcontentid, $filepath, $filename));

        $this->h5p_file_storage->removeContentFile($file, $h5pcontentid);

        // Check the file doesn't exists.
        $this->assertFalse($this->h5p_fs_fs->file_exists($this->h5p_fs_context->id, file_storage::COMPONENT,
            file_storage::CONTENT_FILEAREA, $h5pcontentid, $filepath, $filename));
    }

    /**
     * Test H5P custom styles generation.
     */
    public function test_generate_custom_styles(): void {
        \set_config('h5pcustomcss', '.debug { color: #fab; }', 'core_h5p');
        $h5pfsrc = new \ReflectionClass(file_storage::class);
        $customcssfilename = $h5pfsrc->getConstant('CUSTOM_CSS_FILENAME');

        // Test 'h5pcustomcss' with data.
        file_storage::generate_custom_styles();

        $this->assertTrue($this->h5p_fs_fs->file_exists(
            \context_system::instance()->id,
            file_storage::COMPONENT,
            file_storage::CSS_FILEAREA,
            0,
            '/',
            $customcssfilename)
        );

        $cssfile = $this->h5p_fs_fs->get_file(
            \context_system::instance()->id,
            file_storage::COMPONENT,
            file_storage::CSS_FILEAREA,
            0,
            '/',
            $customcssfilename
        );
        $this->assertInstanceOf('stored_file', $cssfile);

        $csscontents = $cssfile->get_content();
        $this->assertEquals($csscontents, '.debug { color: #fab; }');

        // Test 'h5pcustomcss' without data.
        \set_config('h5pcustomcss', '', 'core_h5p');
        file_storage::generate_custom_styles();
        $this->assertFalse($this->h5p_fs_fs->file_exists(
            \context_system::instance()->id,
            file_storage::COMPONENT,
            file_storage::CSS_FILEAREA,
            0,
            '/',
            $customcssfilename)
        );
    }

    /**
     * Test H5P custom styles retrieval.
     */
    public function test_get_custom_styles(): void {
        global $CFG;
        $css = '.debug { color: #fab; }';
        $cssurl = $CFG->wwwroot . '/pluginfile.php/1/core_h5p/css/custom_h5p.css';
        \set_config('h5pcustomcss', $css, 'core_h5p');
        $h5pfsrc = new \ReflectionClass(file_storage::class);
        $customcssfilename = $h5pfsrc->getConstant('CUSTOM_CSS_FILENAME');

        // Normal operation without data.
        \set_config('h5pcustomcss', '', 'core_h5p');
        file_storage::generate_custom_styles();
        $style = file_storage::get_custom_styles();
        $this->assertNull($style);

        // Normal operation with data.
        \set_config('h5pcustomcss', $css, 'core_h5p');
        file_storage::generate_custom_styles();
        $style = file_storage::get_custom_styles();

        $this->assertNotEmpty($style);
        $this->assertEquals($style['cssurl']->out(), $cssurl);
        $this->assertEquals($style['cssversion'], md5($css));

        // No CSS set when there is a file.
        \set_config('h5pcustomcss', '', 'core_h5p');
        try {
            $style = file_storage::get_custom_styles();
            $this->fail('moodle_exception for when there is no CSS and yet there is a file, was not thrown');
        } catch (\moodle_exception $me) {
            $this->assertEquals(
                'The H5P \'h5pcustomcss\' setting is empty and yet the custom CSS file \''.$customcssfilename.'\' exists.',
                $me->errorcode
            );
        }
        \set_config('h5pcustomcss', $css, 'core_h5p'); // Reset for next assertion.

        // No CSS file when there is CSS.
        $cssfile = $this->h5p_fs_fs->get_file(
            \context_system::instance()->id,
            file_storage::COMPONENT,
            file_storage::CSS_FILEAREA,
            0,
            '/',
            $customcssfilename
        );
        $cssfile->delete();
        try {
            $style = file_storage::get_custom_styles();
            $this->fail('moodle_exception for when there is CSS and yet there is a file, was not thrown');
        } catch (\moodle_exception $me) {
            $this->assertEquals(
                'The H5P custom CSS file \''.$customcssfilename.
                '\' does not exist and yet there is CSS in the \'h5pcustomcss\' setting.',
                $me->errorcode
            );
        }
    }
}

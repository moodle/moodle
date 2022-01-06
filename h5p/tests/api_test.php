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
 * Testing the H5P API.
 *
 * @package    core_h5p
 * @category   test
 * @copyright  2020 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types = 1);

namespace core_h5p;

defined('MOODLE_INTERNAL') || die();

/**
 * Test class covering the H5P API.
 *
 * @package    core_h5p
 * @copyright  2020 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class api_testcase extends \advanced_testcase {

    /**
     * Test the behaviour of delete_library().
     *
     * @dataProvider  delete_library_provider
     * @param  string $libraryname          Machine name of the library to delete.
     * @param  int    $expectedh5p          Total of H5P contents expected after deleting the library.
     * @param  int    $expectedlibraries    Total of H5P libraries expected after deleting the library.
     * @param  int    $expectedcontents     Total of H5P content_libraries expected after deleting the library.
     * @param  int    $expecteddependencies Total of H5P library dependencies expected after deleting the library.
     */
    public function test_delete_library(string $libraryname, int $expectedh5p, int $expectedlibraries,
            int $expectedcontents, int $expecteddependencies): void {
        global $DB;

        $this->setRunTestInSeparateProcess(true);
        $this->resetAfterTest();

        // Generate h5p related data.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_h5p');
        $generator->generate_h5p_data();
        $generator->create_library_record('H5P.TestingLibrary', 'TestingLibrary', 1, 0);

        // Check the current content in H5P tables is the expected.
        $counth5p = $DB->count_records('h5p');
        $counth5plibraries = $DB->count_records('h5p_libraries');
        $counth5pcontents = $DB->count_records('h5p_contents_libraries');
        $counth5pdependencies = $DB->count_records('h5p_library_dependencies');

        $this->assertSame(1, $counth5p);
        $this->assertSame(7, $counth5plibraries);
        $this->assertSame(5, $counth5pcontents);
        $this->assertSame(7, $counth5pdependencies);

        // Delete this library.
        $factory = new factory();
        $library = $DB->get_record('h5p_libraries', ['machinename' => $libraryname]);
        if ($library) {
            api::delete_library($factory, $library);
        }

        // Check the expected libraries and content have been removed.
        $counth5p = $DB->count_records('h5p');
        $counth5plibraries = $DB->count_records('h5p_libraries');
        $counth5pcontents = $DB->count_records('h5p_contents_libraries');
        $counth5pdependencies = $DB->count_records('h5p_library_dependencies');

        $this->assertSame($expectedh5p, $counth5p);
        $this->assertSame($expectedlibraries, $counth5plibraries);
        $this->assertSame($expectedcontents, $counth5pcontents);
        $this->assertSame($expecteddependencies, $counth5pdependencies);
    }

    /**
     * Data provider for test_delete_library().
     *
     * @return array
     */
    public function delete_library_provider(): array {
        return [
            'Delete MainLibrary' => [
                'MainLibrary',
                0,
                6,
                0,
                4,
            ],
            'Delete Library1' => [
                'Library1',
                0,
                5,
                0,
                1,
            ],
            'Delete Library2' => [
                'Library2',
                0,
                4,
                0,
                1,
            ],
            'Delete Library3' => [
                'Library3',
                0,
                4,
                0,
                0,
            ],
            'Delete Library4' => [
                'Library4',
                0,
                4,
                0,
                1,
            ],
            'Delete Library5' => [
                'Library5',
                0,
                3,
                0,
                0,
            ],
            'Delete a library without dependencies' => [
                'H5P.TestingLibrary',
                1,
                6,
                5,
                7,
            ],
            'Delete unexisting library' => [
                'LibraryX',
                1,
                7,
                5,
                7,
            ],
        ];
    }

    /**
     * Test the behaviour of get_dependent_libraries().
     *
     * @dataProvider  get_dependent_libraries_provider
     * @param  string $libraryname     Machine name of the library to delete.
     * @param  int    $expectedvalue   Total of H5P required libraries expected.
     */
    public function test_get_dependent_libraries(string $libraryname, int $expectedvalue): void {
        global $DB;

        $this->resetAfterTest();

        // Generate h5p related data.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_h5p');
        $generator->generate_h5p_data();
        $generator->create_library_record('H5P.TestingLibrary', 'TestingLibrary', 1, 0);

        // Get required libraries.
        $library = $DB->get_record('h5p_libraries', ['machinename' => $libraryname], 'id');
        if ($library) {
            $libraries = api::get_dependent_libraries((int)$library->id);
        } else {
            $libraries = [];
        }

        $this->assertCount($expectedvalue, $libraries);
    }

    /**
     * Data provider for test_get_dependent_libraries().
     *
     * @return array
     */
    public function get_dependent_libraries_provider(): array {
        return [
            'Main library of a content' => [
                'MainLibrary',
                0,
            ],
            'Library1' => [
                'Library1',
                1,
            ],
            'Library2' => [
                'Library2',
                2,
            ],
            'Library without dependencies' => [
                'H5P.TestingLibrary',
                0,
            ],
            'Unexisting library' => [
                'LibraryX',
                0,
            ],
        ];
    }

    /**
     * Test the behaviour of get_library().
     *
     * @dataProvider  get_library_provider
     * @param  string $libraryname     Machine name of the library to delete.
     * @param  bool   $emptyexpected   Wether the expected result is empty or not.
     */
    public function test_get_library(string $libraryname, bool $emptyexpected): void {
        global $DB;

        $this->resetAfterTest();

        // Generate h5p related data.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_h5p');
        $generator->generate_h5p_data();
        $generator->create_library_record('H5P.TestingLibrary', 'TestingLibrary', 1, 0);

        // Get the library identifier.
        $library = $DB->get_record('h5p_libraries', ['machinename' => $libraryname], 'id');
        if ($library) {
            $result = api::get_library((int)$library->id);
        } else {
            $result = null;
        }

        if ($emptyexpected) {
            $this->assertEmpty($result);
        } else {
            $this->assertEquals($library->id, $result->id);
            $this->assertEquals($libraryname, $result->machinename);
        }

    }

    /**
     * Data provider for test_get_library().
     *
     * @return array
     */
    public function get_library_provider(): array {
        return [
            'Main library of a content' => [
                'MainLibrary',
                false,
            ],
            'Library1' => [
                'Library1',
                false,
            ],
            'Library without dependencies' => [
                'H5P.TestingLibrary',
                false,
            ],
            'Unexisting library' => [
                'LibraryX',
                true,
            ],
        ];
    }

    /**
     * Test the behaviour of get_content_from_pluginfile_url().
     */
    public function test_get_content_from_pluginfile_url(): void {
        $this->setRunTestInSeparateProcess(true);
        $this->resetAfterTest();
        $factory = new factory();

        // Create the H5P data.
        $filename = 'find-the-words.h5p';
        $path = __DIR__ . '/fixtures/' . $filename;
        $fakefile = helper::create_fake_stored_file_from_path($path);
        $config = (object)[
            'frame' => 1,
            'export' => 1,
            'embed' => 0,
            'copyright' => 0,
        ];

        // Get URL for this H5P content file.
        $syscontext = \context_system::instance();
        $url = \moodle_url::make_pluginfile_url(
            $syscontext->id,
            \core_h5p\file_storage::COMPONENT,
            'unittest',
            $fakefile->get_itemid(),
            '/',
            $filename
        );

        // Scenario 1: Get the H5P for this URL and check there isn't any existing H5P (because it hasn't been saved).
        list($newfile, $h5p) = api::get_content_from_pluginfile_url($url->out());
        $this->assertEquals($fakefile->get_pathnamehash(), $newfile->get_pathnamehash());
        $this->assertEquals($fakefile->get_contenthash(), $newfile->get_contenthash());
        $this->assertFalse($h5p);

        // Scenario 2: Save the H5P and check now the H5P is exactly the same as the original one.
        $h5pid = helper::save_h5p($factory, $fakefile, $config);
        list($newfile, $h5p) = api::get_content_from_pluginfile_url($url->out());

        $this->assertEquals($h5pid, $h5p->id);
        $this->assertEquals($fakefile->get_pathnamehash(), $h5p->pathnamehash);
        $this->assertEquals($fakefile->get_contenthash(), $h5p->contenthash);

        // Scenario 3: Get the H5P for an unexisting H5P file.
        $url = \moodle_url::make_pluginfile_url(
            $syscontext->id,
            \core_h5p\file_storage::COMPONENT,
            'unittest',
            $fakefile->get_itemid(),
            '/',
            'unexisting.h5p'
        );
        list($newfile, $h5p) = api::get_content_from_pluginfile_url($url->out());
        $this->assertFalse($newfile);
        $this->assertFalse($h5p);
    }

    /**
     * Test the behaviour of create_content_from_pluginfile_url().
     */
    public function test_create_content_from_pluginfile_url(): void {
        global $DB;

        $this->setRunTestInSeparateProcess(true);
        $this->resetAfterTest();
        $factory = new factory();

        // Create the H5P data.
        $filename = 'find-the-words.h5p';
        $path = __DIR__ . '/fixtures/' . $filename;
        $fakefile = helper::create_fake_stored_file_from_path($path);
        $config = (object)[
            'frame' => 1,
            'export' => 1,
            'embed' => 0,
            'copyright' => 0,
        ];

        // Get URL for this H5P content file.
        $syscontext = \context_system::instance();
        $url = \moodle_url::make_pluginfile_url(
            $syscontext->id,
            \core_h5p\file_storage::COMPONENT,
            'unittest',
            $fakefile->get_itemid(),
            '/',
            $filename
        );

        // Scenario 1: Create the H5P from this URL and check the content is exactly the same as the fake file.
        $messages = new \stdClass();
        list($newfile, $h5pid) = api::create_content_from_pluginfile_url($url->out(), $config, $factory, $messages);
        $this->assertNotFalse($h5pid);
        $h5p = $DB->get_record('h5p', ['id' => $h5pid]);
        $this->assertEquals($fakefile->get_pathnamehash(), $h5p->pathnamehash);
        $this->assertEquals($fakefile->get_contenthash(), $h5p->contenthash);
        $this->assertTrue(empty($messages->error));
        $this->assertTrue(empty($messages->info));

        // Scenario 2: Create the H5P for an unexisting H5P file.
        $url = \moodle_url::make_pluginfile_url(
            $syscontext->id,
            \core_h5p\file_storage::COMPONENT,
            'unittest',
            $fakefile->get_itemid(),
            '/',
            'unexisting.h5p'
        );
        list($newfile, $h5p) = api::create_content_from_pluginfile_url($url->out(), $config, $factory, $messages);
        $this->assertFalse($newfile);
        $this->assertFalse($h5p);
        $this->assertTrue(empty($messages->error));
        $this->assertTrue(empty($messages->info));
    }

    /**
     * Test the behaviour of delete_content_from_pluginfile_url().
     */
    public function test_delete_content_from_pluginfile_url(): void {
        global $DB;

        $this->setRunTestInSeparateProcess(true);
        $this->resetAfterTest();
        $factory = new factory();

        // Create the H5P data.
        $filename = 'find-the-words.h5p';
        $path = __DIR__ . '/fixtures/' . $filename;
        $fakefile = helper::create_fake_stored_file_from_path($path);
        $config = (object)[
            'frame' => 1,
            'export' => 1,
            'embed' => 0,
            'copyright' => 0,
        ];

        // Get URL for this H5P content file.
        $syscontext = \context_system::instance();
        $url = \moodle_url::make_pluginfile_url(
            $syscontext->id,
            \core_h5p\file_storage::COMPONENT,
            'unittest',
            $fakefile->get_itemid(),
            '/',
            $filename
        );

        // Scenario 1: Try to remove the H5P content for an undeployed file.
        list($newfile, $h5p) = api::get_content_from_pluginfile_url($url->out());
        $this->assertEquals(0, $DB->count_records('h5p'));
        api::delete_content_from_pluginfile_url($url->out(), $factory);
        $this->assertEquals(0, $DB->count_records('h5p'));

        // Scenario 2: Deploy an H5P from this URL, check it's created, remove it and check it has been removed as expected.
        $this->assertEquals(0, $DB->count_records('h5p'));

        $messages = new \stdClass();
        list($newfile, $h5pid) = api::create_content_from_pluginfile_url($url->out(), $config, $factory, $messages);
        $this->assertEquals(1, $DB->count_records('h5p'));

        api::delete_content_from_pluginfile_url($url->out(), $factory);
        $this->assertEquals(0, $DB->count_records('h5p'));

        // Scenario 3: Try to remove the H5P for an unexisting H5P URL.
        $url = \moodle_url::make_pluginfile_url(
            $syscontext->id,
            \core_h5p\file_storage::COMPONENT,
            'unittest',
            $fakefile->get_itemid(),
            '/',
            'unexisting.h5p'
        );
        $this->assertEquals(0, $DB->count_records('h5p'));
        api::delete_content_from_pluginfile_url($url->out(), $factory);
        $this->assertEquals(0, $DB->count_records('h5p'));
    }

    /**
     * Test the behaviour of get_export_info_from_context_id().
     */
    public function test_get_export_info_from_context_id(): void {
        global $DB;

        $this->setRunTestInSeparateProcess(true);
        $this->resetAfterTest();
        $factory = new factory();

        // Create the H5P data.
        $filename = 'find-the-words.h5p';
        $syscontext = \context_system::instance();

        // Test scenario 1: H5P exists and deployed.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_h5p');
        $fakeexportfile = $generator->create_export_file($filename,
            $syscontext->id,
            \core_h5p\file_storage::COMPONENT,
            \core_h5p\file_storage::EXPORT_FILEAREA);

        $exportfile = api::get_export_info_from_context_id($syscontext->id,
            $factory,
            \core_h5p\file_storage::COMPONENT,
            \core_h5p\file_storage::EXPORT_FILEAREA);
        $this->assertEquals($fakeexportfile['filename'], $exportfile['filename']);
        $this->assertEquals($fakeexportfile['filepath'], $exportfile['filepath']);
        $this->assertEquals($fakeexportfile['filesize'], $exportfile['filesize']);
        $this->assertEquals($fakeexportfile['timemodified'], $exportfile['timemodified']);
        $this->assertEquals($fakeexportfile['fileurl'], $exportfile['fileurl']);

        // Test scenario 2: H5P exist, deployed but the content has changed.
        // We need to change the contenthash to simulate the H5P file was changed.
        $h5pfile = $DB->get_record('h5p', []);
        $h5pfile->contenthash = sha1('testedit');
        $DB->update_record('h5p', $h5pfile);
        $exportfile = api::get_export_info_from_context_id($syscontext->id,
            $factory,
            \core_h5p\file_storage::COMPONENT,
            \core_h5p\file_storage::EXPORT_FILEAREA);
        $this->assertNull($exportfile);

        // Tests scenario 3: H5P is not deployed.
        // We need to delete the H5P record to simulate the H5P was not deployed.
        $DB->delete_records('h5p', ['id' => $h5pfile->id]);
        $exportfile = api::get_export_info_from_context_id($syscontext->id,
            $factory,
            \core_h5p\file_storage::COMPONENT,
            \core_h5p\file_storage::EXPORT_FILEAREA);
        $this->assertNull($exportfile);
    }
}

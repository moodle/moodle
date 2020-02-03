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
}

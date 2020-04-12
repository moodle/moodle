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
 * Testing the H5peditorStorage interface implementation.
 *
 * @package    core_h5p
 * @category   test
 * @copyright  2020 Victor Deniz <victor@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_h5p;

use core_h5p\local\library\autoloader;

/**
 *
 * Test class covering the H5peditorStorage interface implementation.
 *
 * @package    core_h5p
 * @copyright  2020 Victor Deniz <victor@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @runTestsInSeparateProcesses
 */
class editor_framework_testcase extends \advanced_testcase {

    /** @var editor_framework H5P editor_framework instance */
    protected $editorframework;

    /**
     * Set up function for tests.
     */
    protected function setUp() {
        parent::setUp();

        autoloader::register();

        $this->editorframework = new editor_framework();
    }

    /**
     * Test that the method getLibraries get the specified libraries or all the content types (runnable = 1).
     */
    public function test_getLibraries(): void {
        $this->resetAfterTest(true);

        $generator = \testing_util::get_data_generator();
        $h5pgenerator = $generator->get_plugin_generator('core_h5p');

        // Generate some h5p related data.
        $data = $h5pgenerator->generate_h5p_data();

        $expectedlibraries = [];
        foreach ($data as $key => $value) {
            if (isset($value->data)) {
                $value->data->name = $value->data->machinename;
                $value->data->majorVersion = $value->data->majorversion;
                $value->data->minorVersion = $value->data->minorversion;
                $expectedlibraries[$value->data->title] = $value->data;
            }
        }
        ksort($expectedlibraries);

        // Get all libraries.
        $libraries = $this->editorframework->getLibraries();
        foreach ($libraries as $library) {
            $actuallibraries[] = $library->title;
        }
        sort($actuallibraries);

        $this->assertEquals(array_keys($expectedlibraries), $actuallibraries);

        // Get a subset of libraries.
        $librariessubset = array_slice($expectedlibraries, 0, 4);

        $actuallibraries = [];
        $libraries = $this->editorframework->getLibraries($librariessubset);
        foreach ($libraries as $library) {
            $actuallibraries[] = $library->title;
        }

        $this->assertEquals(array_keys($librariessubset), $actuallibraries);
    }
}

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
 * Testing the H5P core methods.
 *
 * @package    core_h5p
 * @category   test
 * @copyright  2019 Victor Deniz <victor@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_h5p\local\tests;

use core_h5p\factory;

defined('MOODLE_INTERNAL') || die();

/**
 * Test class covering the H5PFileStorage interface implementation.
 *
 * @package    core_h5p
 * @copyright  2019 Victor Deniz <victor@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @runTestsInSeparateProcesses
 */
class h5p_core_test extends \advanced_testcase {

    protected function setup() {
        parent::setUp();

        $factory = new factory();
        $this->core = $factory->get_core();
    }

    /**
     * Check that given an H5P content type machine name, the required library are fetched and installed from the official H5P
     * repository.
     */
    public function test_fetch_content_type(): void {
        global $DB;

        $this->resetAfterTest(true);

        if (!PHPUNIT_LONGTEST) {
            $this->markTestSkipped('PHPUNIT_LONGTEST is not defined');
        }

        // Get info of latest content types versions.
        $contenttypes = $this->core->get_latest_content_types()->contentTypes;
        // We are installing the first content type.
        $librarydata = $contenttypes[0];

        $library = [
                'machineName' => $librarydata->id,
                'majorVersion' => $librarydata->version->major,
                'minorVersion' => $librarydata->version->minor,
                'patchVersion' => $librarydata->version->patch,
        ];

        // Verify that the content type is not yet installed.
        $conditions['machinename'] = $library['machineName'];
        $typeinstalled = $DB->count_records('h5p_libraries', $conditions);

        $this->assertEquals(0, $typeinstalled);

        // Fetch the content type.
        $this->core->fetch_content_type($library);

        // Check that the content type is now installed.
        $typeinstalled = $DB->get_record('h5p_libraries', $conditions);
        $this->assertEquals($librarydata->id, $typeinstalled->machinename);
        $this->assertEquals($librarydata->coreApiVersionNeeded->major, $typeinstalled->coremajor);
        $this->assertEquals($librarydata->coreApiVersionNeeded->minor, $typeinstalled->coreminor);
    }

    /**
     * Test that latest version of non installed H5P content type libraries are fetched and installed from the
     * official H5P repository. To speed up the test, only if checked that one content type is installed.
     */
    public function test_fetch_latest_content_types(): void {
        global $DB;

        $this->resetAfterTest(true);

        if (!PHPUNIT_LONGTEST) {
            $this->markTestSkipped('PHPUNIT_LONGTEST is not defined');
        }

        $contentfiles = $DB->count_records('h5p_libraries');

        // Initially there are no h5p records in database.
        $this->assertEquals(0, $contentfiles);

        // Fetch generator.
        $generator = \testing_util::get_data_generator();
        $h5pgenerator = $generator->get_plugin_generator('core_h5p');

        // Get info of latest content types versions.
        [$contenttypes, $contenttoinstall] = $h5pgenerator->create_content_types(1);
        // Number of H5P content types.
        $numcontenttypes = count($contenttypes) + count($contenttoinstall);

        $contenttoinstall = $contenttoinstall[0];

        // Content type libraries has runnable set to 1.
        $conditions = ['runnable' => 1];
        $contentfiles = $DB->get_records('h5p_libraries', $conditions, '', 'machinename');

        // There is a record for each installed content type, except the one that was hold for later.
        $this->assertEquals($numcontenttypes - 1, count($contentfiles));
        $this->assertArrayNotHasKey($contenttoinstall->id, $contentfiles);

        $result = $this->core->fetch_latest_content_types();

        $contentfiles = $DB->get_records('h5p_libraries', $conditions, '', 'machinename');

        // There is a new record for the new installed content type.
        $this->assertCount($numcontenttypes, $contentfiles);
        $this->assertArrayHasKey($contenttoinstall->id, $contentfiles);
        $this->assertCount(1, $result->typesinstalled);
        $this->assertStringStartsWith($contenttoinstall->id, $result->typesinstalled[0]['name']);

        // New execution doesn't install any content type.
        $result = $this->core->fetch_latest_content_types();

        $contentfiles = $DB->get_records('h5p_libraries', $conditions, '', 'machinename');

        $this->assertEquals($numcontenttypes, count($contentfiles));
        $this->assertCount(0, $result->typesinstalled);
    }
}

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

namespace core_h5p;

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
        global $CFG;
        parent::setUp();

        autoloader::register();

        require_once($CFG->libdir . '/tests/fixtures/testable_core_h5p.php');

        $factory = new h5p_test_factory();
        $this->core = $factory->get_core();
        $this->core->set_endpoint($this->getExternalTestFileUrl(''));
    }

    /**
     * Check that given an H5P content type machine name, the required library are fetched and installed from the official H5P
     * repository.
     */
    public function test_fetch_content_type(): void {
        global $DB;

        if (!PHPUNIT_LONGTEST) {
            $this->markTestSkipped('PHPUNIT_LONGTEST is not defined');
        }

        $this->resetAfterTest(true);

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

        if (!PHPUNIT_LONGTEST) {
            $this->markTestSkipped('PHPUNIT_LONGTEST is not defined');
        }

        $this->resetAfterTest(true);

        $contentfiles = $DB->count_records('h5p_libraries');

        // Initially there are no h5p records in database.
        $this->assertEquals(0, $contentfiles);

        $contenttypespending = ['H5P.Accordion'];

        // Fetch generator.
        $generator = \testing_util::get_data_generator();
        $h5pgenerator = $generator->get_plugin_generator('core_h5p');

        // Get info of latest content types versions.
        [$installedtypes, $typesnotinstalled] = $h5pgenerator->create_content_types($contenttypespending, $this->core);
        // Number of H5P content types.
        $numcontenttypes = $installedtypes + $typesnotinstalled;

        // Content type libraries has runnable set to 1.
        $conditions = ['runnable' => 1];
        $contentfiles = $DB->get_records('h5p_libraries', $conditions, '', 'machinename');

        // There is a record for each installed content type, except the one that was hold for later.
        $this->assertEquals($numcontenttypes - 1, count($contentfiles));
        $this->assertArrayNotHasKey($contenttypespending[0], $contentfiles);

        $result = $this->core->fetch_latest_content_types();

        $contentfiles = $DB->get_records('h5p_libraries', $conditions, '', 'machinename');

        // There is a new record for the new installed content type.
        $this->assertCount($numcontenttypes, $contentfiles);
        $this->assertArrayHasKey($contenttypespending[0], $contentfiles);
        $this->assertCount(1, $result->typesinstalled);
        $this->assertStringStartsWith($contenttypespending[0], $result->typesinstalled[0]['name']);

        // New execution doesn't install any content type.
        $result = $this->core->fetch_latest_content_types();

        $contentfiles = $DB->get_records('h5p_libraries', $conditions, '', 'machinename');

        $this->assertEquals($numcontenttypes, count($contentfiles));
        $this->assertCount(0, $result->typesinstalled);
    }
}

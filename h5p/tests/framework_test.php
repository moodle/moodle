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

use core_collator;
use Moodle\H5PCore;
use Moodle\H5PDisplayOptionBehaviour;

// phpcs:disable moodle.NamingConventions.ValidFunctionName.LowercaseMethod

/**
 *
 * Test class covering the H5PFrameworkInterface interface implementation.
 *
 * @package    core_h5p
 * @category   test
 * @copyright  2019 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core_h5p\framework
 * @runTestsInSeparateProcesses
 */
final class framework_test extends \advanced_testcase {

    /** @var \core_h5p\framework */
    private $framework;

    /**
     * Set up function for tests.
     */
    public function setUp(): void {
        parent::setUp();
        $factory = new \core_h5p\factory();
        $this->framework = $factory->get_framework();
    }

    /**
     * Test the behaviour of getPlatformInfo().
     */
    public function test_getPlatformInfo(): void {
        global $CFG;

        $platforminfo = $this->framework->getPlatformInfo();

        $expected = array(
            'name' => 'Moodle',
            'version' => $CFG->version,
            'h5pVersion' => $CFG->version
        );

        $this->assertEquals($expected, $platforminfo);
    }

    /**
     * Test the behaviour of fetchExternalData() when the store path is not defined.
     *
     * This test is intensive and requires downloading content of an external file,
     * therefore it might take longer time to execute.
     * In order to execute this test PHPUNIT_LONGTEST should be set to true in phpunit.xml or directly in config.php.
     */
    public function test_fetchExternalData_no_path_defined(): void {

        if (!PHPUNIT_LONGTEST) {
            $this->markTestSkipped('PHPUNIT_LONGTEST is not defined');
        }

        $this->resetAfterTest();

        $library = 'H5P.Accordion';
        // Provide a valid URL to an external H5P content.
        $url = $this->getExternalTestFileUrl('/'.$library.'.h5p');

        // Test fetching an external H5P content without defining a path to where the file should be stored.
        $data = $this->framework->fetchExternalData($url, null, true);

        // The response should not be empty and return true if the file was successfully downloaded.
        $this->assertNotEmpty($data);
        $this->assertTrue($data);

        $h5pfolderpath = $this->framework->getUploadedH5pFolderPath();
        // The uploaded file should exist on the filesystem.
        $this->assertTrue(file_exists($h5pfolderpath . '.h5p'));
    }

    /**
     * Test the behaviour of fetchExternalData() when the store path is defined.
     *
     * This test is intensive and requires downloading content of an external file,
     * therefore it might take longer time to execute.
     * In order to execute this test PHPUNIT_LONGTEST should be set to true in phpunit.xml or directly in config.php.
     */
    public function test_fetchExternalData_path_defined(): void {
        global $CFG;

        if (!PHPUNIT_LONGTEST) {
            $this->markTestSkipped('PHPUNIT_LONGTEST is not defined');
        }

        $this->resetAfterTest();

        $library = 'H5P.Accordion';
        // Provide a valid URL to an external H5P content.
        $url = $this->getExternalTestFileUrl('/'.$library.'.h5p');

        $h5pfolderpath = $CFG->tempdir . uniqid('/h5p-');

        $data = $this->framework->fetchExternalData($url, null, true, $h5pfolderpath . '.h5p');

        // The response should not be empty and return true if the content has been successfully saved to a file.
        $this->assertNotEmpty($data);
        $this->assertTrue($data);

        // The uploaded file should exist on the filesystem.
        $this->assertTrue(file_exists($h5pfolderpath . '.h5p'));
    }

    /**
     * Test the behaviour of fetchExternalData() when the URL is pointing to an external file that is
     * not an h5p content.
     *
     * This test is intensive and requires downloading content of an external file,
     * therefore it might take longer time to execute.
     * In order to execute this test PHPUNIT_LONGTEST should be set to true in phpunit.xml or directly in config.php.
     */
    public function test_fetchExternalData_url_not_h5p(): void {

        if (!PHPUNIT_LONGTEST) {
            // This test is intensive and requires downloading the content of an external file.
            $this->markTestSkipped('PHPUNIT_LONGTEST is not defined');
        }

        $this->resetAfterTest();

        // Provide an URL to an external file that is not an H5P content file.
        $url = $this->getExternalTestFileUrl('/h5pcontenttypes.json');

        $data = $this->framework->fetchExternalData($url, null, true);

        // The response should not be empty and return true if the content has been successfully saved to a file.
        $this->assertNotEmpty($data);
        $this->assertTrue($data);

        // The uploaded file should exist on the filesystem with it's original extension.
        // NOTE: The file would be later validated by the H5P Validator.
        $h5pfolderpath = $this->framework->getUploadedH5pFolderPath();
        $this->assertTrue(file_exists($h5pfolderpath . '.json'));
    }

    /**
     * Test the behaviour of fetchExternalData() when the URL is invalid.
     */
    public function test_fetchExternalData_url_invalid(): void {
        // Provide an invalid URL to an external file.
        $url = "someprotocol://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf";

        $data = $this->framework->fetchExternalData($url, null, true);

        // The response should be empty.
        $this->assertEmpty($data);
    }

    /**
     * Test the behaviour of setLibraryTutorialUrl().
     */
    public function test_setLibraryTutorialUrl(): void {
        global $DB;

        $this->resetAfterTest();

        $generator = $this->getDataGenerator()->get_plugin_generator('core_h5p');

        // Create several libraries records.
        $lib1 = $generator->create_library_record('Library1', 'Lib1', 1, 0, 1, '', null, 'http://tutorial1.org',
            'http://example.org');
        $lib2 = $generator->create_library_record('Library2', 'Lib2', 2, 0, 1, '', null, 'http://tutorial2.org');
        $lib3 = $generator->create_library_record('Library3', 'Lib3', 3, 0);

        // Check only lib1 tutorial URL is updated.
        $url = 'https://newtutorial.cat';
        $this->framework->setLibraryTutorialUrl($lib1->machinename, $url);

        $libraries = $DB->get_records('h5p_libraries');
        $this->assertEquals($libraries[$lib1->id]->tutorial, $url);
        $this->assertNotEquals($libraries[$lib2->id]->tutorial, $url);

        // Check lib1 tutorial URL is set to null.
        $this->framework->setLibraryTutorialUrl($lib1->machinename, null);

        $libraries = $DB->get_records('h5p_libraries');
        $this->assertCount(3, $libraries);
        $this->assertNull($libraries[$lib1->id]->tutorial);

        // Check no tutorial URL is set if library name doesn't exist.
        $this->framework->setLibraryTutorialUrl('Unexisting library', $url);

        $libraries = $DB->get_records('h5p_libraries');
        $this->assertCount(3, $libraries);
        $this->assertNull($libraries[$lib1->id]->tutorial);
        $this->assertEquals($libraries[$lib2->id]->tutorial, 'http://tutorial2.org');
        $this->assertNull($libraries[$lib3->id]->tutorial);

        // Check tutorial is set as expected when it was null.
        $this->framework->setLibraryTutorialUrl($lib3->machinename, $url);

        $libraries = $DB->get_records('h5p_libraries');
        $this->assertEquals($libraries[$lib3->id]->tutorial, $url);
        $this->assertNull($libraries[$lib1->id]->tutorial);
        $this->assertEquals($libraries[$lib2->id]->tutorial, 'http://tutorial2.org');
    }

    /**
     * Test the behaviour of setErrorMessage().
     */
    public function test_setErrorMessage(): void {
        // Set an error message and an error code.
        $message = "Error message";
        $code = '404';

        // Set an error message.
        $this->framework->setErrorMessage($message, $code);

        // Get the error messages.
        $errormessages = $this->framework->getMessages('error');

        $expected = new \stdClass();
        $expected->code = 404;
        $expected->message = 'Error message';

        $this->assertEquals($expected, $errormessages[0]);
    }

    /**
     * Test the behaviour of setInfoMessage().
     */
    public function test_setInfoMessage(): void {
        $message = "Info message";

        // Set an info message.
        $this->framework->setInfoMessage($message);

        // Get the info messages.
        $infomessages = $this->framework->getMessages('info');

        $expected = 'Info message';

        $this->assertEquals($expected, $infomessages[0]);
    }

    /**
     * Test the behaviour of getMessages() when requesting the info messages.
     */
    public function test_getMessages_info(): void {
        // Set an info message.
        $this->framework->setInfoMessage("Info message");
        // Set an error message.
        $this->framework->setErrorMessage("Error message 1", 404);

        // Get the info messages.
        $infomessages = $this->framework->getMessages('info');

        $expected = 'Info message';

        // Make sure that only the info message has been returned.
        $this->assertCount(1, $infomessages);
        $this->assertEquals($expected, $infomessages[0]);

        $infomessages = $this->framework->getMessages('info');

        // Make sure the info messages have now been removed.
        $this->assertEmpty($infomessages);
    }

    /**
     * Test the behaviour of getMessages() when requesting the error messages.
     */
    public function test_getMessages_error(): void {
        // Set an info message.
        $this->framework->setInfoMessage("Info message");
        // Set an error message.
        $this->framework->setErrorMessage("Error message 1", 404);
        // Set another error message.
        $this->framework->setErrorMessage("Error message 2", 403);

        // Get the error messages.
        $errormessages = $this->framework->getMessages('error');

        // Make sure that only the error messages are being returned.
        $this->assertEquals(2, count($errormessages));

        $expected1 = (object) [
            'code' => 404,
            'message' => 'Error message 1'
        ];

        $expected2 = (object) [
            'code' => 403,
            'message' => 'Error message 2'
        ];

        $this->assertEquals($expected1, $errormessages[0]);
        $this->assertEquals($expected2, $errormessages[1]);

        $errormessages = $this->framework->getMessages('error');

        // Make sure the info messages have now been removed.
        $this->assertEmpty($errormessages);
    }

    /**
     * Test the behaviour of t() when translating existing string that does not require any arguments.
     */
    public function test_t_existing_string_no_args(): void {
        // Existing language string without passed arguments.
        $translation = $this->framework->t('No copyright information available for this content.');

        // Make sure the string translation has been returned.
        $this->assertEquals('No copyright information available for this content.', $translation);
    }

    /**
     * Test the behaviour of t() when translating existing string that does require parameters.
     */
    public function test_t_existing_string_args(): void {
        // Existing language string with passed arguments.
        $translation = $this->framework->t('Illegal option %option in %library',
            ['%option' => 'example', '%library' => 'Test library']);

        // Make sure the string translation has been returned.
        $this->assertEquals('Illegal option example in Test library', $translation);
    }

    /**
     * Test the behaviour of t() when translating non-existent string.
     */
    public function test_t_non_existent_string(): void {
        // Non-existing language string.
        $message = 'Random message %option';

        $translation = $this->framework->t($message);

        // Make sure a debugging message is triggered.
        $this->assertDebuggingCalled("String translation cannot be found. Please add a string definition for '" .
            $message . "' in the core_h5p component.");
        // As the string does not exist in the mapping array, make sure the passed message is returned.
        $this->assertEquals($message, $translation);
    }

    /**
     * Test the behaviour of getLibraryFileUrl() when requesting a file URL from an existing library and
     * the folder name is parsable.
     **/
    public function test_getLibraryFileUrl(): void {
        global $CFG;

        $this->resetAfterTest();

        $generator = $this->getDataGenerator()->get_plugin_generator('core_h5p');
        // Create a library record.
        $lib = $generator->create_library_record('Library', 'Lib', 1, 1);

        $expected = "{$CFG->wwwroot}/pluginfile.php/1/core_h5p/libraries/{$lib->id}/Library-1.1/library.json";

        // Get the URL of a file from an existing library. The provided folder name is parsable.
        $actual = $this->framework->getLibraryFileUrl('Library-1.1', 'library.json');

        // Make sure the expected URL is returned.
        $this->assertEquals($expected, $actual);
    }

    /**
     * Test the behaviour of getLibraryFileUrl() when requesting a file URL from a non-existent library and
     * the folder name is parsable.
     **/
    public function test_getLibraryFileUrl_non_existent_library(): void {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator()->get_plugin_generator('core_h5p');
        // Create a library record.
        $generator->create_library_record('Library', 'Lib', 1, 1);

        // Get the URL of a file from a non-existent library. The provided folder name is parsable.
        $actual = $this->framework->getLibraryFileUrl('Library2-1.1', 'library.json');

        // Make sure a debugging message is triggered.
        $this->assertDebuggingCalled('The library "Library2-1.1" does not exist.');

        // Make sure that an URL is not returned.
        $this->assertEquals(null, $actual);
    }

    /**
     * Test the behaviour of getLibraryFileUrl() when requesting a file URL from an existing library and
     * the folder name is not parsable.
     **/
    public function test_getLibraryFileUrl_not_parsable_folder_name(): void {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator()->get_plugin_generator('core_h5p');
        // Create a library record.
        $generator->create_library_record('Library', 'Lib', 1, 1);

        // Get the URL of a file from an existing library. The provided folder name is not parsable.
        $actual = $this->framework->getLibraryFileUrl('Library1.1', 'library.json');

        // Make sure a debugging message is triggered.
        $this->assertDebuggingCalled(
            'The provided string value "Library1.1" is not a valid name for a library folder.');

        // Make sure that an URL is not returned.
        $this->assertEquals(null, $actual);
    }

    /**
     * Test the behaviour of getLibraryFileUrl() when requesting a file URL from a library that has multiple
     * versions and the folder name is parsable.
     **/
    public function test_getLibraryFileUrl_library_has_multiple_versions(): void {
        global $CFG;

        $this->resetAfterTest();

        $generator = $this->getDataGenerator()->get_plugin_generator('core_h5p');
        // Create library records with a different minor version.
        $lib1 = $generator->create_library_record('Library', 'Lib', 1, 1);
        $lib2 = $generator->create_library_record('Library', 'Lib', 1, 3);

        $expected = "{$CFG->wwwroot}/pluginfile.php/1/core_h5p/libraries/{$lib2->id}/Library-1.3/library.json";

        // Get the URL of a file from an existing library (Library 1.3). The provided folder name is parsable.
        $actual = $this->framework->getLibraryFileUrl('Library-1.3', 'library.json');

        // Make sure the proper URL (from the requested library version) is returned.
        $this->assertEquals($expected, $actual);
    }

    /**
     * Test the behaviour of getLibraryFileUrl() when requesting a file URL from a library that has multiple
     * patch versions and the folder name is parsable.
     **/
    public function test_getLibraryFileUrl_library_has_multiple_patch_versions(): void {
        global $CFG;

        $this->resetAfterTest();

        $generator = $this->getDataGenerator()->get_plugin_generator('core_h5p');
        // Create library records with a different patch version.
        $lib1 = $generator->create_library_record('Library', 'Lib', 1, 1, 2);
        $lib2 = $generator->create_library_record('Library', 'Lib', 1, 1, 4);
        $lib3 = $generator->create_library_record('Library', 'Lib', 1, 1, 3);

        $expected = "{$CFG->wwwroot}/pluginfile.php/1/core_h5p/libraries/{$lib2->id}/Library-1.1/library.json";

        // Get the URL of a file from an existing library. The provided folder name is parsable.
        $actual = $this->framework->getLibraryFileUrl('Library-1.1', 'library.json');

        // Make sure the proper URL (from the latest library patch) is returned.
        $this->assertEquals($expected, $actual);
    }

    /**
     * Test the behaviour of getLibraryFileUrl() when requesting a file URL from a sub-folder
     * of an existing library and the folder name is parsable.
     **/
    public function test_getLibraryFileUrl_library_subfolder(): void {
        global $CFG;

        $this->resetAfterTest();

        $generator = $this->getDataGenerator()->get_plugin_generator('core_h5p');
        // Create a library record.
        $lib = $generator->create_library_record('Library', 'Lib', 1, 1);

        $expected = "{$CFG->wwwroot}/pluginfile.php/1/core_h5p/libraries/{$lib->id}/Library-1.1/css/example.css";

        // Get the URL of a file from a sub-folder from an existing library. The provided folder name is parsable.
        $actual = $this->framework->getLibraryFileUrl('Library-1.1/css', 'example.css');

        // Make sure the proper URL is returned.
        $this->assertEquals($expected, $actual);
    }

    /**
     * Test the behaviour of loadAddons().
     */
    public function test_loadAddons(): void {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator()->get_plugin_generator('core_h5p');

        // Create a Library addon (1.1).
        $generator->create_library_record('Library', 'Lib', 1, 1, 2,
            '', '/regex1/');
        // Create a Library addon (1.3).
        $generator->create_library_record('Library', 'Lib', 1, 3, 2,
            '', '/regex2/');
        // Create a Library addon (1.2).
        $generator->create_library_record('Library', 'Lib', 1, 2, 2,
            '', '/regex3/');
        // Create a Library1 addon (1.2)
        $generator->create_library_record('Library1', 'Lib1', 1, 2, 2,
            '', '/regex11/');

        // Load the latest version of each addon.
        $addons = $this->framework->loadAddons();

        // The addons array should return 2 results (Library and Library1 addon).
        $this->assertCount(2, $addons);

        // Ensure the addons array is consistently ordered before asserting their contents.
        core_collator::asort_array_of_arrays_by_key($addons, 'machineName');
        [$addonone, $addontwo] = array_values($addons);

        // Make sure the version 1.3 is the latest 'Library' addon version.
        $this->assertEquals('Library', $addonone['machineName']);
        $this->assertEquals(1, $addonone['majorVersion']);
        $this->assertEquals(3, $addonone['minorVersion']);

        // Make sure the version 1.2 is the latest 'Library1' addon version.
        $this->assertEquals('Library1', $addontwo['machineName']);
        $this->assertEquals(1, $addontwo['majorVersion']);
        $this->assertEquals(2, $addontwo['minorVersion']);
    }

    /**
     * Test the behaviour of loadLibraries().
     */
    public function test_loadLibraries(): void {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator()->get_plugin_generator('core_h5p');

        // Generate h5p related data.
        $generator->generate_h5p_data();

        // Load all libraries.
        $libraries = $this->framework->loadLibraries();

        // Make sure all libraries are returned.
        $this->assertNotEmpty($libraries);
        $this->assertCount(6, $libraries);
        $this->assertEquals('MainLibrary', $libraries['MainLibrary'][0]->machine_name);
        $this->assertEquals('1', $libraries['MainLibrary'][0]->major_version);
        $this->assertEquals('0', $libraries['MainLibrary'][0]->minor_version);
        $this->assertEquals('1', $libraries['MainLibrary'][0]->patch_version);
    }

    /**
     * Test the behaviour of test_getLibraryId() when requesting an existing machine name.
     */
    public function test_getLibraryId_existing_machine_name(): void {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator()->get_plugin_generator('core_h5p');

        // Create a library.
        $lib = $generator->create_library_record('Library', 'Lib', 1, 1, 2);

        // Request the library ID of the library with machine name 'Library'.
        $libraryid = $this->framework->getLibraryId('Library');

        // Make sure the library ID is being returned.
        $this->assertNotFalse($libraryid);
        $this->assertEquals($lib->id, $libraryid);
    }

    /**
     * Test the behaviour of test_getLibraryId() when requesting a non-existent machine name.
     */
    public function test_getLibraryId_non_existent_machine_name(): void {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator()->get_plugin_generator('core_h5p');

        // Create a library.
        $generator->create_library_record('Library', 'Lib', 1, 1, 2);

        // Request the library ID of the library with machinename => 'TestLibrary' (non-existent).
        $libraryid = $this->framework->getLibraryId('TestLibrary');

        // Make sure the library ID not being returned.
        $this->assertFalse($libraryid);
    }

    /**
     * Test the behaviour of test_getLibraryId() when requesting a non-existent major version.
     */
    public function test_getLibraryId_non_existent_major_version(): void {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator()->get_plugin_generator('core_h5p');

        // Create a library.
        $generator->create_library_record('Library', 'Lib', 1, 1, 2);

        // Request the library ID of the library with machine name => 'Library', majorversion => 2 (non-existent).
        $libraryid = $this->framework->getLibraryId('Library', 2);

        // Make sure the library ID not being returned.
        $this->assertFalse($libraryid);
    }

    /**
     * Test the behaviour of test_getLibraryId() when requesting a non-existent minor version.
     */
    public function test_getLibraryId_non_existent_minor_version(): void {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator()->get_plugin_generator('core_h5p');

        // Create a library.
        $generator->create_library_record('Library', 'Lib', 1, 1, 2);

        // Request the library ID of the library with machine name => 'Library',
        // majorversion => 1,  minorversion => 2 (non-existent).
        $libraryid = $this->framework->getLibraryId('Library', 1, 2);

        // Make sure the library ID not being returned.
        $this->assertFalse($libraryid);
    }

    /**
     * Test the behaviour of isPatchedLibrary().
     *
     * @dataProvider isPatchedLibrary_provider
     * @param array $libraryrecords Array containing data for the library creation
     * @param array $testlibrary Array containing the test library data
     * @param bool $expected The expectation whether the library is patched or not
     **/
    public function test_isPatchedLibrary(array $libraryrecords, array $testlibrary, bool $expected): void {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator()->get_plugin_generator('core_h5p');

        foreach ($libraryrecords as $library) {
            call_user_func_array([$generator, 'create_library_record'], $library);
        }

        $this->assertEquals($expected, $this->framework->isPatchedLibrary($testlibrary));
    }

    /**
     * Data provider for test_isPatchedLibrary().
     *
     * @return array
     */
    public static function isPatchedLibrary_provider(): array {
        return [
            'Unpatched library. No different versioning' => [
                [
                    ['TestLibrary', 'Test', 1, 1, 2],
                ],
                [
                    'machineName' => 'TestLibrary',
                    'majorVersion' => 1,
                    'minorVersion' => 1,
                    'patchVersion' => 2
                ],
                false,
            ],
            'Major version identical; Minor version identical; Patch version newer' => [
                [
                    ['TestLibrary', 'Test', 1, 1, 2],
                ],
                [
                    'machineName' => 'TestLibrary',
                    'majorVersion' => 1,
                    'minorVersion' => 1,
                    'patchVersion' => 3
                ],
                true,
            ],
            'Major version identical; Minor version newer; Patch version newer' => [
                [
                    ['TestLibrary', 'Test', 1, 1, 2],
                ],
                [
                    'machineName' => 'TestLibrary',
                    'majorVersion' => 1,
                    'minorVersion' => 2,
                    'patchVersion' => 3
                ],
                false,
            ],
            'Major version identical; Minor version identical; Patch version older' => [
                [
                    ['TestLibrary', 'Test', 1, 1, 2],
                ],
                [
                    'machineName' => 'TestLibrary',
                    'majorVersion' => 1,
                    'minorVersion' => 1,
                    'patchVersion' => 1
                ],
                false,
            ],
            'Major version identical; Minor version newer; Patch version older' => [
                [
                    ['TestLibrary', 'Test', 1, 1, 2],
                ],
                [
                    'machineName' => 'TestLibrary',
                    'majorVersion' => 1,
                    'minorVersion' => 2,
                    'patchVersion' => 1
                ],
                false,
            ],
            'Major version newer; Minor version identical; Patch version older' => [
                [
                    ['TestLibrary', 'Test', 1, 1, 2],
                ],
                [
                    'machineName' => 'TestLibrary',
                    'majorVersion' => 2,
                    'minorVersion' => 1,
                    'patchVersion' => 1
                ],
                false,
            ],
            'Major version newer; Minor version identical; Patch version newer' => [
                [
                    ['TestLibrary', 'Test', 1, 1, 2],
                ],
                [
                    'machineName' => 'TestLibrary',
                    'majorVersion' => 2,
                    'minorVersion' => 1,
                    'patchVersion' => 3
                ],
                false,
            ],

            'Major version older; Minor version identical; Patch version older' => [
                [
                    ['TestLibrary', 'Test', 1, 1, 2],
                ],
                [
                    'machineName' => 'TestLibrary',
                    'majorVersion' => 0,
                    'minorVersion' => 1,
                    'patchVersion' => 1
                ],
                false,
            ],
            'Major version older; Minor version identical; Patch version newer' => [
                [
                    ['TestLibrary', 'Test', 1, 1, 2],
                ],
                [
                    'machineName' => 'TestLibrary',
                    'majorVersion' => 0,
                    'minorVersion' => 1,
                    'patchVersion' => 3
                ],
                false,
            ],
        ];
    }

    /**
     * Test the behaviour of isInDevMode().
     */
    public function test_isInDevMode(): void {
        $isdevmode = $this->framework->isInDevMode();

        $this->assertFalse($isdevmode);
    }

    /**
     * Test the behaviour of mayUpdateLibraries().
     */
    public function test_mayUpdateLibraries(): void {
        global $DB;

        $this->resetAfterTest();

        // Create some users.
        $contextsys = \context_system::instance();
        $user = $this->getDataGenerator()->create_user();
        $admin = get_admin();
        $managerrole = $DB->get_record('role', ['shortname' => 'manager'], '*', MUST_EXIST);
        $studentrole = $DB->get_record('role', ['shortname' => 'student'], '*', MUST_EXIST);
        $manager = $this->getDataGenerator()->create_user();
        role_assign($managerrole->id, $manager->id, $contextsys);

        // Create a course with a label and enrol the user.
        $course = $this->getDataGenerator()->create_course();
        $label = $this->getDataGenerator()->create_module('label', ['course' => $course->id]);
        list(, $labelcm) = get_course_and_cm_from_instance($label->id, 'label');
        $contextlabel = \context_module::instance($labelcm->id);
        $this->getDataGenerator()->enrol_user($user->id, $course->id, 'student');

        // Create the .h5p file.
        $path = self::get_fixture_path(__NAMESPACE__, 'h5ptest.zip');

        // Admin and manager should have permission to update libraries.
        $file = helper::create_fake_stored_file_from_path($path, $admin->id, $contextsys);
        $this->framework->set_file($file);
        $mayupdatelib = $this->framework->mayUpdateLibraries();
        $this->assertTrue($mayupdatelib);

        $file = helper::create_fake_stored_file_from_path($path, $manager->id, $contextsys);
        $this->framework->set_file($file);
        $mayupdatelib = $this->framework->mayUpdateLibraries();
        $this->assertTrue($mayupdatelib);

        // By default, normal user hasn't permission to update libraries (in both contexts, system and module label).
        $file = helper::create_fake_stored_file_from_path($path, $user->id, $contextsys);
        $this->framework->set_file($file);
        $mayupdatelib = $this->framework->mayUpdateLibraries();
        $this->assertFalse($mayupdatelib);

        $file = helper::create_fake_stored_file_from_path($path, $user->id, $contextlabel);
        $this->framework->set_file($file);
        $mayupdatelib = $this->framework->mayUpdateLibraries();
        $this->assertFalse($mayupdatelib);

        // If the current user (admin) can update libraries, the method should return true (even if the file userid hasn't the
        // required capabilility in the file context).
        $file = helper::create_fake_stored_file_from_path($path, $admin->id, $contextlabel);
        $this->framework->set_file($file);
        $mayupdatelib = $this->framework->mayUpdateLibraries();
        $this->assertTrue($mayupdatelib);

        // If the update capability is assigned to the user, they should be able to update the libraries (only in the context
        // where the capability has been assigned).
        $file = helper::create_fake_stored_file_from_path($path, $user->id, $contextlabel);
        $this->framework->set_file($file);
        $mayupdatelib = $this->framework->mayUpdateLibraries();
        $this->assertFalse($mayupdatelib);
        assign_capability('moodle/h5p:updatelibraries', CAP_ALLOW, $studentrole->id, $contextlabel);
        $mayupdatelib = $this->framework->mayUpdateLibraries();
        $this->assertTrue($mayupdatelib);
        $file = helper::create_fake_stored_file_from_path($path, $user->id, $contextsys);
        $this->framework->set_file($file);
        $mayupdatelib = $this->framework->mayUpdateLibraries();
        $this->assertFalse($mayupdatelib);
    }

    /**
     * Test the behaviour of get_file() and set_file().
     */
    public function test_get_file(): void {
        $this->resetAfterTest();

        // Create some users.
        $contextsys = \context_system::instance();
        $user = $this->getDataGenerator()->create_user();

        // The H5P file.
        $path = self::get_fixture_path(__NAMESPACE__, 'h5ptest.zip');

        // An error should be raised when it's called before initialitzing it.
        $this->expectException('coding_exception');
        $this->expectExceptionMessage('Using get_file() before file is set');
        $this->framework->get_file();

        // Check the value when only path and user are set.
        $file = helper::create_fake_stored_file_from_path($path, $user->id);
        $this->framework->set_file($file);
        $file = $this->framework->get_file();
        $this->assertEquals($user->id, $$file->get_userid());
        $this->assertEquals($contextsys->id, $file->get_contextid());

        // Check the value when also the context is set.
        $course = $this->getDataGenerator()->create_course();
        $contextcourse = \context_course::instance($course->id);
        $file = helper::create_fake_stored_file_from_path($path, $user->id, $contextcourse);
        $this->framework->set_file($file);
        $file = $this->framework->get_file();
        $this->assertEquals($user->id, $$file->get_userid());
        $this->assertEquals($contextcourse->id, $file->get_contextid());
    }

    /**
     * Test the behaviour of saveLibraryData() when saving data for a new library.
     */
    public function test_saveLibraryData_new_library(): void {
        global $DB;

        $this->resetAfterTest();

        $librarydata = array(
            'title' => 'Test',
            'machineName' => 'TestLibrary',
            'majorVersion' => '1',
            'minorVersion' => '0',
            'patchVersion' => '2',
            'runnable' => 1,
            'fullscreen' => 1,
            'preloadedJs' => array(
                array(
                    'path' => 'js/name.min.js'
                )
            ),
            'preloadedCss' => array(
                array(
                    'path' => 'css/name.css'
                )
            ),
            'dropLibraryCss' => array(
                array(
                    'machineName' => 'Name2'
                )
            )
        );

        // Create a new library.
        $this->framework->saveLibraryData($librarydata);

        $library = $DB->get_record('h5p_libraries', ['machinename' => $librarydata['machineName']]);

        // Make sure the library data was properly saved.
        $this->assertNotEmpty($library);
        $this->assertNotEmpty($librarydata['libraryId']);
        $this->assertEquals($librarydata['title'], $library->title);
        $this->assertEquals($librarydata['machineName'], $library->machinename);
        $this->assertEquals($librarydata['majorVersion'], $library->majorversion);
        $this->assertEquals($librarydata['minorVersion'], $library->minorversion);
        $this->assertEquals($librarydata['patchVersion'], $library->patchversion);
        $this->assertEquals($librarydata['preloadedJs'][0]['path'], $library->preloadedjs);
        $this->assertEquals($librarydata['preloadedCss'][0]['path'], $library->preloadedcss);
        $this->assertEquals($librarydata['dropLibraryCss'][0]['machineName'], $library->droplibrarycss);
    }

    /**
     * Test the behaviour of saveLibraryData() when saving (updating) data for an existing library.
     */
    public function test_saveLibraryData_existing_library(): void {
        global $DB;

        $this->resetAfterTest();

        $generator = $this->getDataGenerator()->get_plugin_generator('core_h5p');

        // Create a library record.
        $library = $generator->create_library_record('TestLibrary', 'Test', 1, 0, 2);

        $librarydata = array(
            'libraryId' => $library->id,
            'title' => 'Test1',
            'machineName' => 'TestLibrary',
            'majorVersion' => '1',
            'minorVersion' => '2',
            'patchVersion' => '2',
            'runnable' => 1,
            'fullscreen' => 1,
            'preloadedJs' => array(
                array(
                    'path' => 'js/name.min.js'
                )
            ),
            'preloadedCss' => array(
                array(
                    'path' => 'css/name.css'
                )
            ),
            'dropLibraryCss' => array(
                array(
                    'machineName' => 'Name2'
                )
            )
        );

        // Update the library.
        $this->framework->saveLibraryData($librarydata, false);

        $library = $DB->get_record('h5p_libraries', ['machinename' => $librarydata['machineName']]);

        // Make sure the library data was properly updated.
        $this->assertNotEmpty($library);
        $this->assertNotEmpty($librarydata['libraryId']);
        $this->assertEquals($librarydata['title'], $library->title);
        $this->assertEquals($librarydata['machineName'], $library->machinename);
        $this->assertEquals($librarydata['majorVersion'], $library->majorversion);
        $this->assertEquals($librarydata['minorVersion'], $library->minorversion);
        $this->assertEquals($librarydata['patchVersion'], $library->patchversion);
        $this->assertEquals($librarydata['preloadedJs'][0]['path'], $library->preloadedjs);
        $this->assertEquals($librarydata['preloadedCss'][0]['path'], $library->preloadedcss);
        $this->assertEquals($librarydata['dropLibraryCss'][0]['machineName'], $library->droplibrarycss);
    }

    /**
     * Test the behaviour of insertContent().
     */
    public function test_insertContent(): void {
        global $DB;

        $this->resetAfterTest();

        $content = array(
            'params' => json_encode(['param1' => 'Test']),
            'library' => array(
                'libraryId' => 1
            ),
            'disable' => 8
        );

        // Insert h5p content.
        $contentid = $this->framework->insertContent($content);

        // Get the entered content from the db.
        $dbcontent = $DB->get_record('h5p', ['id' => $contentid]);

        // Make sure the h5p content was properly inserted.
        $this->assertNotEmpty($dbcontent);
        $this->assertEquals($content['params'], $dbcontent->jsoncontent);
        $this->assertEquals($content['library']['libraryId'], $dbcontent->mainlibraryid);
        $this->assertEquals($content['disable'], $dbcontent->displayoptions);
    }

    /**
     * Test the behaviour of insertContent().
     */
    public function test_insertContent_latestlibrary(): void {
        global $DB;

        $this->resetAfterTest();

        $generator = $this->getDataGenerator()->get_plugin_generator('core_h5p');
        // Create a library record.
        $lib = $generator->create_library_record('TestLibrary', 'Test', 1, 1, 2);

        $content = array(
            'params' => json_encode(['param1' => 'Test']),
            'library' => array(
                'libraryId' => 0,
                'machineName' => 'TestLibrary',
            ),
            'disable' => 8
        );

        // Insert h5p content.
        $contentid = $this->framework->insertContent($content);

        // Get the entered content from the db.
        $dbcontent = $DB->get_record('h5p', ['id' => $contentid]);

        // Make sure the h5p content was properly inserted.
        $this->assertNotEmpty($dbcontent);
        $this->assertEquals($content['params'], $dbcontent->jsoncontent);
        $this->assertEquals($content['disable'], $dbcontent->displayoptions);
        // As the libraryId was empty, the latest library has been used.
        $this->assertEquals($lib->id, $dbcontent->mainlibraryid);
    }

    /**
     * Test the behaviour of updateContent().
     */
    public function test_updateContent(): void {
        global $DB;

        $this->resetAfterTest();

        /** @var \core_h5p_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_h5p');

        // Create a library record.
        $lib = $generator->create_library_record('TestLibrary', 'Test', 1, 1, 2);

        // Create an h5p content with 'TestLibrary' as it's main library.
        $contentid = $generator->create_h5p_record($lib->id);

        $content = array(
            'id' => $contentid,
            'params' => json_encode(['param2' => 'Test2']),
            'library' => array(
                'libraryId' => $lib->id
            ),
            'disable' => 8
        );

        // Update the h5p content.
        $this->framework->updateContent($content);

        $h5pcontent = $DB->get_record('h5p', ['id' => $contentid]);

        // Make sure the h5p content was properly updated.
        $this->assertNotEmpty($h5pcontent);
        $this->assertNotEmpty($h5pcontent->pathnamehash);
        $this->assertNotEmpty($h5pcontent->contenthash);
        $this->assertEquals($content['params'], $h5pcontent->jsoncontent);
        $this->assertEquals($content['library']['libraryId'], $h5pcontent->mainlibraryid);
        $this->assertEquals($content['disable'], $h5pcontent->displayoptions);
    }

    /**
     * Test the behaviour of updateContent() with metadata.
     */
    public function test_updateContent_withmetadata(): void {
        global $DB;

        $this->resetAfterTest();

        /** @var \core_h5p_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_h5p');

        // Create a library record.
        $lib = $generator->create_library_record('TestLibrary', 'Test', 1, 1, 2);

        // Create an h5p content with 'TestLibrary' as it's main library.
        $contentid = $generator->create_h5p_record($lib->id);

        $params = ['param2' => 'Test2'];
        $metadata = [
            'license' => 'CC BY',
            'licenseVersion' => '4.0',
            'yearFrom' => 2000,
            'yearTo' => 2023,
            'defaultLanguage' => 'ca',
        ];
        $content = [
            'id' => $contentid,
            'params' => json_encode($params),
            'library' => [
                'libraryId' => $lib->id,
            ],
            'disable' => 8,
            'metadata' => $metadata,
        ];

        // Update the h5p content.
        $this->framework->updateContent($content);

        $h5pcontent = $DB->get_record('h5p', ['id' => $contentid]);

        // Make sure the h5p content was properly updated.
        $this->assertNotEmpty($h5pcontent);
        $this->assertNotEmpty($h5pcontent->pathnamehash);
        $this->assertNotEmpty($h5pcontent->contenthash);
        $this->assertEquals(json_encode(array_merge($params, ['metadata' => $metadata])), $h5pcontent->jsoncontent);
        $this->assertEquals($content['library']['libraryId'], $h5pcontent->mainlibraryid);
        $this->assertEquals($content['disable'], $h5pcontent->displayoptions);
    }

    /**
     * Test the behaviour of saveLibraryDependencies().
     */
    public function test_saveLibraryDependencies(): void {
        global $DB;

        $this->resetAfterTest();

        $generator = $this->getDataGenerator()->get_plugin_generator('core_h5p');

        // Create a library 'Library'.
        $library = $generator->create_library_record('Library', 'Title');
        // Create a library 'DependencyLibrary1'.
        $dependency1 = $generator->create_library_record('DependencyLibrary1', 'DependencyTitle1');
        // Create a library 'DependencyLibrary2'.
        $dependency2 = $generator->create_library_record('DependencyLibrary2', 'DependencyTitle2');

        $dependencies = array(
            array(
                'machineName' => $dependency1->machinename,
                'majorVersion' => $dependency1->majorversion,
                'minorVersion' => $dependency1->minorversion
            ),
            array(
                'machineName' => $dependency2->machinename,
                'majorVersion' => $dependency2->majorversion,
                'minorVersion' => $dependency2->minorversion
            ),
        );

        // Set 'DependencyLibrary1' and 'DependencyLibrary2' as library dependencies of 'Library'.
        $this->framework->saveLibraryDependencies($library->id, $dependencies, 'preloaded');

        $libdependencies = $DB->get_records('h5p_library_dependencies', ['libraryid' => $library->id], 'id ASC');

        // Make sure the library dependencies for 'Library' are properly set.
        $this->assertEquals(2, count($libdependencies));
        $this->assertEquals($dependency1->id, reset($libdependencies)->requiredlibraryid);
        $this->assertEquals($dependency2->id, end($libdependencies)->requiredlibraryid);
    }

    /**
     * Test the behaviour of deleteContentData().
     */
    public function test_deleteContentData(): void {
        global $DB;

        $this->resetAfterTest();

        /** @var \core_h5p_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_h5p');
        // For the mod_h5pactivity component, the activity needs to be created too.
        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $this->setUser($user);
        $activity = $this->getDataGenerator()->create_module('h5pactivity', ['course' => $course]);
        $activitycontext = \context_module::instance($activity->cmid);
        $filerecord = [
            'contextid' => $activitycontext->id,
            'component' => 'mod_h5pactivity',
            'filearea' => 'package',
            'itemid' => 0,
            'filepath' => '/',
            'filename' => 'dummy.h5p',
            'addxapistate' => true,
        ];

        // Generate some h5p related data.
        $data = $generator->generate_h5p_data(false, $filerecord);
        $h5pid = $data->h5pcontent->h5pid;

        // Make sure the particular h5p content exists in the DB.
        $this->assertNotEmpty($DB->get_record('h5p', ['id' => $h5pid]));
        // Make sure the content libraries exists in the DB.
        $this->assertCount(5, $DB->get_records('h5p_contents_libraries', ['h5pid' => $h5pid]));
        // Make sure the particular xAPI state exists in the DB.
        $records = $DB->get_records('xapi_states');
        $record = reset($records);
        $this->assertCount(1, $records);
        $this->assertNotNull($record->statedata);

        // Delete the h5p content and it's related data.
        $this->framework->deleteContentData($h5pid);

        // The particular h5p content should no longer exist in the db.
        $this->assertEmpty($DB->get_record('h5p', ['id' => $h5pid]));
        // The particular content libraries should no longer exist in the db.
        $this->assertEmpty($DB->get_record('h5p_contents_libraries', ['h5pid' => $h5pid]));
        // The xAPI state should be reseted.
        $records = $DB->get_records('xapi_states');
        $record = reset($records);
        $this->assertCount(1, $records);
        $this->assertNull($record->statedata);
    }

    /**
     * Test the behaviour of resetContentUserData().
     */
    public function test_resetContentUserData(): void {
        global $DB;

        $this->resetAfterTest();

        /** @var \core_h5p_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_h5p');
        // For the mod_h5pactivity component, the activity needs to be created too.
        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $this->setUser($user);
        $activity = $this->getDataGenerator()->create_module('h5pactivity', ['course' => $course]);
        $activitycontext = \context_module::instance($activity->cmid);
        $filerecord = [
            'contextid' => $activitycontext->id,
            'component' => 'mod_h5pactivity',
            'filearea' => 'package',
            'itemid' => 0,
            'filepath' => '/',
            'filename' => 'dummy.h5p',
            'addxapistate' => true,
        ];

        // Generate some h5p related data.
        $data = $generator->generate_h5p_data(false, $filerecord);
        $h5pid = $data->h5pcontent->h5pid;

        // Make sure the H5P content, libraries and xAPI state exist in the DB.
        $this->assertNotEmpty($DB->get_record('h5p', ['id' => $h5pid]));
        $this->assertCount(5, $DB->get_records('h5p_contents_libraries', ['h5pid' => $h5pid]));
        $records = $DB->get_records('xapi_states');
        $record = reset($records);
        $this->assertCount(1, $records);
        $this->assertNotNull($record->statedata);

        // Reset the user data associated to this H5P content.
        $this->framework->resetContentUserData($h5pid);

        // The H5P content should still exist in the db.
        $this->assertNotEmpty($DB->get_record('h5p', ['id' => $h5pid]));
        // The particular content libraries should still exist in the db.
        $this->assertCount(5, $DB->get_records('h5p_contents_libraries', ['h5pid' => $h5pid]));
        // The xAPI state should still exist in the db, but should be reset.
        $records = $DB->get_records('xapi_states');
        $record = reset($records);
        $this->assertCount(1, $records);
        $this->assertNull($record->statedata);
    }

    /**
     * Test the behaviour of deleteLibraryUsage().
     */
    public function test_deleteLibraryUsage(): void {
        global $DB;

        $this->resetAfterTest();

        $generator = $this->getDataGenerator()->get_plugin_generator('core_h5p');

        // Generate some h5p related data.
        $data = $generator->generate_h5p_data();
        $h5pid = $data->h5pcontent->h5pid;

        // Get the h5p content libraries from the DB.
        $h5pcontentlibraries = $DB->get_records('h5p_contents_libraries', ['h5pid' => $h5pid]);

        // The particular h5p content should have 5 content libraries.
        $this->assertNotEmpty($h5pcontentlibraries);
        $this->assertCount(5, $h5pcontentlibraries);

        // Delete the h5p content and it's related data.
        $this->framework->deleteLibraryUsage($h5pid);

        // Get the h5p content libraries from the DB.
        $h5pcontentlibraries = $DB->get_record('h5p_contents_libraries', ['h5pid' => $h5pid]);

        // The particular h5p content libraries should no longer exist in the db.
        $this->assertEmpty($h5pcontentlibraries);
    }

    /**
     * Test the behaviour of test_saveLibraryUsage().
     */
    public function test_saveLibraryUsage(): void {
        global $DB;

        $this->resetAfterTest();

        $generator = $this->getDataGenerator()->get_plugin_generator('core_h5p');

        // Create a library 'Library'.
        $library = $generator->create_library_record('Library', 'Title');
        // Create a library 'DependencyLibrary1'.
        $dependency1 = $generator->create_library_record('DependencyLibrary1', 'DependencyTitle1');
        // Create a library 'DependencyLibrary2'.
        $dependency2 = $generator->create_library_record('DependencyLibrary2', 'DependencyTitle2');
        // Create an h5p content with 'Library' as it's main library.
        $contentid = $generator->create_h5p_record($library->id);

        $dependencies = array(
            array(
                'library' => array(
                    'libraryId' => $dependency1->id,
                    'machineName' => $dependency1->machinename,
                    'dropLibraryCss' => $dependency1->droplibrarycss
                ),
                'type' => 'preloaded',
                'weight' => 1
            ),
            array(
                'library' => array(
                    'libraryId' => $dependency2->id,
                    'machineName' => $dependency2->machinename,
                    'dropLibraryCss' => $dependency2->droplibrarycss
                ),
                'type' => 'preloaded',
                'weight' => 2
            ),
        );

        // Save 'DependencyLibrary1' and 'DependencyLibrary2' as h5p content libraries.
        $this->framework->saveLibraryUsage($contentid, $dependencies);

        // Get the h5p content libraries from the DB.
        $libdependencies = $DB->get_records('h5p_contents_libraries', ['h5pid' => $contentid], 'id ASC');

        // Make sure that 'DependencyLibrary1' and 'DependencyLibrary2' are properly set as h5p content libraries.
        $this->assertEquals(2, count($libdependencies));
        $this->assertEquals($dependency1->id, reset($libdependencies)->libraryid);
        $this->assertEquals($dependency2->id, end($libdependencies)->libraryid);
    }

    /**
     * Test the behaviour of getLibraryUsage() without skipping a particular h5p content.
     */
    public function test_getLibraryUsage_no_skip_content(): void {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator()->get_plugin_generator('core_h5p');

        // Generate h5p related data.
        $generateddata = $generator->generate_h5p_data();
        // The Id of the library 'Library1'.
        $library1id = $generateddata->lib1->data->id;
        // The Id of the library 'Library2'.
        $library2id = $generateddata->lib2->data->id;
        // The Id of the library 'Library5'.
        $library5id = $generateddata->lib5->data->id;

        // Get the library usage for 'Library1' (do not skip content).
        $data = $this->framework->getLibraryUsage($library1id);

        $expected = array(
            'content' => 1,
            'libraries' => 1
        );

        // Make sure 'Library1' is used by 1 content and is a dependency to 1 library.
        $this->assertEquals($expected, $data);

        // Get the library usage for 'Library2' (do not skip content).
        $data = $this->framework->getLibraryUsage($library2id);

        $expected = array(
            'content' => 1,
            'libraries' => 2,
        );

        // Make sure 'Library2' is used by 1 content and is a dependency to 2 libraries.
        $this->assertEquals($expected, $data);

         // Get the library usage for 'Library5' (do not skip content).
        $data = $this->framework->getLibraryUsage($library5id);

        $expected = array(
            'content' => 0,
            'libraries' => 1,
        );

        // Make sure 'Library5' is not used by any content and is a dependency to 1 library.
        $this->assertEquals($expected, $data);
    }

    /**
     * Test the behaviour of getLibraryUsage() when skipping a particular content.
     */
    public function test_getLibraryUsage_skip_content(): void {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator()->get_plugin_generator('core_h5p');

        // Generate h5p related data.
        $generateddata = $generator->generate_h5p_data();
        // The Id of the library 'Library1'.
        $library1id = $generateddata->lib1->data->id;

        // Get the library usage for 'Library1' (skip content).
        $data = $this->framework->getLibraryUsage($library1id, true);
        $expected = array(
            'content' => -1,
            'libraries' => 1,
        );

        // Make sure 'Library1' is a dependency to 1 library.
        $this->assertEquals($expected, $data);
    }

    /**
     * Test the behaviour of loadLibrary() when requesting an existing library.
     */
    public function test_loadLibrary_existing_library(): void {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator()->get_plugin_generator('core_h5p');

        // Generate h5p related data.
        $generateddata = $generator->generate_h5p_data();
        // The library data of 'Library1'.
        $library1 = $generateddata->lib1->data;
        // The library data of 'Library5'.
        $library5 = $generateddata->lib5->data;

        // The preloaded dependencies.
        $preloadeddependencies = array();

        foreach ($generateddata->lib1->dependencies as $preloadeddependency) {
            $preloadeddependencies[] = array(
                'machineName' => $preloadeddependency->machinename,
                'majorVersion' => $preloadeddependency->majorversion,
                'minorVersion' => $preloadeddependency->minorversion
            );
        }

        // Create a dynamic dependency.
        $generator->create_library_dependency_record($library1->id, $library5->id, 'dynamic');

        $dynamicdependencies[] = array(
            'machineName' => $library5->machinename,
            'majorVersion' => $library5->majorversion,
            'minorVersion' => $library5->minorversion
        );

        // Load 'Library1' data.
        $data = $this->framework->loadLibrary($library1->machinename, $library1->majorversion,
            $library1->minorversion);

        $expected = array(
            'libraryId' => $library1->id,
            'title' => $library1->title,
            'machineName' => $library1->machinename,
            'majorVersion' => $library1->majorversion,
            'minorVersion' => $library1->minorversion,
            'patchVersion' => $library1->patchversion,
            'runnable' => $library1->runnable,
            'fullscreen' => $library1->fullscreen,
            'embedTypes' => $library1->embedtypes,
            'preloadedJs' => $library1->preloadedjs,
            'preloadedCss' => $library1->preloadedcss,
            'dropLibraryCss' => $library1->droplibrarycss,
            'semantics' => $library1->semantics,
            'preloadedDependencies' => $preloadeddependencies,
            'dynamicDependencies' => $dynamicdependencies
        );

        // Make sure the 'Library1' data is properly loaded.
        $this->assertEquals($expected, $data);
    }

    /**
     * Test the behaviour of loadLibrary() when requesting a non-existent library.
     */
    public function test_loadLibrary_non_existent_library(): void {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator()->get_plugin_generator('core_h5p');

        // Generate h5p related data.
        $generator->generate_h5p_data();

        // Attempt to load a non-existent library.
        $data = $this->framework->loadLibrary('MissingLibrary', 1, 2);

        // Make sure nothing is loaded.
        $this->assertFalse($data);
    }

    /**
     * Test the behaviour of loadLibrarySemantics().
     *
     * @dataProvider loadLibrarySemantics_provider
     * @param array $libraryrecords Array containing data for the library creation
     * @param array $testlibrary Array containing the test library data
     * @param string $expected The expected semantics value
     **/
    public function test_loadLibrarySemantics(array $libraryrecords, array $testlibrary, string $expected): void {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator()->get_plugin_generator('core_h5p');

        foreach ($libraryrecords as $library) {
            call_user_func_array([$generator, 'create_library_record'], $library);
        }

        $this->assertEquals($expected, $this->framework->loadLibrarySemantics(
            $testlibrary['machinename'], $testlibrary['majorversion'], $testlibrary['minorversion']));
    }

    /**
     * Data provider for test_loadLibrarySemantics().
     *
     * @return array
     */
    public static function loadLibrarySemantics_provider(): array {

        $semantics = json_encode(
            [
                'type' => 'text',
                'name' => 'text',
                'label' => 'Plain text',
                'description' => 'Please add some text'
            ]
        );

        return [
            'Library with semantics' => [
                [
                    ['Library1', 'Lib1', 1, 1, 2, $semantics],
                ],
                [
                    'machinename' => 'Library1',
                    'majorversion' => 1,
                    'minorversion' => 1
                ],
                $semantics,
            ],
            'Library without semantics' => [
                [
                    ['Library2', 'Lib2', 1, 2, 2, ''],
                ],
                [
                    'machinename' => 'Library2',
                    'majorversion' => 1,
                    'minorversion' => 2
                ],
                '',
            ]
        ];
    }

    /**
     * Test the behaviour of alterLibrarySemantics().
     */
    public function test_alterLibrarySemantics(): void {
        global $DB;

        $this->resetAfterTest();

        $generator = $this->getDataGenerator()->get_plugin_generator('core_h5p');

        $semantics = json_encode(
            array(
                'type' => 'text',
                'name' => 'text',
                'label' => 'Plain text',
                'description' => 'Please add some text'
            )
        );

        // Create a library 'Library1' with semantics.
        $library1 = $generator->create_library_record('Library1', 'Lib1', 1, 1, 2, $semantics);

        $updatedsemantics = array(
            'type' => 'text',
            'name' => 'updated text',
            'label' => 'Updated text',
            'description' => 'Please add some text'
        );

        // Alter the semantics of 'Library1'.
        $this->framework->alterLibrarySemantics($updatedsemantics, 'Library1', 1, 1);

        // Get the semantics of 'Library1' from the DB.
        $currentsemantics = $DB->get_field('h5p_libraries', 'semantics', array('id' => $library1->id));

        // The semantics for Library1 shouldn't be updated.
        $this->assertEquals($semantics, $currentsemantics);
    }

    /**
     * Test the behaviour of deleteLibraryDependencies() when requesting to delete the
     * dependencies of an existing library.
     */
    public function test_deleteLibraryDependencies_existing_library(): void {
        global $DB;

        $this->resetAfterTest();

        $generator = $this->getDataGenerator()->get_plugin_generator('core_h5p');

        // Generate h5p related data.
        $data = $generator->generate_h5p_data();
        // The data of the library 'Library1'.
        $library1 = $data->lib1->data;

        // Get the dependencies of 'Library1'.
        $dependencies = $DB->get_records('h5p_library_dependencies', ['libraryid' => $library1->id]);
        // The 'Library1' should have 3 dependencies ('Library2', 'Library3', 'Library4').
        $this->assertCount(3, $dependencies);

        // Delete the dependencies of 'Library1'.
        $this->framework->deleteLibraryDependencies($library1->id);

        $dependencies = $DB->get_records('h5p_library_dependencies', ['libraryid' => $library1->id]);
        // The 'Library1' should have 0 dependencies.
        $this->assertCount(0, $dependencies);
    }

    /**
     * Test the behaviour of deleteLibraryDependencies() when requesting to delete the
     * dependencies of a non-existent library.
     */
    public function test_deleteLibraryDependencies_non_existent_library(): void {
        global $DB;

        $this->resetAfterTest();

        $generator = $this->getDataGenerator()->get_plugin_generator('core_h5p');

        // Generate h5p related data.
        $data = $generator->generate_h5p_data();
        // The data of the library 'Library1'.
        $library1 = $data->lib1->data;

        // Get the dependencies of 'Library1'.
        $dependencies = $DB->get_records('h5p_library_dependencies', ['libraryid' => $library1->id]);
        // The 'Library1' should have 3 dependencies ('Library2', 'Library3', 'Library4').
        $this->assertCount(3, $dependencies);

        // Delete the dependencies of a non-existent library.
        $this->framework->deleteLibraryDependencies(0);

        $dependencies = $DB->get_records('h5p_library_dependencies', ['libraryid' => $library1->id]);
        // The 'Library1' should have 3 dependencies.
        $this->assertCount(3, $dependencies);
    }

    /**
     * Test the behaviour of deleteLibrary().
     */
    public function test_deleteLibrary(): void {
        global $DB;

        $this->resetAfterTest();

        $generator = $this->getDataGenerator()->get_plugin_generator('core_h5p');

        // Generate h5p related data.
        $data = $generator->generate_h5p_data(true);
        // The data of the 'Library1' library.
        $library1 = $data->lib1->data;

        // Get the library dependencies of 'Library1'.
        $dependencies = $DB->get_records('h5p_library_dependencies', ['libraryid' => $library1->id]);

        // The 'Library1' should have 3 library dependencies ('Library2', 'Library3', 'Library4').
        $this->assertCount(3, $dependencies);

        // Return the created 'Library1' files.
        $libraryfiles = $DB->get_records('files',
            array(
                'component' => \core_h5p\file_storage::COMPONENT,
                'filearea' => \core_h5p\file_storage::LIBRARY_FILEAREA,
                'itemid' => $library1->id
            )
        );

        // The library ('Library1') should have 7 related folders/files.
        $this->assertCount(7, $libraryfiles);

        // Delete the library.
        $this->framework->deleteLibrary($library1);

        $lib1 = $DB->get_record('h5p_libraries', ['machinename' => $library1->machinename]);
        $dependencies = $DB->get_records('h5p_library_dependencies', ['libraryid' => $library1->id]);
        $libraryfiles = $DB->get_records('files',
            array(
                'component' => \core_h5p\file_storage::COMPONENT,
                'filearea' => \core_h5p\file_storage::LIBRARY_FILEAREA,
                'itemid' => $library1->id
            )
        );

        // The 'Library1' should not exist.
        $this->assertEmpty($lib1);
        // The library ('Library1')  should have 0 dependencies.
        $this->assertCount(0, $dependencies);
        // The library (library1) should have 0 related folders/files.
        $this->assertCount(0, $libraryfiles);
    }

    /**
     * Test the behaviour of loadContent().
     */
    public function test_loadContent(): void {
        global $DB;

        $this->resetAfterTest();

        $generator = $this->getDataGenerator()->get_plugin_generator('core_h5p');

        // Generate h5p related data.
        $data = $generator->generate_h5p_data();
        // The Id of the created h5p content.
        $h5pid = $data->h5pcontent->h5pid;
        // Get the h5p content data from the DB.
        $h5p = $DB->get_record('h5p', ['id' => $h5pid]);
        // The data of content's main library ('MainLibrary').
        $mainlibrary = $data->mainlib->data;

        // Load the h5p content.
        $content = $this->framework->loadContent($h5pid);

        $expected = array(
            'id' => $h5p->id,
            'params' => $h5p->jsoncontent,
            'embedType' => 'iframe',
            'disable' => $h5p->displayoptions,
            'title' => $mainlibrary->title,
            'slug' => H5PCore::slugify($mainlibrary->title) . '-' . $h5p->id,
            'filtered' => $h5p->filtered,
            'libraryId' => $mainlibrary->id,
            'libraryName' => $mainlibrary->machinename,
            'libraryMajorVersion' => $mainlibrary->majorversion,
            'libraryMinorVersion' => $mainlibrary->minorversion,
            'libraryEmbedTypes' => $mainlibrary->embedtypes,
            'libraryFullscreen' => $mainlibrary->fullscreen,
            'metadata' => '',
            'pathnamehash' => $h5p->pathnamehash
        );

        $params = json_decode($h5p->jsoncontent);
        if (empty($params->metadata)) {
            $params->metadata = new \stdClass();
        }
        $expected['metadata'] = $params->metadata;
        $expected['params'] = json_encode($params->params ?? $params);

        // The returned content should match the expected array.
        $this->assertEquals($expected, $content);
    }

    /**
     * Test the behaviour of loadContentDependencies() when requesting content dependencies
     * without specifying the dependency type.
     */
    public function test_loadContentDependencies_no_type_defined(): void {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator()->get_plugin_generator('core_h5p');

        // Generate h5p related data.
        $data = $generator->generate_h5p_data();
        // The Id of the h5p content.
        $h5pid = $data->h5pcontent->h5pid;
        // The content dependencies.
        $dependencies = $data->h5pcontent->contentdependencies;

        // Add Library5 as a content dependency (dynamic dependency type).
        $library5 = $data->lib5->data;
        $generator->create_contents_libraries_record($h5pid, $library5->id, 'dynamic');

        // Get all content dependencies.
        $contentdependencies = $this->framework->loadContentDependencies($h5pid);

        $expected = array();
        foreach ($dependencies as $dependency) {
            $expected[$dependency->machinename] = array(
                'libraryId' => $dependency->id,
                'machineName' => $dependency->machinename,
                'majorVersion' => $dependency->majorversion,
                'minorVersion' => $dependency->minorversion,
                'patchVersion' => $dependency->patchversion,
                'preloadedCss' => $dependency->preloadedcss,
                'preloadedJs' => $dependency->preloadedjs,
                'dropCss' => '0',
                'dependencyType' => 'preloaded'
            );
        }

        $expected = array_merge($expected,
            array(
                'Library5' => array(
                    'libraryId' => $library5->id,
                    'machineName' => $library5->machinename,
                    'majorVersion' => $library5->majorversion,
                    'minorVersion' => $library5->minorversion,
                    'patchVersion' => $library5->patchversion,
                    'preloadedCss' => $library5->preloadedcss,
                    'preloadedJs' => $library5->preloadedjs,
                    'dropCss' => '0',
                    'dependencyType' => 'dynamic'
                )
            )
        );

        // The loaded content dependencies should return 6 libraries.
        $this->assertCount(6, $contentdependencies);
        $this->assertEquals($expected, $contentdependencies);
    }

    /**
     * Test the behaviour of loadContentDependencies() when requesting content dependencies
     * with specifying the dependency type.
     */
    public function test_loadContentDependencies_type_defined(): void {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator()->get_plugin_generator('core_h5p');

        // Generate h5p related data.
        $data = $generator->generate_h5p_data();
        // The Id of the h5p content.
        $h5pid = $data->h5pcontent->h5pid;
        // The content dependencies.
        $dependencies = $data->h5pcontent->contentdependencies;

        // Add Library5 as a content dependency (dynamic dependency type).
        $library5 = $data->lib5->data;
        $generator->create_contents_libraries_record($h5pid, $library5->id, 'dynamic');

        // Load all content dependencies of dependency type 'preloaded'.
        $preloadeddependencies = $this->framework->loadContentDependencies($h5pid, 'preloaded');

        $expected = array();
        foreach ($dependencies as $dependency) {
            $expected[$dependency->machinename] = array(
                'libraryId' => $dependency->id,
                'machineName' => $dependency->machinename,
                'majorVersion' => $dependency->majorversion,
                'minorVersion' => $dependency->minorversion,
                'patchVersion' => $dependency->patchversion,
                'preloadedCss' => $dependency->preloadedcss,
                'preloadedJs' => $dependency->preloadedjs,
                'dropCss' => '0',
                'dependencyType' => 'preloaded'
            );
        }

        // The loaded content dependencies should return 5 libraries.
        $this->assertCount(5, $preloadeddependencies);
        $this->assertEquals($expected, $preloadeddependencies);

        // Load all content dependencies of dependency type 'dynamic'.
        $dynamicdependencies = $this->framework->loadContentDependencies($h5pid, 'dynamic');

        $expected = array(
            'Library5' => array(
                'libraryId' => $library5->id,
                'machineName' => $library5->machinename,
                'majorVersion' => $library5->majorversion,
                'minorVersion' => $library5->minorversion,
                'patchVersion' => $library5->patchversion,
                'preloadedCss' => $library5->preloadedcss,
                'preloadedJs' => $library5->preloadedjs,
                'dropCss' => '0',
                'dependencyType' => 'dynamic'
            )
        );

        // The loaded content dependencies should now return 1 library.
        $this->assertCount(1, $dynamicdependencies);
        $this->assertEquals($expected, $dynamicdependencies);
    }

    /**
     * Test the behaviour of getOption().
     */
    public function test_getOption(): void {
        $this->resetAfterTest();

        // Get value for display_option_download.
        $value = $this->framework->getOption(H5PCore::DISPLAY_OPTION_DOWNLOAD);
        $expected = H5PDisplayOptionBehaviour::CONTROLLED_BY_AUTHOR_DEFAULT_OFF;
        $this->assertEquals($expected, $value);

        // Get value for display_option_embed using default value (it should be ignored).
        $value = $this->framework->getOption(H5PCore::DISPLAY_OPTION_EMBED, H5PDisplayOptionBehaviour::NEVER_SHOW);
        $expected = H5PDisplayOptionBehaviour::CONTROLLED_BY_AUTHOR_DEFAULT_OFF;
        $this->assertEquals($expected, $value);

        // Get value for unexisting setting without default.
        $value = $this->framework->getOption('unexistingsetting');
        $expected = false;
        $this->assertEquals($expected, $value);

        // Get value for unexisting setting with default.
        $value = $this->framework->getOption('unexistingsetting', 'defaultvalue');
        $expected = 'defaultvalue';
        $this->assertEquals($expected, $value);
    }

    /**
     * Test the behaviour of setOption().
     */
    public function test_setOption(): void {
        $this->resetAfterTest();

        // Set value for 'newsetting' setting.
        $name = 'newsetting';
        $value = $this->framework->getOption($name);
        $this->assertEquals(false, $value);
        $newvalue = 'value1';
        $this->framework->setOption($name, $newvalue);
        $value = $this->framework->getOption($name);
        $this->assertEquals($newvalue, $value);

        // Set value for display_option_download and then get it again. Check it hasn't changed.
        $name = H5PCore::DISPLAY_OPTION_DOWNLOAD;
        $newvalue = H5PDisplayOptionBehaviour::NEVER_SHOW;
        $this->framework->setOption($name, $newvalue);
        $value = $this->framework->getOption($name);
        $expected = H5PDisplayOptionBehaviour::CONTROLLED_BY_AUTHOR_DEFAULT_OFF;
        $this->assertEquals($expected, $value);
    }

    /**
     * Test the behaviour of updateContentFields().
     */
    public function test_updateContentFields(): void {
        global $DB;

        $this->resetAfterTest();

        $generator = $this->getDataGenerator()->get_plugin_generator('core_h5p');

        // Create 'Library1' library.
        $library1 = $generator->create_library_record('Library1', 'Lib1', 1, 1, 2);
        // Create 'Library2' library.
        $library2 = $generator->create_library_record('Library2', 'Lib2', 1, 1, 2);

        // Create an h5p content with 'Library1' as it's main library.
        $h5pid = $generator->create_h5p_record($library1->id, 'iframe');

        $updatedata = array(
            'jsoncontent' => json_encode(['value' => 'test']),
            'mainlibraryid' => $library2->id
        );

        // Update h5p content fields.
        $this->framework->updateContentFields($h5pid, $updatedata);

        // Get the h5p content from the DB.
        $h5p = $DB->get_record('h5p', ['id' => $h5pid]);

        $expected = json_encode(['value' => 'test']);

        // Make sure the h5p content fields are properly updated.
        $this->assertEquals($expected, $h5p->jsoncontent);
        $this->assertEquals($library2->id, $h5p->mainlibraryid);
    }

    /**
     * Test the behaviour of clearFilteredParameters().
     */
    public function test_clearFilteredParameters(): void {
        global $DB;

        $this->resetAfterTest();

        $generator = $this->getDataGenerator()->get_plugin_generator('core_h5p');

        // Create 3 libraries.
        $library1 = $generator->create_library_record('Library1', 'Lib1', 1, 1, 2);
        $library2 = $generator->create_library_record('Library2', 'Lib2', 1, 1, 2);
        $library3 = $generator->create_library_record('Library3', 'Lib3', 1, 1, 2);

        // Create h5p content with 'Library1' as a main library.
        $h5pcontentid1 = $generator->create_h5p_record($library1->id);
        // Create h5p content with 'Library1' as a main library.
        $h5pcontentid2 = $generator->create_h5p_record($library1->id);
        // Create h5p content with 'Library2' as a main library.
        $h5pcontentid3 = $generator->create_h5p_record($library2->id);
        // Create h5p content with 'Library3' as a main library.
        $h5pcontentid4 = $generator->create_h5p_record($library3->id);

        $h5pcontent1 = $DB->get_record('h5p', ['id' => $h5pcontentid1]);
        $h5pcontent2 = $DB->get_record('h5p', ['id' => $h5pcontentid2]);
        $h5pcontent3 = $DB->get_record('h5p', ['id' => $h5pcontentid3]);
        $h5pcontent4 = $DB->get_record('h5p', ['id' => $h5pcontentid4]);

        // The filtered parameters should be present in each h5p content.
        $this->assertNotEmpty($h5pcontent1->filtered);
        $this->assertNotEmpty($h5pcontent2->filtered);
        $this->assertNotEmpty($h5pcontent3->filtered);
        $this->assertNotEmpty($h5pcontent4->filtered);

        // Clear the filtered parameters for contents that have library1 and library3 as
        // their main library.
        $this->framework->clearFilteredParameters([$library1->id, $library3->id]);

        $h5pcontent1 = $DB->get_record('h5p', ['id' => $h5pcontentid1]);
        $h5pcontent2 = $DB->get_record('h5p', ['id' => $h5pcontentid2]);
        $h5pcontent3 = $DB->get_record('h5p', ['id' => $h5pcontentid3]);
        $h5pcontent4 = $DB->get_record('h5p', ['id' => $h5pcontentid4]);

        // The filtered parameters should be still present only for the content that has
        // library 2 as a main library.
        $this->assertEmpty($h5pcontent1->filtered);
        $this->assertEmpty($h5pcontent2->filtered);
        $this->assertNotEmpty($h5pcontent3->filtered);
        $this->assertEmpty($h5pcontent4->filtered);
    }

    /**
     * Test the behaviour of getNumNotFiltered().
     */
    public function test_getNumNotFiltered(): void {
        global $DB;

        $this->resetAfterTest();

        $generator = $this->getDataGenerator()->get_plugin_generator('core_h5p');

        // Create 3 libraries.
        $library1 = $generator->create_library_record('Library1', 'Lib1', 1, 1, 2);
        $library2 = $generator->create_library_record('Library2', 'Lib2', 1, 1, 2);
        $library3 = $generator->create_library_record('Library3', 'Lib3', 1, 1, 2);

        // Create h5p content with library1 as a main library.
        $h5pcontentid1 = $generator->create_h5p_record($library1->id);
        // Create h5p content with library1 as a main library.
        $h5pcontentid2 = $generator->create_h5p_record($library1->id);
        // Create h5p content with library2 as a main library.
        $h5pcontentid3 = $generator->create_h5p_record($library2->id);
        // Create h5p content with library3 as a main library.
        $h5pcontentid4 = $generator->create_h5p_record($library3->id);

        $h5pcontent1 = $DB->get_record('h5p', ['id' => $h5pcontentid1]);
        $h5pcontent2 = $DB->get_record('h5p', ['id' => $h5pcontentid2]);
        $h5pcontent3 = $DB->get_record('h5p', ['id' => $h5pcontentid3]);
        $h5pcontent4 = $DB->get_record('h5p', ['id' => $h5pcontentid4]);

        // The filtered parameters should be present in each h5p content.
        $this->assertNotEmpty($h5pcontent1->filtered);
        $this->assertNotEmpty($h5pcontent2->filtered);
        $this->assertNotEmpty($h5pcontent3->filtered);
        $this->assertNotEmpty($h5pcontent4->filtered);

        // Clear the filtered parameters for contents that have library1 and library3 as
        // their main library.
        $this->framework->clearFilteredParameters([$library1->id, $library3->id]);

        $countnotfiltered = $this->framework->getNumNotFiltered();

        // 3 contents don't have their parameters filtered.
        $this->assertEquals(3, $countnotfiltered);
    }

    /**
     * Test the behaviour of getNumContent().
     */
    public function test_getNumContent(): void {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator()->get_plugin_generator('core_h5p');

        // Generate h5p related data.
        $data = $generator->generate_h5p_data();

        // The 'MainLibrary' library data.
        $mainlibrary = $data->mainlib->data;

        // The 'Library1' library data.
        $library1 = $data->lib1->data;

        // Create new h5p content with MainLibrary as a main library.
        $generator->create_h5p_record($mainlibrary->id);

        // Get the number of h5p contents that are using 'MainLibrary' as their main library.
        $countmainlib = $this->framework->getNumContent($mainlibrary->id);

        // Get the number of h5p contents that are using 'Library1' as their main library.
        $countlib1 = $this->framework->getNumContent($library1->id);

        // Make sure that 2 contents are using MainLibrary as their main library.
        $this->assertEquals(2, $countmainlib);
        // Make sure that 0 contents are using Library1 as their main library.
        $this->assertEquals(0, $countlib1);
    }

    /**
     * Test the behaviour of getNumContent() when certain contents are being skipped.
     */
    public function test_getNumContent_skip_content(): void {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator()->get_plugin_generator('core_h5p');

        // Generate h5p related data.
        $data = $generator->generate_h5p_data();

        // The 'MainLibrary' library data.
        $mainlibrary = $data->mainlib->data;

        // Create new h5p content with MainLibrary as a main library.
        $h5pcontentid = $generator->create_h5p_record($mainlibrary->id);

        // Get the number of h5p contents that are using 'MainLibrary' as their main library.
        // Skip the newly created content $h5pcontentid.
        $countmainlib = $this->framework->getNumContent($mainlibrary->id, [$h5pcontentid]);

        // Make sure that 1 content is returned instead of 2 ($h5pcontentid being skipped).
        $this->assertEquals(1, $countmainlib);
    }

    /**
     * Test the behaviour of isContentSlugAvailable().
     */
    public function test_isContentSlugAvailable(): void {
        $this->resetAfterTest();

        $slug = 'h5p-test-slug-1';

        // Currently this returns always true. The slug is generated as a unique value for
        // each h5p content and it is not stored in the h5p content table.
        $isslugavailable = $this->framework->isContentSlugAvailable($slug);

        $this->assertTrue($isslugavailable);
    }

    /**
     * Test that a record is stored for cached assets.
     */
    public function test_saveCachedAssets(): void {
        global $DB;

        $this->resetAfterTest();

        $libraries = array(
            array(
                'machineName' => 'H5P.TestLib',
                'libraryId' => 405,
            ),
            array(
                'FontAwesome' => 'FontAwesome',
                'libraryId' => 406,
            ),
            array(
                'machineName' => 'H5P.SecondLib',
                'libraryId' => 407,
            ),
        );

        $key = 'testhashkey';

        $this->framework->saveCachedAssets($key, $libraries);

        $records = $DB->get_records('h5p_libraries_cachedassets');

        $this->assertCount(3, $records);
    }

    /**
     * Test that the correct libraries are removed from the cached assets table
     */
    public function test_deleteCachedAssets(): void {
        global $DB;

        $this->resetAfterTest();

        $libraries = array(
            array(
                'machineName' => 'H5P.TestLib',
                'libraryId' => 405,
            ),
            array(
                'FontAwesome' => 'FontAwesome',
                'libraryId' => 406,
            ),
            array(
                'machineName' => 'H5P.SecondLib',
                'libraryId' => 407,
            ),
        );

        $key1 = 'testhashkey';
        $this->framework->saveCachedAssets($key1, $libraries);

        $libraries = array(
            array(
                'machineName' => 'H5P.DiffLib',
                'libraryId' => 408,
            ),
            array(
                'FontAwesome' => 'FontAwesome',
                'libraryId' => 406,
            ),
            array(
                'machineName' => 'H5P.ThirdLib',
                'libraryId' => 409,
            ),
        );

        $key2 = 'secondhashkey';
        $this->framework->saveCachedAssets($key2, $libraries);

        $libraries = array(
            array(
                'machineName' => 'H5P.AnotherDiffLib',
                'libraryId' => 410,
            ),
            array(
                'FontAwesome' => 'NotRelated',
                'libraryId' => 411,
            ),
            array(
                'machineName' => 'H5P.ForthLib',
                'libraryId' => 412,
            ),
        );

        $key3 = 'threeforthewin';
        $this->framework->saveCachedAssets($key3, $libraries);

        $records = $DB->get_records('h5p_libraries_cachedassets');
        $this->assertCount(9, $records);

        // Selecting one library id will result in all related library entries also being deleted.
        // Going to use the FontAwesome library id. The first two hashes should be returned.
        $hashes = $this->framework->deleteCachedAssets(406);
        $this->assertCount(2, $hashes);
        $index = array_search($key1, $hashes);
        $this->assertEquals($key1, $hashes[$index]);
        $index = array_search($key2, $hashes);
        $this->assertEquals($key2, $hashes[$index]);
        $index = array_search($key3, $hashes);
        $this->assertFalse($index);

        // Check that the records have been removed as well.
        $records = $DB->get_records('h5p_libraries_cachedassets');
        $this->assertCount(3, $records);
    }

    /**
     * Test the behaviour of getLibraryContentCount().
     */
    public function test_getLibraryContentCount(): void {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator()->get_plugin_generator('core_h5p');

        // Generate h5p related data.
        $data = $generator->generate_h5p_data();

        // The 'MainLibrary' library data.
        $mainlibrary = $data->mainlib->data;

        // The 'Library2' library data.
        $library2 = $data->lib2->data;

        // Create new h5p content with Library2 as it's main library.
        $generator->create_h5p_record($library2->id);

        // Create new h5p content with MainLibrary as it's main library.
        $generator->create_h5p_record($mainlibrary->id);

        $countlibrarycontent = $this->framework->getLibraryContentCount();

        $expected = array(
            "{$mainlibrary->machinename} {$mainlibrary->majorversion}.{$mainlibrary->minorversion}" => 2,
            "{$library2->machinename} {$library2->majorversion}.{$library2->minorversion}" => 1,
        );

        // MainLibrary and Library1 are currently main libraries to the existing h5p contents.
        // Should return the number of cases where MainLibrary and Library1 are main libraries to an h5p content.
        $this->assertEquals($expected, $countlibrarycontent);
    }

    /**
     * Test the behaviour of test_libraryHasUpgrade().
     *
     * @dataProvider libraryHasUpgrade_provider
     * @param array $libraryrecords Array containing data for the library creation
     * @param array $testlibrary Array containing the test library data
     * @param bool $expected The expectation whether the library is patched or not
     **/
    public function test_libraryHasUpgrade(array $libraryrecords, array $testlibrary, bool $expected): void {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator()->get_plugin_generator('core_h5p');

        foreach ($libraryrecords as $library) {
            call_user_func_array([$generator, 'create_library_record'], $library);
        }

        $this->assertEquals($expected, $this->framework->libraryHasUpgrade($testlibrary));
    }

    /**
     * Data provider for test_libraryHasUpgrade().
     *
     * @return array
     */
    public static function libraryHasUpgrade_provider(): array {
        return [
            'Lower major version; Identical lower version' => [
                [
                    ['Library', 'Lib', 2, 2],
                ],
                [
                    'machineName' => 'Library',
                    'majorVersion' => 1,
                    'minorVersion' => 2
                ],
                true,
            ],
            'Major version identical; Lower minor version' => [
                [
                    ['Library', 'Lib', 2, 2],
                ],
                [
                    'machineName' => 'Library',
                    'majorVersion' => 2,
                    'minorVersion' => 1
                ],
                true,
            ],
            'Major version identical; Minor version identical' => [
                [
                    ['Library', 'Lib', 2, 2],
                ],
                [
                    'machineName' => 'Library',
                    'majorVersion' => 2,
                    'minorVersion' => 2
                ],
                false,
            ],
            'Major version higher; Minor version identical' => [
                [
                    ['Library', 'Lib', 2, 2],
                ],
                [
                    'machineName' => 'Library',
                    'majorVersion' => 3,
                    'minorVersion' => 2
                ],
                false,
            ],
            'Major version identical; Minor version newer' => [
                [
                    ['Library', 'Lib', 2, 2],
                ],
                [
                    'machineName' => 'Library',
                    'majorVersion' => 2,
                    'minorVersion' => 4
                ],
                false,
            ]
        ];
    }


    /**
     * Test the behaviour of get_latest_library_version().
     */
    public function test_get_latest_library_version(): void {
        global $DB;

        $this->resetAfterTest();

        $generator = $this->getDataGenerator()->get_plugin_generator('core_h5p');
        // Create a library record.
        $machinename = 'TestLibrary';
        $lib1 = $generator->create_library_record($machinename, 'Test', 1, 1, 2);
        $lib2 = $generator->create_library_record($machinename, 'Test', 1, 2, 1);

        $content = array(
            'params' => json_encode(['param1' => 'Test']),
            'library' => array(
                'libraryId' => 0,
                'machineName' => 'TestLibrary',
            ),
            'disable' => 8
        );

        // Get the latest id (at this point, should be lib2).
        $latestlib = $this->framework->get_latest_library_version($machinename);
        $this->assertEquals($lib2->id, $latestlib->id);

        // Get the latest id (at this point, should be lib3).
        $lib3 = $generator->create_library_record($machinename, 'Test', 2, 1, 0);
        $latestlib = $this->framework->get_latest_library_version($machinename);
        $this->assertEquals($lib3->id, $latestlib->id);

        // Get the latest id (at this point, should be still lib3).
        $lib4 = $generator->create_library_record($machinename, 'Test', 1, 1, 3);
        $latestlib = $this->framework->get_latest_library_version($machinename);
        $this->assertEquals($lib3->id, $latestlib->id);

        // Get the latest id (at this point, should be lib5).
        $lib5 = $generator->create_library_record($machinename, 'Test', 2, 1, 6);
        $latestlib = $this->framework->get_latest_library_version($machinename);
        $this->assertEquals($lib5->id, $latestlib->id);
    }
}

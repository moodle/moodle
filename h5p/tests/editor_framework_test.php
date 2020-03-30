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
     * Test that the method getLanguage retrieves the translation of a library in the requested language.
     *
     * @dataProvider  get_language_provider
     *
     * @param  array  $datalib        Library data to create
     * @param  string $lang           Language to retrieve the translation
     * @param  bool   $emptyexpected  True when false value is expected; false, otherwise
     * @param  string $machinename    The machine readable name of the library(content type)
     * @param  int    $majorversion   Major part of version number
     * @param  int    $minorversion   Minor part of version number
     */
    public function test_get_language(array $datalib, string $lang, ?bool $emptyexpected = false, ?string $machinename = '',
            ?int $majorversion = 1, ?int $minorversion = 0): void {
        $this->resetAfterTest(true);

        // Fetch generator.
        $generator = \testing_util::get_data_generator();
        $h5pgenerator = $generator->get_plugin_generator('core_h5p');

        $h5pfilestorage = new file_storage();
        $h5ptempath = $h5pfilestorage->getTmpPath();

        $expectedresult = '';
        if ($datalib) {
            $translations = [];
            if (array_key_exists('translation', $datalib)) {
                $translations = $datalib['translation'];
            }
            // Create DB entry for this library.
            $tmplib = $h5pgenerator->create_library_record($datalib['machinename'], $datalib['title'], $datalib['majorversion'],
                $datalib['minorversion']);
            // Create the files for this libray.
            [$library, $files] = $h5pgenerator->create_library($h5ptempath, $tmplib->id, $datalib['machinename'],
                $datalib['majorversion'], $datalib['minorversion'], $translations);
            $h5pfilestorage->saveLibrary($library);

            // If machinename, majorversion or minorversion are empty, use the value in datalib.
            if (empty($machinename)) {
                $machinename = $datalib['machinename'];
            }
            if (empty($majorversion)) {
                $majorversion = $datalib['majorversion'];
            }
            if (empty($minorversion)) {
                $minorversion = $datalib['minorversion'];
            }
            if (!$emptyexpected && array_key_exists($lang, $translations)) {
                $expectedresult = $translations[$lang];
            }
        }

        // Get Language.
        $json = $this->editorframework->getLanguage($machinename, $majorversion, $minorversion, $lang);

        if ($emptyexpected) {
            $this->assertFalse($json);
        } else {
            $this->assertEquals($expectedresult, $json);
        }
    }

    /**
     * Data provider for test_get_language().
     *
     * @return array
     */
    public function get_language_provider(): array {
        return [
            'No library' => [
                [],
                'en',
                true,
                'Library1',
                1,
                2,
            ],
            'One library created but getting translation from an unexisting one' => [
                'Library1 1.2' => [
                    'machinename' => 'Library1',
                    'title' => 'Lib1',
                    'majorversion' => 1,
                    'minorversion' => 2,
                    'translation' => [
                        'es' => '{"libraryStrings": {"key": "valor"}}',
                        'fr' => '{"libraryStrings": {"key": "valeur"}}',
                    ],
                ],
                'es',
                true,
                'AnotherLibrary',
            ],
            'One library without any translation' => [
                'Library1 1.2' => [
                    'machinename' => 'Library1',
                    'title' => 'Lib1',
                    'majorversion' => 1,
                    'minorversion' => 2,
                ],
                'es',
                true,
            ],
            'One library with 2 translations (es and fr) - es' => [
                'Library1 1.2' => [
                    'machinename' => 'Library1',
                    'title' => 'Lib1',
                    'majorversion' => 1,
                    'minorversion' => 2,
                    'translation' => [
                        'es' => '{"libraryStrings": {"key": "valor"}}',
                        'fr' => '{"libraryStrings": {"key": "valeur"}}',
                    ],
                ],
                'es',
            ],
            'One library with 2 translations (es and fr) - fr' => [
                'Library1 1.2' => [
                    'machinename' => 'Library1',
                    'title' => 'Lib1',
                    'majorversion' => 1,
                    'minorversion' => 2,
                    'translation' => [
                        'es' => '{"libraryStrings": {"key": "valor"}}',
                        'fr' => '{"libraryStrings": {"key": "valeur"}}',
                    ],
                ],
                'fr',
            ],
            'One library with 2 translations (es and fr) - unexisting translation (de)' => [
                'Library1 1.2' => [
                    'machinename' => 'Library1',
                    'title' => 'Lib1',
                    'majorversion' => 1,
                    'minorversion' => 2,
                    'translation' => [
                        'es' => '{"libraryStrings": {"key": "valor"}}',
                        'fr' => '{"libraryStrings": {"key": "valeur"}}',
                    ],
                ],
                'de',
                true
            ],
            'One library with 3 translations (one of them English) - fr' => [
                'Library1 1.2' => [
                    'machinename' => 'Library1',
                    'title' => 'Lib1',
                    'majorversion' => 1,
                    'minorversion' => 2,
                    'translation' => [
                        'en' => '{"libraryStrings": {"key": "value"}}',
                        'es' => '{"libraryStrings": {"key": "valor"}}',
                        'fr' => '{"libraryStrings": {"key": "valeur"}}',
                    ],
                ],
                'fr',
            ],
            'One library with 3 translations (one of them English) - en' => [
                'Library1 1.2' => [
                    'machinename' => 'Library1',
                    'title' => 'Lib1',
                    'majorversion' => 1,
                    'minorversion' => 2,
                    'translation' => [
                        'en' => '{"libraryStrings": {"key": "value"}}',
                        'es' => '{"libraryStrings": {"key": "valor"}}',
                        'fr' => '{"libraryStrings": {"key": "valeur"}}',
                    ],
                ],
                'en',
            ],
        ];
    }

    /**
     * Test that the method getAvailableLanguages retrieves all the language available of a library.
     *
     * @dataProvider  get_available_languages_provider
     *
     * @param  array  $datalib        Library data to create
     * @param  array  $expectedlangs  Available languages expected.
     * @param  string $machinename    The machine readable name of the library(content type)
     * @param  int    $majorversion   Major part of version number
     * @param  int    $minorversion   Minor part of version number
     */
    public function test_get_available_languages(array $datalib, ?array $expectedlangs = null, ?string $machinename = '',
            ?int $majorversion = 1, ?int $minorversion = 0): void {
        $this->resetAfterTest(true);

        // Fetch generator.
        $generator = \testing_util::get_data_generator();
        $h5pgenerator = $generator->get_plugin_generator('core_h5p');

        $h5pfilestorage = new file_storage();
        $h5ptempath = $h5pfilestorage->getTmpPath();

        $translations = [];
        if ($datalib) {
            if (array_key_exists('translation', $datalib)) {
                $translations = $datalib['translation'];
            }
            // Create DB entry for this library.
            $tmplib = $h5pgenerator->create_library_record($datalib['machinename'], $datalib['title'], $datalib['majorversion'],
                $datalib['minorversion']);
            // Create the files for this libray.
            [$library, $files] = $h5pgenerator->create_library($h5ptempath, $tmplib->id, $datalib['machinename'],
                $datalib['majorversion'], $datalib['minorversion'], $translations);
            $h5pfilestorage->saveLibrary($library);

            if (empty($machinename)) {
                $machinename = $datalib['machinename'];
            }
            if (empty($majorversion)) {
                $majorversion = $datalib['majorversion'];
            }
            if (empty($minorversion)) {
                $minorversion = $datalib['minorversion'];
            }
        }

        // Get available languages.
        $langs = $this->editorframework->getAvailableLanguages($machinename, $majorversion, $minorversion);

        $this->assertCount(count($expectedlangs), $langs);
        $this->assertEquals(ksort($expectedlangs), ksort($langs));
    }

    /**
     * Data provider for test_get_available_languages().
     *
     * @return array
     */
    public function get_available_languages_provider(): array {
        return [
            'No library' => [
                [],
                [],
                'Library1',
                1,
                2,
            ],
            'One library created but getting available from an unexisting one' => [
                'Library1 1.2' => [
                    'machinename' => 'Library1',
                    'title' => 'Lib1',
                    'majorversion' => 1,
                    'minorversion' => 2,
                    'translation' => [
                        'es' => '{"libraryStrings": {"key": "valor"}}',
                        'fr' => '{"libraryStrings": {"key": "valeur"}}',
                    ],
                ],
                [],
                'Library2',
                1,
                2,
            ],
            'One library without any translation' => [
                'Library1 1.2' => [
                    'machinename' => 'Library1',
                    'title' => 'Lib1',
                    'majorversion' => 1,
                    'minorversion' => 2,
                ],
                ['en'],
            ],
            'One library with 2 translations (es and fr)' => [
                'Library1 1.2' => [
                    'machinename' => 'Library1',
                    'title' => 'Lib1',
                    'majorversion' => 1,
                    'minorversion' => 2,
                    'translation' => [
                        'es' => '{"libraryStrings": {"key": "valor"}}',
                        'fr' => '{"libraryStrings": {"key": "valeur"}}',
                    ],
                ],
                ['en', 'es', 'fr'],
            ],
            'One library with 3 translations (one of them English)' => [
                'Library1 1.2' => [
                    'machinename' => 'Library1',
                    'title' => 'Lib1',
                    'majorversion' => 1,
                    'minorversion' => 2,
                    'translation' => [
                        'en' => '{"libraryStrings": {"key": "value"}}',
                        'es' => '{"libraryStrings": {"key": "valor"}}',
                        'fr' => '{"libraryStrings": {"key": "valeur"}}',
                    ],
                ],
                ['en', 'es', 'fr'],
            ],
        ];
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

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
 * Testing the H5PEditorAjaxInterface interface implementation.
 *
 * @package    core_h5p
 * @category   test
 * @copyright  2020 Victor Deniz <victor@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_h5p;

use core_h5p\local\library\autoloader;
use Moodle\H5PCore;

/**
 *
 * Test class covering the H5PEditorAjaxInterface interface implementation.
 *
 * @package    core_h5p
 * @copyright  2020 Victor Deniz <victor@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @runTestsInSeparateProcesses
 */
class editor_ajax_test extends \advanced_testcase {

    /** @var editor_ajax H5P editor ajax instance */
    protected $editorajax;

    /**
     * Set up function for tests.
     */
    protected function setUp(): void {
        parent::setUp();

        autoloader::register();

        $this->editorajax = new editor_ajax();
    }

    /**
     * Test that getLatestLibraryVersions method retrieves the latest installed library versions.
     */
    public function test_getLatestLibraryVersions(): void {
        $this->resetAfterTest();

        $generator = \testing_util::get_data_generator();
        $h5pgenerator = $generator->get_plugin_generator('core_h5p');

        // Create several libraries records.
        $h5pgenerator->create_library_record('Library1', 'Lib1', 2, 0);
        $lib2 = $h5pgenerator->create_library_record('Library2', 'Lib2', 2, 1);
        $expectedlibraries[] = $lib2->id;
        $lib3 = $h5pgenerator->create_library_record('Library3', 'Lib3', 1, 3);
        $expectedlibraries[] = $lib3->id;
        $h5pgenerator->create_library_record('Library1', 'Lib1', 2, 1);
        $lib12 = $h5pgenerator->create_library_record('Library1', 'Lib1', 3, 0);
        $expectedlibraries[] = $lib12->id;

        $actuallibraries = $this->editorajax->getLatestLibraryVersions();
        ksort($actuallibraries);

        $this->assertEquals($expectedlibraries, array_keys($actuallibraries));
    }

    /**
     * Test that getContentTypeCache method retrieves the latest library versions that exists locally.
     */
    public function test_getContentTypeCache(): void {
        $this->resetAfterTest();

        $h5pgenerator = \testing_util::get_data_generator()->get_plugin_generator('core_h5p');

        // Create several libraries records.
        $lib1 = $h5pgenerator->create_library_record('Library1', 'Lib1', 1, 0, 1, '', null, 'http://tutorial.org',
            'http://example.org');
        $lib2 = $h5pgenerator->create_library_record('Library2', 'Lib2', 2, 0, 1, '', null, 'http://tutorial.org');
        $lib3 = $h5pgenerator->create_library_record('Library3', 'Lib3', 3, 0);
        $libs = [$lib1, $lib2, $lib3];

        $libraries = $this->editorajax->getContentTypeCache();
        $this->assertCount(3, $libraries);
        foreach ($libs as $lib) {
            $library = $libraries[$lib->id];
            $this->assertEquals($library->id, $lib->id);
            $this->assertEquals($library->machine_name, $lib->machinename);
            $this->assertEquals($library->major_version, $lib->majorversion);
            $this->assertEquals($library->tutorial, $lib->tutorial);
            $this->assertEquals($library->example, $lib->example);
            $this->assertEquals($library->is_recommended, 0);
            $this->assertEquals($library->summary, '');
        }
    }

    /**
     * Test that the method getTranslations retrieves the translations of several libraries.
     *
     * @dataProvider  get_translations_provider
     *
     * @param  array  $datalibs      Libraries to create
     * @param  string $lang          Language to get the translations
     * @param  bool   $emptyexpected True if empty translations are expected; false otherwise
     * @param  array  $altstringlibs When defined, libraries are no created and the content here is used to call the method
     */
    public function test_get_translations(array $datalibs, string $lang, bool $emptyexpected, ?array $altstringlibs = []): void {
        $this->resetAfterTest();

        // Fetch generator.
        $generator = \testing_util::get_data_generator();
        $h5pgenerator = $generator->get_plugin_generator('core_h5p');

        $h5pfilestorage = new file_storage();
        $h5ptempath = $h5pfilestorage->getTmpPath();

        if (!empty($altstringlibs)) {
            // Libraries won't be created and the getTranslation method will be called with this $altstringlibs.
            $stringlibs = $altstringlibs;
        } else {
            $stringlibs = [];
            foreach ($datalibs as $datalib) {
                // Create DB entry for this library.
                $tmplib = $h5pgenerator->create_library_record($datalib['machinename'], $datalib['title'], $datalib['majorversion'],
                    $datalib['minorversion']);
                // Create the files for this libray.
                [$library, $files] = $h5pgenerator->create_library($h5ptempath, $tmplib->id, $datalib['machinename'],
                    $datalib['majorversion'], $datalib['minorversion'], $datalib['translation']);
                $h5pfilestorage->saveLibrary($library);
                $stringlibs[] = H5PCore::libraryToString($library);
            }
        }

        $translations = $this->editorajax->getTranslations($stringlibs, $lang);

        if ($emptyexpected) {
            $this->assertEmpty($translations);
        } else {
            foreach ($translations as $stringlib => $translation) {
                $this->assertEquals($datalibs[$stringlib]['translation'][$lang], $translation);
            }
        }
    }

    /**
     * Data provider for test_get_translations().
     *
     * @return array
     */
    public function get_translations_provider(): array {
        return [
            'No library' => [
                [],
                'es',
                true,
                ['Library1 1.2']
            ],
            'One library with existing translation (es)' => [
                [
                    'Library1 1.2' => [
                        'machinename' => 'Library1',
                        'title' => 'Lib1',
                        'majorversion' => 1,
                        'minorversion' => 2,
                        'translation' => [
                            'es' => '{"libraryStrings": {"key": "valor"}}',
                            'fr' => '{"libraryStrings": {"key": "valeur"}}',
                        ],
                    ]
                ],
                'es',
                false
            ],
            'One library with existing translation (fr)' => [
                [
                    'Library1 1.2' => [
                        'machinename' => 'Library1',
                        'title' => 'Lib1',
                        'majorversion' => 1,
                        'minorversion' => 2,
                        'translation' => [
                            'es' => '{"libraryStrings": {"key": "valor"}}',
                            'fr' => '{"libraryStrings": {"key": "valeur"}}',
                        ],
                    ]
                ],
                'fr',
                false
            ],
            'One library with unexisting translation (de)' => [
                [
                    'Library1 1.2' => [
                        'machinename' => 'Library1',
                        'title' => 'Lib1',
                        'majorversion' => 1,
                        'minorversion' => 2,
                        'translation' => [
                            'es' => '{"libraryStrings": {"key": "valor"}}',
                            'fr' => '{"libraryStrings": {"key": "valeur"}}',
                        ],
                    ]
                ],
                'de',
                true
            ],
            'Two libraries with existing translation (es)' => [
                [
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
                    'Library2 3.4' => [
                        'machinename' => 'Library2',
                        'title' => 'Lib1',
                        'majorversion' => 3,
                        'minorversion' => 4,
                        'translation' => [
                            'es' => '{"libraryStrings": {"key": "valor"}}',
                            'fr' => '{"libraryStrings": {"key": "valeur"}}',
                        ],
                    ]
                ],
                'es',
                false
            ],
            'Two libraries with unexisting translation (de)' => [
                [
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
                    'Library2 3.4' => [
                        'machinename' => 'Library2',
                        'title' => 'Lib1',
                        'majorversion' => 3,
                        'minorversion' => 4,
                        'translation' => [
                            'es' => '{"libraryStrings": {"key": "valor"}}',
                            'fr' => '{"libraryStrings": {"key": "valeur"}}',
                        ],
                    ]
                ],
                'de',
                true
            ],
        ];
    }
}

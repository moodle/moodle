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
* Test class covering the h5p data generator class.
*
* @package    core_h5p
* @category   test
* @copyright  2019 Mihail Geshoski <mihail@moodle.com>
* @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace core_h5p;

use core_h5p\local\library\autoloader;

defined('MOODLE_INTERNAL') || die();

/**
* Generator testcase for the core_grading generator.
*
* @package    core_h5p
* @category   test
* @copyright  2019 Mihail Geshoski <mihail@moodle.com>
* @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
* @runTestsInSeparateProcesses
*/
class generator_testcase extends \advanced_testcase {

    /**
     * Tests set up.
     */
    protected function setUp(): void {
        parent::setUp();

        autoloader::register();
    }

    /**
     * Test the returned data of generate_h5p_data() when the method is called without requesting
     * creation of library files.
     */
    public function test_generate_h5p_data_no_files_created_return_data() {
        global $DB;

        $this->resetAfterTest();

        $generator = $this->getDataGenerator()->get_plugin_generator('core_h5p');

        $data = $generator->generate_h5p_data();

        $mainlib = $DB->get_record('h5p_libraries', ['machinename' => 'MainLibrary']);
        $lib1 = $DB->get_record('h5p_libraries', ['machinename' => 'Library1']);
        $lib2 = $DB->get_record('h5p_libraries', ['machinename' => 'Library2']);
        $lib3 = $DB->get_record('h5p_libraries', ['machinename' => 'Library3']);
        $lib4 = $DB->get_record('h5p_libraries', ['machinename' => 'Library4']);
        $lib5 = $DB->get_record('h5p_libraries', ['machinename' => 'Library5']);

        $h5p = $DB->get_record('h5p', ['mainlibraryid' => $mainlib->id]);

        $expected = (object) [
            'h5pcontent' => (object) array(
                 'h5pid' => $h5p->id,
                 'contentdependencies' => array($mainlib, $lib1, $lib2, $lib3, $lib4)
            ),
            'mainlib' => (object) array(
                'data' => $mainlib,
                'dependencies' => array($lib1, $lib2, $lib3)
            ),
            'lib1' => (object) array(
                'data' => $lib1,
                'dependencies' => array($lib2, $lib3, $lib4)
            ),
            'lib2' => (object) array(
                'data' => $lib2,
                'dependencies' => array()
            ),
            'lib3' => (object) array(
                'data' => $lib3,
                'dependencies' => array($lib5)
            ),
            'lib4' => (object) array(
                'data' => $lib4,
                'dependencies' => array()
            ),
            'lib5' => (object) array(
                'data' => $lib5,
                'dependencies' => array()
            ),
        ];

        $this->assertEquals($expected, $data);
    }

    /**
     * Test the returned data of generate_h5p_data() when the method requests
     * creation of library files.
     */
    public function test_generate_h5p_data_files_created_return_data() {
        global $DB;

        $this->resetAfterTest();

        $generator = $this->getDataGenerator()->get_plugin_generator('core_h5p');

        $data = $generator->generate_h5p_data(true);

        $mainlib = $DB->get_record('h5p_libraries', ['machinename' => 'MainLibrary']);
        $lib1 = $DB->get_record('h5p_libraries', ['machinename' => 'Library1']);
        $lib2 = $DB->get_record('h5p_libraries', ['machinename' => 'Library2']);
        $lib3 = $DB->get_record('h5p_libraries', ['machinename' => 'Library3']);
        $lib4 = $DB->get_record('h5p_libraries', ['machinename' => 'Library4']);
        $lib5 = $DB->get_record('h5p_libraries', ['machinename' => 'Library5']);

        $h5p = $DB->get_record('h5p', ['mainlibraryid' => $mainlib->id]);

        $expected = (object) [
            'h5pcontent' => (object) array(
                 'h5pid' => $h5p->id,
                 'contentdependencies' => array($mainlib, $lib1, $lib2, $lib3, $lib4)
            ),
            'mainlib' => (object) array(
                'data' => $mainlib,
                'dependencies' => array($lib1, $lib2, $lib3)
            ),
            'lib1' => (object) array(
                'data' => $lib1,
                'dependencies' => array($lib2, $lib3, $lib4)
            ),
            'lib2' => (object) array(
                'data' => $lib2,
                'dependencies' => array()
            ),
            'lib3' => (object) array(
                'data' => $lib3,
                'dependencies' => array($lib5)
            ),
            'lib4' => (object) array(
                'data' => $lib4,
                'dependencies' => array()
            ),
            'lib5' => (object) array(
                'data' => $lib5,
                'dependencies' => array()
            ),
        ];

        $this->assertEquals($expected, $data);
    }

    /**
     * Test the behaviour of generate_h5p_data(). Test whether library files are created or not
     * on filesystem depending what the method defines.
     *
     * @dataProvider test_generate_h5p_data_files_creation_provider
     * @param bool $createlibraryfiles Whether to create library files on the filesystem
     * @param bool $expected The expectation whether the files have been created or not
     **/
    public function test_generate_h5p_data_files_creation(bool $createlibraryfiles, bool $expected) {
        global $DB;

        $this->resetAfterTest();

        $generator = $this->getDataGenerator()->get_plugin_generator('core_h5p');
        $generator->generate_h5p_data($createlibraryfiles);

        $libraries[] = $DB->get_record('h5p_libraries', ['machinename' => 'MainLibrary']);
        $libraries[] = $DB->get_record('h5p_libraries', ['machinename' => 'Library1']);
        $libraries[] = $DB->get_record('h5p_libraries', ['machinename' => 'Library2']);
        $libraries[] = $DB->get_record('h5p_libraries', ['machinename' => 'Library3']);
        $libraries[] = $DB->get_record('h5p_libraries', ['machinename' => 'Library4']);
        $libraries[] = $DB->get_record('h5p_libraries', ['machinename' => 'Library5']);

        foreach($libraries as $lib) {
            // Return the created library files.
            $libraryfiles = $DB->get_records('files',
                array(
                    'component' => \core_h5p\file_storage::COMPONENT,
                    'filearea' => \core_h5p\file_storage::LIBRARY_FILEAREA,
                    'itemid' => $lib->id
                )
            );

            $haslibraryfiles = !empty($libraryfiles);

            $this->assertEquals($expected, $haslibraryfiles);
        }
    }

    /**
     * Data provider for test_generate_h5p_data_files_creation().
     *
     * @return array
     */
    public function test_generate_h5p_data_files_creation_provider(): array {
        return [
            'Do not create library related files on the filesystem' => [
                false,
                false
            ],
            'Create library related files on the filesystem' => [
                true,
                true
            ]
        ];
    }

    /**
     * Test the behaviour of create_library_record(). Test whether the library data is properly
     * saved in the database.
     */
    public function test_create_library_record() {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator()->get_plugin_generator('core_h5p');

        $data = $generator->create_library_record(
            'Library', 'Lib', 1, 2, 3, 'Semantics example', '/regex11/', 'http://tutorial.org/', 'http://example.org/'
        );
        unset($data->id);

        $expected = (object) [
            'machinename' => 'Library',
            'title' => 'Lib',
            'majorversion' => '1',
            'minorversion' => '2',
            'patchversion' => '3',
            'runnable' => '1',
            'fullscreen' => '1',
            'embedtypes' => '',
            'preloadedjs' => 'js/example.js',
            'preloadedcss' => 'css/example.css',
            'droplibrarycss' => '',
            'semantics' => 'Semantics example',
            'addto' => '/regex11/',
            'tutorial' => 'http://tutorial.org/',
            'example' => 'http://example.org/',
            'coremajor' => null,
            'coreminor' => null,
            'metadatasettings' => null,
        ];

        $this->assertEquals($expected, $data);
    }

    /**
     * Test the behaviour of create_h5p_record(). Test whather the h5p content data is
     * properly saved in the database.
     *
     * @dataProvider test_create_h5p_record_provider
     * @param array $h5pdata The h5p content data
     * @param \stdClass $expected The expected saved data
     **/
    public function test_create_h5p_record(array $h5pdata, \stdClass $expected) {
        global $DB;

        $this->resetAfterTest();

        $generator = $this->getDataGenerator()->get_plugin_generator('core_h5p');

        $h5pid = call_user_func_array([$generator, 'create_h5p_record'], $h5pdata);

        $data = $DB->get_record('h5p', ['id' => $h5pid]);
        unset($data->id);
        unset($data->timecreated);
        unset($data->timemodified);

        $this->assertEquals($data, $expected);
    }

    /**
     * Data provider for test_create_h5p_record().
     *
     * @return array
     */
    public function test_create_h5p_record_provider(): array {
        $createdjsoncontent = json_encode(
            array(
                'text' => '<p>Created dummy text<\/p>\n',
                'questions' => '<p>Test created question<\/p>\n'
            )
        );

        $defaultjsoncontent = json_encode(
            array(
                'text' => '<p>Dummy text<\/p>\n',
                'questions' => '<p>Test question<\/p>\n'
            )
        );

        $createdfilteredcontent = json_encode(
            array(
                'text' => 'Created dummy text',
                'questions' => 'Test created question'
            )
        );

        $defaultfilteredcontent = json_encode(
            array(
                'text' => 'Dummy text',
                'questions' => 'Test question'
            )
        );

        return [
            'Create h5p content record with set json content and set filtered content' => [
                [
                    1,
                    $createdjsoncontent,
                    $createdfilteredcontent
                ],
                (object) array(
                    'jsoncontent' => $createdjsoncontent,
                    'mainlibraryid' => '1',
                    'displayoptions' => '8',
                    'pathnamehash' => sha1('pathname'),
                    'contenthash' => sha1('content'),
                    'filtered' => $createdfilteredcontent,
                )
            ],
            'Create h5p content record with set json content and default filtered content' => [
                [
                    1,
                    $createdjsoncontent,
                    null
                ],
                (object) array(
                    'jsoncontent' => $createdjsoncontent,
                    'mainlibraryid' => '1',
                    'displayoptions' => '8',
                    'pathnamehash' => sha1('pathname'),
                    'contenthash' => sha1('content'),
                    'filtered' => $defaultfilteredcontent,
                )
            ],
            'Create h5p content record with default json content and set filtered content' => [
                [
                    1,
                    null,
                    $createdfilteredcontent
                ],
                (object) array(
                    'jsoncontent' => $defaultjsoncontent,
                    'mainlibraryid' => '1',
                    'displayoptions' => '8',
                    'pathnamehash' => sha1('pathname'),
                    'contenthash' => sha1('content'),
                    'filtered' => $createdfilteredcontent,
                )
            ],
            'Create h5p content record with default json content and default filtered content' => [
                [
                    1,
                    null,
                    null
                ],
                (object) array(
                    'jsoncontent' => $defaultjsoncontent,
                    'mainlibraryid' => '1',
                    'displayoptions' => '8',
                    'pathnamehash' => sha1('pathname'),
                    'contenthash' => sha1('content'),
                    'filtered' => $defaultfilteredcontent,
                )
            ]
        ];
    }

    /**
     * Test the behaviour of create_contents_libraries_record(). Test whether the contents libraries
     * are properly saved in the database.
     *
     * @dataProvider test_create_contents_libraries_record_provider
     * @param array $contentslibrariestdata The h5p contents libraries data.
     * @param \stdClass $expected The expected saved data.
     **/
    public function test_create_contents_libraries_record(array $contentslibrariestdata, \stdClass $expected) {
        global $DB;

        $this->resetAfterTest();

        $generator = $this->getDataGenerator()->get_plugin_generator('core_h5p');

        $contentlibid = call_user_func_array([$generator, 'create_contents_libraries_record'], $contentslibrariestdata);

        $data = $DB->get_record('h5p_contents_libraries', ['id' => $contentlibid]);
        unset($data->id);

        $this->assertEquals($data, $expected);
    }

    /**
     * Data provider for test_create_contents_libraries_record().
     *
     * @return array
     */
    public function test_create_contents_libraries_record_provider(): array {
        return [
            'Create h5p content library with set dependency type' => [
                [
                    1,
                    1,
                    'dynamic'
                ],
                (object) array(
                    'h5pid' => '1',
                    'libraryid' => '1',
                    'dependencytype' => 'dynamic',
                    'dropcss' => '0',
                    'weight' => '1'
                )
            ],
            'Create h5p content library with a default dependency type' => [
                [
                    1,
                    1
                ],
                (object) array(
                    'h5pid' => '1',
                    'libraryid' => '1',
                    'dependencytype' => 'preloaded',
                    'dropcss' => '0',
                    'weight' => '1'
                )
            ]
        ];
    }

    /**
     * Test the behaviour of create_library_dependency_record(). Test whether the contents libraries
     * are properly saved in the database.
     *
     * @dataProvider test_create_library_dependency_record_provider
     * @param array $librarydependencydata The library dependency data.
     * @param \stdClass $expected The expected saved data.
     **/
    public function test_create_library_dependency_record(array $librarydependencydata, \stdClass $expected) {
        global $DB;

        $this->resetAfterTest();

        $generator = $this->getDataGenerator()->get_plugin_generator('core_h5p');

        $contentlibid = call_user_func_array([$generator, 'create_library_dependency_record'], $librarydependencydata);

        $data = $DB->get_record('h5p_library_dependencies', ['id' => $contentlibid]);
        unset($data->id);

        $this->assertEquals($data, $expected);
    }

    /**
     * Data provider for test_create_library_dependency_record().
     *
     * @return array
     */
    public function test_create_library_dependency_record_provider(): array {
        return [
            'Create h5p library dependency with set dependency type' => [
                [
                    1,
                    1,
                    'dynamic'
                ],
                (object) array(
                    'libraryid' => '1',
                    'requiredlibraryid' => '1',
                    'dependencytype' => 'dynamic'
                )
            ],
            'Create h5p library dependency with default dependency type' => [
                [
                    1,
                    1
                ],
                (object) array(
                    'libraryid' => '1',
                    'requiredlibraryid' => '1',
                    'dependencytype' => 'preloaded'
                )
            ]
        ];
    }

    /**
     * Test the behaviour of create_content_file(). Test whether a file belonging to a content is created.
     *
     * @dataProvider test_create_content_file_provider
     * @param array $filedata Data from the file to be created.
     * @param array $expecteddata Data expected.Data from the file to be created.
     */
    public function test_create_content_file($filedata, $expecteddata): void {
        $this->resetAfterTest();

        $generator = self::getDataGenerator()->get_plugin_generator('core_h5p');

        if ($expecteddata[1] === 'exception') {
            $this->expectException('coding_exception');
        }
        call_user_func_array([$generator, 'create_content_file'], $filedata);

        $systemcontext = \context_system::instance();
        $filearea = $filedata[1];
        $filepath = '/'. dirname($filedata[0]). '/';
        $filename = basename($filedata[0]);
        $itemid = $expecteddata[0];

        $fs = new \file_storage();
        $exists = $fs->file_exists($systemcontext->id, file_storage::COMPONENT, $filearea, $itemid, $filepath,
            $filename);
        if ($expecteddata[1] === true) {
            $this->assertTrue($exists);
        } else if ($expecteddata[1] === false) {
            $this->assertFalse($exists);
        }
    }

    /**
     * Data provider for test_create_content_file(). Data from different files to be created.
     *
     * @return array
     **/
    public function test_create_content_file_provider(): array {
        return [
            'Create file in content with id 4' => [
                [
                    'images/img1.png',
                    'content',
                    4
                ],
                [
                    4,
                    true
                ]
            ],
            'Create file in the editor' => [
                [
                    'images/img1.png',
                    'editor'
                ],
                [
                    0,
                    true
                ]
            ],
            'Create file in content without id' => [
                [
                    'images/img1.png',
                    'content'
                ],
                [
                    0,
                    'exception'
                ]
            ]
        ];
    }
}

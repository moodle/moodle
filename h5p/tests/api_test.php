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

use stdClass;

defined('MOODLE_INTERNAL') || die();

/**
 * Test class covering the H5P API.
 *
 * @package    core_h5p
 * @copyright  2020 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core_h5p\api
 */
class api_test extends \advanced_testcase {

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
    public static function delete_library_provider(): array {
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
    public static function get_dependent_libraries_provider(): array {
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
    public static function get_library_provider(): array {
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
        $path = self::get_fixture_path(__NAMESPACE__, $filename);
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
     * Test the behaviour of get_original_content_from_pluginfile_url().
     *
     * @covers ::get_original_content_from_pluginfile_url
     */
    public function test_get_original_content_from_pluginfile_url(): void {
        $this->setRunTestInSeparateProcess(true);
        $this->resetAfterTest();
        $this->setAdminUser();

        $factory = new factory();
        $syscontext = \context_system::instance();

        // Create the original file.
        $filename = 'greeting-card.h5p';
        $path = self::get_fixture_path(__NAMESPACE__, $filename);
        $originalfile = helper::create_fake_stored_file_from_path($path);
        $originalfilerecord = [
            'contextid' => $originalfile->get_contextid(),
            'component' => $originalfile->get_component(),
            'filearea'  => $originalfile->get_filearea(),
            'itemid'    => $originalfile->get_itemid(),
            'filepath'  => $originalfile->get_filepath(),
            'filename'  => $originalfile->get_filename(),
        ];

        $config = (object)[
            'frame' => 1,
            'export' => 1,
            'embed' => 0,
            'copyright' => 0,
        ];

        $originalurl = \moodle_url::make_pluginfile_url(
            $originalfile->get_contextid(),
            $originalfile->get_component(),
            $originalfile->get_filearea(),
            $originalfile->get_itemid(),
            $originalfile->get_filepath(),
            $originalfile->get_filename()
        );

        // Create a reference to the original file.
        $reffilerecord = [
            'contextid' => $syscontext->id,
            'component' => 'core',
            'filearea'  => 'phpunit',
            'itemid'    => 0,
            'filepath'  => '/',
            'filename'  => $filename
        ];

        $fs = get_file_storage();
        $ref = $fs->pack_reference($originalfilerecord);
        $repos = \repository::get_instances(['type' => 'user']);
        $userrepository = reset($repos);
        $referencedfile = $fs->create_file_from_reference($reffilerecord, $userrepository->id, $ref);
        $this->assertEquals($referencedfile->get_contenthash(), $originalfile->get_contenthash());

        $referencedurl = \moodle_url::make_pluginfile_url(
            $syscontext->id,
            'core',
            'phpunit',
            0,
            '/',
            $filename
        );

        // Scenario 1: Original file (without any reference).
        $originalh5pid = helper::save_h5p($factory, $originalfile, $config);
        list($source, $h5p, $file) = api::get_original_content_from_pluginfile_url($originalurl->out());
        $this->assertEquals($originalfile->get_pathnamehash(), $source->get_pathnamehash());
        $this->assertEquals($originalfile->get_contenthash(), $source->get_contenthash());
        $this->assertEquals($originalh5pid, $h5p->id);
        $this->assertFalse($file);

        // Scenario 2: Referenced file (alias to originalfile).
        list($source, $h5p, $file) = api::get_original_content_from_pluginfile_url($referencedurl->out());
        $this->assertEquals($originalfile->get_pathnamehash(), $source->get_pathnamehash());
        $this->assertEquals($originalfile->get_contenthash(), $source->get_contenthash());
        $this->assertEquals($originalfile->get_contenthash(), $source->get_contenthash());
        $this->assertEquals($originalh5pid, $h5p->id);
        $this->assertEquals($referencedfile->get_pathnamehash(), $file->get_pathnamehash());
        $this->assertEquals($referencedfile->get_contenthash(), $file->get_contenthash());
        $this->assertEquals($referencedfile->get_contenthash(), $file->get_contenthash());

        // Scenario 3: Unexisting file.
        $unexistingurl = \moodle_url::make_pluginfile_url(
            $syscontext->id,
            'core',
            'phpunit',
            0,
            '/',
            'unexisting.h5p'
        );
        list($source, $h5p, $file) = api::get_original_content_from_pluginfile_url($unexistingurl->out());
        $this->assertFalse($source);
        $this->assertFalse($h5p);
        $this->assertFalse($file);
    }

    /**
     * Test the behaviour of can_edit_content().
     *
     * @covers ::can_edit_content
     * @dataProvider can_edit_content_provider
     *
     * @param string $currentuser User who will call the method.
     * @param string $fileauthor Author of the file to check.
     * @param string $filecomponent Component of the file to check.
     * @param bool $expected Expected result after calling the can_edit_content method.
     * @param string $filearea Area of the file to check.
     *
     * @return void
     */
    public function test_can_edit_content(string $currentuser, string $fileauthor, string $filecomponent, bool $expected,
            $filearea = 'unittest'): void {
        global $USER, $DB;

        $this->setRunTestInSeparateProcess(true);
        $this->resetAfterTest();

        // Create course.
        $course = $this->getDataGenerator()->create_course();
        $context = \context_course::instance($course->id);

        // Create some users.
        $this->setAdminUser();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $users = [
            'admin' => $USER,
            'teacher' => $teacher,
            'student' => $student,
        ];

        // Set current user.
        if ($currentuser !== 'admin') {
            $this->setUser($users[$currentuser]);
        }

        $itemid = rand();
        if ($filearea === 'post') {
            // Create a forum and add a discussion.
            $forum = $this->getDataGenerator()->create_module('forum', ['course' => $course->id]);

            $record = new stdClass();
            $record->course = $course->id;
            $record->userid = $users[$fileauthor]->id;
            $record->forum = $forum->id;
            $discussion = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);
            $post = $DB->get_record('forum_posts', ['discussion' => $discussion->id]);
            $itemid = $post->id;
        }

        // Create the file.
        $filename = 'greeting-card.h5p';
        $path = self::get_fixture_path(__NAMESPACE__, $filename);
        if ($filecomponent === 'contentbank') {
            $generator = $this->getDataGenerator()->get_plugin_generator('core_contentbank');
            $contents = $generator->generate_contentbank_data(
                'contenttype_h5p',
                1,
                (int)$users[$fileauthor]->id,
                $context,
                true,
                $path
            );
            $content = array_shift($contents);
            $file = $content->get_file();
        } else {
            $filerecord = [
                'contextid' => $context->id,
                'component' => $filecomponent,
                'filearea'  => $filearea,
                'itemid'    => $itemid,
                'filepath'  => '/',
                'filename'  => basename($path),
                'userid'    => $users[$fileauthor]->id,
            ];
            $fs = get_file_storage();
            $file = $fs->create_file_from_pathname($filerecord, $path);
        }

        // Check if the currentuser can edit the file.
        $result = api::can_edit_content($file);
        $this->assertEquals($expected, $result);
    }

    /**
     * Data provider for test_can_edit_content().
     *
     * @return array
     */
    public static function can_edit_content_provider(): array {
        return [
            // Component = user.
            'user: Admin user is author' => [
                'currentuser' => 'admin',
                'fileauthor' => 'admin',
                'filecomponent' => 'user',
                'expected' => true,
            ],
            'user: Admin user, teacher is author' => [
                'currentuser' => 'admin',
                'fileauthor' => 'teacher',
                'filecomponent' => 'user',
                'expected' => false,
            ],
            'user: Teacher user, teacher is author' => [
                'currentuser' => 'teacher',
                'fileauthor' => 'teacher',
                'filecomponent' => 'user',
                'expected' => true,
            ],
            'user: Teacher user, admin is author' => [
                'currentuser' => 'teacher',
                'fileauthor' => 'admin',
                'filecomponent' => 'user',
                'expected' => false,
            ],
            'user: Student user, student is author' => [
                'currentuser' => 'student',
                'fileauthor' => 'student',
                'filecomponent' => 'user',
                'expected' => true,
            ],
            'user: Student user, teacher is author' => [
                'currentuser' => 'student',
                'fileauthor' => 'teacher',
                'filecomponent' => 'user',
                'expected' => false,
            ],

            // Component = mod_h5pactivity.
            'mod_h5pactivity: Admin user is author' => [
                'currentuser' => 'admin',
                'fileauthor' => 'admin',
                'filecomponent' => 'mod_h5pactivity',
                'expected' => true,
            ],
            'mod_h5pactivity: Admin user, teacher is author' => [
                'currentuser' => 'admin',
                'fileauthor' => 'teacher',
                'filecomponent' => 'mod_h5pactivity',
                'expected' => true,
            ],
            'mod_h5pactivity: Teacher user, teacher is author' => [
                'currentuser' => 'teacher',
                'fileauthor' => 'teacher',
                'filecomponent' => 'mod_h5pactivity',
                'expected' => true,
            ],
            'mod_h5pactivity: Teacher user, admin is author' => [
                'currentuser' => 'teacher',
                'fileauthor' => 'admin',
                'filecomponent' => 'mod_h5pactivity',
                'expected' => true,
            ],
            'mod_h5pactivity: Student user, student is author' => [
                'currentuser' => 'student',
                'fileauthor' => 'student',
                'filecomponent' => 'mod_h5pactivity',
                'expected' => false,
            ],
            'mod_h5pactivity: Student user, teacher is author' => [
                'currentuser' => 'student',
                'fileauthor' => 'teacher',
                'filecomponent' => 'mod_h5pactivity',
                'expected' => false,
            ],

            // Component = mod_book.
            'mod_book: Admin user is author' => [
                'currentuser' => 'admin',
                'fileauthor' => 'admin',
                'filecomponent' => 'mod_book',
                'expected' => true,
            ],
            'mod_book: Admin user, teacher is author' => [
                'currentuser' => 'admin',
                'fileauthor' => 'teacher',
                'filecomponent' => 'mod_book',
                'expected' => true,
            ],

            // Component = mod_forum.
            'mod_forum: Admin user is author' => [
                'currentuser' => 'admin',
                'fileauthor' => 'admin',
                'filecomponent' => 'mod_forum',
                'expected' => true,
            ],
            'mod_forum: Admin user, teacher is author' => [
                'currentuser' => 'admin',
                'fileauthor' => 'teacher',
                'filecomponent' => 'mod_forum',
                'expected' => true,
            ],
            'mod_forum: Teacher user, admin is author' => [
                'currentuser' => 'teacher',
                'fileauthor' => 'admin',
                'filecomponent' => 'mod_forum',
                'expected' => true,
            ],
            'mod_forum: Student user, teacher is author' => [
                'currentuser' => 'student',
                'fileauthor' => 'teacher',
                'filecomponent' => 'mod_forum',
                'expected' => false,
            ],
            'mod_forum/post: Admin user is author' => [
                'currentuser' => 'admin',
                'fileauthor' => 'admin',
                'filecomponent' => 'mod_forum',
                'expected' => true,
                'filearea' => 'post',
            ],
            'mod_forum/post: Teacher user, admin is author' => [
                'currentuser' => 'teacher',
                'fileauthor' => 'admin',
                'filecomponent' => 'mod_forum',
                'expected' => true,
                'filearea' => 'post',
            ],
            'mod_forum/post: Student user, teacher is author' => [
                'currentuser' => 'student',
                'fileauthor' => 'teacher',
                'filecomponent' => 'mod_forum',
                'expected' => false,
                'filearea' => 'post',
            ],

            // Component = block_html.
            'block_html: Admin user is author' => [
                'currentuser' => 'admin',
                'fileauthor' => 'admin',
                'filecomponent' => 'block_html',
                'expected' => true,
            ],
            'block_html: Admin user, teacher is author' => [
                'currentuser' => 'admin',
                'fileauthor' => 'teacher',
                'filecomponent' => 'block_html',
                'expected' => true,
            ],

            // Component = contentbank.
            'contentbank: Admin user is author' => [
                'currentuser' => 'admin',
                'fileauthor' => 'admin',
                'filecomponent' => 'contentbank',
                'expected' => true,
            ],
            'contentbank: Admin user, teacher is author' => [
                'currentuser' => 'admin',
                'fileauthor' => 'teacher',
                'filecomponent' => 'contentbank',
                'expected' => true,
            ],
            'contentbank: Teacher user, teacher is author' => [
                'currentuser' => 'teacher',
                'fileauthor' => 'teacher',
                'filecomponent' => 'contentbank',
                'expected' => true,
            ],
            'contentbank: Teacher user, admin is author' => [
                'currentuser' => 'teacher',
                'fileauthor' => 'admin',
                'filecomponent' => 'contentbank',
                'expected' => false,
            ],
            'contentbank: Student user, student is author' => [
                'currentuser' => 'student',
                'fileauthor' => 'student',
                'filecomponent' => 'contentbank',
                'expected' => false,
            ],
            'contentbank: Student user, teacher is author' => [
                'currentuser' => 'student',
                'fileauthor' => 'teacher',
                'filecomponent' => 'contentbank',
                'expected' => false,
            ],

            // Unexisting components.
            'Unexisting component' => [
                'currentuser' => 'admin',
                'fileauthor' => 'admin',
                'filecomponent' => 'unexisting_component',
                'expected' => false,
            ],
            'Unexisting module activity' => [
                'currentuser' => 'admin',
                'fileauthor' => 'admin',
                'filecomponent' => 'mod_unexisting',
                'expected' => false,
            ],
            'Unexisting block' => [
                'currentuser' => 'admin',
                'fileauthor' => 'admin',
                'filecomponent' => 'block_unexisting',
                'expected' => false,
            ],
        ];
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
        $path = self::get_fixture_path(__NAMESPACE__, $filename);
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
        $path = self::get_fixture_path(__NAMESPACE__, $filename);
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

    /**
     * Test the behaviour of set_library_enabled().
     *
     * @covers ::set_library_enabled
     * @dataProvider set_library_enabled_provider
     *
     * @param string $libraryname Library name to enable/disable.
     * @param string $action Action to be done with the library. Supported values: enable, disable.
     * @param int $expected Expected value for the enabled library field. -1 will be passed if the library doesn't exist.
     */
    public function test_set_library_enabled(string $libraryname, string $action, int $expected): void {
        global $DB;

        $this->resetAfterTest();

        // Create libraries.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_h5p');
        $generator->generate_h5p_data();

        // Check by default the library is enabled.
        $library = $DB->get_record('h5p_libraries', ['machinename' => $libraryname]);
        if ($expected >= 0) {
            $this->assertEquals(1, $library->enabled);
            $libraryid = (int) $library->id;
        } else {
            // Unexisting library. Set libraryid to some unexisting id.
            $libraryid = -1;
            $this->expectException('dml_missing_record_exception');
        }

        \core_h5p\api::set_library_enabled($libraryid, ($action == 'enable'));

        // Check the value of the "enabled" field after calling enable/disable method.
        $libraries = $DB->get_records('h5p_libraries');
        foreach ($libraries as $libraryid => $library) {
            if ($library->machinename == $libraryname) {
                $this->assertEquals($expected, $library->enabled);
            } else {
                // Check that only $libraryname has been enabled/disabled.
                $this->assertEquals(1, $library->enabled);
            }
        }
    }

    /**
     * Data provider for test_set_library_enabled().
     *
     * @return array
     */
    public static function set_library_enabled_provider(): array {
        return [
            'Disable existing library' => [
                'libraryname' => 'MainLibrary',
                'action' => 'disable',
                'expected' => 0,
            ],
            'Enable existing library' => [
                'libraryname' => 'MainLibrary',
                'action' => 'enable',
                'expected' => 1,
            ],
            'Disable existing library (not main)' => [
                'libraryname' => 'Library1',
                'action' => 'disable',
                'expected' => 0,
            ],
            'Enable existing library (not main)' => [
                'libraryname' => 'Library1',
                'action' => 'enable',
                'expected' => 1,
            ],
            'Disable existing library (not runnable)' => [
                'libraryname' => 'Library3',
                'action' => 'disable',
                'expected' => 1, // Not runnable libraries can't be disabled.
            ],
            'Enable existing library (not runnable)' => [
                'libraryname' => 'Library3',
                'action' => 'enable',
                'expected' => 1,
            ],
            'Enable unexisting library' => [
                'libraryname' => 'Unexisting library',
                'action' => 'enable',
                'expected' => -1,
            ],
            'Disable unexisting library' => [
                'libraryname' => 'Unexisting library',
                'action' => 'disable',
                'expected' => -1,
            ],
        ];
    }

    /**
     * Test the behaviour of is_library_enabled().
     *
     * @covers ::is_library_enabled
     * @dataProvider is_library_enabled_provider
     *
     * @param string $libraryname Library name to check.
     * @param bool $expected Expected result after calling the method.
     * @param bool $exception Exception expected or not.
     * @param bool $useid Whether to use id for calling is_library_enabled method.
     * @param bool $uselibraryname Whether to use libraryname for calling is_library_enabled method.
     */
    public function test_is_library_enabled(string $libraryname, bool $expected, bool $exception = false,
        bool $useid = false, bool $uselibraryname = true): void {
        global $DB;

        $this->resetAfterTest();

        // Create the following libraries:
        // - H5P.Lib1: 1 version enabled, 1 version disabled.
        // - H5P.Lib2: 2 versions enabled.
        // - H5P.Lib3: 2 versions disabled.
        // - H5P.Lib4: 1 version disabled.
        // - H5P.Lib5: 1 version enabled.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_h5p');
        $libraries = [
            'H5P.Lib1.1' => $generator->create_library_record('H5P.Lib1', 'Lib1', 1, 1, 0, '', null, null, null, false),
            'H5P.Lib1.2' => $generator->create_library_record('H5P.Lib1', 'Lib1', 1, 2),
            'H5P.Lib2.1' => $generator->create_library_record('H5P.Lib2', 'Lib2', 2, 1),
            'H5P.Lib2.2' => $generator->create_library_record('H5P.Lib2', 'Lib2', 2, 2),
            'H5P.Lib3.1' => $generator->create_library_record('H5P.Lib3', 'Lib3', 3, 1, 0, '', null, null, null, false),
            'H5P.Lib3.2' => $generator->create_library_record('H5P.Lib3', 'Lib3', 3, 2, 0, '', null, null, null, false),
            'H5P.Lib4.1' => $generator->create_library_record('H5P.Lib4', 'Lib4', 4, 1, 0, '', null, null, null, false),
            'H5P.Lib5.1' => $generator->create_library_record('H5P.Lib5', 'Lib5', 5, 1),
        ];

        $countenabledlibraries = $DB->count_records('h5p_libraries', ['enabled' => 1]);
        $this->assertEquals(4, $countenabledlibraries);

        if ($useid) {
            $librarydata = ['id' => $libraries[$libraryname]->id];
        } else if ($uselibraryname) {
            $librarydata = ['machinename' => $libraryname];
        } else {
            $librarydata = ['invalid' => true];
        }

        if ($exception) {
            $this->expectException(\moodle_exception::class);
        }

        $result = api::is_library_enabled((object) $librarydata);
        $this->assertEquals($expected, $result);
    }

    /**
     * Data provider for test_is_library_enabled().
     *
     * @return array
     */
    public static function is_library_enabled_provider(): array {
        return [
            'Library with 2 versions, one of them disabled' => [
                'libraryname' => 'H5P.Lib1',
                'expected' => false,
            ],
            'Library with 2 versions, all enabled' => [
                'libraryname' => 'H5P.Lib2',
                'expected' => true,
            ],
            'Library with 2 versions, all disabled' => [
                'libraryname' => 'H5P.Lib3',
                'expected' => false,
            ],
            'Library with only one version, disabled' => [
                'libraryname' => 'H5P.Lib4',
                'expected' => false,
            ],
            'Library with only one version, enabled' => [
                'libraryname' => 'H5P.Lib5',
                'expected' => true,
            ],
            'Library with 2 versions, one of them disabled (using id) - 1.1 (disabled)' => [
                'libraryname' => 'H5P.Lib1.1',
                'expected' => false,
                'exception' => false,
                'useid' => true,
            ],
            'Library with 2 versions, one of them disabled (using id) - 1.2 (enabled)' => [
                'libraryname' => 'H5P.Lib1.2',
                'expected' => true,
                'exception' => false,
                'useid' => true,
            ],
            'Library with 2 versions, all enabled (using id) - 2.1' => [
                'libraryname' => 'H5P.Lib2.1',
                'expected' => true,
                'exception' => false,
                'useid' => true,
            ],
            'Library with 2 versions, all enabled (using id) - 2.2' => [
                'libraryname' => 'H5P.Lib2.2',
                'expected' => true,
                'exception' => false,
                'useid' => true,
            ],
            'Library with 2 versions, all disabled (using id) - 3.1' => [
                'libraryname' => 'H5P.Lib3.1',
                'expected' => false,
                'exception' => false,
                'useid' => true,
            ],
            'Library with 2 versions, all disabled (using id) - 3.2' => [
                'libraryname' => 'H5P.Lib3.2',
                'expected' => false,
                'exception' => false,
                'useid' => true,
            ],
            'Library with only one version, disabled (using id)' => [
                'libraryname' => 'H5P.Lib4.1',
                'expected' => false,
                'exception' => false,
                'useid' => true,
            ],
            'Library with only one version, enabled (using id)' => [
                'libraryname' => 'H5P.Lib5.1',
                'expected' => true,
                'exception' => false,
                'useid' => true,
            ],
            'Unexisting library' => [
                'libraryname' => 'H5P.Unexisting',
                'expected' => true,
            ],
            'Missing required parameters' => [
                'libraryname' => 'H5P.Unexisting',
                'expected' => false,
                'exception' => true,
                'useid' => false,
                'uselibraryname' => false,
            ],
        ];
    }

    /**
     * Test the behaviour of is_valid_package().
     * @runInSeparateProcess
     *
     * @covers ::is_valid_package
     * @dataProvider is_valid_package_provider
     *
     * @param string $filename The H5P content to validate.
     * @param bool $expected Expected result after calling the method.
     * @param bool $isadmin Whether the user calling the method will be admin or not.
     * @param bool $onlyupdatelibs Whether new libraries can be installed or only the existing ones can be updated.
     * @param bool $skipcontent Should the content be skipped (so only the libraries will be saved)?
     */
    public function test_is_valid_package(string $filename, bool $expected, bool $isadmin = false, bool $onlyupdatelibs = false,
            bool $skipcontent = false): void {
        global $USER;

        $this->resetAfterTest();

        if ($isadmin) {
            $this->setAdminUser();
            $user = $USER;
        } else {
            // Create a user.
            $user = $this->getDataGenerator()->create_user();
            $this->setUser($user);
        }

        // Prepare the file.
        $path = __DIR__ . $filename;
        $file = helper::create_fake_stored_file_from_path($path, (int)$user->id);

        // Check if the H5P content is valid or not.
        $result = api::is_valid_package($file, $onlyupdatelibs, $skipcontent);
        $this->assertEquals($expected, $result);
    }

    /**
     * Data provider for test_is_valid_package().
     *
     * @return array
     */
    public static function is_valid_package_provider(): array {
        return [
            'Valid H5P file (as admin)' => [
                'filename' => '/fixtures/greeting-card.h5p',
                'expected' => true,
                'isadmin' => true,
            ],
            'Valid H5P file (as user) without library update and checking content' => [
                'filename' => '/fixtures/greeting-card.h5p',
                'expected' => false, // Libraries are missing and user hasn't the right permissions to upload them.
                'isadmin' => false,
                'onlyupdatelibs' => false,
                'skipcontent' => false,
            ],
            'Valid H5P file (as user) with library update and checking content' => [
                'filename' => '/fixtures/greeting-card.h5p',
                'expected' => false, // Libraries are missing and user hasn't the right permissions to upload them.
                'isadmin' => false,
                'onlyupdatelibs' => true,
                'skipcontent' => false,
            ],
            'Valid H5P file (as user) without library update and skipping content' => [
                'filename' => '/fixtures/greeting-card.h5p',
                'expected' => true, // Content check is skipped so the package will be considered valid.
                'isadmin' => false,
                'onlyupdatelibs' => false,
                'skipcontent' => true,
            ],
            'Valid H5P file (as user) with library update and skipping content' => [
                'filename' => '/fixtures/greeting-card.h5p',
                'expected' => true, // Content check is skipped so the package will be considered valid.
                'isadmin' => false,
                'onlyupdatelibs' => true,
                'skipcontent' => true,
            ],
            'Invalid H5P file (as admin)' => [
                'filename' => '/fixtures/h5ptest.zip',
                'expected' => false,
                'isadmin' => true,
            ],
            'Invalid H5P file (as user)' => [
                'filename' => '/fixtures/h5ptest.zip',
                'expected' => false,
                'isadmin' => false,
            ],
            'Invalid H5P file (as user) skipping content' => [
                'filename' => '/fixtures/h5ptest.zip',
                'expected' => true, // Content check is skipped so the package will be considered valid.
                'isadmin' => false,
                'onlyupdatelibs' => false,
                'skipcontent' => true,
            ],
        ];
    }
}

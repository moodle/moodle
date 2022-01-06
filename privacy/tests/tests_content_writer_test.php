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
 * Unit Tests for the Content Writer used for unit testing.
 *
 * @package     core_privacy
 * @category    test
 * @copyright   2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

use \core_privacy\local\request\writer;
use \core_privacy\tests\request\content_writer;

/**
 * Unit Tests for the Content Writer used for unit testing.
 *
 * @copyright   2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tests_content_writer_test extends advanced_testcase {

    /**
     * It should be possible to store and retrieve data.
     */
    public function test_export_data() {
        $context = \context_system::instance();
        $writer = $this->get_writer_instance();

        $dataa = (object) [
            'example' => 'a',
        ];
        $datab = (object) [
            'example' => 'b',
        ];

        $writer->set_context($context)
            ->export_data(['data'], $dataa)
            ->export_data([], $datab);

        $data = $writer->get_data([]);
        $this->assertSame($datab, $data);

        $data = $writer->get_data(['data']);
        $this->assertSame($dataa, $data);
    }

    /**
     * It should be possible to store and retrieve data at the same point in different contexts.
     */
    public function test_export_data_no_context_clash() {
        $writer = $this->get_writer_instance();

        $context = \context_system::instance();
        $dataa = (object) [
            'example' => 'a',
        ];
        $writer->set_context($context)
            ->export_data(['data'], $dataa);

        $adminuser = \core_user::get_user_by_username('admin');
        $usercontext = \context_user::instance($adminuser->id);
        $datab = (object) [
            'example' => 'b',
        ];
        $writer->set_context($usercontext)
            ->export_data(['data'], $datab);

        $writer->set_context($context);
        $data = $writer->get_data(['data']);
        $this->assertSame($dataa, $data);
        $this->assertTrue($writer->has_any_data());
        $this->assertTrue($writer->has_any_data(['data']));
        $this->assertFalse($writer->has_any_data(['somepath']));

        $writer->set_context($usercontext);
        $data = $writer->get_data(['data']);
        $this->assertSame($datab, $data);
    }

    /**
     * Test export and recover with children.
     */
    public function test_get_data_with_children() {
        $writer = $this->get_writer_instance();
        $context = \context_system::instance();

        $writer->set_context($context)
            ->export_data(['a'], (object) ['parent' => true])
            ->export_data(['a', 'b'], (object) ['parent' => false]);

        $this->assertTrue($writer->get_data(['a'])->parent);
        $this->assertFalse($writer->get_data(['a', 'b'])->parent);
        $this->assertEquals([], $writer->get_data(['a', 'b', 'c']));
    }

    /**
     * It should be possible to store and retrieve metadata.
     */
    public function test_export_metadata() {
        $context = \context_system::instance();
        $writer = $this->get_writer_instance();

        $writer->set_context($context)
            ->export_metadata(['metadata'], 'somekey', 'value1', 'description1')
            ->export_metadata([], 'somekey', 'value2', 'description2');

        $allmetadata = $writer->get_all_metadata([]);
        $this->assertCount(1, $allmetadata);
        $this->assertArrayHasKey('somekey', $allmetadata);
        $this->assertEquals('value2', $allmetadata['somekey']->value);
        $this->assertEquals('description2', $allmetadata['somekey']->description);

        $metadata = $writer->get_metadata([], 'somekey', false);
        $this->assertEquals('value2', $metadata->value);
        $this->assertEquals('description2', $metadata->description);
        $this->assertEquals('value2', $writer->get_metadata([], 'somekey', true));

        $allmetadata = $writer->get_all_metadata(['metadata']);
        $this->assertCount(1, $allmetadata);
        $this->assertArrayHasKey('somekey', $allmetadata);
        $this->assertEquals('value1', $allmetadata['somekey']->value);
        $this->assertEquals('description1', $allmetadata['somekey']->description);

        $metadata = $writer->get_metadata(['metadata'], 'somekey', false);
        $this->assertEquals('value1', $metadata->value);
        $this->assertEquals('description1', $metadata->description);
        $this->assertEquals('value1', $writer->get_metadata(['metadata'], 'somekey', true));
    }

    /**
     * It should be possible to store and retrieve metadata at the same point in different contexts.
     */
    public function test_export_metadata_no_context_clash() {
        $writer = $this->get_writer_instance();

        $context = \context_system::instance();
        $writer->set_context($context)
            ->export_metadata(['metadata'], 'somekey', 'value1', 'description1');

        $adminuser = \core_user::get_user_by_username('admin');
        $usercontext = \context_user::instance($adminuser->id);
        $writer->set_context($usercontext)
            ->export_metadata(['metadata'], 'somekey', 'value2', 'description2');

        $writer->set_context($context);
        $allmetadata = $writer->get_all_metadata(['metadata']);
        $this->assertCount(1, $allmetadata);
        $this->assertArrayHasKey('somekey', $allmetadata);
        $this->assertEquals('value1', $allmetadata['somekey']->value);
        $this->assertEquals('description1', $allmetadata['somekey']->description);

        $metadata = $writer->get_metadata(['metadata'], 'somekey', false);
        $this->assertEquals('value1', $metadata->value);
        $this->assertEquals('description1', $metadata->description);
        $this->assertEquals('value1', $writer->get_metadata(['metadata'], 'somekey', true));

        $writer->set_context($usercontext);
        $allmetadata = $writer->get_all_metadata(['metadata']);
        $this->assertCount(1, $allmetadata);
        $this->assertArrayHasKey('somekey', $allmetadata);
        $this->assertEquals('value2', $allmetadata['somekey']->value);
        $this->assertEquals('description2', $allmetadata['somekey']->description);

        $metadata = $writer->get_metadata(['metadata'], 'somekey', false);
        $this->assertEquals('value2', $metadata->value);
        $this->assertEquals('description2', $metadata->description);
        $this->assertEquals('value2', $writer->get_metadata(['metadata'], 'somekey', true));
        $this->assertTrue($writer->has_any_data());
        $this->assertTrue($writer->has_any_data(['metadata']));
        $this->assertFalse($writer->has_any_data(['somepath']));
    }

    /**
     * It should be possible to store and retrieve user preferences.
     */
    public function test_export_user_preference() {
        $context = \context_system::instance();
        $adminuser = \core_user::get_user_by_username('admin');
        $usercontext = \context_user::instance($adminuser->id);
        $writer = $this->get_writer_instance();

        $writer->set_context($context)
            ->export_user_preference('core_privacy', 'somekey', 'value0', 'description0');
        $writer->set_context($usercontext)
            ->export_user_preference('core_tests', 'somekey', 'value1', 'description1')
            ->export_user_preference('core_privacy', 'somekey', 'value2', 'description2')
            ->export_user_preference('core_tests', 'someotherkey', 'value2', 'description2');

        $writer->set_context($usercontext);

        $someprefs = $writer->get_user_preferences('core_privacy');
        $this->assertCount(1, (array) $someprefs);
        $this->assertTrue(isset($someprefs->somekey));
        $this->assertEquals('value0', $someprefs->somekey->value);
        $this->assertEquals('description0', $someprefs->somekey->description);

        $someprefs = $writer->get_user_context_preferences('core_tests');
        $this->assertCount(2, (array) $someprefs);
        $this->assertTrue(isset($someprefs->somekey));
        $this->assertEquals('value1', $someprefs->somekey->value);
        $this->assertEquals('description1', $someprefs->somekey->description);
        $this->assertTrue(isset($someprefs->someotherkey));
        $this->assertEquals('value2', $someprefs->someotherkey->value);
        $this->assertEquals('description2', $someprefs->someotherkey->description);

        $someprefs = $writer->get_user_context_preferences('core_privacy');
        $this->assertCount(1, (array) $someprefs);
        $this->assertTrue(isset($someprefs->somekey));
        $this->assertEquals('value2', $someprefs->somekey->value);
        $this->assertEquals('description2', $someprefs->somekey->description);
    }

    /**
     * It should be possible to store and retrieve user preferences at the same point in different contexts.
     */
    public function test_export_user_preference_no_context_clash() {
        $writer = $this->get_writer_instance();
        $context = \context_system::instance();
        $coursecontext = \context_course::instance(SITEID);
        $adminuser = \core_user::get_user_by_username('admin');
        $usercontext = \context_user::instance($adminuser->id);

        $writer->set_context($context)
            ->export_user_preference('core_tests', 'somekey', 'value0', 'description0');
        $writer->set_context($coursecontext)
            ->export_user_preference('core_tests', 'somekey', 'value1', 'description1');
        $writer->set_context($usercontext)
            ->export_user_preference('core_tests', 'somekey', 'value2', 'description2');

        // Set the course context and fetch with get_user_preferences to get the global preference.
        $writer->set_context($coursecontext);
        $someprefs = $writer->get_user_preferences('core_tests');
        $this->assertCount(1, (array) $someprefs);
        $this->assertTrue(isset($someprefs->somekey));
        $this->assertEquals('value0', $someprefs->somekey->value);
        $this->assertEquals('description0', $someprefs->somekey->description);

        // Set the course context and fetch with get_user_context_preferences.
        $someprefs = $writer->get_user_context_preferences('core_tests');
        $this->assertCount(1, (array) $someprefs);
        $this->assertTrue(isset($someprefs->somekey));
        $this->assertEquals('value1', $someprefs->somekey->value);
        $this->assertEquals('description1', $someprefs->somekey->description);

        $writer->set_context($usercontext);
        $someprefs = $writer->get_user_context_preferences('core_tests');
        $this->assertCount(1, (array) $someprefs);
        $this->assertTrue(isset($someprefs->somekey));
        $this->assertEquals('value2', $someprefs->somekey->value);
        $this->assertEquals('description2', $someprefs->somekey->description);
    }

    /**
     * Test export and recover with children.
     */
    public function test_get_metadata_with_children() {
        $writer = $this->get_writer_instance();
        $context = \context_system::instance();

        $writer->set_context($context)
            ->export_metadata(['a'], 'abc', 'ABC', 'A, B, C')
            ->export_metadata(['a', 'b'], 'def', 'DEF', 'D, E, F');

        $this->assertEquals('ABC', $writer->get_metadata(['a'], 'abc'));
        $this->assertEquals('DEF', $writer->get_metadata(['a', 'b'], 'def'));
    }

    /**
     * It should be possible to export files in the files and children contexts.
     */
    public function test_export_file_special_folders() {
        $context = \context_system::instance();

        $filea = $this->get_stored_file('/', 'files');
        $fileb = $this->get_stored_file('/children/', 'foo.zip');

        $writer = $this->get_writer_instance()
            ->set_context($context)
            ->export_file([], $filea)
            ->export_file([], $fileb);

        $files = $writer->get_files([]);

        $this->assertCount(2, $files);
        $this->assertEquals($filea, $files['files']);
        $this->assertEquals($fileb, $files['children/foo.zip']);
    }

    /**
     * It should be possible to export mutliple files in the same subcontext/path space but different context and not
     * have them clash.
     */
    public function test_export_file_no_context_clash() {
        $writer = $this->get_writer_instance();
        $context = \context_system::instance();
        $filea = $this->get_stored_file('/foo/', 'foo.txt');
        $writer = $this->get_writer_instance()
            ->set_context($context)
            ->export_file([], $filea);

        $adminuser = \core_user::get_user_by_username('admin');
        $usercontext = \context_user::instance($adminuser->id);
        $fileb = $this->get_stored_file('/foo/', 'foo.txt');
        $writer->set_context($usercontext)
            ->export_file([], $fileb);

        $writer->set_context($context);
        $files = $writer->get_files([]);
        $this->assertCount(1, $files);
        $this->assertEquals($filea, $files['foo/foo.txt']);

        $writer->set_context($usercontext);
        $files = $writer->get_files([]);
        $this->assertCount(1, $files);
        $this->assertEquals($fileb, $files['foo/foo.txt']);
        $this->assertTrue($writer->has_any_data());
        $this->assertFalse($writer->has_any_data(['somepath']));
    }

    /**
     * Test export and recover with children.
     */
    public function test_get_file_with_children() {
        $writer = $this->get_writer_instance();
        $context = \context_system::instance();

        $filea = $this->get_stored_file('/foo/', 'foo.txt');
        $fileb = $this->get_stored_file('/foo/', 'foo.txt');

        $writer->set_context($context)
            ->export_file(['a'], $filea)
            ->export_file(['a', 'b'], $fileb);

        $files = $writer->get_files(['a']);
        $this->assertCount(1, $files);
        $this->assertEquals($filea, $files['foo/foo.txt']);

        $files = $writer->get_files(['a', 'b']);
        $this->assertCount(1, $files);
        $this->assertEquals($fileb, $files['foo/foo.txt']);
    }

    /**
     * It should be possible to export related data in the files and children contexts.
     */
    public function test_export_related_data() {
        $context = \context_system::instance();

        $writer = $this->get_writer_instance()
            ->set_context($context)
            ->export_related_data(['file', 'data'], 'file', 'data1')
            ->export_related_data([], 'file', 'data2');

        $data = $writer->get_related_data([]);
        $this->assertCount(1, $data);
        $this->assertEquals('data2', $data['file']);

        $data = $writer->get_related_data([], 'file');
        $this->assertEquals('data2', $data);

        $data = $writer->get_related_data(['file', 'data']);
        $this->assertCount(1, $data);
        $this->assertEquals('data1', $data['file']);

        $data = $writer->get_related_data(['file', 'data'], 'file');
        $this->assertEquals('data1', $data);
        $this->assertTrue($writer->has_any_data());
        $this->assertTrue($writer->has_any_data(['file']));
        $this->assertTrue($writer->has_any_data(['file', 'data']));
        $this->assertFalse($writer->has_any_data(['somepath']));
    }

    /**
     * It should be possible to export related data in the same location,but in a different context.
     */
    public function test_export_related_data_no_context_clash() {
        $writer = $this->get_writer_instance();

        $context = \context_system::instance();
        $writer->set_context($context)
            ->export_related_data(['file', 'data'], 'file', 'data1');

        $adminuser = \core_user::get_user_by_username('admin');
        $usercontext = \context_user::instance($adminuser->id);
        $writer->set_context($usercontext)
            ->export_related_data(['file', 'data'], 'file', 'data2');

        $writer->set_context($context);
        $data = $writer->get_related_data(['file', 'data']);
        $this->assertCount(1, $data);
        $this->assertEquals('data1', $data['file']);

        $writer->set_context($usercontext);
        $data = $writer->get_related_data(['file', 'data']);
        $this->assertCount(1, $data);
        $this->assertEquals('data2', $data['file']);
    }

    /**
     * Test export and recover with children.
     */
    public function test_get_related_data_with_children() {
        $writer = $this->get_writer_instance();
        $context = \context_system::instance();

        $writer->set_context($context)
            ->export_related_data(['a'], 'abc', 'ABC')
            ->export_related_data(['a', 'b'], 'def', 'DEF');

        $this->assertEquals('ABC', $writer->get_related_data(['a'], 'abc'));
        $this->assertEquals('DEF', $writer->get_related_data(['a', 'b'], 'def'));
    }

    /**
     * It should be possible to export related files in the files and children contexts.
     */
    public function test_export_custom_file() {
        $context = \context_system::instance();

        $writer = $this->get_writer_instance()
            ->set_context($context)
            ->export_custom_file(['file.txt'], 'file.txt', 'Content 1')
            ->export_custom_file([], 'file.txt', 'Content 2');

        $files = $writer->get_custom_file([]);
        $this->assertCount(1, $files);
        $this->assertEquals('Content 2', $files['file.txt']);
        $file = $writer->get_custom_file([], 'file.txt');
        $this->assertEquals('Content 2', $file);

        $files = $writer->get_custom_file(['file.txt']);
        $this->assertCount(1, $files);
        $this->assertEquals('Content 1', $files['file.txt']);
        $file = $writer->get_custom_file(['file.txt'], 'file.txt');
        $this->assertEquals('Content 1', $file);
        $this->assertTrue($writer->has_any_data());
        $this->assertTrue($writer->has_any_data(['file.txt']));
        $this->assertFalse($writer->has_any_data(['somepath']));
    }

    /**
     * It should be possible to export related files in the same location
     * in different contexts.
     */
    public function test_export_custom_file_no_context_clash() {
        $writer = $this->get_writer_instance();
        $context = \context_system::instance();

        $writer->set_context($context)
            ->export_custom_file(['file.txt'], 'file.txt', 'Content 1');

        $adminuser = \core_user::get_user_by_username('admin');
        $usercontext = \context_user::instance($adminuser->id);
        $writer->set_context($usercontext)
            ->export_custom_file(['file.txt'], 'file.txt', 'Content 2');

        $writer->set_context($context);
        $files = $writer->get_custom_file(['file.txt']);
        $this->assertCount(1, $files);
        $this->assertEquals('Content 1', $files['file.txt']);

        $writer->set_context($usercontext);
        $files = $writer->get_custom_file(['file.txt']);
        $this->assertCount(1, $files);
        $this->assertEquals('Content 2', $files['file.txt']);
    }

    /**
     * Test export and recover with children.
     */
    public function test_get_custom_file_with_children() {
        $writer = $this->get_writer_instance();
        $context = \context_system::instance();

        $writer->set_context($context)
            ->export_custom_file(['a'], 'file.txt', 'ABC')
            ->export_custom_file(['a', 'b'], 'file.txt', 'DEF');

        $this->assertEquals('ABC', $writer->get_custom_file(['a'], 'file.txt'));
        $this->assertEquals('DEF', $writer->get_custom_file(['a', 'b'], 'file.txt'));
    }

    /**
     * Get a fresh content writer.
     *
     * @return  moodle_content_writer
     */
    public function get_writer_instance() {
        $factory = $this->createMock(writer::class);
        return new content_writer($factory);
    }

    /**
     * Helper to create a stored file objectw with the given supplied content.
     *
     * @param   string  $filepath The file path to use in the stored_file
     * @param   string  $filename The file name to use in the stored_file
     * @return  stored_file
     */
    protected function get_stored_file($filepath, $filename) {
        static $counter = 0;
        $counter++;
        $filecontent = "Example content {$counter}";
        $contenthash = file_storage::hash_from_string($filecontent);

        $file = $this->getMockBuilder(stored_file::class)
            ->setMethods(null)
            ->setConstructorArgs([
                get_file_storage(),
                (object) [
                    'contenthash' => $contenthash,
                    'filesize' => strlen($filecontent),
                    'filepath' => $filepath,
                    'filename' => $filename,
                ]
            ])
            ->getMock();

        return $file;
    }
}

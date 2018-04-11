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

        $writer->set_context($usercontext);
        $data = $writer->get_data(['data']);
        $this->assertSame($datab, $data);
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

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
 * Unit Tests for the Moodle Content Writer.
 *
 * @package     core_privacy
 * @category    test
 * @copyright   2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

use \core_privacy\local\request\writer;
use \core_privacy\local\request\moodle_content_writer;

/**
 * Tests for the \core_privacy API's moodle_content_writer functionality.
 *
 * @copyright   2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class moodle_content_writer_test extends advanced_testcase {

    /**
     * Test that exported data is saved correctly within the system context.
     *
     * @dataProvider export_data_provider
     * @param   \stdClass  $data Data
     */
    public function test_export_data($data) {
        $context = \context_system::instance();
        $subcontext = [];

        $writer = $this->get_writer_instance()
            ->set_context($context)
            ->export_data($subcontext, $data);

        $fileroot = $this->fetch_exported_content($writer);

        $contextpath = $this->get_context_path($context, $subcontext, 'data.json');
        $this->assertTrue($fileroot->hasChild($contextpath));

        $json = $fileroot->getChild($contextpath)->getContent();
        $expanded = json_decode($json);
        $this->assertEquals($data, $expanded);
    }

    /**
     * Test that exported data is saved correctly for context/subcontext.
     *
     * @dataProvider export_data_provider
     * @param   \stdClass  $data Data
     */
    public function test_export_data_different_context($data) {
        $context = \context_user::instance(\core_user::get_user_by_username('admin')->id);
        $subcontext = ['sub', 'context'];

        $writer = $this->get_writer_instance()
            ->set_context($context)
            ->export_data($subcontext, $data);

        $fileroot = $this->fetch_exported_content($writer);

        $contextpath = $this->get_context_path($context, $subcontext, 'data.json');
        $this->assertTrue($fileroot->hasChild($contextpath));

        $json = $fileroot->getChild($contextpath)->getContent();
        $expanded = json_decode($json);
        $this->assertEquals($data, $expanded);
    }

    /**
     * Test that exported is saved within the correct directory locations.
     */
    public function test_export_data_writes_to_multiple_context() {
        $subcontext = ['sub', 'context'];

        $systemcontext = \context_system::instance();
        $systemdata = (object) [
            'belongsto' => 'system',
        ];
        $usercontext = \context_user::instance(\core_user::get_user_by_username('admin')->id);
        $userdata = (object) [
            'belongsto' => 'user',
        ];

        $writer = $this->get_writer_instance();

        $writer
            ->set_context($systemcontext)
            ->export_data($subcontext, $systemdata);

        $writer
            ->set_context($usercontext)
            ->export_data($subcontext, $userdata);

        $fileroot = $this->fetch_exported_content($writer);

        $contextpath = $this->get_context_path($systemcontext, $subcontext, 'data.json');
        $this->assertTrue($fileroot->hasChild($contextpath));

        $json = $fileroot->getChild($contextpath)->getContent();
        $expanded = json_decode($json);
        $this->assertEquals($systemdata, $expanded);

        $contextpath = $this->get_context_path($usercontext, $subcontext, 'data.json');
        $this->assertTrue($fileroot->hasChild($contextpath));

        $json = $fileroot->getChild($contextpath)->getContent();
        $expanded = json_decode($json);
        $this->assertEquals($userdata, $expanded);
    }

    /**
     * Test that multiple writes to the same location cause the latest version to be written.
     */
    public function test_export_data_multiple_writes_same_context() {
        $subcontext = ['sub', 'context'];

        $systemcontext = \context_system::instance();
        $originaldata = (object) [
            'belongsto' => 'system',
        ];

        $newdata = (object) [
            'abc' => 'def',
        ];

        $writer = $this->get_writer_instance();

        $writer
            ->set_context($systemcontext)
            ->export_data($subcontext, $originaldata);

        $writer
            ->set_context($systemcontext)
            ->export_data($subcontext, $newdata);

        $fileroot = $this->fetch_exported_content($writer);

        $contextpath = $this->get_context_path($systemcontext, $subcontext, 'data.json');
        $this->assertTrue($fileroot->hasChild($contextpath));

        $json = $fileroot->getChild($contextpath)->getContent();
        $expanded = json_decode($json);
        $this->assertEquals($newdata, $expanded);
    }

    /**
     * Data provider for exporting user data.
     */
    public function export_data_provider() {
        return [
            'basic' => [
                (object) [
                    'example' => (object) [
                        'key' => 'value',
                    ],
                ],
            ],
        ];
    }

    /**
     * Test that metadata can be set.
     *
     * @dataProvider export_metadata_provider
     * @param   string  $key Key
     * @param   string  $value Value
     * @param   string  $description Description
     */
    public function test_export_metadata($key, $value, $description) {
        $context = \context_system::instance();
        $subcontext = ['a', 'b', 'c'];

        $writer = $this->get_writer_instance()
            ->set_context($context)
            ->export_metadata($subcontext, $key, $value, $description);

        $fileroot = $this->fetch_exported_content($writer);

        $contextpath = $this->get_context_path($context, $subcontext, 'metadata.json');
        $this->assertTrue($fileroot->hasChild($contextpath));

        $json = $fileroot->getChild($contextpath)->getContent();
        $expanded = json_decode($json);
        $this->assertTrue(isset($expanded->$key));
        $this->assertEquals($value, $expanded->$key->value);
        $this->assertEquals($description, $expanded->$key->description);
    }

    /**
     * Test that metadata can be set additively.
     */
    public function test_export_metadata_additive() {
        $context = \context_system::instance();
        $subcontext = [];

        $writer = $this->get_writer_instance();

        $writer
            ->set_context($context)
            ->export_metadata($subcontext, 'firstkey', 'firstvalue', 'firstdescription');

        $writer
            ->set_context($context)
            ->export_metadata($subcontext, 'secondkey', 'secondvalue', 'seconddescription');

        $fileroot = $this->fetch_exported_content($writer);

        $contextpath = $this->get_context_path($context, $subcontext, 'metadata.json');
        $this->assertTrue($fileroot->hasChild($contextpath));

        $json = $fileroot->getChild($contextpath)->getContent();
        $expanded = json_decode($json);

        $this->assertTrue(isset($expanded->firstkey));
        $this->assertEquals('firstvalue', $expanded->firstkey->value);
        $this->assertEquals('firstdescription', $expanded->firstkey->description);

        $this->assertTrue(isset($expanded->secondkey));
        $this->assertEquals('secondvalue', $expanded->secondkey->value);
        $this->assertEquals('seconddescription', $expanded->secondkey->description);
    }

    /**
     * Test that metadata can be set additively.
     */
    public function test_export_metadata_to_multiple_contexts() {
        $systemcontext = \context_system::instance();
        $usercontext = \context_user::instance(\core_user::get_user_by_username('admin')->id);
        $subcontext = [];

        $writer = $this->get_writer_instance();

        $writer
            ->set_context($systemcontext)
            ->export_metadata($subcontext, 'firstkey', 'firstvalue', 'firstdescription')
            ->export_metadata($subcontext, 'secondkey', 'secondvalue', 'seconddescription');

        $writer
            ->set_context($usercontext)
            ->export_metadata($subcontext, 'firstkey', 'alternativevalue', 'alternativedescription')
            ->export_metadata($subcontext, 'thirdkey', 'thirdvalue', 'thirddescription');

        $fileroot = $this->fetch_exported_content($writer);

        $systemcontextpath = $this->get_context_path($systemcontext, $subcontext, 'metadata.json');
        $this->assertTrue($fileroot->hasChild($systemcontextpath));

        $json = $fileroot->getChild($systemcontextpath)->getContent();
        $expanded = json_decode($json);

        $this->assertTrue(isset($expanded->firstkey));
        $this->assertEquals('firstvalue', $expanded->firstkey->value);
        $this->assertEquals('firstdescription', $expanded->firstkey->description);
        $this->assertTrue(isset($expanded->secondkey));
        $this->assertEquals('secondvalue', $expanded->secondkey->value);
        $this->assertEquals('seconddescription', $expanded->secondkey->description);
        $this->assertFalse(isset($expanded->thirdkey));

        $usercontextpath = $this->get_context_path($usercontext, $subcontext, 'metadata.json');
        $this->assertTrue($fileroot->hasChild($usercontextpath));

        $json = $fileroot->getChild($usercontextpath)->getContent();
        $expanded = json_decode($json);

        $this->assertTrue(isset($expanded->firstkey));
        $this->assertEquals('alternativevalue', $expanded->firstkey->value);
        $this->assertEquals('alternativedescription', $expanded->firstkey->description);
        $this->assertFalse(isset($expanded->secondkey));
        $this->assertTrue(isset($expanded->thirdkey));
        $this->assertEquals('thirdvalue', $expanded->thirdkey->value);
        $this->assertEquals('thirddescription', $expanded->thirdkey->description);
    }

    /**
     * Data provider for exporting user metadata.
     *
     * return   array
     */
    public function export_metadata_provider() {
        return [
            'basic' => [
                'key',
                'value',
                'This is a description',
            ],
            'valuewithspaces' => [
                'key',
                'value has mixed',
                'This is a description',
            ],
            'encodedvalue' => [
                'key',
                base64_encode('value has mixed'),
                'This is a description',
            ],
        ];
    }

    /**
     * Exporting a single stored_file should cause that file to be output in the files directory.
     */
    public function test_export_area_files() {
        $this->resetAfterTest();
        $context = \context_system::instance();
        $fs = get_file_storage();

        // Add two files to core_privacy::tests::0.
        $files = [];
        $file = (object) [
            'component' => 'core_privacy',
            'filearea' => 'tests',
            'itemid' => 0,
            'path' => '/',
            'name' => 'a.txt',
            'content' => 'Test file 0',
        ];
        $files[] = $file;

        $file = (object) [
            'component' => 'core_privacy',
            'filearea' => 'tests',
            'itemid' => 0,
            'path' => '/sub/',
            'name' => 'b.txt',
            'content' => 'Test file 1',
        ];
        $files[] = $file;

        // One with a different itemid.
        $file = (object) [
            'component' => 'core_privacy',
            'filearea' => 'tests',
            'itemid' => 1,
            'path' => '/',
            'name' => 'c.txt',
            'content' => 'Other',
        ];
        $files[] = $file;

        // One with a different filearea.
        $file = (object) [
            'component' => 'core_privacy',
            'filearea' => 'alternative',
            'itemid' => 0,
            'path' => '/',
            'name' => 'd.txt',
            'content' => 'Alternative',
        ];
        $files[] = $file;

        // One with a different component.
        $file = (object) [
            'component' => 'core',
            'filearea' => 'tests',
            'itemid' => 0,
            'path' => '/',
            'name' => 'e.txt',
            'content' => 'Other tests',
        ];
        $files[] = $file;

        foreach ($files as $file) {
            $record = [
                'contextid' => $context->id,
                'component' => $file->component,
                'filearea'  => $file->filearea,
                'itemid'    => $file->itemid,
                'filepath'  => $file->path,
                'filename'  => $file->name,
            ];

            $file->namepath = '/' . $file->filearea . '/' . ($file->itemid ?: '') . $file->path . $file->name;
            $file->storedfile = $fs->create_file_from_string($record, $file->content);
        }

        $writer = $this->get_writer_instance()
            ->set_context($context)
            ->export_area_files([], 'core_privacy', 'tests', 0);

        $fileroot = $this->fetch_exported_content($writer);

        $firstfiles = array_slice($files, 0, 2);
        foreach ($firstfiles as $file) {
            $contextpath = $this->get_context_path($context, ['_files'], $file->namepath);
            $this->assertTrue($fileroot->hasChild($contextpath));
            $this->assertEquals($file->content, $fileroot->getChild($contextpath)->getContent());
        }

        $otherfiles = array_slice($files, 2);
        foreach ($otherfiles as $file) {
            $contextpath = $this->get_context_path($context, ['_files'], $file->namepath);
            $this->assertFalse($fileroot->hasChild($contextpath));
        }
    }

    /**
     * Exporting a single stored_file should cause that file to be output in the files directory.
     *
     * @dataProvider    export_file_provider
     * @param   string  $filearea File area
     * @param   int     $itemid Item ID
     * @param   string  $filepath File path
     * @param   string  $filename File name
     * @param   string  $content Content
     */
    public function test_export_file($filearea, $itemid, $filepath, $filename, $content) {
        $this->resetAfterTest();
        $context = \context_system::instance();
        $filenamepath = '/' . $filearea . '/' . ($itemid ?: '') . $filepath . $filename;

        $filerecord = array(
            'contextid' => $context->id,
            'component' => 'core_privacy',
            'filearea'  => $filearea,
            'itemid'    => $itemid,
            'filepath'  => $filepath,
            'filename'  => $filename,
        );

        $fs = get_file_storage();
        $file = $fs->create_file_from_string($filerecord, $content);

        $writer = $this->get_writer_instance()
            ->set_context($context)
            ->export_file([], $file);

        $fileroot = $this->fetch_exported_content($writer);

        $contextpath = $this->get_context_path($context, ['_files'], $filenamepath);
        $this->assertTrue($fileroot->hasChild($contextpath));
        $this->assertEquals($content, $fileroot->getChild($contextpath)->getContent());
    }

    /**
     * Data provider for the test_export_file function.
     *
     * @return  array
     */
    public function export_file_provider() {
        return [
            'basic' => [
                'intro',
                0,
                '/',
                'testfile.txt',
                'An example file content',
            ],
            'longpath' => [
                'attachments',
                '12',
                '/path/within/a/path/within/a/path/',
                'testfile.txt',
                'An example file content',
            ],
            'pathwithspaces' => [
                'intro',
                0,
                '/path with/some spaces/',
                'testfile.txt',
                'An example file content',
            ],
            'filewithspaces' => [
                'submission_attachments',
                1,
                '/path with/some spaces/',
                'test file.txt',
                'An example file content',
            ],
            'image' => [
                'intro',
                0,
                '/',
                'logo.png',
                file_get_contents(__DIR__ . '/fixtures/logo.png'),
            ],
            'UTF8' => [
                'submission_content',
                2,
                '/Žluťoučký/',
                'koníček.txt',
                'koníček',
            ],
            'EUC-JP' => [
                'intro',
                0,
                '/言語設定/',
                '言語設定.txt',
                '言語設定',
            ],
        ];
    }

    /**
     * User preferences can be exported against a user.
     *
     * @dataProvider    export_user_preference_provider
     * @param   string      $component  Component
     * @param   string      $key Key
     * @param   string      $value Value
     * @param   string      $desc Description
     */
    public function test_export_user_preference_context_user($component, $key, $value, $desc) {
        $admin = \core_user::get_user_by_username('admin');

        $writer = $this->get_writer_instance();

        $context = \context_user::instance($admin->id);
        $writer = $this->get_writer_instance()
            ->set_context($context)
            ->export_user_preference($component, $key, $value, $desc);

        $fileroot = $this->fetch_exported_content($writer);

        $contextpath = $this->get_context_path($context, [get_string('userpreferences')], "{$component}.json");
        $this->assertTrue($fileroot->hasChild($contextpath));

        $json = $fileroot->getChild($contextpath)->getContent();
        $expanded = json_decode($json);
        $this->assertTrue(isset($expanded->$key));
        $data = $expanded->$key;
        $this->assertEquals($value, $data->value);
        $this->assertEquals($desc, $data->description);
    }

    /**
     * User preferences can be exported against a course category.
     *
     * @dataProvider    export_user_preference_provider
     * @param   string      $component  Component
     * @param   string      $key Key
     * @param   string      $value Value
     * @param   string      $desc Description
     */
    public function test_export_user_preference_context_coursecat($component, $key, $value, $desc) {
        global $DB;

        $categories = $DB->get_records('course_categories');
        $firstcategory = reset($categories);

        $context = \context_coursecat::instance($firstcategory->id);
        $writer = $this->get_writer_instance()
            ->set_context($context)
            ->export_user_preference($component, $key, $value, $desc);

        $fileroot = $this->fetch_exported_content($writer);

        $contextpath = $this->get_context_path($context, [get_string('userpreferences')], "{$component}.json");
        $this->assertTrue($fileroot->hasChild($contextpath));

        $json = $fileroot->getChild($contextpath)->getContent();
        $expanded = json_decode($json);
        $this->assertTrue(isset($expanded->$key));
        $data = $expanded->$key;
        $this->assertEquals($value, $data->value);
        $this->assertEquals($desc, $data->description);
    }

    /**
     * User preferences can be exported against a course.
     *
     * @dataProvider    export_user_preference_provider
     * @param   string      $component  Component
     * @param   string      $key Key
     * @param   string      $value Value
     * @param   string      $desc Description
     */
    public function test_export_user_preference_context_course($component, $key, $value, $desc) {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();

        $context = \context_course::instance($course->id);
        $writer = $this->get_writer_instance()
            ->set_context($context)
            ->export_user_preference($component, $key, $value, $desc);

        $fileroot = $this->fetch_exported_content($writer);

        $contextpath = $this->get_context_path($context, [get_string('userpreferences')], "{$component}.json");
        $this->assertTrue($fileroot->hasChild($contextpath));

        $json = $fileroot->getChild($contextpath)->getContent();
        $expanded = json_decode($json);
        $this->assertTrue(isset($expanded->$key));
        $data = $expanded->$key;
        $this->assertEquals($value, $data->value);
        $this->assertEquals($desc, $data->description);
    }

    /**
     * User preferences can be exported against a module context.
     *
     * @dataProvider    export_user_preference_provider
     * @param   string      $component  Component
     * @param   string      $key Key
     * @param   string      $value Value
     * @param   string      $desc Description
     */
    public function test_export_user_preference_context_module($component, $key, $value, $desc) {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', ['course' => $course->id]);

        $context = \context_module::instance($forum->cmid);
        $writer = $this->get_writer_instance()
            ->set_context($context)
            ->export_user_preference($component, $key, $value, $desc);

        $fileroot = $this->fetch_exported_content($writer);

        $contextpath = $this->get_context_path($context, [get_string('userpreferences')], "{$component}.json");
        $this->assertTrue($fileroot->hasChild($contextpath));

        $json = $fileroot->getChild($contextpath)->getContent();
        $expanded = json_decode($json);
        $this->assertTrue(isset($expanded->$key));
        $data = $expanded->$key;
        $this->assertEquals($value, $data->value);
        $this->assertEquals($desc, $data->description);
    }

    /**
     * User preferences can not be exported against a block context.
     *
     * @dataProvider    export_user_preference_provider
     * @param   string      $component  Component
     * @param   string      $key Key
     * @param   string      $value Value
     * @param   string      $desc Description
     */
    public function test_export_user_preference_context_block($component, $key, $value, $desc) {
        global $DB;

        $blocks = $DB->get_records('block_instances');
        $block = reset($blocks);

        $context = \context_block::instance($block->id);
        $writer = $this->get_writer_instance()
            ->set_context($context)
            ->export_user_preference($component, $key, $value, $desc);

        $fileroot = $this->fetch_exported_content($writer);

        $contextpath = $this->get_context_path($context, [get_string('userpreferences')], "{$component}.json");
        $this->assertTrue($fileroot->hasChild($contextpath));

        $json = $fileroot->getChild($contextpath)->getContent();
        $expanded = json_decode($json);
        $this->assertTrue(isset($expanded->$key));
        $data = $expanded->$key;
        $this->assertEquals($value, $data->value);
        $this->assertEquals($desc, $data->description);
    }

    /**
     * Writing user preferences for two different blocks with the same name and
     * same parent context should generate two different context paths and export
     * files.
     */
    public function test_export_user_preference_context_block_multiple_instances() {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $coursecontext = context_course::instance($course->id);
        $block1 = $generator->create_block('online_users', ['parentcontextid' => $coursecontext->id]);
        $block2 = $generator->create_block('online_users', ['parentcontextid' => $coursecontext->id]);
        $block1context = context_block::instance($block1->id);
        $block2context = context_block::instance($block2->id);
        $component = 'block';
        $desc = 'test preference';
        $block1key = 'block1key';
        $block1value = 'block1value';
        $block2key = 'block2key';
        $block2value = 'block2value';
        $writer = $this->get_writer_instance();

        // Confirm that we have two different block contexts with the same name
        // and the same parent context id.
        $this->assertNotEquals($block1context->id, $block2context->id);
        $this->assertEquals($block1context->get_context_name(), $block2context->get_context_name());
        $this->assertEquals($block1context->get_parent_context()->id, $block2context->get_parent_context()->id);

        $retrieveexport = function($context) use ($writer, $component) {
            $fileroot = $this->fetch_exported_content($writer);

            $contextpath = $this->get_context_path($context, [get_string('userpreferences')], "{$component}.json");
            $this->assertTrue($fileroot->hasChild($contextpath));

            $json = $fileroot->getChild($contextpath)->getContent();
            return json_decode($json);
        };

        $writer->set_context($block1context)
            ->export_user_preference($component, $block1key, $block1value, $desc);
        $writer->set_context($block2context)
            ->export_user_preference($component, $block2key, $block2value, $desc);

        $block1export = $retrieveexport($block1context);
        $block2export = $retrieveexport($block2context);

        // Confirm that the exports didn't write to the same file.
        $this->assertTrue(isset($block1export->$block1key));
        $this->assertTrue(isset($block2export->$block2key));
        $this->assertFalse(isset($block1export->$block2key));
        $this->assertFalse(isset($block2export->$block1key));
        $this->assertEquals($block1value, $block1export->$block1key->value);
        $this->assertEquals($block2value, $block2export->$block2key->value);
    }

    /**
     * User preferences can be exported against the system.
     *
     * @dataProvider    export_user_preference_provider
     * @param   string      $component  Component
     * @param   string      $key Key
     * @param   string      $value Value
     * @param   string      $desc Description
     */
    public function test_export_user_preference_context_system($component, $key, $value, $desc) {
        $context = \context_system::instance();
        $writer = $this->get_writer_instance()
            ->set_context($context)
            ->export_user_preference($component, $key, $value, $desc);

        $fileroot = $this->fetch_exported_content($writer);

        $contextpath = $this->get_context_path($context, [get_string('userpreferences')], "{$component}.json");
        $this->assertTrue($fileroot->hasChild($contextpath));

        $json = $fileroot->getChild($contextpath)->getContent();
        $expanded = json_decode($json);
        $this->assertTrue(isset($expanded->$key));
        $data = $expanded->$key;
        $this->assertEquals($value, $data->value);
        $this->assertEquals($desc, $data->description);
    }

    /**
     * User preferences can be exported against the system.
     */
    public function test_export_multiple_user_preference_context_system() {
        $context = \context_system::instance();
        $writer = $this->get_writer_instance();
        $component = 'core_privacy';

        $writer
            ->set_context($context)
            ->export_user_preference($component, 'key1', 'val1', 'desc1')
            ->export_user_preference($component, 'key2', 'val2', 'desc2');

        $fileroot = $this->fetch_exported_content($writer);

        $contextpath = $this->get_context_path($context, [get_string('userpreferences')], "{$component}.json");
        $this->assertTrue($fileroot->hasChild($contextpath));

        $json = $fileroot->getChild($contextpath)->getContent();
        $expanded = json_decode($json);

        $this->assertTrue(isset($expanded->key1));
        $data = $expanded->key1;
        $this->assertEquals('val1', $data->value);
        $this->assertEquals('desc1', $data->description);

        $this->assertTrue(isset($expanded->key2));
        $data = $expanded->key2;
        $this->assertEquals('val2', $data->value);
        $this->assertEquals('desc2', $data->description);
    }

    /**
     * User preferences can be exported against the system.
     */
    public function test_export_user_preference_replace() {
        $context = \context_system::instance();
        $writer = $this->get_writer_instance();
        $component = 'core_privacy';
        $key = 'key';

        $writer
            ->set_context($context)
            ->export_user_preference($component, $key, 'val1', 'desc1');

        $writer
            ->set_context($context)
            ->export_user_preference($component, $key, 'val2', 'desc2');

        $fileroot = $this->fetch_exported_content($writer);

        $contextpath = $this->get_context_path($context, [get_string('userpreferences')], "{$component}.json");
        $this->assertTrue($fileroot->hasChild($contextpath));

        $json = $fileroot->getChild($contextpath)->getContent();
        $expanded = json_decode($json);

        $this->assertTrue(isset($expanded->$key));
        $data = $expanded->$key;
        $this->assertEquals('val2', $data->value);
        $this->assertEquals('desc2', $data->description);
    }

    /**
     * Provider for various user preferences.
     *
     * @return  array
     */
    public function export_user_preference_provider() {
        return [
            'basic' => [
                'core_privacy',
                'onekey',
                'value',
                'description',
            ],
            'encodedvalue' => [
                'core_privacy',
                'donkey',
                base64_encode('value'),
                'description',
            ],
            'long description' => [
                'core_privacy',
                'twokey',
                'value',
                'This is a much longer description which actually states what this is used for. Blah blah blah.',
            ],
        ];
    }

    /**
     * Test that exported data is human readable.
     *
     * @dataProvider unescaped_unicode_export_provider
     * @param string $text
     */
    public function test_export_data_unescaped_unicode($text) {
        $context = \context_system::instance();
        $subcontext = [];
        $data = (object) ['key' => $text];

        $writer = $this->get_writer_instance()
                ->set_context($context)
                ->export_data($subcontext, $data);

        $fileroot = $this->fetch_exported_content($writer);

        $contextpath = $this->get_context_path($context, $subcontext, 'data.json');

        $json = $fileroot->getChild($contextpath)->getContent();
        $this->assertRegExp("/$text/", $json);

        $expanded = json_decode($json);
        $this->assertEquals($data, $expanded);
    }

    /**
     * Test that exported metadata is human readable.
     *
     * @dataProvider unescaped_unicode_export_provider
     * @param string $text
     */
    public function test_export_metadata_unescaped_unicode($text) {
        $context = \context_system::instance();
        $subcontext = ['a', 'b', 'c'];

        $writer = $this->get_writer_instance()
                ->set_context($context)
                ->export_metadata($subcontext, $text, $text, $text);

        $fileroot = $this->fetch_exported_content($writer);

        $contextpath = $this->get_context_path($context, $subcontext, 'metadata.json');

        $json = $fileroot->getChild($contextpath)->getContent();
        $this->assertRegExp("/$text.*$text.*$text/is", $json);

        $expanded = json_decode($json);
        $this->assertTrue(isset($expanded->$text));
        $this->assertEquals($text, $expanded->$text->value);
        $this->assertEquals($text, $expanded->$text->description);
    }

    /**
     * Test that exported related data is human readable.
     *
     * @dataProvider unescaped_unicode_export_provider
     * @param string $text
     */
    public function test_export_related_data_unescaped_unicode($text) {
        $context = \context_system::instance();
        $subcontext = [];
        $data = (object) ['key' => $text];

        $writer = $this->get_writer_instance()
                ->set_context($context)
                ->export_related_data($subcontext, 'name', $data);

        $fileroot = $this->fetch_exported_content($writer);

        $contextpath = $this->get_context_path($context, $subcontext, 'name.json');

        $json = $fileroot->getChild($contextpath)->getContent();
        $this->assertRegExp("/$text/", $json);

        $expanded = json_decode($json);
        $this->assertEquals($data, $expanded);
    }

    /**
     * Test that exported user preference is human readable.
     *
     * @dataProvider unescaped_unicode_export_provider
     * @param string $text
     */
    public function test_export_user_preference_unescaped_unicode($text) {
        $context = \context_system::instance();
        $component = 'core_privacy';

        $writer = $this->get_writer_instance()
                ->set_context($context)
                ->export_user_preference($component, $text, $text, $text);

        $fileroot = $this->fetch_exported_content($writer);

        $contextpath = $this->get_context_path($context, [get_string('userpreferences')], "{$component}.json");

        $json = $fileroot->getChild($contextpath)->getContent();
        $this->assertRegExp("/$text.*$text.*$text/is", $json);

        $expanded = json_decode($json);
        $this->assertTrue(isset($expanded->$text));
        $this->assertEquals($text, $expanded->$text->value);
        $this->assertEquals($text, $expanded->$text->description);
    }

    /**
     * Provider for various user preferences.
     *
     * @return array
     */
    public function unescaped_unicode_export_provider() {
        return [
            'Unicode' => ['ةكءيٓ‌پچژکگیٹڈڑہھےâîûğŞAaÇÖáǽ你好!'],
        ];
    }

    /**
     * Test that exported data is shortened when exceeds the limit.
     *
     * @dataProvider long_filename_provider
     * @param string $longtext
     * @param string $expected
     * @param string $text
     */
    public function test_export_data_long_filename($longtext, $expected, $text) {
        $context = \context_system::instance();
        $subcontext = [$longtext];
        $data = (object) ['key' => $text];

        $writer = $this->get_writer_instance()
                ->set_context($context)
                ->export_data($subcontext, $data);

        $fileroot = $this->fetch_exported_content($writer);

        $contextpath = $this->get_context_path($context, $subcontext, 'data.json');
        $expectedpath = "System {$context->id}/{$expected}/data.json";
        $this->assertEquals($expectedpath, $contextpath);

        $json = $fileroot->getChild($contextpath)->getContent();
        $this->assertRegExp("/$text/", $json);

        $expanded = json_decode($json);
        $this->assertEquals($data, $expanded);
    }

    /**
     * Test that exported related data is shortened when exceeds the limit.
     *
     * @dataProvider long_filename_provider
     * @param string $longtext
     * @param string $expected
     * @param string $text
     */
    public function test_export_related_data_long_filename($longtext, $expected, $text) {
        $context = \context_system::instance();
        $subcontext = [$longtext];
        $data = (object) ['key' => $text];

        $writer = $this->get_writer_instance()
                ->set_context($context)
                ->export_related_data($subcontext, 'name', $data);

        $fileroot = $this->fetch_exported_content($writer);

        $contextpath = $this->get_context_path($context, $subcontext, 'name.json');
        $expectedpath = "System {$context->id}/{$expected}/name.json";
        $this->assertEquals($expectedpath, $contextpath);

        $json = $fileroot->getChild($contextpath)->getContent();
        $this->assertRegExp("/$text/", $json);

        $expanded = json_decode($json);
        $this->assertEquals($data, $expanded);
    }

    /**
     * Test that exported metadata is shortened when exceeds the limit.
     *
     * @dataProvider long_filename_provider
     * @param string $longtext
     * @param string $expected
     * @param string $text
     */
    public function test_export_metadata_long_filename($longtext, $expected, $text) {
        $context = \context_system::instance();
        $subcontext = [$longtext];
        $data = (object) ['key' => $text];

        $writer = $this->get_writer_instance()
                ->set_context($context)
                ->export_metadata($subcontext, $text, $text, $text);

        $fileroot = $this->fetch_exported_content($writer);

        $contextpath = $this->get_context_path($context, $subcontext, 'metadata.json');
        $expectedpath = "System {$context->id}/{$expected}/metadata.json";
        $this->assertEquals($expectedpath, $contextpath);

        $json = $fileroot->getChild($contextpath)->getContent();
        $this->assertRegExp("/$text.*$text.*$text/is", $json);

        $expanded = json_decode($json);
        $this->assertTrue(isset($expanded->$text));
        $this->assertEquals($text, $expanded->$text->value);
        $this->assertEquals($text, $expanded->$text->description);
    }

    /**
     * Test that exported user preference is shortened when exceeds the limit.
     *
     * @dataProvider long_filename_provider
     * @param string $longtext
     * @param string $expected
     * @param string $text
     */
    public function test_export_user_preference_long_filename($longtext, $expected, $text) {
        $this->resetAfterTest();

        if (!array_key_exists('json', core_filetypes::get_types())) {
            // Add json as mime type to avoid lose the extension when shortening filenames.
            core_filetypes::add_type('json', 'application/json', 'archive', [], '', 'JSON file archive');
        }
        $context = \context_system::instance();
        $expectedpath = "System {$context->id}/User preferences/{$expected}.json";

        $component = $longtext;

        $writer = $this->get_writer_instance()
                ->set_context($context)
                ->export_user_preference($component, $text, $text, $text);

        $fileroot = $this->fetch_exported_content($writer);

        $contextpath = $this->get_context_path($context, [get_string('userpreferences')], "{$component}.json");
        $this->assertEquals($expectedpath, $contextpath);

        $json = $fileroot->getChild($contextpath)->getContent();
        $this->assertRegExp("/$text.*$text.*$text/is", $json);

        $expanded = json_decode($json);
        $this->assertTrue(isset($expanded->$text));
        $this->assertEquals($text, $expanded->$text->value);
        $this->assertEquals($text, $expanded->$text->description);
    }

    /**
     * Provider for long filenames.
     *
     * @return array
     */
    public function long_filename_provider() {
        return [
            'More than 100 characters' => [
                'Etiam sit amet dui vel leo blandit viverra. Proin viverra suscipit velit. Aenean efficitur suscipit nibh nec suscipit',
                'Etiam sit amet dui vel leo blandit viverra. Proin viverra suscipit velit. Aenean effici - 22f7a5030d',
                'value',
            ],
        ];
    }

    /**
     * Get a fresh content writer.
     *
     * @return  moodle_content_writer
     */
    public function get_writer_instance() {
        $factory = $this->createMock(writer::class);
        return new moodle_content_writer($factory);
    }

    /**
     * Fetch the exported content for inspection.
     *
     * @param   moodle_content_writer   $writer
     * @return  \org\bovigo\vfs\vfsStreamDirectory
     */
    protected function fetch_exported_content(moodle_content_writer $writer) {
        $export = $writer
            ->set_context(\context_system::instance())
            ->finalise_content();

        $fileroot = \org\bovigo\vfs\vfsStream::setup('root');

        $target = \org\bovigo\vfs\vfsStream::url('root');
        $fp = get_file_packer();
        $fp->extract_to_pathname($export, $target);

        return $fileroot;
    }

    /**
     * Determine the path for the current context.
     *
     * Note: This is a wrapper around the real function.
     *
     * @param   \context        $context    The context being written
     * @param   array           $subcontext The subcontext path
     * @param   string          $name       THe name of the file target
     * @return  array                       The context path.
     */
    protected function get_context_path($context, $subcontext = null, $name = '') {
        $rc = new ReflectionClass(moodle_content_writer::class);
        $writer = $this->get_writer_instance();
        $writer->set_context($context);

        if (null === $subcontext) {
            $rcm = $rc->getMethod('get_context_path');
            $rcm->setAccessible(true);
            $path = $rcm->invoke($writer);
        } else {
            $rcm = $rc->getMethod('get_path');
            $rcm->setAccessible(true);
            $path = $rcm->invoke($writer, $subcontext, $name);
        }

        // PHPUnit uses mikey179/vfsStream which is a stream wrapper for a virtual file system that uses '/'
        // as the directory separator.
        $path = str_replace(DIRECTORY_SEPARATOR, '/', $path);

        return $path;
    }

    /**
     * Test correct rewriting of @@PLUGINFILE@@ in the exported contents.
     *
     * @dataProvider rewrite_pluginfile_urls_provider
     * @param string $filearea The filearea within that component.
     * @param int $itemid Which item those files belong to.
     * @param string $input Raw text as stored in the database.
     * @param string $expectedoutput Expected output of URL rewriting.
     */
    public function test_rewrite_pluginfile_urls($filearea, $itemid, $input, $expectedoutput) {

        $writer = $this->get_writer_instance();
        $writer->set_context(\context_system::instance());

        $realoutput = $writer->rewrite_pluginfile_urls([], 'core_test', $filearea, $itemid, $input);

        $this->assertEquals($expectedoutput, $realoutput);
    }

    /**
     * Provides testable sample data for {@link self::test_rewrite_pluginfile_urls()}.
     *
     * @return array
     */
    public function rewrite_pluginfile_urls_provider() {
        return [
            'zeroitemid' => [
                'intro',
                0,
                '<p><img src="@@PLUGINFILE@@/hello.gif" /></p>',
                '<p><img src="_files/intro/hello.gif" /></p>',
            ],
            'nonzeroitemid' => [
                'submission_content',
                34,
                '<p><img src="@@PLUGINFILE@@/first.png" alt="First" /></p>',
                '<p><img src="_files/submission_content/34/first.png" alt="First" /></p>',
            ],
            'withfilepath' => [
                'post_content',
                9889,
                '<a href="@@PLUGINFILE@@/embedded/docs/muhehe.exe">Click here!</a>',
                '<a href="_files/post_content/9889/embedded/docs/muhehe.exe">Click here!</a>',
            ],
        ];
    }
}

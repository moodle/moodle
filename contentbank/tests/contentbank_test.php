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
 * Test for extensions manager.
 *
 * @package    core_contentbank
 * @category   test
 * @copyright  2020 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_contentbank;

defined('MOODLE_INTERNAL') || die();

use advanced_testcase;
use context_block;
use context_course;
use context_coursecat;
use context_module;
use context_system;
use context_user;
use Exception;

global $CFG;
require_once($CFG->dirroot . '/contentbank/tests/fixtures/testable_contenttype.php');
require_once($CFG->dirroot . '/contentbank/tests/fixtures/testable_content.php');

/**
 * Test for extensions manager.
 *
 * @package    core_contentbank
 * @category   test
 * @copyright  2020 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core_contentbank\contentbank
 */
class contentbank_test extends advanced_testcase {

    /**
     * Setup to ensure that fixtures are loaded.
     */
    public static function setupBeforeClass(): void {
        global $CFG;

        require_once($CFG->dirroot . '/contentbank/tests/fixtures/testable_contenttype.php');
    }

    /**
     * Data provider for test_get_extension_supporter.
     *
     * @return  array
     */
    public static function get_extension_provider(): array {
        return [
            'H5P file' => ['something.h5p', '.h5p'],
            'PDF file' => ['something.pdf', '.pdf']
        ];
    }

    /**
     * Tests for get_extension() function.
     *
     * @dataProvider    get_extension_provider
     * @param   string  $filename    The filename given
     * @param   string   $expected   The extension of the file
     *
     * @covers ::get_extension
     */
    public function test_get_extension(string $filename, string $expected): void {
        $this->resetAfterTest();

        $cb = new contentbank();

        $extension = $cb->get_extension($filename);
        $this->assertEquals($expected, $extension);
    }

    /**
     * Data provider for test_load_context_supported_extensions.
     *
     * @return  array
     */
    public static function get_extension_supporters_provider(): array {
        return [
            'H5P first' => [['.h5p' => ['h5p', 'testable']], '.h5p', 'h5p'],
            'Testable first (but upload not implemented)' => [['.h5p' => ['testable', 'h5p']], '.h5p', 'h5p'],
        ];
    }

    /**
     * Tests for get_extension_supporter() function with admin permissions.
     *
     * @dataProvider    get_extension_supporters_provider
     * @param   array   $supporters   The content type plugin supporters for each extension
     * @param   string  $extension    The extension of the file given
     * @param   string  $expected   The supporter contenttype of the file
     *
     * @covers ::load_context_supported_extensions
     */
    public function test_get_extension_supporter_for_admins(array $supporters, string $extension, string $expected): void {
        $this->resetAfterTest();

        $cb = new contentbank();

        $systemcontext = context_system::instance();

        // All contexts allowed for admins.
        $this->setAdminUser();
        $contextsupporters = $cb->load_context_supported_extensions($systemcontext);
        $this->assertArrayHasKey($extension, $contextsupporters);
        $this->assertEquals($expected, $contextsupporters[$extension]);
    }

    /**
     * Tests for get_extension_supporter() function with user default permissions.
     *
     * @dataProvider    get_extension_supporters_provider
     * @param   array   $supporters   The content type plugin supporters for each extension
     * @param   string  $extension    The extension of the file given
     * @param   string  $expected   The supporter contenttype of the file
     *
     * @covers ::load_context_supported_extensions
     */
    public function test_get_extension_supporter_for_users(array $supporters, string $extension, string $expected): void {
        $this->resetAfterTest();

        $cb = new contentbank();
        $systemcontext = context_system::instance();

        // Set a user with no permissions.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        // Users with no capabilities can't upload content.
        $contextsupporters = $cb->load_context_supported_extensions($systemcontext);
        $this->assertEquals([], $contextsupporters);
    }

    /**
     * Tests for get_extension_supporter() function with teacher defaul permissions.
     *
     * @dataProvider    get_extension_supporters_provider
     * @param   array   $supporters   The content type plugin supporters for each extension
     * @param   string  $extension    The extension of the file given
     * @param   string  $expected   The supporter contenttype of the file
     *
     * @covers ::load_context_supported_extensions
     */
    public function test_get_extension_supporter_for_teachers(array $supporters, string $extension, string $expected): void {
        $this->resetAfterTest();

        $cb = new contentbank();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $this->setUser($teacher);
        $coursecontext = context_course::instance($course->id);

        // Teachers has permission in their context to upload supported by H5P content type.
        $contextsupporters = $cb->load_context_supported_extensions($coursecontext);
        $this->assertArrayHasKey($extension, $contextsupporters);
        $this->assertEquals($expected, $contextsupporters[$extension]);
    }

    /**
     * Tests for get_extension_supporter() function.
     *
     * @dataProvider    get_extension_supporters_provider
     * @param   array   $supporters   The content type plugin supporters for each extension
     * @param   string  $extension    The extension of the file given
     * @param   string  $expected   The supporter contenttype of the file
     *
     * @covers ::get_extension_supporter
     */
    public function test_get_extension_supporter(array $supporters, string $extension, string $expected): void {
        $this->resetAfterTest();

        $cb = new contentbank();
        $systemcontext = context_system::instance();
        $this->setAdminUser();

        $supporter = $cb->get_extension_supporter($extension, $systemcontext);
        $this->assertEquals($expected, $supporter);
    }

    /**
     * Test the behaviour of search_contents().
     *
     * @dataProvider search_contents_provider
     * @param  string $search String to search.
     * @param  string $where Context where to search.
     * @param  int $expectedresult Expected result.
     * @param  array $contexts List of contexts where to create content.
     */
    public function test_search_contents(?string $search, string $where, int $expectedresult, array $contexts = [],
            ?array $contenttypes = null): void {
        global $DB, $CFG;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Create users.
        $managerroleid = $DB->get_field('role', 'id', ['shortname' => 'manager']);
        $manager = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->role_assign($managerroleid, $manager->id);

        // Create a category and a course.
        $coursecat = $this->getDataGenerator()->create_category();
        $course = $this->getDataGenerator()->create_course();
        $existingcontexts = [];
        $existingcontexts['system'] = \context_system::instance();
        $existingcontexts['category'] = \context_coursecat::instance($coursecat->id);
        $existingcontexts['course'] = \context_course::instance($course->id);

        if (empty($where)) {
            $contextid = 0;
        } else {
            $contextid = $existingcontexts[$where]->id;
        }

        // Add some content to the content bank.
        $filepath = $CFG->dirroot . '/h5p/tests/fixtures/filltheblanks.h5p';
        $generator = $this->getDataGenerator()->get_plugin_generator('core_contentbank');
        foreach ($contexts as $context) {
            $contextinstance = $existingcontexts[$context];
            $records = $generator->generate_contentbank_data('contenttype_h5p', 3,
                $manager->id, $contextinstance, false, $filepath);
        }

        // Search for some content.
        $cb = new contentbank();
        $contents = $cb->search_contents($search, $contextid, $contenttypes);

        $this->assertCount($expectedresult, $contents);
        if (!empty($contents) && !empty($search)) {
            foreach ($contents as $content) {
                $this->assertStringContainsString($search, $content->get_name());
            }
        }
    }

    /**
     * Data provider for test_search_contents().
     *
     * @return array
     */
    public static function search_contents_provider(): array {

        return [
            'Search all content in all contexts' => [
                null,
                '',
                9,
                ['system', 'category', 'course']
            ],
            'Search in all contexts for existing string in all contents' => [
                'content',
                '',
                9,
                ['system', 'category', 'course']
            ],
            'Search in all contexts for unexisting string in all contents' => [
                'chocolate',
                '',
                0,
                ['system', 'category', 'course']
            ],
            'Search in all contexts for existing string in some contents' => [
                '1',
                '',
                3,
                ['system', 'category', 'course']
            ],
            'Search in all contexts for existing string in some contents (create only 1 context)' => [
                '1',
                '',
                1,
                ['system']
            ],
            'Search in system context for existing string in all contents' => [
                'content',
                'system',
                3,
                ['system', 'category', 'course']
            ],
            'Search in category context for unexisting string in all contents' => [
                'chocolate',
                'category',
                0,
                ['system', 'category', 'course']
            ],
            'Search in course context for existing string in some contents' => [
                '1',
                'course',
                1,
                ['system', 'category', 'course']
            ],
            'Search in system context' => [
                null,
                'system',
                3,
                ['system', 'category', 'course']
            ],
            'Search in course context with existing content' => [
                null,
                'course',
                3,
                ['system', 'category', 'course']
            ],
            'Search in course context without existing content' => [
                null,
                'course',
                0,
                ['system', 'category']
            ],
            'Search in an empty contentbank' => [
                null,
                '',
                0,
                []
            ],
            'Search in a context in an empty contentbank' => [
                null,
                'system',
                0,
                []
            ],
            'Search for a string in an empty contentbank' => [
                'content',
                '',
                0,
                []
            ],
            'Search with unexisting content-type' => [
                null,
                'course',
                0,
                ['system', 'category', 'course'],
                ['contenttype_unexisting'],
            ],
        ];
    }

    /**
     * Test create_content_from_file function.
     *
     * @covers ::create_content_from_file
     */
    public function test_create_content_from_file(): void {
        global $USER, $CFG;

        $this->resetAfterTest();
        $this->setAdminUser();
        $systemcontext = \context_system::instance();
        $name = 'greeting-card.h5p';

        // Create a dummy H5P file.
        $dummyh5p = array(
            'contextid' => $systemcontext->id,
            'component' => 'contentbank',
            'filearea' => 'public',
            'itemid' => 1,
            'filepath' => '/',
            'filename' => $name,
            'userid' => $USER->id
        );
        $path = $CFG->dirroot . '/h5p/tests/fixtures/' . $name;
        $dummyh5pfile = \core_h5p\helper::create_fake_stored_file_from_path($path);

        $cb = new contentbank();
        $content = $cb->create_content_from_file($systemcontext, $USER->id, $dummyh5pfile);

        $this->assertEquals('contenttype_h5p', $content->get_content_type());
        $this->assertInstanceOf('\\contenttype_h5p\\content', $content);
        $this->assertEquals($name, $content->get_name());
    }

    /**
     * Test the behaviour of delete_contents().
     *
     * @covers  ::delete_contents
     */
    public function test_delete_contents(): void {
        global $DB;

        $this->resetAfterTest();
        $cb = new \core_contentbank\contentbank();

        // Create a category and two courses.
        $systemcontext = context_system::instance();
        $coursecat = $this->getDataGenerator()->create_category();
        $coursecatcontext = context_coursecat::instance($coursecat->id);
        $course1 = $this->getDataGenerator()->create_course();
        $course1context = context_course::instance($course1->id);
        $course2 = $this->getDataGenerator()->create_course();
        $course2context = context_course::instance($course2->id);

        // Add some content to the content bank.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_contentbank');
        $systemcontent = $generator->generate_contentbank_data(null, 3, 0, $systemcontext);
        $categorycontent = $generator->generate_contentbank_data(null, 3, 0, $coursecatcontext);
        $course1content = $generator->generate_contentbank_data(null, 3, 0, $course1context);
        $course2content = $generator->generate_contentbank_data(null, 3, 0, $course2context);

        // Check the content has been created as expected.
        $this->assertEquals(12, $DB->count_records('contentbank_content'));

        // Check the system content is deleted as expected and the rest of the content is not.
        $this->assertTrue($cb->delete_contents($systemcontext));
        $this->assertEquals(0, $DB->count_records('contentbank_content', ['contextid' => $systemcontext->id]));
        // And the rest of the context content exists.
        $this->assertEquals(9, $DB->count_records('contentbank_content'));

        // Check the course category content is deleted as expected and the rest of the content is not.
        $this->assertTrue($cb->delete_contents($coursecatcontext));
        $this->assertEquals(0, $DB->count_records('contentbank_content', ['contextid' => $coursecatcontext->id]));
        // And the rest of the context content exists.
        $this->assertEquals(6, $DB->count_records('contentbank_content'));

        // Check the course content is deleted as expected and the rest of the content is not.
        $this->assertTrue($cb->delete_contents($course1context));
        $this->assertEquals(0, $DB->count_records('contentbank_content', ['contextid' => $course1context->id]));
        // And the rest of the context content exists.
        $this->assertEquals(3, $DB->count_records('contentbank_content'));
    }

    /**
     * Test the behaviour of delete_contents() for empty content bank.
     *
     * @covers  ::delete_contents
     */
    public function test_delete_contents_for_empty_contentbank(): void {

        $this->resetAfterTest();
        $cb = new \core_contentbank\contentbank();

        // Create a category and two courses.
        $systemcontext = \context_system::instance();
        $coursecat = $this->getDataGenerator()->create_category();
        $coursecatcontext = \context_coursecat::instance($coursecat->id);
        $course = $this->getDataGenerator()->create_course();
        $coursecontext = \context_course::instance($course->id);

        // Check there's no error when trying to delete content from an empty content bank.
        $this->assertTrue($cb->delete_contents($systemcontext));
        $this->assertTrue($cb->delete_contents($coursecatcontext));
        $this->assertTrue($cb->delete_contents($coursecontext));
    }

    /**
     * Test the behaviour of move_contents().
     *
     * @covers  ::move_contents
     */
    public function test_move_contents(): void {
        global $DB;

        $this->resetAfterTest();
        $cb = new \core_contentbank\contentbank();

        // Create a category and two courses.
        $course1 = $this->getDataGenerator()->create_course();
        $course1context = context_course::instance($course1->id);
        $course2 = $this->getDataGenerator()->create_course();
        $course2context = context_course::instance($course2->id);

        // Add some content to the content bank.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_contentbank');
        $course1content = $generator->generate_contentbank_data(null, 3, 0, $course1context);
        $course2content = $generator->generate_contentbank_data(null, 3, 0, $course2context);

        // Check the content has been created as expected.
        $this->assertEquals(6, $DB->count_records('contentbank_content'));
        $this->assertEquals(3, $DB->count_records('contentbank_content', ['contextid' => $course1context->id]));

        // Check the content is moved to another context as expected and the rest of the content is not.
        $this->assertTrue($cb->move_contents($course1context, $course2context));
        $this->assertEquals(6, $DB->count_records('contentbank_content'));
        $this->assertEquals(0, $DB->count_records('contentbank_content', ['contextid' => $course1context->id]));
        $this->assertEquals(6, $DB->count_records('contentbank_content', ['contextid' => $course2context->id]));
    }

    /**
     * Test the behaviour of move_contents() for empty content bank.
     *
     * @covers  ::move_contents
     */
    public function test_move_contents_for_empty_contentbank(): void {

        $this->resetAfterTest();
        $cb = new \core_contentbank\contentbank();

        // Create a category and two courses.
        $systemcontext = \context_system::instance();
        $course = $this->getDataGenerator()->create_course();
        $coursecontext = \context_course::instance($course->id);

        // Check there's no error when trying to move content context from an empty content bank.
        $this->assertTrue($cb->delete_contents($systemcontext, $coursecontext));
    }

    /**
     * Data provider for get_contenttypes_with_capability_feature.
     *
     * @return  array
     */
    public static function get_contenttypes_with_capability_feature_provider(): array {
        return [
            'no-contenttypes_enabled' => [
                'contenttypesenabled' => [],
                'contenttypescanfeature' => [],
            ],
            'contenttype_enabled_noeditable' => [
                'contenttypesenabled' => ['testable'],
                'contenttypescanfeature' => [],
            ],
            'contenttype_enabled_editable' => [
                'contenttypesenabled' => ['testable'],
                'contenttypescanfeature' => ['testable'],
            ],
            'no-contenttype_enabled_editable' => [
                'contenttypesenabled' => [],
                'contenttypescanfeature' => ['testable'],
            ],
        ];
    }

    /**
     * Tests for get_contenttypes_with_capability_feature() function.
     *
     * @dataProvider    get_contenttypes_with_capability_feature_provider
     * @param   array $contenttypesenabled Content types enabled.
     * @param   array $contenttypescanfeature Content types the user has the permission to use the feature.
     *
     * @covers ::get_contenttypes_with_capability_feature
     */
    public function test_get_contenttypes_with_capability_feature(array $contenttypesenabled, array $contenttypescanfeature): void {
        $this->resetAfterTest();

        $cb = new contentbank();

        $plugins = [];

        // Content types not enabled where the user has permission to use a feature.
        if (empty($contenttypesenabled) && !empty($contenttypescanfeature)) {
            $enabled = false;

            // Mock core_plugin_manager class and the method get_plugins_of_type.
            $pluginmanager = $this->getMockBuilder(\core_plugin_manager::class)
                ->disableOriginalConstructor()
                ->onlyMethods(['get_plugins_of_type'])
                ->getMock();

            // Replace protected singletoninstance reference (core_plugin_manager property) with mock object.
            $ref = new \ReflectionProperty(\core_plugin_manager::class, 'singletoninstance');
            $ref->setValue(null, $pluginmanager);

            // Return values of get_plugins_of_type method.
            foreach ($contenttypescanfeature as $contenttypepluginname) {
                $contenttypeplugin = new \stdClass();
                $contenttypeplugin->name = $contenttypepluginname;
                $contenttypeplugin->type = 'contenttype';
                // Add the feature to the fake content type.
                $classname = "\\contenttype_$contenttypepluginname\\contenttype";
                $classname::$featurestotest = ['test2'];
                $plugins[] = $contenttypeplugin;
            }

            // Set expectations and return values.
            $pluginmanager->expects($this->once())
                ->method('get_plugins_of_type')
                ->with('contenttype')
                ->willReturn($plugins);
        } else {
            $enabled = true;
            // Get access to private property enabledcontenttypes.
            $rc = new \ReflectionClass(\core_contentbank\contentbank::class);
            $rcp = $rc->getProperty('enabledcontenttypes');

            foreach ($contenttypesenabled as $contenttypename) {
                $plugins["\\contenttype_$contenttypename\\contenttype"] = $contenttypename;
                // Add to the testable contenttype the feature to test.
                if (in_array($contenttypename, $contenttypescanfeature)) {
                    $classname = "\\contenttype_$contenttypename\\contenttype";
                    $classname::$featurestotest = ['test2'];
                }
            }
            // Set as enabled content types only those in the test.
            $rcp->setValue($cb, $plugins);
        }

        $actual = $cb->get_contenttypes_with_capability_feature('test2', null, $enabled);
        $this->assertEquals($contenttypescanfeature, array_values($actual));
    }

    /**
     * Test the behaviour of get_content_from_id()
     *
     * @covers  ::get_content_from_id
     */
    public function test_get_content_from_id(): void {

        $this->resetAfterTest();
        $cb = new \core_contentbank\contentbank();

        // Create a category and two courses.
        $systemcontext = context_system::instance();

        // Add some content to the content bank.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_contentbank');
        $contents = $generator->generate_contentbank_data(null, 3, 0, $systemcontext);
        $content = reset($contents);

        // Get the content instance form id.
        $newinstance = $cb->get_content_from_id($content->get_id());
        $this->assertEquals($content->get_id(), $newinstance->get_id());

        // Now produce and exception with an innexistent id.
        $this->expectException(Exception::class);
        $cb->get_content_from_id(0);
    }

    /**
     * Test the behaviour of is_context_allowed().
     *
     * @covers ::is_context_allowed
     */
    public function test_is_context_allowed(): void {
        $this->resetAfterTest();

        $cb = new contentbank();

        // System context.
        $this->assertTrue($cb->is_context_allowed(context_system::instance()));

        // User context.
        $user = $this->getDataGenerator()->create_user();
        $this->assertFalse($cb->is_context_allowed(context_user::instance($user->id)));

        // Category context.
        $category = $this->getDataGenerator()->create_category();
        $this->assertTrue($cb->is_context_allowed(context_coursecat::instance($category->id)));

        // Course context.
        $course = $this->getDataGenerator()->create_course(['category' => $category->id]);
        $coursecontext = context_course::instance($course->id);
        $this->assertTrue($cb->is_context_allowed($coursecontext));

        // Module context.
        $module = $this->getDataGenerator()->create_module('page', ['course' => $course->id]);
        $this->assertFalse($cb->is_context_allowed(context_module::instance($module->cmid)));

        // Block context.
        $block = $this->getDataGenerator()->create_block('online_users', ['parentcontextid' => $coursecontext->id]);
        $this->assertFalse($cb->is_context_allowed(context_block::instance($block->id)));
    }
}

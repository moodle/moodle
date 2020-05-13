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

use advanced_testcase;
use context_course;
use context_system;

/**
 * Test for extensions manager.
 *
 * @package    core_contentbank
 * @category   test
 * @copyright  2020 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core_contentbank\contentbank
 */
class core_contentbank_testcase extends advanced_testcase {

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
    public function get_extension_provider() {
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
    public function test_get_extension(string $filename, string $expected) {
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
    public function get_extension_supporters_provider() {
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
    public function test_get_extension_supporter_for_admins(array $supporters, string $extension, string $expected) {
        $this->resetAfterTest();

        $cb = new contentbank();
        $expectedsupporters = [$extension => $expected];

        $systemcontext = context_system::instance();

        // All contexts allowed for admins.
        $this->setAdminUser();
        $contextsupporters = $cb->load_context_supported_extensions($systemcontext);
        $this->assertEquals($expectedsupporters, $contextsupporters);
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
    public function test_get_extension_supporter_for_users(array $supporters, string $extension, string $expected) {
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
    public function test_get_extension_supporter_for_teachers(array $supporters, string $extension, string $expected) {
        $this->resetAfterTest();

        $cb = new contentbank();
        $expectedsupporters = [$extension => $expected];

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $this->setUser($teacher);
        $coursecontext = context_course::instance($course->id);

        // Teachers has permission in their context to upload supported by H5P content type.
        $contextsupporters = $cb->load_context_supported_extensions($coursecontext);
        $this->assertEquals($expectedsupporters, $contextsupporters);
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
    public function test_get_extension_supporter(array $supporters, string $extension, string $expected) {
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
            array $contenttypes = null): void {
        global $DB;

        $this->resetAfterTest();

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
        $generator = $this->getDataGenerator()->get_plugin_generator('core_contentbank');
        foreach ($contexts as $context) {
            $contextinstance = $existingcontexts[$context];
            $records = $generator->generate_contentbank_data('contenttype_h5p', 3,
                $manager->id, $contextinstance, false);
        }

        // Search for some content.
        $cb = new contentbank();
        $contents = $cb->search_contents($search, $contextid, $contenttypes);

        $this->assertCount($expectedresult, $contents);
        if (!empty($contents) && !empty($search)) {
            foreach ($contents as $content) {
                $this->assertContains($search, $content->get_name());
            }
        }
    }

    /**
     * Data provider for test_search_contents().
     *
     * @return array
     */
    public function search_contents_provider(): array {

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
    public function test_create_content_from_file() {
        global $USER;

        $this->resetAfterTest();
        $this->setAdminUser();
        $systemcontext = \context_system::instance();
        $name = 'dummy_h5p.h5p';

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
        $fs = get_file_storage();
        $dummyh5pfile = $fs->create_file_from_string($dummyh5p, 'Dummy H5Pcontent');

        $cb = new contentbank();
        $content = $cb->create_content_from_file($systemcontext, $USER->id, $dummyh5pfile);

        $this->assertEquals('contenttype_h5p', $content->get_content_type());
        $this->assertInstanceOf('\\contenttype_h5p\\content', $content);
        $this->assertEquals($name, $content->get_name());
    }
}

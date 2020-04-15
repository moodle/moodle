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

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/contentbank/tests/fixtures/testable_contenttype.php');

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

        $cb = new \core_contentbank\contentbank();

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

        $cb = new \core_contentbank\contentbank();
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

        $cb = new \core_contentbank\contentbank();
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

        $cb = new \core_contentbank\contentbank();
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

        $cb = new \core_contentbank\contentbank();
        $systemcontext = context_system::instance();
        $this->setAdminUser();

        $supporter = $cb->get_extension_supporter($extension, $systemcontext);
        $this->assertEquals($expected, $supporter);
    }
}

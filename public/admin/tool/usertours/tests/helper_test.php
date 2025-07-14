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

namespace tool_usertours;

/**
 * Tests for helper.
 *
 * @package    tool_usertours
 * @category   test
 * @copyright  2022 Huong Nguyen <huongnv13@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \tool_usertours\helper
 * @covers \tool_usertours\hook\before_serverside_filter_fetch
 * @covers \tool_usertours\hook\before_clientside_filter_fetch
 */
final class helper_test extends \advanced_testcase {
    /**
     * Data Provider for get_string_from_input.
     *
     * @return array
     */
    public static function get_string_from_input_provider(): array {
        return [
            'Text'  => [
                'example',
                'example',
            ],
            'Text which looks like a langstring' => [
                'example,fakecomponent',
                'example,fakecomponent',
            ],
            'Text which is a langstring' => [
                'administration,core',
                'Administration',
            ],
            'Text which is a langstring but uses "moodle" instead of "core"' => [
                'administration,moodle',
                'Administration',
            ],
            'Text which is a langstring, but with extra whitespace' => [
                '  administration,moodle  ',
                'Administration',
            ],
            'Looks like a langstring, but has incorrect space around comma' => [
                'administration , moodle',
                'administration , moodle',
            ],
        ];
    }

    /**
     * Ensure that the get_string_from_input function returns langstring strings correctly.
     *
     * @dataProvider get_string_from_input_provider
     * @param string $string The string to test
     * @param string $expected The expected result
     */
    public function test_get_string_from_input($string, $expected): void {
        $this->assertEquals($expected, helper::get_string_from_input($string));
    }

    public function test_get_all_filters(): void {
        $filters = helper::get_all_filters();
        $this->assertIsArray($filters);

        array_map(
            function ($filter) {
                $this->assertIsString($filter);
                $this->assertTrue(class_exists($filter));
                $this->assertTrue(is_a($filter, \tool_usertours\local\filter\base::class, true));
                $rc = new \ReflectionClass($filter);
                $this->assertTrue($rc->isInstantiable());
            },
            $filters,
        );

        $this->assertNotContains(\tool_usertours\test\hook\serverside_filter_fixture::class, $filters);
        $this->assertNotContains(\tool_usertours\test\hook\clientside_filter_fixture::class, $filters);
        $this->assertContains(\tool_usertours\local\filter\accessdate::class, $filters);
        $this->assertContains(\tool_usertours\local\clientside_filter\cssselector::class, $filters);

        $filters = helper::get_all_clientside_filters();
        array_map(
            function ($filter) {
                $this->assertIsString($filter);
            },
            $filters,
        );
    }

    public function test_get_invalid_server_filter(): void {
        \core\di::set(
            \core\hook\manager::class,
            \core\hook\manager::phpunit_get_instance([
                'test_plugin1' => self::get_fixture_path(__NAMESPACE__, 'invalid_serverside_hook_fixture.php'),
            ]),
        );

        $this->expectException(\coding_exception::class);
        helper::get_all_filters();
    }

    public function test_clientside_filter_for_serverside_hook(): void {
        \core\di::set(
            \core\hook\manager::class,
            \core\hook\manager::phpunit_get_instance([
                'test_plugin1' => self::get_fixture_path(__NAMESPACE__, 'clientside_filter_for_serverside_hook.php'),
            ]),
        );

        $this->expectException(\coding_exception::class);
        helper::get_all_filters();
    }

    public function test_serverside_filter_for_clientside_hook(): void {
        \core\di::set(
            \core\hook\manager::class,
            \core\hook\manager::phpunit_get_instance([
                'test_plugin1' => self::get_fixture_path(__NAMESPACE__, 'serverside_filter_for_clientside_hook.php'),
            ]),
        );

        $this->expectException(\coding_exception::class);
        helper::get_all_clientside_filters();
    }

    public function test_filter_hooks(): void {
        \core\di::set(
            \core\hook\manager::class,
            \core\hook\manager::phpunit_get_instance([
                'test_plugin1' => self::get_fixture_path(__NAMESPACE__, 'hook_fixtures.php'),
            ]),
        );

        $filters = helper::get_all_filters();
        $this->assertIsArray($filters);

        // Check the modifications from the serverside hook.
        $this->assertContains(\tool_usertours\test\hook\serverside_filter_fixture::class, $filters);
        $this->assertNotContains(\tool_usertours\test\hook\another_clientside_filter_fixture::class, $filters);
        $this->assertNotContains(\tool_usertours\local\filter\accessdate::class, $filters);

        // Check the modifications from the clientside hook.
        $this->assertContains(\tool_usertours\test\hook\clientside_filter_fixture::class, $filters);
        $this->assertNotContains(\tool_usertours\test\hook\another_serverside_filter_fixture::class, $filters);
        $this->assertNotContains(\tool_usertours\local\clientside_filter\cssselector::class, $filters);

        array_map(
            function ($filter) {
                $this->assertIsString($filter);
                $this->assertTrue(class_exists($filter));
                $this->assertTrue(is_a($filter, \tool_usertours\local\filter\base::class, true));
                $rc = new \ReflectionClass($filter);
                $this->assertTrue($rc->isInstantiable());
            },
            $filters,
        );
    }

    public function test_get_clientside_filter_module_names(): void {
        \core\di::set(
            \core\hook\manager::class,
            \core\hook\manager::phpunit_get_instance([
                'test_plugin1' => self::get_fixture_path(__NAMESPACE__, 'invalid_clientside_hook_fixture.php'),
            ]),
        );

        $filters = helper::get_all_clientside_filters();

        $this->expectException(\coding_exception::class);
        $this->expectExceptionMessageMatches('/Could not determine component/');
        helper::get_clientside_filter_module_names($filters);
    }
}

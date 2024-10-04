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

declare(strict_types=1);

namespace tiny_h5p;

use advanced_testcase;

/**
 * Unit tests for the \tiny_h5p\plugininfo class.
 *
 * @package     tiny_h5p
 * @covers      \tiny_h5p\plugininfo::get_plugin_configuration_for_external
 * @copyright   2025 Moodle Pty Ltd
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class plugininfo_test extends advanced_testcase {

    /**
     * Basic setup for tests.
     */
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest(true);
    }

    /**
     * Test the get_plugin_configuration_for_external method.
     *
     * @dataProvider get_plugin_configuration_for_external_provider
     * @param ?string $role Role name to assign to the user. If null, no role is assigned.
     * @param array $expectedconfiguration Expected configuration.
     * @return void
     */
    public function test_get_plugin_configuration_for_external(?string $role, array $expectedconfiguration): void {
        global $CFG;

        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $course = $generator->create_course();
        $context = \context_course::instance($course->id);
        if ($role) {
            $generator->enrol_user($user->id, $course->id, $role);
        }
        $this->setUser($user);

        $this->assertEquals($expectedconfiguration, plugininfo::get_plugin_configuration_for_external($context));
    }

    /**
     * Data provider for test_get_plugin_configuration_for_external.
     *
     * @return array
     */
    public static function get_plugin_configuration_for_external_provider(): array {
        return [
            [
                'role' => null,
                'expectedconfiguration' => ['embedallowed' => '0', 'uploadallowed' => '0'],
            ],
            [
                'role' => 'guest',
                'expectedconfiguration' => ['embedallowed' => '0', 'uploadallowed' => '0'],
            ],
            [
                'role' => 'student',
                'expectedconfiguration' => ['embedallowed' => '0', 'uploadallowed' => '0'],
            ],
            [
                'role' => 'teacher',
                'expectedconfiguration' => ['embedallowed' => '0', 'uploadallowed' => '0'],
            ],
            [
                'role' => 'editingteacher',
                'expectedconfiguration' => ['embedallowed' => '1', 'uploadallowed' => '1'],
            ],
        ];
    }
}

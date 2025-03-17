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

namespace tiny_aiplacement;

use advanced_testcase;

/**
 * Unit tests for the \tiny_aiplacement\plugininfo class.
 *
 * @package     tiny_aiplacement
 * @covers      \tiny_aiplacement\plugininfo::is_enabled_for_external
 * @covers      \tiny_aiplacement\plugininfo::get_plugin_configuration_for_external
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

        $aimanager = \core\di::get(\core_ai\manager::class);
        $aiprovider = $aimanager->create_provider_instance(
            classname: '\aiprovider_openai\provider',
            name: 'test_provider',
            enabled: true,
            config: ['apikey' => 'test_api_key'],
        );
        $aimanager->set_action_state(
            plugin: $aiprovider->provider,
            actionbasename: \core_ai\aiactions\generate_text::class::get_basename(),
            enabled: 1,
            instanceid: $aiprovider->id
        );
        $aimanager->set_action_state(
            plugin: $aiprovider->provider,
            actionbasename: \core_ai\aiactions\generate_image::class::get_basename(),
            enabled: 1,
            instanceid: $aiprovider->id
        );
    }

    /**
     * Test the is_enabled_for_external and get_plugin_configuration_for_external methods.
     *
     * @dataProvider for_external_provider
     * @param ?string $role Role name to assign to the user. If null, no role is assigned.
     * @param bool $enabled True if the aiplacement_editor must be enabled.
     * @param bool $expectedenabled Expected result for is_enabled_for_external.
     * @param array $expectedconfiguration Expected result for get_plugin_configuration_for_external.
     * @return void
     */
    public function test_for_external(?string $role, bool $enabled, bool $expectedenabled, array $expectedconfiguration): void {
        global $CFG;

        set_config('enabled', (int) $enabled, 'aiplacement_editor');

        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $course = $generator->create_course();
        $context = \context_course::instance($course->id);
        if ($role) {
            $generator->enrol_user($user->id, $course->id, $role);
        }
        $this->setUser($user);

        $this->assertEquals($expectedenabled, plugininfo::is_enabled_for_external($context, ['pluginname' => 'aiplacement']));
        $this->assertEquals($expectedconfiguration, plugininfo::get_plugin_configuration_for_external($context));
    }

    /**
     * Data provider for test_for_external.
     *
     * @return array
     */
    public static function for_external_provider(): array {
        return [
            [
                'role' => null,
                'enabled' => true,
                'expectedenabled' => false,
                'expectedconfiguration' => [
                    'policyagreed' => '0',
                    'generate_text' => '0',
                    'generate_image' => '0',
                ],
            ],
            [
                'role' => 'guest',
                'enabled' => true,
                'expectedenabled' => false,
                'expectedconfiguration' => [
                    'policyagreed' => '0',
                    'generate_text' => '0',
                    'generate_image' => '0',
                ],
            ],
            [
                'role' => 'student',
                'enabled' => true,
                'expectedenabled' => true,
                'expectedconfiguration' => [
                    'policyagreed' => '0',
                    'generate_text' => '1',
                    'generate_image' => '1',
                ],
            ],
            [
                'role' => 'teacher',
                'enabled' => true,
                'expectedenabled' => true,
                'expectedconfiguration' => [
                    'policyagreed' => '0',
                    'generate_text' => '1',
                    'generate_image' => '1',
                ],
            ],
            [
                'role' => 'teacher',
                'enabled' => false,
                'expectedenabled' => false,
                'expectedconfiguration' => [
                    'policyagreed' => '0',
                    'generate_text' => '0',
                    'generate_image' => '0',
                ],
            ],
        ];
    }
}

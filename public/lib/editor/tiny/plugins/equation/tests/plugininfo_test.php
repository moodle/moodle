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

namespace tiny_equation;

use advanced_testcase;

/**
 * Unit tests for the \tiny_equation\plugininfo class.
 *
 * @package     tiny_equation
 * @covers      \tiny_equation\plugininfo::get_plugin_configuration_for_external
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
     * @param bool $enabled True if the filter must be enabled.
     * @param array $expectedconfiguration Expected configuration.
     * @return void
     */
    public function test_get_plugin_configuration_for_external(bool $enabled, array $expectedconfiguration): void {
        global $CFG;

        $filtermanager = \core_plugin_manager::resolve_plugininfo_class('filter');
        $filtermanager::enable_plugin('mathjaxloader', $enabled ? TEXTFILTER_ON : TEXTFILTER_OFF);

        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $context = \context_system::instance();
        $this->setUser($user);

        $this->assertEquals($expectedconfiguration, plugininfo::get_plugin_configuration_for_external($context));
    }

    /**
     * Data provider for test_get_plugin_configuration_for_external.
     *
     * @return array
     */
    public static function get_plugin_configuration_for_external_provider(): array {
        $settings = [
            'libraries' => json_encode([
                [
                    'key' => 'group1',
                    'groupname' => get_string('librarygroup1', 'tiny_equation'),
                    'elements' => explode("\n", trim(get_config('tiny_equation', 'librarygroup1'))),
                    'active' => true,
                ],
                [
                    'key' => 'group2',
                    'groupname' => get_string('librarygroup2', 'tiny_equation'),
                    'elements' => explode("\n", trim(get_config('tiny_equation', 'librarygroup2'))),
                ],
                [
                    'key' => 'group3',
                    'groupname' => get_string('librarygroup3', 'tiny_equation'),
                    'elements' => explode("\n", trim(get_config('tiny_equation', 'librarygroup3'))),
                ],
                [
                    'key' => 'group4',
                    'groupname' => get_string('librarygroup4', 'tiny_equation'),
                    'elements' => explode("\n", trim(get_config('tiny_equation', 'librarygroup4'))),
                ],
            ]),
            'texdocsurl' => get_docs_url('Using_TeX_Notation'),
        ];

        return [
            [
                'enabled' => true,
                'expectedconfiguration' => ['texfilter' => '1', ...$settings],
            ],
            [
                'enabled' => false,
                'expectedconfiguration' => ['texfilter' => '0', ...$settings],
            ],
        ];
    }
}

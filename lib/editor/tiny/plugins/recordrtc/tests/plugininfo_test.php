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

namespace tiny_recordrtc;

use advanced_testcase;

/**
 * Unit tests for the \tiny_recordrtc\plugininfo class.
 *
 * @package     tiny_recordrtc
 * @covers      \tiny_recordrtc\plugininfo::is_enabled_for_external
 * @covers      \tiny_recordrtc\plugininfo::get_plugin_configuration_for_external
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
     * Test the is_enabled_for_external and get_plugin_configuration_for_external methods.
     *
     * @dataProvider for_external_provider
     * @param bool $guest Use a guest user.
     * @param bool $expectedenabled Expected result for is_enabled_for_external.
     * @param array $expectedconfiguration Expected result for get_plugin_configuration_for_external.
     * @return void
     */
    public function test_for_external(bool $guest, bool $expectedenabled, array $expectedconfiguration): void {
        global $CFG;

        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $context = \context_system::instance();
        if ($guest) {
            $this->setUser($user);
        } else {
            $this->setGuestUser();
        }

        $this->assertEquals($expectedenabled, plugininfo::is_enabled_for_external($context, ['pluginname' => 'recordrtc']));
        $this->assertEquals($expectedconfiguration, plugininfo::get_plugin_configuration_for_external($context));
    }

    /**
     * Data provider for test_for_external.
     *
     * @return array
     */
    public static function for_external_provider(): array {
        $settings = [
            'pausingallowed' => get_config('tiny_recordrtc', 'allowedpausing'),
            'allowedtypes' => get_config('tiny_recordrtc', 'allowedtypes'),
            'audiobitrate' => get_config('tiny_recordrtc', 'audiobitrate'),
            'videobitrate' => get_config('tiny_recordrtc', 'videobitrate'),
            'screenbitrate' => get_config('tiny_recordrtc', 'screenbitrate'),
            'audiotimelimit' => get_config('tiny_recordrtc', 'audiotimelimit'),
            'videotimelimit' => get_config('tiny_recordrtc', 'videotimelimit'),
            'screentimelimit' => get_config('tiny_recordrtc', 'screentimelimit'),
            'maxrecsize' => (string) get_max_upload_file_size(),
            'videoscreenwidth' => explode(',', get_config('tiny_recordrtc', 'screensize'))[0],
            'videoscreenheight' => explode(',', get_config('tiny_recordrtc', 'screensize'))[1],
            'audiortcformat' => (string) get_config('tiny_recordrtc', 'audiortcformat'),
        ];
        $allowedtypes = explode(',', $settings['allowedtypes']);

        return [
            [
                'guest' => false,
                'expectedenabled' => false,
                'expectedconfiguration' => [
                    'videoallowed' => '0',
                    'audioallowed' => '0',
                    'screenallowed' => '0',
                    ...$settings,
                ],
            ],
            [
                'guest' => true,
                'expectedenabled' => true,
                'expectedconfiguration' => [
                    'videoallowed' => in_array('video', $allowedtypes) ? '1' : '0',
                    'audioallowed' => in_array('audio', $allowedtypes) ? '1' : '0',
                    'screenallowed' => in_array('screen', $allowedtypes) ? '1' : '0',
                    ...$settings,
                ],
            ],
        ];
    }
}

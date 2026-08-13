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

namespace tiny_premium\external;

use advanced_testcase;
use context_system;
use core_external\external_api;
use tiny_premium\manager;

/**
 * Unit tests for the tiny_premium\external get_api_key class.
 *
 * @package     tiny_premium
 * @covers      \tiny_premium\external\get_api_key
 * @copyright   2026 Matt Porritt <matt.porritt@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class get_api_key_test extends advanced_testcase {
    /**
     * Basic setup for tests.
     */
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest(true);
        $this->setAdminUser();
    }

    /**
     * Test that usecloud correctly reflects the configured plugin source.
     *
     * The plugin_source config value is stored as a string, so the comparison
     * in the external function must not use a strict type comparison against
     * the manager::PACKAGE_SELF_HOSTED int constant, or self-hosted mode will
     * never be detected and the function will always report usecloud = true.
     *
     * @dataProvider execute_provider
     * @param int $pluginsource The tiny_premium/plugin_source config value to set.
     * @param bool $expectedusecloud The expected usecloud value in the response.
     */
    public function test_execute(int $pluginsource, bool $expectedusecloud): void {
        set_config('apikey', 'abc123', 'tiny_premium');
        set_config('plugin_source', $pluginsource, 'tiny_premium');

        $context = context_system::instance();
        $result = get_api_key::execute($context->id);
        $result = external_api::clean_returnvalue(get_api_key::execute_returns(), $result);

        $this->assertSame($expectedusecloud, $result['usecloud']);
    }

    /**
     * Data provider for test_execute.
     *
     * @return array
     */
    public static function execute_provider(): array {
        return [
            'Cloud plugin source' => [
                'pluginsource' => manager::PACKAGE_CLOUD,
                'expectedusecloud' => true,
            ],
            'Self-hosted plugin source' => [
                'pluginsource' => manager::PACKAGE_SELF_HOSTED,
                'expectedusecloud' => false,
            ],
        ];
    }
}

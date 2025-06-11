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
 * Testcase class for the tool_ally\auto_configurator class.
 *
 * @package   tool_ally
 * @author    Sam Chaffee
 * @copyright Copyright (c) 2017 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_ally;

use tool_ally\auto_config_resolver;
use tool_ally\auto_configurator;
use tool_ally\auto_config;
use advanced_testcase;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/prophesize_deprecation_workaround_mixin.php');


/**
 * Testcase class for the tool_ally\auto_configurator class.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2017 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @group     tool_ally
 * @group     ally
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @runTestsInSeparateProcesses
 */
class auto_configurator_test extends advanced_testcase {
    use prophesize_deprecation_workaround_mixin;

    public function setUp(): void {
        $this->resetAfterTest(true);
    }

    public function test_configure_settings() {
        $configs = [
            'secret' => 'password!',
            'key' => 'key',
            'adminurl' => 'http://somefakeurl.invalid',
            'pushurl' => 'http://someotherfakeurl.invalid',
        ];

        $resolver = $this->createMock(auto_config_resolver::class);
        $configurator = new auto_configurator();

        $resolver->method('resolve')
            ->willReturn($configs);

        $configurator->configure_settings($resolver);

        $dbconfigs = get_config('tool_ally');
        foreach ($configs as $name => $expected) {
            $this->assertEquals($expected, $dbconfigs->{$name});
        }
    }

    public function test_configure_settings_invalid_setting() {
        $configs = [
            'secret' => 'password!',
            'key' => 'key',
            'adminurl' => 'http://somefakeurl.invalid',
            'pushurl' => 'http://someotherfakeurl.invalid',
            'blawblaw' => 'yada',
        ];

        $resolver = $this->createMock(auto_config_resolver::class);
        $configurator = new auto_configurator();

        $resolver->method('resolve')
            ->willReturn($configs);

        $configurator->configure_settings($resolver);

        $dbconfigs = get_config('tool_ally');
        $this->assertArrayNotHasKey('blawblaw', (array) $dbconfigs);

        unset($configs['blawblaw']);
        foreach ($configs as $name => $expected) {
            $this->assertEquals($expected, $dbconfigs->{$name});
        }
    }

    public function test_configure_webservices() {
        $wsconfig = new auto_config();

        $configurator = new auto_configurator();
        $configurator->configure_webservices($wsconfig);

        $this->assertNotEmpty($wsconfig->token);
    }
}

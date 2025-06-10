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
 * Testcase class for the tool_ally\auto_config_resolver class.
 *
 * @package   tool_ally
 * @author    Sam Chaffee
 * @copyright Copyright (c) 2017 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_ally;

use tool_ally\auto_config_resolver;

/**
 * Testcase class for the tool_ally\auto_config_resolver class.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2017 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class auto_config_resolver_test extends \advanced_testcase {
    public function setUp(): void {
        $this->resetAfterTest(true);
    }

    public function test_resolve_clioption() {
        $configs = [
            'secret'   => 'password!',
            'key'      => 'key',
            'adminurl' => 'http://somefakeurl.invalid',
            'pushurl'  => 'http://someotherfakeurl.invalid',
        ];

        $resolver = new auto_config_resolver(json_encode($configs));
        $this->assertSame($configs, $resolver->resolve());
    }

    public function test_resolve_envvar() {
        $configs = [
            'secret'   => 'password!',
            'key'      => 'key',
            'adminurl' => 'http://somefakeurl.invalid',
            'pushurl'  => 'http://someotherfakeurl.invalid',
        ];
        $configstr = json_encode($configs);
        putenv("MOODLE_TOOL_ALLY_AUTO_CONFIGS=$configstr");

        $resolver = new auto_config_resolver('');

        $this->assertSame($configs, $resolver->resolve());
    }

    /**
     * You provide configs by using the 'configs' CLI option or by setting them to MOODLE_TOOL_ALLY_AUTO_CONFIGS
     * environment variable
     */
    public function test_resolve_noconfigs() {
        $this->expectException(\coding_exception::class);
        $this->expectExceptionMessage('No configs supplied.');
        // Be sure that the env variable is not set any longer.
        putenv('MOODLE_TOOL_ALLY_AUTO_CONFIGS');

        $resolver = new auto_config_resolver('');
        $resolver->resolve();
    }

    public function test_resolve_badconfigs() {
        $this->expectException(\coding_exception::class);
        $this->expectExceptionMessage('Config string was not valid');

        $resolver = new auto_config_resolver('somebad;$jsonstring[');
        $resolver->resolve();
    }
}

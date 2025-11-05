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

namespace tool_mergeusers;

use basic_testcase;
use tool_mergeusers\local\settable_db_config;

/**
 * Testing of settable_db_config instance.
 *
 * @package   tool_mergeusers
 * @author    Jordi Pujol-Ahulló <jordi.pujol@urv.cat>
 * @copyright 2025 onwards to Universitat Rovira i Virgili
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class settable_db_config_test extends basic_testcase {
    /**
     * @group tool_mergeusers
     * @group tool_mergeusers_settable_db_config
     */
    public function test_config_is_initialized_empty(): void {
        $config = new settable_db_config();
        $this->assertTrue($config->empty());
    }

    /**
     * @group tool_mergeusers
     * @group tool_mergeusers_settable_db_config
     */
    public function test_config_is_initialized_with_valid_settings(): void {
        $config = new settable_db_config();
        $config->add_raw(['gathering' => 'somevalue']);
        $this->assertFalse($config->empty());
        $this->assertEquals('somevalue', $config->gathering);
    }

    /**
     * @group tool_mergeusers
     * @group tool_mergeusers_settable_db_config
     * @dataProvider override_setting_provider
     */
    public function test_config_override_specific_setting(
        string $settingname,
        mixed $firstvalue,
        mixed $secondvalue,
        mixed $expectedvalue,
    ): void {
        $config = new settable_db_config();
        $config->add_raw([$settingname => $firstvalue]);
        $this->assertEquals($firstvalue, $config->{$settingname});
        $config->{$settingname} = $secondvalue;
        $this->assertEquals($expectedvalue, $config->{$settingname});
    }

    public static function override_setting_provider(): array {
        return [
            'alwaysrollback is settable' => [
                'alwaysrollback',
                false,
                true,
                true,
            ],
            'debugdb is settable' => [
                'debugdb',
                false,
                true,
                true,
            ],
            'gathering is not settable' => [
                'gathering',
                'somevalue',
                'newvaluenotpossible',
                'somevalue',
            ],
        ];
    }
}

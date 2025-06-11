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
 * Tests SDK class.
 *
 * @package   local_aws_sdk
 * @copyright Copyright (c) 2017 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_aws_sdk;
use local_aws_sdk\aws_apcu_cache;
use local_aws_sdk\aws_sdk;

/**
 * Tests SDK class.
 *
 * @package   local_aws_sdk
 * @copyright Copyright (c) 2017 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class aws_sdk_test extends \advanced_testcase {
    /**
     * Test various use cases for creating config.
     *
     * @param array $cfg
     * @param array $expected
     * @dataProvider cfg_provider
     */
    public function test_config_from_cfg($cfg, $expected) {
        global $CFG;

        $this->resetAfterTest();

        $CFG->phpunit_local_aws_sdk_test = $cfg;

        $this->assertEquals($expected, aws_sdk::config_from_cfg('phpunit_local_aws_sdk_test'));
    }

    /**
     * Test error handling for missing CFG value.
     */
    public function test_config_from_cfg_missing_cfg() {
        $this->expectException(\coding_exception::class);
        aws_sdk::config_from_cfg('asdf_asdf_hodor');
    }

    /**
     * Test error handling for CFG value is not array.
     */
    public function test_config_from_cfg_not_array() {
        global $CFG;

        $this->resetAfterTest();

        $CFG->phpunit_local_aws_sdk_test = 'hodor';

        $this->expectException(\coding_exception::class);
        aws_sdk::config_from_cfg('phpunit_local_aws_sdk_test');
    }

    /**
     * Test error handling for unknown credential cache.
     */
    public function test_config_from_cfg_unknown_cache() {
        global $CFG;

        $this->resetAfterTest();

        $CFG->phpunit_local_aws_sdk_test = [
            'region'            => 'us-east-2',
            'credentials_cache' => 'hodor',
        ];

        $this->expectException(\coding_exception::class);
        aws_sdk::config_from_cfg('phpunit_local_aws_sdk_test');
    }

    public function cfg_provider() {
        aws_sdk::autoload();

        $data = [
            // Test with no credentials.
            [
                [
                    'region' => 'us-east-2',
                ],
                [
                    'region' => 'us-east-2',
                ],
            ],
            // Test with key/secret.
            [
                [
                    'region' => 'us-east-2',
                    'key'    => 'Foo',
                    'secret' => 'Hodor',
                ],
                [
                    'region'      => 'us-east-2',
                    'credentials' => [
                        'key'    => 'Foo',
                        'secret' => 'Hodor',
                    ],
                ],
            ],
        ];

        if (aws_apcu_cache::are_requirements_met()) {
            $data[] = [
                [
                    'region'            => 'us-east-2',
                    'credentials_cache' => 'apcu',
                ],
                [
                    'region'      => 'us-east-2',
                    'credentials' => new aws_apcu_cache(),
                ],
            ];
        } else {
            $data[] = [
                [
                    'region'            => 'us-east-2',
                    'credentials_cache' => 'apcu',
                ],
                [
                    'region' => 'us-east-2',
                ],
            ];
        }

        return $data;
    }
}

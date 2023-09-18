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

namespace core\plugininfo;

use testable_core_plugin_manager;
use testable_plugininfo_base;

/**
 * Unit tests for plugin base class.
 *
 * @package   core
 * @copyright 2019 Andrew Nicols
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class base_test extends \advanced_testcase {

    /**
     * Setup to ensure that fixtures are loaded.
     */
    public static function setUpBeforeClass(): void {
        global $CFG;

        require_once($CFG->dirroot.'/lib/tests/fixtures/testable_plugin_manager.php');
        require_once($CFG->dirroot.'/lib/tests/fixtures/testable_plugininfo_base.php');
    }

    /**
     * Tear down the testable plugin manager singleton between tests.
     */
    public function tearDown(): void {
        // The caches of the testable singleton must be reset explicitly. It is
        // safer to kill the whole testable singleton at the end of every test.
        testable_core_plugin_manager::reset_caches();
    }

    /**
     * Test the load_disk_version function to check that it handles a variety of invalid supported fields.
     *
     * @dataProvider load_disk_version_invalid_supported_version_provider
     * @param array|null $supported Supported versions to inject
     * @param string|int|null $incompatible Incompatible version to inject.
     * @param int $version Version to test
     */
    public function test_load_disk_version_invalid_supported_version($supported, $incompatible, $version): void {
        $pluginman = testable_core_plugin_manager::instance();

        // Prepare a fake plugininfo instance.
        $plugininfo = new testable_plugininfo_base();
        $plugininfo->type = 'fake';
        $plugininfo->typerootdir = '/dev/null';
        $plugininfo->name = 'example';
        $plugininfo->rootdir = '/dev/null/fake';
        $plugininfo->pluginman = $pluginman;
        $plugininfo->versiondisk = 2015060600;
        $plugininfo->supported = $supported;
        $plugininfo->incompatible = $incompatible;

        $pluginman->add_fake_plugin_info($plugininfo);

        $this->expectException(\coding_exception::class);
        $this->expectExceptionMessage('Incorrect syntax in plugin supported declaration in example');
        $plugininfo->load_disk_version();
    }

    /**
     * Data provider for the load_disk_version tests for testing with invalid supported fields.
     *
     * @return array
     */
    public function load_disk_version_invalid_supported_version_provider(): array {
        return [
            'Invalid supported range.' => [
                'supported' => [31, 29],
                'incompatible' => null,
                'version' => 32,
            ],
            'Explicit list, low' => [
                'supported' => [29, 30, 31, 32],
                'incompatible' => null,
                'version' => 28,
            ],
            'Explicit list, high' => [
                'supported' => [29, 30, 31, 32],
                'incompatible' => null,
                'version' => 33,
            ],
            'Explicit list, in list' => [
                'supported' => [29, 30, 31, 32, 33],
                'incompatible' => null,
                'version' => 31,
            ],
            'Explicit list, missing value, unsupported' => [
                'supported' => [29, 30, 32],
                'incompatible' => null,
                'version' => 31,
            ],
            'Explicit list, missing value, supported' => [
                'supported' => [29, 30, 32],
                'incompatible' => null,
                'version' => 30,
            ],
        ];
    }

    /**
     * Test the load_disk_version function to check that it handles a variety of invalid incompatible fields.
     *
     * @dataProvider load_disk_version_invalid_incompatible_version_provider
     * @param mixed $incompatible
     */
    public function test_load_disk_version_invalid_incompatible_version($incompatible): void {
        $pluginman = testable_core_plugin_manager::instance();

        // Prepare a fake plugininfo instance.
        $plugininfo = new testable_plugininfo_base();
        $plugininfo->type = 'fake';
        $plugininfo->typerootdir = '/dev/null';
        $plugininfo->name = 'example';
        $plugininfo->rootdir = '/dev/null/fake';
        $plugininfo->pluginman = $pluginman;
        $plugininfo->versiondisk = 2015060600;
        $plugininfo->incompatible = $incompatible;

        $pluginman->add_fake_plugin_info($plugininfo);

        $this->expectException(\coding_exception::class);
        $this->expectExceptionMessage('Incorrect syntax in plugin incompatible declaration in example');
        $plugininfo->load_disk_version();
    }

    /**
     * Data provider for the load_disk_version tests for testing with invalid incompatible fields.
     *
     * @return array
     */
    public function load_disk_version_invalid_incompatible_version_provider(): array {
        return [
            [[38]],
            [['38']],
            [3.8],
            ['3.8'],
            [''],
            ['somestring'],
        ];

    }

    /**
     * Test the load_disk_version function to check that it handles a range of correct supported and incompatible field
     * definitions.
     *
     * @dataProvider load_disk_version_branch_supports_provider
     * @param array|null $supported Supported versions to inject
     * @param string|int|null $incompatible Incompatible version to inject.
     * @param int $version Version to test
     */
    public function test_load_disk_version_branch_supports($supported, $incompatible, $version): void {
        $pluginman = testable_core_plugin_manager::instance();

        // Prepare a fake plugininfo instance.
        $plugininfo = new testable_plugininfo_base();
        $plugininfo->type = 'fake';
        $plugininfo->typerootdir = '/dev/null';
        $plugininfo->name = 'example';
        $plugininfo->rootdir = '/dev/null/fake';
        $plugininfo->pluginman = $pluginman;
        $plugininfo->versiondisk = 2015060600;
        $plugininfo->supported = $supported;
        $plugininfo->incompatible = $incompatible;

        $pluginman->add_fake_plugin_info($plugininfo);

        $plugininfo->load_disk_version();

        $this->assertEquals($supported, $plugininfo->supported);
        $this->assertEquals($incompatible, $plugininfo->incompatible);
    }

    /**
     * Test cases for tests of load_disk_version for testing the supported/incompatible fields.
     *
     * @return array
     */
    public function load_disk_version_branch_supports_provider(): array {
        return [
            'Range, branch in support, lowest' => [
                'supported' => [29, 31],
                'incompatible' => null,
                'version' => 29,
            ],
            'Range, branch in support, mid' => [
                'supported' => [29, 31],
                'incompatible' => null,
                'version' => 30,
            ],
            'Range, branch in support, highest' => [
                'supported' => [29, 31],
                'incompatible' => null,
                'version' => 31,
            ],

            'Range, branch not in support, high' => [
                'supported' => [29, 31],
                'incompatible' => null,
                'version' => 32,
            ],
            'Range, branch not in support, low' => [
                'supported' => [29, 31],
                'incompatible' => null,
                'version' => 28,
            ],
            'Range, incompatible, high.' => [
                'supported' => [29, 31],
                'incompatible' => 32,
                'version' => 33,
            ],
            'Range, incompatible, low.' => [
                'supported' => [29, 31],
                'incompatible' => 32,
                'version' => 31,
            ],
            'Range, incompatible, equal.' => [
                'supported' => [29, 31],
                'incompatible' => 32,
                'version' => 32,
            ],
            'No supports' => [
                'supported' => null,
                'incompatible' => null,
                'version' => 32,
            ],
            'No supports, but incompatible, older' => [
                'supported' => null,
                'incompatible' => 30,
                'version' => 32,
            ],
            'No supports, but incompatible, equal' => [
                'supported' => null,
                'incompatible' => 32,
                'version' => 32,
            ],
            'No supports, but incompatible, newer' => [
                'supported' => null,
                'incompatible' => 34,
                'version' => 32,
            ],
            'String incompatible' => [
                'supported' => null,
                'incompatible' => '34',
                'version' => 32,
            ],
            'Empty incompatible' => [
                'supported' => null,
                'incompatible' => null,
                'version' => 32,
            ],
        ];
    }

    /**
     * Ensure that plugintype_supports_ordering() returns true.
     * @covers ::plugintype_supports_ordering
     */
    public function test_plugintype_supports_ordering(): void {
        $this->assertFalse(base::plugintype_supports_ordering());
    }

    /**
     * Ensure that the base implementation is used for plugins not supporting ordering.
     *
     * @dataProvider plugins_not_supporting_ordering
     * @param string $plugin
     * @coversNothing
     *
     * Note: This test cannot declare coverage because it covers the various plugin implementations.
     */
    public function test_get_sorted_plugins(
        string $plugin,
    ): void {
        [$plugintype, $pluginname] = explode('_', $plugin, 2);
        $classname = \core_plugin_manager::resolve_plugininfo_class($plugintype);

        $this->assertFalse($classname::plugintype_supports_ordering());

        $this->assertNull($classname::get_sorted_plugins());
        $this->assertNull($classname::get_sorted_plugins(true));
        $this->assertNull($classname::get_sorted_plugins(false));

        $this->assertFalse($classname::change_plugin_order($pluginname, base::MOVE_UP));
        $this->assertFalse($classname::change_plugin_order($pluginname, base::MOVE_DOWN));
    }

    /**
     * Data provider for plugins_not_supporting_ordering.
     *
     * @return string[]
     */
    public function plugins_not_supporting_ordering(): array {
        return [
            ['mod_assign'],
            ['block_login'],
        ];
    }
}

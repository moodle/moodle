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

use advanced_testcase;
use coding_exception;
use tool_mergeusers\fixtures\add_settings_before_merging_callbacks;
use tool_mergeusers\local\config;
use tool_mergeusers\local\default_db_config;
use tool_mergeusers\local\jsonizer;

/**
 * Testing of config instance.
 *
 * @package   tool_mergeusers
 * @author    Jordi Pujol-Ahulló <jordi.pujol@urv.cat>
 * @copyright 2025 onwards to Universitat Rovira i Virgili
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers   \tool_mergeusers\local\config
 */
final class config_test extends advanced_testcase {
    /**
     * Ensures that the given gathering class implements the interface.
     *
     * This test may run on Moodle instances with hook implemented.
     *
     * Hook add_settings_before_merging may alter the default settings,
     * in particular with a different default gathering implementation.
     *
     * @group tool_mergeusers
     * @group tool_mergeusers_config
     */
    public function test_config_is_initialized_with_a_valid_gathering(): void {
        $config = config::instance();
        $interfaces = class_implements($config->gathering);
        $this->assertArrayHasKey(\tool_mergeusers\local\cli\gathering::class, $interfaces);
    }

    /**
     * This test emulates a scenario without hook implemented for adding settings.
     *
     * @group tool_mergeusers
     * @group tool_mergeusers_config
     */
    public function test_config_has_default_gathering(): void {
        $this->prepare_hook_scenario_without_settings();
        $config = config::instance();
        $this->assertEquals(default_db_config::$config['gathering'], $config->gathering);
    }

    /**
     * @group tool_mergeusers
     * @group tool_mergeusers_config
     */
    public function test_config_is_initialized_with_default_and_custom_settings(): void {
        $expectedgathering = $this->prepare_custom_settings();
        $config = config::instance();

        $this->assertEquals($expectedgathering, $config->gathering);
    }

    /**
     * @group tool_mergeusers
     * @group tool_mergeusers_config
     */
    public function test_config_is_initialized_with_default_and_hook_settings(): void {
        $this->prepare_hook_settings();
        $config = config::instance();

        $this->assertEquals(add_settings_before_merging_callbacks::$gatheringname, $config->gathering);
    }

    /**
     * @group tool_mergeusers
     * @group tool_mergeusers_config
     */
    public function test_config_is_initialized_with_default_hook_and_custom_settings(): void {
        $expectedgathering = $this->prepare_custom_settings();
        $this->prepare_hook_settings();
        $config = config::instance();

        $this->assertEquals($expectedgathering, $config->gathering);
    }

    /**
     * Prepares the scenario with some custom settings.
     *
     * @return string
     */
    private function prepare_custom_settings(): string {
        $this->resetAfterTest();
        $newgathering = random_string();
        set_config('customdbsettings', jsonizer::to_json(['gathering' => $newgathering]), 'tool_mergeusers');
        return $newgathering;
    }

    /**
     * Prepares a hook callback to test hook settings.
     *
     * @return void
     * @throws coding_exception
     */
    private function prepare_hook_settings(): void {
        require_once(__DIR__ . '/fixtures/add_settings_before_merging_callbacks.php');
        \core\di::set(
            \core\hook\manager::class,
            \core\hook\manager::phpunit_get_instance([
                'tool_mergeusers' => __DIR__ . '/fixtures/add_settings_before_merging_hooks.php',
            ]),
        );
    }

    /**
     * Prepares a scenario without settings hook implemented.
     *
     * @return void
     * @throws coding_exception
     */
    private function prepare_hook_scenario_without_settings(): void {
        require_once(__DIR__ . '/fixtures/add_empty_settings_before_merging_callbacks.php');
        \core\di::set(
            \core\hook\manager::class,
            \core\hook\manager::phpunit_get_instance([
                'tool_mergeusers' => __DIR__ . '/fixtures/add_empty_settings_before_merging_hooks.php',
            ]),
        );
    }
}

<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace core\tests;

// phpcs:disable moodle.PHPUnit.TestCaseProvider.dataProviderSyntaxMethodNotFound

/**
 * Base class for general testing of plugin features and APIs.
 * The test must not modify database or any global state.
 *
 * Following is required to allow filtering of test by Frankenstyle plugin name:
 *  - all providers used in the tests must use components as keys of provider data
 *  - all tests must include group "plugin_checks"
 *
 * @package   core
 * @copyright 2025 Petr Skoda
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class plugin_checks_testcase extends \basic_testcase {
    /**
     * Data provider for testing of all available plugins.
     *
     * @return array as array of [component, plugintype, pluginname, dir]
     */
    public static function all_plugins_provider(): array {
        $result = [];
        foreach (\core_component::get_plugin_types() as $plugintype => $unused) {
            foreach (\core_component::get_plugin_list($plugintype) as $pluginname => $dir) {
                $component = $plugintype . '_' . $pluginname;
                $result[$component] = [$component, $plugintype, $pluginname, $dir];
            }
        }
        return $result;
    }

    /**
     * Include file and return an array variable defined in its global scope.
     * This is intended primarily for files inside plugin /db/ subdirectory.
     *
     * @param string $phpfile
     * @param string $variablename
     * @return array|null NULL means file does not exist
     */
    protected function fetch_array_from_file(string $phpfile, string $variablename): ?array {
        if (!file_exists($phpfile)) {
            return null;
        }
        require($phpfile);
        return $$variablename;
    }
}

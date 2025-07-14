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

use advanced_testcase;

/**
 * Unit tests for the dataformat plugininfo class
 *
 * @package     core
 * @covers      \core\plugininfo\dataformat
 * @copyright   2022 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class dataformat_test extends advanced_testcase {

    /**
     * Helper method, to allow easy filtering of default formats in order to perform assertions without any third-party
     * formats affecting expected results
     *
     * @param string $format
     * @return bool
     */
    private function filter_default_plugins(string $format): bool {
        $defaultformats = ['csv', 'excel', 'html', 'json', 'ods', 'pdf'];

        return in_array($format, $defaultformats);
    }

    /**
     * Test getting enabled plugins
     */
    public function test_get_enabled_plugins(): void {
        $this->resetAfterTest();

        // Check all default formats.
        $plugins = array_filter(dataformat::get_enabled_plugins(), [$this, 'filter_default_plugins']);
        $this->assertEquals([
            'csv' => 'csv',
            'excel' => 'excel',
            'html' => 'html',
            'json' => 'json',
            'ods' => 'ods',
            'pdf' => 'pdf',
        ], $plugins);

        // Disable excel & html.
        dataformat::enable_plugin('excel', 0);
        dataformat::enable_plugin('html', 0);

        $plugins = array_filter(dataformat::get_enabled_plugins(), [$this, 'filter_default_plugins']);
        $this->assertEquals([
            'csv' => 'csv',
            'json' => 'json',
            'ods' => 'ods',
            'pdf' => 'pdf',
        ], $plugins);
    }

    /**
     * Test getting enabled plugins obeys configured sortorder
     */
    public function test_get_enabled_plugins_sorted(): void {
        $this->resetAfterTest();

        set_config('dataformat_plugins_sortorder', 'csv,pdf,excel,json,html,ods');

        $plugins = array_filter(dataformat::get_enabled_plugins(), [$this, 'filter_default_plugins']);
        $this->assertEquals([
            'csv' => 'csv',
            'pdf' => 'pdf',
            'excel' => 'excel',
            'json' => 'json',
            'html' => 'html',
            'ods' => 'ods',
        ], $plugins);
    }
}

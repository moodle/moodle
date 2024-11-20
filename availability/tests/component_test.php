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

namespace core_availability;

/**
 * Unit tests for the component and plugin definitions for availability system.
 *
 * @package core_availability
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class component_test extends \advanced_testcase {
    /**
     * Tests loading a class from /availability/classes.
     */
    public function test_load_class(): void {
        $result = get_class_methods('\core_availability\info');
        $this->assertTrue(is_array($result));
    }

    /**
     * Tests the plugininfo class is present and working.
     */
    public function test_plugin_info(): void {
        // This code will throw debugging information if the plugininfo class
        // is missing. Unfortunately it doesn't actually cause the test to
        // fail, but it's obvious when running test at least.
        $pluginmanager = \core_plugin_manager::instance();
        $list = $pluginmanager->get_enabled_plugins('availability');
        $this->assertArrayHasKey('completion', $list);
        $this->assertArrayHasKey('date', $list);
        $this->assertArrayHasKey('grade', $list);
        $this->assertArrayHasKey('group', $list);
        $this->assertArrayHasKey('grouping', $list);
        $this->assertArrayHasKey('profile', $list);
    }
}

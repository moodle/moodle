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
 * One Roster Enrolment Client Unit tests.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_oneroster;

use advanced_testcase;

/**
 * One Roster tests for the plugin class.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers  enrol_oneroster\plugin
 */
class plugin_testcase extends advanced_testcase {
    /**
     * Ensure that the name of the plugin is correctly determined.
     */
    public function test_name(): void {
        $plugin = new plugin();
        $this->assertEquals('oneroster', $plugin->get_name());
    }

    /**
     * Ensure that the class alias maps to the plugin correctly.
     */
    public function test_class_alias(): void {
        require_once(__DIR__ . '/../lib.php');
        $plugin = new \enrol_oneroster_plugin();
        $this->assertInstanceOf(plugin::class, $plugin);
    }
}

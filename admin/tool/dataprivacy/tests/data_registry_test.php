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
 * Unit tests for the data_registry class.
 *
 * @package    tool_dataprivacy
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use \tool_dataprivacy\data_registry;

/**
 * Unit tests for the data_registry class.
 *
 * @package    tool_dataprivacy
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_dataprivacy_dataregistry_testcase extends advanced_testcase {

    /**
     * Ensure that the get_effective_context_value only errors if provided an inappropriate element.
     *
     * This test is not great because we only test a limited set of values. This is a fault of the underlying API.
     */
    public function test_get_effective_context_value_invalid_element() {
        $this->expectException(coding_exception::class);
        data_registry::get_effective_context_value(\context_system::instance(), 'invalid');
    }

    /**
     * Ensure that the get_effective_contextlevel_value only errors if provided an inappropriate element.
     *
     * This test is not great because we only test a limited set of values. This is a fault of the underlying API.
     */
    public function test_get_effective_contextlevel_value_invalid_element() {
        $this->expectException(coding_exception::class);
        data_registry::get_effective_contextlevel_value(\context_system::instance(), 'invalid');
    }
}

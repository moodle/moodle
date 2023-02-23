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

namespace core_privacy;

use core_privacy\local\metadata\types\plugintype_link;

/**
 * Tests for the \core_privacy API's types\plugintype_link functionality.
 *
 * @package     core_privacy
 * @category    test
 * @copyright   2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core_privacy\local\metadata\types\plugintype_link
 */
class types_plugintype_link_test extends \advanced_testcase {

    /**
     * Ensure that warnings are thrown if string identifiers contain invalid characters.
     *
     * @dataProvider invalid_string_provider
     * @param   string  $name Name
     * @param   string  $summary Summary
     * @covers ::__construct
     */
    public function test_invalid_configs($name, $privacyfields, $summary) {
        $record = new plugintype_link($name, $privacyfields, $summary);
        $this->assertDebuggingCalled();
    }

    /**
     * Ensure that warnings are not thrown if debugging is not enabled, even if string identifiers contain invalid characters.
     *
     * @dataProvider invalid_string_provider
     * @param   string  $name Name
     * @param   string  $summary Summary
     * @covers ::__construct
     */
    public function test_invalid_configs_debug_normal($name, $privacyfields, $summary) {
        global $CFG;
        $this->resetAfterTest();

        $CFG->debug = DEBUG_NORMAL;
        $record = new plugintype_link($name, $privacyfields, $summary);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Ensure that no warnings are shown for valid combinations.
     *
     * @dataProvider valid_string_provider
     * @param   string  $name Name
     * @param   string  $summary Summary
     * @covers ::__construct
     */
    public function test_valid_configs($name, $privacyfields, $summary) {
        $record = new plugintype_link($name, $privacyfields, $summary);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Data provider with a list of invalid string identifiers.
     *
     * @return  array
     */
    public function invalid_string_provider() {
        return [
            'Space in summary' => [
                'example',
                [],
                'This table is used for purposes.',
            ],
            'Comma in summary' => [
                'example',
                [],
                'privacy,foo',
            ],
        ];
    }

    /**
     * Data provider with a list of valid string identifiers.
     *
     * @return  array
     */
    public function valid_string_provider() {
        return [
            'Valid combination' => [
                'example',
                [],
                'privacy:example:valid',
            ],
        ];
    }
}

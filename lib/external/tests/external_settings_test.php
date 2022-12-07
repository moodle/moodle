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

namespace core_external;

/**
 * Unit tests for core_external\external_settings.
 *
 * @package     core_external
 * @category    test
 * @copyright   2022 Andrew Lyons <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @covers      \core_external\external_settings
 */
class external_settings_test extends \advanced_testcase {
    /**
     * Tests for external_settings class.
     */
    public function test_external_settings() {

        $settings = external_settings::get_instance();
        $currentraw = $settings->get_raw();
        $currentfilter = $settings->get_filter();
        $currentfile = $settings->get_file();
        $currentfileurl = $settings->get_fileurl();

        $this->assertInstanceOf(external_settings::class, $settings);

        // Check apis.
        $settings->set_file('plugin.php');
        $this->assertEquals('plugin.php', $settings->get_file());
        $settings->set_filter(false);
        $this->assertFalse($settings->get_filter());
        $settings->set_fileurl(false);
        $this->assertFalse($settings->get_fileurl());
        $settings->set_raw(true);
        $this->assertTrue($settings->get_raw());

        // Restore original values.
        $settings->set_file($currentfile);
        $settings->set_filter($currentfilter);
        $settings->set_fileurl($currentfileurl);
        $settings->set_raw($currentraw);
    }

}

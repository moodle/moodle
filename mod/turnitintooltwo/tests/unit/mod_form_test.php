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
 * Unit tests for (some of) turnitintooltwo/mod_form.php.
 *
 * @package    turnitintooltwo
 * @copyright  2018 Turnitin
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $CFG;

require_once($CFG->dirroot . '/mod/turnitintooltwo/mod_form.php');

class mod_form_test extends advanced_testcase {

    /**
     * Test that when current has a submitpapers the submitpapers from the config is not used.
     * I.E. instructor default overrides the system default
     */
    public function test_should_populate_submitpapersto_to_the_value_in_current_when_submitpapersto_not_null() {
        $this->resetAfterTest();

        $current = new stdClass();
        $current->submitpapersto = 1;

        // Test that System Default is not applied
        set_config('default_submitpapersto', 0, 'turnitintooltwo');

        $current = mod_turnitintooltwo_mod_form::populate_submitpapersto($current);

        $this->assertEquals($current->submitpapersto, 1);
    }

    /**
     * Test that when current does not have a submitpapers the submitpapers from the config is used.
     * I.E. when there is no instructor default the system default is used
     */
    public function test_should_populate_submitpapersto_to_the_value_in_config_when_submitpapersto_null() {
        $this->resetAfterTest();

        $current = new stdClass();
        $current->submitpapersto = null;

        // Test that System Default is not applied
        set_config('default_submitpapersto', 0, 'turnitintooltwo');

        $current = mod_turnitintooltwo_mod_form::populate_submitpapersto($current);

        $this->assertEquals($current->submitpapersto, 0);
    }
}
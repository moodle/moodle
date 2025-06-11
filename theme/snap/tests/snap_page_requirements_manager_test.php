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
 * Test snap requirements manager
 * @author    Guy Thomas
 * @copyright Copyright (c) 2016 Open LMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace theme_snap;
use theme_snap\snap_page_requirements_manager;

/**
 * Class theme_snap_snap_page_requirements_manager_test
 * @author    Guy Thomas
 * @copyright Copyright (c) 2016 Open LMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class snap_page_requirements_manager_test extends \advanced_testcase {

    /**
     * Test classic theme does not black list M.core_completion.init.
     */
    public function test_js_init_call_classic() {
        global $CFG, $PAGE;

        $this->resetAfterTest();

        $CFG->theme = 'classic';
        $PAGE->initialise_theme_and_output();

        $PAGE->requires->js_init_call('M.core_completion.init');

        $endcode = $PAGE->requires->get_end_code();
        $this->assertStringContainsString('M.core_completion.init', $endcode);
    }

    /**
     * Test snap theme black lists M.core_completion.init and excludes the code.
     */
    public function test_js_init_call_snap() {
        global $CFG, $PAGE;

        $this->resetAfterTest();

        $CFG->theme = 'snap';
        $PAGE->initialise_theme_and_output();

        $PAGE->requires->js_init_call('M.core_completion.init');

        $endcode = $PAGE->requires->get_end_code();
        $this->assertStringNotContainsString('M.core_completion.init', $endcode);
    }

    /**
     * Integration test - Test classic theme does not use snap page requirements manager.
     */
    public function test_classic_theme_regular_requirements_manager() {
        global $CFG, $PAGE;

        $this->resetAfterTest();

        $CFG->theme = 'classic';

        $PAGE->initialise_theme_and_output();
        $this->assertInstanceOf('page_requirements_manager', $PAGE->requires);
    }

    /**
     * Integration test - Test snap theme uses snap page requirements manager.
     */
    public function test_snap_theme_snap_requirements_manager() {
        global $CFG, $PAGE;

        $this->resetAfterTest();

        $CFG->theme = 'snap';

        $PAGE->initialise_theme_and_output();
        // @codingStandardsIgnoreLine
        $this->assertInstanceOf(snap_page_requirements_manager::class, $PAGE->requires);
    }
}

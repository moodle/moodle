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
 *
 *
 * @package
 * @copyright  2016 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class progress_display_test extends \advanced_testcase {

    /**
     * Test basic function of progress_display, updating status and outputting wibbler.
     */
    public function test_progress_display_update(): void {
        ob_start();
        $progress = new core_mock_progress_display();
        $progress->start_progress('');
        $this->assertEquals(1, $progress->get_current_state());
        $this->assertEquals(1, $progress->get_direction());
        $this->assertTimeCurrent($progress->get_last_wibble());
        // Wait 1 second to ensure that all code in update_progress is run.
        $this->waitForSecond();
        $progress->update_progress();
        $this->assertEquals(2, $progress->get_current_state());
        $this->assertEquals(1, $progress->get_direction());
        $this->assertTimeCurrent($progress->get_last_wibble());
        $output = ob_get_clean();
        $this->assertStringContainsString('wibbler', $output);
        $this->assertStringContainsString('wibble state0', $output);
        $this->assertStringContainsString('wibble state1', $output);
    }

    /**
     * Test wibbler states. Wibbler should reverse direction at the start and end of its sequence.
     */
    public function test_progress_display_wibbler(): void {
        ob_start();
        $progress = new core_mock_progress_display();
        $progress->start_progress('');
        $this->assertEquals(1, $progress->get_direction());

        // Set wibbler to final state and progress to check that it reverses direction.
        $progress->set_current_state(core_mock_progress_display::WIBBLE_STATES);
        $this->waitForSecond();
        $progress->update_progress();
        $this->assertEquals(core_mock_progress_display::WIBBLE_STATES - 1, $progress->get_current_state());
        $this->assertEquals(-1, $progress->get_direction());

        // Set wibbler to beginning and progress to check that it reverses direction.
        $progress->set_current_state(0);
        $this->waitForSecond();
        $progress->update_progress();
        $this->assertEquals(1, $progress->get_current_state());
        $this->assertEquals(1, $progress->get_direction());
        $output = ob_get_clean();
        $this->assertStringContainsString('wibbler', $output);
        $this->assertStringContainsString('wibble state0', $output);
        $this->assertStringContainsString('wibble state13', $output);

    }

}

/**
 * Helper class that allows access to private values
 */
class core_mock_progress_display extends \core\progress\display {
    public function get_last_wibble() {
        return $this->lastwibble;
    }

    public function get_current_state() {
        return $this->currentstate;
    }

    public function get_direction() {
        return $this->direction;
    }

    public function set_current_state($state) {
        $this->currentstate = $state;
    }

    public function set_direction($direction) {
        $this->direction = $direction;
    }
}

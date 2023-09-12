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

namespace core\hook\navigation;

/**
 * Test hook for primary navigation.
 *
 * @coversDefaultClass \core\hook\navigation\primary_extend
 *
 * @package   core
 * @author    Petr Skoda
 * @copyright 2023 Open LMS
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class primary_extend_test extends \advanced_testcase {
    /**
     * Test stoppable_trait.
     * @covers ::stop_propagation
     */
    public function test_stop_propagation() {
        global $PAGE;
        $this->resetAfterTest();

        $PAGE = new \moodle_page();
        $PAGE->set_url('/');
        $primarynav = new \core\navigation\views\primary($PAGE);

        $hook = new primary_extend($primarynav);
        $this->assertInstanceOf('Psr\EventDispatcher\StoppableEventInterface', $hook);
        $this->assertFalse($hook->isPropagationStopped());

        $hook->stop_propagation();
        $this->assertTrue($hook->isPropagationStopped());
    }
}

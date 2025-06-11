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

namespace core_calendar;

use core_calendar\local\event\entities\action_interface;

/**
 * Action factory testcase.
 *
 * @package    core_calendar
 * @copyright 2017 Cameron Ball <cameron@cameron1729.xyz>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class action_factory_test extends \advanced_testcase {
    /**
     * Test action factory.
     */
    public function test_action_factory(): void {
        $factory = new action_factory();
        $instance = $factory->create_instance(
            'test',
            new \moodle_url('http://example.com'),
            1729,
            true
        );

        $this->assertInstanceOf(action_interface::class, $instance);
    }
}

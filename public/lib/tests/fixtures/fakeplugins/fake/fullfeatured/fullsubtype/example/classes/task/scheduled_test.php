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

namespace fullsubtype_example\task;

use core\task\scheduled_task;

/**
 * Test scheduled class.
 *
 * @package core
 * @copyright 2024 Jake Dallimore <jrhdallimore@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class scheduled_test extends scheduled_task {
    /**
     * Get the task name.
     *
     * @return string
     */
    public function get_name() {
        return "Test scheduled task";
    }

    /**
     * Dummy execute doing nothing.
     */
    public function execute() {
    }
}

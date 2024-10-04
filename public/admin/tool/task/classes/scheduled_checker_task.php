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

namespace tool_task;

/**
 * Checker class. Fake scheduled task used only to check that crontab settings are valid.
 *
 * @package    tool_task
 * @copyright  2021 Jordi Pujol-Ahulló <jpahullo@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class scheduled_checker_task extends \core\task\scheduled_task {

    /**
     * Gets the checker task name.
     */
    public function get_name() {
        return "Checker task";
    }

    /**
     * Does nothing.
     */
    public function execute() {
    }
}

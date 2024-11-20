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

namespace core\hook\task;

use core\hook\described_hook;
use core\task\task_base;

/**
 * Hook to allow plugins to get information when a task has reached the maximum fail delay in adhoc and scheduled task
 *
 * @package    core
 * @copyright  2024 Raquel Ortega <raquel.ortega@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\core\attribute\tags('task')]
#[\core\attribute\label('Allow plugins to get information when a task reaches its maximum failure delay.')]
final class after_failed_task_max_delay {

    /**
     * Constructor.
     * @param task_base $task failed task.
     */
    public function __construct(
        protected task_base $task,
    ) {

    }

    /**
     * Get the task object.
     *
     * @return task_base Task object.
     */
    public function get_task(): task_base {
        return $this->task;
    }
}

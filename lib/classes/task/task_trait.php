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

namespace core\task;

/**
 * This file defines a trait to assist with unit tests in tasks.
 *
 * @package    core
 * @copyright  2024 Huong Nguyen <huongnv13@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait task_trait {

    /**
     * Helper to execute a particular task.
     *
     * @param string $taskclass The task class.
     */
    protected function execute_task(string $taskclass): void {
        // Run the scheduled task.
        $this->start_output_buffering();
        $task = manager::get_scheduled_task($taskclass);
        $task->execute();
        $this->stop_output_buffering();
    }

    /**
     * Helper to start output buffering.
     */
    protected function start_output_buffering(): void {
        ob_start();
    }

    /**
     * Helper to stop output buffering.
     *
     * @return string|null The output buffer contents or null if output buffering is not active.
     */
    protected function stop_output_buffering(): ?string {
        $output = ob_get_contents();
        ob_end_clean();

        return $output;
    }
}

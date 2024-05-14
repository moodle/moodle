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

namespace core\output;

use core\task\adhoc_task;
use core\task\stored_progress_task_trait;
use core\url;
use stdClass;

/**
 * Indicator for displaying status and progress of a background task
 *
 * @package   core
 * @copyright 2024 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class task_indicator implements renderable, templatable {
    /** @var ?stdClass $taskrecord */
    protected ?stdClass $taskrecord;

    /** @var ?stored_progress_bar $progressbar */
    protected ?stored_progress_bar $progressbar;

    /**
     * Find the task record, and get the progress bar object.
     *
     * @param adhoc_task $task The task whose progress is being indicated. The task class must use stored_progress_task_trait.
     * @param string $heading The header text for the indicator.
     * @param string $message A message to explain what is happening while the task is running.
     * @param ?url $redirecturl An optional URL to redirect to when the task completes.
     * @param ?pix_icon $icon An optional icon to display with the heading.
     * @param array $extraclasses Extra class names to apply to the indicator's container.
     * @throws \coding_exception
     */
    public function __construct(
        /** @var adhoc_task $task The task whose progress is being indicated. The task class must use stored_progress_task_trait. */
        protected adhoc_task $task,
        /** @var string $heading The header text for the indicator. */
        protected string $heading,
        /** @var string $message A message to explain what is happening while the task is running. */
        protected string $message,
        /** @var ?url $redirecturl An optional URL to redirect to when the task completes. */
        protected ?url $redirecturl = null,
        /** @var ?pix_icon $icon An optional icon to display with the heading. */
        protected ?pix_icon $icon = new pix_icon('i/timer', ''),
        /** @var array $extraclasses Extra class names to apply to the indicator's container. */
        protected array $extraclasses = [],
    ) {
        if (!class_uses($task::class, stored_progress_task_trait::class)) {
            throw new \coding_exception('task_indicator can only be used for tasks using stored_progress_task_trait.');
        }
        $this->setup_task_data();
    }

    /**
     * Fetch the task record matching the current task, if there is one.
     *
     * If one exists, also set up a progress bar, and set up the "run now" link if permitted.
     *
     * @return void
     */
    protected function setup_task_data(): void {
        $this->taskrecord = \core\task\manager::get_queued_adhoc_task_record($this->task) ?: null;
        if ($this->taskrecord) {
            $this->task->set_id($this->taskrecord->id);
            $idnumber = stored_progress_bar::convert_to_idnumber($this->task::class, $this->task->get_id());
            $this->progressbar = stored_progress_bar::get_by_idnumber($idnumber);
        }
    }

    /**
     * Check if there is a task record matching the defined task.
     *
     * If so, set the task ID and progress bar, then return true. If not, return false.
     *
     * @return bool
     */
    public function has_task_record(): bool {
        $this->setup_task_data();
        return !is_null($this->taskrecord);
    }

    #[\Override]
    public function export_for_template(renderer_base $output): array {
        $export = [];
        if ($this->taskrecord) {
            $export['heading'] = $this->heading;
            $export['message'] = $this->message;
            $export['progress'] = $this->progressbar->export_for_template($output);
            $export['icon'] = $this->icon ? $this->icon->export_for_template($output) : '';
            $export['redirecturl'] = $this->redirecturl->out();
            $export['extraclasses'] = implode(' ', $this->extraclasses);
            $this->progressbar->init_js();
        }
        return $export;
    }
}

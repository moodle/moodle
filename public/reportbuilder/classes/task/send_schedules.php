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

declare(strict_types=1);

namespace core_reportbuilder\task;

use core\{clock, di};
use core\task\scheduled_task;
use core_reportbuilder\local\helpers\schedule;
use core_reportbuilder\local\models\schedule as model;

/**
 * Scheduled task for sending queued report schedules
 *
 * @package     core_reportbuilder
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class send_schedules extends scheduled_task {

    /**
     * Return name of the task
     *
     * @return string
     */
    public function get_name(): string {
        return get_string('tasksendschedules', 'core_reportbuilder');
    }

    /**
     * Execute the task, request all pending schedules to be sent
     */
    public function execute(): void {
        global $DB;

        $schedules = model::get_records_select('enabled = 1 AND timenextsend <= :time', [
            'time' => di::get(clock::class)->time(),
        ]);
        $schedules = array_filter($schedules, [schedule::class, 'should_send_schedule']);

        // Loop over all schedules for sending, execute corresponding task to send each individually.
        foreach ($schedules as $schedule) {
            $sendschedule = new send_schedule();
            $sendschedule->set_custom_data([
                'reportid' => $schedule->get('reportid'),
                'scheduleid' => $schedule->get('id'),
            ]);
            $sendschedule->execute();

            // Calculate next send time (set persistent property manually to avoid updating it's user/time modified data).
            $DB->set_field($schedule::TABLE, 'timenextsend', schedule::calculate_next_send_time($schedule->read()),
                ['id' => $schedule->get('id')]);
        }
    }
}

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
use core\task\adhoc_task;
use core_user;
use core_reportbuilder\local\schedules\base;
use core_reportbuilder\local\helpers\schedule as helper;
use core_reportbuilder\local\models\schedule;
use moodle_exception;

/**
 * Ad-hoc task for sending a single report schedule
 *
 * @package     core_reportbuilder
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class send_schedule extends adhoc_task {

    use \core\task\logging_trait;

    /**
     * Return name of the task
     *
     * @return string
     */
    public function get_name(): string {
        return get_string('tasksendschedule', 'core_reportbuilder');
    }

    /**
     * Execute the task
     */
    public function execute(): void {
        global $CFG, $USER, $DB;

        [
            'reportid' => $reportid,
            'scheduleid' => $scheduleid,
        ] = (array) $this->get_custom_data();

        // Custom reports are disabled.
        if (empty($CFG->enablecustomreports)) {
            return;
        }

        $schedule = schedule::get_record(['id' => $scheduleid, 'reportid' => $reportid]);
        if ($schedule === false || !$instance = base::from_persistent($schedule)) {
            $this->log('Invalid schedule', 0);
            return;
        }

        $this->log_start('Sending schedule: ' . $schedule->get_formatted_name() . ' (' . $instance->get_name() . ')');

        $originaluser = $USER;

        // Get the schedule creator, ensure it's an active account.
        try {
            $schedulecreator = core_user::get_user($schedule->get('usercreated'), '*', MUST_EXIST);
            core_user::require_active_user($schedulecreator);
        } catch (moodle_exception $exception) {
            $this->log('Invalid schedule creator: ' . $exception->getMessage(), 0);
            return;
        }

        // Switch to schedule creator, and retrieve list of recipient users.
        \core\cron::setup_user($schedulecreator);

        $users = [];
        if ($instance->requires_audience()) {
            $users = helper::get_schedule_report_users($schedule);
        }

        // Execute schedule type.
        $instance->execute($users, $this->get_trace());

        // Finish, clean up (set persistent property manually to avoid updating it's user/time modified data).
        $DB->set_field($schedule::TABLE, 'timelastsent', di::get(clock::class)->time(), [
            'id' => $schedule->get('id'),
        ]);

        $this->log_finish('Sending schedule complete');

        // Restore cron user to original state.
        \core\cron::setup_user($originaluser);
    }
}

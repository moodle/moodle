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
use core_reportbuilder\local\helpers\{report, schedule as helper};
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
        if ($schedule === false) {
            $this->log('Invalid schedule', 0);
            return;
        }

        $this->log_start('Sending schedule: ' . $schedule->get_formatted_name());

        $scheduleattachment = null;
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

        $users = helper::get_schedule_report_users($schedule);
        if (count($users) > 0) {

            $scheduleuserviewas = $schedule->get('userviewas');
            $schedulereportempty = $schedule->get('reportempty');

            // Handle schedule configuration as to who the report should be viewed as.
            if ($scheduleuserviewas === schedule::REPORT_VIEWAS_CREATOR) {
                $scheduleattachment = helper::get_schedule_report_file($schedule);
            } else if ($scheduleuserviewas !== schedule::REPORT_VIEWAS_RECIPIENT) {

                // Get the user to view the schedule report as, ensure it's an active account.
                try {
                    $scheduleviewas = core_user::get_user($scheduleuserviewas, '*', MUST_EXIST);
                    core_user::require_active_user($scheduleviewas);
                } catch (moodle_exception $exception) {
                    $this->log('Invalid schedule view as user: ' . $exception->getMessage(), 0);
                    return;
                }

                \core\cron::setup_user($scheduleviewas);
                $scheduleattachment = helper::get_schedule_report_file($schedule);
            }

            // Apply special handling if report is empty (default is to send it anyway).
            if ($schedulereportempty === schedule::REPORT_EMPTY_DONT_SEND && $scheduleattachment !== null &&
                    report::get_report_row_count($schedule->get('reportid')) === 0) {

                $this->log('Empty report, skipping');
            } else {

                // Now iterate over recipient users, send the report to each.
                foreach ($users as $user) {
                    $this->log('Sending to: ' . fullname($user, true));

                    // If we already created the attachment, send that. Otherwise generate per recipient.
                    if ($scheduleattachment !== null) {
                        helper::send_schedule_message($schedule, $user, $scheduleattachment);
                    } else {
                        \core\cron::setup_user($user);

                        if ($schedulereportempty === schedule::REPORT_EMPTY_DONT_SEND &&
                                report::get_report_row_count($schedule->get('reportid')) === 0) {

                            $this->log('Empty report, skipping', 2);
                            continue;
                        }

                        $recipientattachment = helper::get_schedule_report_file($schedule);
                        helper::send_schedule_message($schedule, $user, $recipientattachment);
                        $recipientattachment->delete();
                    }
                }
            }
        }

        // Finish, clean up (set persistent property manually to avoid updating it's user/time modified data).
        $DB->set_field($schedule::TABLE, 'timelastsent', di::get(clock::class)->time(), [
            'id' => $schedule->get('id'),
        ]);

        if ($scheduleattachment !== null) {
            $scheduleattachment->delete();
        }

        $this->log_finish('Sending schedule complete');

        // Restore cron user to original state.
        \core\cron::setup_user($originaluser);
    }
}

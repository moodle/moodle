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

use core_user;
use core\task\adhoc_task;
use core_reportbuilder\local\helpers\schedule as helper;
use core_reportbuilder\local\models\schedule;

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

        $originaluser = $USER;

        $scheduleuserviewas = $schedule->get('userviewas');
        $schedulereportempty = $schedule->get('reportempty');
        $scheduleattachment = null;

        $this->log_start('Sending schedule: ' . $schedule->get_formatted_name());

        // Handle schedule configuration as to who the report should be viewed as.
        if ($scheduleuserviewas === schedule::REPORT_VIEWAS_CREATOR) {
            cron_setup_user(core_user::get_user($schedule->get('usercreated')));
            $scheduleattachment = helper::get_schedule_report_file($schedule);
        } else if ($scheduleuserviewas !== schedule::REPORT_VIEWAS_RECIPIENT) {
            cron_setup_user(core_user::get_user($scheduleuserviewas));
            $scheduleattachment = helper::get_schedule_report_file($schedule);
        }

        // Apply special handling if report is empty (default is to send it anyway).
        if ($schedulereportempty === schedule::REPORT_EMPTY_DONT_SEND &&
                $scheduleattachment !== null && helper::get_schedule_report_count($schedule) === 0) {

            $this->log('Empty report, skipping');
            return;
        }

        $users = helper::get_schedule_report_users($schedule);
        foreach ($users as $user) {
            $this->log('Sending to: ' . fullname($user, true));

            // If we already created the attachment, send that. Otherwise generate per recipient.
            if ($scheduleattachment !== null) {
                helper::send_schedule_message($schedule, $user, $scheduleattachment);
            } else {
                cron_setup_user($user);

                if ($schedulereportempty === schedule::REPORT_EMPTY_DONT_SEND &&
                        helper::get_schedule_report_count($schedule) === 0) {

                    $this->log('Empty report, skipping', 2);
                    continue;
                }

                $recipientattachment = helper::get_schedule_report_file($schedule);
                helper::send_schedule_message($schedule, $user, $recipientattachment);
                $recipientattachment->delete();
            }
        }

        // Finish, clean up (set persistent property manually to avoid updating it's user/time modified data).
        $DB->set_field($schedule::TABLE, 'timelastsent', time(), ['id' => $schedule->get('id')]);

        if ($scheduleattachment !== null) {
            $scheduleattachment->delete();
        }

        $this->log_finish('Sending schedule complete');

        // Restore cron user to original state.
        cron_setup_user($originaluser);
    }
}

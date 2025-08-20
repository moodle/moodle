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

namespace core_reportbuilder\reportbuilder\schedule;

use core\exception\moodle_exception;
use core\user;
use core_reportbuilder\local\helpers\{report, schedule as helper};
use core_reportbuilder\local\models\schedule;
use core_reportbuilder\local\schedules\base;
use MoodleQuickForm;
use progress_trace;
use stdClass;
use stored_file;

/**
 * Message schedule class
 *
 * @package     core_reportbuilder
 * @copyright   2025 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class message extends base {
    /** @var int Send schedule with empty report */
    public const REPORT_EMPTY_SEND_EMPTY = 0;

    /** @var int Send schedule without report */
    public const REPORT_EMPTY_SEND_WITHOUT = 1;

    /** @var int Don't send schedule if report is empty */
    public const REPORT_EMPTY_DONT_SEND = 2;

    #[\Override]
    public function get_name(): string {
        return get_string('scheduleemail', 'core_reportbuilder');
    }

    #[\Override]
    public function get_description(): string {
        return get_string('scheduleemaildescription', 'core_reportbuilder');
    }

    #[\Override]
    public function definition(MoodleQuickForm $mform): void {
        // Message fields.
        $mform->addElement('header', 'headermessage', get_string('messagecontent', 'core_reportbuilder'));

        $mform->addElement('text', 'configdata[subject]', get_string('messagesubject', 'core_reportbuilder'));
        $mform->setType('configdata[subject]', PARAM_TEXT);
        $mform->addRule('configdata[subject]', null, 'required', null, 'client');
        $mform->addRule('configdata[subject]', get_string('maximumchars', '', 255), 'maxlength', 255);

        $mform->addElement('editor', 'configdata[message]', get_string('messagebody', 'core_reportbuilder'), null, [
            'autosave' => false,
        ]);
        $mform->setType('configdata[message]', PARAM_RAW);
        $mform->addRule('configdata[message]', null, 'required', null, 'client');

        // Advanced.
        $mform->addElement('header', 'headeradvanced', get_string('advanced'));

        $mform->addElement('select', 'configdata[reportempty]', get_string('scheduleempty', 'core_reportbuilder'), [
            static::REPORT_EMPTY_SEND_EMPTY => get_string('scheduleemptysendwithattachment', 'core_reportbuilder'),
            static::REPORT_EMPTY_SEND_WITHOUT => get_string('scheduleemptysendwithoutattachment', 'core_reportbuilder'),
            static::REPORT_EMPTY_DONT_SEND => get_string('scheduleemptydontsend', 'core_reportbuilder'),
        ]);
    }

    #[\Override]
    public function execute(array $users, progress_trace $trace): void {
        $scheduleattachment = null;

        if (count($users) > 0) {
            $schedule = $this->get_persistent();

            $scheduleuserviewas = $schedule->get('userviewas');
            $schedulereportempty = $this->get_configdata()['reportempty'] ?? static::REPORT_EMPTY_SEND_EMPTY;

            // Handle schedule configuration as to who the report should be viewed as.
            if ($scheduleuserviewas === schedule::REPORT_VIEWAS_CREATOR) {
                $scheduleattachment = helper::get_schedule_report_file($schedule);
            } else if ($scheduleuserviewas !== schedule::REPORT_VIEWAS_RECIPIENT) {
                // Get the user to view the schedule report as, ensure it's an active account.
                try {
                    $scheduleviewas = user::get_user($scheduleuserviewas, '*', MUST_EXIST);
                    user::require_active_user($scheduleviewas);
                } catch (moodle_exception $exception) {
                    $trace->output('Invalid schedule view as user: ' . $exception->getMessage());
                    return;
                }

                \core\cron::setup_user($scheduleviewas);
                $scheduleattachment = helper::get_schedule_report_file($schedule);
            }

            // Apply special handling if report is empty (default is to send it anyway).
            if (
                $schedulereportempty === static::REPORT_EMPTY_DONT_SEND &&
                $scheduleattachment !== null &&
                report::get_report_row_count($schedule->get('reportid')) === 0
            ) {
                $trace->output('Empty report, skipping', 1);
            } else {
                // Now iterate over recipient users, send the report to each.
                foreach ($users as $user) {
                    $trace->output('Sending to: ' . fullname($user, true), 1);

                    // If we already created the attachment, send that. Otherwise generate per recipient.
                    if ($scheduleattachment !== null) {
                        $this->send_message($user, $scheduleattachment);
                    } else {
                        \core\cron::setup_user($user);

                        if (
                            $schedulereportempty === static::REPORT_EMPTY_DONT_SEND &&
                            report::get_report_row_count($schedule->get('reportid')) === 0
                        ) {
                            $trace->output('Empty report, skipping', 2);
                            continue;
                        }

                        $recipientattachment = helper::get_schedule_report_file($schedule);
                        $this->send_message($user, $recipientattachment);
                        $recipientattachment->delete();
                    }
                }
            }
        }

        if ($scheduleattachment !== null) {
            $scheduleattachment->delete();
        }
    }

    /**
     * Send schedule message to user
     *
     * @param stdClass $user
     * @param stored_file $attachment
     * @return bool
     */
    private function send_message(stdClass $user, stored_file $attachment): bool {
        $config = $this->get_configdata();

        $message = new \core\message\message();
        $message->component = 'moodle';
        $message->name = 'reportbuilderschedule';
        $message->courseid = SITEID;
        $message->userfrom = user::get_noreply_user();
        $message->userto = $user;
        $message->subject = $config['subject'];
        $message->fullmessage = $config['message']['text'];
        $message->fullmessageformat = $config['message']['format'];
        $message->fullmessagehtml = $message->fullmessage;
        $message->smallmessage = $message->fullmessage;

        // Attach report to outgoing message.
        $message->attachment = $attachment;
        $message->attachname = $attachment->get_filename();

        return (bool) message_send($message);
    }
}

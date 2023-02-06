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

namespace core_reportbuilder\local\helpers;

use context_user;
use core_plugin_manager;
use core_user;
use invalid_parameter_exception;
use stdClass;
use stored_file;
use table_dataformat_export_format;
use core\message\message;
use core\plugininfo\dataformat;
use core_reportbuilder\local\models\audience as audience_model;
use core_reportbuilder\local\models\schedule as model;
use core_reportbuilder\table\custom_report_table_view;

/**
 * Helper class for report schedule related methods
 *
 * @package     core_reportbuilder
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class schedule {

    /**
     * Create report schedule, calculate when it should be next sent
     *
     * @param stdClass $data
     * @param int|null $timenow Time to use as comparison against current date (defaults to current time)
     * @return model
     */
    public static function create_schedule(stdClass $data, ?int $timenow = null): model {
        $data->name = trim($data->name);

        $schedule = (new model(0, $data));
        $schedule->set('timenextsend', self::calculate_next_send_time($schedule, $timenow));

        return $schedule->create();
    }

    /**
     * Update report schedule
     *
     * @param stdClass $data
     * @return model
     * @throws invalid_parameter_exception
     */
    public static function update_schedule(stdClass $data): model {
        $schedule = model::get_record(['id' => $data->id, 'reportid' => $data->reportid]);
        if ($schedule === false) {
            throw new invalid_parameter_exception('Invalid schedule');
        }

        // Normalize model properties.
        $data = array_intersect_key((array) $data, model::properties_definition());
        if (array_key_exists('name', $data)) {
            $data['name'] = trim($data['name']);
        }

        $schedule->set_many($data);
        $schedule->set('timenextsend', self::calculate_next_send_time($schedule))
            ->update();

        return $schedule;
    }

    /**
     * Toggle report schedule enabled
     *
     * @param int $reportid
     * @param int $scheduleid
     * @param bool $enabled
     * @return bool
     * @throws invalid_parameter_exception
     */
    public static function toggle_schedule(int $reportid, int $scheduleid, bool $enabled): bool {
        $schedule = model::get_record(['id' => $scheduleid, 'reportid' => $reportid]);
        if ($schedule === false) {
            throw new invalid_parameter_exception('Invalid schedule');
        }

        return $schedule->set('enabled', $enabled)->update();
    }

    /**
     * Return array of users who match the audience records added to the given schedule
     *
     * @param model $schedule
     * @return stdClass[]
     */
    public static function get_schedule_report_users(model $schedule): array {
        global $DB;

        $audienceids = (array) json_decode($schedule->get('audiences'));

        // Retrieve all selected audience records for the schedule.
        [$audienceselect, $audienceparams] = $DB->get_in_or_equal($audienceids, SQL_PARAMS_NAMED, 'aid', true, true);
        $audiences = audience_model::get_records_select("id {$audienceselect}", $audienceparams);
        if (count($audiences) === 0) {
            return [];
        }

        // Now convert audiences to SQL for user retrieval.
        [$wheres, $params] = audience::user_audience_sql($audiences);
        [$userorder] = users_order_by_sql('u');

        $sql = 'SELECT u.*
                  FROM {user} u
                 WHERE ' . implode(' OR ', $wheres) . '
              ORDER BY ' . $userorder;

        return $DB->get_records_sql($sql, $params);
    }

    /**
     * Return count of schedule report rows
     *
     * @param model $schedule
     * @return int
     */
    public static function get_schedule_report_count(model $schedule): int {
        global $DB;

        $table = custom_report_table_view::create($schedule->get('reportid'));
        $table->setup();

        return $DB->count_records_sql($table->countsql, $table->countparams);
    }

    /**
     * Generate stored file instance for given schedule, in user draft
     *
     * @param model $schedule
     * @return stored_file
     */
    public static function get_schedule_report_file(model $schedule): stored_file {
        global $CFG;
        require_once("{$CFG->libdir}/filelib.php");

        $table = custom_report_table_view::create($schedule->get('reportid'));

        $table->setup();
        $table->query_db(0, false);

        // Set up table as if it were being downloaded, retrieve appropriate export class (ensure output buffer is
        // cleaned in order to instantiate export class without exception).
        ob_start();
        $table->download = $schedule->get('format');
        $exportclass = new table_dataformat_export_format($table, $table->download);
        ob_end_clean();

        // Create our schedule report stored file.
        $context = context_user::instance($schedule->get('usercreated'));
        $filerecord = [
            'contextid' => $context->id,
            'component' => 'user',
            'filearea' => 'draft',
            'itemid' => file_get_unused_draft_itemid(),
            'filepath' => '/',
            'filename' => clean_filename($schedule->get_formatted_name()),
        ];

        $storedfile = \core\dataformat::write_data_to_filearea(
            $filerecord,
            $table->download,
            $exportclass->format_data($table->headers),
            $table->rawdata,
            static function(stdClass $record, bool $supportshtml) use ($table, $exportclass): array {
                $record = $table->format_row($record);
                if (!$supportshtml) {
                    $record = $exportclass->format_data($record);
                }
                return $record;
            }
        );

        $table->close_recordset();

        return $storedfile;
    }

    /**
     * Check whether given schedule needs to be sent
     *
     * @param model $schedule
     * @return bool
     */
    public static function should_send_schedule(model $schedule): bool {
        if (!$schedule->get('enabled')) {
            return false;
        }

        $timenow = time();

        // Ensure we've reached the initial scheduled start time.
        $timescheduled = $schedule->get('timescheduled');
        if ($timescheduled > $timenow) {
            return false;
        }

        // If there's no recurrence, check whether it's been sent since initial scheduled start time. This ensures that even if
        // the schedule was manually sent beforehand, it'll still be automatically sent once the start time is first reached.
        if ($schedule->get('recurrence') === model::RECURRENCE_NONE) {
            return $schedule->get('timelastsent') < $timescheduled;
        }

        return $schedule->get('timenextsend') <= $timenow;
    }

    /**
     * Calculate the next time a schedule should be sent, based on it's recurrence and when it was initially scheduled. Ensures
     * returned value is after the current date
     *
     * @param model $schedule
     * @param int|null $timenow Time to use as comparison against current date (defaults to current time)
     * @return int
     */
    public static function calculate_next_send_time(model $schedule, ?int $timenow = null): int {
        global $CFG;

        $timenow = $timenow ?? time();

        $recurrence = $schedule->get('recurrence');
        $timescheduled = $schedule->get('timescheduled');

        // If no recurrence is set or we haven't reached last sent date, return early.
        if ($recurrence === model::RECURRENCE_NONE || $timescheduled > $timenow) {
            return $timescheduled;
        }

        // Extract attributes from date (year, month, day, hours, minutes).
        [
            'year' => $year,
            'mon' => $month,
            'mday' => $day,
            'wday' => $dayofweek,
            'hours' => $hour,
            'minutes' => $minute,
        ] = usergetdate($timescheduled, $CFG->timezone);

        switch ($recurrence) {
            case model::RECURRENCE_DAILY:
                $day += 1;
            break;
            case model::RECURRENCE_WEEKDAYS:
                $day += 1;

                $calendar = \core_calendar\type_factory::get_calendar_instance();
                $weekend = get_config('core', 'calendar_weekend');

                // Increment day until day of week falls on a weekday.
                while ((bool) ($weekend & (1 << (++$dayofweek % $calendar->get_num_weekdays())))) {
                    $day++;
                }
            break;
            case model::RECURRENCE_WEEKLY:
                $day += 7;
            break;
            case model::RECURRENCE_MONTHLY:
                $month += 1;
            break;
            case model::RECURRENCE_ANNUALLY:
                $year += 1;
            break;
        }

        // We need to recursively increment the timestamp until we get one after the current time.
        $timestamp = make_timestamp($year, $month, $day, $hour, $minute, 0, $CFG->timezone);
        if ($timestamp < $timenow) {
            // Ensure we don't modify anything in the original model.
            $scheduleclone = new model(0, $schedule->to_record());

            return self::calculate_next_send_time(
                $scheduleclone->set('timescheduled', $timestamp), $timenow);
        } else {
            return $timestamp;
        }
    }

    /**
     * Send schedule message to user
     *
     * @param model $schedule
     * @param stdClass $user
     * @param stored_file $attachment
     * @return bool
     */
    public static function send_schedule_message(model $schedule, stdClass $user, stored_file $attachment): bool {
        $message = new message();
        $message->component = 'moodle';
        $message->name = 'reportbuilderschedule';
        $message->courseid = SITEID;
        $message->userfrom = core_user::get_noreply_user();
        $message->userto = $user;
        $message->subject = $schedule->get('subject');
        $message->fullmessage = $schedule->get('message');
        $message->fullmessageformat = $schedule->get('messageformat');
        $message->fullmessagehtml = $message->fullmessage;
        $message->smallmessage = $message->fullmessage;

        // Attach report to outgoing message.
        $message->attachment = $attachment;
        $message->attachname = $attachment->get_filename();

        return (bool) message_send($message);
    }

    /**
     * Delete report schedule
     *
     * @param int $reportid
     * @param int $scheduleid
     * @return bool
     * @throws invalid_parameter_exception
     */
    public static function delete_schedule(int $reportid, int $scheduleid): bool {
        $schedule = model::get_record(['id' => $scheduleid, 'reportid' => $reportid]);
        if ($schedule === false) {
            throw new invalid_parameter_exception('Invalid schedule');
        }

        return $schedule->delete();
    }

    /**
     * Return list of available data formats
     *
     * @return string[]
     */
    public static function get_format_options(): array {
        $dataformats = core_plugin_manager::instance()->get_plugins_of_type('dataformat');

        return array_map(static function(dataformat $dataformat): string {
            return $dataformat->displayname;
        }, $dataformats);
    }

    /**
     * Return list of available view as user options
     *
     * @return string[]
     */
    public static function get_viewas_options(): array {
        return [
            model::REPORT_VIEWAS_CREATOR => get_string('scheduleviewascreator', 'core_reportbuilder'),
            model::REPORT_VIEWAS_RECIPIENT => get_string('scheduleviewasrecipient', 'core_reportbuilder'),
            model::REPORT_VIEWAS_USER => get_string('userselect', 'core_reportbuilder'),
        ];
    }

    /**
     * Return list of recurrence options
     *
     * @return string[]
     */
    public static function get_recurrence_options(): array {
        return [
            model::RECURRENCE_NONE => get_string('none'),
            model::RECURRENCE_DAILY => get_string('recurrencedaily', 'core_reportbuilder'),
            model::RECURRENCE_WEEKDAYS => get_string('recurrenceweekdays', 'core_reportbuilder'),
            model::RECURRENCE_WEEKLY => get_string('recurrenceweekly', 'core_reportbuilder'),
            model::RECURRENCE_MONTHLY => get_string('recurrencemonthly', 'core_reportbuilder'),
            model::RECURRENCE_ANNUALLY => get_string('recurrenceannually', 'core_reportbuilder'),
        ];
    }

    /**
     * Return list of options for when report is empty
     *
     * @return string[]
     */
    public static function get_report_empty_options(): array {
        return [
            model::REPORT_EMPTY_SEND_EMPTY => get_string('scheduleemptysendwithattachment', 'core_reportbuilder'),
            model::REPORT_EMPTY_SEND_WITHOUT => get_string('scheduleemptysendwithoutattachment', 'core_reportbuilder'),
            model::REPORT_EMPTY_DONT_SEND => get_string('scheduleemptydontsend', 'core_reportbuilder'),
        ];
    }
}

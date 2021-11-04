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

use core_plugin_manager;
use invalid_parameter_exception;
use stdClass;
use core\plugininfo\dataformat;
use core_reportbuilder\local\models\schedule as model;

/**
 * Helper class for report schedule related methods
 *
 * @package     core_reportbuilder
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class schedule {

    /**
     * Create report schedule
     *
     * @param stdClass $data
     * @return model
     */
    public static function create_schedule(stdClass $data): model {
        $data->name = trim($data->name);

        // TODO: Calculate next send.

        return (new model(0, $data))->create();
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

        $schedule->set_many($data)->update();

        // TODO: Calculate next send.

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

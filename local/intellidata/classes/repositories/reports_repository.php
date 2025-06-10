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

/**
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 * @package    local_intellidata
 * @copyright  2021 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */

namespace local_intellidata\repositories;

/**
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 * @package    local_intellidata
 * @copyright  2021 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */
class reports_repository {

    /**
     * Update or create.
     *
     * @param $report
     * @return mixed
     * @throws \dml_exception
     */
    public static function update_or_create($report) {
        global $DB;

        $report = clone $report;
        $record = $DB->get_record('local_intellidata_reports', ['external_identifier' => $report->external_identifier]);

        if ($record) {
            $report->id = $record->id;
            $report->timecreated = $record->timecreated;
            $DB->update_record('local_intellidata_reports', $report);
        } else {
            $report->timecreated = time();
            $report->id = $DB->insert_record('local_intellidata_reports', $report);
        }

        return $report;
    }

    /**
     * Delete by external identifier.
     *
     * @param $externalidentifier
     * @throws \dml_exception
     */
    public static function delete_by_external_identifier($externalidentifier) {
        global $DB;

        $DB->delete_records('local_intellidata_reports', ['external_identifier' => $externalidentifier]);
    }

    /**
     * Get by external identifier.
     *
     * @param $externalidentifier
     * @return false|mixed|\stdClass
     * @throws \dml_exception
     */
    public static function get_by_external_identifier($externalidentifier) {
        global $DB;

        return $DB->get_record('local_intellidata_reports', ['external_identifier' => $externalidentifier]);
    }

    /**
     * Get by id.
     *
     * @param $id
     * @return false|mixed|\stdClass
     * @throws \dml_exception
     */
    public static function get_by_id($id) {
        global $DB;

        return $DB->get_record('local_intellidata_reports', ['id' => $id]);
    }
}

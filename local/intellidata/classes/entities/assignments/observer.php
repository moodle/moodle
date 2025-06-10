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
 * Observer
 *
 * @package    local_intellidata
 * @author     IntelliBoard
 * @copyright  2020 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_intellidata\entities\assignments;

use local_intellidata\helpers\DBManagerHelper;
use local_intellidata\helpers\TrackingHelper;
use local_intellidata\services\events_service;

/**
 * Event observer for transcripts.
 */
class observer {

    /**
     * Triggered when 'submission_created' event is triggered.
     *
     * @param \mod_assign\event\submission_created $event
     */
    public static function submission_created(\mod_assign\event\submission_created $event) {
        global $DB;

        if (TrackingHelper::eventstracking_enabled()) {
            $eventdata = $event->get_data();
            $submission = $DB->get_record('assign_submission', ['id' => $eventdata['other']['submissionid']]);
            $submission->submission_type = str_replace('assignsubmission_', '', $eventdata['objecttable']);

            self::export_event($eventdata, $submission);
        }
    }

    /**
     * Triggered when 'submission_updated' event is triggered.
     *
     * @param \mod_assign\event\submission_updated $event
     */
    public static function submission_updated(\mod_assign\event\submission_updated $event) {
        global $DB;

        if (TrackingHelper::eventstracking_enabled()) {
            $eventdata = $event->get_data();

            $submission = $DB->get_record('assign_submission', ['id' => $eventdata['other']['submissionid']]);
            $submission->submission_type = str_replace('assignsubmission_', '', $eventdata['objecttable']);

            self::export_event($eventdata, $submission);
        }
    }

    /**
     * Triggered when 'submission_status_viewed' event is triggered.
     *
     * @param \mod_assign\event\submission_status_viewed $event
     */
    public static function submission_status_viewed(\mod_assign\event\submission_status_viewed $event) {
        global $DB;

        if (TrackingHelper::eventstracking_enabled()) {
            $eventdata = $event->get_data();
            $condition = [
                'userid' => $eventdata['userid'],
                'assignid' => $eventdata['other']['assignid'],
                'timecreated' => $eventdata['timecreated'],
            ];

            $submissionssql = "SELECT *
                    FROM {assign_submission}
                    WHERE assignment=:assignid AND userid=:userid AND timecreated=:timecreated
                    ORDER BY attemptnumber DESC";

            $submission = $DB->get_record_sql($submissionssql, $condition);
            if ($submission) {
                $submission->submission_type = self::get_submission_type($submission->id);
                self::export_event($eventdata, $submission);
            }
        }
    }

    /**
     * Triggered when 'submission_duplicated' event is triggered.
     *
     * @param \mod_assign\event\submission_duplicated $event
     */
    public static function submission_duplicated(\mod_assign\event\submission_duplicated $event) {

        if (TrackingHelper::eventstracking_enabled()) {
            $eventdata = $event->get_data();

            $submission = $event->get_record_snapshot($eventdata['objecttable'], $eventdata['objectid']);
            $submission->submission_type = self::get_submission_type($submission->id);

            self::export_event($eventdata, $submission);
        }
    }

    /**
     * Triggered when 'assessable_submitted' event is triggered.
     *
     * @param \mod_assign\event\assessable_submitted $event
     */
    public static function assessable_submitted(\mod_assign\event\assessable_submitted $event) {
        if (TrackingHelper::eventstracking_enabled()) {
            $eventdata = $event->get_data();

            $submission = $event->get_record_snapshot($eventdata['objecttable'], $eventdata['objectid']);
            $submission->submission_type = self::get_submission_type($submission->id);

            self::export_event($eventdata, $submission);
        }
    }

    /**
     * Triggered when 'submission_graded' event is triggered.
     *
     * @param \mod_assign\event\submission_graded $event
     */
    public static function submission_graded(\mod_assign\event\submission_graded $event) {
        global $DB;

        if (TrackingHelper::eventstracking_enabled()) {

            $eventdata = $event->get_data();
            $gradedata = $event->get_record_snapshot($eventdata['objecttable'], $eventdata['objectid']);
            $submission = $DB->get_record('assign_submission', [
                'assignment' => $gradedata->assignment,
                'userid' => $gradedata->userid,
                'attemptnumber' => $gradedata->attemptnumber,
            ]);

            if ($submission) {
                $submission->grade = ((float) $gradedata->grade > 0) ? $gradedata->grade : 0;
                $submission->feedback_at = $gradedata->timemodified;
                $submission->feedback_by = $gradedata->grader;
                $submission->submission_type = self::get_submission_type($submission->id);

                $feedback = $DB->get_record('assignfeedback_comments', [
                    'assignment' => $gradedata->assignment,
                    'grade' => $gradedata->id,
                ]);
                if (!empty($feedback->commenttext)) {
                    $submission->feedback = $feedback->commenttext;
                }

                self::export_event($eventdata, $submission);
            }
        }
    }

    /**
     * Triggered when 'submission_status_updated' event is triggered.
     *
     * @param \mod_assign\event\submission_status_updated $event
     */
    public static function submission_status_updated(\mod_assign\event\submission_status_updated $event) {
        global $DB;

        if (TrackingHelper::eventstracking_enabled()) {

            $eventdata = $event->get_data();
            $submission = $event->get_record_snapshot($eventdata['objecttable'], $eventdata['objectid']);
            $submission = submission::prepare_export_data($submission);

            self::export_event($eventdata, $submission);
        }
    }

    /**
     * Export data event.
     *
     * @param $eventdata
     * @param $submission
     * @param array $fields
     * @throws \core\invalid_persistent_exception
     */
    private static function export_event($eventdata, $submission, $fields = []) {
        $entity = new submission($submission, $fields);
        $data = $entity->export();
        $data->crud = $eventdata['crud'];

        $tracking = new events_service($entity::TYPE);
        $tracking->track($data);
    }

    /**
     * Get submission type.
     *
     * @param $submissionid
     * @return string
     * @throws \dml_exception
     */
    public static function get_submission_type($submissionid) {
        global $DB;

        $xmltables = DBManagerHelper::get_install_xml_tables();

        $select = $join = [];
        foreach ($xmltables as $xmltable) {
            if ($xmltable['plugintype'] === 'assignsubmission') {
                $select[] = "CASE WHEN MAX({$xmltable['name']}.id) IS NOT NULL THEN '{$xmltable['plugin']}' ELSE '' END";
                $join[] = "LEFT JOIN {{$xmltable['name']}} {$xmltable['name']} on {$xmltable['name']}.submission=s.id";
            }
        }

        if (!empty($select)) {
            $select = implode(",',',", $select);
            $join = implode(' ', $join);
            $innerwhere = " WHERE s.id=:submissionid ";
            $condition['submissionid'] = $submissionid;

            $submissionssql = "SELECT
                        s.id AS submission_id,
                        CONCAT($select, '') AS submission_type
                    FROM {assign_submission} s
                         $join
                    $innerwhere
                    GROUP BY s.id";

            $record = $DB->get_record_sql($submissionssql, $condition);

            return (isset($record->submission_type)) ? $record->submission_type : '';
        }

        return '';
    }
}

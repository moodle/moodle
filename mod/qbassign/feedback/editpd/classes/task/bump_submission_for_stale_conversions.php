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
 * Bump submission timemodified for conversions that are stale.
 *
 * @package    qbassignfeedback_editpd
 * @copyright  2022 Catalyst IT Australia Pty Ltd
 * @author     Cameron Ball <cameronball@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace qbassignfeedback_editpd\task;

use core\task\adhoc_task;

/**
 * Adhoc task to bump the submission timemodified associated with a stale conversion.
 *
 * @package    qbassignfeedback_editpd
 * @copyright  2022 Catalyst IT Australia Pty Ltd
 * @author     Cameron Ball <cameronball@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class bump_submission_for_stale_conversions extends adhoc_task {

    /**
     * Run the task.
     */
    public function execute() {
        global $DB;

        // Used to only get records after whenever document conversion was enabled for this site.
        $earliestconversion = $DB->get_record_sql("SELECT MIN(timecreated) AS min
                                                     FROM {files}
                                                    WHERE filearea = 'documentconversion'");

        if (isset($earliestconversion->min)) {
            ['sql' => $extensionsql, 'params' => $extensionparams] = array_reduce(
                ['doc', 'docx', 'rtf', 'xls', 'xlsx', 'ppt', 'pptx', 'html', 'odt', 'ods', 'png', 'jpg', 'txt', 'gif'],
                function(array $c, string $ext) use ($DB): array {
                    return [
                        'sql' => $c['sql'] . ($c['sql'] ? ' OR ' : '') . $DB->sql_like('f1.filename', ':' . $ext),
                        'params' => $c['params'] + [$ext => '%.' . $ext]
                    ];
                },
                ['sql' => '', 'params' => []]
            );

            // A converted file has its filename set to the contenthash of the file it converted.
            // Find all files in the relevant file areas for which there is no corresponding
            // file with the contenthash as the file name.
            //
            // Also check if the file has a greater modified time than the submission, if it does
            // that means it is both stale (as per the above) and will never be reconverted.
            $sql = "SELECT f3.id, f3.timemodified as fmodified, asu.id as submissionid
                      FROM {files} f1
                 LEFT JOIN {files} f2 ON f1.contenthash = f2.filename
                           AND f2.component = 'core' AND f2.filearea = 'documentconversion'
                      JOIN {qbassign_submission} asu ON asu.id = f1.itemid
                      JOIN {qbassign_grades} asg ON asg.userid = asu.userid AND asg.qbassignment = asu.qbassignment
                      JOIN {files} f3 ON f3.itemid = asg.id
                     WHERE f1.filearea = 'submission_files'
                           AND f3.timecreated >= :earliest
                           AND ($extensionsql)
                           AND f2.filename IS NULL
                           AND f3.component = 'qbassignfeedback_editpd'
                           AND f3.filearea = 'combined'
                           AND f3.filename = 'combined.pdf'
                           AND f3.timemodified >= asu.timemodified";

            $submissionstobump = $DB->get_records_sql($sql, ['earliest' => $earliestconversion->min] + $extensionparams);
            foreach ($submissionstobump as $submission) {

                // Set the submission modified time to one second later than the
                // converted files modified time, this will cause qbassign to reconvert
                // everything and delete the old files when the qbassignment grader is
                // viewed. See get_page_images_for_attempt in document_services.php.
                $newmodified = $submission->fmodified + 1;
                $record = (object)[
                    'id' => $submission->submissionid,
                    'timemodified' => $newmodified
                ];

                mtrace('Set submission ' . $submission->submissionid . ' timemodified to ' . $newmodified);
                $DB->update_record('qbassign_submission', $record);
            }
        }
    }
}

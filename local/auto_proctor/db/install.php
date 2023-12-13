<?php
// This file is part of Moodle Course Rollover Plugin
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
 * @package     local_auto_proctor
 * @author      Angelica
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @var stdClass $plugin
*/

defined('MOODLE_INTERNAL') || die();

function xmldb_local_auto_proctor_install() {
    global $DB;

    // Target tables
    $sourceTable = 'quiz';
    $targetTable = 'auto_proctor_quiz_tb';

    // Get all quiz IDs and course IDs from the source table
    $quizRecords = $DB->get_records_sql("SELECT id, course FROM {" . $sourceTable . "}");

    // Insert each quiz ID and course ID into auto_proctor_quiz_tb
    if (!empty($quizRecords)) {
        foreach ($quizRecords as $quizRecord) {
            $DB->insert_record($targetTable, ['quizid' => $quizRecord->id, 'course' => $quizRecord->course]);
        }
    }

    return true;
}
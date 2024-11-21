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
 * @package    local_intelliboard
 * @copyright  2020 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @website    http://intelliboard.net/
 */

namespace local_intelliboard\task;

use local_intelliboard\transcripts\transcripts_processor;

/**
 * Task to sync data with attendance
 *
 * @copyright  2020 Intelliboard
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class transcripts_process extends \core\task\scheduled_task {
    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     * @throws \coding_exception
     */
    public function get_name() {
        return get_string('synctranscriptstask', 'local_intelliboard');
    }

    /**
     * Do the job.
     * Throw exceptions on errors (the job will be retried).
     * @return bool
     * @throws \dml_exception
     * @throws \moodle_exception
     * @throws \Exception
     */
    public function execute() {

        $params = ['start' => 0];

        $transcriptssyncrecordsnum = (int)get_config('local_intelliboard', 'transcriptssyncrecordsnum');
        $params['limit'] = ($transcriptssyncrecordsnum) ? $transcriptssyncrecordsnum : 1000;

        $lasttranscriptsrecordid = get_config('local_intelliboard', 'lasttranscriptsrecordid');
        $params['ueid'] = ($lasttranscriptsrecordid) ? $lasttranscriptsrecordid : 0;

        mtrace("IntelliBoards Transcripts Sync CRON started!");

        transcripts_processor::process($params);

        mtrace("IntelliBoards Transcripts Sync CRON completed!");

        return true;
    }
}

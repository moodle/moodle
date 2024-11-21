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
 * @copyright  2019 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @website    http://intelliboard.net/
 */

namespace local_intelliboard\task;

/**
 * Task to process new created BBB meetings.
 *
 * @copyright  2018 Intelliboard
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class check_active_bbb_meetings extends \core\task\scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('check_active_meetings', 'local_intelliboard');
    }

    /**
     * Do the job.
     * Throw exceptions on errors (the job will be retried).
     */
    public function execute() {
        global $DB;

        if(!get_config('local_intelliboard', 'enablebbbmeetings')) {
            return false;
        }

        // delete broken logs
        $DB->execute("DELETE FROM {local_intelliboard_bbb_meet} WHERE meetingid = null OR meetingid = ''");

        $bbb = new \local_intelliboard\bbb_client();
        $bbbmeetings = new \local_intelliboard\bbb_meetings();

        $activemeetings = $bbb->getActiveMeetings();
        /**
        * IDs of active Moodle meetings
        * Also meeting can be created from BBB interface.
        * We need only meetings, which created from Moodle
        */
        $listofactivemeetingsids = [];

        /** Check active meetings */
        foreach($activemeetings as $meeting) {
            $transaction = $DB->start_delegated_transaction();

            // meeting ID without course id and cmid
            $puremeetingid = explode('-', $meeting->meetingID->__toString())[0];

            // Skip if meeting not created from Moodle system
            if(!$DB->record_exists('bigbluebuttonbn', ['meetingid' => $puremeetingid])) {
                $DB->commit_delegated_transaction($transaction);
                continue;
            }

            $listofactivemeetingsids[] = $meeting->meetingID->__toString();

            $meetinginfo = $bbb->getMeetingInfo($meeting->meetingID->__toString());

            $bbbmeetings->check_meeting($meetinginfo);

            $DB->commit_delegated_transaction($transaction);
        }

        /** Check stopped meetings */
        $bbbmeetings->check_stopped_meetings($listofactivemeetingsids);
    }

}
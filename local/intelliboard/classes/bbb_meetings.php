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

namespace local_intelliboard;

class bbb_meetings {
    /**
     * @param string $meetingid
     * @param int $localmeetingid
     * @return array
     */
    public function get_meeting_details($meetingid, $localmeetingid) {
        global $DB;

        // get meeting name
        $sql = 'SELECT meetingname
                  FROM {local_intelliboard_bbb_meet}
                 WHERE meetingid LIKE ? AND id = ?';
        $meeting = $DB->get_record_sql($sql, [$meetingid, $localmeetingid]);

        $formattedmeeting = [
            'meetingname' => $meeting->meetingname,
            'meetingusers' => []
        ];

        // get meeting attendees
        $sql = 'SELECT *
                  FROM {local_intelliboard_bbb_atten}
                 WHERE meetingid LIKE ? AND localmeetingid = ?';
        $attendees = $DB->get_records_sql($sql, [$meetingid, $localmeetingid]);

        foreach($attendees as $attendee) {
            $formattedmeeting['meetingusers'][] = [
                'userid' => $attendee->userid,
                'fullname' => $attendee->fullname,
                'role' => $attendee->role,
                'ispresenter' => $attendee->ispresenter == 'true' ? get_string('yes') : get_string('no'),
                'islisteningonly' => $attendee->islisteningonly == 'true' ? get_string('yes') : get_string('no'),
                'hasjoinedvoice' => $attendee->hasjoinedvoice == 'true' ? get_string('yes') : get_string('no'),
                'hasvideo' => $attendee->hasvideo == 'true' ? get_string('yes') : get_string('no'),
                'arrivaltime' => $attendee->arrivaltime,
                'departuretime' => $attendee->departuretime,
            ];
        }

        return $formattedmeeting;
    }

    /**
     * Create meeting data (row in table local_intelliboard_bbb_meet)
     * @param $meetingdata
     * @return bool|int
     * @throws \dml_exception
     */
    public function create_meeting_log($meetingdata) {
        global $DB;

        $meetingid = $meetingdata->meetingID->__toString();

        if (empty($meetingid)) {
            return false;
        }

        $courseid = explode('-', $meetingid)[1];
        $activityonstanceid = explode('-', $meetingid)[2];

        $sql = 'SELECT userid
                  FROM {bigbluebuttonbn_logs}
                 WHERE meetingid = :meetingid AND
                       log = :logcreate 
              ORDER BY id
                  DESC LIMIT 1';
        $meetingslogs = $DB->get_records_sql(
            $sql,
            [
                'meetingid' => $meetingid,
                'logcreate' => 'Create',
            ]
        );

        $ownerid = array_shift($meetingslogs);

        if(isset($ownerid)) {
            $ownerid = $ownerid->userid;
        }

        $dataObj = new \stdClass();
        $dataObj->meetingname = $meetingdata->meetingName->__toString();
        $dataObj->meetingid = $meetingid;
        $dataObj->internalmeetingid = $meetingdata->internalMeetingID->__toString();
        $dataObj->createtime = (int) $meetingdata->createTime->__toString();
        $dataObj->createdate = $meetingdata->createDate->__toString();
        $dataObj->voicebridge = $meetingdata->voiceBridge->__toString();
        $dataObj->dialnumber = $meetingdata->dialNumber->__toString();
        $dataObj->attendeepw = $meetingdata->attendeePW->__toString();
        $dataObj->moderatorpw = $meetingdata->moderatorPW->__toString();
        $dataObj->running = $meetingdata->running->__toString();
        $dataObj->duration = $meetingdata->duration->__toString();
        $dataObj->hasuserjoined = $meetingdata->hasUserJoined->__toString();
        $dataObj->recording = $meetingdata->recording->__toString();
        $dataObj->hasbeenforciblyended = $meetingdata->hasBeenForciblyEnded->__toString();
        $dataObj->starttime = time();
        $dataObj->endtime = null;
        $dataObj->participantcount = (int) $meetingdata->participantCount->__toString();
        $dataObj->listenercount = (int) $meetingdata->listenerCount->__toString();
        $dataObj->voiceparticipantcount = (int) $meetingdata->voiceParticipantCount->__toString();
        $dataObj->videocount = (int) $meetingdata->videoCount->__toString();
        $dataObj->maxusers = (int) $meetingdata->maxUsers->__toString();
        $dataObj->moderatorcount = (int) $meetingdata->moderatorCount->__toString();
        $dataObj->ownerid = $ownerid;

        $cmid = $DB->get_record_sql(
            "SELECT cm.*
               FROM {course_modules} cm
               JOIN {modules} m ON m.id = cm.module AND m.name = 'bigbluebuttonbn'
              WHERE cm.course = :course AND cm.instance = :instance", [
                'course' => $courseid,
                'instance' => $activityonstanceid,
            ]
        );

        $dataObj->courseid = $courseid;
        $dataObj->bigbluebuttonbnid = $activityonstanceid;
        $dataObj->cmid = $cmid ? $cmid->id : null;

        $res = $DB->insert_record('local_intelliboard_bbb_meet', $dataObj);

        echo 'creating log for ' . $meetingdata->meetingID->__toString();
        echo $res ? " - done\n" : " - error\n";

        return $res;
    }

    /**
     * Update meeting data (row in table local_intelliboard_bbb_meet)
     * @param $meeting
     * @param $oldlog
     * @return bool
     */
    public function update_meeting_log($meeting, $oldlog) {
        global $DB;

        $participantcount = (int) $meeting->participantCount->__toString() > $oldlog->participantcount ?
            (int) $meeting->participantCount->__toString() :
            $oldlog->participantcount;

        $listenercount = (int) $meeting->listenerCount->__toString() > $oldlog->listenercount ?
            (int) $meeting->listenerCount->__toString() :
            $oldlog->listenercount;

        $voiceparticipantcount = (int) $meeting->voiceParticipantCount->__toString() > $oldlog->voiceparticipantcount ?
            (int) $meeting->voiceParticipantCount->__toString() :
            $oldlog->voiceparticipantcount;

        $videocount = (int) $meeting->videoCount->__toString() > $oldlog->videocount ?
            (int) $meeting->videoCount->__toString() :
            $oldlog->videocount;

        $moderatorcount = (int) $meeting->moderatorCount->__toString() > $oldlog->moderatorcount ?
            (int) $meeting->moderatorCount->__toString() :
            $oldlog->moderatorcount;

        $dataObj = new \stdClass();
        $dataObj->id = $oldlog->id;
        $dataObj->meetingname = $meeting->meetingName->__toString();
        $dataObj->voicebridge = $meeting->voiceBridge->__toString();
        $dataObj->dialnumber = $meeting->dialNumber->__toString();
        $dataObj->attendeepw = $meeting->attendeePW->__toString();
        $dataObj->moderatorpw = $meeting->moderatorPW->__toString();
        $dataObj->running = $meeting->running->__toString();
        $dataObj->duration = $meeting->duration->__toString();
        $dataObj->hasuserjoined = $meeting->hasUserJoined->__toString();
        $dataObj->recording = $meeting->recording->__toString();
        $dataObj->hasbeenforciblyended = $meeting->hasBeenForciblyEnded->__toString();
        $dataObj->participantcount = $participantcount;
        $dataObj->listenercount = $listenercount;
        $dataObj->voiceparticipantcount = $voiceparticipantcount;
        $dataObj->videocount = $videocount;
        $dataObj->maxusers = (int) $meeting->maxUsers->__toString();
        $dataObj->moderatorcount = $moderatorcount;

        $res = $DB->update_record('local_intelliboard_bbb_meet', $dataObj);

        echo 'updating log for ' . $meeting->meetingID->__toString();

        echo $res ? " - done\n" : " - error\n";

        return $res;
    }

    /**
     * Create or update data for attendees and set 'endtime' for non active attendees
     *
     * @param \SimpleXMLElement $attendees
     * @param string $meetingid
     * @param $localmeetingid
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function check_attendees($attendees, $meetingid, $localmeetingid) {
        global $DB;

        $activeusersids = [];

        try {
            $transaction = $DB->start_delegated_transaction();

            foreach($attendees->attendee as $attendee) {
                $userexists = $DB->record_exists('user', ["id" => $attendee->userID->__toString()]);

                if (!$userexists) {
                    continue;
                }

                $activeusersids[] = $attendee->userID->__toString();

                $attendeeexists = $DB->get_record_sql(
                    'SELECT id
                   FROM {local_intelliboard_bbb_atten}
                  WHERE userid = ? AND meetingid LIKE ? AND localmeetingid = ?',
                    [$attendee->userID->__toString(), $meetingid, $localmeetingid]
                );

                /** Create or update attendee data */
                if(!$attendeeexists) {
                    $attendeeObj = new \stdClass();
                    $attendeeObj->userid = $attendee->userID->__toString();
                    $attendeeObj->fullname = $attendee->fullName->__toString();
                    $attendeeObj->role = $attendee->role->__toString();
                    $attendeeObj->ispresenter = $attendee->isPresenter->__toString();
                    $attendeeObj->islisteningonly = $attendee->isListeningOnly->__toString();
                    $attendeeObj->hasjoinedvoice = $attendee->hasJoinedVoice->__toString();
                    $attendeeObj->hasvideo = $attendee->hasVideo->__toString();
                    $attendeeObj->meetingid = $meetingid;
                    $attendeeObj->localmeetingid = $localmeetingid;
                    $attendeeObj->arrivaltime = time();

                    $DB->insert_record('local_intelliboard_bbb_atten', $attendeeObj);
                } else {
                    $attendeeObj = new \stdClass();
                    $attendeeObj->id = $attendeeexists->id;
                    $attendeeObj->localmeetingid = $localmeetingid;

                    if($attendee->hasJoinedVoice->__toString() == 'true') {
                        $attendeeObj->hasjoinedvoice = $attendee->hasJoinedVoice->__toString();
                        $attendeeObj->islisteningonly = 'false';
                    }

                    if($attendee->hasVideo->__toString() == 'true') {
                        $attendeeObj->hasvideo = $attendee->hasVideo->__toString();
                        $attendeeObj->islisteningonly = 'false';
                    }
                    $DB->update_record('local_intelliboard_bbb_atten', $attendeeObj);
                }
            }

            if($activeusersids) {
                $activeusersin = $DB->get_in_or_equal(
                    $activeusersids, SQL_PARAMS_QM, 'param', false
                );
                $where = ' AND userid ' . $activeusersin[0];
                $sqlargs = array_merge([$localmeetingid], $activeusersin[1]);
            } else {
                $where = '';
                $sqlargs = [$localmeetingid];
            }

            // get non active users
            $sql = "SELECT *
                  FROM {local_intelliboard_bbb_atten}
                 WHERE departuretime IS NULL AND
                       localmeetingid = ? {$where}";
            $nonactiveusers = $DB->get_records_sql(
                $sql,
                $sqlargs
            );

            // set departure time for non active users
            foreach($nonactiveusers as $user) {
                $dataObj = new \stdClass();
                $dataObj->id = $user->id;
                $dataObj->departuretime = time();

                $DB->update_record('local_intelliboard_bbb_atten', $dataObj);
            }

            // Assuming the both inserts work, we get to the following line.
            $transaction->allow_commit();

        } catch(\Exception $e) {
            $transaction->rollback($e);
        }

        echo 'checking attendees of ' . $meetingid . " - done\n";
    }

    /**
     * Check active meetings (create or update log, check attendees)
     * @param \SimpleXMLElement $meeting
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function check_meeting($meeting) {
        global $DB;

        $meetingid = $meeting->meetingID->__toString();
        $sql = 'SELECT *
                  FROM {local_intelliboard_bbb_meet}
                 WHERE meetingid LIKE ? AND
                       endtime IS NULL';

        $oldlog = $DB->get_record_sql($sql, [$meetingid]);

        /** Create or update log */
        if(!$oldlog) {
            $localmeetingid = $this->create_meeting_log($meeting);

            // check attendees
            $this->check_attendees($meeting->attendees, $meetingid, $localmeetingid);
        } else {
            $this->update_meeting_log($meeting, $oldlog);

            // check attendees
            $this->check_attendees($meeting->attendees, $meetingid, $oldlog->id);
        }
    }

    /**
     * Set end time for non active meetings
     * @param array $activemeetings
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function check_stopped_meetings($activemeetings) {
        global $DB;

        if($activemeetings) {
            $notin = $DB->get_in_or_equal($activemeetings, SQL_PARAMS_QM, 'param', false);
            $where = ' AND meetingid ' . $notin[0];
            $sqlargs = $notin[1];
        } else {
            $where = '';
            $sqlargs = [];
        }

        $sql = "SELECT id, meetingid
                  FROM {local_intelliboard_bbb_meet}
                 WHERE endtime IS NULL {$where}";

        try {
            $transaction = $DB->start_delegated_transaction();

            // stopped meetings - meetings, which do not in list of active meetings and
            // they does not have value for field 'endtime'
            $stoppedmeetings = $DB->get_records_sql($sql, $sqlargs);

            // set value for field 'endtime' of stopped meetings
            foreach($stoppedmeetings as $meeting) {
                $meetingobj = new \stdClass();
                $meetingobj->id = $meeting->id;
                $meetingobj->running = 'false';
                $meetingobj->endtime = time();

                $res = $DB->update_record('local_intelliboard_bbb_meet', $meetingobj);

                $this->set_departuretime_for_users($meeting->id);

                echo 'was set \'endtime\' for meeting ' . $meeting->meetingid;
                echo $res ? " - done\n" : " - error\n";
            }

            $transaction->allow_commit();

        } catch(\Exception $e) {
            $transaction->rollback($e);
        }
    }

    /**
     * Set departure time for course users
     * @param int $localmeetingid
     * @return bool
     * @throws \dml_exception
     */
    public function set_departuretime_for_users($localmeetingid) {
        global $DB;

        $sql = 'UPDATE {local_intelliboard_bbb_atten} SET departuretime = ? WHERE localmeetingid = ?';
        $updated = $DB->execute($sql, [time(), $localmeetingid]);

        return $updated;
    }
}


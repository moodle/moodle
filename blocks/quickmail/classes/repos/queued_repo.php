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
 * @package    block_quickmail
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_quickmail\repos;

defined('MOODLE_INTERNAL') || die();

use block_quickmail\repos\repo;
use block_quickmail\repos\interfaces\queued_repo_interface;
use block_quickmail\persistents\message;

class queued_repo extends repo implements queued_repo_interface {

    public $defaultsort = 'created';

    public $defaultdir = 'desc';

    public $sortableattrs = [
        'id' => 'id',
        'course' => 'course_id',
        'subject' => 'subject',
        'created' => 'timecreated',
        'scheduled' => 'to_send_at',
    ];

    /**
     * Fetches a queued message by id, or returns null
     *
     * @param  int  $messageid
     * @return message|null
     */
    public static function find_or_null($messageid) {
        // First, try to find the message by id, returning null by default.
        if (!$message = message::find_or_null($messageid)) {
            return null;
        }

        // If this message is NOT a queued message, return null.
        if (!$message->is_queued_message()) {
            return null;
        }

        return $message;
    }

    /**
     * Fetches a message by id which must belong to the given user id, or returns null
     *
     * @param  integer $messageid
     * @param  integer $userid
     * @return message|null
     */
    public static function find_for_user_or_null($messageid = 0, $userid = 0) {
        // First, try to find the message by id, returning null by default.
        if (!$message = self::find_or_null($messageid)) {
            return null;
        }

        // If this message does not belong to this user, return null.
        if (!$message->is_owned_by_user($userid)) {
            return null;
        }

        return $message;
    }

    /**
     * Fetches a message by id which must belong to the given user id, or returns null
     *
     * @param  integer $messageid
     * @param  integer $userid
     * @param  integer $courseid
     * @return message|null
     */
    public static function find_for_user_course_or_null($messageid = 0, $userid = 0, $courseid = 0) {
        // First, try to find the message by id, returning null by default.
        if (!$message = self::find_for_user_or_null($messageid, $userid)) {
            return null;
        }

        // If this message does not belong to this course, return null.
        if (!$message->is_owned_by_course($courseid)) {
            return null;
        }

        return $message;
    }

    /**
     * Returns all queued messages belonging to the given user id
     *
     * Optionally, can be scoped to a specific course if given a course_id
     *
     * @param  int     $userid
     * @param  int     $courseid   optional, defaults to 0 (all)
     * @param  array   $params  sort|dir|paginate|page|per_page|uri
     * @return array
     */
    public static function get_for_user($userid, $courseid = 0, $params = []) {
        // Instantiate repo.
        $repo = new self($params);
        $sortby = $repo->get_sort_column_name($repo->sort);
        $sortdir = strtoupper($repo->dir);

        // Set params for db query.
        $queryparams = [
            'user_id' => $userid,
            'is_draft' => 0,
        ];

        // Conditionally add course id to db query params if appropriate.
        if ($courseid) {
            $queryparams['course_id'] = $courseid;
        }

        global $DB;

        // If not paginating, return all sorted results.
        if (!$repo->paginate) {
            // Get SQL given params.
            $sql = self::get_for_user_sql($courseid, $sortby, $sortdir, false);

            // Pull data, iterate through recordset, instantiate persistents, add to array.
            $data = [];
            $recordset = $DB->get_recordset_sql($sql, $queryparams);
            foreach ($recordset as $record) {
                $data[] = new message(0, $record);
            }
            $recordset->close();
        } else {
            // Get (count) SQL given params.
            $sql = self::get_for_user_sql($courseid, $sortby, $sortdir, true);

            // Pull count.
            $count = $DB->count_records_sql($sql, $queryparams);

            // Get the calculated pagination parameters object.
            $paginated = $repo->get_paginated($count);

            // Set the pagination object on the result.
            $repo->set_result_pagination($paginated);

            // Get SQL given params.
            $sql = self::get_for_user_sql($courseid, $sortby, $sortdir, false);

            // Pull data, iterate through recordset, instantiate persistents, add to array.
            $data = [];
            $recordset = $DB->get_recordset_sql($sql, $queryparams, $paginated->offset, $paginated->per_page);
            foreach ($recordset as $record) {
                $data[] = new message(0, $record);
            }
            $recordset->close();
        }

        $repo->set_result_data($data);

        return $repo->result;
    }

    private static function get_for_user_sql($courseid, $sortby, $sortdir, $ascount = false) {
        $sql = $ascount
            ? 'SELECT COUNT(DISTINCT m.id) '
            : 'SELECT DISTINCT m.* ';

        $sql .= 'FROM {block_quickmail_messages} m
                 WHERE m.user_id = :user_id';

        if ($courseid) {
            $sql .= ' AND m.course_id = :course_id';
        }

        $sql .= ' AND m.to_send_at <> 0
                  AND m.timedeleted = 0
                  AND m.sent_at = 0
                  AND m.is_draft = 0';

        if (!$ascount) {
            $sql .= ' ORDER BY ' . $sortby . ' ' . $sortdir;
        }

        return $sql;
    }

    /**
     * Call this to inject into msg recipients table for courses that have messages as ALL from the mdl_block_quickmail_msg_course table.
     *
     * @return array
     */
    public static function sync_course_recip_msgs() {
        global $DB;
        $params = array(
            "sent_at" => 0
        );
        $syncthese = $DB->get_records('block_quickmail_msg_course', $params);

        $now = time();
        
        foreach ($syncthese as $cmsg) {
            // If the message gets deleted but not removed (which shouldn't happen) from the 
            // quickmail_msg_course table then skip past it.
            try {
                $zeemsg = new message($cmsg->message_id);
            } catch (\Exception $e) {
                error_log("\n\nCourse ".$cmsg->course_id. " could not find message: ".$cmsg->message_id."\n");
                continue;
            }

            if ($zeemsg->get('to_send_at') <= $now) {
                $zeemsg->populate_recip_course_msg();
            }
        }
        // syncing complete.
    }

    /**
     * Returns an array of all messages that should be sent by the system right now
     *
     * @return array
     */
    public static function get_all_messages_to_send() {
        global $DB;

        self::sync_course_recip_msgs();

        $now = time();

        $sql = 'SELECT m.*
                FROM {block_quickmail_messages} m
                INNER JOIN {block_quickmail_msg_recips} mr ON m.id = mr.message_id
                WHERE m.is_draft = 0
                AND m.is_sending = 0
                AND m.timedeleted = 0
                AND m.to_send_at <= :now
                AND mr.sent_at = 0
                GROUP BY m.id
                
                UNION
                
                SELECT m.*
                FROM {block_quickmail_messages} m
                INNER JOIN {block_quickmail_msg_course} mc ON m.id = mc.message_id
                WHERE m.is_draft = 0
                AND m.is_sending = 0
                AND m.timedeleted = 0
                AND m.to_send_at <= :now2
                AND mc.sent_at = 0
                GROUP BY m.id';

        // Pull data, iterate through recordset, instantiate persistents, add to array.
        $data = [];
        $recordset = $DB->get_recordset_sql($sql, ['now' => $now, 'now2' => $now]);
        foreach ($recordset as $record) {
            $data[] = new message(0, $record);
        }
        $recordset->close();

        return $data;
    }

}

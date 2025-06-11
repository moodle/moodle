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
use block_quickmail\repos\interfaces\draft_repo_interface;
use block_quickmail\persistents\message;

class draft_repo extends repo implements draft_repo_interface {

    public $defaultsort = 'created';

    public $defaultdir = 'desc';

    public $sortableattrs = [
        'id' => 'id',
        'course' => 'course_id',
        'subject' => 'subject',
        'created' => 'timecreated',
        'modified' => 'timemodified',
    ];

    /**
     * Fetches a draft message by id, or returns null
     *
     * @param  int  $messageid
     * @return message|null
     */
    public static function find_or_null($messageid) {
        // First, try to find the message by id, returning null by default.
        if (!$message = message::find_or_null($messageid)) {
            return null;
        }

        // If this message is NOT a draft, return null.
        if (!$message->is_message_draft()) {
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
     * Returns all unsent, non-deleted, draft messages belonging to the given user id
     *
     * Optionally, can be scoped to a specific course if given a course_id
     *
     * @param  int     $userid
     * @param  int     $courseid   optional, defaults to 0 (all)
     * @param  array   $params  sort|dir|paginate|page|per_page|uri
     * @return mixed
     */
    public static function get_for_user($userid, $courseid = 0, $params = []) {
        // Instantiate repo.
        $repo = new self($params);

        // Set params for db query.
        $queryparams = [
            'user_id' => $userid,
            'is_draft' => 1,
            'sent_at' => 0,
            'timedeleted' => 0
        ];

        // Conditionally add course id to db query params if appropriate.
        if ($courseid) {
            $queryparams['course_id'] = $courseid;
        }

        // If not paginating, return all sorted results.
        if (!$repo->paginate) {
            $data = message::get_records(
                $queryparams,
                $repo->get_sort_column_name($repo->sort),
                strtoupper($repo->dir)
            );

            // Otherwise, paginate and set the sorted results.
        } else {
            // Get total count of records (necessary for pagination).
            $count = message::count_records(
                $queryparams
            );

            // Get the calculated pagination parameters object.
            $paginated = $repo->get_paginated($count);

            // Set the pagination object on the result.
            $repo->set_result_pagination($paginated);

            // Pull the data with the validated pagination offset.
            $data = message::get_records(
                $queryparams,
                $repo->get_sort_column_name($repo->sort),
                strtoupper($repo->dir),
                $paginated->offset,
                $paginated->per_page
            );
        }

        $repo->set_result_data($data);

        return $repo->result;
    }

}

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
use block_quickmail\repos\interfaces\sent_repo_interface;
use block_quickmail\persistents\message;

class sent_repo extends repo implements sent_repo_interface {

    public $defaultsort = 'sent';

    public $defaultdir = 'desc';

    public $sortableattrs = [
        'id' => 'id',
        'course' => 'course_id',
        'subject' => 'subject',
        'sent' => 'sent_at',
        'created' => 'timecreated',
        'modified' => 'timemodified',
    ];

    /**
     * Returns all sent messages belonging to the given user id
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

        $sql .= ' AND m.is_draft = 0 AND m.timedeleted = 0 AND m.deleted = 0 AND m.sent_at > 0';

        if (!$ascount) {
            $sql .= ' ORDER BY ' . $sortby . ' ' . $sortdir;
        }

        return $sql;
    }

}

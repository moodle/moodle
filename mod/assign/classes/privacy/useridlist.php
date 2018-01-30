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
 * This file contains the mod_assign useridlist
 *
 * This is for collecting a list of user IDs
 *
 * @package mod_assign
 * @copyright 2018 Adrian Greeve <adrian@moodle.com>
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_assign\privacy;

defined('MOODLE_INTERNAL') || die();

/**
 * An object for collecting user IDs related to a teacher.
 *
 * @copyright  2018 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class useridlist {

    /** @var int The ID of the teacher. */
    protected $teacherid;

    /** @var int The ID of the assignment object. */
    protected $assignid;

    /** @var array A collection of user IDs (students). */
    protected $userids = [];

    /**
     * Create this object.
     *
     * @param int $teacherid The teacher ID.
     * @param int $assignid The assignment ID.
     */
    public function __construct($teacherid, $assignid) {
        $this->teacherid = $teacherid;
        $this->assignid = $assignid;
    }

    /**
     * Returns the teacher ID.
     *
     * @return int The teacher ID.
     */
    public function get_teacherid() {
        return $this->teacherid;
    }

    /**
     * Returns the assign ID.
     *
     * @return int The assign ID.
     */
    public function get_assignid() {
        return $this->assignid;
    }

    /**
     * Returns the user IDs.
     *
     * @return array User IDs.
     */
    public function get_userids() {
        return $this->userids;
    }

    /**
     * Add sql and params to return user IDs.
     *
     * @param string $sql The sql string to return user IDs.
     * @param array $params Parameters for the sql statement.
     */
    public function add_from_sql($sql, $params) {
        global $DB;
        $userids = $DB->get_records_sql($sql, $params);
        if (!empty($userids)) {
            $this->userids = array_merge($this->userids, $userids);
        }
    }
}

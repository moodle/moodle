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

namespace block_xp\local\division;

use block_xp\local\userfilter\everyone;
use block_xp\local\userfilter\group_members;
use block_xp\local\userfilter\user_filter;
use context_course;

/**
 * Division.
 *
 * @package    block_xp
 * @copyright  2024 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class group_division implements division {

    /**
     * The group ID.
     *
     * @var int
     */
    protected $groupid;

    /**
     * Constructor.
     *
     * @param int $groupid The group ID.
     */
    public function __construct(int $groupid) {
        $this->groupid = $groupid;
    }

    /**
     * Get the ID.
     *
     * @return int
     */
    public function get_id() {
        return $this->groupid;
    }

    /**
     * Get the name.
     *
     * @return string
     */
    public function get_name(): string {
        global $DB;
        if (!$this->groupid) {
            return get_string('allparticipants', 'core');
        }
        $mingroup = $DB->get_record('groups', ['id' => $this->groupid], 'id, name, courseid');
        if (!$mingroup) {
            return '?';
        }
        return format_string($mingroup->name, true, ['context' => context_course::instance($mingroup->courseid)]);
    }

    /**
     * Get the user filter.
     *
     * @return user_filter
     */
    public function get_user_filter(): user_filter {
        if (!$this->groupid) {
            return new everyone();
        }
        return new group_members($this->groupid);
    }

}

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
 * Attendance module renderable component.
 *
 * @package    mod_attendance
 * @copyright  2022 Dan Marsden
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_attendance\output;

use renderable;
use mod_attendance_structure;
use mod_attendance_summary;
use mod_attendance\local\url_helpers;

/**
 * Class report data.
 *
 * @copyright  2011 Artem Andreev <andreev.artem@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_data implements renderable {
    /** @var array|null|stdClass  */
    public $pageparams;
    /** @var array  */
    public $users;
    /** @var array  */
    public $groups;
    /** @var array  */
    public $sessions;
    /** @var array  */
    public $statuses;
    /** @var array includes disablrd/deleted statuses. */
    public $allstatuses;
    /** @var array  */
    public $usersgroups = array();
    /** @var array  */
    public $sessionslog = array();
    /** @var array|mod_attendance_summary  */
    public $summary = array();
    /** @var mod_attendance_structure  */
    public $att;

    /**
     * report_data constructor.
     * @param mod_attendance_structure $att
     */
    public function  __construct(mod_attendance_structure $att) {
        $this->pageparams = $att->pageparams;

        $this->users = $att->get_users($att->pageparams->group, $att->pageparams->page);

        if (isset($att->pageparams->userids)) {
            foreach ($this->users as $key => $user) {
                if (!in_array($user->id, $att->pageparams->userids)) {
                    unset($this->users[$key]);
                }
            }
        }

        $this->groups = groups_get_all_groups($att->course->id);

        $this->sessions = $att->get_filtered_sessions();

        $this->statuses = $att->get_statuses(true, true);
        $this->allstatuses = attendance_get_statuses($att->id, false);

        if ($att->pageparams->view == ATT_VIEW_SUMMARY) {
            $this->summary = new mod_attendance_summary($att->id);
        } else {
            $this->summary = new mod_attendance_summary($att->id, array_keys($this->users),
                                                        $att->pageparams->startdate, $att->pageparams->enddate);
        }

        foreach ($this->users as $key => $user) {
            $usersummary = $this->summary->get_taken_sessions_summary_for($user->id);
            if ($att->pageparams->view != ATT_VIEW_NOTPRESENT ||
                attendance_calc_fraction($usersummary->takensessionspoints, $usersummary->takensessionsmaxpoints) <
                $att->get_lowgrade_threshold()) {

                $this->usersgroups[$user->id] = groups_get_all_groups($att->course->id, $user->id);

                $this->sessionslog[$user->id] = $att->get_user_filtered_sessions_log($user->id);
            } else {
                unset($this->users[$key]);
            }
        }

        $this->att = $att;
    }

    /**
     * url take helper.
     * @param int $sessionid
     * @param int $grouptype
     * @return mixed
     */
    public function url_take($sessionid, $grouptype) {
        return url_helpers::url_take($this->att, $sessionid, $grouptype);
    }

    /**
     * url view helper.
     * @param array $params
     * @return mixed
     */
    public function url_view($params=array()) {
        return url_helpers::url_view($this->att, $params);
    }

    /**
     * url helper.
     * @param array $params
     * @return moodle_url
     */
    public function url($params=array()) {
        $params = array_merge($params, $this->pageparams->get_significant_params());

        return $this->att->url_report($params);
    }

}

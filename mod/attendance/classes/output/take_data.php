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
use moodle_url;
use stdClass;
use mod_attendance\local\url_helpers;
/**
 * class take data.
 *
 * @copyright  2011 Artem Andreev <andreev.artem@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class take_data implements renderable {
    /** @var array  */
    public $users;
    /** @var array|null|stdClass  */
    public $pageparams;
    /** @var int  */
    public $groupmode;
    /** @var stdclass  */
    public $cm;
    /** @var array  */
    public $statuses;
    /** @var mixed  */
    public $sessioninfo;
    /** @var array  */
    public $sessionlog;
    /** @var array  */
    public $sessions4copy;
    /** @var bool  */
    public $updatemode;
    /** @var string  */
    private $urlpath;
    /** @var array */
    private $urlparams;
    /** @var mod_attendance_structure  */
    public $att;

    /**
     * take_data constructor.
     * @param mod_attendance_structure $att
     */
    public function  __construct(mod_attendance_structure $att) {
        if ($att->pageparams->grouptype) {
            $this->users = $att->get_users($att->pageparams->grouptype, $att->pageparams->page);
        } else {
            $this->users = $att->get_users($att->pageparams->group, $att->pageparams->page);
        }

        $this->pageparams = $att->pageparams;

        $this->groupmode = $att->get_group_mode();
        $this->cm = $att->cm;

        $this->statuses = $att->get_statuses();

        $this->sessioninfo = $att->get_session_info($att->pageparams->sessionid);
        $this->updatemode = $this->sessioninfo->lasttaken > 0;

        if (isset($att->pageparams->copyfrom)) {
            $this->sessionlog = $att->get_session_log($att->pageparams->copyfrom);
        } else if ($this->updatemode) {
            $this->sessionlog = $att->get_session_log($att->pageparams->sessionid);
        } else {
            $this->sessionlog = array();
        }

        if (!$this->updatemode) {
            $this->sessions4copy = $att->get_today_sessions_for_copy($this->sessioninfo);
        }

        $this->urlpath = $att->url_take()->out_omit_querystring();
        $params = $att->pageparams->get_significant_params();
        $params['id'] = $att->cm->id;
        $this->urlparams = $params;

        $this->att = $att;
    }

    /**
     * Url function
     * @param array $params
     * @param array $excludeparams
     * @return moodle_url
     */
    public function url($params=array(), $excludeparams=array()) {
        $params = array_merge($this->urlparams, $params);

        foreach ($excludeparams as $paramkey) {
            unset($params[$paramkey]);
        }

        return new moodle_url($this->urlpath, $params);
    }

    /**
     * Url view helper.
     * @param array $params
     * @return mixed
     */
    public function url_view($params=array()) {
        return url_helpers::url_view($this->att, $params);
    }

    /**
     * Url path helper.
     * @return string
     */
    public function url_path() {
        return $this->urlpath;
    }
}

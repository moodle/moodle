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
 * Class definition for mod_attendance_take_page_params
 *
 * @package   mod_attendance
 * @copyright  2016 Dan Marsden http://danmarsden.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * contains functions/constants used by take attendance page.
 *
 * @copyright  2016 Dan Marsden http://danmarsden.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_attendance_take_page_params {
    /** Sorted list. */
    const SORTED_LIST           = 1;
    /** Sorted grid. */
    const SORTED_GRID           = 2;

    /** Default view */
    const DEFAULT_VIEW_MODE     = self::SORTED_LIST;

    /** @var int */
    public $sessionid;
    /** @var int */
    public $grouptype;
    /** @var int */
    public $group;
    /** @var int */
    public $sort;
    /** @var int */
    public $copyfrom;

    /** @var int view mode of taking attendance page*/
    public $viewmode;

    /** @var int */
    public $gridcols;

    /**
     * Initialize params.
     */
    public function init() {
        if (!isset($this->group)) {
            $this->group = 0;
        }
        if (!isset($this->sort)) {
            $this->sort = ATT_SORT_DEFAULT;
        }
        $this->init_view_mode();
        $this->init_gridcols();
    }

    /**
     * Initialise view mode params.
     */
    private function init_view_mode() {
        if (isset($this->viewmode)) {
            set_user_preference("attendance_take_view_mode", $this->viewmode);
        } else {
            $this->viewmode = get_user_preferences("attendance_take_view_mode", self::DEFAULT_VIEW_MODE);
        }
    }

    /**
     * Initilise grid columns.
     */
    private function init_gridcols() {
        if (isset($this->gridcols)) {
            set_user_preference("attendance_gridcolumns", $this->gridcols);
        } else {
            $this->gridcols = get_user_preferences("attendance_gridcolumns", 5);
        }
    }

    /**
     * Get main page params.
     * @return array
     */
    public function get_significant_params() {
        $params = array();

        $params['sessionid'] = $this->sessionid;
        $params['grouptype'] = $this->grouptype;
        if ($this->group) {
            $params['group'] = $this->group;
        }
        if ($this->sort != ATT_SORT_DEFAULT) {
            $params['sort'] = $this->sort;
        }
        if (isset($this->copyfrom)) {
            $params['copyfrom'] = $this->copyfrom;
        }

        return $params;
    }
}

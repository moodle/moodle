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
 * Class definition for mod_attendance_preferences_page_params
 *
 * @package   mod_attendance
 * @copyright  2016 Dan Marsden http://danmarsden.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * base preferences page param class
 *
 * @copyright  2016 Dan Marsden http://danmarsden.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_attendance_preferences_page_params {
    /** Add */
    const ACTION_ADD              = 1;
    /** Delete */
    const ACTION_DELETE           = 2;
    /** Hide */
    const ACTION_HIDE             = 3;
    /** Show */
    const ACTION_SHOW             = 4;
    /** Save */
    const ACTION_SAVE             = 5;

    /** @var int view mode of taking attendance page*/
    public $action;

    /** @var int */
    public $statusid;

    /** @var array */
    public $statusset;

    /**
     * Get params for this page.
     *
     * @return array
     */
    public function get_significant_params() {
        $params = array();

        if (isset($this->action)) {
            $params['action'] = $this->action;
        }
        if (isset($this->statusid)) {
            $params['statusid'] = $this->statusid;
        }
        if (isset($this->statusset)) {
            $params['statusset'] = $this->statusset;
        }

        return $params;
    }
}
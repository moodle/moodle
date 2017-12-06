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
 * Class definition for mod_attendance_sessions_page_params
 *
 * @package   mod_attendance
 * @copyright  2016 Dan Marsden http://danmarsden.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * stores constants/data used by sessions page params.
 *
 * @copyright  2016 Dan Marsden http://danmarsden.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_attendance_sessions_page_params {
    /**
     *  Add Session.
     */
    const ACTION_ADD               = 1;

    /**
     *  Update Session.
     */
    const ACTION_UPDATE            = 2;

    /**
     * Delete Session
     */
    const ACTION_DELETE            = 3;

    /**
     *  Delete selected Sessions.
     */
    const ACTION_DELETE_SELECTED   = 4;

    /**
     *  Change duration of a session.
     */
    const ACTION_CHANGE_DURATION   = 5;

    /**
     *  Delete a hidden session.
     */
    const ACTION_DELETE_HIDDEN     = 6;

    /** @var int view mode of taking attendance page*/
    public $action;
}
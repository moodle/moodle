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
 * Class definition for mod_attendance_header
 *
 * @package    mod_attendance
 * @author     Daniel Thee Roperto <daniel.roperto@catalyst-au.net>
 * @copyright  2017 Catalyst IT Australia {@link http://www.catalyst-au.net}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Used to render the page header.
 *
 * @package    mod_attendance
 * @author     Daniel Thee Roperto <daniel.roperto@catalyst-au.net>
 * @copyright  2017 Catalyst IT Australia {@link http://www.catalyst-au.net}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_attendance_header implements renderable {
    /** @var mod_attendance_structure */
    private $attendance;

    /** @var string */
    private $title;

    /**
     * mod_attendance_header constructor.
     *
     * @param mod_attendance_structure $attendance
     * @param null                     $title
     */
    public function __construct(mod_attendance_structure $attendance, $title = null) {
        $this->attendance = $attendance;
        $this->title = $title;
    }

    /**
     * Gets the attendance data.
     *
     * @return mod_attendance_structure
     */
    public function get_attendance() {
        return $this->attendance;
    }

    /**
     * Gets the title. If title was not provided, use the module name.
     *
     * @return string
     */
    public function get_title() {
        return is_null($this->title) ? $this->attendance->name : $this->title;
    }

    /**
     * Checks if the header should be rendered.
     *
     * @return bool
     */
    public function should_render() {
        return !is_null($this->title) || !empty($this->attendance->intro);
    }
}

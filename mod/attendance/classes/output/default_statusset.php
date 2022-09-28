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
use moodle_url;

/**
 * Default status set
 *
 * @copyright  2011 Artem Andreev <andreev.artem@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class default_statusset implements renderable {
    /** @var array  */
    public $statuses;
    /** @var array  */
    public $errors;

    /**
     * default_statusset constructor.
     * @param array $statuses
     * @param array $errors
     */
    public function __construct($statuses, $errors) {
        $this->statuses = $statuses;
        $this->errors = $errors;
    }

    /**
     * url helper.
     * @param array $params
     * @return moodle_url
     */
    public function url($params) {
        return new moodle_url('/mod/attendance/defaultstatus.php', $params);
    }
}

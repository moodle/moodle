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

/**
 * Output a selector to change between status sets.
 *
 * @copyright  2011 Artem Andreev <andreev.artem@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class set_selector implements renderable {
    /** @var int  */
    public $maxstatusset;
    /** @var mod_attendance_structure  */
    private $att;

    /**
     * set_selector constructor.
     * @param mod_attendance_structure $att
     * @param int $maxstatusset
     */
    public function __construct(mod_attendance_structure $att, $maxstatusset) {
        $this->att = $att;
        $this->maxstatusset = $maxstatusset;
    }

    /**
     * url helper
     * @param array $statusset
     * @return moodle_url
     */
    public function url($statusset) {
        $params = array();
        $params['statusset'] = $statusset;

        return $this->att->url_preferences($params);
    }

    /**
     * get current statusset.
     * @return int
     */
    public function get_current_statusset() {
        if (isset($this->att->pageparams->statusset)) {
            return $this->att->pageparams->statusset;
        }
        return 0;
    }

    /**
     * get statusset name.
     * @param int $statusset
     * @return string
     */
    public function get_status_name($statusset) {
        return attendance_get_setname($this->att->id, $statusset, true);
    }
}

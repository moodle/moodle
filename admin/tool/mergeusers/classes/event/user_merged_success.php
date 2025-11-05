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
 * The user_merged_success event.
 *
 * @package   tool_mergeusers
 * @author    Gerard Cuello Adell <gerard.urv@gmail.com>
 * @copyright 2016 onwards to Universitat Rovira i Virgili (https://www.urv.cat)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_mergeusers\event;

use coding_exception;

/**
 * Class user_merged_success called when merging user accounts has gone right.
 *
 * @package   tool_mergeusers
 * @author    Gerard Cuello Adell <gerard.urv@gmail.com>
 * @copyright 2016 onwards to Universitat Rovira i Virgili (https://www.urv.cat)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_merged_success extends user_merged {
    /**
     * Gets the event name.
     * @return string
     * @throws coding_exception
     */
    public static function get_name() {
        return get_string('eventusermergedsuccess', 'tool_mergeusers');
    }

    /**
     * Human-readable detail of this event.
     *
     * @return string
     */
    public function get_description() {
        return $this->get_description_as('success');
    }
}

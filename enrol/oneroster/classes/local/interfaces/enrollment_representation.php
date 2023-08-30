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
 * One Roster Enrolment Client.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_oneroster\local\interfaces;

use stdClass;

/**
 * A One Roster Object which can represent a Moodle User.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface enrollment_representation {
    /**
     * Get the data which represents this One Roster Object as a Moodle User.
     *
     * @return  stdClass
     */
    public function get_enrolment_data(): stdClass;

    /**
     * Get the data relating to the One Roster Role.
     *
     * Note: This uses the One Roster role representation.
     * It must be translated by the client per user-defined mappings.
     *
     * @return  stdClass
     */
    public function get_role_data(): stdClass;

    /**
     * Get the representation of a Moodle course that this enrollment is in.
     *
     * @return  course_representation
     */
    public function get_course_representation(): course_representation;
}

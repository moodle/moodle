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

namespace enrol_oneroster\local\services;

use enrol_oneroster\local\service as abstract_service;
use enrol_oneroster\local\endpoints\rostering as rostering_endpoint;

/**
 * One Roster v1p1 Service definition for Aeries.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class aeries extends abstract_service {
    /** @var array A list of optional endpoints */
    protected static $optionalendpoints = [
        rostering_endpoint::getClassesForSchool => false,
        rostering_endpoint::getCoursesForSchool => false,
        rostering_endpoint::getCoursesForStudent => false,
        rostering_endpoint::getTermsForSchool => false,
    ];
}

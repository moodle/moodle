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

namespace enrol_oneroster\local\collections;

use enrol_oneroster\local\interfaces\container as container_interface;
use enrol_oneroster\local\endpoints\rostering as rostering_endpoint;

/**
 * One Roster Courses for School collection.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class courses_for_school extends courses {

    /**
     * Get the operation ID for the endpoint, otherwise known as the name of the endpoint.
     *
     * @param   container_interface $container The container to which this entity belongs
     * @return  string
     */
    protected static function get_operation_id(container_interface $container): string {
        if ($container->supports(rostering_endpoint::getCoursesForSchool)) {
            return rostering_endpoint::getCoursesForSchool;
        }

        return rostering_endpoint::getAllCourses;
    }

    /**
     * Process the supplied parameters and modify them as required.
     *
     * @param   array $params
     * @return  array
     */
    protected function process_params(array $params): array {
        if (!$this->container->supports(rostering_endpoint::getCoursesForSchool)) {
            $this->filter->add_filter(
                'org.sourcedId',
                $params[':id']
            );
            unset($params[':id']);
        }

        return parent::process_params($params);
    }
}

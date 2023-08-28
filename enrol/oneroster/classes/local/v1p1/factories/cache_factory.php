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

namespace enrol_oneroster\local\v1p1\factories;

use cache;
use enrol_oneroster\local\factories\cache_factory as parent_cache_factory;

/**
 * One Roster 1.1 Cache Factory.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cache_factory extends parent_cache_factory {

    /**
     * Get the organisation entity cache.
     *
     * @return  cache
     */
    public function get_org_cache(): cache {
        return cache::make('enrol_oneroster', 'v1p1_remote_orgs');
    }

    /**
     * Get the academicSession entity cache.
     *
     * @return  cache
     */
    public function get_academic_session_cache(): cache {
        return cache::make('enrol_oneroster', 'v1p1_remote_academic_sessions');
    }

    /**
     * Get the class entity cache.
     *
     * @return  cache
     */
    public function get_class_cache(): cache {
        return cache::make('enrol_oneroster', 'v1p1_remote_classes');
    }

    /**
     * Get the course entity cache.
     *
     * @return  cache
     */
    public function get_course_cache(): cache {
        return cache::make('enrol_oneroster', 'v1p1_remote_courses');
    }

    /**
     * Get the user entity cache.
     *
     * @return  cache
     */
    public function get_user_cache(): cache {
        return cache::make('enrol_oneroster', 'v1p1_remote_users');
    }

    /**
     * Get the enrollment entity cache.
     *
     * @return  cache
     */
    public function get_enrolment_cache(): cache {
        return cache::make('enrol_oneroster', 'v1p1_remote_enrolments');
    }
}

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
 * Search area base class for areas working at module level.
 *
 * @package    core_search
 * @copyright  2015 David Monllao {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_search;

defined('MOODLE_INTERNAL') || die();

/**
 * Base implementation for search areas working at module level.
 *
 * Even if the search area works at multiple levels, if module is one of these levels
 * it should extend this class, as this class provides helper methods for module level search management.
 *
 * @package    core_search
 * @copyright  2015 David Monllao {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class base_mod extends base {

    /**
     * The context levels the search area is working on.
     *
     * This can be overwriten by the search area if it works at multiple
     * levels.
     *
     * @var array
     */
    protected static $levels = [CONTEXT_MODULE];

    /**
     * Gets the course module for the required instanceid + modulename.
     *
     * The returned data depends on the logged user, when calling this through
     * self::get_document the admin user is used so everything would be returned.
     *
     * No need more internal caching here, modinfo is already cached.
     *
     * @throws \dml_missing_record_exception
     * @param string $modulename The module name
     * @param int $instanceid Module instance id (depends on the module)
     * @param int $courseid Helps speeding up things
     * @return \cm_info
     */
    protected function get_cm($modulename, $instanceid, $courseid) {
        $modinfo = get_fast_modinfo($courseid);

        // Hopefully not many, they are indexed by cmid.
        $instances = $modinfo->get_instances_of($modulename);
        foreach ($instances as $cminfo) {
            if ($cminfo->instance == $instanceid) {
                return $cminfo;
            }
        }

        // Nothing found.
        throw new \dml_missing_record_exception($modulename);

        return $cm;
    }
}

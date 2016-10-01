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
 * Theme filter.
 *
 * @package    tool_usertours
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_usertours\local\filter;

defined('MOODLE_INTERNAL') || die();

use tool_usertours\tour;
use context;

/**
 * Theme filter.
 *
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class role extends base {
    /**
     * The name of the filter.
     *
     * @return  string
     */
    public static function get_filter_name() {
        return 'role';
    }

    /**
     * Retrieve the list of available filter options.
     *
     * @return  array                   An array whose keys are the valid options
     *                                  And whose values are the values to display
     */
    public static function get_filter_options() {
        return role_get_names(null, ROLENAME_ALIAS, true);
    }

    /**
     * Check whether the filter matches the specified tour and/or context.
     *
     * @param   tour        $tour       The tour to check
     * @param   context     $context    The context to check
     * @return  boolean
     */
    public static function filter_matches(tour $tour, context $context) {
        global $USER;

        $values = $tour->get_filter_values(self::get_filter_name());

        if (empty($values)) {
            // There are no values configured.
            // No values means all.
            return true;
        }

        if (is_siteadmin()) {
            return true;
        }

        // Presence within the array is sufficient. Ignore any value.
        $values = array_flip($values);

        $cache = \cache::make_from_params(\cache_store::MODE_REQUEST, 'tool_usertours', 'filter_role');
        $cachekey = "{$USER->id}_{$context->id}";
        $userroles = $cache->get($cachekey);
        if ($userroles === false) {
            $userroles = get_user_roles_with_special($context);
            $cache->set($cachekey, $userroles);
        }

        foreach ($userroles as $role) {
            if (isset($values[$role->roleid])) {
                return true;
            }
        }

        return false;
    }
}

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
class theme extends base {
    /**
     * The name of the filter.
     *
     * @return  string
     */
    public static function get_filter_name() {
        return 'theme';
    }

    /**
     * Retrieve the list of available filter options.
     *
     * @return  array                   An array whose keys are the valid options
     *                                  And whose values are the values to display
     */
    public static function get_filter_options() {
        $manager = \core_plugin_manager::instance();
        $themes = $manager->get_installed_plugins('theme');

        $options = [];
        foreach (array_keys($themes) as $themename) {
            try {
                $theme = \theme_config::load($themename);
            } catch (Exception $e) {
                // Bad theme, just skip it for now.
                continue;
            }
            if ($themename !== $theme->name) {
                // Obsoleted or broken theme, just skip for now.
                continue;
            }
            if ($theme->hidefromselector) {
                // The theme doesn't want to be shown in the theme selector and as theme
                // designer mode is switched off we will respect that decision.
                continue;
            }
            $options[$theme->name] = get_string('pluginname', "theme_{$theme->name}");
        }
        return $options;
    }

    /**
     * Check whether the filter matches the specified tour and/or context.
     *
     * @param   tour        $tour       The tour to check
     * @param   context     $context    The context to check
     * @return  boolean
     */
    public static function filter_matches(tour $tour, context $context) {
        global $PAGE;

        $values = $tour->get_filter_values('theme');

        if (empty($values)) {
            // There are no values configured.
            // No values means all.
            return true;
        }

        // Presence within the array is sufficient. Ignore any value.
        $values = array_flip($values);
        return isset($values[$PAGE->theme->name]);
    }
}

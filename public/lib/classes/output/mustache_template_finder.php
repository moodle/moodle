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

namespace core\output;

use core\exception\coding_exception;
use core\exception\moodle_exception;
use core_component;

/**
 * Get information about valid locations for mustache templates.
 *
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      2.9
 * @package core
 */
class mustache_template_finder {
    /**
     * Helper function for getting a list of valid template directories for a specific component.
     *
     * @param string $component The component to search
     * @param string $themename The current theme name
     * @param bool $themeoverrides Whether to apply theme overrides. Defaults to true.
     * @return string[] List of valid directories for templates for this component. Directories are not checked for existence.
     */
    public static function get_template_directories_for_component($component, $themename = '', $themeoverrides = true) {
        global $CFG, $PAGE;

        // Default the param.
        if ($themename == '') {
            $themename = $PAGE->theme->name;
        }

        // Clean params for safety.
        $component = clean_param($component, PARAM_COMPONENT);
        $themename = clean_param($themename, PARAM_COMPONENT);

        // Validate the component.
        $dirs = [];
        $compdirectory = core_component::get_component_directory($component);
        if (!$compdirectory) {
            throw new coding_exception("Component was not valid: " . s($component));
        }

        if ($themeoverrides) {
            // Find the parent themes.
            $parents = [];
            if ($themename === $PAGE->theme->name) {
                $parents = $PAGE->theme->parents;
            } else {
                $themeconfig = theme_config::load($themename);
                $parents = $themeconfig->parents;
            }

            // First check the theme.
            $dirs[] = $CFG->dirroot . '/theme/' . $themename . '/templates/' . $component . '/';
            if (isset($CFG->themedir)) {
                $dirs[] = $CFG->themedir . '/' . $themename . '/templates/' . $component . '/';
            }
            // Now check the parent themes.
            // Search each of the parent themes second.
            foreach ($parents as $parent) {
                $dirs[] = $CFG->dirroot . '/theme/' . $parent . '/templates/' . $component . '/';
                if (isset($CFG->themedir)) {
                    $dirs[] = $CFG->themedir . '/' . $parent . '/templates/' . $component . '/';
                }
            }
        }

        $dirs[] = $compdirectory . '/templates/';

        return $dirs;
    }

    /**
     * Helper function for getting a filename for a template from the template name.
     *
     * Theme overrides are automatically applied unless a '!' is appended to `$name`.
     *
     * Example: Template core/test overridden in theme_foo.
     *   - `get_template_filepath('core/test', 'foo')` resolves to 'theme/foo/templates/core/test.mustache'.
     *   - `get_template_filepath('core/test!', 'foo')` resolves to 'lib/templates/test.mustache'.
     *
     * @param string $name This is the componentname/templatename combined. May end in an exclamation mark.
     * @param string $themename This is the current theme name.
     * @return string
     */
    public static function get_template_filepath($name, $themename = '') {
        if (strpos($name, '/') === false) {
            throw new coding_exception('Templates names must be specified as "componentname/templatename"' .
                                       ' (' . s($name) . ' requested) ');
        }

        [$component, $templatename] = explode('/', $name, 2);
        $component = clean_param($component, PARAM_COMPONENT);

        // We apply theme overrides if the name does NOT end with an exclamation mark.
        $themeoverrides = true;
        if (str_ends_with($templatename, '!')) {
            $themeoverrides = false;
            // Remove exclamation mark.
            $templatename = substr($templatename, 0, strlen($templatename) - 1);
        }

        $dirs = self::get_template_directories_for_component($component, $themename, $themeoverrides);

        foreach ($dirs as $dir) {
            $candidate = $dir . $templatename . '.mustache';
            if (file_exists($candidate)) {
                return $candidate;
            }
        }

        throw new moodle_exception('filenotfound', 'error', '', null, $name);
    }
}

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
 * Defines Manager class for my profile navigation tree.
 *
 * @package   core_user
 * @copyright 2015 onwards Ankit Agarwal
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_user\output\myprofile;
defined('MOODLE_INTERNAL') || die();

/**
 * Defines MAnager class for myprofile navigation tree.
 *
 * @since     Moodle 2.9
 * @package   core_user
 * @copyright 2015 onwards Ankit Agarwal
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manager {
    /**
     * Parse all callbacks and builds the tree.
     *
     * @param integer   $user ID of the user for which the profile is displayed.
     * @param bool      $iscurrentuser true if the profile being viewed is of current user, else false.
     * @param \stdClass $course Course object
     *
     * @return tree Fully build tree to be rendered on my profile page.
     */
    public static function build_tree($user, $iscurrentuser, $course = null) {
        global $CFG;
        $tree = new tree();

        // Add core nodes.

        require_once($CFG->libdir . "/myprofilelib.php");
        core_myprofile_navigation($tree, $user, $iscurrentuser, $course);

        // Core components.
        $components = \core_component::get_core_subsystems();
        foreach ($components as $component => $directory) {
            if (empty($directory)) {
                continue;
            }
            $file = $directory . "/lib.php";
            if (is_readable($file)) {
                require_once($file);
                $function = "core_" . $component . "_myprofile_navigation";
                if (function_exists($function)) {
                    $function($tree, $user, $iscurrentuser, $course);
                }
            }
        }

        // Plugins.
        $pluginswithfunction = get_plugins_with_function('myprofile_navigation', 'lib.php');
        foreach ($pluginswithfunction as $plugins) {
            foreach ($plugins as $function) {
                $function($tree, $user, $iscurrentuser, $course);
            }
        }

        $tree->sort_categories();
        return $tree;
    }
}

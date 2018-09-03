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
 * Adhoc task that builds and caches all of the site's installed themes.
 *
 * @package    core
 * @copyright  2017 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\task;

defined('MOODLE_INTERNAL') || die();

/**
 * Class that builds and caches all of the site's installed themes.
 *
 * @package     core
 * @copyright   2017 Ryan Wyllie <ryan@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class build_installed_themes_task extends adhoc_task {

    /**
     * Run the task.
     */
    public function execute() {
        global $CFG;
        require_once("{$CFG->libdir}/outputlib.php");

        $themenames = array_keys(\core_component::get_plugin_list('theme'));
        // Load the theme configs.
        $themeconfigs = array_map(function($themename) {
            return \theme_config::load($themename);
        }, $themenames);

        // Build the list of themes and cache them in local cache.
        theme_build_css_for_themes($themeconfigs);
    }
}

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
 * Defines the renderer for the hvp (H5P) module.
 *
 * @package     mod_hvp
 * @copyright   2016 Joubel AS <contact@joubel.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * The renderer for the hvp module.
 *
 * @copyright   2016 Joubel AS <contact@joubel.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @SuppressWarnings(PHPMD)
 */
class mod_hvp_renderer extends plugin_renderer_base {

    /**
     * Alter which stylesheets are loaded for H5P. This is useful for adding
     * your own custom styles or replacing existing ones.
     *
     * @param object $scripts List of stylesheets that will be loaded
     * @param array $libraries Array of libraries indexed by the library's machineName
     * @param string $embedtype Possible values: div, iframe, external, editor
     */
    public function hvp_alter_styles(&$scripts, $libraries, $embedtype) {
    }

    /**
     * Alter which scripts are loaded for H5P. Useful for adding your
     * own custom scripts or replacing existing ones.
     *
     * @param object $scripts List of JavaScripts that will be loaded
     * @param array $libraries Array of libraries indexed by the library's machineName
     * @param string $embedtype Possible values: div, iframe, external, editor
     */
    public function hvp_alter_scripts(&$scripts, $libraries, $embedtype) {
    }

    /**
     * Alter semantics before they are processed. This is useful for changing
     * how the editor looks and how content parameters are filtered.
     *
     * @param object $semantics Semantics as object
     * @param string $name Machine name of library
     * @param int $majorversion Major version of library
     * @param int $minorversion Minor version of library
     */
    public function hvp_alter_semantics(&$semantics, $name, $majorversion, $minorversion) {
    }

    /**
     * Alter parameters of H5P content after it has been filtered through
     * semantics. This is useful for adapting the content to the current context.
     *
     * @param object $parameters The content parameters for the library
     * @param string $name The machine readable name of the library
     * @param int $majorversion Major version of the library
     * @param int $minorversion Minor version of the library
     */
    public function hvp_alter_filtered_parameters(&$parameters, $name, $majorversion, $minorversion) {
    }
}

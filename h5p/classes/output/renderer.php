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

namespace core_h5p\output;

use plugin_renderer_base;

/**
 * Renderer class.
 *
 * @package    core_h5p
 * @copyright  2020 Victor Deniz {victor@moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends plugin_renderer_base {

    /**
     * Alter which stylesheets are loaded for H5P.
     * This is useful for adding custom styles or replacing existing ones.
     *
     * This method can be overridden by other themes if the styles must be loaded from
     * a different place than the "Raw initial SCSS" and "Raw SCSS" theme settings.
     *
     * @param \stdClass[] $styles List of stylesheets that will be loaded
     * @param array $libraries Array of libraries indexed by the library's machineName
     * @param string $embedtype Possible values: div, iframe, external, editor
     */
    public function h5p_alter_styles(&$styles, array $libraries, string $embedtype) {
        $customcss = \core_h5p\file_storage::get_custom_styles();
        if (!empty($customcss)) {
            // Add the CSS file to the styles array, to load it from the H5P player.
            $styles[] = (object) [
                'path' => $customcss['cssurl']->out(),
                'version' => '?ver='.$customcss['cssversion'],
            ];
        }
    }

    /**
     * Alter which scripts are loaded for H5P.
     * This is useful for adding custom scripts or replacing existing ones.
     *
     * @param array|object $scripts List of JavaScripts that will be loaded
     * @param array $libraries Array of libraries indexed by the library's machineName
     * @param string $embedtype Possible values: div, iframe, external, editor
     */
    public function h5p_alter_scripts(&$scripts, array $libraries, string $embedtype) {
    }

    /**
     * Alter semantics before they are processed. This is useful for changing
     * how the editor looks and how content parameters are filtered.
     *
     * @param object|object $semantics Semantics as object
     * @param string $name Machine name of library
     * @param int $majorversion Major version of library
     * @param int $minorversion Minor version of library
     */
    public function h5p_alter_semantics(&$semantics, $name, $majorversion, $minorversion) {
    }

    /**
     * Alter parameters of H5P content after it has been filtered through semantics.
     * This is useful for adapting the content to the current context.
     *
     * @param array|object $parameters The content parameters for the library
     * @param string $name The machine readable name of the library
     * @param int $majorversion Major version of the library
     * @param int $minorversion Minor version of the library
     */
    public function h5p_alter_filtered_parameters(&$parameters, string $name, int $majorversion, int $minorversion) {
    }
}

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
 * Mod H5P Renderer library functions for Snap theme.
 *
 * @package   theme_snap
 * @author    Diego Monroy
 * @copyright Copyright (c) 2021 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

global $CFG;
$h5prenderer = $CFG->dirroot.'/mod/hvp/renderer.php';

if (file_exists($h5prenderer)) {
    // Be sure to include the H5P renderer so it can be extended.
    require_once($h5prenderer);
    /**
     * Class theme_snap_mod_hvp_renderer
     */
    class theme_snap_mod_hvp_renderer extends mod_hvp_renderer {

        /**
         * Add styles when an H5P is displayed.
         *
         * @param array $styles Styles that will be applied.
         * @param array $libraries Libraries that will be shown.
         * @param string $embedtype How the H5P is displayed.
         */
        public function hvp_alter_styles(&$styles, $libraries=null, $embedtype=null) {
            $content = $this->get_settings('hvpcustomcss');

            if (!empty($content)) {
                $styles[] = (object) array(
                    'path' => $this->get_style_url($content),
                    'version' => '',
                );
            }
        }

        /**
         * Create the style URL for H5P CSS.
         *
         * @param string $content settings H5P Custom CSS.
         * @return string $url settings path to serve as URL.
         */
        public function get_style_url($content) {
            global $CFG;
            $syscontext = \context_system::instance();
            $itemid = md5($content);

            $url = \moodle_url::make_file_url("$CFG->wwwroot/pluginfile.php",
                "/$syscontext->id/theme_snap/$content/$itemid/hvpcustomcss.css");

            if ($this->get_is_valid($url)) {
                return $url;
            } else {
                $url = "$CFG->wwwroot/mod/hvp/styles.css";
                return $url;
            }
        }

        /**
         * Verify setting value to assign.
         *
         * @param string $setting settings H5P Custom CSS.
         * @param string $format false as default.
         */
        public static function get_settings($setting, $format = false) {
            global $CFG;
            require_once($CFG->dirroot . '/lib/weblib.php');

            if (empty($setting)) {
                return false;
            } else if (!$format) {
                return $setting;
            } else {
                return format_string($setting);
            }
        }

        /**
         * Verify that generated url has a valid status.
         *
         * @param string $url CSS generated url.
         * @return bool.
         */
        public static function get_is_valid($url) {
            $handle = curl_init($url);
            curl_setopt($handle,  CURLOPT_RETURNTRANSFER, true);

            // Get element.
            $response = curl_exec($handle);

            // Check for 404 (file not found).
            $httpcode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
            curl_close($handle);

            // If the css has loaded successfully without any redirection or error.
            if ($httpcode >= 200 && $httpcode < 300) {
                return true;
            } else {
                return false;
            }
        }

    }
}

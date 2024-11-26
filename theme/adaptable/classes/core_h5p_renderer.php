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
 * Adaptable theme.
 *
 * @package    theme_adaptable
 * @copyright  2023 G J Barnard
 *               {@link https://moodle.org/user/profile.php?id=442195}
 *               {@link https://gjbarnard.co.uk}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
defined('MOODLE_INTERNAL') || die;

global $CFG;
if (file_exists($CFG->dirroot . '/h5p/classes/output/renderer.php')) {
    if (method_exists(\core_h5p\output\renderer::class, 'h5p_alter_styles')) {
        /**
         * Class theme_adaptable_core_h5p_renderer.
         *
         * See: https://tracker.moodle.org/browse/MDL-69087 and
         *      https://github.com/sarjona/h5pmods-moodle-plugin.
         *
         * @package     theme_adaptable
         * @copyright   2023 G J Barnard
         * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
         */
        class theme_adaptable_core_h5p_renderer extends \core_h5p\output\renderer {
            use \theme_adaptable\hvp_toolbox;

            /**
             * Add styles when an H5P is displayed.
             *
             * @param array $styles Styles that will be applied.
             * @param array $libraries Libraries that will be shown.
             * @param string $embedtype How the H5P is displayed.
             */
            public function h5p_alter_styles(&$styles, $libraries, $embedtype) {
                $this->ahvp_alter_styles($styles, $libraries, $embedtype);
            }
        }
    }
}

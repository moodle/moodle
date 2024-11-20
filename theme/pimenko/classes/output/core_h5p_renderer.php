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

namespace theme_pimenko\output;

use theme_config;
use context_system;

/**
 * Class core_h5p_renderer
 *
 * Extends the H5P renderer so that we are able to override the relevant
 * functions declared there
 */
class core_h5p_renderer extends \core_h5p\output\renderer {
    /**
     * Add styles when an H5P is displayed.
     *
     * @param array $styles Styles that will be applied.
     * @param array $libraries Libraries that wil be shown.
     * @param string $embedType How the H5P is displayed.
     */
    public function h5p_alter_styles(&$styles, $libraries, $embedtype) {
        global $CFG;
        $theme = theme_config::load('pimenko');

        // Generate url of file we set up in settings.
        $component = 'theme_pimenko';
        $itemid = theme_get_revision();
        $filepath = $theme->settings->h5pcss;
        $syscontext = context_system::instance();
        // Don't add url if filepath is empty to prevent case where user don't custom H5P css.
        if ($filepath) {
            $url = "$CFG->wwwroot/pluginfile.php" . "/$syscontext->id/$component/h5pcss/$itemid" . $filepath;

            // Now we can send it to h5p.
            $styles[] = (object) [
                'path' => $url,
                'version' => '',
            ];
        }
    }

}

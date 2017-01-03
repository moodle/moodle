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
 * Contains class \core\output\icon_system
 *
 * @package    core
 * @category   output
 * @copyright  2016 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\output;

use renderer_base;
use pix_icon;

/**
 * Class allowing different systems for mapping and rendering icons.
 *
 * Possible icon styles are:
 *   1. standard - image tags are generated which point to pix icons stored in a plugin pix folder.
 *   2. fontawesome - font awesome markup is generated with the name of the icon mapped from the moodle icon name.
 *   3. inline - inline tags are used for svg and png so no separate page requests are made (at the expense of page size).
 *
 * @package    core
 * @category   output
 * @copyright  2016 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class icon_system_font extends icon_system {

    private $map = [];

    public function get_core_icon_map() {
        return [];
    }

    public function render_pix_icon(renderer_base $output, pix_icon $icon) {
        $subtype = 'pix_icon_' . $this->system;
        $subpix = new $subtype($icon);
        $data = $subpix->export_for_template($output);

        return $output->render_from_template('core/pix_icon_' . $this->system, $data);
    }

    /**
     * Overridable function to get a mapping of all icons.
     * Default is to do no mapping.
     */
    public function get_icon_name_map() {
        if ($this->map === []) {
            $this->map = $this->get_core_icon_map();
            $callback = 'get_' . $this->system . '_icon_map';

            if ($pluginsfunction = get_plugins_with_function($callback)) {
                foreach ($pluginsfunction as $plugintype => $plugins) {
                    foreach ($plugins as $pluginfunction) {
                        $pluginmap = $pluginfunction();
                        $this->map += $pluginmap;
                    }
                }
            }

        }
        return $this->map;
    }

}


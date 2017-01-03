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
abstract class icon_system {
    const STANDARD = 'standard';
    const FONTAWESOME = 'fontawesome';
    const INLINE = 'inline';
    const SYSTEMS = [ self::STANDARD, self::FONTAWESOME, self::INLINE ];

    private static $instance = null;
    private $map = null;
    protected $system = '';

    private function __construct($system) {
        $this->system = $system;
    }

    public final static function instance($type = null) {
        global $PAGE;

        if ($type == null) {
            if (!empty(self::$instance)) {
                return self::$instance;
            }
            $type = $PAGE->theme->get_icon_system();
            $system = '\\core\\output\\icon_system_' . $type;
            self::$instance = new $system($type);
            // Default one is a singleton.
            return self::$instance;
        } else {
            $system = '\\core\\output\\icon_system_' . $type;
            // Not a singleton.
            return new $system($type);
        }
    }

    /**
     * Validate the theme config setting.
     *
     * @param string $system
     * @return boolean
     */
    public final static function is_valid_system($system) {
        return in_array($system, self::SYSTEMS);
    }

    /**
     * Render the pix icon according to the icon system.
     *
     * @param renderer_base $output
     * @param pix_icon $icon
     * @return string
     */
    public abstract function render_pix_icon(renderer_base $output, pix_icon $icon);

    /**
     * Overridable function to get a mapping of all icons.
     * Default is to do no mapping.
     */
    public function get_icon_name_map() {
        return [];
    }

    /**
     * Overridable function to map the icon name to something else.
     * Default is to do no mapping. Map is cached in the singleton.
     */
    public final function remap_icon_name($iconname, $component) {
        if ($this->map === null) {
            $this->map = $this->get_icon_name_map();
        }
        if ($component == null) {
            $component = 'core';
        } else if ($component != 'theme') {
            $component = \core_component::normalize_componentname($component);
        }

        if (isset($this->map[$component . ':' . $iconname])) {
            return $this->map[$component . ':' . $iconname];
        }
        return false;
    }
}


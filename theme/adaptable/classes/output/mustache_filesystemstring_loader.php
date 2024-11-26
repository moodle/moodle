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
 * Mustache file system loader.
 *
 * @package    theme_adaptable
 * @copyright  2020 G J Barnard
 *               {@link https://moodle.org/user/profile.php?id=442195}
 *               {@link https://gjbarnard.co.uk}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

namespace theme_adaptable\output;

use coding_exception;

/**
 * Mustache file system loader.
 */
class mustache_filesystemstring_loader extends \core\output\mustache_filesystem_loader {
    /**
     * @var $templates array of templates.
     */
    private $templates = [];

    /**
     * Provide a default no-args constructor (we don't really need anything).
     */
    public function __construct() {
    }

    /**
     * Load a Template by name.
     *
     * @param string $name
     *
     * @return string Mustache Template source
     */
    public function load($name) {
        if (!isset($this->templates[$name])) {
            $templateoverride = \theme_adaptable\toolbox::get_template_override($name);
            if ($templateoverride !== false) {
                $this->templates[$name] = $templateoverride;
            } else {
                $this->templates[$name] = $this->loadFile($name);
            }
        }

        return $this->templates[$name];
    }
}

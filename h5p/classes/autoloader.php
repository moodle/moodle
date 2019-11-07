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
 * H5P Autoloader.
 *
 * @package    core_h5p
 * @copyright  2019 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_h5p;

defined('MOODLE_INTERNAL') || die();

/**
 * H5P Autoloader.
 *
 * @package    core_h5p
 * @copyright  2019 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class autoloader {
    public static function register(): void {
        spl_autoload_register([self::class, 'autoload']);
    }

    public static function autoload($classname): void {
        global $CFG;

        $classes = [
            'H5PCore' => '/lib/h5p/h5p.classes.php',
            'H5PHubEndpoints' => '/lib/h5p/h5p.classes.php',
            'H5PFrameworkInterface' => '/lib/h5p/h5p.classes.php',
            'H5PContentValidator' => 'lib/h5p/h5p.classes.php',
            'H5PValidator' => '/lib/h5p/h5p.classes.php',
            'H5PStorage' => '/lib/h5p/h5p.classes.php',
            'H5PDevelopment' => '/lib/h5p/h5p-development.class.php',
            'H5PFileStorage' => '/lib/h5p/h5p-file-storage.interface.php',
            'H5PMetadata' => '/lib/h5p/h5p-metadata.class.php',
        ];

        if (isset($classes[$classname])) {
            require_once("{$CFG->dirroot}{$classes[$classname]}");
        }
    }
}

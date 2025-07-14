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
 * @package    core
 * @subpackage backup-convert
 * @copyright  2011 Mark Nielsen <mark@moodlerooms.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Factory class to create new instances of backup converters
 */
abstract class convert_factory {

    /**
     * Instantinates the given converter operating on a given directory
     *
     * @throws coding_exception
     * @param $name The converter name
     * @param $tempdir The temp directory to operate on
     * @param base_logger|null if the conversion should be logged, use this logger
     * @return base_converter
     */
    public static function get_converter($name, $tempdir, $logger = null) {
        global $CFG;

        $name = clean_param($name, PARAM_SAFEDIR);

        $classfile = "$CFG->dirroot/backup/converter/$name/lib.php";
        $classname = "{$name}_converter";

        if (!file_exists($classfile)) {
            throw new coding_exception("Converter factory error: class file not found $classfile");
        }
        require_once($classfile);

        if (!class_exists($classname)) {
            throw new coding_exception("Converter factory error: class not found $classname");
        }

        return new $classname($tempdir, $logger);
    }
}

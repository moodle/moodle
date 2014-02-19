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
 * Moodle implementation of LESS.
 *
 * @package    core
 * @copyright  2014 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir . '/lessphp/Autoloader.php');
Less_Autoloader::register();

/**
 * Moodle LESS compiler class.
 *
 * @package    core
 * @copyright  2014 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_lessc extends Less_Parser {

    /**
     * Add a rule to import a file.
     *
     * This is useful when you want to import the content of a LESS files but
     * you are not sure if it contains @imports or not. This method will
     * import the file using @import with a relative path from your
     * main file to compile. Less does not support absolute paths.
     *
     * @param string $filepath The path to the LESS file to import.
     * @param string $relativeto The path from which the relative path should be built.
     *                           Typically this would be the path to a file passed
     *                           to {@link self::parseFile()}.
     * @return void
     */
    public function import_file($filepath, $relativeto) {
        global $CFG;

        if (!is_readable($filepath) || !is_readable($relativeto)) {
            throw new coding_exception('Could not read the files');
        }

        $filepath = realpath($filepath);
        $relativeto = realpath($relativeto);

        if (strtolower(substr($filepath, -5)) != '.less') {
            throw new coding_exception('Imports only work with LESS files.');
        } else if (strpos(realpath($filepath), $CFG->dirroot) !== 0 ||
                strpos(realpath($relativeto), $CFG->dirroot) !== 0) {
            throw new coding_exception('Files must be in CFG->dirroot.');
        }

        // Simplify the file path the start of dirroot.
        $filepath = trim(substr($filepath, strlen($CFG->dirroot)), '/');
        $relativeto = trim(substr($relativeto, strlen($CFG->dirroot)), '/');

        // Split the file path and remove the file name.
        $dirs = explode('/', $relativeto);
        array_pop($dirs);

        // Generate the relative path.
        $relativepath = str_repeat('../', count($dirs)) . $filepath;

        $this->parse('@import "' . $relativepath . '";');
    }

    /**
     * Parse the content of a file.
     *
     * The purpose of this method is to provide a way to import the
     * content of a file without messing with the import directories
     * as {@link self::parseFile()} would do. But of course you should
     * have manually set your import directories previously.
     *
     * @see self::SetImportDirs()
     * @param string $filepath The path to the file.
     * @return void
     */
    public function parse_file_content($filepath) {
        $this->parse(file_get_contents($filepath));
    }

}

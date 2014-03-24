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

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
 * XML format importer class from file storage
 *
 * @package    core_dtl
 * @copyright  2008 Andrei Bautu
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * XML format importer class from file storage.
 */
class file_xml_database_importer extends xml_database_importer {
    /** @var string Path to the XML data file. */
    protected $filepath;

    /**
     * Object constructor.
     *
     * @param string $filepath - path to the XML data file. Use 'php://input' for PHP
     * input stream.
     * @param moodle_database $mdb Connection to the target database
     * @see xml_database_importer::__construct()
     * @param boolean $check_schema - whether or not to check that XML database
     * @see xml_database_importer::__construct()
     */
    public function __construct($filepath, moodle_database $mdb, $check_schema=true) {
        $this->filepath = $filepath;
        parent::__construct($mdb, $check_schema);
    }

    /**
     * Common import method: it opens the file storage, creates the parser, feeds
     * the XML parser with data, releases the parser and closes the file storage.
     * @return void
     */
    public function import_database() {
        $file = fopen($this->filepath, 'r');
        $parser = $this->get_parser();
        while ($data = fread($file, 65536)) {
            if (!xml_parse($parser, $data, feof($file))) {
                throw new dbtransfer_exception('malformedxmlexception');
            }
        }
        xml_parser_free($parser);
        fclose($file);
    }
}

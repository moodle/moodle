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
 * XML format exporter class to file storage
 *
 * @package    core_dtl
 * @copyright  2008 Andrei Bautu
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * XML format exporter class to file storage.
 */
class file_xml_database_exporter extends xml_database_exporter {
    /** @var string Path to the XML data file. */
    protected $filepath;
    /** @var resource File descriptor for the output file. */
    protected $file;

    /**
     * Object constructor.
     *
     * @param string $filepath - path to the XML data file. Use 'php://output' for PHP
     * output stream.
     * @param moodle_database $mdb Connection to the source database
     * @see xml_database_exporter::__construct()
     * @param boolean $check_schema - whether or not to check that XML database
     * @see xml_database_exporter::__construct()
     */
    public function __construct($filepath, moodle_database $mdb, $check_schema=true) {
        parent::__construct($mdb, $check_schema);
        $this->filepath = $filepath;
    }

    /**
     * Specific output method for the file XML sink.
     * @param string $text
     */
    protected function output($text) {
        fwrite($this->file, $text);
    }

    /**
     * Specific implementation for file exporting the database: it opens output stream, calls
     * superclass @see database_exporter::export_database() and closes output stream.
     *
     * @exception dbtransfer_exception if any checking (e.g. database schema) fails
     *
     * @param string $description a user description of the data.
     */
    public function export_database($description=null) {
        // TODO: add exception if file creation fails
        $this->file = fopen($this->filepath, 'wb');
        parent::export_database($description);
        fclose($this->file);
    }
}

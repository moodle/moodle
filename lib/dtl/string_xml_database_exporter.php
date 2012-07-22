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
 * XML format exporter class to memory storage
 *
 * @package    core_dtl
 * @copyright  2008 Andrei Bautu
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * XML format exporter class to memory storage (i.e. a string).
 */
class string_xml_database_exporter extends xml_database_exporter {
    /** @var string String with XML data. */
    protected $data;

    /**
     * Specific output method for the memory XML sink.
     * @param string $text
     */
    protected function output($text) {
        $this->data .= $text;
    }

    /**
     * Returns the output of the exporters
     * @return string XML data from exporter
     */
    public function get_output() {
        return $this->data;
    }

    /**
     * Specific implementation for memory exporting the database: it clear the buffer
     * and calls superclass @see database_exporter::export_database().
     *
     * @exception dbtransfer_exception if any checking (e.g. database schema) fails
     * @param string $description a user description of the data.
     * @return void
     */
    public function export_database($description=null) {
        $this->data = '';
        parent::export_database($description);
    }
}

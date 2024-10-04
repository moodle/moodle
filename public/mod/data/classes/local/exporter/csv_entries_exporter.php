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

namespace mod_data\local\exporter;

use coding_exception;
use csv_export_writer;

/**
 * CSV entries exporter for mod_data.
 *
 * @package    mod_data
 * @copyright  2023 ISB Bayern
 * @author     Philipp Memmel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class csv_entries_exporter extends entries_exporter {

    /** @var string[] Possible delimiter names. Only used internally to check if a valid delimiter name
     *   has been specified.
     */
    private const POSSIBLE_DELIMITER_NAMES = ['comma', 'tab', 'semicolon', 'colon', 'cfg'];

    /**
     * @var string name of the delimiter to use for the csv export. Possible values:
     *  'comma', 'tab', 'semicolon', 'colon' or 'cfg'.
     */
    private string $delimitername = 'comma';

    /**
     * Returns the csv data exported by the csv_export_writer for further handling.
     *
     * @see \mod_data\local\exporter\entries_exporter::get_data_file_content()
     */
    public function get_data_file_content(): string {
        global $CFG;
        require_once($CFG->libdir . '/csvlib.class.php');

        return csv_export_writer::print_array($this->exportdata, $this->delimitername, '"', true);
    }

    /**
     * Returns the file extension of this entries exporter.
     *
     * @see \mod_data\local\exporter\entries_exporter::get_export_data_file_extension()
     */
    public function get_export_data_file_extension(): string {
        return 'csv';
    }

    /**
     * Setter for the delimiter name which should be used in this csv_entries_exporter object.
     *
     * Calling this setter is optional, the delimiter name defaults to 'comma'.
     *
     * @param string $delimitername one of 'comma', 'tab', 'semicolon', 'colon' or 'cfg'
     * @return void
     * @throws coding_exception if a wrong delimiter name has been specified
     */
    public function set_delimiter_name(string $delimitername): void {
        if (!in_array($delimitername, self::POSSIBLE_DELIMITER_NAMES)) {
            throw new coding_exception('Wrong delimiter type',
                'Please choose on of the following delimiters: '
                . '\"comma\", \"tab\", \"semicolon\", \"colon\", \"cfg\"');
        }
        $this->delimitername = $delimitername;
    }
}

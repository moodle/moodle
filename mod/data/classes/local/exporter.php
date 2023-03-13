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

namespace mod_data\local;

use file_serving_exception;
use zip_archive;

/**
 * Exporter class for exporting data.
 *
 * @package    mod_data
 * @copyright  2023 ISB Bayern
 * @author     Philipp Memmel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class exporter {

    /** @var int Tracks the currently edited row of the export data file. */
    private int $currentrow;

    /**
     * @var array The data structure containing the data for exporting. It's a 2-dimensional array of
     *  rows and columns.
     */
    protected array $exportdata;

    /** @var string Name of the export file name without extension. */
    protected string $exportfilename;

    /**
     * Creates an exporter object.
     *
     * This object can be used to export data to different formats.
     */
    public function __construct() {
        $this->currentrow = 0;
        $this->exportdata = [];
        $this->exportfilename = 'Exportfile';
    }

    /**
     * Adds a row (array of strings) to the export data.
     *
     * @param array $row the row to add, $row has to be a plain array of strings
     * @return void
     */
    public function add_row(array $row): void {
        $this->exportdata[] = $row;
        $this->currentrow++;
    }

    /**
     * Adds a data string (so the content for a "cell") to the current row.
     *
     * @param string $cellcontent the content to add to the current row
     * @return void
     */
    public function add_to_current_row(string $cellcontent): void {
        $this->exportdata[$this->currentrow][] = $cellcontent;
    }

    /**
     * Signal the exporter to finish the current row and jump to the next row.
     *
     * @return void
     */
    public function next_row(): void {
        $this->currentrow++;
    }

    /**
     * Sets the name of the export file.
     *
     * Only use the basename without path and without extension here.
     *
     * @param string $exportfilename name of the file without path and extension
     * @return void
     */
    public function set_export_file_name(string $exportfilename): void {
        $this->exportfilename = $exportfilename;
    }

    /**
     * The exporter will prepare a data file from the rows and columns being added.
     * Overwrite this method to generate the data file as string.
     *
     * @return string the data file as a string
     */
    abstract protected function get_data_file_content(): string;

    /**
     * Overwrite the method to return the file extension your data file will have, for example
     * <code>return 'csv';</code> for a csv file exporter.
     *
     * @return string the file extension of the data file your exporter is using
     */
    abstract protected function get_export_data_file_extension(): string;

    /**
     * Returns the count of currently stored records (rows excluding header row).
     *
     * @return int the count of records/rows
     */
    public function get_records_count(): int {
        // The attribute $this->exportdata also contains a header. If only one row is present, this
        // usually is the header, so record count should be 0.
        if (count($this->exportdata) <= 1) {
            return 0;
        }
        return count($this->exportdata) - 1;
    }

    /**
     * Sends the generated export file.
     *
     * Care: By default this function finishes the current PHP request and directly serves the file to the user as download.
     *
     * @param bool $sendtouser true if the file should be sent directly to the user, if false the file content will be returned
     *  as string
     * @return string|null file content as string if $sendtouser is true
     */
    public function send_file(bool $sendtouser = true): null|string {
        if (empty($this->filenamesinzip)) {
            if ($sendtouser) {
                send_file($this->get_data_file_content(),
                    $this->exportfilename . '.' . $this->get_export_data_file_extension(),
                    null, 0, true, true);
                return null;
            } else {
                return $this->get_data_file_content();
            }
        }
        return null;
    }
}

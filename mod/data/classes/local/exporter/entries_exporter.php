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

use file_serving_exception;
use moodle_exception;
use zip_archive;

/**
 * Exporter class for exporting data and - if needed - files as well in a zip archive.
 *
 * @package    mod_data
 * @copyright  2023 ISB Bayern
 * @author     Philipp Memmel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class entries_exporter {

    /** @var int Tracks the currently edited row of the export data file. */
    private int $currentrow;

    /**
     * @var array The data structure containing the data for exporting. It's a 2-dimensional array of
     *  rows and columns.
     */
    protected array $exportdata;

    /** @var string Name of the export file name without extension. */
    protected string $exportfilename;

    /** @var zip_archive The zip archive object we store all the files in, if we need to export files as well. */
    private zip_archive $ziparchive;

    /** @var bool Tracks the state if the zip archive already has been closed. */
    private bool $isziparchiveclosed;

    /** @var string full path of the zip archive. */
    private string $zipfilepath;

    /** @var array Array to store all filenames in the zip archive for export. */
    private array $filenamesinzip;

    /**
     * Creates an entries_exporter object.
     *
     * This object can be used to export data to different formats including files. If files are added,
     * everything will be bundled up in a zip archive.
     */
    public function __construct() {
        $this->currentrow = 0;
        $this->exportdata = [];
        $this->exportfilename = 'Exportfile';
        $this->filenamesinzip = [];
        $this->isziparchiveclosed = true;
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
     * Signal the entries_exporter to finish the current row and jump to the next row.
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
     * The entries_exporter will prepare a data file from the rows and columns being added.
     * Overwrite this method to generate the data file as string.
     *
     * @return string the data file as a string
     */
    abstract protected function get_data_file_content(): string;

    /**
     * Overwrite the method to return the file extension your data file will have, for example
     * <code>return 'csv';</code> for a csv file entries_exporter.
     *
     * @return string the file extension of the data file your entries_exporter is using
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
     * Use this method to add a file which should be exported to the entries_exporter.
     *
     * @param string $filename the name of the file which should be added
     * @param string $filecontent the content of the file as a string
     * @param string $zipsubdir the subdirectory in the zip archive. Defaults to 'files/'.
     * @return void
     * @throws moodle_exception if there is an error adding the file to the zip archive
     */
    public function add_file_from_string(string $filename, string $filecontent, string $zipsubdir = 'files/'): void {
        if (empty($this->filenamesinzip)) {
            // No files added yet, so we need to create a zip archive.
            $this->create_zip_archive();
        }
        if (!str_ends_with($zipsubdir, '/')) {
            $zipsubdir .= '/';
        }
        $zipfilename = $zipsubdir . $filename;
        $this->filenamesinzip[] = $zipfilename;
        $this->ziparchive->add_file_from_string($zipfilename, $filecontent);
    }

    /**
     * Sends the generated export file.
     *
     * Care: By default this function finishes the current PHP request and directly serves the file to the user as download.
     *
     * @param bool $sendtouser true if the file should be sent directly to the user, if false the file content will be returned
     *  as string
     * @return string|null file content as string if $sendtouser is true
     * @throws moodle_exception if there is an issue adding the data file
     * @throws file_serving_exception if the file could not be served properly
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
        $this->add_file_from_string($this->exportfilename . '.' . $this->get_export_data_file_extension(),
            $this->get_data_file_content(), '/');
        $this->finish_zip_archive();

        if ($this->isziparchiveclosed) {
            if ($sendtouser) {
                send_file($this->zipfilepath, $this->exportfilename . '.zip', null, 0, false, true);
                return null;
            } else {
                return file_get_contents($this->zipfilepath);
            }
        } else {
            throw new file_serving_exception('Could not serve zip file, it could not be closed properly.');
        }
    }

    /**
     * Checks if a file with the given name has already been added to the file export bundle.
     *
     * Care: Filenames are compared to all files in the specified zip subdirectory which
     *  defaults to 'files/'.
     *
     * @param string $filename the filename containing the zip path of the file to check
     * @param string $zipsubdir The subdirectory in which the filename should be looked for,
     *  defaults to 'files/'
     * @return bool true if file with the given name already exists, false otherwise
     */
    public function file_exists(string $filename, string $zipsubdir = 'files/'): bool {
        if (!str_ends_with($zipsubdir, '/')) {
            $zipsubdir .= '/';
        }
        if (empty($filename)) {
            return false;
        }
        return in_array($zipsubdir . $filename, $this->filenamesinzip, true);
    }

    /**
     * Creates a unique filename based on the given filename.
     *
     * This method adds "_1", "_2", ... to the given file name until the newly generated filename
     * is not equal to any of the already saved ones in the export file bundle.
     *
     * @param string $filename the filename based on which a unique filename should be generated
     * @return string the unique filename
     */
    public function create_unique_filename(string $filename): string {
        if (!$this->file_exists($filename)) {
            return $filename;
        }

        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $filenamewithoutextension = empty($extension)
            ? $filename
            : substr($filename, 0,strlen($filename) - strlen($extension) - 1);
        $filenamewithoutextension = $filenamewithoutextension . '_1';
        $i = 1;
        $filename = empty($extension) ? $filenamewithoutextension : $filenamewithoutextension . '.' . $extension;
        while ($this->file_exists($filename)) {
            // In case we have already a file ending with '_XX' where XX is an ascending number, we have to
            // remove '_XX' first before adding '_YY' again where YY is the successor of XX.
            $filenamewithoutextension = preg_replace('/_' . $i . '$/', '_' . ($i + 1), $filenamewithoutextension);
            $filename = empty($extension) ? $filenamewithoutextension : $filenamewithoutextension . '.' . $extension;
            $i++;
        }
        return $filename;
    }

    /**
     * Prepares the zip archive.
     *
     * @return void
     */
    private function create_zip_archive(): void {
        $tmpdir = make_request_directory();
        $this->zipfilepath = $tmpdir . '/' . $this->exportfilename . '.zip';
        $this->ziparchive = new zip_archive();
        $this->isziparchiveclosed = !$this->ziparchive->open($this->zipfilepath);
    }

    /**
     * Closes the zip archive.
     *
     * @return void
     */
    private function finish_zip_archive(): void {
        if (!$this->isziparchiveclosed) {
            $this->isziparchiveclosed = $this->ziparchive->close();
        }
    }
}

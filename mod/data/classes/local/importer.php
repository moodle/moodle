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

use coding_exception;
use core_php_time_limit;
use file_packer;
use file_serving_exception;
use zip_archive;

/**
 * Importer class for importing data.
 *
 * @package    mod_data
 * @copyright  2023 ISB Bayern
 * @author     Philipp Memmel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class importer {

    /** @var string The import file path of the file which data should be imported from. */
    protected string $importfilepath;

    /** @var string The original name of the import file name including extension of the file which data should be imported from. */
    protected string $importfilename;

    /** @var string $importfiletype The file type of the import file. */
    protected string $importfiletype;

    /**
     * Creates an importer object.
     *
     * This object can be used to import data from data files (like csv).
     *
     * @param string $importfilepath the complete path of the import file including filename
     * @param string $importfilename the import file name as uploaded by the user
     * @throws coding_exception
     */
    public function __construct(string $importfilepath, string $importfilename) {
        $this->importfilepath = $importfilepath;
        $this->importfilename = $importfilename;
        $this->importfiletype = pathinfo($importfilename, PATHINFO_EXTENSION);
        if ($this->importfiletype !== $this->get_import_data_file_extension()) {
            throw new coding_exception('Only ' . $this->get_import_data_file_extension() . '" files are allowed.');
        }
    }

    /**
     * Return the file extension of the import data file which is being used, for example 'csv' for a csv importer.
     *
     * @return string the file extension of the export data file
     */
    abstract public function get_import_data_file_extension(): string;

    /**
     * Returns the file content of the data file.
     *
     * @return false|string the data file content as string; false, if file cannot be found/read
     */
    public function get_data_file_content(): false|string {
        return file_get_contents($this->importfilepath);
    }
}

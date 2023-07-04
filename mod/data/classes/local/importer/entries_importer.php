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

namespace mod_data\local\importer;

use coding_exception;
use core_php_time_limit;
use file_packer;

/**
 * Importer class for importing data and - if needed - files as well from a zip archive.
 *
 * @package    mod_data
 * @copyright  2023 ISB Bayern
 * @author     Philipp Memmel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class entries_importer {

    /** @var string The import file path of the file which data should be imported from. */
    protected string $importfilepath;

    /** @var string The original name of the import file name including extension of the file which data should be imported from. */
    protected string $importfilename;

    /** @var string $importfiletype The file type of the import file. */
    protected string $importfiletype;

    /** @var file_packer Zip file packer to extract files from a zip archive. */
    private file_packer $packer;

    /** @var bool Tracks state if zip archive has been extracted already. */
    private bool $zipfileextracted;

    /** @var string Temporary directory where zip archive is being extracted to. */
    private string $extracteddir;

    /**
     * Creates an entries_importer object.
     *
     * This object can be used to import data from data files (like csv) and zip archives both including a data file and files to be
     * stored in the course module context.
     *
     * @param string $importfilepath the complete path of the import file including filename
     * @param string $importfilename the import file name as uploaded by the user
     * @throws coding_exception if a wrong file type is being used
     */
    public function __construct(string $importfilepath, string $importfilename) {
        $this->importfilepath = $importfilepath;
        $this->importfilename = $importfilename;
        $this->importfiletype = pathinfo($importfilename, PATHINFO_EXTENSION);
        $this->zipfileextracted = false;
        if ($this->importfiletype !== $this->get_import_data_file_extension() && $this->importfiletype !== 'zip') {
            throw new coding_exception('Only "zip" or "' . $this->get_import_data_file_extension() . '" files are '
                . 'allowed.');
        }
    }

    /**
     * Return the file extension of the import data file which is being used, for example 'csv' for a csv entries_importer.
     *
     * @return string the file extension of the export data file
     */
    abstract public function get_import_data_file_extension(): string;

    /**
     * Returns the file content of the data file.
     *
     * Returns the content of the file directly if the entries_importer's file is a data file itself.
     *  If the entries_importer's file is a zip archive, the content of the first found data file in the
     *  zip archive's root will be returned.
     *
     * @return false|string the data file content as string; false, if file cannot be found/read
     */
    public function get_data_file_content(): false|string {
        if ($this->importfiletype !== 'zip') {
            // We have no zip archive, so the file itself must be the data file.
            return file_get_contents($this->importfilepath);
        }

        // So we have a zip archive and need to find the right data file in the root of the zip archive.
        $this->extract_zip();
        $datafilenames = array_filter($this->packer->list_files($this->importfilepath),
            fn($file) => pathinfo($file->pathname, PATHINFO_EXTENSION) === $this->get_import_data_file_extension()
                && !str_contains($file->pathname, '/'));
        if (empty($datafilenames) || count($datafilenames) > 1) {
            return false;
        }
        return file_get_contents($this->extracteddir . reset($datafilenames)->pathname);
    }

    /**
     * Returns the file content from a file which has been stored in the zip archive.
     *
     * @param string $filename
     * @param string $zipsubdir
     * @return false|string the file content as string, false if the file could not be found/read
     */
    public function get_file_content_from_zip(string $filename, string $zipsubdir = 'files/'): false|string {
        if (empty($filename)) {
            // Nothing to return.
            return false;
        }
        // Just to be sure extract if not extracted yet.
        $this->extract_zip();
        if (!str_ends_with($zipsubdir, '/')) {
            $zipsubdir .= '/';
        }
        $filepathinextractedzip = $this->extracteddir . $zipsubdir . $filename;
        return file_exists($filepathinextractedzip) ? file_get_contents($filepathinextractedzip) : false;
    }

    /**
     * Extracts (if not already done and if we have a zip file to deal with) the zip file to a temporary directory.
     *
     * @return void
     */
    private function extract_zip(): void {
        if ($this->zipfileextracted || $this->importfiletype !== 'zip') {
            return;
        }
        $this->packer = get_file_packer();
        core_php_time_limit::raise(180);
        $this->extracteddir = make_request_directory();
        if (!str_ends_with($this->extracteddir, '/')) {
            $this->extracteddir .= '/';
        }
        $this->packer->extract_to_pathname($this->importfilepath, $this->extracteddir);
        $this->zipfileextracted = true;
    }
}
